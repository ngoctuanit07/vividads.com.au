<?php

class Artis_Vendorload_Block_Adminhtml_VendorLoad_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendorload_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendorload')->__('Vendor Load Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendorload')->__('Vendor Load Information'),
          'title'     => Mage::helper('vendorload')->__('Vendor Load Information'),
          'content'   => $this->getLayout()->createBlock('vendorload/adminhtml_vendorload_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
