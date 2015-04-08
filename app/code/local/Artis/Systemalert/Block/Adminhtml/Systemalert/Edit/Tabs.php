<?php

class Artis_Systemalert_Block_Adminhtml_Systemalert_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('systemalert_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('systemalert')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('systemalert')->__('Item Information'),
          'title'     => Mage::helper('systemalert')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('systemalert/adminhtml_systemalert_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}