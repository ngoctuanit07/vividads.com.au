<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Zoom_Model_Source_CarouselDirection extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
			array('value' => 'under', 'label' => $hlp->__('Under the main image')),
			array('value' => 'left', 'label' => $hlp->__('To the left of the main image')),
		);
	}
	
}