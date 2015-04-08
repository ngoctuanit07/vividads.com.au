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
	
	
	if($optArr){
	foreach($optArr as $optionKey => $optionVal) {
	    
	    if($optionVal->getIsDependent() == 0){ //$style ='style ="display:block;"';} else{$style ='style ="display:none;"';
		
    		$str .='<dl class="clearfix" id="opt__'.$optionKey.'" '.$style.'><dt><label><b>'.$optionVal->getTitle().'</b></label></dt><dd><select class="textfield" onchange="getchildren('.$optionKey.',this.value)"><option value="" price="0">-- Please Select --</option>';
		
		foreach($optionVal->getValues() as $valuesKey => $valuesVal){
		   
		    $str .='<option value="'.$valuesVal->getId().'">'.$valuesVal->getTitle().'</option>';
		}
		  
		$str .='</select></dd></dl>';
	    }
	   
	    
	}
	$retStr = $price."##".$str;
	
	echo $retStr;
	}else{
	    echo $retStr;
	}
	
    }
    
    public function getchildoptAction(){
	extract($_REQUEST);
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$tableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_type_value');
	$tableName1 = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_title');
	$tableName2 = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_type_title');
	$tableName3 = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_type_price');
	
	
	$select = $connectionRead->select()->from($tableName, array('*'))->where("option_id = '".$optId."' AND option_type_id = '".$optvalId."'");
	$row = $connectionRead->fetchrow($select);
	$denidsArr=explode(",", $row['dependent_ids']);
	$subopt = array();
	$suboptVal = array();
	$retStr = '';
	if($row['dependent_ids'] != ''){
	    foreach($denidsArr as $di){
		$select1 = $connectionRead->select()->from($tableName, array('*'))->where("in_group_id = '".$di."'");
		$row1 = $connectionRead->fetchrow($select1);
    		
		if (!in_array($row1['option_id'],$subopt)) {
		   $subopt[] = $row1['option_id']; 
		}
		
		foreach($subopt as $so){
		    if (in_array($so,$row1)) {
			$suboptVal[$so][] = $row1['option_type_id']; 
			
		    }
		    
		}
		
	    }
	    
	    foreach ($suboptVal as $k=>$v){
		$select2 = $connectionRead->select()->from($tableName1, array('*'))->where("option_id = '".$k."'");
		$row2 = $connectionRead->fetchrow($select2);
		//echo '<br>'; echo $row2['title'];
		
		$retStr .= '<dl id="opt__'.$k.'" class="clearfix"><dt><label><b>'.$row2['title'].'</b></label></dt><dd><select class="textfield"><option price="0" value="">-- Please Select --</option>';
		
		foreach($v as $val){
		    $select3 = $connectionRead->select()->from($tableName2, array('*'))->where("option_type_id = '".$val."'")->order('option_type_title_id');
		    $row3 = $connectionRead->fetchrow($select3);
		    
		    $select4 = $connectionRead->select()->from($tableName3, array('*'))->where("option_type_id = '".$val."'")->order('option_type_id');
		    $row4 = $connectionRead->fetchrow($select4);
		    
		    if($row4['price'] != ''){
			$price = number_format($row4['price'],2);
			$pStr = '+$'.$price;
			
		    }else{
			$pStr = '';
		    }
		    
		    
		    $retStr .= '<option value="'.$val.'">'.$row3['title'].' '.$pStr.'</option>';
		    
		}
		$retStr .= '</select></dd></dl>';
		
	    }
	    
	    echo $retStr;   
	    
	}else{
	   echo $retStr; 
	}
	
	//print_r( $suboptVal);
	//echo $retStr;
    }
    
    
    
    
    
    
}
?>