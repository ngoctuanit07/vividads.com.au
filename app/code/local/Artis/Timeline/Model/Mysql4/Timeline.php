<?php

class Artis_Timeline_Model_Mysql4_Timeline extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the timeline_id refers to the key field in your database table.
        $this->_init('timeline/timeline', 'timeline_id');
    }
}