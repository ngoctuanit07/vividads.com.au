<?php

class Artis_Vendor_Block_Adminhtml_Vendor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vendor';
        $this->_controller = 'adminhtml_vendor';
        
        $this->_updateButton('save', 'label', Mage::helper('vendor')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('vendor')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('vendor_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'vendor_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'vendor_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('vendor_data') && Mage::registry('vendor_data')->getId() ) {
            return Mage::helper('vendor')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('vendor_data')->getTitle()));
        } else {
            return Mage::helper('vendor')->__('Add Item');
        }
    }
}