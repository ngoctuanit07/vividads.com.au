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
class Aitoc_Aitemails_Block_Rewrite_AdminSystemEmailTemplatePreview extends Mage_Adminhtml_Block_System_Email_Template_Preview
{

    protected function _toHtml()
    {
        $template = Mage::getModel('core/email_template');
        if($id = (int)$this->getRequest()->getParam('id')) {
            $template->load($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }
        #AITOC FIX FOR PREVIEW,don't know why magento escape html-chars ,probably bug
        #$template->setTemplateText(
        #    $this->escapeHtml($template->getTemplateText())
        #);
        Varien_Profiler::start("email_template_proccessing");
        $vars = array();
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        if($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        Varien_Profiler::stop("email_template_proccessing");
        return $templateProcessed;
    }    
    
}