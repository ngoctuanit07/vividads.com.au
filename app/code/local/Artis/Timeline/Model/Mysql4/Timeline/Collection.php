<?php

class Artis_Timeline_Model_Mysql4_Timeline_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('timeline/timeline');
    }
}