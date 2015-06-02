<?php

class MW_Mwcore_Block_Adminhtml_Notification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'mwcore';
        $this->_controller = 'adminhtml_notification';
        
        $this->_updateButton('save', 'label', Mage::helper('mwcore')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('mwcore')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('mwcore_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'mwcore_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'mwcore_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('mwcore_data') && Mage::registry('mwcore_data')->getId() ) {
            return Mage::helper('mwcore')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('mwcore_data')->getId()));
        } else {
            return Mage::helper('mwcore')->__('Add Item');
        }
    }
}