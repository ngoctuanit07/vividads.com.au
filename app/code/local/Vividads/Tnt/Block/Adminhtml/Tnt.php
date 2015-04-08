<?php
class Vividads_Tnt_Block_Adminhtml_Tnt extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_tnt';
    $this->_blockGroup = 'tnt';
    $this->_headerText = Mage::helper('tnt')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('tnt')->__('Add Item');
    parent::__construct();
  }
}