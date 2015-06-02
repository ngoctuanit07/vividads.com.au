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
class Aitoc_Aitemails_Block_Email_Template_Edit_Downloadable extends Mage_Adminhtml_Block_Widget
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('aitemails/downloadable.phtml');
    }
    
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('aitattachmentInfo');

        $accordion->addItem('aitattachment', array(
            'title'   => Mage::helper('adminhtml')->__('Attachments'),
            'content' => $this->getLayout()->createBlock('aitemails/email_template_edit_attachments')->toHtml(),
            'open'    => true,
        ));
        
        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }
}