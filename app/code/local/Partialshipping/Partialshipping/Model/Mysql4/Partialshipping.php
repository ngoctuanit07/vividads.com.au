<?php

class Partialshipping_Partialshipping_Model_Mysql4_Partialshipping extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the partialshipping_id refers to the key field in your database table.
        $this->_init('partialshipping/partialshipping', 'partialshipping_id');
    }
}