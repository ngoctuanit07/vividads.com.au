<?php

class Ordercalculator_Calculator_Block_Adminhtml_Calculator_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'calculator';
        $this->_controller = 'adminhtml_calculator';
        
        $this->_updateButton('save', 'label', Mage::helper('calculator')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('calculator')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('calculator_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'calculator_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'calculator_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('calculator_data') && Mage::registry('calculator_data')->getId() ) {
            return Mage::helper('calculator')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('calculator_data')->getTitle()));
        } else {
            return Mage::helper('calculator')->__('Add Item');
        }
    }
}