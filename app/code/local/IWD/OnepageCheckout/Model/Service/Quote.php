<?php
class IWD_OnepageCheckout_Model_Service_Quote extends Mage_Sales_Model_Service_Quote
{
    protected function _validate()
    {
        $helper = Mage::helper('onepagecheckout');
        if (!$this->getQuote()->isVirtual())
        {
            $address = $this->getQuote()->getShippingAddress();
            $addrValidator = Mage::getSingleton('onepagecheckout/type_geo')->validateAddress($address);
            if ($addrValidator !== true)
                Mage::throwException($helper->__('Please check shipping address information. %s', implode(' ', $addrValidator)));

            $ship_method = $address->getShippingMethod();
            $rate = $address->getShippingRateByCode($ship_method);
            if (!$this->getQuote()->isVirtual() && (!$ship_method || !$rate))
                Mage::throwException($helper->__('Please specify a shipping method.'));
        }

        $addrValidator = Mage::getSingleton('onepagecheckout/type_geo')->validateAddress($this->getQuote()->getBillingAddress());

        if ($addrValidator !== true)
            Mage::throwException($helper->__('Please check billing address information. %s', implode(' ', $addrValidator)));

        if (!($this->getQuote()->getPayment()->getMethod()))
			Mage::throwException($helper->__('Please select a valid payment method.'));

        return $this;
    }
	
	
	/**
     * Submit all available items
     * All created items will be set to the object
     */
    public function submitAll()
    {
        // don't allow submitNominalItems() to inactivate quote
        $shouldInactivateQuoteOld = $this->_shouldInactivateQuote;
        $this->_shouldInactivateQuote = false;
        try {
            $this->submitNominalItems();
            $this->_shouldInactivateQuote = $shouldInactivateQuoteOld;
        } catch (Exception $e) {
            $this->_shouldInactivateQuote = $shouldInactivateQuoteOld;
            throw $e;
        }
        // no need to submit the order if there are no normal items remained
        if (!$this->_quote->getAllVisibleItems()) {
            $this->_inactivateQuote();
            return;
        }
		
		
        $this->submitOrder();
    }
	
	/**
     * Submit the quote. Quote submit process will create the order based on quote data
     *
     * @return Mage_Sales_Model_Order
     */
    public function submitOrder()
    {
        $this->_deleteNominalItems();
        $this->_validate();
        $quote = $this->_quote;
        $isVirtual = $quote->isVirtual();
		
		
		
		
        $transaction = Mage::getModel('core/resource_transaction');
        
		
		
		
		if ($quote->getCustomerId()) {
            $transaction->addObject($quote->getCustomer());
        }
        
		$transaction->addObject($quote);       
		
		$quote->reserveOrderId();
		
		
		
        if ($isVirtual) {
            $order = $this->_convertor->addressToOrder($quote->getBillingAddress());
        } else {
            $order = $this->_convertor->addressToOrder($quote->getShippingAddress());
        }
		
		
		
        $order->setBillingAddress($this->_convertor->addressToOrderAddress($quote->getBillingAddress()));
       
	    if ($quote->getBillingAddress()->getCustomerAddress()) {
            $order->getBillingAddress()->setCustomerAddress($quote->getBillingAddress()->getCustomerAddress());
        }
       
	  
	   
	    if (!$isVirtual) {
            $order->setShippingAddress($this->_convertor->addressToOrderAddress($quote->getShippingAddress()));
            
			if ($quote->getShippingAddress()->getCustomerAddress()) {
                $order->getShippingAddress()->setCustomerAddress($quote->getShippingAddress()->getCustomerAddress());
            }
        }
		
		
		
        $order->setPayment($this->_convertor->paymentToOrderPayment($quote->getPayment()));

        
		
		
		
		foreach ($this->_orderData as $key => $value) {
            
			$order->setData($key, $value);
        }

        foreach ($quote->getAllItems() as $item) {
            
			$orderItem = $this->_convertor->itemToOrderItem($item);
            
			if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }
		
		

        $order->setQuote($quote);

		
		$transaction->addObject($order);
        $transaction->addCommitCallback(array($order, 'place'));
        $transaction->addCommitCallback(array($order, 'save'));

        
		 
		
		
		/**
         * We can use configuration data for declare new order status
         */
        
		
		Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$quote));
        Mage::dispatchEvent('sales_model_service_quote_submit_before', array('order'=>$order, 'quote'=>$quote));
        
		
		
		try {
            
			///order saving///
			
			 $transaction->save();
			
			//print_r($transaction);
			
			//echo 'here';
			//exit;
			
            $this->_inactivateQuote();
            Mage::dispatchEvent('sales_model_service_quote_submit_success', array('order'=>$order, 'quote'=>$quote));
        
		
		
		} catch (Exception $e) {

            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                // reset customer ID's on exception, because customer not saved
                $quote->getCustomer()->setId(null);
            }

            //reset order ID's on exception, because order not saved
            $order->setId(null);
            /** @var $item Mage_Sales_Model_Order_Item */
            foreach ($order->getItemsCollection() as $item) {
                $item->setOrderId(null);
                $item->setItemId(null);
            }

            Mage::dispatchEvent('sales_model_service_quote_submit_failure', array('order'=>$order, 'quote'=>$quote));
            throw $e;
        }
        Mage::dispatchEvent('sales_model_service_quote_submit_after', array('order'=>$order, 'quote'=>$quote));
        $this->_order = $order;
        return $order;
    }
	
	
	

	
}
