<?php
/**
 * Email Template Manager
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitemails
 * @version      2.0.13
 * @license:     POwWoRanFs2vVHb6wy050abrHO3ftlyCeMSf01dXU3
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitemails_Model_Observer
{
   
    public function performSaveCommitAfter( Varien_Event_Observer $observer )
    {
        if ($this->_castObject($observer))
        {
            if (true !== Mage::registry('aitemails_template_save_noredirect') && Mage::app()->getRequest()->getParam('fromaitemails'))
            {
                $this->_redirectToList(Mage::helper('aitemails')->__('Custom template saved successfully'));
            }
        }
    }
    
    protected function _castObject( Varien_Event_Observer $observer )
    {
        $object = $observer->getObject();
        if ($object instanceof Mage_Core_Model_Email_Template)
        {
            return $object;
        }
    }
        
    protected function _redirectToList($message = '')
    {
        if ($message)
        {
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        }
        $curWebsite = Mage::app()->getRequest()->getParam('website');
        $curStore   = Mage::app()->getRequest()->getParam('store');
        $aParams    = array();
        if ($curWebsite) $aParams['website'] = $curWebsite;
        if ($curStore) $aParams['store'] = $curStore;
        Mage::app()->getResponse()->setRedirect(Mage::getModel('adminhtml/url')->getUrl('aitemails/index', $aParams));
        Mage::app()->getResponse()->sendHeaders();
        exit(); // if not to exit, this headers will be cleared in controller, and we will have no redirect
    }
   
}