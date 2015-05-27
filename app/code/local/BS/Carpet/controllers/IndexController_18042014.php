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
    
    public function loadproducthomeAction(){
	extract($_REQUEST);
    	$category = new Mage_Catalog_Model_Category();
	$category->load($catId);
	$collection = $category->getProductCollection()->addAttributeToSelect('*');
	$str ='Product: <select class="textfield" id="threeway_product" style="width:215px; margin-left:8px;"><option value="">--Please select--</option>';
	
	foreach ($collection as $_product) { 
            $str .= '<option value="'.$catId.':'.$_product->getId().'">'.$_product->getName().'</option>';
	}
	$str .= '<select>';
	echo $str;
    }
    
    public function loadproductdetailsAction(){
  //echo 'zzzz'; die;
	extract($_REQUEST);
	$prctArr = explode(":",$pId);
	$productid = $prctArr[1];
	//echo "prod id::".$productid; die;
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
		
    public function loadproductdetailspopAction(){
  //echo 'zzzz'; die;
	//extract($_REQUEST);
	//$prctArr = explode(":",$pId);
	$productid =$_REQUEST['pId'];
	//echo "prod id::".$productid; die;
	$_Product = Mage::getModel('catalog/product')->load($productid);
	$price = '$'.number_format($_Product->getPrice(),2);
	
	$optArr = $_Product->getOptions();
	$str = '';
	$retStr = '';
	
	
	if($optArr){
	foreach($optArr as $optionKey => $optionVal) {
	    
	    if($optionVal->getIsDependent() == 0){ //$style ='style ="display:block;"';} else{$style ='style ="display:none;"';
		
    		$str .='<dl class="clearfix" id="opt__'.$optionKey.'" '.$style.'><dt><label><b>'.$optionVal->getTitle().'</b></label></dt><dd><select class="textfield" id="select_'.$optionKey.'" onchange="getchildrenpop('.$optionKey.',this.value)"><option value="" price="0">-- Please Select --</option>';
		
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
	
    }
    //9-4-2014
		
		
public function getchildoptpopAction(){
	//extract($_REQUEST);
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$tableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_type_value');
	$tableName1 = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_title');
	$tableName2 = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_type_title');
	$tableName3 = Mage::getSingleton('core/resource')->getTableName('catalog_product_option_type_price');
	//echo $optId."***".$optvalId; die;
	$optId=$_REQUEST['optId'];
	$optvalId=$_REQUEST['optvalId'];
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
		
		$retStr .= '<dl id="opt__'.$k.'" class="clearfix"><dt><label><b>'.$row2['title'].'</b></label></dt><dd><select class="textfield" id="select_'.$k.'"><option price="0" value="">-- Please Select --</option>';
		
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
	
    }
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
    public function getartworkAction(){
	extract($_REQUEST);
	//print_r($_REQUEST);
	$_Product = Mage::getModel('catalog/product')->load($prodId);
	$category = Mage::getModel('catalog/category')->load($catId);
	$img = Mage::helper('catalog/image')->init($_Product, 'image')->resize(250);
	
        
    
	$str='<div class="clearfix"></div>
		<div class="float-L clearfix" id="innerLeft-tabing">
		  <div id="uploadYourArtworkData" class="innerLeft-tabing">
		  <div class="title">Submit Your Artwork Details </div>
		  
		  <div id="subret" style="display:none"></div>
		  <div style="overflow: auto; width: 625px;" id="printreadytab1" class="detailtab">
			<dl class="clearfix">
			  <dt>Project Name</dt>
			  <dd>
			    <input type="text" value="" maxlength="70" class="textfield" id="projectname" name="projectname">
			  </dd>
			</dl>
			<dl class="clearfix">
			  <dt>Category</dt>
			  <dd>
			  <input type="text" value="'.$category->getName().'" maxlength="70" class="textfield products" id="categoryName" name="categoryName" readonly="readonly">
			</dd>
			</dl>
			<dl class="clearfix">
			  <dt>Product Name</dt>
			  <dd>
				
			    <input type="text" value="'.$_Product->getName().'" maxlength="70" class="textfield products" id="productName" name="productName" readonly="readonly">
			  </dd>
			</dl>
			
		
			';
			
			
			$str2='<dl class="clearfix">
			    <dt>Price</dt>
			    
			    <dd id="total_display_pr">
				<div class="price-box">
				<span itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" id="product-price-610" class="regular-price">
				<span itemprop="price" id="price-to-change" class="price">AU$'.number_format($_Product->getprice(),2).'</span></span>
				<input type="text" id="price_get" value='.$_Product->getprice().' />
				<input type="text" id="opt_price" value="0" />
				<input type="text" id="product_id" value='.$_Product->getId().' />
				</div>
			    </dd>
		        </dl>
			<dl class="float-L">
			    <dd class="text float-L">Include any notes or requests to our production team before they process your file...Type here...</dd>
			    <dd class="float-L textarea">
			    <textarea class="textfield w587" id="description_pr" name="description_pr" onkeyup="changLimit(this.value)" maxlength="255"></textarea>
			    <div class="shadowSprite shadow590"></div>
			    <dd class="text float-L">
			    <div class="allowedWords">Allowed Words (<span id="tWords">255</span>)</div>
			    </dd>
			    </dd>
			    
			</dl>
			<dl class="float-L upload">
			    <dd class="text float-L">Select Upload Options</dd>
			    <dd class="text">
			    <div class="upload-module clear">
			    <div class="Options1">
			    <form action="" method="post" id="form-validate" enctype="multipart/form-data">
			    <input type="file" name="artworkfile" id="artworkfile" /> <span class="sizelimit">Upto 2GB</size>
			    <input type="hidden" name="optarr" id="optarr" value="" />
			    <input type="hidden" name="cusprice" id="cusprice" value="" />
			    <input type="hidden" name="cusnote" id="cusnote" value="" />
			    <input type="hidden" name="prodId" id="prodId" value="'.$_Product->getId().'" />
					<input type="hidden" id="width" />
				  <input type="hidden" id="length" />
				  <input type="hidden" name="dimension" id="dimension"/> 
			    <input type="hidden" name="form_key" value="'.Mage::getSingleton('core/session')->getFormKey().'" />
			    
			    </form>
			    
			    </div>
			    
			    
			    
			    </div>
			    </dd>
			    <div class="shadowSprite shadow590"></div>
			</dl>
			<dl class="clearfix float-R">
			    
			    <input type="button" onclick="addToCartGc('.$_Product->getId().');" name="addCartBtn" id="addCartBtn" value="Add to Cart" class="cart-button add-to-cart">
			</dl>
			<dl style="height:20px;">
			</dl>
	
		  </div>
		</div>
		
    	</div>';
	
	$str3 = '<div class="float-R clearfix" id="innerRight-specifications">
		<div style="width:270px; height:220px; border:0px solid #ccc;margin-top:15px;">
		<p class="product-image">
				      
	        <img title="Custom Mesh Banner" alt="Custom Mesh Banner" src="'.$img.'" id="mainImage">
				       
		      
		  </p>
		</div>
		<div style="margin-top:30px;">
		<div class="mrg-B10">
		  <dl class="clearfix">
		    <dt>File Type</dt>
		    <dd> JPEG, PDF, DOC, JPG, DOCX,
		      EPS, CDR, AI, GIF, PSD, TIF,
		      TIFF, PPT, PNG, BMP </dd>
		  </dl>
		  <dl class="clearfix">
		    <dt>Resolution</dt>
		    <dd>150 DPI to 600 DPI</dd>
		  </dl>
		  <dl class="clearfix">
		    <dt>Color Mode</dt>
		    <dd>CMYK</dd>
		  </dl>
		  <dl class="clearfix">
		    <dt>Text</dt>
		    <dd>Convert to Outlines</dd>
		  </dl>
		  <dl class="clearfix">
		    <dt>Layers</dt>
		    <dd>Flatten to a Background Layer</dd>
		  </dl>
		  <dl class="clearfix">
		    <dt>Compression</dt>
		    <dd>Zip files if necessary</dd>
		  </dl>
		</div>
		
		<div class="clear">
		  <p>&nbsp;</p>
		</div>
		<div class="supportModule float-R">
		  <div class="supportIcon"></div>
		  <div class="smalltext">Need Help or questions?</div>
		  <div class="box grey-gredient-bg">
		    <div class="float-L">
		      <div class="calltext">(03) 8400-4345</div>
		      <a href="mailto:Array" class="emailink" title="send us email" rel="nofollow">send us email</a>&nbsp; » </div>
		    <div class="float-L livesupport"> 
		    <a rel="nofollow" href="#" >
		    <img align="bottom" alt="Live Support" title="Live Support" src="https://image.providesupport.com/image/bannerbuzz/current"></a> </div>
		  </div>
		  
		</div>
	      </div>';
	
	echo $str."##".$str2.'##'.$str3;
    }
    
   
   public function addcartartworkAction(){
	extract($_REQUEST);
	//echo '<pre>';print_r($_REQUEST);echo '</pre>';
	//die;
	$_product = Mage::getModel('catalog/product')->load($prodId);
  $_helper = Mage::helper('catalog/output');
	$Arr = explode("**",$optarr);
	$selArr = explode("##",$Arr[0]);
	$optArr = explode("##",$Arr[1]);
	array_pop($optArr);
	array_pop($selArr);
  $sel = array();
	foreach($selArr as $key=>$val){
	    $sel[$val]=$optArr[$key];
	    
	}
	
	$string=trim($_FILES['artworkfile']['name']);
	$size=$_FILES['artworkfile']['size'];
	$arr= explode('.', $string);
	$ext=$arr[count($arr)-1];
	//echo $ext;
	$extArr = array("jpeg", "pdf", "doc", "jpg", "docx", "eps", "cdr", "ai", "gif", "psd", "tif", "tiff", "ppt", "png", "bmp");
	
	if(in_array($ext,$extArr) && $_FILES["artworkfile"]["size"] < 2097152){
	    if(isset($_FILES['artworkfile']['name'])){
		
		$time = time();
		$path = Mage::getBaseDir('media') . DS ."artworkfiles" . DS;
		
		$fileExt = strtolower(substr(strrchr($_FILES['artworkfile']['name'], "."), 1));
                $filename = "artwork_".$prodId."_".time().".".$fileExt;
		$uploader = new Varien_File_Uploader('artworkfile');
		$uploader->setAllowedExtensions($extArr);
		$uploader->setAllowCreateFolders(true);
		$uploader->setAllowRenameFiles(false);
		$uploader->setFilesDispersion(false);
    		$uploader->save($path,$filename);
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."artworkfiles/".$filename;
		$dimension=$_REQUEST['dimension'];
		//echo $dimension; die;
		
		$params = array(
		'product' => $proId,
		'qty' => 1,
		'price' => $cusprice,
		'artworkfile' => $url,
		'customernotes' => $cusnote,
		'dimension' => $dimension,
		'options' => $sel
		);
		
		$cart = Mage::getModel('checkout/cart');
		Mage::getSingleton('core/session', array('name'=>'frontend'));
		
		$cart->addProduct($_product, $params);
		$cart->save();
		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
		echo "Product ".$_product->getName()." has been added to your cart";
	    }else{
		echo "No file name found";   
	    }
		    
	}else{
	    echo "File type is not allowed or file size is too large";
	}
	
	
	
   }
	 
		public function gettireunitpriceAction(){
		$qty=$_REQUEST['qty'];
		$old_price=$_REQUEST['old_price'];
		$opt_price=$_REQUEST['opt_price'];
		$_product=Mage::getModel('catalog/product')->load($_REQUEST['pId']);
		$price_calculate=($_product->getTierPrice($qty) + $opt_price) * $qty;
		echo $price_calculate;
	  }

    
    
}



?>