<?php

class Artis_Designer_Block_Adminhtml_Designer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('designer_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('designer')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('designer')->__('Item Information'),
          'title'     => Mage::helper('designer')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('designer/adminhtml_designer_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}