<?php
class Mconnect_Brandlogo_Block_Adminhtml_Brandlogo extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_brandlogo';
    $this->_blockGroup = 'brandlogo';
    $this->_headerText = Mage::helper('brandlogo')->__('Brand Logo Manager');
    $this->_addButtonLabel = Mage::helper('brandlogo')->__('Add Brand Logo');
    parent::__construct();
  }
}