<?php
/**
 * Manufacturers extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   FME
 * @package    Manufacturers
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Faqs_Block_ProductFaqs extends Mage_Catalog_Block_Product_Abstract
{

	const DISPLAY_FAQS = 'faqs/general/enabled';

	protected function _tohtml()
    {
		 if ($this->getFromXml()=='yes'&&!Mage::getStoreConfig(self::DISPLAY_FAQS))
            return parent::_toHtml();

        $this->setFormAction( Mage::getUrl('faqs/index/add') );
		$this->setLinksforProduct();
        $this->setTemplate("faqs/pfaqs.phtml");
		return parent::_toHtml();
    }
    
}