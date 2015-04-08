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
class Aitoc_Aitemails_Block_Rewrite_AdminSystemEmailTemplateEdit extends Mage_Adminhtml_Block_System_Email_Template_Edit
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $curWebsite = Mage::app()->getRequest()->getParam('website');
        $curStore   = Mage::app()->getRequest()->getParam('store');
        $aParams    = array();
        if ($curWebsite) $aParams['website'] = $curWebsite;
        if ($curStore) $aParams['store'] = $curStore;
        
        if (Mage::app()->getRequest()->getParam('fromaitemails'))
        {
            $sBackLocation = $this->getUrl('aitemails/index', $aParams);
        } 
        else 
        {
            $sBackLocation = $this->getUrl('*/*');
        }
        
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('adminhtml')->__('Back'),
                        'onclick' => "window.location.href = '" . $sBackLocation . "'",
                        'class'   => 'back'
                    )
                )
        );
        return $this;
    }
    
    protected function _afterToHtml($html)
    {
        $html .= $this->getLayout()->createBlock('aitemails/email_template_edit_autoload')->toHtml();
        $html = preg_replace('@</form>[^<]+<form (.+) id="email_template_preview_form"@', 
                $this->getLayout()->createBlock('aitemails/email_template_edit_downloadable')->toHtml() . '</form><form $1 id="email_template_preview_form"', $html);
        return $html;
    }
}