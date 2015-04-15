<?php

class MDN_Quotation_AdminController extends Mage_Adminhtml_Controller_Action {

    /**
     * Select customer for new quote
     */
    public function SelectOrCreateCustomerAction() {
        $this->loadLayout();
        $this->renderLayout();
    }


    /**
     * Edit quote
     */
    public function editAction() {
        $quoteId = $this->getRequest()->getParam('quote_id');
		$msgread = $this->getRequest()->getParam('msg');
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
        Mage::register('current_quote', $quote);
        if (!$quote->hasRealProduct())
            Mage::getSingleton('adminhtml/session')->addError($this->__('Quote must contain at least one real product'));

        $check = $quote->checkProducts();
		
		///update the message history and put messages read
		
	//if($msgread==1){
			 /*
			 $resource = Mage::getSingleton('core/resource')->getConnection('core_write');
			$temptableHistory=Mage::getSingleton('core/resource')->getTableName('quotation_history');
				
				if($resource->isTableExists($temptableHistory))
				{
					
        		$sqlHistory="update ".$temptableHistory." SET  qh_readstatus=0 WHERE qh_quotation_id=".$quoteId;
				$resource->query($sqlHistory);
         
    			}
				*/
		//	}
		
        if($check['error'] === true)
            Mage::getSingleton('adminhtml/session')->addError($this->__($check['message']));
        $this->loadLayout();
        $this->renderLayout();
    }

   


/**
     * Print order
     */
    public function printorderAction() {
        
		$order_id = $this->getRequest()->getParam('order_id');
        
		//$quote = Mage::getModel('Quotation/Quotation')->load($QuoteId);
		 $order = Mage::getModel('sales/order')->load($order_id);
         $this->checkQuoteOwner($order);
		 
		    $orderModel=Mage::getModel('sales/order')->load($order->getIncrement_id(), 'increment_id');
            $order_id=$orderModel->getEntityId();
		    $customerid = $orderModel->getCustomer()->getEntity_id();
		 
		    $session = $this->_getSession();
			//var_dump($session);
			//exit;
		 
		 
		try {
            $layout = $this->loadLayout();
			
	        $customer = Mage::getModel('customer/customer')->load($customerid);
            $session->setCustomerAsLoggedIn($customer);
								
           // $comit = $order->commit();			 
            $pdf = Mage::getModel('Quotation/quotationpdf')->getOrderPdf(array($order));  
			$name = Mage::helper('quotation')->__('order_') . $order->getincrement_id() . '.pdf';
			
			
			
			$respnose =$this->getResponse()
                ->setHeader('Content-type', 'application/pdf', true)               
                ->setBody($pdf->render());
			echo $respnose; exit;
			
			$this->_prepareDownloadResponseV2($name, $pdf->render(), 'application/pdf');
        } catch (Exception $ex) {
            Mage::getSingleton('checkout/session')->addError($ex->getMessage());
            $this->_redirect('Quotation/Quote/View', array('order_id' => $order_id));
        }
    }



    /**
     * Load layout to create a new quote
     */
    public function newAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save edited quote
     */
    public function postAction() {
        
        //print_r($_REQUEST);exit;
		//var_dump($_REQUEST); exit;

        $post = $this->getRequest()->getPost();
        $data = $post['myform'];
        $quoteId = $post["myform"]["quotation_id"];
        $quote = Mage::getModel("Quotation/Quotation")->load($quoteId);
		
		
        //set quotation information
        foreach ($data as $key => $value) {
            $quote->setData($key, $value);
        }
		
		
		 
		
        //process attachment
        try {
            
			$delete = (isset($post['delete_attachment']) ? $post['delete_attachment'] : 0);
            $this->postProcessAttachment($quote, $delete);
        
		} catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Error processing attachment: %s', $ex->getMessage()));
        }

        //save products information
        try {
            $this->postProcessSaveProducts($quote, $post);
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Error saving products: %s', $ex->getMessage()));
        }

        //add products (standard or fake)
        try {
            $this->postProcessAddProducts($quote, $post);
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Error adding products: %s', $ex->getMessage()));
        }

        // commercial part
        try {
            $proposalData = (isset($post["myform"]["proposal"]) ? $post["myform"]["proposal"] : array());
            Mage::helper('quotation/Proposal')->save($proposalData, $quote);
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Error saving proposal: %s', $ex->getMessage()));
        }

        //shipping
        $this->postProcessShipping($quote, $post);

        //update datas
        if ($quote->getauto_calculate_price())
            $quote->CalculateQuotationPriceHt();
        if ($quote->getauto_calculate_weight())
            $quote->CalculateWeight();


        //delete associated product & promotion
        Mage::getModel('Quotation/Quotation_Bundle')->deleteBundle($quote);
        Mage::getModel('Quotation/Quotation_Promotion')->deletePromotion($quote);

        //save
        $quote->save();
        
        $quote = Mage::getModel('Quotation/Quotation')->load($quote->getId());
        $quoteItems = $quote->getItems();
        $id = '';
        
        /*************************** Start by dev ******************************************/
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	    $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $tableShipping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
		$tableQuotation = Mage::getSingleton('core/resource')->getTableName('quotation');
                   
        /************* update Email Template  ****************/
		$connectionWrite->beginTransaction();
        $data = array();
        
		if($post['form']['email_attachement'] != ''){
        $data['email_attachement'] = $post['form']['email_attachement'];
		}else{
        $data['email_attachement'] = '';
		}
		
        $where = $connectionWrite->quoteInto('quotation_id =?', $quote->getId());
		
        $connectionWrite->update($tableQuotation, $data, $where);
        
		$connectionWrite->commit();
		
		//Zend_debug::dump($connectionWrite->update($tableQuotation, $data, $where)); exit;
		//$connectionWrite->commit();
		
		
		
		/****************  Update Billing ******************************************/
		
		$selectBilling = $connectionRead->select()
                        ->from($tableBilling, array('*'))
                        ->where('quotation_id=?',$quote->getId());
        $rowBilling = $connectionRead->fetchAll($selectBilling);
        //print_r($rowBilling);
        
        if(count($rowBilling[0]['quotation_id']) > 0){
	    
       // $sqlPaymentSystem="UPDATE  ".$tableBilling."  SET  phone = '".$quote['billing_address']['telephone']."',   street1 = '".$quote['billing_address']['street'][0]."',street2 = '".$_REQUEST['billing_address']['street'][1]."', city = '".$quote['billing_address']['city']."', region ='".$quote['billing_address']['region']."', region_id ='".$quote['billing_address']['region_id']."', postcode = '".$quote['billing_address']['postcode']."', country_id ='".$billing['country_id']."', telephone = '".$quote['billing_address']['telephone']."' WHERE quotation_id = '".$quote_id."'";
       // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);

        $connectionWrite->beginTransaction();
        $data = array();
        
        if($post['quote']['billing_address']['firstname'] != '')
        $data['firstname'] = $post['quote']['billing_address']['firstname'];
        else
        $data['firstname'] = '';
                
        if($post['quote']['billing_address']['lastname'] != '')
        $data['lastname'] = $post['quote']['billing_address']['lastname'];
        else
        $data['lastname'] = '';
        
        if($post['quote']['billing_address']['company'] != '')
        $data['company'] = $post['quote']['billing_address']['company'];
        else
        $data['company'] = '';
        
        if($post['quote']['billing_address']['telephone'] != '')
        $data['phone'] = $post['quote']['billing_address']['telephone'];
        else
        $data['phone'] = '';
        
        if($post['quote']['billing_address']['street'][0] != '')
        $data['street1'] = $post['quote']['billing_address']['street'][0];
        else
        $data['street1'] = '';
        
        if($post['quote']['billing_address']['street'][1] != '')
        $data['street2'] = $post['quote']['billing_address']['street'][1];
        else
        $data['street2'] = '';
        
        if($post['quote']['billing_address']['city'] != '')
        $data['city'] = $post['quote']['billing_address']['city'];
        else
        $data['city'] = '';
        
        if($post['quote']['billing_address']['region'] != '')
        $data['region'] =$post['quote']['billing_address']['region'];
        else
        $data['region'] = '';
        
        if($post['quote']['billing_address']['region_id'] != '')
        $data['region_id'] =$post['quote']['billing_address']['region_id'];
        else
        $data['region_id'] = '';
        
        if($post['quote']['billing_address']['postcode'] != '')
        $data['postcode'] = $post['quote']['billing_address']['postcode'];
        else
        $data['postcode'] = '';
        
        if($post['billing']['country_id'] != '')
        $data['country_id'] =$post['billing']['country_id'];
        else
        $data['country_id'] = '';
        
        if($post['quote']['billing_address']['telephone'] != '')
        $data['telephone'] = $post['quote']['billing_address']['telephone'];
        else
        $data['telephone'] = '';
        
        $where = $connectionWrite->quoteInto('quotation_id =?', $quote->getId());
        $connectionWrite->update($tableBilling, $data, $where);
        $connectionWrite->commit();
         
        
        }else{
            
            
            $connectionWrite->beginTransaction();
            $data = array();

                
                $data['quotation_id'] = $quote->getId();
                
                if($post['quote']['billing_address']['firstname'] != '')
                $data['firstname'] = $post['quote']['billing_address']['firstname'];
                else
                $data['firstname'] = '';
                
                        
                if($post['quote']['billing_address']['lastname'] != '')
                $data['lastname'] = $post['quote']['billing_address']['lastname'];
                else
                $data['lastname'] = '';
                
                if($post['quote']['billing_address']['company'] != '')
                $data['company'] = $post['quote']['billing_address']['company'];
                else
                $data['company'] = '';
        
                if($post['quote']['billing_address']['telephone'] != '')
                $data['phone'] = $post['quote']['billing_address']['telephone'];
                else
                $data['phone'] = '';
                
                if($post['quote']['billing_address']['street'][0] != '')
                $data['street1'] = $post['quote']['billing_address']['street'][0];
                else
                $data['street1'] = '';
                
                if($post['quote']['billing_address']['street'][1] != ''){
                    $data['street2'] = $post['quote']['billing_address']['street'][1];
                }
                else
                $data['street2'] = '';
                
                if($post['quote']['billing_address']['city'] != '')
                $data['city'] = $post['quote']['billing_address']['city'];
                else
                $data['city'] = '';
                
                if($post['quote']['billing_address']['region'] != '')
                $data['region'] =$post['quote']['billing_address']['region'];
                else
                $data['region'] = '';
                
                if($post['quote']['billing_address']['region_id'] != '')
                $data['region_id'] =$post['quote']['billing_address']['region_id'];
                else
                $data['region_id'] = '';
                
                if($post['quote']['billing_address']['postcode'] != '')
                $data['postcode'] = $post['quote']['billing_address']['postcode'];
                else
                $data['postcode'] = '';
                
                if($post['billing']['country_id'] != '')
                $data['country_id'] =$post['billing']['country_id'];
                else
                $data['country_id'] = '';
                
                if($post['quote']['billing_address']['telephone'] != '')
                $data['telephone'] = $post['quote']['billing_address']['telephone'];
                else
                $data['telephone'] = '';
            

            $connectionWrite->insert($tableBilling, $data);
            $connectionWrite->commit();
        }
        
        $selectShipping = $connectionRead->select()
                        ->from($tableShipping, array('*'))
                        ->where('quotation_id=?',$quote->getId());
        $rowShipping = $connectionRead->fetchAll($selectShipping);
        
        if(count($rowShipping[0]['quotation_id']) > 0){
       // $sqlPaymentSystem="UPDATE ".$tableShipping."  SET  phone = '".$quote['shipping_address']['telephone']."',   street1 = '".$quote['shipping_address']['street'][0]."',street2 = '".$_REQUEST['shipping_address']['street'][1]."', city = '".$quote['shipping_address']['city']."', region ='".$quote['shipping_address']['region']."', region_id ='".$quote['shipping_address']['region_id']."', postcode = '".$quote['shipping_address']['postcode']."', country_id ='".$shipping['country_id']."', telephone = '".$quote['shipping_address']['telephone']."' , inhand = '".$inhand."' WHERE quotation_id = '".$quote_id."'";
       // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
       
       //print_r($post['quote']['shipping_address']);exit;
        
        $connectionWrite->beginTransaction();
        $data1 = array();
        
        if($post['quote']['shipping_address']['firstname'] != '')
        $data1['firstname'] = $post['quote']['shipping_address']['firstname'];
        else
            $data1['firstname'] = '';
                
        if($post['quote']['shipping_address']['lastname'] != '')
        $data1['lastname'] = $post['quote']['shipping_address']['lastname'];
        else
        $data1['lastname'] = '';
        
        
        if($post['quote']['shipping_address']['company'] != '')
        $data1['company'] = $post['quote']['shipping_address']['company'];
        else
        $data1['company'] = '';
        
        if($post['quote']['shipping_address']['telephone'] != '')
        $data1['phone'] = $post['quote']['shipping_address']['telephone'];
        else
        $data1['phone'] = '';
        
        if($post['quote']['shipping_address']['street'][0] != '')
        $data1['street1'] = $post['quote']['shipping_address']['street'][0];
        else
            $data1['street1'] = '';
            
        if($post['quote']['shipping_address']['street'][1] != '')
        $data1['street2'] = $post['quote']['shipping_address']['street'][1];
        else
            $data1['street2'] = '';
            
        if($post['quote']['shipping_address']['city'] != '')
        $data1['city'] = $post['quote']['shipping_address']['city'];
        else
            $data1['city'] = '';
            
        if($post['quote']['shipping_address']['region'] != '')
        $data1['region'] =$post['quote']['shipping_address']['region'];
        else
            $data1['region'] = '';
            
        if($post['quote']['shipping_address']['region_id'] != '')
        $data1['region_id'] =$post['quote']['shipping_address']['region_id'];
        else
            $data1['region_id'] = '';
            
        if($post['quote']['shipping_address']['postcode'] != '')
        $data1['postcode'] = $post['quote']['shipping_address']['postcode'];
        else
            $data1['postcode'] = '';
            
        if($post['shipping']['country_id'] != '')
        $data1['country_id'] =$post['shipping']['country_id'];
        else
            $data1['country_id'] = '';
            
        if($post['quote']['shipping_address']['telephone'] != '')
        $data1['telephone'] = $post['quote']['shipping_address']['telephone'];
        else
            $data1['telephone'] = '';
        
        if($post['myform']['delivery_date'] == '')//07_03_2014
        $post['myform']['delivery_date'] = '0000-00-00';
        
        $data1['inhand'] = $post['myform']['delivery_date'];//07_03_2014
        
        $where = $connectionWrite->quoteInto('quotation_id =?', $quote->getId());
        $connectionWrite->update($tableShipping, $data1, $where);
        $connectionWrite->commit();
        
        }else{
            
            $connectionWrite->beginTransaction();
            $data1 = array();

            $data1['quotation_id'] = $quote->getId();
            
            if($post['quote']['shipping_address']['firstname'] != '')
            $data1['firstname'] = $post['quote']['shipping_address']['firstname'];
            else
            $data1['firstname'] = '';
                    
            if($post['quote']['shipping_address']['lastname'] != '')
            $data1['lastname'] = $post['quote']['shipping_address']['lastname'];
            else
            $data1['lastname'] = '';
            
            if($post['quote']['shipping_address']['company'] != '')
            $data1['company'] = $post['quote']['shipping_address']['company'];
            else
            $data1['company'] = '';
            
            $data1['phone'] = $post['quote']['shipping_address']['telephone'];
            
            if($post['quote']['shipping_address']['street'][0] != '')
            $data1['street1'] = $post['quote']['shipping_address']['street'][0];
            else
            $data1['street1'] =  '';
            
            if($post['quote']['shipping_address']['street'][1] != ''){
                $data1['street2'] = $post['quote']['shipping_address']['street'][1];
            }
            else
            $data1['street2'] = '';
            
            if($post['quote']['shipping_address']['city'] != '')
            $data1['city'] = $post['quote']['shipping_address']['city'];
            else
            $data1['city'] = '';
            
            if($post['quote']['shipping_address']['region'] != '')
            $data1['region'] =$post['quote']['shipping_address']['region'];
            else
            $data1['region'] = '';
            
            if($post['quote']['shipping_address']['region_id'] != '')
            $data1['region_id'] =$post['quote']['shipping_address']['region_id'];
            else
            $data1['region_id'] = '';
            
            if($post['quote']['shipping_address']['postcode'] != '')
            $data1['postcode'] = $post['quote']['shipping_address']['postcode'];
            else
            $data1['postcode'] = '';
            
            if($post['shipping']['country_id'] != '')
            $data1['country_id'] =$post['shipping']['country_id'];
            else
            $data1['country_id'] = '';
            
            if($post['quote']['shipping_address']['telephone'] != '')
            $data1['telephone'] = $post['quote']['shipping_address']['telephone'];
            else
            $data1['telephone'] = '';
            
            if($post['myform']['delivery_date'] == '')//07_03_2014
            $post['myform']['delivery_date'] = '0000-00-00';
            $data1['inhand'] = $post['myform']['delivery_date'];//07_03_2014
            
            $connectionWrite->insert($tableShipping, $data1);
            $connectionWrite->commit();
            
        }
      
         $subtotal = 0;
        /********************************End by dev **********************************************/
        
        foreach ($quoteItems as $item) {
            $id .= "'".$item->getId()."', ";
            
            $ProductId = $item->getProductId();
            
            /************** Start to add tax 30_01_2014 ********************/
            
            $_product = Mage::getModel('catalog/product')->load($ProductId);
            $tax_helper = Mage::getSingleton('tax/calculation');
            $tax_request = $tax_helper->getRateOriginRequest();
            $tax_request->setProductClassId($_product->getTaxClassId());
            
            $tax = $tax_helper->getRate($tax_request);
            
            $item->setEcoTax($tax);
            $item->save();
            
           
            /************** End to add tax 30_01_2014 ********************/
            
             /************************** Start For bundle product *************************************/
            
            if($item->getProductType() == 'bundle')
            {
                
                $temptableItem=Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
                $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                
                $connectionWrite->beginTransaction();
                $condition = array($connectionWrite->quoteInto('parent_item_id=?', $item->getId()));
                $connectionWrite->delete($temptableItem, $condition);
                $connectionWrite->commit();
                
                $bundle_flag = 0;
                
                // For checkbox
                if(!empty($_REQUEST['bundle'][$ProductId]))
                {
                    $priceht = 0;
                    foreach($_REQUEST['bundle'][$ProductId] as $key=>$options)
                    {
                            
                        foreach($options as $selection)
                        {                        
                            $simple_product = Mage::getModel('catalog/product')->load($selection);
                                            
                            $simple_qty = $_REQUEST['bundle_qty'][$ProductId][$key][$selection];
                            
                            $priceht += $simple_product->getPrice() * $simple_qty;
                            
                            $connectionWrite->beginTransaction();
                            $data1 = array();
                            $data1['quotation_id']= $quote->getId();
                            $data1['product_id']=$selection;
                            $data1['parent_item_id']= $item->getId();
                            $data1['qty']= $simple_qty;
                            $data1['price']= $simple_product->getPrice() * $simple_qty;
                            $data1['caption'] = $simple_product->getName();
                            $data1['sku'] = $simple_product->getSku();
                            $connectionWrite->insert($temptableItem, $data1);
                            $connectionWrite->commit();
                        }
                    }
                    
                    $bundle_flag = 1;
                }
                
                 if(!empty($_REQUEST['bundle_radio'][$ProductId]))// For radio
                {
                    $priceht = 0;
                    foreach($_REQUEST['bundle_radio'][$ProductId] as $key=>$options)
                    {
                                               
                            $simple_product = Mage::getModel('catalog/product')->load($options);
                                            
                            $simple_qty = $_REQUEST['bundle_qty'][$ProductId][$key][$options];
                            
                            $priceht += $simple_product->getPrice() * $simple_qty;
                            
                            $connectionWrite->beginTransaction();
                            $data1 = array();
                            $data1['quotation_id']= $quote->getId();
                            $data1['product_id']=$options;
                            $data1['parent_item_id']= $item->getId();
                            $data1['qty']= $simple_qty;
                            $data1['price']= $simple_product->getPrice() * $simple_qty;
                            $data1['caption'] = $simple_product->getName();
                            $data1['sku'] = $simple_product->getSku();
                            $connectionWrite->insert($temptableItem, $data1);
                            $connectionWrite->commit();
                       
                    }
                    
                    $bundle_flag = 1;
                }
               
                if($_REQUEST['bundle_select'][$ProductId] != '')// For Drop-down
                {
                    $priceht = 0;
                    foreach($_REQUEST['bundle_select'][$ProductId] as $key=>$options)
                    {
                        $simple_product = Mage::getModel('catalog/product')->load($options);
                                        
                        $simple_qty = $_REQUEST['bundle_qty_select'][$ProductId][$key];
                        
                        $priceht += $simple_product->getPrice() * $simple_qty;
                        
                        $connectionWrite->beginTransaction();
                        $data1 = array();
                        $data1['quotation_id']= $quote->getId();
                        $data1['product_id']= $options;
                        $data1['parent_item_id']= $item->getId();
                        $data1['qty']= $simple_qty;
                        $data1['price']= $simple_product->getPrice() * $simple_qty;
                        $data1['caption'] = $simple_product->getName();
                        $data1['sku'] = $simple_product->getSku();
                        $connectionWrite->insert($temptableItem, $data1);
                        $connectionWrite->commit();
                    }
                    
                    $bundle_flag = 1;
                }
                
                 if(!empty($_REQUEST['bundle_multi'][$ProductId])) // For multi select
                {
                    $priceht = 0;
                    foreach($_REQUEST['bundle_multi'][$ProductId] as $key=>$options)
                    {
                        foreach($options as $pro_id=>$selections)
                        {
                            $simple_product = Mage::getModel('catalog/product')->load($selections);
                                            
                            $simple_qty = $_REQUEST['bundle_qty_multi'][$ProductId][$key];
                            
                            $priceht += $simple_product->getPrice() * $simple_qty;
                            
                            $connectionWrite->beginTransaction();
                            $data1 = array();
                            $data1['quotation_id']= $quote->getId();
                            $data1['product_id']=$selections;
                            $data1['parent_item_id']= $item->getId();
                            $data1['qty']= $simple_qty;
                            $data1['price']= $simple_product->getPrice() * $simple_qty;
                            $data1['caption'] = $simple_product->getName();
                            $data1['sku'] = $simple_product->getSku();
                            $connectionWrite->insert($temptableItem, $data1);
                            $connectionWrite->commit();
                        }
                    }
                    
                    $bundle_flag = 1;
                }
                
                /********************** Start for add bundle item at irst save 18_02_2014 *************************/
                if($bundle_flag == 0)
                {
                   $selectionCollection = $_product->getTypeInstance(true)->getSelectionsCollection(
                    $_product->getTypeInstance(true)->getOptionsIds($_product), $_product
                    );
                   
                    foreach($selectionCollection as $option)
                    {
                        $simple_product = Mage::getModel('catalog/product')->load($option->getProductId());
                                            
                        $simple_qty = $option->getSelectionQty();
                        
                        $priceht += $simple_product->getPrice() * $simple_qty;
                        
                        $connectionWrite->beginTransaction();
                        $data1 = array();
                        $data1['quotation_id']= $quote->getId();
                        $data1['product_id']=$option->getProductId();
                        $data1['parent_item_id']= $item->getId();
                        $data1['qty']= $simple_qty;
                        $data1['price']= $simple_product->getPrice() * $simple_qty;
                        $data1['caption'] = $simple_product->getName();
                        $data1['sku'] = $simple_product->getSku();
                        $connectionWrite->insert($temptableItem, $data1);
                        $connectionWrite->commit();
                    }
                }
                
                /********************** End for add bundle item at irst save 18_02_2014 *************************/
                
                
                //$item->setPriceHt($priceht);
               // $item->setPriceHt($_product->getPrice());
                $item->save();
                
                $subtotal += $_product->getPrice();
            }
            
           // $quote->setPriceHt($subtotal);
           // $quote->save();
            /************************** End For bundle product *************************************/
            
            
            /*********************** add planning auto *********************************/
                $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
		{
                $sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$quote->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'quote' ";
                $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlShipping);
                }
                
                if(count($chkShipping) == 0)
                {
                
                    $created_date = $quote->getCreatedTime();
                    
                    $Product = Mage::getModel('catalog/product')->load($ProductId);
                    
                    $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableTimeline))
                    {
                    $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
                    $chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTimeline);
                    }
                
                    $order_placed_date =  $created_date;
                    $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                    $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                    $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                    $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                    $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                    
                    
                                      
                    $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                    {
                    $sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$quote->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'quote', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
                    $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                    }
                }
                
               
            
            /*********************** add planning auto *********************************/
            
            /************************ Get custom option value ******************************/
              /*  
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
              */
                /************************ Get custom option value ******************************/
                
            
            
          

            
            }
        /******************* Delete exrta the planner from table *************************/
        $id = rtrim($id,', ');


        $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
        if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping) and $id != '')
        {
        $sqlShipping="DELETE FROM  ".$temptableShipping." WHERE quote_id = '".$quote->getId()."' AND item_id NOT IN($id) AND planning_type = 'quote' ";
        $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
        }
        /******************* Delete exrta the planner from table *************************/
        
        Mage::dispatchEvent('model_save_after', array('object'=>Mage::getModel("Quotation/Quotation")));
	mage::helper('AdminLogger')->updatelog($quoteId,'Edit the Quote');
        
        $alert=array('Delete Proofs In quote','Add New Proof In Quote','Edit Proof Item In Quote','Add New Quote','Edit the Quote','Edit Product in the Quote','Delete the Quote','Create order From Quote','Edit Planning In Quote','Add New Design In quote','Assign the Designer In quote','dd Feedback in Quote','Delete Design In Quote');
        Mage::getModel('systemalert/systemalert')->sendalert($alert,'order',$quote);

		
		
		
		$_email_attachment = $post['myform']['email_attachement'];
		
		if($_email_attachment !=''){
		///saving as attachement file/////
		$file_directory = Mage::getBaseDir('media').'/attachedfiles/';
		$_file = fopen($file_directory.$quote->getIncrement_id().'.html','w');
		$_conn = fwrite($_file,$_email_attachment) or die('could not write to file');
		//var_dump($file_directory.$quote->getIncrement_id());
		//exit;
		
		$_conn = fclose($_file);
		}
		$_files = $_POST['email_attachements'];
		///saving to db files///
	
	   $_connection_write = Mage::getSingleton('core/resource')->getConnection('core_write');
	   $_quotation_attachements = Mage::getSingleton('core/resource')->getTableName('quotation_attachements');
		
	   if(count($_files)>0){
		
		foreach($_files as $_at_file){
		try{
		   $_connection_write->beginTransaction();
		   $_files_data['quotemail_id']=$quoteId;
		   $_files_data['email_attachment']= $_at_file; 
		   
		   $_connection_write->insert($_quotation_attachements,$_files_data);
		   
	   $_connection_write->commit();
		
		}catch(Exception $Ex){
			echo $Ex;
		}
	}
	   
	   }//end of saving files
		
		//echo $_email_attachment;		
		//Zend_debug::dump($file_directory.$quote->getIncrement_id().'.html'.$_file);		
		//exit;
		
		
		
		
		
		
        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('quotation successfully saved.'));
        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteId, 'tab' => $this->getRequest()->getParam('tab_to_display')));
    }
    
    
    /***************************** Add custom function ***********************************/
     public function isweekend($date){
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
                        if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
                        {
                        $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                        $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
                        }
                        
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
                    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
                    {
                    $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                    $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
                    }
                    
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

    /**
     * Process PDF attachment
     */
    protected function postProcessAttachment($quote, $delete) {
        if (isset($_FILES['upload_pdf']) && $_FILES['upload_pdf']['name'] != "") {
            $pdfAdditional = $_FILES['upload_pdf'];
            $uploader = new Varien_File_Uploader($pdfAdditional);
            $uploader->setAllowedExtensions(array('pdf'));
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $directory = Mage::helper('quotation/Attachment')->getAttachmentDirectory();
            $fileName = Mage::helper('quotation/Attachment')->getFileName($quote);
            $filePath = Mage::helper('quotation/Attachment')->getAttachmentPath($quote);
            if (file_exists($filePath))
                unlink($filePath);
            $uploader->save($directory, $fileName);
        }
        else {
            if ($delete) {
                $filePath = Mage::helper('quotation/Attachment')->getAttachmentPath($quote);
                if (file_exists($filePath))
                    unlink($filePath);
            }
        }
    }

    /**
     * Add products to quote (if applicable)
     */
    //protected function postProcessAddProducts($quote, $post) {
    //
    //    //add fake product
    //    if ($post['fake_name'] != '') {
    //        $quote->addFakeProduct($post['fake_name'], $post['fake_qty'], $post['fake_price'], $post['fake_weight']);
    //    }
    //
    //    //add "regular" products
    //    if (isset($post['add_product_log'])) {
    //        $addString = $post['add_product_log'];
    //        $lines = explode(';', $addString);
    //        foreach ($lines as $line) {
    //            $t = explode('=', $line);
    //            if (count($t) == 2) {
    //                $qty = $t[1];
    //                $productId = str_replace('add_qty_', '', $t[0]);
    //                $quote->addProduct($productId, $qty);
    //            }
    //        }
    //    }
    //}
    
    
    protected function postProcessAddProducts($quote, $post) {

        //add fake product
        if ($post['fake_name'] != '') {
            $quote->addFakeProduct($post['fake_name'], $post['fake_qty'], $post['fake_price'], $post['fake_weight']);
        }

        //add "regular" products
        //if (isset($post['add_product_log'])) {
        //    echo $addString = $post['add_product_log'];
        //    $lines = explode(';', $addString);
        //    print_r($lines);exit;
        //    foreach ($lines as $line) {
        //        $t = explode('=', $line);
        //        if (count($t) == 2) {
        //            $qty = $t[1];
        //            $productId = str_replace('add_qty_', '', $t[0]);
        //            $quote->addProduct($productId, $qty);
        //        }
        //    }
        //}
        
        
        if (isset($post['add_product_log'])) {
            $addString = $post['add_product_log'];
            $lines = explode(';', $addString);
            
            foreach ($lines as $line) {
                $t = explode('=', $line);
                if (count($t) == 2) {
                    $pro[$t[0]] = $t[1];
                }
            }
            
            foreach ($pro as $key=>$line) {
                    $qty = $line;
                    $productId = str_replace('add_qty_', '', $key);
                    $quote->addProduct($productId, $qty);
            }
        }
    }

    /**
     * Process shipping
     */
    protected function postProcessShipping($quote, $post) {

        $shippingMethod = null;
        $shippingDescription = null;
        $shippingRate = null;

        //if ($post["myform"]["shipping_method"]) {
        //    $shippingMethod = $post["myform"]["shipping_method"];
        //    $shippingObject = Mage::helper('quotation/ShippingRates')->getRate($quote, $quote->GetCustomerAddress(), $shippingMethod);
        //    $shippingDescription = $shippingObject['carrier_title'] . ' / ' . $shippingObject['method_title'];
        //    $shippingRate = $shippingObject['price'];
        //} else {
        //    $shippingMethod = '';
        //    $shippingDescription = '';
        //    $shippingRate = '';
        //}

        //save
        //$quote->setshipping_method($shippingMethod)
        //        ->setshipping_description($shippingDescription)
        //        ->setshipping_rate($shippingRate);
        
        /************** Start to add custom shipping price 17_02_2014 *********************/
        
        //start 19_02_2014
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableShipping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
                //$sqlSystem="SELECT * FROM ".$tableBilling." WHERE quotation_id = '".$quote->getId()."' ";
        $sqlSystem = $connectionRead->select()
                                ->from($tableShipping, array('*'))
                                ->where('quotation_id=?',$quote->getId()); 
         try {
                 $chkSystem = $connectionRead->query($sqlSystem);
                 $fetchSystem = $chkSystem->fetch();
         } catch (Exception $e){
         //echo $e->getMessage();
         }
        
        if ($post["myform"]["shipping_method"]) {
            $shippingMethod = $post["myform"]["shipping_method"];
            $shippingObject = Mage::helper('quotation/ShippingRates')->getRate($quote, $quote->GetCustomerAddress(), $shippingMethod, $fetchSystem);
            $shippingDescription = $shippingObject['carrier_title'] . ' / ' . $shippingObject['method_title'];
            $shippingRate = $shippingObject['price'];
        } else {
            $shippingMethod = '';
            $shippingDescription = '';
            $shippingRate = '';
        }
        //end 19_02_2014
        
        $quote->setshipping_method($shippingMethod)
                ->setshipping_description($shippingDescription);
                
            
            if($post["custom_ship"] == 1)
                 $quote->setshipping_rate($post["custom_price"]);
            else
            $quote->setshipping_rate($shippingRate);
        /************** End to add custom shipping price 17_02_2014 *********************/
    }

    /**
     * Save product information
     */
    protected function postProcessSaveProducts($quote, $post) {

        foreach ($quote->getItems() as $item) {

            if (isset($post["remove_" . $item->getId()])) {
                $remove = $post["remove_" . $item->getId()];
                if ($remove) {
                    
                    /*********************** Start for bundle product *********************************/
                    $temptableItem=Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
                    $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                    
                    $connectionWrite->beginTransaction();
                    $condition = array($connectionWrite->quoteInto('parent_item_id=?', $item->getId()));
                    $connectionWrite->delete($temptableItem, $condition);
                    $connectionWrite->commit();
                    /*********************** End for bundle product *********************************/
                    
                    $item->delete();
                    continue;
                }
            }

            $exclude = 0;
            if (isset($post["exclude_" . $item->getId()]))
                $exclude = $post["exclude_" . $item->getId()];
            $weight = $post["weight_" . $item->getId()];

            //retrieve options and serialize
            $options = '';
            $optionsValue = array();
            $product = Mage::getModel('catalog/product')->load($item->getproduct_id());
            
                foreach ($product->getProductOptionsCollection() as $option) {
                    switch ($option->getType()) {
                        case Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE:
                        case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                            $values = array();
                            foreach ($option->getValues() as $possibleValue) {
                                $chName = 'product_' . $item->getId() . '_option_' . $option->getId() . '_' . $possibleValue->getId();
                                if (isset($post[$chName]))
                                    $values[] = $possibleValue->getId();
                            }
                            if (count($values) > 0)
                                $optionsValue[$option->getId()] = $values;
                            break;
                        case Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE:
                            $optionFieldName = 'product_' . $item->getid() . '_option_' . $option->getid();
                            if ((isset($post[$optionFieldName])) && ($post[$optionFieldName] != '')) {
    
                                $tmp = explode('-', $post[$optionFieldName]);
    
                                if (count($tmp) >= 3) {
                                    $optionsValue[$option->getid()] = array(
                                        'year' => (int) $tmp[0],
                                        'month' => (int) $tmp[1],
                                        'day' => (int) $tmp[2]
                                    );
                                }
                            }
                            break;
                        default:
                            $optionFieldName = 'product_' . $item->getId() . '_option_' . $option->getId();
                            if ((isset($post[$optionFieldName])) && ($post[$optionFieldName] != ''))
                                $optionsValue[$option->getId()] = $post[$optionFieldName];
                            break;
                    }
                }
                $options = Mage::helper('quotation/Serialization')->serializeObject($optionsValue);
    
                //save data
                $item
                        ->setorder($post["order_" . $item->getId()])
                        ->setcaption($post["caption_" . $item->getId()])
                        ->setsku($post["sku_" . $item->getId()])
                        ->setweight($weight)
                        ->setqty($post["qty_" . $item->getId()])
                        ->setprice_ht($post["price_ht_" . $item->getId()])
                        ->setdiscount_purcent($post["discount_purcent_" . $item->getId()])
                        ->setexclude($exclude)
                        ->setoptions($options);
    
                if (isset($post["description_" . $item->getId()]))
                    $item->setdescription($post["description_" . $item->getId()]);
                $item->save();
            
            
        }
        
        
         /************************************* Start by Dev *********************************************/
        
        $quoteItems = $quote->getItems();

        foreach ($quoteItems as $item) {
            
            $ProductId = $item->getProductId();
            
            //$product = Mage::getModel('catalog/product')->load($ProductId);
            //if($product->getTypeId() != 'bundle')
            //{
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
                            
                            
                             $finished_date = date('Y-m-j');
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
                                           ot_entity_type ='quote', 
                                           ot_entity_id = '".$quote->getId()."', 
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
                                                order_quote_id = '".$quote->getId()."' ,
                                                product_id ='".$chkOrganiger1['ot_entity_id']."', 
                                                task_type = '".$chkOrganiger1['ot_task_type']."'";
                                                
                                $chkChain = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChain);
                            }
                        }
                    }
                }
           // }
            
            
            
    
        
        }
        Mage::dispatchEvent('model_save_after', array('object'=>Mage::getModel("Quotation/Quotation")));
	mage::helper('AdminLogger')->updatelog($quote->getId(),'Edit Product in the Quote');
               
        /************************************* End by dev ********************************************/
        $quote->resetItems();
        
    }

    /**
     * Create new quote
     */
    public function createAction() {

        $post = $this->getRequest()->getPost();
        $customerId = $post["myform"]["customer_id"];
        $caption = $post["myform"]["caption"];
        $manager = Mage::getSingleton('admin/session')->getUser()->getusername();
		
		//create quote
        $quote = Mage::getModel("Quotation/Quotation")
                        ->setCaption($caption)
                        ->setcustomer_id($customerId)
                        ->setmanager($manager)
                        ->save();
                        
        /************************************* Start by Dev *********************************************/
        
        $customer = Mage::getModel('customer/customer')->load($customerId);
        
        //$address = $customer->getDefaultShipping();
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $tableShipping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
		$tableQuotation = Mage::getSingleton('core/resource')->getTableName('quotation');
 		
		/* upate quotation table with quotation_id*/
		
		$_store_id = $customer->getStore_id();
		$_store = Mage::getModel('core/store')->load($_store_id);
		
				
		if($quote->getId()){
			
			$_connection = $connectionWrite->beginTransaction();
        	$_sql_data['store_id'] = $_store_id;
			$_sql_data['store_name'] = $_store->getName();
			$_sql_data['message'] = 'New Quote';
							  
			$_where = $_connection->quoteInto('quotation_id=?', $quote->getId());					
			$_connection = $_connection->update($tableQuotation, $_sql_data, $_where);		 
			$connectionWrite->commit();
		   }
		
		
		if($customer->getDefaultBilling())
        {
        $connectionWrite->beginTransaction();
          $data = array();
        
        //$cust_data = $customer->getData();
        
       $address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
        
        $data['quotation_id'] = $quote->getId();
        
        if($address->getFirstname() != '')
        $data['firstname'] = $address->getFirstname();
                
        if($address->getLastname() != '')
        $data['lastname'] = $address->getLastname();
        
        if($address->getTelephone() != '')
        $data['phone'] = $address->getTelephone();
        
        $street = $address->getStreet();
        if($street[0] != '')
        $data['street1'] = $street[0];
        if($street[1] != ''){
            $data['street2'] = $street[1];
        }
        
        if($address->getCity() != '')
        $data['city'] = $address->getCity();
        
        if($address->getRegion() != '')
        {
            $data['region'] = $address->getRegion();
            $data['region_id'] =$address->getRegion();
        }
        
        if($address->getRegionId() != '')
        {
            $region = Mage::getModel('directory/region')->load($address->getRegionId());
            $data['region_id'] =$region->getName();
            $data['region'] =$region->getName();
        }
        
        if($address->getPostcode() != '')
        $data['postcode'] = $address->getPostcode();
        
        if($address->getCountryId() != '')
        $data['country_id'] = $address->getCountryId();
        if($address->getTelephone() != '')
        $data['telephone'] = $address->getTelephone();
        
        
        $connectionWrite->insert($tableBilling, $data);
        $connectionWrite->commit();
        }
       
        
        if($customer->getDefaultShipping())
        {
            
            $connectionWrite->beginTransaction();
             $data1 = array();
              //$cust_data = $customer->getData();
             $address = Mage::getModel('customer/address')->load($customer->getDefaultShipping());
             
             
            
            $data1['quotation_id'] = $quote->getId();
            
            if($address->getFirstname() != '')
            $data1['firstname'] = $address->getFirstname();
                    
            if($address->getLastname() != '')
            $data1['lastname'] = $address->getLastname();
            
            
            if($address->getTelephone() != '')
            $data1['phone'] = $address->getTelephone();
            
            $street = $address->getStreet();
            if($street[0] != '')
            $data1['street1'] = $street[0];
            if($street[1] != ''){
                $data1['street2'] = $street[1];
            }
            if($address->getCity() != '')
            $data1['city'] = $address->getCity();
            
            if($address->getRegion() != '')
            {
                $data1['region'] = $address->getRegion();
                $data1['region_id'] =$address->getRegion();
            }
            
            if($address->getRegionId() != '')
            {
                $region = Mage::getModel('directory/region')->load($address->getRegionId());
                $data1['region_id'] =$region->getName();
                $data1['region'] =$region->getName();
            }
            
            if($address->getPostcode() != '')
            $data1['postcode'] = $address->getPostcode();
            
            if($address->getCountryId() != '')
            $data1['country_id'] = $address->getCountryId();
            if($address->getTelephone() != '')
            $data1['telephone'] = $address->getTelephone();
            
            
            $connectionWrite->insert($tableShipping, $data1);
            $connectionWrite->commit();
        }
        
        $quoteItems = $quote->getItems();
         
        foreach ($quoteItems as $item) {
            
            $ProductId = $item->getProductId();
            
            $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
            if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
            {
            $sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'";
            $chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
            
            
            if($chkOrganiger)
            {
                 $sqlOrganiger1="INSERT INTO ".$temptableOrganiger." SET ot_created_at = NOW(), 
                                ot_author_user = '".$chkOrganiger[0]['ot_author_user']."' ,
                                ot_target_user ='".$chkOrganiger[0]['ot_target_user']."', 
                                ot_caption= '".addslashes($chkOrganiger[0]['ot_caption'])."', 
                                ot_description = '".addslashes($chkOrganiger[0]['ot_description'])."', 
                                ot_deadline = '".$chkOrganiger[0]['ot_deadline']."', 
                                ot_notify_date = '".$chkOrganiger[0]['ot_notify_date']."', 
                                ot_priority = '".$chkOrganiger[0]['ot_priority']."', 
                                ot_finished = '".$chkOrganiger[0]['ot_finished']."', 
                                ot_read ='".$chkOrganiger[0]['ot_read']."', 
                                ot_origin ='".$chkOrganiger[0]['ot_origin']."', 
                                ot_category = '".$chkOrganiger[0]['ot_category']."', 
                                ot_entity_type ='quote', 

                                ot_entity_id = '".$quote->getId()."', 
                                ot_entity_description = '".addslashes($chkOrganiger[0]['ot_entity_description'])."', 
                                ot_notification_read = '".$chkOrganiger[0]['ot_notification_read']."'";
                                
                $chkOrganiger1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlOrganiger1);
            }
            }
        }
        
        Mage::dispatchEvent('model_save_after', array('object'=>Mage::getModel("Quotation/Quotation")));
	mage::helper('AdminLogger')->updatelog($quote->getId(),'Add New Quote');
        
        /************************************* End by dev ********************************************/

        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('quotation successfully created.'));
        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quote->getId()));
    }

    /**
     * Delete quote
     */
    public function deleteAction() {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $quote = Mage::getModel("Quotation/Quotation")->load($quoteId);
            $quote->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Quotation successfully deleted.'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError('Error while deleting Quotation: ' . $ex->getMessage());
        }
        
        Mage::dispatchEvent('model_save_after', array('object'=>Mage::getModel("Quotation/Quotation")));
	mage::helper('AdminLogger')->updatelog($quoteId,'Delete the Quote');

        $this->_redirect('Quotation/Admin/List');
    }

    /**
     * Display quote grid
     */
    public function ListAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Quote duplication (select customer)
     *
     */
    public function DuplicateAction() {
        $this->loadLayout();

        $quoteId = $this->getRequest()->getParam('quotation_id');
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
        Mage::register('current_quote', $quote);

        $this->getLayout()->getBlock('quotationduplicatecustomer')->setMode('duplicate');
        $this->renderLayout();
    }

    /**
     * Duplicate quote
     *
     */
    public function ApplyDuplicateAction() {

        $quoteId = $this->getRequest()->getParam('quotation_id');
        $customerId = $this->getRequest()->getParam('customer_id');
        $oldQuotation = Mage::getModel('Quotation/Quotation')->load($quoteId);
        $newQuotation = $oldQuotation->duplicate($customerId);

        //Confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Quotation successfully duplicated.'));
        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $newQuotation->getId()));
    }

    /**
     * Get products grid
     *
     */
    public function productSelectionGridAction() {
        $quoteId = $this->getRequest()->getParam('quote_id');
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
        Mage::register('current_quote', $quote);

        $block = $this->getLayout()->createBlock('Quotation/Adminhtml_ProductSelection');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Return customer grid
     */
    public function customerSelectionGridAction() {
        $this->loadLayout();
        $mode = $this->getRequest()->getParam('mode');
        $quoteId = $this->getRequest()->getParam('quotation_id');
        if ($quoteId) {
            $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
            Mage::register('current_quote', $quote);
        }
        $block = $this->getLayout()->createBlock('Quotation/Adminhtml_SelectCustomer');
        $block->setMode($mode);
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Notify customer
     */
    public function notifyAction() {

        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
            $quote->NotifyCustomer(); ///commented on 24-2-2014
            //$quote->NotifyCreationToAdmin($quote);  ///24-2-2014

            //confirm
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('customer successfully notified.'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to notify customer : %s', $ex->getMessage()));
        }

        //redirect
        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteId));
    }


/**
     * email customer
     */
    public function emailpreviewAction() {
        try {
             $quoteId = $this->getRequest()->getParam('quotemail_id');
             $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
             $quotemail = $quote->previewEmail(); 
            
			echo($quotemail);
		
        } catch (Exception $ex) {
            echo $ex;
        }

        
    }



    /**
     * Remind customer
     */
    public function RemindCustomerAction() {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
            mage::getModel('Quotation/Quotation_Reminder')->sendCustomerReminder($quote);

            //confirm
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Customer successfully reminded.'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
        }
        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteId));
    }

    /**
     * Ajax refresh for quote grid in customer view
     */
    public function SelectedQuotationGridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Quotation/Adminhtml_Customer_Edit_Tab_Quotations')->setData('AjaxGrid', true)->toHtml()
        );
    }

    /**
     * Remove attached PDF
     */
    public function DeleteAdditionalPdfAction() {
        $quoteId = $this->getRequest()->getParam('quote_id');
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
        $path = Mage::helper('quotation/Attachment')->getAttachmentDirectory();
        $file = $path . $quote->getadditional_pdf();

        if (file_exists($file))
            unlink($file);

        $quote->setadditional_pdf('');
        $quote->save();

        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Additional pdf deleted.'));

        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteId));
    }

    /**
     * Download attached PDF
     */
    public function DownloadAdditionalPdfAction() {
        $quoteId = $this->getRequest()->getParam('quote_id');
        try {
            $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
            $filePath = Mage::helper('quotation/Attachment')->getAttachmentPath($quote);
            $content = file_get_contents($filePath);
            $this->_prepareDownloadResponse($quote->getadditional_pdf() . '.pdf', $content, 'application/pdf');
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Unable to download attachment : %s', $ex->getMessage()));
            $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteId));
        }
    }

    /**
     * Ajax history grid
     */
    public function gridAjaxAction() {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $block = $this->getLayout()->createBlock('Quotation/Adminhtml_History');
            $block->setCurrentQuote($quoteId);

            $this->getResponse()->setBody(
                    $block->toHtml()
            );
        } catch (Exception $e) {
            $this->getResponse()->setBody($e->getMessage() . ' : ' . $e->getTraceAsString());
        }
    }

    /**
     * Check ACL
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('customer/quotation_list');
    }
    
    public function createorderAction() {
        
        $quote_id = $this->getRequest()->getParam('quote_id');
		
		
		
        Mage::getSingleton('adminhtml/session')->setQuoteid($quote_id);
        $quote = Mage::getModel('Quotation/Quotation')->load($quote_id);
        Mage::register('current_quote', $quote);
       // echo "SHIPPING METHOD : ".$quote->getShippingMethod();
       // exit;
        $customerId = $quote->getCustomerId();
        
		
		
		
        //Mage::getSingleton('adminhtml/session_quote')->clear();
        //
        //$eventData = array(
        //    'order_create_model' => Mage::getSingleton('adminhtml/sales_order_create'),
        //    'request_model'      => $this->getRequest(),
        //    'session'            => $this->_getSession(),
        //);
        //
        //Mage::dispatchEvent('adminhtml_sales_order_create_process_data_before', $eventData);
        //
        //
        ////Mage::getSingleton('adminhtml/session_quote')->setCustomerId();
        //
        //
        //Mage::getSingleton('adminhtml/session_quote')->setCustomerId((int) $customerId);
        //
        //$productHelper = Mage::helper('catalog/product');
        //foreach ($quote->getItems() as $id => $item) {
        //    $buyRequest = new Varien_Object($item);
        //    $params = array('files_prefix' => 'item_' . $item->getProductId() . '_');
        //    $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
        //    if ($buyRequest->hasData()) {
        //        $items[$item->getProductId()] = $buyRequest->toArray();
        //    }
        //    
        //    $product = Mage::getModel('catalog/product')
        //    ->setStoreId(Mage::getSingleton('adminhtml/session_quote')->getStoreId())
        //    ->load($item->getProductId());
        //    
        //     if ($product->getId()) {
        //    $product->setSkipCheckRequiredOption(true);
        //    $buyRequest = $item->getBuyRequest();
        //    if (is_numeric($qty)) {
        //        $buyRequest->setQty($qty);
        //    }
        //    $item1 = Mage::getSingleton('adminhtml/session_quote')->getQuote()->addProduct($product, $buyRequest);
        //    
        //    if (is_string($item1)) {
        //        return $item1;
        //    }
        //
        //    if ($additionalOptions = $item->getProductOptionByCode('additional_options')) {
        //        $item1->addOption(new Varien_Object(
        //            array(
        //                'product' => $item1->getProduct(),
        //                'code' => 'additional_options',
        //                'value' => serialize($additionalOptions)
        //            )
        //        ));
        //    }
        //
        //    
        //    
        //}
        
        $_order = Mage::getModel('sales/order')->loadByIncrementId($quote->getIncrementId());
      
      
	    if($_order->getId() != '')
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('The order allready exist for this quote'));
            $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quote_id));
        }
        else{
                $customer = Mage::getModel('customer/customer')->load($customerId);
        
                $transaction = Mage::getModel('core/resource_transaction');
                $storeId = $customer->getStoreId();
                //$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
                
                $reservedOrderId = $quote->getIncrementId();
                
                
                
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
                
				
				 
                
                /****************** Start to add billing and shipping to order 10_02_2014 **************************/
                $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                $temptableQuoteBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
                $temptableQuoteShiping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
                
                $sqlBill = $connectionRead->select()
                                ->from($temptableQuoteBilling, array('*'))
                                ->where('quotation_id=?', $quote->getId());
                $chkBill = $connectionWrite->fetchRow($sqlBill);
                
                $sqlShip = $connectionRead->select()
                                ->from($temptableQuoteShiping, array('*'))
                                ->where('quotation_id=?', $quote->getId());
                $chkShip = $connectionWrite->fetchRow($sqlShip);
                
                //Start 11_03_2014
                $countryCode = Mage::getStoreConfig('general/country/default');
                //echo "code :".$countryCode;
                $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
                
          foreach($regionCollection as $region) {
                    
                  if($chkBill['region_id'] == $region['name'] or $chkBill['region_id'] == $region['code'])
                    {
                        $chkBill['region_id'] = $region['region_id'];
                    }
                    
                    
                  if($chkShip['region_id'] == $region['name'] or $chkShip['region_id'] == $region['code'])
                    {
                        $chkShip['region_id'] = $region['region_id'];
                    }
                }
                //End 11_03_2014
                
                /****************** End to add billing and shipping to order 10_02_2014 **************************/
                
                // set Billing Address
                $billing = $customer->getDefaultBillingAddress();
		//if($billing){
		// exit('no billing address');
                
                
                //$billingAddress = Mage::getModel('sales/order_address')
                //->setStoreId($storeId)
                //->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
                //->setCustomerId($customer->getId())
                //->setCustomerAddressId($customer->getDefaultBilling())
                //->setCustomer_address_id($billing->getId())
                //->setPrefix($billing->getPrefix())
                //->setFirstname($billing->getFirstname())
                //->setMiddlename($billing->getMiddlename())
                //->setLastname($billing->getLastname())
                //->setSuffix($billing->getSuffix())
                //->setCompany($billing->getCompany())
                //->setStreet($billing->getStreet())
                //->setCity($billing->getCity())
                //->setCountry_id($billing->getCountryId())
                //->setRegion($billing->getRegion())
                //->setRegion_id($billing->getRegionId())
                //->setPostcode($billing->getPostcode())
                //->setTelephone($billing->getTelephone())
                //->setFax($billing->getFax());
                
              if($billing)
                $bill_id = $billing->getId();
                
                $billingAddress = Mage::getModel('sales/order_address')
                ->setStoreId($storeId)
                ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
                ->setCustomerId($customer->getId())
                ->setCustomerAddressId($customer->getDefaultBilling())
                ->setCustomer_address_id($bill_id)
                //->setPrefix($billing->getPrefix())
                ->setFirstname($chkBill['firstname'])
                //->setMiddlename($billing->getMiddlename())
                ->setLastname($chkBill['lastname'])
                //->setSuffix($billing->getSuffix())
                ->setCompany($chkBill['company'])
                ->setStreet($chkBill['street1'].' , '.$chkBill['street2'])
                ->setCity($chkBill['city'])
                ->setCountry_id($chkBill['country_id'])
                ->setRegion($chkBill['region'])//11_03_2014
                ->setRegion_id($chkBill['region_id'])
                ->setPostcode($chkBill['postcode'])
                ->setTelephone($chkBill['telephone']);
                $order->setBillingAddress($billingAddress);
                //}else{
                //    $billingAddress = $billingAddress = Mage::getModel('sales/order_address')
                //    ->setStoreId($storeId)
                //    ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING);
                //    $order->setBillingAddress($billingAddress);
                //}
                
                $shipping = $customer->getDefaultShippingAddress();
               // if($shipping){
		// exit('no shipping address');

                //$shippingAddress = Mage::getModel('sales/order_address')
                //->setStoreId($storeId)
                //->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
                //->setCustomerId($customer->getId())
                //->setCustomerAddressId($customer->getDefaultShipping())
                //->setCustomer_address_id($shipping->getId())
                //->setPrefix($shipping->getPrefix())
                //->setFirstname($shipping->getFirstname())
                //->setMiddlename($shipping->getMiddlename())
                //->setLastname($shipping->getLastname())
                //->setSuffix($shipping->getSuffix())
                //->setCompany($shipping->getCompany())
                //->setStreet($shipping->getStreet())
                //->setCity($shipping->getCity())
                //->setCountry_id($shipping->getCountryId())
                //->setRegion($shipping->getRegion())
                //->setRegion_id($shipping->getRegionId())
                //->setPostcode($shipping->getPostcode())
                //->setTelephone($shipping->getTelephone())
                //->setFax($shipping->getFax());
                
              if($shipping)
                $ship_id = $shipping->getId();
                
                $shippingAddress = Mage::getModel('sales/order_address')
                ->setStoreId($storeId)
                ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
                ->setCustomerId($customer->getId())
                ->setCustomerAddressId($customer->getDefaultShipping())
                ->setCustomer_address_id($ship_id)
                 //->setPrefix($billing->getPrefix())
                ->setFirstname($chkShip['firstname'])
                //->setMiddlename($billing->getMiddlename())
                ->setLastname($chkShip['lastname'])
                //->setSuffix($billing->getSuffix())
                ->setCompany($chkShip['company'])
                ->setStreet($chkShip['street1'].' , '.$chkShip['street2'])
                ->setCity($chkShip['city'])
                ->setCountry_id($chkShip['country_id'])
                ->setRegion($chkShip['region'])//11_03_2014
                ->setRegion_id($chkShip['region_id'])
                ->setPostcode($chkShip['postcode'])
                ->setTelephone($chkShip['telephone']);
               
               /////14-2-2014 gc S 24-2-2014 S
               //$shipMethod=array(
               //                 "australiapost_EXPRESS"=>"Australia Post/ Express",
               //                 "flatrate_flatrate"=>"Flat Rate - Fixed",
               //                 "freeshipping_freeshipping"=>"Free Shipping",
               //                 "excellence_73"=>"TNT SHIPPING/ONFC Satchel",
               //                 "excellence_75"=>"TNT SHIPPING/Overnight Express",
               //                 "excellence_EX12"=>"TNT SHIPPING/12:00 Express",
               //                 "excellence_717B"=>"TNT SHIPPING/Technology Express - Sensitive Express",
               //                 "excellence_76" => "TNT SHIPPING/Road Express",
               //                 "excellence_48N-"=>"TNT SHIPPING/Economy Express-",
               //                 "excellence_15N-" => "TNT SHIPPING/Express-",
               //                 "excellence_15N-PR" =>"TNT SHIPPING/Express-Priority"
               //                 
               //                  );
           if($quote->getShippingMethod() == 'flatrate_flatrate'){
                    $shipDes = 'Flat Rate - Fixed';
               }elseif($quote->getShippingMethod() == 'freeshipping_freeshipping') {
                    $shipDes = 'Free Shipping';
               }elseif($quote->getShippingMethod() == 'australiapost_EXPRESS'){
                    $shipDes = 'Australia Post/ Express';
                
               }elseif($quote->getShippingMethod() == 'australiapost_STANDARD'){
                    $shipDes = 'Australia Post/ Standard';
                
               }elseif($quote->getShippingMethod() == 'australiapost_STANDARD_EC'){
                    $shipDes = 'Australia Post/ Standard with Extra Cover';
                
               }elseif($quote->getShippingMethod() == 'australiapost_EXPRESS_EC'){
                    $shipDes = ' Australia Post/ Express with Extra Cover';
                
               }
               
               elseif(strpos($quote->getShippingMethod(),"excellence_73") !== false ){
                    $shipDes = 'TNT SHIPPING/ONFC Satchel';
               }elseif(strpos($quote->getShippingMethod(),"excellence_75") !== false){
                    $shipDes = 'TNT SHIPPING/Overnight Express';
               }elseif(strpos($quote->getShippingMethod(),"excellence_EX12") !== false){
                    $shipDes = 'TNT SHIPPING/12:00 Express';
               }elseif(strpos($quote->getShippingMethod(),"excellence_EX10") !== false){
                    $shipDes = 'TNT SHIPPING/10:00 Express';
               }elseif(strpos($quote->getShippingMethod(),"excellence_712") !== false){
                    $shipDes = 'TNT SHIPPING/9:00 Express';
               }
               
               elseif(strpos($quote->getShippingMethod(),"excellence_717B") !== false){
                    $shipDes = 'TNT SHIPPING/Technology Express - Sensitive Express';
               }elseif(strpos($quote->getShippingMethod(),"excellence_76") !== false){
                    $shipDes = 'TNT SHIPPING/Road Express';
               }elseif(strpos($quote->getShippingMethod(),"excellence_48N-") !== false){
                    $shipDes = 'TNT SHIPPING/Economy Express';
               }elseif(strpos($quote->getShippingMethod(),"excellence_15N-") !== false){
                    $shipDes = 'TNT SHIPPING/Express';
               }elseif(strpos($quote->getShippingMethod(),"excellence_15N-PR") !== false){
                    $shipDes = 'TNT SHIPPING/Express-Priority';
               }else{
                $shipDes = $quote->getShippingMethod();
             }
               
               
               
               
               
               
               
               ///$shipDes = $shipMethod[$quote->getShippingMethod()];
               
               /////14-2-2014 gc E 24-2-2014 E
               
          $order->setShippingAddress($shippingAddress)
                ->setShippingMethod($quote->getShippingMethod())
                ->setShippingAmount($quote->getShippingRate())             /////14-2-2014 gc added
                ->setShippingDescription($shipDes);
                
                //} else{
                //    $shippingAddress = Mage::getModel('sales/order_address')
                //    ->setStoreId($storeId)
                //    ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);
                //    $order->setShippingAddress($shippingAddress);
                //}
                
           $orderPayment = Mage::getModel('sales/order_payment')
                ->setStoreId($storeId)
                ->setCustomerPaymentId(0)
                ->setMethod('free');
                $order->setPayment($orderPayment);
                
                // let say, we have 2 products
                $subTotal = 0;
                $total_qty_ordered = 0;
                
       $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                
       $temptableOrderItem = Mage::getSingleton('core/resource')
											->getTableName('sales_flat_order_item');
        
                
       $select = $connectionRead->select()
                ->from($temptableOrderItem, array('item_id'))
                ->order('item_id DESC')
                ->limit(1);
                //$row =$connectionRead->fetchRow($select);
                $result = $connectionRead->fetchRow($select);
                
                 $last_item_id = $result['item_id'];
               
         //$products = array('1' => array('qty' => 1),'2' =>array('qty' => 1));
         foreach ($quote->getItems() as $productId=>$product) {
                $_product = Mage::getModel('catalog/product')->load($product->getProductId());
                
                $last_item_id++;   
                
             if($_product->getTypeId() == 'bundle')
                    {
            $final_price = $_product->getPrice();        
			if($_product->getSpecial_price() != '' ){
				$final_price = $_product->getSpecial_price();
				}
			    
			/********************** Start for bundle product 12_02_2014 ********************************/
                  $rowTotal = $final_price * $product['qty'];
				  
                  $orderItem = Mage::getModel('sales/order_item')
                                        ->setStoreId($storeId)
                                        ->setQuoteItemId(0)
                                        ->setQuoteParentItemId(NULL)
                                        ->setProductId($product->getProductId())
                                        ->setProductType($_product->getTypeId())
                                        ->setQtyBackordered(NULL)
                                        ->setProductOptions(Mage::getModel('bundle/product_type')->getOrderOptions($_product))//19_02_2014
                                        ->setTotalQtyOrdered($product['qty'])
                                        ->setQtyOrdered($product['qty'])
                                        ->setName($_product->getName())
                                        ->setSku($_product->getSku())
                                        ->setPrice($final_price)
                                        ->setBasePrice($final_price)
                                        ->setOriginalPrice($final_price)
                                        ->setRowTotal($rowTotal)
                                        ->setBaseRowTotal($rowTotal);
                                        
                            $total_qty_ordered += $product['qty'];
                            $subTotal += $rowTotal;
                            $parentItem = $order->addItem($orderItem);
                            
                            $temptableItem=Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
                            $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                            
                            $sqlItem = $connectionRead->select()
                                            ->from($temptableItem, array('*'))
                                            ->where("parent_item_id = '".$product->getQuotationItemId()."' ");
                            $chkItem = $connectionRead->fetchAll($sqlItem);
                            $it = 0;
                            foreach($chkItem as $bItem)
                            {
                                $product_bundle = Mage::getModel('catalog/product')->load($bItem['product_id']);
                                
			$final_price = $product_bundle->getPrice();        
			if($product_bundle->getSpecial_price() != '' ){
				$final_price = $product_bundle->getSpecial_price();
				}	
								
								$rowTotal = $product_bundle->getPrice() * $product['qty'];
                                $orderItem = Mage::getModel('sales/order_item')
                                        ->setStoreId($storeId)
                                        ->setQuoteItemId(0)
                                        ->setParentItemId($last_item_id)
                                        ->setProductId($bItem['product_id'])//13_02_2014
                                        ->setProductType($product_bundle->getTypeId())
                                        ->setQtyBackordered(NULL)
                                        ->setProductOptions(Mage::getModel('bundle/product_type')->getOrderOptions($_product))//19_02_2014
                                        ->setTotalQtyOrdered($bItem['qty'])
                                        ->setQtyOrdered($product['qty'])
                                        ->setName($product_bundle->getName())
                                        ->setSku($product_bundle->getSku())
                                        ->setPrice($product_bundle->getSpecial_price()?$product_bundle->getSpecial_price():$product_bundle->getPrice())
                                        ->setBasePrice($product_bundle->getSpecial_price()?$product_bundle->getSpecial_price():$product_bundle->getPrice())
                                        ->setOriginalPrice($product_bundle->getSpecial_price()?$product_bundle->getSpecial_price():$product_bundle->getPrice())
                                        ->setRowTotal($rowTotal)
                                        ->setBaseRowTotal($rowTotal);
                                $order->addItem($orderItem);
                                
                                $it++;
                            }
                            $last_item_id = $last_item_id + $it;
 
 /********************** End for bundle product 12_02_2014 **********************************/
                    }
                    else
                    {
                  
                       
					  $final_price = $_product->getPrice();        
						if($_product->getSpecial_price() != '' ){
							$final_price = $_product->getSpecial_price();
						}
						$rowTotal = $final_price * $product['qty'];
					   
					    $orderItem = Mage::getModel('sales/order_item')
                                    ->setStoreId($storeId)
                                    ->setQuoteItemId(0)
                                    ->setQuoteParentItemId(NULL)
                                    ->setProductId($product->getProductId())
                                    ->setProductType($_product->getTypeId())
                                    ->setQtyBackordered(NULL)
                                    ->setTotalQtyOrdered($product['qty'])
                                    ->setQtyOrdered($product['qty'])
                                    ->setName($_product->getName())
                                    ->setSku($_product->getSku())
                                    ->setPrice($final_price)
                                    ->setBasePrice($final_price)
                                    ->setOriginalPrice($final_price)
                                    ->setRowTotal($rowTotal)
                                    ->setBaseRowTotal($rowTotal);
                                    
                                    $total_qty_ordered += $product['qty'];
                                    $subTotal += $rowTotal;
                                    $order->addItem($orderItem);
                    }
                }
                $grandTotal = $subTotal + $quote->getShippingRate();  ///14-2-2014 gc added
                $order->setSubtotal($quote->getPriceHt())
                ->setBaseSubtotal($quote->getPriceHt())
                ->setGrandTotal($quote->GetFinalPriceWithTaxes())
                ->setTotalQtyOrdered($total_qty_ordered)
                ->setBaseTaxAmount($quote->GetTaxAmount())
                ->setTaxAmount($quote->GetTaxAmount())
                ->setBaseGrandTotal($quote->GetFinalPriceWithTaxes());
                
                       
                $order->save;
                
                
                
                $transaction->addObject($order);
                $transaction->addCommitCallback(array($order, 'place'));
                $transaction->addCommitCallback(array($order, 'save'));
                $transaction->save();
                
                //start to save delivery date 07_03_2014
                if($chkShip['inhand'] != '0000-00-00')
                {
                    $orderAttributes = Mage::getModel('amorderattr/attribute');
                    $orderAttributes->load($order->getId(), 'order_id');
                    $orderAttributes->setData('delivery_date', $chkShip['inhand']);
                    $orderAttributes->save();
                }
                //end to save delivery date 07_03_2014
                
               
                
                // For adding the comment masage from quote to order   
                $temptableHistory = Mage::getSingleton('core/resource')->getTableName('quotation_history');
                $temptableComment = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHistory))
                {
                   $sqlHistory ="SELECT * FROM ".$temptableHistory." WHERE qh_quotation_id = '".$quote_id."'";
                   $chkHistory = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlHistory);
                   
                   foreach($chkHistory as $history)
                   {
                       
                       $sqlComment="INSERT INTO ".$temptableComment." SET parent_id = '".$order->getId()."', 
                                      comment = '".strtoupper($history['qh_user'])." - ".addslashes($history['qh_message'])."' ,
                                      created_at ='".$history['qh_date']."', entity_name = 'order'";
                                      
                       $chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
                   }
                }
                
                //$invoice = $order->prepareInvoice();
                //$invoice->register();
                //Mage::getModel('core/resource_transaction')
                //  ->addObject($invoice)
                //  ->save();
                //
                //$invoice->sendEmail(true, '');
                
                $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                
                $temptableDesign = Mage::getSingleton('core/resource')->getTableName('task_designer');        
                $temptableItems = Mage::getSingleton('core/resource')->getTableName('quotation_items');
				
                $select = $connectionRead->select()
                ->from($temptableItems, array('*'))
                ->where('quotation_id=?',$quote_id);
                //$row =$connectionRead->fetchRow($select);
                $result = $connectionRead->fetchAll($select);
                
                    
                //foreach($result as $quoteitem)
                //{
                //    $tableItemName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                //    
                //    $select = $connectionRead->select()
                //    ->from($tableItemName, array('*'))
                //    ->where('order_id=?',$order->getId())
                //    ->where('product_id=?',$quoteitem['product_id']);
                //    $row =$connectionRead->fetchRow($select);
                //    
                //
                //    $connectionWrite->beginTransaction();
                //    $data3 = array();
                //    $data3['order_quote_id'] = $order->getId();
                //    $data3['proof_type'] = 'order';
                //    $data3['item_id'] = $row['item_id'];
                //    $where1 = $connectionWrite->quoteInto("order_quote_id =? AND proof_type='quote' AND item_id = '".$quoteitem['quotation_item_id']."'", $quote_id);
                //    $connectionWrite->update($temptableDesign, $data3, $where1);
                //    $connectionWrite->commit();
                //    
                //    $temptableProofs = Mage::getSingleton('core/resource')->getTableName('proofs');
                //    
                //    $connectionWrite->beginTransaction();
                //    $data4 = array();
                //    $data4['order_id'] = $order->getId();
                //    $data4['proof_type'] = 'order';
                //    $data4['item_id'] = $row['item_id'];
                //    $where1 = $connectionWrite->quoteInto("order_id =? AND proof_type='quote' AND item_id = '".$quoteitem['quotation_item_id']."'", $quote_id);
                //    $connectionWrite->update($temptableProofs, $data4, $where1);
                //    $connectionWrite->commit();
                //}
                
                /********************** Set vendor in item table *******************************/
//                $items = $order->getAllItems();
//                foreach ($items as $item) {
//                    
//                    $select = $connectionRead->select()
//                    ->from($temptableProofs, array('*'))
//                    ->where('order_id=?',$order->getId())
//                    ->where('item_id=?',$item->getId())
//                    ->where('proof_type=?','order');
//                    //$row =$connectionRead->fetchRow($select);
//                    $result = $connectionRead->fetchAll($select);
//                    
//                    if(count($result) > 0)
//		    $file_recieved = 'yes';
//		    else
//		    $file_recieved = 'no';
//		    
//		    if($result[0]['status'] == 'Approved')
//		    $proof_approved = 'yes';
//		    else
//		    $proof_approved = 'no';
//                    
//                                      
//                    $temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_item');
//                    
//                    $_product = Mage::getModel('catalog/product')->load($item->getProductId());
//                    
//                    $name = $_product->getAttributeText('vendor_id');
//                    $target_vendor = Mage::getResourceModel('catalog/product')->getAttribute("vendor_id")->getSource()->getOptionId($name);
//                    
//                    $connectionWrite->beginTransaction();
//                    $data2 = array();
//                    $data2['target_user']= $target_vendor;
//                    $data2['item_id'] = $item->getId();
//                    $data2['order_id'] = $order->getId();
//                    $data2['product_id'] = $item->getProductId();
//                    $data2['product_sku'] = addslashes($_product->getSku());
//                    $data2['postdate'] = NOW();
//                    $data2['order_status'] = $order->getStatus();
//                    
//                    $data2['file_recieved'] = $file_recieved;
//		    $data2['proof_approved'] = $proof_approved;
//		    $data2['proof_approve_date'] = Now();
//                    if($row['quantity'] != '')
//		    $data2['qty'] = $result[0]['quantity'];
//                    
//                    $connectionWrite->insert($temptableVendor, $data2);
//                    $connectionWrite->commit();
//                    
//                    
//                    /*********************** add planning auto *********************************/
//                        $ProductId = $item->getProductId();
//                        $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
//                        if($connectionWrite->isTableExists($temptableShipping))
//                        {
//                            if($connectionWrite->isTableExists($temptableShipping))
//                            {
//                                //$sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ";
//                                $sqlShipping = $connectionRead->select()
//                                                ->from($temptableShipping, array('*'))
//                                                ->where("quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ");
//                                $chkShipping = $connectionRead->fetchAll($sqlShipping);
//                            }
//                            
//                            if(count($chkShipping) == 0)
//                            {
//                            
//                                $created_date = $order->getCreatedAt();
//                                $Product = Mage::getModel('catalog/product')->load($ProductId);
//                                
//                                $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
//                                //$sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
//                                $sqlTimeline = $connectionRead->select()
//                                                ->from($temptableTimeline, array('*'))
//                                                ->where('product_id=?', $ProductId);
//                                $chkTimeline = $connectionWrite->fetchAll($sqlTimeline);
//                            
//                                $order_placed_date =  $created_date;
//                                
//                              
//                                    $order_placed_date = '';
//                                     $artwork_date = '';
//                                    $proof_date = '';
//                                    $production_start_date = '';
//                                    $shipping_date = '';
//                                    $delivery_date = '';
//                                
//                                
//                                                  
//                                $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
//                                if($connectionWrite->isTableExists($temptableShipping))
//                                {
//                                    //$sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$order->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'order', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
//                                    //$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
//                                    $connectionWrite->beginTransaction();
//                                    $data = array();
//                                    $data['quote_id']= $order->getId();
//                                    $data['item_id'] = $item->getId();
//                                    $data['product_id'] = $ProductId; 
//                                    $data['planning_type'] = 'order'; 
//                                    $data['order_placed_date'] = $order_placed_date; 
//                                    $data['artwork_date'] = $artwork_date; 
//                                    $data['proof_date'] = $proof_date; 
//                                    $data['start_date'] = $production_start_date; 
//                                    $data['shipping_date'] = $shipping_date; 
//                                    $data['delivery_date']= $delivery_date; 
//                                    $connectionWrite->insert($temptableShipping, $data);
//                                    $connectionWrite->commit();
//                                }
//                            }
//                        }
//                  
//                
//            
//                /*********************** add planning auto *********************************/
//		                        
//                    
//                }
                /********************** Set vendor in item table *******************************/
                $task_designer_table = Mage::getSingleton('core/resource')->getTableName('task_designer');
                $task_designer_chat_table = Mage::getSingleton('core/resource')->getTableName('task_designer_chat');
				
				$data = array('order_quote_id'=>$order->getEntity_id(),
							   'proof_type'=>'customer',
							  );
				$where  = $connectionWrite->quoteInto('order_quote_id =?',$quote_id);			  
				$connectionWrite->update($task_designer_table, $data, $where );				
                
                //$alert=array('Create order From Quote');
                //Mage::getModel('systemalert/systemalert')->sendalert($alert,'order',$order);
                
                // For deleting the quote
                if($order->getId() != '')
                {
                   $quote = Mage::getModel("Quotation/Quotation")->load($quote_id);
                   $quote->delete();
                }
                
               // print_r($orderItem);
                
              
                Mage::dispatchEvent('model_save_after', array('object'=>Mage::getModel("Quotation/Quotation")));
                mage::helper('AdminLogger')->updatelog($quote_id,'Create order From Quote');
                
                
                       
               // exit();
                $url = Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view/order_id/".$order->getId());
                $url = str_replace('p//s','p/admin/s',$url);
                //exit();
                Mage::log($url);
                Mage::app()->getResponse()->setRedirect($url);
                //exit;
        }
    }
    
        public function UpdateAction()
	{
		//create planning
                
                extract($_REQUEST);
                
		$quoteId = $_REQUEST['quote_id'];
		
		try 
		{
			$quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
			$created_date = $quote->getCreated_time();
			foreach($order_date as $key=>$value)
                        {
			
                            $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                            if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                            {
                            $sqlShipping="UPDATE  ".$temptableShipping." SET order_placed_date = '$value', artwork_date = '$artwork[$key]', proof_date = '$proof[$key]', start_date ='$start[$key]', shipping_date = '$shipping_date[$key]', delivery_date = '$delivery_date[$key]' WHERE entity_id = '".$key."'";
                            $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                            }
                        }
			
                        Mage::dispatchEvent('model_save_after', array('object'=>Mage::getModel("Quotation/Quotation")));
                        mage::helper('AdminLogger')->updatelog($quoteId,'Edit Planning In Quote');
			
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Planning created'));
		}
		catch (Exception $ex)
		{
			Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
		}
                
               
		
		//redirect
    	$this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteId));
    	
	}
    

   public function updateDatesAction(){
        $quote_id=$this->getRequest()->getParam('quote_id');
        $inputid=$this->getRequest()->getParam('objectid');
        $retdate=$this->getRequest()->getParam('objectval');
        if(preg_match('/[0-9]{2,5}$/',$inputid,$matchArr)){
                $entity_id=$matchArr[0];

                preg_match('/^[a-z_]+/',$inputid,$matchArr2);
                $inputkey=trim($matchArr2[0],"_");

        $startkey=0;
        $return=array();
        $seq=array('order_date','artwork','proof','start','shipping_date','delivery_date');
                $sqlflip=array_flip($seq);
                $startkey=($sqlflip[$inputkey])+1;


                            $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                            if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                            {

                $sqlShipping="SELECT product_id FROM  ".$temptableShipping." WHERE quote_id = '".$quote_id."' AND entity_id ='".$entity_id."'  AND planning_type = 'quote' ";
                $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($sqlShipping);
                }
                if(count($chkShipping) >= 0)
                {
                   $ProductId=$chkShipping[0];
                    $Product = Mage::getModel('catalog/product')->load($ProductId);

                    $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableTimeline))
                    {
                    $sqlTimeline="SELECT *,artwork_day as artwork,production_day as start,proof_day as proof, sunday_production as sunday_start, holiday_production as holiday_start, shipping_day as shipping_date, sunday_shipping as sunday_shipping_date, holiday_shipping as holiday_shipping_date,delivary_day as delivery_date,sunday_delivary as sunday_delivery_date , holiday_delivary as holiday_delivery_date  FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
                    $chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTimeline);
                    }

			if($chkTimeline)
                        for($k=$startkey;$k<sizeof($seq);$k++){
                                $key=$seq[$k];
                                    $return[$key][0] = $retdate= $this->gettimelinedate($chkTimeline[0][$key],$retdate,$chkTimeline[0]['sunday_'.$key],$chkTimeline[0]['holiday_'.$key]);
                                    $return[$key][1] = date("d-M-y",strtotime($retdate));

                                    /*$proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$artwork_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                                    $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$proof_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                                    $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$production_start_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                                    $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$shipping_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                                        */
                        }
                        $return['objid']=$entity_id;
                }
                        echo json_encode($return);

        }else{
                echo 'notfound';
        }
   }
   
   public function addCommentAction()
   {
     //print_r($_REQUEST);
     
     extract($_REQUEST);
     
     
    $user = Mage::getSingleton('admin/session');
    $userName = $user->getUser()->getUsername();
    
    $temptableHistory=Mage::getSingleton('core/resource')->getTableName('quotation_history');
	$temptableQuotation=Mage::getSingleton('core/resource')->getTableName('quotation');
    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHistory))
    {
        
        $sqlHistory="INSERT INTO ".$temptableHistory." SET qh_quotation_id = '".$quote_id."', qh_message = '".$comment."', qh_date = NOW(), qh_user = '".$userName."' ";
        
		//update the history of messages 
		
		$sqlmessages = 'update '.$temptableHistory.' set `qh_readstatus`=0 WHERE `qh_quotation_id`='.$quote_id;
		Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlmessages);
		
		///checking history of message
		//$chkHistory = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlHistory);
		
		 
		$sqlquotemessages = 'update '.$temptableQuotation.' set `readstatus`=0 WHERE `quotation_id`='.$quote_id;
		Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlquotemessages);
		
		///checking history of message
		$chkHistory = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlHistory);
		
		
    }
    
   
    
    $quote = Mage::getModel('Quotation/Quotation')->load($quote_id);
    
    if($notify != '')
   
    
    $comments_history = '';
	$counter = 0;
	
	foreach ($quote->getHistory()->setOrder('qh_id', 'desc') as $_item)
        {
			 
            $comments_history .= '<li>
                <strong>'.Mage::helper('core')->formatDate($_item->getCreatedAtDate(), 'medium').'></strong>
                '.Mage::helper('core')->formatTime($_item->getCreatedAtDate(), 'medium').'<span class="separator">|</span><strong>'.$_item->getStatusLabel() .'</strong><br/><small>'.$_item->getqh_user().'
               </small>
                
                    <br/>';
					$item_message = $_item->getqh_message();
           $comments_history .=  $item_message;   
          
		   $comments_history .= '</li>';
			
			
			if($counter<1){
			
			$comment_email ='<ul><li>
                <strong>'.Mage::helper('core')->formatDate($_item->getCreatedAtDate(), 'medium').'></strong>
                '.Mage::helper('core')->formatTime($_item->getCreatedAtDate(), 'medium').'<span class="separator">|</span><strong>'.$_item->getStatusLabel() .'</strong><br/><small>'.$_item->getqh_user().'
               </small>
                
                    <br/>';
              $comment_email .=$item_message;  
            $comment_email .='</li></ul>';
			}
			$counter++;
			
        }
		 echo $comments_history;	//echo $comment_email;
		
		if($notify==1){
		 //end comments history to the client 
		  Mage::getModel('Quotation/Quotation_Notification')->chatNotify($comment_email,$quote_id);
		}
   }
   
   public function pricechangeAction()
   {
        extract($_REQUEST);
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $temptableItems = Mage::getSingleton('core/resource')->getTableName('quotation_items');
                
        $select = $connectionRead->select()
        ->from($temptableItems, array('*'))
        ->where('quotation_item_id=?',$bundle_id);
        //$row =$connectionRead->fetchRow($select);
        $result = $connectionRead->fetchRow($select);
                
        $Product = Mage::getModel('catalog/product')->load($result['product_id']);
        if($Product->getPriceType() != '')
        {
            if($type == 'del')
            {
                $sub_product = Mage::getModel('catalog/product')->load($product_id);
                $price1 = $sub_product->getPrice();
                echo $netprice = $price-$price1;
            }
            else if($type == 'add')
            {
                $sub_product = Mage::getModel('catalog/product')->load($product_id);
                $price1 = $sub_product->getPrice();
                echo $netprice = $price+$price1;
            }
        }
        
   }
   
    ///save shipping and save billing
    public function saveshippingAction(){
	   
	   $resource = Mage::getSingleton('core/resource');
	   $table_shipping = $resource->getTableName('quotation_shipping');
	   $connectionWrite = $resource->getConnection('core_write');
	   $connectionRead = $resource->getConnection('core_read');
	    
	   $_quote_obj = $this->getRequest()->getPost('quote_data');
	   $_quotation_id = $this->getRequest()->getPost('quotation_id');
	   $_quote_data = array();
	   
	   foreach($_quote_obj as $_quote){
		   $_quote_data [$_quote['name']] = $_quote['value'];
		   }
		    //  var_dump($_quote_data);
		    $cquote = Mage::getModel('Quotation/Quotation')->load($_quotation_id);
			$customer = $cquote->getCustomer();
			//var_dump($customer);
			$email = $customer->getEmail();	   
	        $data = array(
	   			'firstname'=>$_quote_data['quote[shipping_address][firstname]'],
				'lastname'=>$_quote_data['quote[shipping_address][lastname]'],
				'email'=>$email,
				'company'=>$_quote_data['quote[shipping_address][company]'],
				'phone'=>$_quote_data['quote[shipping_address][telephone]'],
				'street1'=>$_quote_data['quote[shipping_address][street][0]'],
				'street2'=>$_quote_data['quote[shipping_address][street][1]'],
				'city'=>$_quote_data['quote[shipping_address][city]'],
				'region'=>$_quote_data['quote[shipping_address][region]'],
				'region_id'=>$_quote_data['quote[shipping_address][region_id]'],
				'postcode'=>$_quote_data['quote[shipping_address][postcode]'],
				'country_id'=>$_quote_data['shipping[country_id]'],
				'telephone'=>$_quote_data['quote[shipping_address][telephone]'],
		
	   );	   
	   
	   if($connectionWrite->isTableExists($table_shipping))
           {                            
				//  $connectionWrite->beginTransaction();
				 ///reading if shipping address exists 
				 $cAddress = 0;
				 $address = $connectionRead->query('select * from '.$table_shipping.' where quotation_id='.$_quotation_id);
				 foreach($address as $addr){
					 $cAddress = $addr['entity_id'];
					 $cAddress = 1;
					 }
				 if($cAddress!=1){					 
					 
					 $data['quotation_id']=$_quotation_id;
					 $data['repid']='';
					 $data['hearabout']='';
					 $data ['inhand1'] = date('Y-m-d h:m:s');
					 $data['inhand'] = date('Y-m-d h:m:s'); 
					 
					 $connectionWrite->beginTransaction();
					 $result = $connectionWrite->insert($table_shipping, $data);
					 
					 $connectionWrite->commit();
					  
					 $customAddress = Mage::getModel('customer/address');
                	//$customAddress = new Mage_Customer_Model_Address();
                	$customAddress->setData($data)
                            	  ->setCustomerId($customer->getEntity_id())
                            	 // ->setIsDefaultBilling('1')
                            	    ->setIsDefaultShipping('1')
                            	   ->setSaveInAddressBook('1')
								 ;
             //  var_dump($customAddress);
			    try {
					
                     $customAddress->save();
					 echo 1;
                }
                catch (Exception $ex) {
                    //Zend_Debug::dump($ex->getMessage());
					echo 0;
                }
					 
					 
					 
			}else{
				  $where = $connectionWrite->quoteInto('quotation_id =?', $_quotation_id);
				  $result = $connectionWrite->update($table_shipping,$data, $where);
				  if($result){
					      echo 1;
					  }else{
						  echo 0;
						  }
				 // $connectionWrite->commit();
			}
         }
	   
	   
	    
		/*
		 $customAddress = Mage::getModel('customer/address');
                //$customAddress = new Mage_Customer_Model_Address();
                $customAddress->setData($_custom_address)
                            ->setCustomerId($customer->getId())
                            ->setIsDefaultBilling('1')
                            ->setIsDefaultShipping('1')
                            ->setSaveInAddressBook('1');
                try {
                    $customAddress->save();
                }
                catch (Exception $ex) {
                    //Zend_Debug::dump($ex->getMessage());
                }
				*/
	   
	   } //end of save shipping data
	   
	///save shipping and save billing
    public function savebillingAction(){
	   
	   $resource = Mage::getSingleton('core/resource');
	   $table_billing = $resource->getTableName('quotation_billing');
	   $connectionWrite = $resource->getConnection('core_write');
	   $connectionRead = $resource->getConnection('core_read');
	    
	   $_quote_obj = $this->getRequest()->getPost('quote_data');
	   $_quotation_id = $this->getRequest()->getPost('quotation_id');
	   $_quote_data = array();
	   
	   foreach($_quote_obj as $_quote){
		   $_quote_data [$_quote['name']] = $_quote['value'];
		   }
		//  var_dump($_quote_data);
		 $cquote = Mage::getModel('Quotation/Quotation')->load($_quotation_id);
			$customer = $cquote->getCustomer();
			$email = $customer->getEmail();	   
	   $data = array(
	   			'firstname'=>$_quote_data['quote[billing_address][firstname]'],
				'lastname'=>$_quote_data['quote[billing_address][lastname]'],
				'email'=>$email,
				'company'=>$_quote_data['quote[billing_address][company]'],
				'phone'=>$_quote_data['quote[billing_address][telephone]'],
				'street1'=>$_quote_data['quote[billing_address][street][0]'],
				'street2'=>$_quote_data['quote[billing_address][street][1]'],
				'city'=>$_quote_data['quote[billing_address][city]'],
				'region'=>$_quote_data['quote[billing_address][region]'],
				'region_id'=>$_quote_data['quote[billing_address][region_id]'],
				'postcode'=>$_quote_data['quote[billing_address][postcode]'],
				'country_id'=>$_quote_data['billing[country_id]'],
				'telephone'=>$_quote_data['quote[billing_address][telephone]'],
		
	   );	   
	   
	   if($connectionWrite->isTableExists($table_billing))
           {                            
				 // $connectionWrite->beginTransaction();
				 
				 //  $connectionWrite->beginTransaction();
				 ///reading if shipping address exists 
				 $cAddress = 0;
				 $address = $connectionRead->query('select * from '.$table_billing.' where quotation_id='.$_quotation_id);
				 foreach($address as $addr){
					 $cAddress = $addr['entity_id'];
					 $cAddress = 1;
					 }
				 if($cAddress!=1){					 
					 
					 $data['quotation_id']=$_quotation_id;
					 $data['repid']='';
					 $data['hearabout']='';
					 
					 
					 $connectionWrite->beginTransaction();
					 $result = $connectionWrite->insert($table_billing, $data);
					 
					 $connectionWrite->commit();
					  
					 $customAddress = Mage::getModel('customer/address');
                	//$customAddress = new Mage_Customer_Model_Address();
                	$customAddress->setData($data)
                            	  ->setCustomerId($customer->getEntity_id())
                            	  ->setIsDefaultBilling('1')
                            	   // ->setIsDefaultShipping('1')
                            	   ->setSaveInAddressBook('1')
								 ;
             //  var_dump($customAddress);
			    try {
					
                     $customAddress->save();
					 echo 1;
                }
                catch (Exception $ex) {
                    //Zend_Debug::dump($ex->getMessage());
					echo 0;
                }
					 
					 
					 
			}else{
				 
				  $where = $connectionWrite->quoteInto('quotation_id =?', $_quotation_id);
				  $result = $connectionWrite->update($table_billing,$data, $where);
				 
				  if($result){
					      echo 1;
					  }else{
						  echo 0;
						  }
			}
				 // $connectionWrite->commit();
        }
	   
	    
		/*
		 $customAddress = Mage::getModel('customer/address');
                //$customAddress = new Mage_Customer_Model_Address();
                $customAddress->setData($_custom_address)
                            ->setCustomerId($customer->getId())
                            ->setIsDefaultBilling('1')
                            ->setIsDefaultShipping('1')
                            ->setSaveInAddressBook('1');
                try {
                    $customAddress->save();
                }
                catch (Exception $ex) {
                    //Zend_Debug::dump($ex->getMessage());
                }
				*/
	   
	   } //end of save shipping data   
}
