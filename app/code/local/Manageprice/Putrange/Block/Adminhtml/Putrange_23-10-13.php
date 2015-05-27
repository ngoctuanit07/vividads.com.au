<?php
class Manageprice_Putrange_Block_Adminhtml_Putrange extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_putrange';
    $this->_blockGroup = 'putrange';
    $this->_headerText = Mage::helper('putrange')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('putrange')->__('Add Item');
    parent::__construct();
  }
}