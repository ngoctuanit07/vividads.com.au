<?php

/**
 * MD_Quotemail.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Quotemail
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */

class MD_Quotemail_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('quotemail')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('quotemail')->__('Disabled')
        );
    }
}