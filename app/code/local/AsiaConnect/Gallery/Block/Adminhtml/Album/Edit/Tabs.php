<?php

class AsiaConnect_Gallery_Block_Adminhtml_Album_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('album_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('gallery')->__('Album Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('information_form_section', array(
          'label'     => Mage::helper('gallery')->__('Album Information'),
          'title'     => Mage::helper('gallery')->__('Album Information'),
          'content'   => $this->getLayout()->createBlock('gallery/adminhtml_album_edit_tab_information')->toHtml(),
      ));
      $this->addTab('configuration_form_section', array(
          'label'     => Mage::helper('gallery')->__('Album Configuration'),
          'title'     => Mage::helper('gallery')->__('Album Configuration'),
          'content'   => $this->getLayout()->createBlock('gallery/adminhtml_album_edit_tab_configuration')->toHtml(),
      ));
      
      $this->addTab('meta_section', array(
		  'label'     => Mage::helper('gallery')->__('Meta Data'),
		  'title'     => Mage::helper('gallery')->__('Meta Data'),
		  'content'   => $this->getLayout()->createBlock('gallery/adminhtml_album_edit_tab_meta')->toHtml(),
	  ));
      return parent::_beforeToHtml();
  }
}