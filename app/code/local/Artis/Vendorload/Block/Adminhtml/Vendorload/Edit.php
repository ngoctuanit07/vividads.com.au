<?php

class Artis_Vendorload_Block_Adminhtml_VendorLoad_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendorload';
        $this->_controller = 'adminhtml_vendorload';
        
        $this->_updateButton('save', 'label', Mage::helper('vendorload')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('vendorload')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendorload_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendorload_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendorload_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('vendorload_data') && Mage::registry('vendorload_data')->getId() ) {
            return Mage::helper('vendorload')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('vendorload_data')->getTitle()));
        } else {
            return Mage::helper('vendorload')->__('Add Item');
        }
    }
}