<?php
class MW_Mwcore_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_notification';
    $this->_blockGroup = 'mwcore';
    $this->_headerText = Mage::helper('mwcore')->__('Notification Manager');
    $this->_addButtonLabel = Mage::helper('mwcore')->__('Add Notification');
    parent::__construct();
  }
  
}