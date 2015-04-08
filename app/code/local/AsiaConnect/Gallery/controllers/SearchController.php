<?php
class AsiaConnect_Gallery_SearchController extends AsiaConnect_Gallery_Controller_Action
{

    public function indexAction()
    {
    }
    
    public function resultAction(){
    	$this->loadLayout();
    	$breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs');
		$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Home'), Mage::helper('gallery')->__('Home'),Mage::getBaseUrl(),true);
		$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Gallery'), Mage::helper('gallery')->__('gallery'),Mage::getUrl('gallery'),false,false);
		$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Search'), Mage::helper('gallery')->__('search'),null,false,true);
        $this->renderLayout();
    }		
		
}