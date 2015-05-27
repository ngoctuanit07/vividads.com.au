<?php

class MW_Mwcore_Model_Mysql4_Notification extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the mcore_id refers to the key field in your database table.
        $this->_init('mwcore/notification', 'notification_id');
    }
}