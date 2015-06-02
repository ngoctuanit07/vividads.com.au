<?php

class AsiaConnect_Gallery_Block_Adminhtml_Review_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('review_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('gallery')->__('Review Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('gallery')->__('Review Information'),
          'title'     => Mage::helper('gallery')->__('Review Information'),
          'content'   => $this->getLayout()->createBlock('gallery/adminhtml_review_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}