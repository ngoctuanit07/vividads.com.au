<?php
class Manageprice_Putrange_Block_Putrange extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPutrange()     
     { 
        if (!$this->hasData('putrange')) {
            $this->setData('putrange', Mage::registry('putrange'));
        }
        return $this->getData('putrange');
        
    }
}