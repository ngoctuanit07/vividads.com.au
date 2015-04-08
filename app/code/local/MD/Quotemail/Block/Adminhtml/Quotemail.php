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
class MD_Quotemail_Block_Adminhtml_Quotemail extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_quotemail';
    $this->_blockGroup = 'quotemail';
    $this->_headerText = Mage::helper('quotemail')->__('Pre-Defined Emails Template Manager');
    $this->_addButtonLabel = Mage::helper('quotemail')->__('Add Email Template');
    parent::__construct();
  }
}