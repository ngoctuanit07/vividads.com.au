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
class Aitoc_Aitemails_Block_Email_Template extends Mage_Adminhtml_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('aitemails/email/template/list.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('aitemails/email_template_grid', 'aitemails.email.template.grid'));
        $this->setChild('store_switch', $this->getLayout()->createBlock('aitemails/email_template_switcher', 'aitemails.store.switcher'));
        return parent::_prepareLayout();
    }
    
    public function getActionParams()
    {
        $website = Mage::app()->getRequest()->getParam('website');
        $store = Mage::app()->getRequest()->getParam('store');
        return array('website' => $website, 'store' => $store);
    }

    public function getMassCreateUrl()
    {
        return $this->getUrl('*/*/massnew', $this->getActionParams());
    }
    
    public function getMassReplaceUrl()
    {
        return $this->getUrl('*/*/massreplace', $this->getActionParams());
    }

    public function getHeaderText()
    {
        return Mage::helper('aitemails')->__('E-mail Templates Manager');
    }
}