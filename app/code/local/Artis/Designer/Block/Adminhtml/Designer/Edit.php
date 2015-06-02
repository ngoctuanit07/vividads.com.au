<?php

class Artis_Designer_Block_Adminhtml_Designer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'designer';
        $this->_controller = 'adminhtml_designer';
        
        $this->_updateButton('save', 'label', Mage::helper('designer')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('designer')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('designer_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'designer_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'designer_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('designer_data') && Mage::registry('designer_data')->getId() ) {
            return Mage::helper('designer')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('designer_data')->getTitle()));
        } else {
            return Mage::helper('designer')->__('Add Item');
        }
    }
}