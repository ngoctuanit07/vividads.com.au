<?php
/**
 * Class Aptoplex_EasyUploader_Model_Resource_Upload_Collection
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Resource_Upload_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    /**
     * Internal constructor
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('aptoplex_easyuploader/upload');
    }
}