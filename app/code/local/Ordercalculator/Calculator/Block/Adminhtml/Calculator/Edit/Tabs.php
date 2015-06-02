<?php

class Ordercalculator_Calculator_Block_Adminhtml_Calculator_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('calculator_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('calculator')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('calculator')->__('Item Information'),
          'title'     => Mage::helper('calculator')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('calculator/adminhtml_calculator_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}