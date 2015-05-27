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
class MD_Vividslider_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('category_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vividslider')->__('Category Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vividslider')->__('Category Information'),
          'title'     => Mage::helper('vividslider')->__('Category Information'),
          'content'   => $this->getLayout()->createBlock('vividslider/adminhtml_category_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}