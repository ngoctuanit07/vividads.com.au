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
class AdjustWare_Deliverydate_Model_Validator_Required
{
    public function validate($label, $value){
        $errors = array();
        if (!$value){
            $errors[] = Mage::helper('adjdeliverydate')->__('Please provide %s' , $label);
        }
        return $errors;
    }
}