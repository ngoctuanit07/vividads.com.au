<?php

class Artis_Eventcalendar_Block_Adminhtml_Eventcalendar_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'eventcalendar';
        $this->_controller = 'adminhtml_eventcalendar';
        
        $this->_updateButton('save', 'label', Mage::helper('eventcalendar')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('eventcalendar')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('eventcalendar_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'eventcalendar_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'eventcalendar_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('eventcalendar_data') && Mage::registry('eventcalendar_data')->getId() ) {
            return Mage::helper('eventcalendar')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('eventcalendar_data')->getTitle()));
        } else {
            return Mage::helper('eventcalendar')->__('Add Item');
        }
    }
}