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
class MD_Vividslider_Block_Adminhtml_Vividslider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
     $this->_controller = 'adminhtml_vividslider';
     $this->_blockGroup = 'vividslider';
     $this->_headerText = Mage::helper('vividslider')->__('Vividads Slider Manager');
     $this->_addButtonLabel = Mage::helper('vividslider')->__('Add New Vivid Slider');
     
	 parent::__construct();	
	 $this->setTemplate('vividslider/vividslider.phtml');
  }
  
  
  /**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Vividslider
     */
    protected function _prepareLayout()
    {      
	   // $this->_addButton('add_new', array(
        //    'label'   => Mage::helper('catalog')->__('Add Product'),
         //   'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
          //  'class'   => 'add'
      //  ));

      //  $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/catalog_product_grid', 'product.grid'));
        return parent::_prepareLayout();
    }

    
  
  
  /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
  
}