<?php

class Artis_Externalform_Block_Adminhtml_Externalform_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
	$this->_removeButton('delete');
	$this->_removeButton('save');
	$this->_removeButton('reset');

                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'externalform';
        $this->_controller = 'adminhtml_externalform';
        
        $this->_updateButton('save', 'label', Mage::helper('externalform')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('externalform')->__('Delete Item'));
		
        /*$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);*/

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('externalform_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'externalform_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'externalform_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('externalform_data') && Mage::registry('externalform_data')->getId() ) {
            return Mage::helper('externalform')->__("Create Iframe '%s'", $this->htmlEscape(Mage::registry('externalform_data')->getTitle()));
        } else {
            return Mage::helper('externalform')->__('Create Iframe');
        }
    }
}