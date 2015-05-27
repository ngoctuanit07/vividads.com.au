<?php
    ob_start();
    include_once '../app/Mage.php';
    Mage::setIsDeveloperMode(true);
    umask(0);
    $store= Mage::app()->getStore($_POST['store_id']);

    Mage::app($store->getCode());
	
	if($_POST['captcha']!=Mage::getSingleton("core/session")->getCaptcha())
	{
		echo "cap_err";
		exit;
	}




  
        if(isset($_POST['shipaddress']))
        {
            $check_shipp_addr=1;
        }
        else
        {
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
            'region_id' => '',
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
            'street'    => array($addr_ship1,$addr_ship2),
            'city'      => $city_ship,
            'region'    => $state_ship,
            'region_id' => '',
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
        //
        
        
        
        $quote = Mage::getModel('sales/quote')->setStoreId($_POST['store_id']);
	
	
    
    //Load Product and add to cart
        
        $qty=$_POST['quantity'];
        $skuu=$_POST["products"];
        
        
        $stock_flag = 0;
        $exist_pro = 0;
        $productName = '';
        for($ii=0;$ii<count($skuu);$ii++)
        {
        
            if(trim($skuu[$ii]) != '' and $qty[$ii] != '')
            {
                $productid = Mage::getModel('catalog/product')->getIdBySku(trim($skuu[$ii]));
                $product = Mage::getModel('catalog/product')->load($productid);
                $params = array(
                       'product' => $productid,
                       'qty' => $qty[$ii]
                      
                       );
                
                    
                $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($product)->getQty();
               
               if($stocklevel > 0)
               {
                $exist_pro = 1;
                $quote->addProduct($product, new Varien_Object($params));
               }
               else
               {
                 $stock_flag = 1;
                 $productName .= $product->getName().', ';
               }
            }
           
        }

    
 
    // Add Billing Address
        
        $quote->getBillingAddress()
              ->addData($billingAddress);

    

    

    
    
    
       
            
            
    /**************************** Start create customer if not exist **********************************/
    $pwd_length = 7; //auto-generated password length
    
    $customer = Mage::getModel('customer/customer');
    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
    $customer->loadByEmail($quote->getBillingAddress()->getEmail());
    
    if(!$customer->getId()) {
    
      //We're good to go with customer registration process
      $customer->setEmail($quote->getBillingAddress()->getEmail()); 
      $customer->setFirstname($fname_bill);
      $customer->setLastname($lname_bill);
      $customer->setPassword($customer->generatePassword($pwd_length));
    
    }
    
    //if process fails, we don't want to break the page
    try{
    
      $customer->save();
      $customer->setConfirmation(null); //confirmation needed to register?
      $customer->save(); //yes, this is also needed
      $customer->sendNewAccountEmail(); //send confirmation email to customer?
    
    } catch(Exception $e){
      // Mage::log($e->__toString());
    }
                    
    /***************************** End create customer if not exist *******************************************/
    
    //Set Customer group As Guest Customer
     $quote->setCheckoutMethod('guest')
            ->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
     
     //$quote->setCustomerId($customer->getId())
     //       ->setCustomerEmail($quote->getBillingAddress()->getEmail());
 
 
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
	
	
        //Add Shipping Address and set shipping method and payment method
    
    
    
        if($check_shipp_addr==1)
        {
            $quote->getShippingAddress()
                ->addData($shippingAddress)
                ->setCollectShippingRates(true)
		->collectShippingRates()
		->setShippingMethod('flatrate_flatrate');
	    
	//    $quote->getShippingAddress()->addData($shippingAddress)->setCollectShippingRates(true)->collectShippingRates()
	//	    ->setShippingMethod('flatrate_flatrate');
            
        }
        else
        {
            $quote->getShippingAddress()
                ->addData($billingAddress)
                ->setCollectShippingRates(true)
		->collectShippingRates()
		->setShippingMethod('flatrate_flatrate');
		
	//    $quote->getShippingAddress()->addData($billingAddress)->setCollectShippingRates(true)->collectShippingRates()
	//	    ->setShippingMethod('flatrate_flatrate');
        }
	
    
    //Set Payment method and payment details
    
        $payment = $quote->getPayment();
        $payment->importData(array('method' => 'checkmo'));
        $quote->getBillingAddress()->setPaymentMethod($payment->getMethod());
        $quote->getShippingAddress()->setPaymentMethod($payment->getMethod());
        $quote->setPayment($payment);
        $quote->collectTotals()->save();

        
        
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
		    
		 
                    $service =  Mage::getModel('sales/service_quote', $quote);
                     //$service->submitAll()
                    $service->submitAll();
		    
		    $order1 = $service->getOrder();
                    //echo $order->getId();
                    $increment_id = $order1->getIncrementId();
            
                   
                    
                    Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "1");
                
                    $order_mail = new Mage_Sales_Model_Order();
                    $order_mail->loadByIncrementId($increment_id);
                    $order_mail->sendNewOrderEmail();
                    
                    // Resource Clean-Up
                    echo "order___";
                    echo $increment_id;
                    echo "___ ";
                    
                    
                    $order = Mage::getModel('sales/order')->load($service->getOrder()->getId());
    
                    $order->sendNewOrderEmail();
                    
                    
                    //Create auto invoice
                    $invoice = $order->prepareInvoice();
                    
                    $invoice->register();
                    Mage::getModel('core/resource_transaction')
                      ->addObject($invoice)
                      ->save();
                    
                    $invoice->sendEmail(true, '');
                    
                    $items = $order->getAllItems();
                    foreach ($items as $item) {
                        
                        $ProductId = $item->getProductId();
                        
                        $incrementId = $order->getIncrementId();
                        
                        if(!strpos($incrementId,'-'))
                        {
                        
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
                                
                                    $order_placed_date =  $created_date;
                                    
                                    $artwork_date = gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                                    $proof_date = gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                                    $production_start_date = gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                                    $shipping_date = gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                                    $delivery_date = gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                                    
                                                      
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
               echo 'Please select atleast one product.';
           }
        }
        else if($_POST["type"]=="quote")
        {
            if($exist_pro == 1)
            {
                
            if($stock_flag == 0)
            {
               
                    $NewQuotation = Mage::getModel('Quotation/Quotation')
                                ->setcustomer_id($customer->getId())
                                ->setcaption('New request')
                               // ->setcustomer_msg($this->getRequest()->getPost('description'))
                                ->setshipping_method('freeshipping_freeshipping')
                                ->setcustomer_request(1)
                                //->setstatus(MDN_Quotation_Model_Quotation::STATUS_CUSTOMER_REQUEST)
                                ->setstatus(MDN_Quotation_Model_Quotation::STATUS_ACTIVE)
                                ->save();
                                
                        //add products to quote
                        $productName = '';
                       
                   for($ii=0;$ii<count($skuu);$ii++)
                    {
                        if(trim($skuu[$ii]) != '' and $qty[$ii] != '')
                        {
                    
                            $productid = Mage::getModel('catalog/product')->getIdBySku(trim($skuu[$ii]));
                            
                            
                            //add product
                            $quoteItem = $NewQuotation->addProduct($productid, $qty[$ii]);
                            
                            //set options
                            $quoteItem->save();
                        }
                      
                       
                    }
                     
                        echo "quote___";
                        echo $NewQuotation->getIncrementId();
                        echo "___ ";
                        
                    $quote1 = Mage::getModel('Quotation/Quotation')->load($NewQuotation->getId());
                    $quoteItems = $quote1->getItems();
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
                            
                                $order_placed_date =  $created_date;
                                $artwork_date = gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                                $proof_date = gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                                $production_start_date = gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                                $shipping_date = gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                                $delivery_date = gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                                
                                                  
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
                    echo 'Please select atleast one product.';
                }
            
        }
      
         $quote = $customer = $service = null;       
        
        // Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "1");
        //$newOrder = $service->getOrder();
        //$order = new Mage_Sales_Model_Order();
        //$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        //$_order = Mage::getModel('sales/order')->load($increment_id);
        //$_order->sendNewOrderEmail();
        
       //$order->sendNewOrderEmail();
 
 /***************************** Add custom function ***********************************/
     function isweekend($date){
     $date = strtotime($date);
     $date = date("l", $date);
     $date = strtolower($date);
     if($date == "sunday"){
      return 1;
     } else {
      return 0;
     }
    }
    
    function gettimelinedate($day_delay,$created_date,$sunday,$holiday)
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
                
                $d = isweekend($artwork_date);
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
                    
                    if(count($chkHoliday) > 0 or ($sunday == 1 and isweekend($artwork_date) == 1))
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
      
        


?>
