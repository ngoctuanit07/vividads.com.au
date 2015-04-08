<?php
class Vividads_Autologin_MyauthController extends Mage_Core_Controller_Front_Action {
	
	public function orderAction()
	{
		// Order Case...
		$incrementId = $this->getRequest()->getParam('order_id');
		//  $incrementId;
		
		if($incrementId){
		
		$hash = $this->getRequest()->getParam('SID');
		$orderModel=Mage::getModel('sales/order')->load($incrementId, 'increment_id');
        $order_id=$orderModel->getEntityId();		
		
		if($order_id){

			 $customerid=$orderModel->getCustomerId();			 	 
			 $storeId=$orderModel->getStoreId();				 
			 $order_id=$orderModel->getEntityId();			 
			 $customerHash = $hash = md5('vividads Melbourne Australia'.$incrementId.$customerid.$storeId.$order_id);
			 if($hash == $customerHash){		   			 
				 $session = $this->_getSession();
				 $customer = Mage::getModel('customer/customer')->load($customerid);
				 $session->setCustomerAsLoggedIn($customer);
				 $this->_redirect('sales/order/view', array('order_id' => $order_id));
			 }else{
				 
				 Mage::getSingleton('customer/session')->addError('Invalid Request');
						$this->_redirect('');
				 }
					}else{
						Mage::getSingleton('customer/session')->addError('Invalid Request');
						$this->_redirect('');
					}
   			 }
	   
	   
	   
	}
	
	
	public function proofAction()
	{
		// Order Case...
		$incrementId = $this->getRequest()->getParam('order_id');
		$orderModel=Mage::getModel('sales/order')->loadByIncrementId($incrementId);
		//print_r($orderModel);

  
		
		
		if($incrementId){
		
       	$orderModel=Mage::getModel('sales/order')->load($incrementId, 'increment_id');
        $order_id=$orderModel->getEntityId();


		if($order_id){

                     $customerid=$orderModel->getCustomerId();

		    	 	$storeId=$orderModel->getStoreId();

                  $order_id=$orderModel->getEntityId();


		   			 
		 		       $session = $this->_getSession();
	          		   $customer = Mage::getModel('customer/customer')->load($customerid);

				   
        		       $session->setCustomerAsLoggedIn($customer);
	                   $this->_redirect('sales/order/view', array('order_id' => $order_id));
					 
	}else{
	    Mage::getSingleton('customer/session')->addError('Invalid Request');
	    $this->_redirect('');
	}
    }
	   
	   
	   
	}
	
	public function orderUpdateAction(){
		
		// Order Case...
		$incrementId = $this->getRequest()->getParam('order_id');
		  $incrementId;
		 
		if($incrementId){
		
		$customer_pass = $this->getRequest()->getParam('SID');
		$orderModel=Mage::getModel('sales/order')->load($incrementId, 'increment_id');
		$customer_email = $orderModel->getCustomerEmail();
        $order_id=$orderModel->getEntityId();
		if($order_id){
			 $authenticated = $session->login($customer_email, $customer_pass);
		 if($authenticated){

                    $customerid=$orderModel->getCustomerId();
			 	 	$customer = Mage::getSingleton('customer/session')->getCustomer();
  		    	 	$storeId=$orderModel->getStoreId();
				 
                     $order_id=$orderModel->getEntityId();
 
		   			 
		 		       $session = $this->_getSession();
	          		   $customer = Mage::getModel('customer/customer')->load($customerid);
        		       $session->setCustomerAsLoggedIn($customer);
	                   $this->_redirect('sales/order/view', array('order_id' => $order_id));
		 }
					 
	}else{
	    Mage::getSingleton('customer/session')->addError('Invalid Request');
	    $this->_redirect('');
	}
    }
	   
	 
		
	}
	
	public function emailAction()
	{
		// Quotation Case...
		$customer_email = $this->getRequest()->getParam('email_id');
		$customer_pass = $this->getRequest()->getParam('psd');
		$customer = Mage::getModel("customer/customer"); 
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
		$customer->loadByEmail($customer_email); 
		$customerid = $customer->getId(); 
		$session = $this->_getSession();
		
		 $authenticated = $session->login($customer_email, $customer_pass);
		 if($authenticated){
					  $customer = Mage::getModel('customer/customer')->load($customerid);
					  $session->setCustomerAsLoggedIn($customer);
					  $myStatus = Mage::getSingleton('customer/session')->isLoggedIn();
					if($myStatus):  
		//echo "You are logged in....";
		$this->_redirect('customer/account/');
		die();
  else: 
  
$this->_redirect('customer/account/');
die();
 
  endif;  
 }
 

 
	 
	    
		
	}
	
	public function quoteAction()
	{
		// Quotation Case...
	     $incrementId = $this->getRequest()->getParam('quote_id');
		 $sid = $this->getRequest()->getParam('SID');

		 
	    if($incrementId){
		   // get customer id
		    $xml = simplexml_load_file('app/etc/local.xml');
			$host = $xml->global->resources->default_setup->connection->host;
	 		$username = $xml->global->resources->default_setup->connection->username;
	 		$password = $xml->global->resources->default_setup->connection->password;
	 		$dbname = $xml->global->resources->default_setup->connection->dbname;
			
			$con = mysql_connect($host, $username, $password);

			if (!$con)
			  {
			  die('Could not connect: ' . mysql_error());
			  }
           			
			$db_selected = mysql_select_db($dbname,$con);
		//	mysql_query("SET NAMES 'utf8' ");
 			$sql = "SELECT * FROM quotation WHERE increment_id ='".$incrementId."'  ; " ;
			$result = mysql_query($sql,$con);
			$rec = mysql_fetch_assoc($result);
			$i=0;
			if($rec){
					$data_array[$i]['customer_id']= $rec['customer_id'];
						//echo "<br>";
					$data_array[$i]['increment_id']= $rec["increment_id"];
						//echo "<br>";
					$data_array[$i]['quotation_id']= $rec["quotation_id"];
					$customerid = $data_array[0]['customer_id'];
				 	$hash =md5('vividexhibits'.$rec["increment_id"].$customerid);					
					if($sid==$hash){
						$hashOk = 1;
						}
					$quotation_id = $data_array[0]['quotation_id'];
			
					$session = $this->_getSession();
					$customer = Mage::getModel('customer/customer')->load($customerid);
					$session->setCustomerAsLoggedIn($customer);
					$myStatus = Mage::getSingleton('customer/session')->isLoggedIn();
/*echo $sid;
echo "<br>";
echo $hash;
echo "Hash is".$hashOk;
echo "my status is ".$myStatus;
exit;*/

					if($myStatus):  
						$this->_redirect('Quotation/Quote/View', array('quote_id' => $quotation_id)); 
						die();
					else: 
						Mage::getSingleton('customer/session')->addError('Invalid Request');
						$this->_redirect('');
					
					endif;  
			
			}else{
				
				
		 	$sqlOrder = "SELECT  entity_id,increment_id,customer_id from sales_flat_order WHERE increment_id ='".$incrementId."'  ; " ;
		 		$resultOrder = mysql_query($sqlOrder,$con);	
			$j=0;
				while ($row = mysql_fetch_assoc($resultOrder)) {
						$data_array[$j]['customer_id']= $row["customer_id"];
						$data_array[$j]['increment_id']= $row["increment_id"];
						$data_array[$j]['entity_id']= $row["entity_id"];
						$j++;
					}
					$customerid = $data_array[0]['customer_id'];
					$orderId = $data_array[0]['entity_id'];
					$quotation_id = $data_array[0]['quote_id'];
					$session = $this->_getSession();
					$customer = Mage::getModel('customer/customer')->load($customerid);
					$session->setCustomerAsLoggedIn($customer);
					$myStatus = Mage::getSingleton('customer/session')->isLoggedIn();
				    	if($myStatus):  
							$this->_redirect('sales/order/view', array('order_id' => $orderId));
							die();
						else: 
						 	Mage::getSingleton('customer/session')->addError('Invalid Request');
							$this->_redirect('');
						  
					    endif;

					}
			
	  }
		
	}
	
	public function quoteTestAction()
	{
		// Quotation Case...
	     $incrementId = $this->getRequest()->getParam('quote_id');
	 
	    if($incrementId){
		   // get customer id
		    $xml = simplexml_load_file('app/etc/local.xml');
			$host = $xml->global->resources->default_setup->connection->host;
	 		$username = $xml->global->resources->default_setup->connection->username;
	 		$password = $xml->global->resources->default_setup->connection->password;
	 		$dbname = $xml->global->resources->default_setup->connection->dbname;
			
			$con = mysql_connect($host, $username, $password);

			if (!$con)
			  {
			  die('Could not connect: ' . mysql_error());
			  }
           			
			$db_selected = mysql_select_db($dbname,$con);
		//	mysql_query("SET NAMES 'utf8' ");
 			$sql = "SELECT * FROM quotation WHERE increment_id ='".$incrementId."'  ; " ;
			$result = mysql_query($sql,$con);
			$rec = mysql_fetch_assoc($result);
			$i=0;
			echo '<pre>';
			print_r($rec);
			echo '</pre>';

			
			if($rec){
					$data_array[$i]['customer_id']= $rec['customer_id'];
						//echo "<br>";
					$data_array[$i]['increment_id']= $rec["increment_id"];
						//echo "<br>";
					$data_array[$i]['quotation_id']= $rec["quotation_id"];
						
				 
					$customerid = $data_array[0]['customer_id'];
					
					$quotation_id = $data_array[0]['quotation_id'];
			
					$session = $this->_getSession();
					$customer = Mage::getModel('customer/customer')->load($customerid);
					echo '<pre>';
			print_r($customer);
			echo '</pre>';

			exit;				
					
					
					$session->setCustomerAsLoggedIn($customer);
					$myStatus = Mage::getSingleton('customer/session')->isLoggedIn();
					if($myStatus):  
						$this->_redirect('Quotation/Quote/View', array('quote_id' => $quotation_id)); 
						die();
					else: 
						Mage::getSingleton('customer/session')->addError('Invalid Request');
						$this->_redirect('');
					
					endif;  
			
			}else{
				
		 	$sqlOrder = "SELECT  entity_id,increment_id,customer_id from sales_flat_order WHERE increment_id ='".$incrementId."'  ; " ;
		 		$resultOrder = mysql_query($sqlOrder,$con);	
			$j=0;
				while ($row = mysql_fetch_assoc($resultOrder)) {
						$data_array[$j]['customer_id']= $row["customer_id"];
						$data_array[$j]['increment_id']= $row["increment_id"];
						$data_array[$j]['entity_id']= $row["entity_id"];
						$j++;
					}
					$customerid = $data_array[0]['customer_id'];
					$orderId = $data_array[0]['entity_id'];
					$quotation_id = $data_array[0]['quote_id'];
					$session = $this->_getSession();
					$customer = Mage::getModel('customer/customer')->load($customerid);
					$session->setCustomerAsLoggedIn($customer);
					$myStatus = Mage::getSingleton('customer/session')->isLoggedIn();
				    	if($myStatus):  
							$this->_redirect('sales/order/view', array('order_id' => $orderId));
							die();
						else: 
						 	Mage::getSingleton('customer/session')->addError('Invalid Request');
							$this->_redirect('');
						  
					    endif;

					}
					
			
					
			
	  }
		
	}
	
	
    public function _getSession(){
        return Mage::getSingleton('customer/session');
    }
}

