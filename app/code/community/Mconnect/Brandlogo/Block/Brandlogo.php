<?php
/**
 * M-Connect Solutions.
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */
?>
<?php
class Mconnect_Brandlogo_Block_Brandlogo extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBrandlogo()     
     { 
        if (!$this->hasData('brandlogo')) {
            $this->setData('brandlogo', Mage::registry('brandlogo'));
        }
		$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
		$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
		$pager->setCollection($this->getCollection());
		$this->setChild('pager', $pager);
        return $this->getData('brandlogo');
        
    }
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
}