<?php

class AsiaConnect_Gallery_Model_Mysql4_Gallery extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('gallery/gallery', 'gallery_id');
    }
}