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
class Aitoc_Aitemails_Block_Email_Template_Massreplace extends Mage_Adminhtml_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('aitemails/email/template/massreplace.phtml');
    }

    protected function _prepareLayout()
    {
		$websiteId = Mage::app()->getRequest()->getParam('website');
        $storeId = Mage::app()->getRequest()->getParam('store');
        
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Back'),
                        'onclick' => "window.location.href = '" . $this->getUrl('*/*', array('website' => Mage::app()->getRequest()->getParam('website'), 'store' => Mage::app()->getRequest()->getParam('store'))) . "'",
                        'class'   => 'back'
                    )
                )
        );
        
        $this->setChild('replace_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Replace In Custom Templates'),
                        'onclick' => "\$('aitemails_replace_in_templates_form').submit();",
                        'class'   => 'save'
                    )
                )
        );
        
        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        return Mage::helper('aitemails')->__('Replace In Custom Templates');
    }
    
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }
    
    public function getReplaceButtonHtml()
    {
        return $this->getChildHtml('replace_button');
    }
    
    public function getMassReplaceProcessUrl()
    {
        return $this->getUrl('*/*/massreplaceprocess', array('website' => Mage::app()->getRequest()->getParam('website'), 'store' => Mage::app()->getRequest()->getParam('store')));
    }
    
    public function getReplaceFieldCntDefault()
    {
        return 5;
    }
}