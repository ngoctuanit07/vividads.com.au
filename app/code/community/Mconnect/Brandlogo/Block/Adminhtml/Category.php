<?php
class Mconnect_Brandlogo_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_category';
    $this->_blockGroup = 'brandlogo';
    $this->_headerText = Mage::helper('brandlogo')->__('Category Manager');
    $this->_addButtonLabel = Mage::helper('brandlogo')->__('Add Category');
    parent::__construct();
  }
  
  
}