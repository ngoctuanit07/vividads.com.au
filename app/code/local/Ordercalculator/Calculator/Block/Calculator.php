<?php
class Ordercalculator_Calculator_Block_Calculator extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCalculator()     
     { 
        if (!$this->hasData('calculator')) {
            $this->setData('calculator', Mage::registry('calculator'));
        }
        return $this->getData('calculator');
        
    }
}