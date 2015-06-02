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
class AdjustWare_Deliverydate_Model_Source_Weekdays
{
    public function toOptionArray()
    {
        $aOptionArray = array();
        
        $aDaysOptions = Mage::getModel('adminhtml/system_config_source_locale_weekdays')->toOptionArray();

        $aOptionArray = array_merge(array(array('value' => 99, 'label'=>Mage::helper('sales')->__('No day'))), $aDaysOptions);
        
        return $aOptionArray;
    }
}