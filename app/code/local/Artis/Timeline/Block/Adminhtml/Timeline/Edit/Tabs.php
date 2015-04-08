<?php

class Artis_Timeline_Block_Adminhtml_Timeline_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('timeline_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('timeline')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('timeline')->__('Item Information'),
          'title'     => Mage::helper('timeline')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('timeline/adminhtml_timeline_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}