<?php
class Partialshipping_Partialshipping_Block_Partialshipping extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPartialshipping()     
     { 
        if (!$this->hasData('partialshipping')) {
            $this->setData('partialshipping', Mage::registry('partialshipping'));
        }
        return $this->getData('partialshipping');
        
    }
}