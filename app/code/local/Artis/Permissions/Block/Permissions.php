<?php
class Artis_Permissions_Block_Permissions extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPermissions()     
     { 
        if (!$this->hasData('permissions')) {
            $this->setData('permissions', Mage::registry('permissions'));
        }
        return $this->getData('permissions');
        
    }
}