<?php
class AsiaConnect_Gallery_Block_Adminhtml_Review extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_review';
    $this->_blockGroup = 'gallery';
    $this->_headerText = Mage::helper('gallery')->__('Review Manager');
    $this->_addButtonLabel = Mage::helper('gallery')->__('Add Review');
    parent::__construct();
  }
}