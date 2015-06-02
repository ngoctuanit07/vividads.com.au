<?php

class Vividads_Tnt_Block_Adminhtml_Tnt_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'tnt';
        $this->_controller = 'adminhtml_tnt';
        
        $this->_updateButton('save', 'label', Mage::helper('tnt')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('tnt')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('tnt_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'tnt_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'tnt_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('tnt_data') && Mage::registry('tnt_data')->getId() ) {
            return Mage::helper('tnt')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('tnt_data')->getTitle()));
        } else {
            return Mage::helper('tnt')->__('Add Item');
        }
    }
}