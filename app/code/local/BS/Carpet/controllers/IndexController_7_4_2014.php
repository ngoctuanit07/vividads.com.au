<?php
class BS_Carpet_IndexController extends Mage_Core_Controller_Front_Action
{
    
    public function loadproductAction(){
	extract($_REQUEST);
    	$category = new Mage_Catalog_Model_Category();
	$category->load($catId);
	$collection = $category->getProductCollection()->addAttributeToSelect('*');
	$str ='<select class="textfield" id="product_sel" onchange="loadProductDetails(this.value)"><option value="">--Please select--</option>';
	
	foreach ($collection as $_product) { 
            $str .= '<option value="'.$catId.':'.$_product->getId().'">'.$_product->getName().'</option>';
	}
	$str .= '<select>';
	echo $str;
    }
    
    public function loadproductdetailsAction(){
	extract($_REQUEST);
	$prctArr = explode(":",$pId);
	$productid = $prctArr[1];
	$_Product = Mage::getModel('catalog/product')->load($productid);
	$price = '$'.number_format($_Product->getPrice(),2);
	
	$optArr = $_Product->getOptions();
	$str = '';
	$retStr = '';
	foreach($optArr as $optionKey => $optionVal) {
	    
	    if($optionVal->getIsDependent() == 0){ $style ='style ="display:block;"';} else{$style ='style ="display:none;"';}
		
    		$str .='<dl class="clearfix" id="opt__'.$optionKey.'" '.$style.'><dt><label><b>'.$optionVal->getTitle().'</b></label></dt><dd><select class="textfield" onchange="getchildren('.$optionKey.',this.value)"><option value="" price="0">-- Please Select --</option>';
		
		foreach($optionVal->getValues() as $valuesKey => $valuesVal){
		   
		    $str .='<option value="'.$valuesVal->getId().'">'.$valuesVal->getTitle().'</option>';
		}
		  
		$str .='</select></dd></dl>';
		
	   
	    
	}
	$retStr = $price."##".$str;
	
	echo $retStr;
	
    }
    
    public function getchildoptAction(){
	extract($_REQUEST);
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$tableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_type_value');
	
	$select = $connectionRead->select()->from($tableName, array('*'))->where("option_id = '".$optId."' AND option_type_id = '".$optvalId."'");
	$row = $connectionRead->fetchrow($select);
	$denidsArr=explode(",", $row['dependent_ids']);
	$subopt = '';
	if(count($denidsArr) >0){
	    foreach($denidsArr as $di){
		$select1 = $connectionRead->select()->from($tableName, array('*'))->where("in_group_id = '".$di."'");
		$row1 = $connectionRead->fetchrow($select1);
    		if (strpos($subopt,$row1['option_id']) === false) {
		    $subopt .="##".$row1['option_id'];
		}
	    }
	    
	}
	
	echo $subopt;
	
    }
    
    
    
    
    
    
}
?>