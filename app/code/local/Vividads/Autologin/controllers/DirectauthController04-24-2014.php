<?php
class Vividads_Autologin_DirectauthController extends Mage_Core_Controller_Front_Action {
	public function orderAction()
	{
		// Order Case...
		$incrementId = $this->getRequest()->getParam('order_id');
		if($incrementId){
		
		$hash = $this->getRequest()->getParam('SID');
		$orderModel=Mage::getModel('sales/order')->load($incrementId, 'increment_id');
                $order_id=$orderModel->getEntityId();
		if($order_id){

                    $customerid=$orderModel->getCustomerId();
  		    		$storeId=$orderModel->getStoreId();
                    $order_id=$orderModel->getEntityId();

		   			$Checkhash=md5('Lets have party'.$incrementId.$customerid.$storeId.$order_id);

					if($Checkhash == $hash){
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
			//mysql_query("SET NAMES 'utf8' ");
			$sql = "SELECT customer_id FROM quotation where increment_id = '".$incrementId."'" ;
			$result = mysql_query($sql,$con);
			$data_array = array();
			$i=0;
			while ($row = mysql_fetch_assoc($result)) {
				  $data_array[$i]['customer_id']= $row["customer_id"];
				  
				  $i++;
				 
			}
		  $customerid = $data_array[0]['customer_id'];
		   // end of get customer id
		  $customer = Mage::getModel('customer/customer')->load($customerid);
          $session->setCustomerAsLoggedIn($customer);
		  
	      $this->_redirect('Quotation/Quote/View', array('quote_id' => $order_id)); 
		   
		   
	   }
	}
    public function _getSession(){
        return Mage::getSingleton('customer/session');
    }
}

