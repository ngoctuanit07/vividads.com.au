<?php

class Artis_Timeline_Block_Adminhtml_Timeline_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'timeline';
        $this->_controller = 'adminhtml_timeline';
        
        $this->_updateButton('save', 'label', Mage::helper('timeline')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('timeline')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('timeline_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'timeline_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'timeline_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('timeline_data') && Mage::registry('timeline_data')->getId() ) {
            return Mage::helper('timeline')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('timeline_data')->getTitle()));
        } else {
            return Mage::helper('timeline')->__('Add Item');
        }
    }
}