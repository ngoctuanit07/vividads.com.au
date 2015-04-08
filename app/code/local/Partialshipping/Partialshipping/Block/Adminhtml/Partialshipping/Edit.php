<?php

class Partialshipping_Partialshipping_Block_Adminhtml_Partialshipping_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'partialshipping';
        $this->_controller = 'adminhtml_partialshipping';
        
        $this->_updateButton('save', 'label', Mage::helper('partialshipping')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('partialshipping')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('partialshipping_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'partialshipping_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'partialshipping_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('partialshipping_data') && Mage::registry('partialshipping_data')->getId() ) {
            return Mage::helper('partialshipping')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('partialshipping_data')->getTitle()));
        } else {
            return Mage::helper('partialshipping')->__('Add Item');
        }
    }
}