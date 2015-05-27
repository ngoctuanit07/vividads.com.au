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


class MD_Quotemail_Model_Mysql4_QuotemailCategory extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the quotemail_id refers to the key field in your database table.
        $this->_init('quotemail/quotemail_category', 'category_id');
    }
}