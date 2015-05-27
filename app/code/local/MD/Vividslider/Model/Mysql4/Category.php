<?php

/**
 * MD_Vividslider.
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
 * @package    Vividslider
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */


class MD_Vividslider_Model_Mysql4_VividsliderCategory extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vividslider_id refers to the key field in your database table.
        $this->_init('vividslider/vividslider_category', 'category_id');
    }
}