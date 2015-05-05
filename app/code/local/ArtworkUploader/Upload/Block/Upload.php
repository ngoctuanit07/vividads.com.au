<?php
class ArtworkUploader_Upload_Block_Upload extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'upload';
    $this->_blockGroup = 'upload';
   // $this->_headerText = Mage::helper('upload')->__('Item Manager');
   // $this->_addButtonLabel = Mage::helper('upload')->__('Add Item');
    parent::__construct();
  }
}