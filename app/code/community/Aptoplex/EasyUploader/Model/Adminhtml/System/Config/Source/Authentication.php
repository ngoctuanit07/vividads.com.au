<?php
/**
 * Class Aptoplex_EasyUploader_Model_Adminhtml_System_Config_Source_Authentication
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Adminhtml_System_Config_Source_Authentication extends Mage_Core_Model_Abstract {

    /**
     * Returns array of authentication types
     *
     * @return array
     */
    public function toOptionArray() {
        $return = array(
            'none'  => Mage::helper('adminhtml')->__('None'),
            'login'   => Mage::helper('adminhtml')->__('Login'),
            'plain'   => Mage::helper('adminhtml')->__('Plain'),
            'crammd5'    => Mage::helper('adminhtml')->__('CRAM-MD5')
        );
        return $return;
    }
}