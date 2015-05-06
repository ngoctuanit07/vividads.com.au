<?php
/**
 * Class Aptoplex_EasyUploader_Model_Adminhtml_System_Config_Source_Ssl
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Adminhtml_System_Config_Source_Ssl extends Mage_Core_Model_Abstract {

    /**
     * Returns array of order SSL connection types
     *
     * @return array
     */
    public function toOptionArray() {
        $return = array(
            'none'  => Mage::helper('adminhtml')->__('None'),
            'ssl'   => Mage::helper('adminhtml')->__('SSL'),
            'tls'   => Mage::helper('adminhtml')->__('SSL TLS')
        );
        return $return;
    }
}