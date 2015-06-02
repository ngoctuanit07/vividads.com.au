<?php

class Artis_Eventcalendar_Block_Adminhtml_Eventcalendar_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('eventcalendar_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('eventcalendar')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('eventcalendar')->__('Item Information'),
          'title'     => Mage::helper('eventcalendar')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('eventcalendar/adminhtml_eventcalendar_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}