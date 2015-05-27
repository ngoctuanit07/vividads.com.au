<?php

class Factoryphotos_Factoryphotos_Block_Adminhtml_Factoryphotos_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'factoryphotos';
        $this->_controller = 'adminhtml_factoryphotos';
        
        $this->_updateButton('save', 'label', Mage::helper('factoryphotos')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('factoryphotos')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('factoryphotos_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'factoryphotos_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'factoryphotos_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('factoryphotos_data') && Mage::registry('factoryphotos_data')->getId() ) {
            return Mage::helper('factoryphotos')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('factoryphotos_data')->getTitle()));
        } else {
            return Mage::helper('factoryphotos')->__('Add Item');
        }
    }
    protected function _prepareLayout()
    {
    
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $return;
    }
}