<?php
class Artis_Calendar_Block_Adminhtml_Calendar extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_calendar';
    $this->_blockGroup = 'calendar';
    $this->_headerText = Mage::helper('calendar')->__('Uplode Calender List');
    $this->_addButtonLabel = Mage::helper('calendar')->__('Add Item');
    parent::__construct();
    
   // $this->_removeButton('add');
  }
}