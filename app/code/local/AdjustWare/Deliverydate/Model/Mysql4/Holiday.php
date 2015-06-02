<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     5aaeipF7ooGEnIcLG9rWordPqCAsU9nCJvGBjy56tC
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @author Adjustware
 */ 
class AdjustWare_Deliverydate_Model_Mysql4_Holiday extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('adjdeliverydate/holiday', 'holiday_id');
    }
}