<?php

class BS_Carpet_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    
	public function getBoSuaCarpetJsonConfig()
    {
    	
    	$widthTitle = strtolower(Mage::getStoreConfig('bs_carpet/general/width'));
    	if($widthTitle == '') {$widthTitle = 'width';}
    	$lengthTitle = strtolower(Mage::getStoreConfig('bs_carpet/general/length'));
    	if($lengthTitle == '') {$lengthTitle = 'length';}
    	
        $config = array();
		
		$config['widths'] = array();
		$config['options'] = array();
        foreach ($this->getProduct()->getOptions() as $option) {
            /* @var $option Mage_Catalog_Model_Product_Option */
        	
        	
        	
        	if(strtolower($option->getTitle()) == $widthTitle){
        		$config['widthid'] = $option->getId();
        		$config['width_type'] = 'field';
        		if($option->getType() == 'drop_down'){
        			$config['width_type'] = 'select';
        			foreach ($option->getValues() as $value){
        				$config['widths'][] .= (float)$value->getTitle();
        			}
        		}
        		
        	}elseif ($option->getType()=='field' && strtolower($option->getTitle()) == $lengthTitle) {
        		$config['lengthid'] = $option->getId();
        	}else {
	        	$priceValue = 0;
	        	
	            if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
	                $_tmpPriceValues = array();
	                foreach ($option->getValues() as $value) {
	                    /* @var $value Mage_Catalog_Model_Product_Option_Value */
	                    $id = $value->getId();
	                    $_tmpPriceValues[$id] = $this->_getPriceConfiguration($value);
	                }
	                $priceValue = $_tmpPriceValues;
	            } else {
	                $priceValue = $this->_getPriceConfiguration($option);
	            }
	            $config['options'][] = array('id'=>$option->getId(), 'values'=>$priceValue, 'type'=>$option->getType()) ;
	        	}
            
        }
        $config['maxlength'] = $this->getProduct()->getMaxCarpetLength();
		$config['id'] = $this->getProduct()->getId();
		$config['iscarpet'] = $this->getProduct()->getIsCarpetProduct();
		$config['price'] = $this->getProduct()->getFinalPrice();		
		$config['prices'] = $this->getTierPrices($this->getProduct());
        return Mage::helper('core')->jsonEncode($config);
    	}
    
    protected function _getPriceConfiguration($option)
    {
    	$data = array();
    	$data['price']      = Mage::helper('core')->currency($option->getPrice(true), false, false);
    	$data['oldPrice']   = Mage::helper('core')->currency($option->getPrice(false), false, false);
    	$data['priceValue'] = $option->getPrice(false);
    	$data['type']       = $option->getPriceType();
    	$data['excludeTax'] = $price = Mage::helper('tax')->getPrice($option->getProduct(), $data['price'], false);
    	$data['includeTax'] = $price = Mage::helper('tax')->getPrice($option->getProduct(), $data['price'], true);
    	return $data;
    }
  
}
