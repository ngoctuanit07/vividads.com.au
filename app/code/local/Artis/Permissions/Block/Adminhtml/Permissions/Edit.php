<?php

class Artis_Permissions_Block_Adminhtml_Permissions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'permissions';
        $this->_controller = 'adminhtml_permissions';
        
        $this->_updateButton('save', 'label', Mage::helper('permissions')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('permissions')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('permissions_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'permissions_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'permissions_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('permissions_data') && Mage::registry('permissions_data')->getId() ) {
            return Mage::helper('permissions')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('permissions_data')->getTitle()));
        } else {
            return Mage::helper('permissions')->__('Add Item');
        }
    }
}