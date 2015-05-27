<?php

class Artis_Calendar_Block_Adminhtml_Calendar_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'calendar';
        $this->_controller = 'adminhtml_calendar';
        
        $this->_updateButton('save', 'label', Mage::helper('calendar')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('calendar')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('calendar_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'calendar_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'calendar_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('calendar_data') && Mage::registry('calendar_data')->getId() ) {
            return Mage::helper('calendar')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('calendar_data')->getTitle()));
        } else {
            return Mage::helper('calendar')->__('Add Item');
        }
    }
}