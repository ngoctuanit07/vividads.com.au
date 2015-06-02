<?php
class Excellence_Ship_Block_Ship extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getShip()     
     { 
        if (!$this->hasData('ship')) {
            $this->setData('ship', Mage::registry('ship'));
        }
        return $this->getData('ship');
        
    }
}