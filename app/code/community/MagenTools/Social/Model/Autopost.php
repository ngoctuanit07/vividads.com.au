<?php

/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */
class MagenTools_Social_Model_Autopost extends Mage_Core_Model_Abstract {

    public function postThis($p, $ref, $params) {

	//echo Mage::getModuleDir('Helper', 'MagenTools_Social') . '/Helper/Facebook.php';exit;
	require_once(Mage::getModuleDir('Helper', 'MagenTools_Social') . '/Helper/Facebook.php');

        if ($p == '' || !is_numeric($p)) {
            return 'Invalid Product Id';
        }$product = Mage::getModel('catalog/product')->load($p);
        if (!$product->getId())
            return 'Product not available';
	$prodName = $product->getName();
	$post_msg = 'New product added: ' . $prodName;
        $fb_vals = Mage::getStoreConfig("social/facebook");

        $postSku = $fb_vals["include_sku"];
        $postPrice = $fb_vals["include_price"];
        $postLink = $fb_vals["include_link"];
	$pageID = $fb_vals["page_id"];

        if ($postSku) {
            $sku = $product->getSku();
            $post_msg .= " | " . $sku;
        }if ($postPrice) {
            $price = $product->getPrice();
            $currSym = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
            $specialprice = $product->getSpecialPrice();
            $specialPriceFromDate = $product->getSpecialFromDate();
            $specialPriceToDate = $product->getSpecialToDate();
            $today = time();
            if ($specialprice && ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate) || $today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate))): $price = "RP: " . $currSym . number_format($price, 2) . ", SP: " . $currSym . number_format($specialprice, 2);
            else: $price = $currSym . number_format($price, 2);
            endif;
            $post_msg .= " | " . $price;
        } //$imgURL = $product->getImageUrl(); //small image
	//$imgURL = Mage::helper('catalog/image')->init($product, 'image'); //big image
	$imgURL = 'http://magentools.com/' . $product->getSku() . '.jpg'; //COMMENT THIS..
        $link = Mage::getBaseUrl() . $product->getUrlPath();



	$user = null;
	$facebook = new Facebook(array(
	    'appId' => $fb_vals["app_id"],
	    'secret' => $fb_vals["app_secret"],
	    'cookie' => true
	));

	$user = $facebook->getUser();
	if($user == 0) {

		Mage::getSingleton('core/session')->setFbSinglePostId($p);
	    $login_url = $facebook->getLoginUrl($params = array('scope' => "user_likes,publish_actions,email,publish_stream,manage_pages",
			'redirect_uri' => $ref));
		header("Location: ".$login_url);
		exit;

	} else {

		$user = $facebook->getUser();

		$accounts = $facebook->api('/me/accounts');

		foreach($accounts['data'] as $account)
		{
		   if($account['id'] == $pageID)
		   {
		      $token = $account['access_token'];
		   }
		}


	    try {
		    $params = array(
			'access_token'	=>  $token,
			'message'       =>  $product->getShortDescription(), 
			'name'          =>  $prodName, 
			'caption'       =>  $prodName, 
			'description'   =>  $product->getDescription(),
			'link'          =>  $link, 
			'picture'       =>  $imgURL
		    );

		    $post = $facebook->api("/".$pageID."/feed","POST",$params);
		    return $post; 

		}
		catch (FacebookApiException $e) {
		   return $result = $e->getResult();
		}

	}

    }


	public function singlePost($p) {

		$ref = Mage::helper("adminhtml")->getUrl("social/adminhtml_index/singlePost");
		$params = array();
		$res = $this->postThis($p, $ref, $params);
		if($res['id']) {
			Mage::getSingleton('core/session')->addSuccess(
				'Product posted successfully on Facebook.'
			);
		}

	}

}
?>
