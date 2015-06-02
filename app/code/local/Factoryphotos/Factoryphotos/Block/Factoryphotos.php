<?php
class Factoryphotos_Factoryphotos_Block_Factoryphotos extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFactoryphotos()     
     { 
        if (!$this->hasData('factoryphotos')) {
            $this->setData('factoryphotos', Mage::registry('factoryphotos'));
        }
        return $this->getData('factoryphotos');
        
    }
}