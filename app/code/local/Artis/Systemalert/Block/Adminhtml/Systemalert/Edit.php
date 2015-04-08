<?php

class Artis_Systemalert_Block_Adminhtml_Systemalert_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'systemalert';
        $this->_controller = 'adminhtml_systemalert';
        
        $this->_updateButton('save', 'label', Mage::helper('systemalert')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('systemalert')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('systemalert_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'systemalert_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'systemalert_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('systemalert_data') && Mage::registry('systemalert_data')->getId() ) {
            return Mage::helper('systemalert')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('systemalert_data')->getTitle()));
        } else {
            return Mage::helper('systemalert')->__('Add Item');
        }
    }
}