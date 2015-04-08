<?php

class Manageprice_Putrange_Block_Adminhtml_Putrange_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'putrange';
        $this->_controller = 'adminhtml_putrange';
        
        $this->_updateButton('save', 'label', Mage::helper('putrange')->__('Save Price'));
        $this->_updateButton('delete', 'label', Mage::helper('putrange')->__('Delete Price'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('putrange_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'putrange_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'putrange_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('putrange_data') && Mage::registry('putrange_data')->getId() ) {
            return Mage::helper('putrange')->__("Edit Price '%s'", $this->htmlEscape(Mage::registry('putrange_data')->getTitle()));
        } else {
            return Mage::helper('putrange')->__('Add Price');
        }
    }
}