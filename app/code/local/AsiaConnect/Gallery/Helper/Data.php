<?php

class AsiaConnect_Gallery_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function checkEmailDuplicationAjaxUrl()
    {
        return $this->_getUrl('gallery/account/checkemailduplication');
    } 
    const MYCONFIG = "gallery/info/enabled";
	const MYNAME = "AsiaConnect_Gallery";
	
	public function myConfig(){
    	return self::MYCONFIG;
    }
	
	function disableConfig()
	{
			Mage::getSingleton('core/config')->saveConfig($this->myConfig(),0); 			
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,1);	
			 Mage::getConfig()->reinit();
	}
	
	function enableConfig()
	{
			Mage::getSingleton('core/config')->saveConfig($this->myConfig(),1); 			
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,0);
			Mage::getConfig()->reinit();			
	}
    
}