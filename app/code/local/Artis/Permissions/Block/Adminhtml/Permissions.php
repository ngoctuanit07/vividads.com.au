<?php
class Artis_Permissions_Block_Adminhtml_Permissions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_permissions';
    $this->_blockGroup = 'permissions';
    $this->_headerText = Mage::helper('permissions')->__('Manage Permissions');
    $this->_addButtonLabel = Mage::helper('permissions')->__('Add Item');
    parent::__construct();
    
    $this->_removeButton('add');
  }
}