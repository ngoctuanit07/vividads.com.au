<?php
/**
 * MD_Quotemail.
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
 * @package    Quotemail
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */

class MD_Quotemail_Block_Adminhtml_Quotemail_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('quotemail_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('quotemail')->__('Pre-Defined Email Information'));
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
          'label'     => Mage::helper('quotemail')->__('Pre-Defined Email Information'),
          'title'     => Mage::helper('quotemail')->__('Pre-Defined Email Information'),
          'content'   => $this->getLayout()->createBlock('quotemail/adminhtml_quotemail_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}