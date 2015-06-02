<?php

class Factoryphotos_Factoryphotos_Model_Mysql4_Factoryphotos extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the factoryphotos_id refers to the key field in your database table.
        $this->_init('factoryphotos/factoryphotos', 'factoryphotos_id');
    }
}