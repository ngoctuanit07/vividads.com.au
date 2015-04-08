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
class Aitoc_Aitemails_Block_Email_Template_Edit_Autoload extends Mage_Adminhtml_Block_Widget
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('aitemails/email/template/edit/autoload.phtml');
    }
    
    public function isNeedAutoload()
    {
        return ($this->getLoadTemplate() && $this->getLoadLocale());
    }
    
    public function getLoadTemplate()
    {
        $templateCode = Mage::app()->getRequest()->getParam('templatecode');
        $defaultTemplates = Mage_Core_Model_Email_Template::getDefaultTemplates();
        if (isset($defaultTemplates[$templateCode]))
        {
            return $templateCode;
        }
        return false;
    }
    
    public function getLoadLocale()
    {
        $localeCode = Mage::app()->getRequest()->getParam('localecode');
        if ($localeCode)
        {
            return $localeCode;
        }
        return false;
    }
}