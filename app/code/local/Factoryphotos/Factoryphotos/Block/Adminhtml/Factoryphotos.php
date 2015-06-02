<?php
class Factoryphotos_Factoryphotos_Block_Adminhtml_Factoryphotos extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_factoryphotos';
    $this->_blockGroup = 'factoryphotos';
    $this->_headerText = Mage::helper('factoryphotos')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('factoryphotos')->__('Add Item');
    parent::__construct();
  }
}