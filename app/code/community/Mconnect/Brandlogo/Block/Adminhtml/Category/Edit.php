<?php

class Mconnect_Brandlogo_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'brandlogo';
        $this->_controller = 'adminhtml_category';

        $this->_updateButton('save', 'label', Mage::helper('brandlogo')->__('Save Category'));
        $this->_updateButton('delete', 'label', Mage::helper('brandlogo')->__('Delete Category'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('category_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'category_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'category_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('category_data') && Mage::registry('category_data')->getId()) {
            return Mage::helper('brandlogo')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('category_data')->getTitle()));
        } else {
            return Mage::helper('brandlogo')->__('Add Category');
        }
    }

}