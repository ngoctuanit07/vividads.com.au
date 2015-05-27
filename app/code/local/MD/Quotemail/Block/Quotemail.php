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
class MD_Quotemail_Block_Quotemail extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getQuotemail()     
     { 
        if (!$this->hasData('quotemail')) {
            $this->setData('quotemail', Mage::registry('quotemail'));
        }
		$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
		$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
		$pager->setCollection($this->getCollection());
		$this->setChild('pager', $pager);
        return $this->getData('quotemail');
        
    }
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
}