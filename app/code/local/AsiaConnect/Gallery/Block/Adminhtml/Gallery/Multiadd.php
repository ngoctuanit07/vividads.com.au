<?php

class AsiaConnect_Gallery_Block_Adminhtml_Gallery_Multiadd extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'adminhtml_gallery';
        $this->_mode = 'multiadd';
        
        $this->removeButton('back')
            ->removeButton('reset')
            ->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Photo'))
            ->_updateButton('save', 'id', 'upload_button');
    }

    public function getHeaderText()
    {
            return Mage::helper('gallery')->__('Add Item');
    }
}