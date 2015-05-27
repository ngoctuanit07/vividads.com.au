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
class Aitoc_Aitemails_Block_Email_Template_Massnew extends Mage_Adminhtml_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('aitemails/email/template/massnew.phtml');
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
        
        $this->setChild('generate_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Generate Templates'),
                        'onclick' => "aitEmailsMassNewGenerateSubmit();",
                        'class'   => 'save'
                    )
                )
        );
        
        return parent::_prepareLayout();
    }
    
    public function showReplaceOption()
    {
        $collection = Mage::getResourceSingleton('aitemails/aittemplate_collection');
        $collection = Mage::getModel('aitemails/aitemails')->applyCollectionScope($collection)->load()->toArray();
        foreach ($collection['items'] as $template)
        {
            if (isset($template['custom_template_id']) && $template['custom_template_id'])
            {
                return true;
            }
        }
        return false;
    }

    public function getHeaderText()
    {
        return Mage::helper('aitemails')->__('Generate Templates');
    }
    
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }
    
    public function getGenerateButtonHtml()
    {
        return $this->getChildHtml('generate_button');
    }
    
    public function getTemplateDefaultEntries()
    {
        $entries = Mage::getConfig()->getNode('global/aitemails/email/template/default')->asArray();
        return $entries;
    }
    
    public function getMassNewGenerateUrl()
    {
        return $this->getUrl('*/*/massnewgenerate', array('website' => Mage::app()->getRequest()->getParam('website'), 'store' => Mage::app()->getRequest()->getParam('store')));
    }
}