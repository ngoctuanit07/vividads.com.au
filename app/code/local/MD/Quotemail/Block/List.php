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
?>
<?php

class MD_Quotemail_Block_List extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getQuotemail() {
        if (!$this->hasData('quotemail')) {
            $this->setData('quotemail', Mage::registry('quotemail'));
        }
        
		
		$_logo_collection = $this->getData('quotemail');
		
		
		return $_logo_collection;
    }

}