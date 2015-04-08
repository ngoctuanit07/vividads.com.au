<?php

class AsiaConnect_Gallery_Block_Adminhtml_Review_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'adminhtml_review';
        
        $this->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Review'));
        $this->_updateButton('delete', 'label', Mage::helper('gallery')->__('Delete Review'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('review_data') && Mage::registry('review_data')->getId() ) {
            return Mage::helper('gallery')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('review_data')->getCreateTime()));
        } else {
            return Mage::helper('gallery')->__('Add Item');
        }
    }
}