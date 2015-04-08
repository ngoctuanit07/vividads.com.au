<?php 

/**
 * List of smtp authenticates types
 */
class Cateyes_Phoneorder_Adminhtml_Model_System_Config_Source_Authenticate
{
    /**
     * get the styles
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'none', 'label'=>Mage::helper('phoneorder')->__('None')),
            array('value' => 'login', 'label'=>Mage::helper('phoneorder')->__('Login')),
            array('value' => 'plain', 'label'=>Mage::helper('phoneorder')->__('Plain')),
            array('value' => 'crammd5', 'label'=>Mage::helper('phoneorder')->__('CRAM-MD5')),
        );
    }

}