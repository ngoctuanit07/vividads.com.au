<?php
class Artis_Externalform_Block_Externalform extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		 return parent::_prepareLayout();
    }
    
     public function getExternalform()     
     { 
        if (!$this->hasData('externalform')) {
            $this->setData('externalform', Mage::registry('externalform'));
        }
        return $this->getData('externalform');
        
    }
	
	
	/*load product options*/
	public function getProductOptionsold(&$product=null, $selected=null)

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
	public function getProductOptions(&$product=null, $selected=null, $qty=null)

		{
			$options = array();
		   		
				$optStr ='<dl class="item-options item-other-options">
                  					<dt>Items Included</dt>';
				//product bundle options
				
			if($product->getTypeId()=='bundle'){
				$collection = $product->getTypeInstance(true)
   									 ->getSelectionsCollection(
        												$product->getTypeInstance(true)
               			 									    ->getOptionsIds($product), $product);

				foreach ($collection as $item) {
					# $item->product_id has the product id.
					 $optStr .='<dd> '.$qty.' x '.$item->getName().'</dd>';
					}
				}else{
					 $optStr .='<dd> '.$qty.' x '.$product->getName().'</dd>';
					}
				
				if($product->hasOptions)
							{
						$optionsArr = $product->getOptions();			
					///products options				
						 foreach ($optionsArr as  $optionKey => $optionVal)
							{
							$optStr .='<dt>'.$optionVal->getTitle().'</dt>';					  
						  
						  foreach($optionVal->getValues() as $valuesKey => $valuesVal)
								{									 
									  if(@is_array($selected)){										 
									  if($valuesVal->getId()==$selected[$optionVal->getId()]){
										  $optStr .= '<dd>'.$valuesVal->getTitle().'</dd>';
										   }
									   }
									  
									}
							 		
								}
								
								//echo($optStr ); 
							 }
		
		  if ($addOptions = $product->getOptionByCode('additional_options')) {
		
			//  $options = array_merge($options, unserialize($addOptions->getValue()));
		
		  }
		  $optStr .='</dl>';
		
		  return $optStr;
		
		} 
	
}