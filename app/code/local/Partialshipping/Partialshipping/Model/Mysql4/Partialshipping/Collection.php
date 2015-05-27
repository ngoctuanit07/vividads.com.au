<?php

class Partialshipping_Partialshipping_Model_Mysql4_Partialshipping_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('partialshipping/partialshipping');
    }
}