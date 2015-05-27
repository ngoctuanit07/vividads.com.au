<?php

/**
 * MD_Quotemail.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Quotemail
 * @object	   MD_Quotemail_Block_Adminhtml_Quotemail_Edit
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php


class MD_Quotemail_Block_Adminhtml_Quotemail_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

/**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
       

    }
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'quotemail';
        $this->_controller = 'adminhtml_quotemail';

        $this->_updateButton('save', 'label', Mage::helper('quotemail')->__('Save Email Template'));
        $this->_updateButton('delete', 'label', Mage::helper('quotemail')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('quotemail_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'quotemail_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'quotemail_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }



    public function getHeaderText() {
        if (Mage::registry('quotemail_data') && Mage::registry('quotemail_data')->getId()) {
            return Mage::helper('quotemail')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('quotemail_data')->getTitle()));
        } else {
            return Mage::helper('quotemail')->__('Add Email Template');
        }
    }

}