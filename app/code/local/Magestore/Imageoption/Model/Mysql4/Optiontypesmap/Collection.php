<?php

class Magestore_Imageoption_Model_Mysql4_Optiontypesmap_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imageoption/optiontypesmap');
    }
}