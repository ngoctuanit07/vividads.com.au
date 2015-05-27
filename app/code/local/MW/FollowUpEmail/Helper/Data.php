<?php



class MW_FollowUpEmail_Helper_Data extends Mage_Core_Helper_Abstract

{

	const MYCONFIG = "followupemail/config/enabled";
	
	public static function getOrderAddress($order, $addressType)

    {

        $addresses = Mage::getResourceModel('sales/order_address_collection')

            ->addAttributeToSelect('*')

            ->addAttributeToFilter('parent_id', $order->getId());



        if ($order->getId()) foreach ($addresses as $address) $address->setOrder($order);



        foreach ($addresses as $address)

            if ($addressType == $address->getAddressType() && !$address->isDeleted())

                return $address;



        return false;

    }

	

	public function renderItemsOrder($order){

		$bc = Mage::getSingleton('core/layout');        

		$block = $bc->createBlock('followupemail/itemsorder');

		$block->setOrder($order);

		return $block->renderView();

	}

	

	public function renderItemsproductorder($order){

		$bc = Mage::getSingleton('core/layout');

        //$dungdk   = $bc->createBlock('sales/order_items')->append($freeGift, 'freegiftbox');			

		$block = $bc->createBlock('followupemail/itemsproductorder');

		$block->setOrder($order);

		return $block->renderView();

		//Mage::log(get_class($block));

		/*$block = $bc->createBlock('sales/order_items')->assign('order', $order)->addItemRender('simple', 'sales/order_item_renderer_default', 'sales/order/items/renderer/default.phtml')->setTemplate('sales/order/items.phtml'); */

		//Mage::log($block->renderHtml();

	}

	

	public function renderProductNames($products){

		$count = count($products);		

		$i = 0;

		$html = "";

		if(is_array($products)){

			foreach($products as $product){

				$i++;

				$obj = Mage::getModel('catalog/product');

				$_product = $obj->load($product);

				//$url = $_product->getProductUrl();	

				$url = Mage::getUrl($_product->getUrlPath());

				if($i == $count-1){

					$html .= '<a href="'.$url.'">'.$_product->getName().'</a>'." and ";

				}							

				else if($i == $count){

					$html .= '<a href="'.$url.'">'.$_product->getName().'</a>';	

				}

				else{

					$html .= '<a href="'.$url.'">'.$_product->getName().'</a>'." , ";

				}

				//$html .= '<a href="'.$url.'">'.$_product->getName().'</a>';				

			}

		}

		return $html;

	}

	

	public function renderProductReviews($products){

		$count = count($products);		

		$i = 0;

		$html = "";

		if(is_array($products)){

			foreach($products as $product){

				$i++;

				$obj = Mage::getModel('catalog/product');

				$_product = $obj->load($product);				

				$urlProduct = Mage::getUrl($_product->getUrlPath());

				$url = $_product->getConnectUrl();

				if($i == $count-1){

					if($url == "")

					$html .= '<a href="'.$urlProduct.'">'.$_product->getName().'</a>'." and ";

					else

					$html .= '<a href="'.$url.'">'.$_product->getName().'</a>'." and ";

				}							

				else if($i == $count){

					if($url == "")

					$html .= '<a href="'.$urlProduct.'">'.$_product->getName().'</a>';	

					else

					$html .= '<a href="'.$url.'">'.$_product->getName().'</a>';	

				}

				else{

					if($url == "")

					$html .= '<a href="'.$urlProduct.'">'.$_product->getName().'</a>'." , ";

					else

					$html .= '<a href="'.$url.'">'.$_product->getName().'</a>'." , ";

				}

				//$html .= '<a href="'.$url.'">'.$_product->getName().'</a>';				

			}

		}

		return $html;

	}

	

	public function renderItemsproductcart($items){

		$bc = Mage::getSingleton('core/layout');

        //$dungdk   = $bc->createBlock('sales/order_items')->append($freeGift, 'freegiftbox');			

		$block = $bc->createBlock('followupemail/itemsproductcart');

		$block->setItems($items);

		return $block->renderView();

	}

	

	public function renderItemscart($items,$cartSubtotal,$cartsubtotal_with_discount,$cartgrand_total){

		$bc = Mage::getSingleton('core/layout');

		$discount = 0;

		if(($cartSubtotal - $cartsubtotal_with_discount) > 0) $discount = $cartSubtotal - $cartsubtotal_with_discount;        	

		$block = $bc->createBlock('followupemail/itemscart');

		$block->setItems($items);

		$block->setSubtotal($cartSubtotal);

		$block->setDiscount($discount);

		$block->setGrandTotal($cartgrand_total);

		return $block->renderView();

	}

	

	public static function explodeEmailList($emails)

    {

        if (!$emails) return array();

        $emails = trim(str_replace(array(',', ';'), ' ', $emails));

        do {

            $emails = str_replace('  ', ' ', $emails);

        } while (strpos($emails, '  ') !== false);

        $result = explode(' ', $emails);

        return $result;

    }



	public static function encryptCode($email,$page,$orderId)

    {

		$code = $email.",".$page.",".$orderId;		

        return Mage::helper('core')->encrypt($code);

    }



    public static function decryptCode($code)

    {

        return Mage::helper('core')->decrypt($code);

    }

	

    public static function getCodeSecurity()

    {

        return md5(mt_rand());

    }

	public function getCustomerByEmail($email){
		$webId = Mage::app()->getWebsite()->getId();
		if($webId == 0) $webId = 1; 
		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId($webId);
		$customer->loadByEmail($email); //load customer by email id	;
		return $customer;
	}
	
	public function _prepareSubjectEmail($params,$_subject){
			$subject = $_subject;
			$store = Mage::getModel('core/store')->load($params['storeId']);				
			$subject = str_replace("{{var store.getFrontendName()}}", $store->getFrontendName(), $subject);			
			return $subject;
	}

	public function _prepareContentEmail($params){		
		$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($params['templateEmailId'],$params['senderInfo']);
        $content = $emailTemplate['content'];	

		$store = Mage::getModel('core/store')->load($params['storeId']);
		$content = str_replace("{{var store.getFrontendName()}}", $store->getFrontendName(), $content);
		$directLink = "";
		$directLinkCart = "";
		$arrCode = explode(',',$this->decryptCode($params['code']));	
		if(is_array($arrCode)){
			$customer = $this->getCustomerByEmail($arrCode[0]);
			if($customer->getId()){
				if($arrCode[1] == 'order'){
					$directLink = $store->getUrl('followupemail/index/direct', array('code' => str_replace('/','special',$params['code'])));	
				}	
				else if($arrCode[1] == 'cart'){
					$directLinkCart = $store->getUrl('followupemail/index/direct', array('code' => str_replace('/','special',$params['code'])));
				}
				else{
					$directLink = "";
					$directLinkCart = "";
				}
			}
			else{
				$directLink = "";
				$directLinkCart = "";
			}
			
		}
		if(!isset($arrCode[1])){
			$directLink = $store->getUrl('followupemail/index/direct', array('code' => $params['code']));			
			$directLinkCart = $store->getUrl('followupemail/index/direct', array('code' => $params['code']));
		}		
		if(isset($params['codeCart'])){
			$arrCodeCart = explode(',',$this->decryptCode($params['codeCart']));
			if(is_array($arrCodeCart)){
				$customer = $this->getCustomerByEmail($arrCodeCart[0]);
				if($customer->getId()){					
					if($arrCodeCart[1] == 'cart'){
						$directLinkCart = $store->getUrl('followupemail/index/direct', array('code' => str_replace('/','special',$params['codeCart'])));
					}
					else{						
						$directLinkCart = "";
					}
				}
				else{					
					$directLinkCart = "";
				}
				
			}
		}				

		$productNamesOrder = "";

		$productReviewsOrder = "";							   		

		$productNamesCart = "";

		$productReviewsCart = "";

		if($params['productIds'] != null && $params['orderId'] != ""){

			$productReviewsOrder = $this->renderProductReviews($params['productIds']);

			$productNamesOrder = $this->renderProductNames($params['productIds']);

		}		
		//Variables Order

		$order_id = $params['orderId'];				

		$itemsOrder= "";

		$itemsProductOrder = "";

		$orderCreateAtDate = "";

		$orderBillingAddress = "";

		$orderShippingAddress = "";

		$orderShippingMethod = "";		

		$orderPaymentMethod = "";		

		$orderStatus = "";		

		$orderSubtotal = "";

		$orderNumber = "";		

		$orderQty = "";		

		/*{{layout handle="sales_email_order_items" order=$order}}*/

		if($order_id != ""){

			$order = Mage::getModel('sales/order')->load($order_id);			
			if($order->getData() != null){
				$itemsOrder = $this->renderItemsOrder($order);			
				$itemsProductOrder = $this->renderItemsproductorder($order);		
				$payment = $order->getPayment();		

				$orderCreateAtDate = $order->getCreatedAtDate();

				$orderBillingAddress = $order->getBillingAddress()->format('html');

				$orderShippingAddress = $order->getShippingAddress()->format('html');

				$orderShippingMethod = $order->getShippingDescription();

				if($payment != null)	

				$orderPaymentMethod = $payment->getMethodInstance()->getTitle();

				

				$orderStatus = $order->getStatus();

				$orderSubtotal = $order->getGrandTotal();

				$orderNumber = $order->getIncrementId();

				$orderQty = $order->getTotalQtyOrdered();		
			}
		
		}		

		if(strpos($content, "{{var order.products}}"))

    	$content = str_replace("{{var order.products}}", $itemsProductOrder, $content); 

			

		if(strpos($content, "{{var order.createAt}}"))

        	$content = str_replace("{{var order.createAt}}", $orderCreateAtDate, $content);	

		

		if(strpos($content, "{{var order.billing_address}}"))

        	$content = str_replace("{{var order.billing_address}}", $orderBillingAddress, $content);	        	        			      

		

		if(strpos($content, "{{var order.shipping_address}}"))

        	$content = str_replace("{{var order.shipping_address}}", $orderShippingAddress, $content);

					

		if(strpos($content, "{{var order.shipping_method}}"))

        	$content = str_replace("{{var order.shipping_method}}", $orderShippingMethod, $content);

			

		if(strpos($content, "{{var order.direct_link}}"))

        	$content = str_replace("{{var order.direct_link}}", $directLink, $content);	

						

		if(strpos($content, "{{var order.payment_method}}"))

    		$content = str_replace("{{var order.payment_method}}", $orderPaymentMethod, $content);		

		

        if(strpos($content, "{{var order.status}}"))

        	$content = str_replace("{{var order.status}}", $orderStatus, $content);	

		

		if(strpos($content, "{{var order.subtotal}}"))

        	$content = str_replace("{{var order.subtotal}}", $orderSubtotal, $content);

			

		if(strpos($content, "{{var order.order_number}}"))

        	$content = str_replace("{{var order.order_number}}", $orderNumber, $content);

			

		if(strpos($content, "{{var order.total_qty}}"))

        	$content = str_replace("{{var order.total_qty}}", $orderQty, $content);

			

		if(strpos($content, "{{var order.product_names}}"))

        	$content = str_replace("{{var order.product_names}}", $productNamesOrder, $content);	

			

		if(strpos($content, "{{var order.product_reviews}}"))

        	$content = str_replace("{{var order.product_reviews}}", $productReviewsOrder, $content);		

				    

		if(strpos($content, "{{var order.items}}"))

        	$content = str_replace("{{var order.items}}", $itemsOrder, $content);	

		// Variables Cart

		$cart = $params['cart'];

		$itemProductCart = "";

		$cartUpdateAt = "";

		$cartSubtotal = "";

		$cartItemQty = "";	

		$itemscart = ""	;

		if($cart != ""){

			$items = explode(',', $cart['item_ids']);	

	   		$itemProductCart = $this->renderItemsproductcart($items);	   		

			$cartUpdateAt = $cart['updated_at'];
			
			$productIds = explode(',', $cart['product_ids']); 
			
			$productReviewsCart = $this->renderProductReviews($productIds);

			$productNamesCart = $this->renderProductNames($productIds);

			$cartSubtotal = $cart['subtotal'];

			$cartsubtotal_with_discount = $cart['subtotal_with_discount'];

			$cartgrand_total = $cart['grand_total'];

			$itemscart = $this->renderItemscart($items,$cartSubtotal,$cartsubtotal_with_discount,$cartgrand_total);

			$cartItemQty = $cart['items_qty'];			

		}				
	/*	mage::log($itemscart);
		mage::log($itemsOrder);*/
        if(strpos($content, "{{var cart.products}}"))

        	$content = str_replace("{{var cart.products}}", $itemProductCart, $content);        	       

				

		if(strpos($content, "{{var cart.update_at}}"))

        	$content = str_replace("{{var cart.update_at}}", $cartUpdateAt, $content);	        	        

			

		if(strpos($content, "{{var cart.subtotal}}"))

        	$content = str_replace("{{var cart.subtotal}}", $cartSubtotal, $content);

			

		if(strpos($content, "{{var cart.items_qty}}"))

        	$content = str_replace("{{var cart.items_qty}}", $cartItemQty, $content);

			

		if(strpos($content, "{{var cart.direct_link}}")){
			
			$content = str_replace("{{var cart.direct_link}}", $directLinkCart, $content);							
		}
        	

			

		if(strpos($content, "{{var cart.product_names}}"))

        	$content = str_replace("{{var cart.product_names}}", $productNamesCart, $content);
			
		if(strpos($content, "{{var cart.product_reviews}}"))

        	$content = str_replace("{{var cart.product_reviews}}", $productReviewsCart, $content);

			

		if(strpos($content, "{{var cart.items}}"))

        	$content = str_replace("{{var cart.items}}", $itemscart, $content);

			

		// Variables Customer

		

		$cData = $params['data'];

		$cFullName = "";

		$cEmail = "";

		$cLastName = "";

		$cFirstName = "";

		$cDefaultAddress = "";

		$cCity = "";

		$cState = "";

		$cZipCode = "";

		$cCountry = "";

		if($params['customerId'] != ""){

			$customerInfo = Mage::getModel('followupemail/observer')->_getCustomer($params['customerId'],null);

			if($customerInfo != null){

				$cFullName = $customerInfo['customer_name'];

				$cEmail = $customerInfo['customer_email'];

				$cLastName = $customerInfo['last_name'];

				$cFirstName = $customerInfo['first_name'];

				$cDefaultAddress = $customerInfo['default_address'];

				$cCity = $customerInfo['city'];

				$cState = $customerInfo['state'];

				$cZipCode = $customerInfo['zip_code'];

				$cCountry = $customerInfo['country'];	

			}			

		}

		if($cData != ""){

			$cFullName = $cData['customer_name'];

			$cEmail = $cData['customer_email'];

			$cLastName = $cData['customer_lastName'];

			$cFirstName = $cData['customer_firstName'];			

		}

		if(strpos($content, "{{var customer.full_name}}"))

        	$content = str_replace("{{var customer.full_name}}", $cFullName, $content);

			

		if(strpos($content, "{{var customer.name}}"))

        	$content = str_replace("{{var customer.name}}", $cFullName, $content);

        

		if(strpos($content, "{{var customer.email}}"))

        	$content = str_replace("{{var customer.email}}", $cEmail, $content);

			

		if(strpos($content, "{{var customer.last_name}}"))

        	$content = str_replace("{{var customer.last_name}}", $cLastName, $content);

			

		if(strpos($content, "{{var customer.first_name}}"))

        	$content = str_replace("{{var customer.first_name}}", $cFirstName, $content);

		

		if(strpos($content, "{{var customer.default_address}}"))

        	$content = str_replace("{{var customer.default_address}}", $cDefaultAddress, $content);

			

		if(strpos($content, "{{var customer.city}}"))

        	$content = str_replace("{{var customer.city}}", $cCity, $content);

			

		if(strpos($content, "{{var customer.state}}"))

        	$content = str_replace("{{var customer.state}}", $cState, $content);

			

		if(strpos($content, "{{var customer.zip_code}}"))

        	$content = str_replace("{{var customer.zip_code}}", $cZipCode, $content);

			

		if(strpos($content, "{{var customer.country}}"))

        	$content = str_replace("{{var customer.country}}", $cCountry, $content);

		     

		// 	Variables Product

		$productName = "";

		$sku = "";

		if($cData != ""){

			if($cData['sku'] != ""){

				$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$cData['sku']);			

				$productName = $product->getName();				

				$sku = $cData['sku'];

			}				

		}		

		if(strpos($content, "{{var product.name}}"))

        	$content = str_replace("{{var product.name}}", $productName, $content);

			

		if(strpos($content, "{{var product.sku}}"))

        	$content = str_replace("{{var product.sku}}", $sku, $content);

				

		$html = "<style type=\"text/css\">\n%s\n</style>\n%s";

		if($emailTemplate['template_styles'] == "")

		return $content;

		else

        return sprintf($html, $emailTemplate['template_styles'], $content);

	}
	
	public function myConfig(){
    	return self::MYCONFIG;
    }

	const MYNAME = "MW_FollowUpEmail";
	
	function disableConfig()
	{
			Mage::getSingleton('core/config')->saveConfig($this->myConfig(),0); 			
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,1);	
			 Mage::getConfig()->reinit();
	}

}