<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Vividads_Adminsearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search Catalog Model
 *
 * @category    Mage
 * @package     Vividads_Adminsearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Vividads_Adminsearch_Model_Catalog extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Vividads_Adminsearch_Model_Search_Catalog
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $collection = Mage::helper('catalogsearch')->getQuery()->getSearchCollection()
            ->addAttributeToSelect('name')
			->addAttributeToSelect('*')
            ->addAttributeToSelect('description')
            ->addSearchFilter($this->getQuery())
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        
		
		
		 
		foreach ($collection as $product) {
			
			
			
            $description = strip_tags($product->getDescription());
			
            $arr[] = array(
                'id'            => 'product/1/'.$product->getId(),
                'type'          => Mage::helper('adminhtml')->__('Product'),
                'name'          => $product->getName(),
                'description'   => Mage::helper('core/string')->substr($description, 0, 30),
                'url' => Mage::helper('adminhtml')->getUrl('*/catalog_product/edit',
				                      array('id'=>$product->getId())),
				'sku'			=>$product->getSku(),
				'url_path'  => $product->getUrl_path(),
				'price'     => $product->getPrice(),
            );
        }
		 

       
	   
	    $this->setResults($arr);

        return $this;
    }
}
