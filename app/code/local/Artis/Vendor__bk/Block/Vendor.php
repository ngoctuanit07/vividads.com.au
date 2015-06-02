<?php
class Artis_Vendor_Block_Vendor extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getVendor()     
     { 
        if (!$this->hasData('vendor')) {
            $this->setData('vendor', Mage::registry('vendor'));
        }
        return $this->getData('vendor');
        
    }
}