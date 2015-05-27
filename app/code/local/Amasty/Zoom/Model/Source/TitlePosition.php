<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Zoom_Model_Source_TitlePosition extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
			array('value' => 'float', 'label' => $hlp->__('Float')),
			array('value' => 'inside', 'label' => $hlp->__('Inside')),
            array('value' => 'outside', 'label' => $hlp->__('Outside')),
            array('value' => 'over', 'label' => $hlp->__('Over')),
		);
	}
	
}