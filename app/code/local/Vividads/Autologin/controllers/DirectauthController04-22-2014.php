<?php
class Vividads_Autologin_DirectauthController extends Mage_Core_Controller_Front_Action {
	public function orderAction()
	{
		$incrementId = $this->getRequest()->getParam('order_id');
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
    public function _getSession(){
        return Mage::getSingleton('customer/session');
    }
}

