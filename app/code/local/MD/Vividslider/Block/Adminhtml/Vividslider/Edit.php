<?php

/**
 * MD_Vividslider.
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
 * @package    Vividslider
 * @object	   MD_Vividslider_Block_Adminhtml_Vividslider_Edit
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php


class MD_Vividslider_Block_Adminhtml_Vividslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

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
        $this->_blockGroup = 'vividslider';
        $this->_controller = 'adminhtml_vividslider';

        $this->_updateButton('save', 'label', Mage::helper('vividslider')->__('Save Slider'));
        $this->_updateButton('delete', 'label', Mage::helper('vividslider')->__('Delete Slider'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vividslider_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vividslider_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vividslider_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }



    public function getHeaderText() {
        if (Mage::registry('vividslider_data') && Mage::registry('vividslider_data')->getId()) {
            return Mage::helper('vividslider')->__("Edit Slider '%s'", $this->htmlEscape(Mage::registry('vividslider_data')->getTitle()));
        } else {
            return Mage::helper('vividslider')->__('Add New Slider');
        }
    }

}