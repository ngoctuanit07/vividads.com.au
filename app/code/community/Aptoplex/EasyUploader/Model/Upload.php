<?php
/**
 * Class Aptoplex_EasyUploader_Model_Upload
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex 
 */
class Aptoplex_EasyUploader_Model_Upload extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('aptoplex_easyuploader/upload');
    }

    protected function _beforeSave() {
        parent::_beforeSave();
        return $this;
    }
}