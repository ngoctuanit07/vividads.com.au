<?php
class Artis_Systemalert_Block_Systemalert extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSystemalert()     
     { 
        if (!$this->hasData('systemalert')) {
            $this->setData('systemalert', Mage::registry('systemalert'));
        }
        return $this->getData('systemalert');
        
    }
}