<?php

class Artis_Externalform_Block_Adminhtml_Externalform_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('externalform_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('externalform')->__('Create Iframe'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('externalform')->__('Create Iframe'),
          'title'     => Mage::helper('externalform')->__('Create Iframe'),
          'content'   => $this->getLayout()->createBlock('externalform/adminhtml_externalform_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}