<?php

class ArtworkUploader_Upload_Model_Mysql4_Upload_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('upload/upload');
    }
}