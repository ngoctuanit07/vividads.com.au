<?php
/**
 *
 */

class FMA_Reviewsplus_Adminhtml_Model_System_Config_Source_Emaildelay
{

   
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Immediately after an order')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('after 1 Day')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('after 2 Days')),
			array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('after 3 Days')),
			array('value' => 4, 'label'=>Mage::helper('adminhtml')->__('after 4 Days')),
			array('value' => 5, 'label'=>Mage::helper('adminhtml')->__('after 5 Days')),
			array('value' => 6, 'label'=>Mage::helper('adminhtml')->__('after 6 Days')),
			array('value' => 7, 'label'=>Mage::helper('adminhtml')->__('after 7 Days')),
        );
    }

}

?>