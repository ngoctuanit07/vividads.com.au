<?php

class AsiaConnect_Gallery_Block_Adminhtml_Gallery_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'adminhtml_gallery';
        
        $this->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Photo'));
        $this->_updateButton('delete', 'label', Mage::helper('gallery')->__('Delete Photo'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('gallery_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'gallery_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'gallery_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('gallery_data') && Mage::registry('gallery_data')->getId() ) {
            return Mage::helper('gallery')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('gallery_data')->getTitle()));
        } else {
            return Mage::helper('gallery')->__('Add Item');
        }
    }
}