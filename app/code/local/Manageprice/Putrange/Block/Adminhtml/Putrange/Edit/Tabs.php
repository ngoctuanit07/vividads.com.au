<?php

class Manageprice_Putrange_Block_Adminhtml_Putrange_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('putrange_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('putrange')->__('Price Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('putrange')->__('Price Information'),
          'title'     => Mage::helper('putrange')->__('Price Information'),
          'content'   => $this->getLayout()->createBlock('putrange/adminhtml_putrange_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}