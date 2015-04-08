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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product type price model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class BS_Carpet_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param double $finalPrice
     * @return double
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        
        $_product = Mage::getModel('catalog/product')->load($product->getId());
        
        $widthTitle = strtolower(Mage::getStoreConfig('bs_carpet/general/width'));
        if($widthTitle == '') {$widthTitle = 'width';}
        $lengthTitle = strtolower(Mage::getStoreConfig('bs_carpet/general/length'));
        if($lengthTitle == '') {$lengthTitle = 'length';}
        
        
        
        if($_product->getIsCarpetProduct()==1){
        	$multi = 1;
        	$width = 0;
        	$length = 0;
        	$optionPrice = 0;
        	if ($optionIds = $product->getCustomOption('option_ids')) {
        		$basePrice = $finalPrice;
        		foreach (explode(',', $optionIds->getValue()) as $optionId) {
        			if ($option = $product->getOptionById($optionId)) {
        				$confItemOption = $product->getCustomOption('option_'.$option->getId());
        				
        				$group = $option->groupFactory($option->getType())
        				->setOption($option)
        				->setConfigurationItemOption($confItemOption);
        				 
        				if($option->getType() == 'field' && strtolower($option->getTitle())==$lengthTitle){
        	
        					$length = (float)$confItemOption->getValue();
        	
        				}else if(strtolower($option->getTitle())==$widthTitle){

        					if($option->getType() == 'drop_down'){
        						$width = (float)$group->getFormattedOptionValue($confItemOption->getValue());
        					}else {
        						$width = (float)$confItemOption->getValue();
        					}
        				}
        					
        				$optionPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
        					
        			}
        		}
        		
        		$dimenion = $width * $length;
        		
        		$tierPrice = $this->getTierPrice($dimenion, $product);
        		
        		if(is_array($tierPrice)){
        			$tierPrice = $finalPrice;
        		}
        		
        		if($tierPrice > $finalPrice){
        			$tierPrice = $finalPrice;
        		}
        		
        		$totalPrice = $dimenion * ($tierPrice + $optionPrice);
        	
        		return $totalPrice;
        	}
        	
        }
        
        return parent::_applyOptionsPrice($product, $qty, $finalPrice);
        
    }

}
