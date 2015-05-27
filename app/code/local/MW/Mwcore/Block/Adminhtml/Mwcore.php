<?php
class MW_Mwcore_Block_Adminhtml_Mwcore extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mwcore';
    $this->_blockGroup = 'mwcore';
    $this->_headerText = Mage::helper('mwcore')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('mwcore')->__('Add Item');
    parent::__construct();
  }
  
}