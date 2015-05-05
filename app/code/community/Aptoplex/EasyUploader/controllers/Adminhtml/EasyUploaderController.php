<?php
/**
 * Class Aptoplex_EasyUploader_Adminhtml_EasyUploaderController
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Adminhtml_EasyUploaderController extends Mage_Adminhtml_Controller_Action {

    /**
     * Data helper
     *
     * @var Aptoplex_EasyUploader_Helper_Data
     */
    protected  $_helper;

    /**
     * Internal constructor
     */
    public function _construct() {
        parent::_construct();
        $this->_helper = Mage::helper('aptoplex_easyuploader/data');
    }

    /**
     * Moves any existing uploaded files (if we're not running in demo mode).
     */
    public function moveExistingUploadsAction() {
        if (!Aptoplex_EasyUploader_Helper_Data::RUN_IN_DEMO_MODE) {
            $newPath = Mage::getBaseDir() . DS . Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_UPLOAD_PATH, null);
            $this->_helper->moveExistingUploads($newPath, 0755, true);
        }
    }
}