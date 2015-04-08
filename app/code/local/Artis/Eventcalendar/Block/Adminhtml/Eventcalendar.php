<?php
class Artis_Eventcalendar_Block_Adminhtml_Eventcalendar extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_eventcalendar';
    $this->_blockGroup = 'eventcalendar';
    $this->_headerText = Mage::helper('eventcalendar')->__('Event Calendar');
    $this->_addButtonLabel = Mage::helper('eventcalendar')->__('Add Item');
    parent::__construct();
    
    $this->_removeButton('add');
  }
}