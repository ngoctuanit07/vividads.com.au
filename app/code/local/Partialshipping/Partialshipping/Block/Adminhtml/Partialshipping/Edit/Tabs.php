<?php

class Partialshipping_Partialshipping_Block_Adminhtml_Partialshipping_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('partialshipping_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('partialshipping')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('partialshipping')->__('Item Information'),
          'title'     => Mage::helper('partialshipping')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('partialshipping/adminhtml_partialshipping_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}