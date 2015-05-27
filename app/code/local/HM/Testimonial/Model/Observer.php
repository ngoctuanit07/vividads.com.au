<?php
class Hm_Testimonial_Model_Observer
{
	
	public function checkLicense($o)
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules; 
		$modules2 = array_keys((array)Mage::getConfig()->getNode('modules')->children()); 
		if(!in_array('MW_Mwcore', $modules2) || !$modulesArray['MW_Mwcore']->is('active') || Mage::getStoreConfig('mwcore/config/enabled')!=1)
		{
			Mage::helper('testimonial')->disableConfig();
		}
		
	}

}
