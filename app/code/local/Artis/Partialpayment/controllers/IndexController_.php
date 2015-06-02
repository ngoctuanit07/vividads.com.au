<?php
class Artis_Partialpayment_IndexController extends Mage_Core_Controller_Front_Action
{

   
   public function paymentfrontAction(){
	  extract($_REQUEST);
	  
	 if($payment['cc_type']!='' && $payment['cc_number']!='' && $payment['cc_exp_month']!='' && $payment['cc_exp_year']!='' && $payment['cc_cid']!='')
	 {
	  
	  
	    $payemnt_type = $payment['method'];
	    $err ='';
	    $suc = '';
	    if($payemnt_type =='anz_egate'){
		$type = 1;
		$payment =null;
		$param = $_REQUEST;
		$result = Mage::getModel('anz/egate')->_call($type,$payment,$param);
		
		if($result === false) {
		     $err = "There has been an error processing your payment. Please try later or contact us for help.";
		}else {
		// Check if there is a gateway error
		    switch ($result['vpc_TxnResponseCode']) {
			case 0:
			   //$payment->setStatus(self::STATUS_APPROVED)->setLastTransId($result['vpc_TransactionNo']);
			   $suc = $result['vpc_TransactionNo'];
			   break;
			case 1:         /* Unspecified failure */
			    
			   $err = "An error has occurred between our store and our credit card processor.  Please try again. If the error persists, please come back later. Your card has not been charged.";
			   break;
			case 2:         /* Card declined */
			   $err = "The credit card details you provided have been declined by our credit card processor. Please review the payment details you have entered and try again. If the problem persists, please contact your card issuer.";
			   break;
			case 3:         /* Timeout */
			   $err = "A timeout has occurred between our store and our credit card processor.  Please try again. If the error persists, please come back later. Your card has not been charged.";
			   break;
			case 4:         /* Card expired */
			   $err = "The credit card you have entered has expired. Please review the credit card details you have entered and try again. If the problem persists, please contact your card issuer.";
			   break;
			case 5:         /* Insufficient funds */
			   $err = "The credit card you have entered does not have sufficient funds to cover your order amount. Please check your current credit card balance, review the payment details you have entered and try again. If the problem persists, please contact your card issuer.";
			   break;
			default:
			   $err = "An error has occurred whilst attempting to process your payment.  Please review your payment details and try again. If the problem persists, please come back later. Your card has not been charged.";
			   break;
		    }
		}
		
	    }
	    
	    if($suc !=''){
		 $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		 $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		 $order = Mage::getModel('sales/order')->load($orderid);
		 $transactionTable=Mage::getSingleton('core/resource')->getTableName('partial_payment');
		 $orderTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		 $payemnt_type = $payment['method'];
		 
		 if($connectionWrite->isTableExists($transactionTable))
		 {
		     
		     $connectionWrite->beginTransaction();
		     $data = array();
		     $data['orderid']= $orderid;
		     $data['amount']=$amount;
		     if($payment_type != ""){
		      $data['payment_type']=$payment_type;   
		     }
		     $data['received_date']=$date;
		     $data['postdate']=Now();
		     $connectionWrite->insert($transactionTable, $data);
		     $connectionWrite->commit(); 
		     
		 }
		 $sqlPaymentSystem = $connectionRead->select()->from($orderTable, array('*'))->where('entity_id=?', $orderid);
		 try {
		      $chkSystem = $connectionWrite->query($sqlPaymentSystem);
		      $resultsSystem = $chkSystem->fetch();
		 } catch (Exception $e){
		      //echo $e->getMessage();
		 }
		 
		 if($resultsSystem['total_paid'] == 0)
		 $paid = $amount;
		 else
		 $paid = $resultsSystem['total_paid']+$amount;
		 
		 $due = 0.00;
		 $connectionWrite->beginTransaction();
		 $data = array();
		 $data['base_total_due'] = $due;
		 $data['total_due'] = $due;
		 $data['base_total_paid'] = $paid;
		 $data['total_paid'] = $paid;
		 $data2['state'] = 'processing';
		 $data2['status'] = 'paid';
		 $where = $connectionWrite->quoteInto('entity_id =?', $orderid);
		 $connectionWrite->update($orderTable, $data, $where);
		 $connectionWrite->commit();
		 
		 $su = 'Payment has been saved successfully.';
		 Mage::getSingleton('core/session')->addSuccess($this->__('Payment has been saved successfully.'));
		 //$this->_getSession()->addSuccess($this->__('Payment has been saved successfully.'));
	    }else{
      		 Mage::getSingleton('core/session')->addError($err);
		 //$this->_getSession()->addError($err);
	    }
	 }else{
	    Mage::getSingleton('core/session')->addError('Please enter value to all mandatory field.');
	    
	 }
	 //session_write_close();
	 if($err != ''){
	    $msg = 'e_'.$err;
	 }else{
	    $msg = 's_'.$su;
	 }
	 
	 $this->_redirect('sales/order/view/order_id/'.$orderid.'/msg/'.$msg);
	 //$this->_redirect('sales/order/history/');
	 
   }
	 //echo $err; exit; 
}
   

?>