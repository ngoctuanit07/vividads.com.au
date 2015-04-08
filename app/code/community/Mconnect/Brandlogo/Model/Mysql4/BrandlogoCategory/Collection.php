<?php

class Mconnect_Brandlogo_Model_Mysql4_BrandlogoCategory_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('brandlogo/brandlogo_category');
    }

}