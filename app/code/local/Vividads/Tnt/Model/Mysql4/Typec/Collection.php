<?php

class Vividads_Tnt_Model_Mysql4_Typec_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct(){
        parent::_construct();
        $this->_init('tnt/typec');
    }
}