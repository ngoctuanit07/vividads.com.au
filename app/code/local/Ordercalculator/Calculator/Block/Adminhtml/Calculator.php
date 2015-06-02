<?php
class Ordercalculator_Calculator_Block_Adminhtml_Calculator extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_calculator';
    $this->_blockGroup = 'calculator';
    $this->_headerText = Mage::helper('calculator')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('calculator')->__('Add Item');
    parent::__construct();
  }
}