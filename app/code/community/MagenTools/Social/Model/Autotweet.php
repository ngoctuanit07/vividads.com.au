<?php

/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */
class MagenTools_Social_Model_Autotweet extends Mage_Core_Model_Abstract {

	public function tweetThis($p) {


		if ($p == '' || !is_numeric($p)) {
		    return 'Invalid Product Id';
		}$product = Mage::getModel('catalog/product')->load($p);
		if (!$product->getId())
		    return 'Product not available';
		$prodName = $product->getName();
		$twt_msg = 'New product added: ' . $prodName;
		$twt_vals = Mage::getStoreConfig("social/twitter");
		
		$twtSku = $twt_vals["include_sku"];
		$twtPrice = $twt_vals["include_price"];
		$twtLink = $twt_vals["include_link"];
		if ($twtSku) {
		    $sku = $product->getSku();
		    $twt_msg .= " | " . $sku;
		}
		if ($twtPrice) {
		    $price = $product->getPrice();
		    $currSym = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		    $specialprice = $product->getSpecialPrice();
		    $specialPriceFromDate = $product->getSpecialFromDate();
		    $specialPriceToDate = $product->getSpecialToDate();
		    $today = time();
		    if ($specialprice && ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate) || $today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate))): $price = "RP: " . $currSym . number_format($price, 2) . ", SP: " . $currSym . number_format($specialprice, 2);
		    else: $price = $currSym . number_format($price, 2);
		    endif;
		    $twt_msg .= " | " . $price;
		} 
		if($twtLink) {
			$productLink = $product->getProductUrl();
			$twt_msg .= " | " . $productLink;
		}
		$link = Mage::getBaseUrl() . $product->getUrlPath();

		require_once( Mage::getModuleDir('Helper', 'MagenTools_Social') . '/Helper/Twitteroauth.php');

		$twitteruser = $twt_vals['username'];
		$consumerkey = $twt_vals['consumer_key'];
		$consumersecret = $twt_vals['consumer_secret'];
		$accesstoken = $twt_vals['access_token'];
		$accesstokensecret = $twt_vals['access_token_secret'];

		$connection = new TwitterOAuth($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
		$status = $connection->post('https://api.twitter.com/1.1/statuses/update.json', array('status' => $twt_msg));
		return $status;


	}   

	public function singleTweet($p) {

                $res = $this->tweetThis($p);
		if($res->id) {
			Mage::getSingleton('core/session')->addSuccess(
                                'Product tweeted successfully on Twitter.'
                        );
		}

        }

 
}
