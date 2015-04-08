<?php

class MW_Mwcore_Block_Adminhtml_Mwcore_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('mwcore_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mwcore')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('mwcore')->__('Item Information'),
          'title'     => Mage::helper('mwcore')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('mwcore/adminhtml_mwcore_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}