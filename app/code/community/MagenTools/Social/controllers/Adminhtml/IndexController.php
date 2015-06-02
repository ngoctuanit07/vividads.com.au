<?php
/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */
class MagenTools_Social_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action 
{
   

	//pinterest
    public function massPinAction() {
        $params = $this->getRequest()->getParams();
        $products = $params['product'];
        $pintoboard = $params['pintoboard'];
        $session = Mage::getSingleton('core/session');
        
        if(sizeof($products) == 0 || $pintoboard == '') {
            $session->addError($this->__('Please select product(s) and board to pin'));
            $this->_redirectReferer();
            return;
        }
        $max_pin_allowed = Mage::getStoreConfig("social/pinterest/max_pins_allowed");
        if(sizeof($products) > $max_pin_allowed) {
            $session->addError($this->__('You have exceeded maximum pins allowed at a time'));
            $this->_redirectReferer();
            return;
        }
        
        $error = 0;        
        $pin = Mage::getModel('social/autopin');
        
        if($pin->chkLogin() == 'No Saved Login') { 
            $email = Mage::getStoreConfig("social/pinterest/email");
            $pass = Mage::getStoreConfig("social/pinterest/password");
            $pin = Mage::getModel('social/autopin');
            $loginError = $pin->loginToPinterest($email, $pass); Mage::log($loginError); 
            if ($loginError) {
                $session->addError($this->__('Cannot login to Pinterest'));
                $this->_redirectReferer();
                return;
            }
        }
        foreach($products as $p) {
            $res = $pin->pinThis($p, $pintoboard);
            //Mage::log($res);
            if($res['code'] != "OK") {
                $error++;
            }  
        }
        if($error > 0) {
            $session->addError($this->__('Total %d out of %d product images not pinned on Pinterest.', $error, count($products)));
        } elseif ($error == 0) {
            $session->addSuccess($this->__('Total %d product images pinned successfully on Pinterest', count($products)));
        }
        $this->_redirectReferer();
        
    }


	//facebook
    public function massPostAction() {
        $params = $this->getRequest()->getParams();
	//print_r($params);exit;
	if($params['code']) {
		$addparams = Mage::getSingleton('admin/session')->getFbPostIds();
		if(!isset($params['product'][0]) && is_array($addparams)) {	
			$params = array_merge($params, array('product'=>$addparams));
		}
		Mage::getSingleton('admin/session')->setFbParams($params);
	} else {
		Mage::getSingleton('admin/session')->setFbPostIds($params['product']);
		$fbparams = Mage::getSingleton('admin/session')->getFbParams();
		if(isset($params['product'][0]) && isset($fbparams['product'][0])) {
			unset($fbparams['product']);
		}
		if(is_array($fbparams)) {
			$params = array_merge($params,$fbparams);
		}
	}
	$ref = Mage::helper("adminhtml")->getUrl("social/adminhtml_index/massPost");
	//print_r($params);exit;
        $products = $params['product'];
        $session = Mage::getSingleton('core/session');
        
        if(sizeof($products) == 0) {
            $session->addError($this->__('Please select product(s) to auto-post'));
            $this->_redirectReferer();
            return;
        }
        $max_posts_allowed = Mage::getStoreConfig("social/facebook/max_posts_allowed");
        if(sizeof($products) > $max_posts_allowed) {
            $session->addError($this->__('You have exceeded maximum auto-posts allowed at a time'));
            $this->_redirectReferer();
            return;
        }
        
        $error = 0; $posts = "";
        $pin = Mage::getModel('social/autopost');
        
        foreach($products as $p) {
            $res = $pin->postThis($p, $ref, $params);
            Mage::log($res);
            if(!$res['id']) {
                $error++;
            } else {
		$posts .= "<a href='http://facebook.com/".$res['id']."'>".$res['id']."</a> | ";
	    }
        }
	$posts = rtrim($posts," | ");
        if($error > 0) {
            $session->addError($this->__('Total %d out of %d products not posted on Facebook.', $error, count($products)));
        } elseif ($error == 0) {
            $session->addSuccess($this->__('Total %d products posted successfully on Facebook. Check the FB posts: %s', count($products), $posts));
        }
        $this->_redirectReferer();
        
    }

	//Twitter
    public function massTweetAction() {
        $params = $this->getRequest()->getParams();
        $products = $params['product'];
        //$pintoboard = $params['pintoboard'];
        $session = Mage::getSingleton('core/session');
        
        if(sizeof($products) == 0) {
            $session->addError($this->__('Please select product(s) to auto-tweet'));
            $this->_redirectReferer();
            return;
        }
        $max_pin_allowed = Mage::getStoreConfig("social/twitter/max_tweets_allowed");
        if(sizeof($products) > $max_pin_allowed) {
            $session->addError($this->__('You have exceeded maximum auto-tweets allowed at a time'));
            $this->_redirectReferer();
            return;
        }
        
        $error = 0;        
        $pin = Mage::getModel('social/autotweet');
        
        foreach($products as $p) {
            $res = $pin->tweetThis($p);
            Mage::log($res);
            if(!isset($res->id)) {
                $error++;
            }  
        }
        if($error > 0) {
            $session->addError($this->__('Total %d out of %d products not tweeted on Twitter.', $error, count($products)));
        } elseif ($error == 0) {
            $session->addSuccess($this->__('Total %d product tweeted successfully on Twitter', count($products)));
        }
        $this->_redirectReferer();
        
    }

	public function singlePostAction() {

		$params = $this->getRequest()->getParams();
		$ref = Mage::helper("adminhtml")->getUrl("social/adminhtml_index/singlePost");
		$cP = Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/index");

		$p = Mage::getSingleton('core/session')->getFbSinglePostId();

		$pin = Mage::getModel('social/autopost');
		$res = $pin->postThis($p, $ref, $params);


		/*$this->getResponse()->setRedirect(
                        Mage::getModel('adminhtml/url')->getUrl("adminhtml/catalog_product/index")
                );*/ 
		header("Location: ".$cP); exit;

	}

}
