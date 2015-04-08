<?php 

/**
 * List of predefined styles
 */
class Cateyes_Phoneorder_Adminhtml_Model_System_Config_Source_Styles
{
    /**
     * get the styles
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'pink', 'label'=>Mage::helper('phoneorder')->__('Pink')),
            array('value' => 'green', 'label'=>Mage::helper('phoneorder')->__('Green')),
            array('value' => 'blue', 'label'=>Mage::helper('phoneorder')->__('Blue')),
        );
    }

}