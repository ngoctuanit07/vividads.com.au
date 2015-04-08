<?php

class Artis_Permissions_Block_Adminhtml_Permissions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('permissions_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('permissions')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('permissions')->__('Item Information'),
          'title'     => Mage::helper('permissions')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('permissions/adminhtml_permissions_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}