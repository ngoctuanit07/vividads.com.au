<?php

class Artis_Externalform_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/externalform?id=15 
    	 *  or
    	 * http://site.com/externalform/id/15 	
    	 */
    	/* 
		$externalform_id = $this->getRequest()->getParam('id');

  		if($externalform_id != null && $externalform_id != '')	{
			$externalform = Mage::getModel('externalform/externalform')->load($externalform_id)->getData();
		} else {
			$externalform = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($externalform == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$externalformTable = $resource->getTableName('externalform');
			
			$select = $read->select()
			   ->from($externalformTable,array('externalform_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$externalform = $read->fetchRow($select);
		}
		Mage::register('externalform', $externalform);
		*/

			
		$this->loadLayout();     
		 $this->renderLayout();
    }
    
    public function getProductAction()
    {
    	$store= Mage::app()->getStore($this->getRequest()->getParam('storeid'));

	Mage::app($store->getCode());
	$catid=$this->getRequest()->getParam('catid');

	$category = Mage::getModel('catalog/category')->setStore($this->getRequest()->getParam('storeid'))->setStoreId($this->getRequest()->getParam('storeid'))->load($catid);
	$products = $category->getProductCollection()->addCategoryFilter($category)
		->addAttributeToFilter('status', 1)
		->addAttributeToFilter('visibility', 4);
		
	$output="";
	
	$output.='<span class="lab">
	    Products:
	</span>
	<span class="inp">
	<label class="field select">
	    <select name="products[]" class="prod" onchange="getProdImage(this.value,this);">
	    
		<option value="">Select a product</option>';

	    foreach($products as $_product){


		 
		  
	     $_product1 = Mage::getModel('catalog/product')->setStore($this->getRequest()->getParam('storeid'))->setStoreId($this->getRequest()->getParam('storeid'))->load($_product->getId());

	    	/*if($_product->getTypeID()=="bundle")
		{
			$arr_bundle_items=array();
			$arr_bundle_items=$_product->getTypeInstance(true)->getChildrenIds($_product->getId(), false);	
			
			$arr_bundle_items=$arr_bundle_items[0];
			
			foreach($arr_bundle_items as $key=>$val)
			{
				$_product1 = Mage::getModel('catalog/product')->setStore($this->getRequest()->getParam('storeid'))->setStoreId($this->getRequest()->getParam('storeid'))->load($val);
			
			
                             if($_product1->getStatus() == 1)
			     {
				$tmp_val="";
				if(strlen($_product1->getName())>53)
				{
					$tmp_val=$_product1->getSku()." - ".substr($_product1->getName(),0,53)."....";
				}
				else
				{
					$tmp_val=$_product1->getSku()." - ".$_product1->getName();
				}
			   
				$output.='<option value="'.$_product1->getSku().'" title="'.$_product1->getSku()." - ".$_product1->getName().'">'.$tmp_val.'</option>';
			    
			     }
			}


		}
		else*/
		{    	


		     if($_product1->getStatus() == 1)
		     {
			$tmp_val="";
			if(strlen($_product1->getName())>53)
			{
				$tmp_val=$_product1->getSku()." - ".substr($_product1->getName(),0,53)."....";
			}
			else
			{
				$tmp_val=$_product1->getSku()." - ".$_product1->getName();
			}
		   
			$output.='<option value="'.$_product1->getSku().'" title="'.$_product1->getSku()." - ".$_product1->getName().'">'.$tmp_val.'</option>';
		    
		     }
		}
		      
		      
		      
	    }
	   
	    $output.='</select>
		<i class="arrow double"></i> </label> 
		
		 <img id="load_image" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/externalform/images/loading2.gif" style="width:30px; height:30px; display:none; float:left;" />
		
	</span>';
	
	echo $output;
    }
    
    
    public function getProductImageAction()
    {
    	
	
	$catid=$this->getRequest()->getParam('prodid');
	$prodid=$_REQUEST['prodid'];
//require_once 'Mage/Checkout/controllers/CartController.php';	
// require_once('app/Mage.php');    
//    umask(0);
//    Mage::app('admin');
//    
//    $product_model = Mage::getModel('catalog/product');	 
//$qty = '1'; // Replace qty with your qty
//$_product = Mage::getModel('catalog/product')->load($prodid);
//$cart = Mage::getModel('checkout/cart');
//$cart->init();
//$cart->addProduct($_product, array('qty' => $qty));
//$cart->save();
//Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
	//echo $prodid;
	if($prodid!="")
	{
		$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$prodid);
	
	
	
		$output="";
	
		$output.='<span class="im">Product Image:</span>';
/*		$output.='<a href="'.Mage::helper("catalog/image")->init($_product, 'image')->keepFrame(true)->setQuality(100)->resize(500).'" rel="lightbox[example]" title="'.$_product->getName().'"><img src="'.Mage::helper("catalog/image")->init($_product, 'image')->keepFrame(true)->setQuality(100)->resize(92,114).'"  /></a>';*/
		$output.='<a href="'.Mage::helper("catalog/image")->init($_product, 'image')->keepFrame(true)->setQuality(100)->resize(500).'" rel="lightbox[example]" title="'.$_product->getName().'"><img width="114" height= "92" src="'.Mage::getModel("catalog/product_media_config")->getMediaUrl($_product->getThumbnail()).'"  /></a>';
		
	}
	else
	{
		$output="";
	
		$output.='<span class="im">Product Image:</span>';
		$output.='<div class="def_cl"></div>';
	}
	
	
	echo $output;
    }
    
	/* load product getProduct Options */	
	
   public function getProductOptionsAction(){
		$productsku = $this->getRequest()->getPost('prodid');
		$product_id= Mage::getModel('catalog/product')->getIdBySku(trim($productsku));
		 
		$current_product = Mage::getModel('catalog/product')->load($product_id);
		//var_dump($current_product->getOptions());
		$options = Mage::getModel('externalform/externalform')->getProductOptions($current_product);		
		return $this->getResponse()->setBody($options);
		
		}	
		
	public function updateOrderAddressAction()
    {

		$params = $this->getRequest()->getParams();


		$parentId = $params['parentId'];
		$u = $params['backurl'];

		$city_bill    = $params['quote']['billing_address']['city'];

		$region		  = $params['quote']['billing_address']['region_id'];

		$zip_bill     = $params['quote']['billing_address']['postcode'];

		$phone_bill   = $params['quote']['billing_address']['telephone'];

		$country_bill = $params['billing']['country_id'];

		$addr_bill1   = $params['billing_address']['street'][0];

		$addr_bill2   = $params['billing_address']['street'][1];	

		$street = $addr_bill1.' '.$addr_bill2;	


		//echo "<br>";
//		var_dump($params);
//		exit;
		
		
		$city_ship    = $params['shipping_address']['city'];
		
		$region_ship   = $params['shipping_address']['region_id'];
		
		$zip_ship     = $params['shipping_address']['postcode'];
		
		$phone_ship   = $params['shipping_address']['telephone'];
		
		 $country_ship = $params['shipping']['country_id'];
		 
		 $addr_ship1   = $params['shipping_address']['street'][0];
		 
		 $addr_ship2   = $params['shipping_address']['street'][1];		
		 
	 	$street_ship   = $addr_ship1.' '.$addr_ship2;	
		
//var_dump($params);				 
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $table = 'sales_flat_order_address';
     
    $query = "UPDATE {$table} SET 
					 street 	= '{$street}',
					 city   	= '{$city_bill}',
					 region  	= '{$region}',
					 postcode  	= '{$zip_bill}',
					 country_id = '{$country_bill}',
					 telephone	= '{$phone_bill}'
			WHERE parent_id = ".$parentId." AND  address_type = 'billing' ";
    
//exit;	
	$writeConnection->query($query);
	
	    $query = "UPDATE {$table} SET 
					 street 	= '{$street_ship}',
					 city   	= '{$city_ship}',
					 region  	= '{$region_ship}',
					 postcode  	= '{$zip_ship}',
					 country_id = '{$country_ship}',
					 telephone	= '{$phone_ship}'
			WHERE parent_id = ".$parentId." AND  address_type = 'shipping' ";
    $writeConnection->query($query);

	    header('Location: '.$u);
	    	exit;
	

		 
		

/*
$eId = 546;

		

      $state_bill = 'Victoria';
      $country_bill  = 'AU';
      $fname_bill ='ijaz';
	  $lname_bill = 'ali';
      $street =  'house no 239 g model town, lahore';
	  $city_bill = 'lahore'; 
	  
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $table = 'sales_flat_order_address';
     
    $query = "UPDATE {$table} SET 
					 street 	= '{$street}',
					 city   	= '{$city_bill}',
					 region_id  = '491',
					 region  	= 'Victoria',
					 postcode  	= '54000',
					 country_id = 'AU',
					 telephone	= '03244321371'
			WHERE parent_id = 546 AND  address_type = 'shipping' ";
     
    $writeConnection->query($query);

    $tableHistory = Mage::getSingleton('core/resource')->getTableName('quotation_history');


    $sqlHistorySystem = "select * from sales_flat_order_address where parent_id = '".$eId."'";
	

    try {

    $chkSystem = $connectionRead->query($sqlHistorySystem);

    $fetchHistory = $chkSystem->fetchall();

    } catch (Exception $e){

    echo $e->getMessage();

    }

   $i=0;

var_dump($fetchHistory);


*/
		
		
	
       //  Zend_debug::dump($this->getRequest()->getPost());
	
    }	
	
   public function addMoreAction()
    {
	$storeId=$_POST["store_id"];
	$root_cat=$_POST["root_cat"];
	
	
	
	$_category = Mage::getModel('catalog/category')->load($root_cat);
	$_subcategories = $_category->getChildrenCategories();
    
	$output="";
    
   
		$output.='<div class="one_pro" >
				<div class="con" style="width:32%">
				  <span class="lab">Category:</span>
				  <span class="inp">
				  <label class="field select">
				    <select name="category[]" class="cat" onchange="getProduct(this.value,this);">
					<option value="">Select a category</option>';
					
					  foreach($_subcategories as $_sub)
					  {
					  
						$_sub=Mage::getModel('catalog/category')->setStoreId($storeId)->load($_sub->getId());
						$output.='<option value="'.$_sub->getId().'">'.$_sub->getName().'</option>';
					  
					  }
				    $output.='</select>
					<i class="arrow double"></i>
					</label>
					 <img id="load_product" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/externalform/images/loading2.gif" style="width:30px; height:30px; display:none; float:left;" />
				 </span>
				    
				</div>
				<div class="con proddiv">
				    <span class="lab">
					Products:
				    </span>
				    <span class="inp">
					<label class="field select">
					<select name="products[]" class="prod">
					      <option value="">Select a product</option>
					</select>
					<i class="arrow double"></i>
					</label>
				    </span>
				  
				</div>
				    
				<div class="im_con prodimgdiv">
				    <span class="im">Product Image:</span>
				    <div class="def_cl"></div>
				    
				</div>
				
				<div class="pro_qty">
				    <span class="lab">
					Quantity:
				    </span>
				    <input type="text" name="quantity[]" class="quantity" value="1" />
				</div>
				
				<div class="del_button">
				    <a href="javascript:void(0);" class="del_it">Delete</a>
				</div>
				<div class="products_options"> </div>
				
			</div>';
			
			echo $output;
	
    }
    
	
	/*public function remove item from quote*/
	
	public function itemRemoveAction(){
		
	        //$mydata = 	$this->getRequest()->getParam();

		    extract($_REQUEST);
			echo '<pre>';
			print_r($item);
			echo '</pre>';		
			$cart = Mage::getModel('checkout/cart')->getQuote();
			//$quote = Mage::getSingleton('checkout/session')->getQuote();
			//$cartItems = $quote->getAllVisibleItems();
			
			//print_r($cart);
			
			/******************* Start to delete item from cart 30_04_2014 ************************/
			$cartHelper = Mage::helper('checkout/cart');
			
			$cartHelper->getCart()->removeItem($item)->save();
			
			/******************* End to delete item from cart 30_04_2014 ************************/
			
		
		}
	
     /*//placing an order to quotation
	 function placeOrderAction
	 */
    public function placeOrderAction()
    {
	
		$store= Mage::app()->getStore()->load($_POST['store_id']);
		 Mage::app($store->getCode());
		
		////start of out 
	
		$html = array();
      
	    if(isset($_POST['shipaddress'])) {
		$check_shipp_addr=1;
	    } else {
		$check_shipp_addr=0;
	    }    
	  
	//Get Customer Billing Address Data
	
	    $fname_bill=$_POST['fnamebill'];
	    $lname_bill=$_POST['lnamebill'];
	    $comp=$_POST["comp_val"];
	    
	    $email_bill=$_POST['emailbill'];
	    $addr_bill1=$_POST['add1bill'];
	    $addr_bill2=$_POST['add2bill'];
	    $city_bill=$_POST['citybill'];
	    $state_bill=$_POST['statebill'];
	    $zip_bill=$_POST['zipbill'];
	    $country_bill="AU";//$_POST['countrybill'];
	    $phone_bill=$_POST['phonebill'];
     
	    
	
	    $billingAddress = array(
		'firstname' => $fname_bill,
		'lastname' => $lname_bill,
		'company' => $comp,
		'email' =>  $email_bill,
		'street' => array($addr_bill1,$addr_bill2),
		'city' => $city_bill,
		'region_id' => $state_bill,
		'region' => $state_bill,
		'postcode' => $zip_bill,
		'country_id' => $country_bill,
		'telephone' =>  $phone_bill,
		'fax' => '',
		'customer_password' => '',
		'confirm_password' =>  '',
		'save_in_address_book' => '0',
		'use_for_shipping' => '1',
	    );
	
	
	
	//Get Customer Shipping Address Data
	
	    $fname_ship=$_POST['fnameship'];
	    $lname_ship=$_POST['lnameship'];
	    $comp_ship=$_POST["comp_val_ship"];	
	    //$email_bill=$_POST['emailbill'];
	    $addr_ship1=$_POST['add1ship'];
	    $addr_ship2=$_POST['add2ship'];
	    $city_ship=$_POST['cityship'];
	    $state_ship=$_POST['stateship'];
	    $zip_ship=$_POST['zipship'];
	    $country_ship="AU";//$_POST['countryship'];
	    $phone_ship=$_POST['phoneship'];
	
	
	    $shippingAddress = array(
		'firstname' => $fname_ship,
		'lastname'  => $lname_ship,
		'company' => $comp_ship,
		'street'    => array($addr_ship1,$addr_ship2),
		'city'      => $city_ship,
		'region'    => $state_bill,
		'region_id' => $state_bill,
		'postcode'  => $zip_ship,
		'country_id' => $country_ship,
		'telephone' => $phone_ship,
	    );
	
	
	//Get Credit Card Details
	
	    //$card_verNum=$_POST['ccVerNum'];
	    //$card_Number=$_POST['creditcardNum'];
	    //$card_type=$_POST['creditcardtype'];
	    //$card_exp_year=$_POST['creditcardYear'];
	    //$card_exp_month=$_POST['creditcardmonth'];
	    $quote = Mage::getModel('sales/quote')->setStoreId($_POST['store_id']);
		//Load Product and add to cart
	    
	    $qty = $_POST['quantity'];
	    $skuu = $_POST["products"];
	    
	    
	    $stock_flag = 0;
	    $exist_pro = 0;
	    $productName = '';  		
		///if there are products in the quotation		
		if($skuu[0] != ''){					
		
		for($ii=0;$ii<count($skuu);$ii++){	    
		
		if(trim($skuu[$ii]) != '' and $qty[$ii] != '')
		{
		    $productid = Mage::getModel('catalog/product')->getIdBySku(trim($skuu[$ii]));		
		    $product = Mage::getModel('catalog/product')->load($productid);		
		
		if($product->getTypeID()=="bundle")
		{
			$arr_bundle_items=array();
			$arr_bundle_items=$product->getTypeInstance(true)->getChildrenIds($productid, false);						
			$arr_bundle_items=$arr_bundle_items[0];
			$_product = Mage::getModel('catalog/product')->load($productid);
			$arr_bundle_items = Mage::getModel('Quotation/Quotationitem')->getBundledChildrenProduct($_product);
	      
		foreach($arr_bundle_items as $val)
				{
									
			$_product1 = Mage::getModel('catalog/product')
										->setStore($_POST['store_id'])
										->setStoreId($_POST['store_id'])
										->load($val['product_id']);
		
			if($_product1->getStatus() == 1)
					 {	
					   $params = array(
						   'product' => $_product1->getId(),
						   'qty' => $_product1->getSelection_qty(),
						  
						   );
				   
		    $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
									->loadByProduct($_product1)->getQty();
	
					   $stocklevel=1;	
					   
					   if($stocklevel > 0)
					   {
						  $exist_pro = 1;		 	
						  $quote->addProduct($_product1, new Varien_Object($params));			
	
					   }else{
						 $stock_flag = 1;
						 $productName .= $_product1->getName().', ';
					   }
					 }
						  if($_product1->getIsPrintable()=="165")
						  {
							$stock_flag=0;
						  }
				}
		
		////end of product bundle items
		
		}else{

			$params = array(
					   'product' => $productid,
					   'qty' => $qty[$ii]					  
					  );
				   $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
				    ->loadByProduct($product)->getQty();
					
					
				   $stocklevel=1;
				  
				   if($stocklevel > 0)
				   {
				      $exist_pro = 1;		 	
				      $quote->addProduct($product, new Varien_Object($params));		
					
				   }else{
				     $stock_flag = 1;
				     $productName .= $product->getName().', ';
				   }
				  if($product->getIsPrintable()=="165")
				  {
					$stock_flag=0;
				  }
				}
			}
	       
	    }
    	
			} ///end of if products are not selected
	
     
	// Add Billing Address
	    
	    $quote->getBillingAddress()
		  ->addData($billingAddress);
    
	 
	//Add Shipping Address and set shipping method and payment method
	
	/*
	
	    if($check_shipp_addr==1)
	    {		
		$quote->getShippingAddress()
		    ->addData($shippingAddress)
		    ->setShippingMethod('flatrate_flatrate')
		    ->setCollectShippingRates(true)
		    ->collectShippingRates()
		    ->collectTotals();		
	    }else{
		
		
		$quote->getShippingAddress()
		    ->addData($billingAddress)		    	
		    ->setShippingMethod('flatrate_flatrate')		    	
		    ->setCollectShippingRates(true)
		    ->collectShippingRates()
		    ->collectTotals();


		   /* ->setCollectShippingRates(true)
		    ->collectShippingRates()
		    ->setShippingMethod('flatrate_flatrate')
	        ->setPaymentMethod('checkmo')*/
		    
		   
	  //  }
	
     
		
	/**************************** Start create customer if not exist **********************************/
	$pwd_length = 7; //auto-generated password length
	
	$customer = Mage::getModel('customer/customer');
	$customer->setWebsiteId(Mage::getModel('core/store')->load($_POST['store_id'])->getWebsiteId());
	$customer->loadByEmail($quote->getBillingAddress()->getEmail());	 
	
	///check if the customer is already registerd
	 
	if(!$customer->getId()) {

 	  $customer = Mage::getModel('customer/customer');
      $customer->setWebsiteId(Mage::getModel('core/store')->load($_POST['store_id'])->getWebsiteId());
	  $customer->setStore(Mage::app()->getStore()->load($_POST['store_id']));
      $customer->loadByEmail($quote->getBillingAddress()->getEmail());



	  //We're good to go with customer registration process
	  $pass = $customer->generatePassword($pwd_length);
	  $customer->setEmail($quote->getBillingAddress()->getEmail()); 
	  $customer->setFirstname($fname_bill);
	  $customer->setLastname($lname_bill);
	  //$customer->setPassword($customer->generatePassword($pwd_length));
	  $customer->setPassword($pass);
	  
	 
	  //if process fails, we don't want to break the page
		try{ 
	   // $customer->save();
	    
	    //$customer->setConfirmation(null); //confirmation needed to register?

	    //$customer->save(); //yes, this is also needed
	   /* $customer->setWebsiteId(Mage::getModel('core/store')->load($_POST['store_id'])->getWebsiteId());
	    $customer->setStore(Mage::app()->getStore()->load($_POST['store_id']));*/
	    $customer->save();
	    
	    /***************** Start Login the customer and send a account mail 02_05_2014 *************************/
	    $customer->setConfirmation(null);
	    $customer->save(); 
	  //  $customer->sendNewAccountEmail();
	    
	    $session = Mage::getSingleton('customer/session', array("name"=>"frontend"));
	    $log = $session->login($quote->getBillingAddress()->getEmail(), $pass);
	    $session->setCustomerAsLoggedIn($session->getCustomer());
	    $customer_id=$session->getCustomerId();

	    Mage::getSingleton('customer/session')->loginById($customer_id);
	    /***************** End Login the customer and send a account mail 02_05_2014 *************************/

	    /******************** Start to save the customer address *******************************/
	     if($check_shipp_addr==1)
	    {
 		$comp_ship=$_POST["comp_val_ship"];	
		$_custom_address_ship = array (
		    'firstname' => $fname_ship,
		    'lastname' => $lname_ship,
		    'company' => $comp_ship,	
		    'street' => array (
			'0' => $addr_ship1,
			'1' => $addr_ship2,
		    ),
		    'city' => $city_ship,
		    'region_id' => $state_ship,
		    'region' => $state_ship,
		    'postcode' => $zip_ship,
		    'country_id' => $country_ship, /* Croatia */
		    'telephone' => $phone_ship,
		);

		$datas=array();
		$datas['firstname']    = $fname_ship;
		    $datas['lastname']     = $lname_ship;
		    $datas['email']        = $email_bill;
			if($comp_ship != '')
		    $datas['company']      = $comp_ship;
		    $datas['phone']        = $phone_ship;
			if($addr_ship1 != '')
		    $datas['street1']      = $addr_ship1;
			if($addr_ship2 != '')
		    $datas['street2']      = $addr_ship2;
		    $datas['city']         = $city_ship;
		    $datas['region_id']    = $state_ship;
		    $datas['region']       = $state_ship;
		    $datas['postcode']     = $zip_ship;
		    $datas['country_id']   = $country_ship;
		    $datas['telephone']    = $phone_ship;
		    

		
		$_custom_address_bill = array (
		    'firstname' => $fname_bill,
		    'lastname' => $lname_bill,
		    'company' => $comp,
		    'street' => array (
			'0' => $addr_bill1,
			'1' => $addr_bill2,
		    ),
		    'city' => $city_bill,
		    'region_id' => $state_bill,
		    'region' => $state_bill,
		    'postcode' => $zip_bill,
		    'country_id' => $country_bill, /* Croatia */
		    'telephone' => $phone_bill,
		);

		$datab = array();
		    //$datab['quotation_id'] = $NewQuotation->getId();
		    $datab['firstname']    = $fname_bill;
		    $datab['lastname']     = $lname_bill;
		    $datab['email']        = $email_bill;
			if($comp != '')
		    $datab['company']      = $comp;
		    $datab['phone']        =  $phone_bill;
		    //$datab['hearabout']    = $_REQUEST['acee_how_did_you_hear_about_us'];		    
		    if($addr_bill1 != '')
		    $datab['street1']      = $addr_bill1;
			if($addr_bill2 != '')
		    $datab['street2']      = $addr_bill2;
		    $datab['city']         = $city_bill;
		    $datab['region_id']       = $state_bill;	
		    $datab['region']       = $state_bill;
		    $datab['postcode']     = $zip_bill;
		    $datab['country_id']   = $country_bill;
		    $datab['telephone']    = $phone_bill;

		
		$customAddress = Mage::getModel('customer/address');
		//$customAddress = new Mage_Customer_Model_Address();
		/*
		$customAddress->setData($_custom_address_ship)
			    ->setCustomerId($customer->getId())
			    ->setIsDefaultShipping('1')
			    ->setSaveInAddressBook('1');
		*/		
		try {
		    $customAddress->save();
		}
		catch (Exception $ex) {
		    //Zend_Debug::dump($ex->getMessage());
		}
		
		$customAddress->setData($_custom_address_bill)
			    ->setCustomerId($customer->getId())
			    ->setIsDefaultBilling('1')
			    ->setSaveInAddressBook('1');
		try {
		    $customAddress->save();
		}
		catch (Exception $ex) {
		    //Zend_Debug::dump($ex->getMessage());
		}
	    }
	    else{
		
		$customAddress = Mage::getModel('customer/address');
		$_custom_address_bill = array (
		    'firstname' => $fname_bill,
		    'lastname' => $lname_bill,
		    'company' => $comp,
		    'street' => array (
			'0' => $addr_bill1,
			'1' => $addr_bill2,
		    ),
		    'city' => $city_bill,
		    'region_id' => $state_bill,
		    'region' => $state_bill,
		    'postcode' => $zip_bill,
		    'country_id' => $country_bill, /* Croatia */
		    'telephone' => $phone_bill,
		);

		$datab = array();
		    //$datab['quotation_id'] = $NewQuotation->getId();
		    $datab['firstname']    = $fname_bill;
		    $datab['lastname']     = $lname_bill;
		    $datab['email']        = $email_bill;
			if($comp != '')
		    $datab['company']      = $comp;
		    $datab['phone']        =  $phone_bill;
		    //$datab['hearabout']    = $_REQUEST['acee_how_did_you_hear_about_us'];		    
		    if($addr_bill1 != '')
		    $datab['street1']      = $addr_bill1;
			if($addr_bill2 != '')
		    $datab['street2']      = $addr_bill2;
		    $datab['city']         = $city_bill;
		    $datab['region_id']       = $state_bill;
		    $datab['region']       = $state_bill;
		    $datab['postcode']     = $zip_bill;
		    $datab['country_id']   = $country_bill;
		    $datab['telephone']    = $phone_bill;

		    $datas = array();
		    
		    $datas['firstname']    = $fname_bill;
		    $datas['lastname']     = $lname_bill;
		    $datas['email']        = $email_bill;
			if($comp != '')
		    $datas['company']      = $comp;
		    $datas['phone']        =  $phone_bill;
			if($addr_bill1 != '')
		    $datas['street1']      = $addr_bill1;
			if($addr_bill2 != '')
		    $datas['street2']      = $addr_bill2;
		    $datas['city']         = $city_bill;
		    $datas['region_id']       = $state_bill;
		    $datas['region']       = $state_bill;
		    $datas['postcode']     = $zip_bill;
		    $datas['country_id']   = $country_bill;
		    $datas['telephone']    = $phone_bill;
		
		$customAddress->setData($_custom_address_bill)
			    ->setCustomerId($customer->getId())
			   // ->setIsDefaultShipping('1')
			    ->setIsDefaultBilling('1')
			    ->setSaveInAddressBook('1');
		try {
		    $customAddress->save();
		
		}
		catch (Exception $ex) {
		    //Zend_Debug::dump($ex->getMessage());
		}
	    }
	    /******************** End to save the customer address *******************************/
	    
	    $customer->sendNewAccountEmail(); //send confirmation email to customer?
	    
	   
	  
	  } catch(Exception $e){
	     Mage::log($e->__toString());
	    // print_r($e);
	  }
	
	}else{
	     if($check_shipp_addr==1)
	    {
        
		$comp_ship=$_POST["comp_val"];	  
		$_custom_address_ship = array (
		    'firstname' => $fname_ship,
		    'lastname' => $lname_ship,
   		    'company' => $comp_ship,
		    'street' => array (
								'0' => $addr_ship1,
								'1' => $addr_ship2,
		    ),
		    'city' => $city_ship,
		    'region_id' => $state_ship,
		    'region' => $state_ship,
		    'postcode' => $zip_ship,
		    'country_id' => $country_ship, /* Croatia */
		    'telephone' => $phone_ship,
		);

		$datas=array();
		$datas['firstname']    = $fname_ship;
		    $datas['lastname']     = $lname_ship;
		    $datas['email']        = $email_bill;
			if($comp_ship != '')
		    $datas['company']      = $comp_ship;
		    $datas['phone']        = $phone_ship;
			if($addr_ship1 != '')
		    $datas['street1']      = $addr_ship1;
			if($addr_ship2 != '')
		    $datas['street2']      = $addr_ship2;
		    $datas['city']         = $city_ship;
		    $datas['region_id']       = $state_ship;
		    $datas['region']       = $state_ship;
		    $datas['postcode']     = $zip_ship;
		    $datas['country_id']   = $country_ship;
		    $datas['telephone']    = $phone_ship;
			$datas['inhand'] = $_POST['del_date'];
			$datas['inhand1'] = $_POST['del_date'];
		
		$_custom_address_bill = array (
		    'firstname' => $fname_bill,
		    'lastname' => $lname_bill,
		    'company' => $comp,
		    'street' => array (
								'0' => $addr_bill1,
								'1' => $addr_bill2,
		    ),
		    'city' => $city_bill,
		    'region_id' => $state_bill,
		    'region' => $state_bill,
		    'postcode' => $zip_bill,
		    'country_id' => $country_bill, /* Croatia */
		    'telephone' => $phone_bill,
		);

		$datab = array();
		    //$datab['quotation_id'] = $NewQuotation->getId();
		    $datab['firstname']    = $fname_bill;
		    $datab['lastname']     = $lname_bill;
		    $datab['email']        = $email_bill;
			if($comp != '')
		    $datab['company']      = $comp;
		    $datab['phone']        =  $phone_bill;
		    //$datab['hearabout']    = $_REQUEST['acee_how_did_you_hear_about_us'];		    
		    if($addr_bill1 != '')
		    $datab['street1']      = $addr_bill1;
		if($addr_bill2 != '')
		    $datab['street2']      = $addr_bill2;
		    $datab['city']         = $city_bill;
		    $datab['region_id']       = $state_bill;
		    $datab['region']       = $state_bill;
		    $datab['postcode']     = $zip_bill;
		    $datab['country_id']   = $country_bill;
		    $datab['telephone']    = $phone_bill;

		    
		
		$customAddress = Mage::getModel('customer/address');
		//$customAddress = new Mage_Customer_Model_Address();
		/*
		$customAddress->setData($_custom_address_ship)
			    ->setCustomerId($customer->getId())
			    ->setIsDefaultShipping('1')
			    ->setSaveInAddressBook('1');
		*/		
		try {
		    $customAddress->save();
		}
		catch (Exception $ex) {
		    //Zend_Debug::dump($ex->getMessage());
		}
		
		$customAddress->setData($_custom_address_bill)
			    ->setCustomerId($customer->getId())
			    ->setIsDefaultBilling('1')
			    ->setSaveInAddressBook('1');
		try {
		    $customAddress->save();
		}
		catch (Exception $ex) {
		    //Zend_Debug::dump($ex->getMessage());
		}
	    	}else{
		
			$customAddress = Mage::getModel('customer/address');
			$_custom_address_bill = array (
				'firstname' => $fname_bill,
				'lastname' => $lname_bill,
				'company' => $comp,
				'street' => array (
									'0' => $addr_bill1,
									'1' => $addr_bill2,
				),
				'city' => $city_bill,
				'region_id' => $state_bill,
				'region' => $state_bill,
				'postcode' => $zip_bill,
				'country_id' => $country_bill, /* Croatia */
				'telephone' => $phone_bill,
			);

		$datab = array();
		    //$datab['quotation_id'] = $NewQuotation->getId();
		    $datab['firstname']    = $fname_bill;
		    $datab['lastname']     = $lname_bill;
		    $datab['email']        = $email_bill;
		    if($comp != '')
		    $datab['company']      = $comp;
		    $datab['phone']        =  $phone_bill;
		    //$datab['hearabout']    = $_REQUEST['acee_how_did_you_hear_about_us'];		    
		    if($addr_bill1 != '')
		    $datab['street1']      = $addr_bill1;
		    if($addr_bill2 != '')
		    $datab['street2']      = $addr_bill2;
		    $datab['city']         = $city_bill;
		    $datab['region_id']       = $state_bill;
		    $datab['region']       = $state_bill;
		    $datab['postcode']     = $zip_bill;
		    $datab['country_id']   = $country_bill;
		    $datab['telephone']    = $phone_bill;


		    $datas = array();
		    
		    $datas['firstname']    = $fname_bill;
		    $datas['lastname']     = $lname_bill;
		    $datas['email']        = $email_bill;
		    if($comp != '')
		    $datas['company']      = $comp;
		    $datas['phone']        =  $phone_bill;
		    if($addr_bill1 != '')
		    $datas['street1']      = $addr_bill1;
		    if($addr_bill2 != '')
		    $datas['street2']      = $addr_bill2;
		    $datas['city']         = $city_bill;
		    $datas['region_id']       = $state_bill;
		    $datas['region']       = $state_bill;
		    $datas['postcode']     = $zip_bill;
		    $datas['country_id']   = $country_bill; 
		    $datas['telephone']    = $phone_bill;
			$datas['inhand'] = $_POST['del_date'];
			$datas['inhand1'] = $_POST['del_date'];
		
		$customAddress->setData($_custom_address_bill)
			    ->setCustomerId($customer->getId())
			   // ->setIsDefaultShipping('1')
			    ->setIsDefaultBilling('1')
			    ->setSaveInAddressBook('1');
		try {
		    $customAddress->save();
		
		}
		catch (Exception $ex) {
		    //Zend_Debug::dump($ex->getMessage());
		}
	  }
	    /******************** End to save the customer address *******************************/
	}
	
	
	 		
	/***************************** End create customer if not exist *******************************************/
	
	//Set Customer group As Guest Customer
	$quote->setCheckoutMethod('guest')
		->setCustomerId(null)
		->setCustomerEmail($quote->getBillingAddress()->getEmail())
		->setCustomerIsGuest(true)
		->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
	 
	
	$quote->setCustomerId($customer->getId())->setCustomerEmail($quote->getBillingAddress()->getEmail());
     
     
	//Set Card Details
     
	    //$paymentMethod =array(
	    //    'method' => 'ccsave',
	    //    'cc_cid' => $card_verNum,
	    //    'cc_owner' => '',
	    //    'cc_number' => $card_Number,
	    //    'cc_type' => $card_type,
	    //    'cc_exp_year' => $card_exp_year,
	    //    'cc_exp_month' => $card_exp_month
	    //);
	
	//Set Payment method and payment details
	
	    $payment = $quote->getPayment();	    
	    $payment->importData(array('method' => 'checkmo'));	   
	    $quote->getBillingAddress()->setPaymentMethod($payment->getMethod());
	   // $quote->getShippingAddress()->setPaymentMethod($payment->getMethod());
	    $quote->setPayment($payment);
	   $quote->collectTotals()
		->save();
	   
	//For MoneyOrder Payment Method Only
	 
	    //$quote->getPayment()->importData( array('method' => 'checkmo'));
	    //$quote->save();
     
     
	//Save Order With All details
	
	    if($_POST["type"]=="invoice")
	    {
		if($exist_pro == 1)
		{
		    if($stock_flag == 0)
		    {
			
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			$customer = Mage::getModel('customer/customer')->load($customer->getId());
        
			$transaction = Mage::getModel('core/resource_transaction');
			$storeId = $_POST['store_id'];
			$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
			
			//$reservedOrderId = $quote->getIncrementId();
			
			
			
			$currency_code = Mage::app()->getStore($storeId)->getCurrentCurrencyCode();
			
			$order = Mage::getModel('sales/order')
			->setIncrementId($reservedOrderId)
			->setStoreId($storeId)
			->setQuoteId(0)
			->setGlobal_currency_code($currency_code)
			->setBase_currency_code($currency_code)
			->setStore_currency_code($currency_code)
			->setOrder_currency_code($currency_code);
			
			// set Customer data
			$order->setCustomer_email($customer->getEmail())
			->setCustomerFirstname($customer->getFirstname())
			->setCustomerLastname($customer->getLastname())
			->setCustomerGroupId($customer->getGroupId())
			->setCustomer_is_guest(0)
			->setCustomer($customer);
			
			// set Billing Address
			$billing = $customer->getDefaultBillingAddress();
			if($billing){
			// exit('no billing address');
			$billingAddress = Mage::getModel('sales/order_address')
			->setStoreId($storeId)
			->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
			->setCustomerId($customer->getId())
			->setCustomerAddressId($customer->getDefaultBilling())
			->setCustomer_address_id($billing->getId())
			->setPrefix($billing->getPrefix())
			->setFirstname($billing->getFirstname())
			->setMiddlename($billing->getMiddlename())
			->setLastname($billing->getLastname())
			->setSuffix($billing->getSuffix())
			->setCompany($billing->getCompany())
			->setStreet($billing->getStreet())
			->setCity($billing->getCity())
			->setCountry_id($billing->getCountryId())
			->setRegion($billing->getRegion())
			->setRegion_id($billing->getRegionId())
			->setPostcode($billing->getPostcode())
			->setTelephone($billing->getTelephone())
			->setFax($billing->getFax());
			$order->setBillingAddress($billingAddress);
			}
			else
			{
			    $billingAddress = $billingAddress = Mage::getModel('sales/order_address')
			    ->setStoreId($storeId)
			    ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING);
			    $order->setBillingAddress($billingAddress);
			}
			
			$shipping = $customer->getDefaultShippingAddress();
			if($shipping){
			// exit('no shipping address');
	
			$shippingAddress = Mage::getModel('sales/order_address')
			->setStoreId($storeId)
			->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
			->setCustomerId($customer->getId())
			->setCustomerAddressId($customer->getDefaultShipping())
			->setCustomer_address_id($shipping->getId())
			->setPrefix($shipping->getPrefix())
			->setFirstname($shipping->getFirstname())
			->setMiddlename($shipping->getMiddlename())
			->setLastname($shipping->getLastname())
			->setSuffix($shipping->getSuffix())
			->setCompany($shipping->getCompany())
			->setStreet($shipping->getStreet())
			->setCity($shipping->getCity())
			->setCountry_id($shipping->getCountryId())
			->setRegion($shipping->getRegion())
			->setRegion_id($shipping->getRegionId())
			->setPostcode($shipping->getPostcode())
			->setTelephone($shipping->getTelephone())
			->setFax($shipping->getFax());
		       
			$order->setShippingAddress($shippingAddress)
			->setShippingMethod('flatrate_flatrate')
			->setShippingDescription("Flatrate");
			

			$flat_rate=Mage::getStoreConfig('carriers/flatrate/price');

			

			$shippingprice=$flat_rate;
			$order->setShippingAmount($shippingprice);
        		$order->setBaseShippingAmount($shippingprice);

			/*echo "AAAAA ".$quote->getShippingMethod();
			exit;*/
			} else{
			    $shippingAddress = Mage::getModel('sales/order_address')
			    ->setStoreId($storeId)
			    ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);
			    $order->setShippingAddress($shippingAddress);
			}
			
			$orderPayment = Mage::getModel('sales/order_payment')
			->setStoreId($storeId)
			->setCustomerPaymentId(0)
			->setMethod('free');
			$order->setPayment($orderPayment);
			
			
			//echo "<pre>";
			//print_r($quote->getItems());
			//exit;
			// let say, we have 2 products
			$subTotal = 0;
			$total_qty_ordered = 0;
			//$products = array('1' => array('qty' => 1),'2' =>array('qty' => 1));
			
			for($ii=0;$ii<count($skuu);$ii++)
			{
			    

			//$_product = Mage::getModel('catalog/product')->load($product->getProductId());
			$productid = Mage::getModel('catalog/product')->getIdBySku(trim($skuu[$ii]));

			$_product = Mage::getModel('catalog/product')->load($productid);

			$request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
			$taxclassid = $product->getData('tax_class_id');
			$percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxclassid));
			
			
			
			$rowTotalbase = ($_product->getPrice() * $qty[$ii]);

			$rowTotalTax = (($_product->getPrice() * $qty[$ii])*$percent)/100;

			$rowTotalinctax=$rowTotalbase+$rowTotalTax;

			
			$orderItem = Mage::getModel('sales/order_item')
			->setStoreId($storeId)
			->setQuoteItemId(0)
			->setQuoteParentItemId(NULL)
			->setProductId($_product->getId())
			->setProductType($_product->getTypeId())
			->setQtyBackordered(NULL)
			->setTotalQtyOrdered($qty[$ii])
			->setQtyOrdered($qty[$ii])
			->setName($_product->getName())
			->setSku($_product->getSku())
			->setPrice($_product->getPrice())
			->setBasePrice($_product->getPrice())
			->setOriginalPrice($_product->getPrice())
			->setTaxAmount((($_product->getPrice()*$percent)/100))
			->setTaxPercent($percent)
			->setRowTotal($rowTotalbase)			
			->setBaseRowTotal($rowTotalbase);
			
			$total_qty_ordered += $qty[$ii];
			$subTotal += $rowTotalbase;
			$order->addItem($orderItem);
			}
			
			$order->setSubtotal($subTotal)
			->setBaseSubtotal($subTotal)
			->setGrandTotal($subTotal)
			->setTotalQtyOrdered($total_qty_ordered)
			->setBaseGrandTotal($subTotal);
			/*echo "Here";
			exit;*/
			$order->save;
			
			/* $items = $order->getAllItems();
                        $itemcount=count($items);
                        $name=array();
                        $unitPrice=array();
                        $sku=array();
                        $ids=array();
                        $qty=array();
                        foreach ($items as $itemId => $item)
                        {
                            $name[] = $item->getName();
                           $unitPrice[]=$item->getPrice();
                           $sku[]=$item->getSku();
                           echo $ids[]=$item->getProductId();
                           $qty[]=$item->getQtyToInvoice();
                        }*/

			
			$transaction->addObject($order);
			$transaction->addCommitCallback(array($order, 'place'));
			$transaction->addCommitCallback(array($order, 'save'));
			$transaction->save();
			

			$order->addStatusToHistory($order->getStatus(), $_POST['comment'], false);
			$order->save();
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			/*$service = Mage::getModel('sales/service_quote', $quote);
			
			//$service->submitAll()
			
			$service->submitAll();
			 
			$increment_id = $service->getOrder()->getIncrementId();
		
			*/

			//echo "OK";
			//exit;
			
			Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "1");
		    
			$order_mail = new Mage_Sales_Model_Order();
			$order_mail->loadByIncrementId($reservedOrderId);
			$order_mail->sendNewOrderEmail();
			//echo "Hi<br>";
			// Resource Clean-Up
			echo "order___";
			echo $reservedOrderId;
			echo "___ ";
			
			
			//$order = Mage::getModel('sales/order')->load($service->getOrder()->getId());
	
			//$order->sendNewOrderEmail();
			
			
			//Create auto invoice
			$invoice = $order->prepareInvoice();
			
			$invoice->register();
			Mage::getModel('core/resource_transaction')
			  ->addObject($invoice)
			  ->save();
			
			$invoice->sendEmail(true, '');
			
			$items = $order->getAllItems();


			foreach($items as $item)
			{
			    $ProductId = $item->getProductId();
			    
			    $incrementId = $order->getIncrementId();
			   
				$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
				
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
				{
				    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
				    {
					$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'";
					$chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
				    }
				   
				    if($chkOrganiger)
				    {
					
					foreach($chkOrganiger as $chkOrganiger1)
					{
					    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
					    {
						if($chkOrganiger1['ot_day'] != '')
						$finished_date = date ( 'Y-m-j', strtotime ( '+'.$chkOrganiger1['ot_day'].' day' . date('Y-m-d') ) );
						else
						$finished_date = date('Y-m-d');
						
						
						$temptableNumber=Mage::getSingleton('core/resource')->getTableName('subadmin_task_number');
						if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableNumber))
						{
						    $sqlNumber="SELECT * FROM ".$temptableNumber." WHERE entity_id = '1' ";
						    $chkNumber = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlNumber);
						}
						
						$flag = 0;
						$finished_date = date ( 'Y-m-j');
						if($chkNumber[0]['task_number'] != '')
						{
						    while($flag == 0)
						    {
							$sqlTask2="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user = '".$chkOrganiger1['ot_target_user']."' AND ot_deadline ='".$finished_date."'";
							$chkTask2 = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTask2);
							
							if(count($chkTask2) > $chkNumber[0]['task_number'])
							{
							    $finished_date = date ( 'Y-m-j', strtotime ( '+1 day' . $finished_date ) );
							    
							}
							else{
							    $flag = 1;
							}
						    }
						}
						
						
						$sqlOrganiger1="INSERT INTO ".$temptableOrganiger." SET ot_created_at = NOW(), 
							       ot_author_user = '".$chkOrganiger1['ot_author_user']."' ,
							       ot_target_user ='".$chkOrganiger1['ot_target_user']."', 
							       ot_caption= '".addslashes($chkOrganiger1['ot_caption'])."', 
							       ot_description = '".addslashes($chkOrganiger1['ot_description'])."', 
							       ot_deadline = '".$finished_date."', 
							       ot_notify_date = '".$chkOrganiger1['ot_notify_date']."', 
							       ot_priority = '".$chkOrganiger1['ot_priority']."', 
							       ot_finished = '".$chkOrganiger1['ot_finished']."', 
							       ot_read ='".$chkOrganiger1['ot_read']."', 
							       ot_origin ='".$chkOrganiger1['ot_origin']."', 
							       ot_category = '".$chkOrganiger1['ot_category']."', 
							       ot_entity_type ='order', 
							       ot_entity_id = '".$order->getId()."', 
							       ot_entity_description = '".addslashes($chkOrganiger1['ot_entity_description'])."', 
							       ot_notification_read = '".$chkOrganiger1['ot_notification_read']."',
							       ot_task_type = '".$chkOrganiger1['ot_task_type']."'";
							       
					        $chkOrganiger2 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlOrganiger1);
					    }
					
					//For chain task
					    $last_id = Mage::getSingleton('core/resource')->getConnection('core_write')->lastInsertId();
					
					    $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					    
					    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					    {
						$sqlChain="INSERT INTO ".$temptableChain." SET task_id = '$last_id', 
							   order_quote_id = '".$order->getId()."' ,
							   product_id ='".$chkOrganiger1['ot_entity_id']."', 
							   task_type = '".$chkOrganiger1['ot_task_type']."'";
								
						$chkChain = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChain);
					    }
					    
					   
					}
				    }
				}
				
			 
				 /*********************** add planning auto *********************************/
				$temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
				
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
				{
				    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
				    {
					$sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ";
					$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlShipping);
				    }
				    
				   
				    
				    if(count($chkShipping) == 0)
				    {
				    
					$created_date = $order->getCreatedAt();
					$Product = Mage::getModel('catalog/product')->load($ProductId);
					
					$temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
					$sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
					$chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTimeline);
					
				    
				    if(!$_POST['del_date'])
				    {
					$order_placed_date =  $created_date;
					
					$artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
					$proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
					$production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
					$shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
					$delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
				    }
				    else
				    {
					 $order_placed_date = date ( 'Y-m-j', strtotime ( '-'.$chkTimeline[0]['delivary_day'].' day' . $_POST['del_date'] ) );
					$artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$_POST['del_date'],$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
					$proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$_POST['del_date'],$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
					$production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$_POST['del_date'],$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
					$shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$_POST['del_date'],$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
					$delivery_date = $_POST['del_date'];
				    }
					
							  
					$temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
					{
					    $sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$order->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'order', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
					    $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
					}
				    }
				}
				
			       
			    
				
			    }
			    /*********************** add planning auto *********************************/
			    
			    /************************ Get custom option value ******************************/
			    
			    $_options = $item->getProductOptions();
		       
			    foreach($_options as $o => $option){
			       
				foreach($option as $optionvalue)
				{
				    if($optionvalue['label'] == 'Graphic Design Service'){
				       
					if($optionvalue['value'] != '')
					{
					    $title = explode(' ',$optionvalue['value']);
					    
					    if (is_numeric($title[0]))
					    $revison_number = $title[0];
					    else
					    $revison_number = 10000;
					}
				    }
				}
			    }
			    
			    $temptableProduct=Mage::getSingleton('core/resource')->getTableName('catalog_product_designer');
			    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProduct))
			      {
				    $sqlProduct="SELECT * FROM ".$temptableProduct." WHERE product_id = '".$ProductId."'";
				    $chkProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlProduct);
			      }
			    
			    $adminid = $chkProduct[0]['user_id'];
			     
			    $temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
			    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableDesign))
			    {
				$sqlDesign="INSERT INTO  ".$temptableDesign." SET order_id = '".$order->getId()."', type='order', item_id ='".$item->getId()."', product_id = '".$ProductId."', revision_number = '".$revison_number."', assign_to = '".$adminid."', postdate = NOW() ";
				$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlDesign);
			    }
			    
			    /************************ Get custom option value ******************************/
			    			    
			
		    

		    }
		    else
		    {
			    echo "error___";
			    echo '0';
			    echo "___";
			    echo $productName = rtrim($productName,', ').' are not available in stock.';
		    }
		 }
		else
	       {
		   echo "error___";
		   echo '0';
		   echo "___";
		   echo 'Please resubmit the form or feel free to email or call us.';
	       }
	    }
	    else if($_POST["type"]=="quote")
	    {
		////check if form type is quote
		
		
		    
		if($stock_flag == 0)
		{
		    
		     try{
			
			$NewQuotation = Mage::getModel('Quotation/Quotation')
				    ->setcustomer_id($customer->getId())
				    ->setcaption('New request')
				   // ->setcustomer_msg($this->getRequest()->getPost('description'))
				    ->setshipping_method('freeshipping_freeshipping')
				    ->setcustomer_request(1)
				    ->setStore($store)
					->setValid_end_date(Date('Y-m-d', strtotime('+15 days')))
				    ->setstatus(MDN_Quotation_Model_Quotation::STATUS_NEW)
					->setStore_name($store->getCode())
				    //->setstatus(MDN_Quotation_Model_Quotation::STATUS_CUSTOMER_REQUEST)
				    //->setstatus(MDN_Quotation_Model_Quotation::STATUS_ACTIVE)
				 	->save();			
		
		//add products to quote
		$productName = '';
		$totalamount = 0;    
		  if($skuu[0]!=''){
		   
		   for($ii=0;$ii<count($skuu);$ii++)
			{
			    if(trim($skuu[$ii]) != '' and $qty[$ii] != '')
			    {			
				$productid = Mage::getModel('catalog/product')->getIdBySku(trim($skuu[$ii]));				
				$product = Mage::getModel('catalog/product')->load($productid);				
				$request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
				$taxclassid = $product->getData('tax_class_id');
				$percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxclassid));

				if($product->getTypeID()=="bundle")
				{
				    //add product
				    $quoteItem = $NewQuotation->addProduct($productid, $qty[$ii]);				    
				    //set options
				    $quoteItem->save();

					$temptableSaleOrderGrid=Mage::getSingleton('core/resource')->getTableName('quotation_items');
					$sqlSaleOrderGrid="UPDATE ".$temptableSaleOrderGrid." SET `product_type`='bundle' where `quotation_item_id`='".$quoteItem->getId()."'";
//exit;
					try {
					$chkSaleOrderGrid = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlSaleOrderGrid);
					} catch (Exception $e){
					//echo $e>getMessage();
					}

					/*$ids = Mage::getResourceSingleton('downloads/relation')->getFileIds($productid);
					$fileIds = array_merge($fileIds, $ids);
					

					$fileIds = array_unique($fileIds);

					$files = Mage::getResourceModel('downloads/files_collection');
					$files->addResetFilter()
					    ->addFilesFilter($fileIds)
					    ->addStatusFilter()
					    ->addCategoryStatusFilter()
					    ->addStoreFilter()
					    ->addSortOrder();*/

					/*$temptableSaleOrderGrid=Mage::getSingleton('core/resource')->getTableName('downloads_relation');
					$sqlSaleOrderGrid="select * from ".$temptableSaleOrderGrid." WHERE product_id='".$productid."'";
					try {
					$chkSaleOrderGrid = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlSaleOrderGrid);
					} catch (Exception $e){
					//echo $e>getMessage();
					}

					foreach($chkSaleOrderGrid as $res_objects) 
            				{
						//echo $res_objects["file_id"]."<br><br>";	
						$temptableSaleOrderGrid=Mage::getSingleton('core/resource')->getTableName('downloads_files');
						$sqlSaleOrderGrid="select * from ".$temptableSaleOrderGrid." WHERE file_id='".$res_objects["file_id"]."'";
						try {
						$chkSaleOrderGrid2 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlSaleOrderGrid);
						} catch (Exception $e){
						//echo $e>getMessage();
						}
						foreach($chkSaleOrderGrid2 as $res_objects2) 
            					{
							echo $res_objects2["file_id"]."  ".$res_objects2["filename"]."===";	
						}
					}				

					//echo $productid;
					exit;*/

					$arr_bundle_items=array();
					$arr_bundle_items=$product->getTypeInstance(true)->getChildrenIds($productid, false);			
					$arr_bundle_items=$arr_bundle_items[0];	
					
					$_product = Mage::getModel('catalog/product')->load($productid);
			        $arr_bundle_items = Mage::getModel('Quotation/Quotationitem')->getBundledChildrenProduct($_product);
	      				
			
					foreach($arr_bundle_items as $val)
					{
						$_product1 = Mage::getModel('catalog/product')
												->setStore($_POST['store_id'])
												->setStoreId($_POST['store_id'])
												->load($val->getProduct_id())
												;
					 $qty = $val->getSelection_qty();
					 
					 if($_product1->getStatus() == 1)
					     {
							//var_dump($qty); 	
						$temptableSaleOrderGrid=Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
						$sqlSaleOrderGrid="INSERT INTO ".$temptableSaleOrderGrid." SET entity_id='',quotation_id='".$NewQuotation->getId()."',product_id='".$_product1->getId()."', parent_item_id='".$quoteItem->getId()."',qty='".$qty."',price='".$_product1->getPrice()."',caption='".$_product1->getName()."',sku='".$_product1->getSku()."'";
						
						try {
								$chkSaleOrderGrid = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlSaleOrderGrid);
								} catch (Exception $e){
						//echo $e>getMessage();
						}
				     }
				}
		}
				else
				{	//add product
					$quoteItem = $NewQuotation->addProduct($productid, $qty[$ii]);
					//set options
					$quoteItem->save();
				}
				
				/************** Start to add tax 30_01_2014 ********************/
            						    
				$tax_helper = Mage::getSingleton('tax/calculation');
				$tax_request = $tax_helper->getRateOriginRequest();
				$tax_request->setProductClassId($product->getTaxClassId());				
				$tax = $tax_helper->getRate($tax_request);				
				$quoteItem->setEcoTax($tax);
				$quoteItem->save();
				
				$totalamount +=   $quoteItem->getPriceHt();
				
				/************** End to add tax 30_01_2014 ********************/
			    }
			  
			   
			}
		    
			/************** Start to add tax 30_01_2014 ********************/

			
			    if($product->getSpecialPrice()>0)
			    {
				$totalamount=$product->getSpecialPrice();
				$NewQuotation->setPriceHt($totalamount);
			    }else{
				$NewQuotation->setPriceHt($totalamount);
			    }
			    $NewQuotation->save();
			 } //end of if no items are there in the quotation 
			    
		
		
		/////////////////////////////////////////////////////////////////////////////////////////////////
		      $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		      $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
				$tableShipping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
				$tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
				$connectionWrite->beginTransaction();
				$datab['quotation_id'] = $NewQuotation->getId();
				$connectionWrite->insert($tableBilling, $datab);
				$connectionWrite->commit(); 				
				
				///if default shipping address is set
				if($customer->getDefault_shipping()){
					
					$connectionWrite->beginTransaction();
					$datas['quotation_id'] = $NewQuotation->getId();
					$connectionWrite->insert($tableShipping, $datas);
					$connectionWrite->commit();
				}
			   /////////////////////////////////////////////////////////////////////////////////////////////////	
				$notificationModel = Mage::getModel('Quotation/Quotation_Notification');
				
				
				///sending automatically new quotatoin email to the client
				/* for temp bases commented* /
			   // $notificationModel->NotifyCreationToAdmin($NewQuotation);	
			    
				/************** End to add tax 30_01_2014 ********************/		
			     
			$new_quotatoin_number = $NewQuotation->getIncrementId();
			$quote1 = Mage::getModel('Quotation/Quotation')->load($NewQuotation->getId());
			$quoteItems = $quote1->getItems();			
			$temptableHistory=Mage::getSingleton('core/resource')->getTableName('quotation_history');
			
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHistory))
			{
			    
			    $sqlHistory="INSERT INTO ".$temptableHistory." SET qh_quotation_id = '".$NewQuotation->getId()."', qh_message = '".$_POST['comment']."', qh_date = '".date('Y-m-d h:i:s a')."', qh_user = '".$fname_bill.' '.$lname_bill."' ";
			    $chkHistory = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHistory);
			}
			
			
			$id = '';
		foreach ($quoteItems as $item) {
			    $id .= "'".$item->getId()."', ";			
			    $ProductId = $item->getProductId();			    
			    $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');		    
		 
		 if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
		  {
		 $sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'";
		 $chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
	
	 if($chkOrganiger)
				{
						
		foreach($chkOrganiger as $chkOrganiger1)
				    {
					if($chkOrganiger1['ot_day'] != '')
					$finished_date = date ( 'Y-m-j', strtotime ( '+'.$chkOrganiger1['ot_day'].' day' . date('Y-m-d') ) );
					else
					$finished_date = date('Y-m-d');
					

					
					$temptableNumber=Mage::getSingleton('core/resource')->getTableName('subadmin_task_number');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableNumber))
					{
					    $sqlNumber="SELECT * FROM ".$temptableNumber." WHERE entity_id = '1' ";
					    $chkNumber = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlNumber);
					}
					
					$flag = 0;
					
					$finished_date = date ( 'Y-m-j');

					if($chkNumber[0]['task_number']!="")
					{

						while($flag == 0)
						{
						    $sqlTask2="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user = '".$chkOrganiger1['ot_target_user']."' AND ot_deadline ='".$finished_date."'";
						    $chkTask2 = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTask2);
						    
						    if(count($chkTask2) > $chkNumber[0]['task_number'])
						    {
							$finished_date = date ( 'Y-m-j', strtotime ( '+1 day' . $finished_date ) );
						
						    }
						    else{
							$flag = 1;

						    }
						}
					}
						
					$sqlOrganiger1="INSERT INTO ".$temptableOrganiger." SET ot_created_at = NOW(), 
						       ot_author_user = '".$chkOrganiger1['ot_author_user']."' ,
						       ot_target_user ='".$chkOrganiger1['ot_target_user']."', 
						       ot_caption= '".addslashes($chkOrganiger1['ot_caption'])."', 
						       ot_description = '".addslashes($chkOrganiger1['ot_description'])."', 
						       ot_deadline = '".$finished_date."', 
						       ot_notify_date = '".$chkOrganiger1['ot_notify_date']."', 
						       ot_priority = '".$chkOrganiger1['ot_priority']."', 
						       ot_finished = '".$chkOrganiger1['ot_finished']."', 
						       ot_read ='".$chkOrganiger1['ot_read']."', 
						       ot_origin ='".$chkOrganiger1['ot_origin']."', 
						       ot_category = '".$chkOrganiger1['ot_category']."', 
						       ot_entity_type ='quote', 
						       ot_entity_id = '".$quote1->getId()."', 
						       ot_entity_description = '".addslashes($chkOrganiger1['ot_entity_description'])."', 
						       ot_notification_read = '".$chkOrganiger1['ot_notification_read']."',
						       ot_task_type = '".$chkOrganiger1['ot_task_type']."'";
						       
				       $chkOrganiger2 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlOrganiger1);
				      
					//For chain task
					$last_id = Mage::getSingleton('core/resource')->getConnection('core_write')->lastInsertId();
					
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					    $sqlChain="INSERT INTO ".$temptableChain." SET task_id = '$last_id', 
							    order_quote_id = '".$quote1->getId()."' ,
							    product_id ='".$chkOrganiger1['ot_entity_id']."', 
							    task_type = '".$chkOrganiger1['ot_task_type']."'";
							    
					    $chkChain = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChain);
					}
				    }
				}
			    }
			    
			    /*********************** add planning auto *********************************/
				$temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
				{
				$sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$quote1->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'quote' ";
				$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlShipping);
				}
				
				if(count($chkShipping) == 0)
				{
				
				    $created_date = $quote1->getCreatedTime();
				    
				    $Product = Mage::getModel('catalog/product')->load($ProductId);
				    
				    $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
				    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableTimeline))
				    {
				    $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
				    $chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTimeline);
				    }
				
				    if(!$_POST['del_date'])
				    {
					$order_placed_date =  $created_date;
					$artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
					$proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
					$production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
					$shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
					$delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
					
				    }
				    else
				    {
					 $order_placed_date = date ( 'Y-m-j', strtotime ( '-'.$chkTimeline[0]['delivary_day'].' day' . $_POST['del_date'] ) );
					$artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$_POST['del_date'],$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
					$proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$_POST['del_date'],$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
					$production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$_POST['del_date'],$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
					$shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$_POST['del_date'],$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
					$delivery_date = $_POST['del_date'];
				    }
				    
						      
				    $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
				    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
				    {
				    $sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$quote1->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'quote', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
				    $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
				    }
				}
				
			       
			    
			    /*********************** add planning auto *********************************/
			    
			    /************************ Get custom option value ******************************/
				
				$productOptions= $item->getOptions();
				$productOptions = Mage::helper('quotation/Serialization')->unserializeObject($productOptions);
				
				$_product =Mage::getModel('catalog/product')->load($ProductId);
			   
				foreach ($_product->getOptions() as $option) { 
				  
				   $values = $option->getValues(); 
				    foreach ($values as $value)
				    {
					if($option->getTitle() == 'Graphic Design Service'){
					   
					    if($value->getId() == $productOptions[$option->getId()])
					    {
						$title = explode(' ',$value->getTitle());
						
						if (is_numeric($title[0]))
						$revison_number = $title[0];
						else
						$revison_number = 10000;
					    }
					}
				    }
				}
				
				
				$temptableProduct=Mage::getSingleton('core/resource')->getTableName('catalog_product_designer');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProduct))
				  {
					$sqlProduct="SELECT * FROM ".$temptableProduct." WHERE product_id = '".$ProductId."'";
					$chkProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlProduct);
				  }
				
				$adminid = $chkProduct[0]['user_id'];
				 
				$temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableDesign))
				{
				    $sqlDesign="INSERT INTO  ".$temptableDesign." SET order_id = '".$quoteId."', type='quote', item_id ='".$item->getId()."', product_id = '".$ProductId."', revision_number = '".$revison_number."', assign_to = '".$adminid."', postdate = NOW() ";
				    $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlDesign);
				}
				
				/************************ Get custom option value ******************************/					    
			  }
			/******************* Delete exrta the planner from table *************************/
			$id = rtrim($id,', ');
		
		
			$temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping) and $id != '')
			{
			$sqlShipping="DELETE FROM  ".$temptableShipping." WHERE quote_id = '".$quote1->getId()."' AND item_id NOT IN($id) AND planning_type = 'quote' ";
			$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
			}
			/******************* Delete exrta the planner from table *************************/
			
			$html['type'] = 'quote';
			$html['id']     = $new_quotatoin_number;
			$html['msg'] = 'Success: You have created new quotation';
			$html['hlink'] = Mage::getBaseUrl().'index.php/externalform/index/thankyou';
			$html['error']=0;
			Mage::getSingleton("core/session",array('name' => 'frontend'))->setQuoteNo($new_quotatoin_number);
			
		     } catch(Exception $e){
			Mage::log($e->__toString());
		       // print_r($e);
		     }
		   
		}else{
		    $error = "Oops error occured during";		    
		    $error .= '<br/>'.$productName = rtrim($productName,', ').' are not available in stock.';
			$html['error'] = $error;
		}
		
	   }
	   
		 $quote = $customer = $service = null;  

		 Mage::getSingleton('core/session',array('name'=>'frontend'))->unsProducts();
		 echo json_encode($html); 
		     
	    
	    // Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "1");
	    //$newOrder = $service->getOrder();
	    //$order = new Mage_Sales_Model_Order();
	    //$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
	    //$_order = Mage::getModel('sales/order')->load($increment_id);
	    //$_order->sendNewOrderEmail();
	    
	   //$order->sendNewOrderEmail();
     
     
	
    }
    
    /***************************** Add custom function ***********************************/
    public function isweekend($date)
    {
		$date = strtotime($date);
		$date = date("l", $date);
		$date = strtolower($date);
		if($date == "sunday"){
		 return 1;
		} else {
		 return 0;
		}
    }
   
    public function gettimelinedate($day_delay,$created_date,$sunday,$holiday)
    {
		if($sunday == 0 and $holiday == 0)
		{
			$artwork_date = date ( 'Y-m-j', strtotime ( '+'.$day_delay.' day' . $created_date ) );
		}
		else
		{
	    if($sunday == 1)
	    {
		$flag = 0;
		$artwork_date = date ( 'Y-m-j', strtotime ( '+'.$day_delay.' day' . $created_date ) );
		
		$d = $this->isweekend($artwork_date);
		if($holiday == 1)
		{
		    while($flag == 0)
		    {
			$artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
			
			$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
			$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
			$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
			
			if(count($chkHoliday) > 0)
			{
			    $d++;
			}
			else
			{
			   $flag = 1; 
			}
		    }
		    
		}
		else
		{
		    $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
		}
		
	    }
	    else if($holiday == 1)
	    {
		$flag = 0;
		$d = 0;
		while($flag == 0)
		{
		    $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
		    
		    $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
		    $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
		    $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
		    
		    if(count($chkHoliday) > 0 or ($sunday == 1 and $this->isweekend($artwork_date) == 1))
		    {
			$d++;
		    }
		    else
		    {
		       $flag = 1; 
		    }
		}
	    }
	    
	}
	
	return $artwork_date;
    }
   /***************************** Add custom function ***********************************/
   /*function refreshCaptchAction*/
   
   function refreshcpatchaAction(){
	   
	   $words  = "abcdefghijlmnopqrstvwyz";
                                    $vocals = "aeiou";                                
                                    $text  = "";
                                    $vocal = rand(0, 1);
                                    $length = rand(3, 3);
                                    for ($i=0; $i<$length; $i++) {
                                        if ($vocal) {
                                            $text .= substr($vocals, mt_rand(0, 3), 1);
                                        } else {
                                            $text .= substr($words, mt_rand(0, 3), 1);
                                        }
                                        $vocal = !$vocal;                        
						            }
					//	echo $text;			
                        
			Mage::getSingleton("core/session",array('name' => 'frontend'))->setCaptcha($text);
			
			$_data = array('newtext'=>$text);		
			
			echo json_encode($_data);
	   
	   }
   
   ///loading thank yo page ///
   
   function thankyouAction(){
	   
	   $this->loadLayout(); 
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Than you for processing order"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("thankyou", array(
                "label" => $this->__("thankyou"),
                "title" => $this->__("Thank you page")
		   )); 
	   
	   $block = Mage::app()->getLayout()
   				 ->createBlock('core/template')
    			->setData('thankyou','thankyou')
    			->setTemplate('externalform/thankyoupage.phtml');		 
		 $this->getLayout()->getBlock('content')->append($block); 		  
		 $this->renderLayout();
	 
	 
	   }
	   
	 ////function removeitem
	 
	 function removeitemAction(){
		 ///get product id 
		 $item_id = $this->getRequest()->getParam('prodid');
		 $items = Mage::getSingleton('core/session',array('name'=>'frontend'))->getProducts();		 
		 $nitems = array();
		  
		// var_dump($item_id);
		// var_dump($items);		 
		 foreach($items as $item){
			 if($item['product_id']!= $item_id){
				 $nitems[$item['product_id']] = $item;
			 	}
			 }
		  
		  Mage::getSingleton('core/session',array('name'=>'frontend'))->setProducts($nitems);
		  //	var_dump($nitems);   
		 return 1;
		 }  
    
}