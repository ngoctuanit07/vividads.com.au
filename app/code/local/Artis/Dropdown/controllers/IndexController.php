<?php
class Artis_Dropdown_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/designer?id=15 
    	 *  or
    	 * http://site.com/designer/id/15 	
    	 */
    	/* 
		$designer_id = $this->getRequest()->getParam('id');

  		if($designer_id != null && $designer_id != '')	{
			$designer = Mage::getModel('designer/designer')->load($designer_id)->getData();
		} else {
			$designer = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
		 
		/*
		 if($designer == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$designerTable = $resource->getTableName('designer');
			
			$select = $read->select()
			   ->from($designerTable,array('designer_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$designer = $read->fetchRow($select);
		}
		Mage::register('designer', $designer);
		 
		*/ 
    			
		// $this->loadLayout();     
		 //$this->renderLayout();
    }
	
	public function loadSubcategoriesAction(){
			
			/*fetching post variables from ajax*/
			$_parent_cat_id = $this->getRequest()->getPost('parent_cat_id');
			$_category_id = $this->getRequest()->getPost('category_id');
			$_menu_level = $this->getRequest()->getPost('menu_level');
			
			/*loading sub categories or products*/
			$_sub_categories = Mage::getModel('catalog/category')->load($_category_id)
											->getChildrenCategories();
											 ;
			
			//echo count($_sub_categories);
			
			if(count($_sub_categories) >0){
			
			$i=0;
			foreach($_sub_categories as $_cur_category){				
				$_cur_category = Mage::getModel('catalog/category')
									->load($_cur_category->getId())
										;
				$i++;
				/*showing sub categories*/				
				$_sub_cats = '<ul class="level0">';
				$_sub_cats='
					<li class="level1 nav-7-'.$i.' first parent" >
					<a href="'.$_cur_category->getUrl().'" onmouseover="settop(this);"><span>
					<img width="120px" height="120px" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/category/'.$_cur_category->getThumbnail().'" />
					<span>'.$_cur_category->getName().'</span>
					</span>
					</a>';
	$_products = Mage::getSingleton('catalog/category')->load($_cur_category->getId())
            			->getProductCollection()
           				->addAttributeToSelect('*')
						->setOrder('price', 'ASC');
						;
					
	 
	$_sub_cats.='				
	<ul    class="level1">  ';

	if(count($_products) > 0){		
	
	$_counter=1;
	
	foreach($_products as $_product){
		//print_r($_product->getData());
		//exit;
		
	$_sub_cats.='<li  class="level2 nav-7-1-'.$_counter.'  parent" style="width:200px; float:left; ">';
	$_sub_cats.='<span onmouseout="distroyImage('.$_product->getId().');" onmouseover="loadImage('.$_product->getId().');" class="last_cat">
			<a href="'.$_product->getUrl_path().'">'.$_product->getName().'</a></span>';
		$_sub_cats.='</li>';	
		
		$_counter++;
		} //end of foreach
		
	}
						
	$_sub_cats.='</ul>';



										
					$_sub_cats .=' </li> ';
					 $_sub_cats .= '</ul>';
					 
					 echo $_sub_cats;
				}
			
			}else{
				
				$_cur_category = Mage::getModel('catalog/category')
									->load($_category_id);
				
				$i=0;						
				$i++;
				/*showing sub categories*/				
				$_sub_cats = '<ul class="level0">';
				$_sub_cats='
					<li class="level1 nav-7-'.$i.' first parent">
					<a href="'.$_cur_category->getUrl().'" onmouseover="settop(this);"><span>
					<img width="120px" height="120px" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/category/'.$_cur_category->getThumbnail().'" />
					<span>'.$_cur_category->getName().'</span>
					</span>
					</a>';		
					
	$_products = Mage::getSingleton('catalog/category')->load($_category_id)
            			->getProductCollection()
           				->addAttributeToSelect('*');
					
	 
	$_sub_cats.='				
	<ul    class="level1">  ';

	if(count($_products) > 0){		
	
	$_counter = 1;
	foreach($_products as $_product){
		//print_r($_product->getData());
		//exit;
		
	$_sub_cats.='<li onmouseout="distroyImage('.$_product->getId().');"   onmouseover="loadImage('.$_product->getId().');" class="level2 nav-7-1-'.$_counter.'  parent">';
	$_sub_cats.='<span  class="last_cat">
			<a href="'.$_product->getUrl_path().'">'.$_product->getName().'</a></span>';
		$_sub_cats.='</li>';
		$_counter++;
			
		} //end of foreach
		
	}
						
	$_sub_cats.='</ul>';
	
							
	$_sub_cats .=' </li> ';
	$_sub_cats .= '</ul>';
	
	
	echo $_sub_cats;
				
				
			}//enf of if statement			
		
		echo '<div class="current_image" style="float:left; position:absolute; 
		left:65%; 
		top:200px; border:1px solid #ccc; box-shadow:2px 2px 2px #ccc; display:none;
		width:300px; height:200px;"><img src="media/loading.gif" style="padding-top:68px;" />  </div>';
		
		
		
		} //end of function loadSubcategories
		
	/*function loadProductImageAction
	  @ param: product_id from post
	  @ output: product Image;
	*/
	
	
	public function loadProductImageAction(){
		
		/*fetching post variables from ajax*/
		$_curr_product_id = $this->getRequest()->getPost('curr_product_id');
			
		$_current_product = Mage::getModel('catalog/product')
							->load($_curr_product_id);
		$_link = $_current_product->getUrl_path();
		
		$_imageUrl =  Mage::helper('catalog/image')->init($_current_product, 'small_image')->resize(295, 195);
		
		
		$_product_image ='<a href="'.$_link.'"><img src="'.$_imageUrl.'" /></a>';
		echo $_product_image;				
							
		
		}
	
		
   
}