<?php
/**
 * Class Aptoplex_EasyUploader_Model_Adminhtml_Config_Data
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Adminhtml_Config_Data extends Mage_Core_Model_Config_Data {

    protected $helper;

    public function _construct() {
        parent::_construct();
        $this->helper = Mage::helper('aptoplex_easyuploader');
    }

    public function save() {
        // Save to the database only if we're NOT in demo mode.
        if (!Aptoplex_EasyUploader_Helper_Data::RUN_IN_DEMO_MODE) {
            parent::save();
        }
        return $this;
    }
}