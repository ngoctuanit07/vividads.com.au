<?php

class Artis_Externalform_Model_Externalform extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('externalform/externalform');
    }
	
	/*load product options*/
	public function getProductOptions_old(&$product=null, $selected=null)

		{
		
		   
		  $options = array();
		   if($product->hasOptions)
							{
						$optionsArr = $product->getOptions();
						 
						 foreach ($optionsArr as  $optionKey => $optionVal)
							{
							$optStr .='<h3 class="optionHeadings" style="color:#888">'.$optionVal->getTitle().'</h3>';
							$optStr .= "<select class='sel_options' id='sel_options' style='display:block;clear:both;' name='options[".$optionVal->getId()."]'>";    
						  
						  foreach($optionVal->getValues() as $valuesKey => $valuesVal)
								{
									 $optStr .= '<option ';
									 if(@is_array($selected)){
										 
									  if($valuesVal->getId()==$selected[$optionVal->getId()]){
										  $optStr .=' selected=true';
										  }
									  }
									  $optStr .='  value="'.$valuesVal->getId().'">'.$valuesVal->getTitle().'</option>';
								}
							$optStr.= "</select>";
							}
								
								//echo($optStr ); 
							 }
		
		  if ($addOptions = $product->getOptionByCode('additional_options')) {
		
			//  $options = array_merge($options, unserialize($addOptions->getValue()));
		
		  }
		
		  return $optStr;
		
		} 
	
	/*load product options*/
	public function getProductOptions(&$product=null, $selected=null)

		{
			$options = array();
		   		if($product->hasOptions)
							{
						$optionsArr = $product->getOptions();
						 $optStr ='<dl class="item-options">
                  					<dt>Items Included</dt>';
						 foreach ($optionsArr as  $optionKey => $optionVal)
							{
							$optStr .='<dt>'.$optionVal->getTitle().'</dt>';
						  foreach($optionVal->getValues() as $valuesKey => $valuesVal)
								{									 
									 if(@is_array($selected)){										 
									  if($valuesVal->getId()==$selected[$optionVal->getId()]){
										  $optStr .= '<dd> ';
										  $optStr .= $valuesVal->getTitle();
										 $optStr.= "</dd";
										  }
									  }
									  
									}
									
								}
							$optStr .='</dl>';	
								//echo($optStr ); 
							 }
		
		  if ($addOptions = $product->getOptionByCode('additional_options')) {
		
			//  $options = array_merge($options, unserialize($addOptions->getValue()));
		
		  }
		
		  return $optStr;
		
		} 	
}