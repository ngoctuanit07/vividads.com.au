<?php
/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */ 
class MagenTools_Social_Model_Observer
{
    static protected $_singletonFlag = false;
    protected $_boards = "";
 
    /**
     * This method runs when product gets saved from Admin panel
     * @param Varien_Event_Observer $observer 
     */
    public function saveSocialTabData(Varien_Event_Observer $observer)
    {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;
 
            $product = $observer->getEvent()->getProduct();
		//if product is disabled, don't pin the image
	    if($product->getStatus()==2) {
		return;
	    }
 
            try {
		//Pinterest... 
		$pin = Mage::getModel('social/autopin');
                $boardID =  $this->_getRequest()->getPost('board_id');
		$p = $product->getId();
                if($boardID) {
			if($pin->chkLogin() == 'No Saved Login') { 
				$email = $this->_getConfig("social/pinterest/email");
	                	$pass = $this->_getConfig("social/pinterest/password");
		                $pin = Mage::getModel('social/autopin');
		                $loginError = $pin->loginToPinterest($email, $pass);
	        	        if (!$loginError) {                        
	                	    //$p = $product->getId();
		                    $res = $pin->pinThis($p, $boardID);
		                } else {
					Mage::getSingleton('adminhtml/session')->addError($loginError);
				}
			} else {        
		                //$p = $product->getId();
	        	        $res = $pin->pinThis($p, $boardID);
				if($res['code'] == 'OK') {
					Mage::getSingleton('core/session')->addSuccess('Product image pinned successfully on Pinterest.');
				}	              
			}
                }

		//Facebook...Twitter...
		$postToFb = $this->_getRequest()->getPost('facebook');
		$postToTwitter = $this->_getRequest()->getPost('twitter');
		if($postToTwitter) {
			Mage::getModel('social/autotweet')
                                ->singleTweet($p);
		}
		//always keep this last..
		if($postToFb) {
			Mage::getModel('social/autopost')
		        	->singlePost($p);	
		}
		

            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
 
    public function getProduct()
    {
        return Mage::registry('product');
    }
  
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
    
    private function _getConfig($code) 
    {
        return Mage::getStoreConfig($code);
    }
    
    public function addMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();

        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'catalog_product'
            && ($block->getRequest()->getActionName() == 'index' || $block->getRequest()->getActionName() == 'grid'))
        {

            $session = Mage::getSingleton('core/session');
            $pinterestBoardOptions = $session->getPinterestBoardOptions();

            if(!(is_array($pinterestBoardOptions) && isset($pinterestBoardOptions[0]['value']))) {
                Mage::getSingleton('social/boards')->setOptionArray();
            }
            $block->addItem('pinterest', array(
                'label' => 'Pinterest AutoPin',
                'url' => Mage::app()->getStore()->getUrl('social/adminhtml_index/massPin'),
                'additional' => array(
                      'visibility' => array(
                         'name' => 'pintoboard',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Pin to Board'),
                         'values' => $pinterestBoardOptions
                       )
                  )
            ));
            $block->addItem('facebook', array(
                'label' => 'Facebook AutoPost',
                'url' => Mage::app()->getStore()->getUrl('social/adminhtml_index/massPost')
	    ));
            $block->addItem('twitter', array(
                'label' => 'Twitter AutoTweet',
                'url' => Mage::app()->getStore()->getUrl('social/adminhtml_index/massTweet')
	    ));
        }
    }
    
}
