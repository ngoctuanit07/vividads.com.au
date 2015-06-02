<?php
class Vividads_Tnt_Block_Tnt extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTnt()     
     { 
        if (!$this->hasData('tnt')) {
            $this->setData('tnt', Mage::registry('tnt'));
        }
        return $this->getData('tnt');
        
    }
}