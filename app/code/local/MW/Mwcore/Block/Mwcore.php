<?php
class MW_Mwcore_Block_Mwcore extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();		
    }
    
     public function getMcore()     
     { 
        if (!$this->hasData('mwcore')) {
            $this->setData('mwcore', Mage::registry('mwcore'));
        }
        return $this->getData('mwcore');
        
    }
    
    
}