<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Zoom_Model_Source_ChangeWith extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
			array('value' => 'mouse', 'label' => $hlp->__('On Mouse Over')),
			array('value' => 'click',  'label' => $hlp->__('On Click')),
		);
	}
	
}