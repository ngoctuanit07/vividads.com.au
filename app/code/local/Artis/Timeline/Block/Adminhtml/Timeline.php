<?php
class Artis_Timeline_Block_Adminhtml_Timeline extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_timeline';
    $this->_blockGroup = 'timeline';
    $this->_headerText = Mage::helper('timeline')->__('Timeline Manager');
    $this->_addButtonLabel = Mage::helper('timeline')->__('Add Item');
    parent::__construct();
    
    $this->_removeButton('add');
  }
}