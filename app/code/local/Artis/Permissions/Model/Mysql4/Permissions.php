<?php

class Artis_Permissions_Model_Mysql4_Permissions extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the permissions_id refers to the key field in your database table.
        $this->_init('permissions/permissions', 'permissions_id');
    }
}