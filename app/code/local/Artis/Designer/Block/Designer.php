<?php
class Artis_Designer_Block_Designer extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getDesigner()     
     { 
        if (!$this->hasData('designer')) {
            $this->setData('designer', Mage::registry('designer'));
        }
        return $this->getData('designer');
        
    }
}