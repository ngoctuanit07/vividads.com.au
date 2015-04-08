<?php
class MW_Mwcore_Model_Checktime
{
	function updateStatus()
	{
		Mage::helper('mwcore')->updatestatus();	
	}
	
	function getNotification()
	{	
		 Mage::log(Mage::helper('mwcore')->getServerNotification());	
	}
}