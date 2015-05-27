<?php

class ArtworkUploader_Upload_Model_Mysql4_Upload extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the proof_id refers to the key field in your database table.
        $this->_init('upload/upload', 'entity_id');
    }
}