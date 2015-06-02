<?php
class MW_Mwcore_Model_Observer
{	
	public function adloginsuccess($o)
	{		 	
		Mage::getSingleton('core/session')->unsNotification();				
		Mage::helper('mwcore')->updatestatus();
	}	
	
	public function logoutupdate($o){
		Mage::getSingleton('core/session')->unsNotification();	
		Mage::helper('mwcore')->updatestatus();
		Mage::helper('mwcore')->resetSpecNotification();	
	}
	
	public function reloadAdmin($o)
	{    
		Mage::getSingleton('core/session')->unsNotification();			
	}
	
}