<?php
/**
 * MD_Vividslider.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Vividslider
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */

class MD_Vividslider_Block_Adminhtml_Vividslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vividslider_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vividslider')->__('Sliders General '));
  }
/**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $return;

    }
  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vividslider')->__('Vivid Slider Information'),
          'title'     => Mage::helper('vividslider')->__('Vivid Slider Information'),
          'content'   => $this->getLayout()->createBlock('vividslider/adminhtml_vividslider_edit_tab_form')->toHtml(),
      ));
	  
	  /*
	   $this->addTab('cateogry_section', array(
          'label'     => Mage::helper('vividslider')->__('Categories'),
          'title'     => Mage::helper('vividslider')->__('Cateogries'),
          'content'   => $this->getLayout()->createBlock('vividslider/adminhtml_vividslider_edit_tab_form')->toHtml(),
      ));
	  
	    $this->addTab('stores_section', array(
          'label'     => Mage::helper('vividslider')->__('Stores'),
          'title'     => Mage::helper('vividslider')->__('Stores'),
          'content'   => $this->getLayout()->createBlock('vividslider/adminhtml_vividslider_edit_tab_form')->toHtml(),
      ));
     */
      return parent::_beforeToHtml();
  }
}