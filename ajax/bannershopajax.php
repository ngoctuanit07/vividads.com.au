<?php   
// Now login on MAGENTO
/* filname: bannershopajax.php
   all functions written  is accessible...
   
*/
include('../app/Mage.php');
Mage::app();


 $crAction = isset($_REQUEST['crAction'])? $_REQUEST['crAction']:'';
 $content = $_REQUEST['content'];

  if($crAction){
	  
	  $pageContent='';
	  
	  if($content==1){
	  $content = 'why-choose-us';
	  
	  $pageContent = getCmsPage($content);
	 // echo $pageContent['content'];
	  }
	  
	  if($content==2){
	  $content = 'free-shipping';
	  
	  $pageContent = getCmsPage($content);
	  echo $pageContent['content'];
	  }
	  
	  
	  if($content==3){
	  $content = 'top-sellers-vinyl-banners';
	  
	  $pageContent = getCmsPage($content);
	   echo $pageContent['content'];
	   
	  
	  }
	
	///if crAction is fetch categoires /////
	
	if($crAction =='products'){
		
		$categoryId = $_REQUEST['category_id'];		
		
		$loaded_products = getProductSelectBox('product','product',$categoryId,'class="textfield" onchange="javascript:loadCustomOptions(this.value),sidebanner(),setshippinginfo(),getPrice(),checkNumeric(),qty_disc(),loadqtycombo()"');
		
		echo $loaded_products;
		}
		
	///if crAction is fetch categoires /////
	
	if($crAction =='products2'){
		
		$categoryId = $_REQUEST['category_id'];		
		
		$loaded_products = getProductSelectBox('product2','product2',$categoryId,'class="textfield" style="width:215px;" onchange=""');
		
		echo 'Product:'.$loaded_products;
		}	
	
	///if crAction is fetch categoires /////
	
	if($crAction =='productPrice'){
		
		$_productId = $_REQUEST['product_id'];	
		$_categoryId = $_REQUEST['category_id'];	
		
		$_price = getPrice($_categoryId, $_productId);	
		
		echo $_price;
		}	
	
	///if crAction is fetch discounts /////
	
	if($crAction =='discountPrice'){
		
		$_productId = $_REQUEST['product_id'];	
		$_categoryId = $_REQUEST['category_id'];
		$_area  = $_REQUEST['area'];	
		
		$_discount = getDiscount($_categoryId, $_productId, $_area);	
		
		echo $_discount;
		}
		
	
	///if crAction is fetch discounts /////
	
	if($crAction =='accessories'){
		
		$_productId = $_REQUEST['product_id'];	
		$_categoryId = $_REQUEST['category_id'];
		$_accessories  = $_REQUEST['accessory_id'];	
		
		$_accessories = getAccessories($_categoryId, $_productId, $_accessories);	
		
		echo $_accessories;
		}	
		
	///if crAction is fetch customoptions /////
	
	if($crAction =='customOptions'){
		
		$productId = $_REQUEST['product_id'];		
		$categoryId = $_REQUEST['category_id'];		
		
	$loaded_box = getFinishSelectBox('finish_sel','finish_sel',$productId,'class="textfield" onchange="javascript:loadCustomFinishOptions(this.value)"');
		
	if($categoryId=='1477'){			
		$loaded_box = getBaseSelectBox('base_sel','base_sel',$productId,'class="textfield" onchange="javascript:loadCustomBaseOptions(this.value)"');
		
		}
		
		if($loaded_box !=false){
			
			
			echo $loaded_box;			
			
			
			}else{
				echo 0;
			}
		}	
		
	  
	  }

    


 
 /*getCMSPage 
 @ param: identifier/key
 @ output: pageof cms
 */
 
 function getCmsPage($identifier=''){
	 
	 if($identifier==''){return 'Error';}
	 
	 $collection = Mage::getModel('cms/page')->getCollection()->addStoreFilter(Mage::app()->getStore()->getId()); 
		$collection->getSelect()->where('is_active = 1'); 
		// use the filter in the case you want to get only enabled CMS pages. 
		/*
		foreach ($collection as $page){     
		$theIdentifier = $page->getIdentifier();     
		$theTitle = $page->getTitle();     
		$theContent = $page->getContent();    
		echo $theTitle; 
		 } 
		 */
	 
		$aCmsPage = Mage::getModel('cms/page')->load($identifier, 'identifier'); 
		$theTitle = $aCmsPage->getTitle(); 
		$theContent = $aCmsPage->getContent(); 
		$pageContents['title']=$theTitle;
		$pageContents['content']=$theContent;
		
		return $pageContents;
	 }
 

 /*function getProductSelectBox
  @param: productId
  @output: selected product from category
 
 */
 
 function getProductSelectBox($id='',$name='', $categoryId=0, $params=''){
	 
	 if($categoryId==0){return fasle;}
	 
	  $output='<select name="'.$name.'" id="'.$id.'" '.$params.'  > ';
		$catName='--Please select--';
	
		//loading categories///
		
		$store_id = Mage::app()->getStore()->getStoreId();
		$store_id = 20;
		// set Cat ID
		$cat_id = $categoryId;
		
//		echo $store_id;

		$category = Mage::getModel('catalog/category')->load($categoryId);
		$sub_categories = 	$category->getChildrenCategories();
		
	//	echo count($sub_categories); 
		
		//foreach($sub_categories as $_cat){
		
		$catagory_model = Mage::getModel('catalog/category')->load($categoryId); //where $category_id is the id of the category
	 
	     $collection = Mage::getResourceModel('catalog/product_collection');	 
	     $collection->addCategoryFilter($catagory_model); //category filter	 
	    //$collection->addAttributeToFilter('status',1); //only enabled product	 
	     $collection->addAttributeToSelect(array('name','url','small_image')); //add product attribute to be fetched
	     //$collection->getSelect()->order('rand()'); //uncomment to get products in random order     
	 
	     $collection->addStoreFilter($store_id);						
		
		$output.='<option value="0">'.$catName.'</option>';	
		
		
		
		foreach($collection as $_product){
			
			if(count($collection) > 0){
			$output.='<option value="'.$categoryId.':'.$_product->getId().'">'.$_product->getName().'</option>';
		     }
		}
		///categories from the sub categories
		foreach($sub_categories as $_sub_category){
			    
				$catagory_model = Mage::getModel('catalog/category')->load($_sub_category->getId()); //where $category_id is the id of the category
	 
	     $collection = Mage::getResourceModel('catalog/product_collection');	 
	     $collection->addCategoryFilter($catagory_model); //category filter	 
	    //$collection->addAttributeToFilter('status',1); //only enabled product	 
	     $collection->addAttributeToSelect(array('name','url','small_image')); //add product attribute to be fetched
	     //$collection->getSelect()->order('rand()'); //uncomment to get products in random order     
	 
	     $collection->addStoreFilter($store_id);
						
				foreach($collection as $_product){
				
				if(count($collection) > 0){
				$output.='<option value="'.$_sub_category->getId().':'.$_product->getId().'">'.$_product->getName().'</option>';
		     	}
				
				}
			
			}
		
		
		
		 $output.='</select>';
		 
	 /*display output*/
	 return $output;
	 
	 
	 }
 
 /*
 	function get
 */
 
 function getFinishSelectBox($id='',$name='', $productId=0, $params=''){
	 
	 if($productId==0){return fasle;}
	 
	 $_product = Mage::getModel('catalog/product')->load($productId);
	 //echo $_product->getId();
	 $_options=$_product->getOptions();
	
	//if product have options then it will show either it will return 0	
	if(count($_options) > 0){
	 
	  $output='<select name="'.$name.'" id="'.$id.'" '.$params.'  > ';		
	  /**/	
	$output.='<option value="">--Please select--</option>';
	/*
	$_finish_vals=array(  		'HE'=>'Hemmed Edges',
							'1PT'=>'1 x Pole Pocket(Top Only)',
							'2PLR'=>'2 x Pole Pockets(Left & Right)',
							'2PTB'=>'2 x Pole Pockets(Top & Bottom)',
							);
	*/						
	foreach($_options as $_option){
					if($_option->getTitle() =='FinishOptions'){
						
						//var_dump($_option->getValues());
						foreach($_option->getValues() as $_optVals){
						$output.='<option value="'.$_optVals->getSku().'">'.$_optVals->getTitle().'</option>';	
						}
					}					
				}	
		 $output.='</select>';
	
	/*display output*/
	 return $output;
	}else{		
		return false;
	 }
}

/*function getPrice*/

function getPrice($categoryId, $productId){
	
	$_product = Mage::getModel('catalog/product')->load($productId);	
	 return $_product->getPrice();	
	}

	 
/*function getAccessories*/

function getAccessories($categoryId, $productId, $accessory_id){
	
	$_product = Mage::getModel('catalog/product')->load($productId);	
	/*load custom options*/ 
	$_options=$_product->getOptions();
	$_accessory_value = 0;
	//var_dump($_options);
	if(count($_options) > 0){
		
		foreach($_options as $_option){
			if($_option->getTitle() =='FinishOptions'){
				foreach($_option->getValues() as $_optVals){
					if($accessory_id == $_optVals->getSku()){						
						$_accessory_value = $_optVals->getPrice();
					}
				}
			   }
			}
		}
	 
	 return $_accessory_value;	
	}

/*function getDiscount*/

function getDiscount($categoryId, $productId, $area){
	
	$_product = Mage::getModel('catalog/product')->load($productId);	
	
	$_tierPrice = $_product->getTierPrice();
	$_price = $_product->getPrice();
	
	$_realPrice = $_price * $area;
	$_discount_price = 0;
	$_discount=0;
	
 if(count($_tierPrice)>0){
	
	foreach($_tierPrice as $_tPrice){
		
		$_discount_price = $_tPrice['website_price']; 
		$_qty = $_tPrice['price_qty']; 
		$_cArea = $area;		
		
	//if($_cArea < $_qty){ $_discount = 0; }
	// $_webPrice = $_tPrice['website_price'];
	
	if($_cArea > $_qty){
		   
		   $_cArea = $area;
		   $_webPrice = $_tPrice['website_price']; 		    	    	
		}	
	}
	     $_discounted_price = $_webPrice * $area;
		if($_discounted_price > 0){
		$_discount = $_realPrice - $_discounted_price;
		}
		//echo $_realPrice.'<br/>';
		//echo $_webPrice.'<br/>';
		
  }
	 return $_discount;	
	}


	 
function getBaseSelectBox($id='',$name='', $productId=0, $params=''){
	 
	 if($productId==0){return fasle;}
	 $_product = Mage::getModel('catalog/product')->load($productId);
	 //echo $_product->getId();
	 $_options=$_product->getOptions();
	
	//if product have options then it will show either it will return 0	
	if(count($_options) > 0){
	 
	  $output='<select name="'.$name.'" id="'.$id.'" '.$params.'  > ';		
	  /**/	
	$output.='<option value="">--Please select--</option>';
					
	foreach($_options as $_option){
					if($_option->getTitle() =='BaseOptions'){
						
						//var_dump($_option->getValues());
						foreach($_option->getValues() as $_optVals){
						$output.='<option value="'.$_optVals->getSku().'">'.$_optVals->getTitle().'</option>';	
						}
					}					
				}	
		 $output.='</select>';
	
	/*display output*/
	 return $output;
	}else{		
		return false;
	 }
}



?>

