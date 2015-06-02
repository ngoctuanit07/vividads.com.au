<?php
class Partialshipping_Partialshipping_Block_Adminhtml_Partialshipping extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_partialshipping';
    $this->_blockGroup = 'partialshipping';
    $this->_headerText = Mage::helper('partialshipping')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('partialshipping')->__('Add Item');
    parent::__construct();
  }
}