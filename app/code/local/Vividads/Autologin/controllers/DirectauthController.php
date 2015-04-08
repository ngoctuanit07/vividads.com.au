<?php
class Vividads_Autologin_DirectauthController extends Mage_Core_Controller_Front_Action {
	public function orderAction()
	{
		// Order Case...
		
		$incrementId = $this->getRequest()->getParam('order_id')?$this->getRequest()->getParam('order_id'):$this->getRequest()->getParam('quote_id');
		 
		if($incrementId){
		
		$hash = $this->getRequest()->getParam('SID');
		$orderModel=Mage::getModel('sales/order')->load($incrementId, 'increment_id');
        $order_id=$orderModel->getEntityId();
		
		if($this->getRequest()->getParam('quote_id') ){
			$orderModel = Mage::getModel('Quotation/Quotation')->load($incrementId, 'increment_id');
					$customerid=$orderModel->getCustomer()->getEntity_id();
  		    		$storeId = $orderModel->getStoreId();
                    $order_id=$orderModel->getQuotation_id();
					$redirect_base_url ='Quotation/Quote/View';
					$redirect_id =  array('quote_id' => $order_id);					 
			}else{
				    $redirect_base_url ='sales/order/view';
					$redirect_id =  array('order_id' => $order_id);	
					$customerid=$orderModel->getCustomerId();
  		    		$storeId=$orderModel->getStoreId();
                    $order_id=$orderModel->getEntityId();
				}
		//$data = array('customer_id'=>$customerid,'store_id'=>$storeId, 'order_id'=>$order_id);
		
		if($order_id){

                    

		   			//$Checkhash=md5('vividexhibits'.$incrementId.$customerid.$storeId.$order_id);
					$Checkhash = md5('vividexhibits'.$incrementId.$customerid);
					
					  //var_dump($Checkhash);exit;
					if($Checkhash == $hash){
		 		       $session = $this->_getSession();
	          		   $customer = Mage::getModel('customer/customer')->load($customerid);
        		       $session->setCustomerAsLoggedIn($customer);
	                   $this->_redirect($redirect_base_url, $redirect_id);
						}
	}else{
	    Mage::getSingleton('customer/session')->addError('Invalid Request');
	    $this->_redirect('');
	}
    }
	   
	   
	   
	}
	
	public function quoteAction()
	{
		// Quotation Case...
	echo    $incrementId = $this->getRequest()->getParam('quote_id');
	 
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
			mysql_query("SET NAMES 'utf8' ");
echo			$sql = "SELECT * FROM quotation WHERE increment_id ='".$incrementId."'  ; " ;
			$result = mysql_query($sql,$con);
			$data_array = array();
			$i=0;
			if($result){
			while ($row = mysql_fetch_assoc($result)) {
		echo		  $data_array[$i]['customer_id']= $row["customer_id"];
		echo "<br>";
		echo  $data_array[$i]['increment_id']= $row["increment_id"];
		echo "<br>";
		echo "Quotation ID is : ". $data_array[$i]['quotation_id']= $row["quotation_id"];
				  
				  $i++;
			/*echo '<pre>';
			print_r($row);
			echo '</pre>';*/
				 
			}
			
		echo  $customerid = $data_array[0]['customer_id'];
		      $quotation_id = $data_array[0]['quotation_id'];
			  
		$session = Mage::getSingleton('customer/session');
		$session->loginById($customerid);
	 
	   $customer = Mage::getModel('customer/customer')->load($customerid);
        $session->setCustomerAsLoggedIn($customer);
	           if(Mage::getSingleton('customer/session')->isLoggedIn()) {
     $customerData = Mage::getSingleton('customer/session')->getCustomer();
      echo $customerData->getId();
 }       
       // $this->_redirect('Quotation/Quote/View', array('quote_id' => $quotation_id)); 
			}else{
				echo "connection Failed.";
				}
		   
	   }
		
	}
    public function _getSession(){
        return Mage::getSingleton('customer/session');
    }
}

