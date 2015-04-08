<?php
class Artis_Vendorload_Block_Adminhtml_VendorLoad extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vendorload';
    $this->_blockGroup = 'vendorload';
    $this->_headerText = Mage::helper('vendorload')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('vendorload')->__('Add Item');
    parent::__construct();
  }
}