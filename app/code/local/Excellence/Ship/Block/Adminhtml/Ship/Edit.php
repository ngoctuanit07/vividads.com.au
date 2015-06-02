<?php

class Excellence_Ship_Block_Adminhtml_Ship_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ship';
        $this->_controller = 'adminhtml_ship';
        
        $this->_updateButton('save', 'label', Mage::helper('ship')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('ship')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('ship_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'ship_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'ship_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('ship_data') && Mage::registry('ship_data')->getId() ) {
            return Mage::helper('ship')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('ship_data')->getTitle()));
        } else {
            return Mage::helper('ship')->__('Add Item');
        }
    }
}