<?php
class Hm_Testimonial_Block_Adminhtml_Testimonial extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_testimonial';
    $this->_blockGroup = 'testimonial';
	$this->setTemplate('hm_testimonial/testimonials.phtml');   
    $this->_headerText = Mage::helper('testimonial')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('testimonial')->__('Add Item');
    parent::__construct();
	 
  }
  
    protected function _prepareLayout()
    {
        /**
         * Display store switcher if system has more one store
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->setChild('store_switcher',
                $this->getLayout()->createBlock('adminhtml/store_switcher')
                    ->setUseConfirm(false)
                    ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
            );
        }
        $this->setChild('grid', $this->getLayout()->createBlock('testimonial/manage_testimonial_grid', 'testimonial.grid'));
        return parent::_prepareLayout();
    }  
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
  
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }  
}
