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


class MD_Vividslider_Model_BrandlogoCategory extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vividslider/vividslider_category');
    }
	
	public function getCollection() {
        return Mage::getResourceModel('vividslider/category_collection');
    }
}