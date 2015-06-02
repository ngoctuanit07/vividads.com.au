<?php 

/**
 * List of smtp ssl types
 */
class Cateyes_Phoneorder_Adminhtml_Model_System_Config_Source_Ssl
{
    /**
     * get the styles
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'none', 'label'=>Mage::helper('phoneorder')->__('No Ssl')),
            array('value' => 'ssl', 'label'=>Mage::helper('phoneorder')->__('SSL')),
            array('value' => 'tls', 'label'=>Mage::helper('phoneorder')->__('SSL TLS')),
        );
    }

}