<?php
class Artis_Systemalert_Block_Adminhtml_Systemalert extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_systemalert';
    $this->_blockGroup = 'systemalert';
    $this->_headerText = Mage::helper('systemalert')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('systemalert')->__('Add Item');
    parent::__construct();
    
    $this->_removeButton('add');
  }
}