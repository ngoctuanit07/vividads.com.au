<?php

class Factoryphotos_Factoryphotos_Block_Adminhtml_Factoryphotos_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('factoryphotos_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('factoryphotos')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('factoryphotos')->__('Item Information'),
          'title'     => Mage::helper('factoryphotos')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('factoryphotos/adminhtml_factoryphotos_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}