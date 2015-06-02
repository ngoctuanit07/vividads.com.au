<?php

class AsiaConnect_Gallery_Block_Adminhtml_Album_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'adminhtml_album';
        
        $this->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Album'));
        $this->_updateButton('delete', 'label', Mage::helper('gallery')->__('Delete Album'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('album_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'album_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'album_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('album_data') && Mage::registry('album_data')->getId() ) {
            return Mage::helper('gallery')->__("Edit Album '%s'", $this->htmlEscape(Mage::registry('album_data')->getTitle()));
        } else {
            return Mage::helper('gallery')->__('Add Album');
        }
    }
}