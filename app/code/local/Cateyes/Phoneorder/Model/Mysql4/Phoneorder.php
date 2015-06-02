<?php
 
class Cateyes_Phoneorder_Model_Mysql4_Phoneorder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('phoneorder/phoneorder', 'phoneorder_id');
    }
}