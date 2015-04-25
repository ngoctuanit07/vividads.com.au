<?php


class MDN_Quotation_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Set buttons
     *
     */
    public function __construct() {
        parent::__construct();
	
	$this->_removeButton('duplicate');
        $this->_objectId = 'id';
        $this->_controller = 'Adminhtml';
        $this->_blockGroup = 'Quotation';
        $quoteId = $this->getRequest()->getParam('quote_id');
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
		
		///checking before convert to invoice ///
		
		$validates = $this->validateQuote($quote);
		$validate_txt = '';
		
		if( count($validates) > 0 ){
			foreach($validates as $validate){
				$validate_txt .= $validate.'\r\n';
				}
			}
		//echo $validate_txt;
		
		

 $this->_addButton(
                'delete',
                array(
                    'label' => Mage::helper('quotation')->__('Delete'),
                    'class' => 'delete',
                    'onclick' => " if (confirm('".$this->__('Are you sure you want to delete this Quote ?')."')) { window.location.href='" . $this->getUrl('Quotation/Admin/delete', array('quote_id' => $this->getRequest()->getParam('quote_id'))) . "' }"
                )
        );


if($quote->getisInvoice() == false){
        
		
		
		
		$this->_addButton(
                'convert_to_invoice',
                array(
                    'label' => Mage::helper('quotation')->__('Convert to Order'),
                    'onclick' => " validate='$validate_txt'; if(validate !=''){alert(validate);}else{ if (confirm('".$this->__('Are you sure ?')."')) { window.location.href='" . $this->getUrl('Quotation/Admin/createorder', array('quote_id' => $this->getRequest()->getParam('quote_id'))) . "' }}"
                )
        );
}
        $this->_addButton(
                'notify_customer',
                array(
                    'label' => Mage::helper('quotation')->__('Notify Customer'),
                    'onclick' => " validate='$validate_txt'; if(validate !=''){alert(validate);}else{ if(document.getElementById('myform[status]').value == 'active'){ if( !confirm('You are about to notify to client. Are you sure ?')){ return false; }else{ window.location.href='" . $this->getUrl('Quotation/Admin/notify', array('quote_id' => $this->getRequest()->getParam('quote_id'))) . "';} }else{ alert('Warning! Status of the Quote is not active, Please put the status to active...'); }}"
                )
        );

 

$this->_addButton(
                'previewemail',
                array(
                    'label' => Mage::helper('quotation')->__('Preview Email'),
                    'onclick' => " validate='$validate_txt'; if(validate !=''){alert(validate);}else{ email_preview(".$this->getRequest()->getParam('quote_id').");}",
                )
        );

/*
        $this->_addButton(
                'remind_customer',
                array(
                    'label' => Mage::helper('quotation')->__('Remind'),
                    'onclick' => "window.location.href='" . $this->getUrl('Quotation/Admin/RemindCustomer', array('quote_id' => $this->getRequest()->getParam('quote_id'))) . "'"
                )
        );
*/
        $this->_addButton(
                'print',
                array(
                    'label' => Mage::helper('quotation')->__('Print'),
                    'class' => 'print',
                    'onclick' => "validate='$validate_txt'; if(validate !=''){alert(validate);}else{ window.location.href='" . $this->getUrl('Quotation/Admin/print', array('quote_id' => $this->getRequest()->getParam('quote_id'))) . "'}"
                )
        );
        //
        //$this->_addButton(
        //        'duplicate',
        //        array(
        //            'label' => Mage::helper('quotation')->__('Duplicate'),
        //            'class' => 'add',
        //            'onclick' => "window.location.href='" . $this->getUrl('Quotation/Admin/Duplicate', array('quotation_id' => $this->getRequest()->getParam('quote_id'))) . "'"
        //        )
        //);
         
        
        //$this->_addButton(
        //        'createorder',
        //        array(
        //            'label' => Mage::helper('quotation')->__('Get Order'),
        //            'onclick' => "if (confirm('".$this->__('Are you sure ?')."')) { window.location.href='" . $this->getUrl('Quotation/Admin/createorder', array('quote_id' => $this->getRequest()->getParam('quote_id'))) . "' }"
        //        )
        //);

       

        $this->_updateButton('save', 'onclick', 'beforeSaveQuote()');

    }

    /**
     * main title
     *
     * @return unknown
     */
    public function getHeaderText() {
        return Mage::helper('quotation')->__('Edit Quote %s', $this->getQuote()->getincrement_id());
    }

    /**
     * Return back url
     */
    public function GetBackUrl() {
        return $this->getUrl('Quotation/Admin/List', array());
    }

    /**
     * Return current quote
     */
    public function getQuote()
    {
        return Mage::registry('current_quote');
    }
	
	
	/**
	* function validateQuote()
	/* Return validated quote like products added, shipping, billing address, elivery date added etc
	*/
	
	public function validateQuote($quote=null){
		
		///if quote is null then it will return false;
		$message = array();
		if($quote==null){ 
			array_push($message,'Erro: No quote is added');
			return $message;
		}
			$quote_data = $quote->getData();
			$customer = Mage::getModel('customer/customer')->load($quote_data['customer_id']);
			$default_billing_address = $customer->getDefaultBillingAddress();
			$default_shipping_address = $customer->getDefaultShippingAddress();
			
			//zend_debug::dump($customer);  
			
			//Zend_debug::dump($quote_data);
			
			if(count($quote->getItems())<= 0){
				array_push($message,'Oops no product(s) are added, Please enter at least one product and save quote.');
				return  $message;
				}
			
						
			
			if($quote_data['shipping_method']==''){
				array_push($message,'Oops! No Shipping Method is selected, please select a proper shipping method and save quote.');
				return  $message;
				
				}
			
			
			if(!$default_billing_address){
				array_push($message,'Oops! No billing address added, please add a proper billing address and save quote.');
				return  $message;
				
				}
				
			if(!$default_shipping_address){
				array_push($message,'Oops! No shipping address added, please add a proper shipping address and save quote.');
				return  $message;
				}
			
			if($quote_data['status']=='new'){
				array_push($message,'Oops! quote is not Active, please first activate it and save quote.');
				return  $message;
				
				}			
					
					
		}

}
