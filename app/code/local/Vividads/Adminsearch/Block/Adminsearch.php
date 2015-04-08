<?php
class Vividads_Adminsearch_Block_Adminsearch extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAdminsearch()     
     { 
        if (!$this->hasData('adminsearch')) {
            $this->setData('adminsearch', Mage::registry('adminsearch'));
        }
        return $this->getData('adminsearch');
        
    }
}