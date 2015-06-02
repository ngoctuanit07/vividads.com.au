<?php
class Artis_Timeline_Block_Timeline extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTimeline()     
     { 
        if (!$this->hasData('timeline')) {
            $this->setData('timeline', Mage::registry('timeline'));
        }
        return $this->getData('timeline');
        
    }
}