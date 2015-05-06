<?php
/**
 * Class Aptoplex_EasyUploader_Model_Resource_Upload
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Resource_Upload extends Mage_Core_Model_Resource_Db_Abstract {

    /**
     * Internal constructor
     */
    protected function _construct() {
        $this->_init('aptoplex_easyuploader/upload', 'entity_id');
    }
}