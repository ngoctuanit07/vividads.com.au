<?php

class Vividads_Tnt_Block_Adminhtml_Tnt_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('tnt_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('tnt')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('tnt')->__('Item Information'),
          'title'     => Mage::helper('tnt')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('tnt/adminhtml_tnt_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}