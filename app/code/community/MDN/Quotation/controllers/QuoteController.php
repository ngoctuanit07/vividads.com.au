<?php

class MDN_Quotation_QuoteController extends Mage_Core_Controller_Front_Action {

    /**
     * Check if quote belong to current customer
     * @param <type> $quoteId
     * @return <type>      
     */
    protected function checkQuoteOwner($quote) {
        $customerId = Mage::Helper('customer')->getCustomer()->getId();
        if ($quote->getcustomer_id() != $customerId)
            $this->_redirect('');
    }


    /**
     * Quote view
     */
    public function viewAction() {
        try
        {
            $QuoteId = $this->getRequest()->getParam('quote_id');
            $Quote = Mage::getModel('Quotation/Quotation')->load($QuoteId);
            $this->checkQuoteOwner($Quote);
            $this->loadLayout();
            $this->renderLayout();
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('customer/session')->addError($ex->getMessage());
            $this->_redirect('*/*/List');
        }
    }

    /**
     * Print quote
     */
    public function printquoteAction() {
        $QuoteId = $this->getRequest()->getParam('quote_id');
        $quote = Mage::getModel('Quotation/Quotation')->load($QuoteId);
        $this->checkQuoteOwner($quote);
        try {
            $this->loadLayout();
            $quote->commit();
            $pdf = Mage::getModel('Quotation/quotationpdf')->getPdf(array($quote));
            $name = Mage::helper('quotation')->__('quotation_') . $quote->getincrement_id() . '.pdf';
            $this->_prepareDownloadResponseV2($name, $pdf->render(), 'application/pdf');
        	
			
			} catch (Exception $ex) {
            	
				Mage::getSingleton('checkout/session')->addError($ex->getMessage());
            	$this->_redirect('Quotation/Quote/View', array('quote_id' => $QuoteId));
        }
    }

 /**
     * Print quote
     */
    public function printAction() {
        try {
			  

            $this->loadLayout();
            $error = false;
            $quoteId = $this->getRequest()->getParam('quote_id');
            $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);

            //create bundle product if not exists
            if (($quote->getproduct_id() == null) || ($quote->getproduct_id() == 0)) {
                if ($quote->getItems()->getSize() > 0)
                    $quote->commit();
                else {
                    $error = true;
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Impossible to print an empty quotation.'));
                }
            }

            //continue....
            if (!$error) {
                $pdf = Mage::getModel('Quotation/quotationpdf')->getPdf(array($quote));
                $name = Mage::helper('quotation')->__('quotation_') . $quote->getincrement_id() . '.pdf';
				//header ('Content-Type:', 'application/pdf');
				//header ('Content-Disposition:', 'inline;');
				/*
				 
				$respnose =$this->getResponse()
                ->setHeader('Content-type', 'application/pdf', true)               
                ->setBody($pdf->render());
			    echo $respnose;exit;
				*/ 
				
				// Output pdf
				//echo $pdf->render();
				//exit;						
				$this->_prepareDownloadResponse($name, $pdf->render(), 'application/pdf');
				
            }
            else
                $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteId));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            $this->_redirectReferer();
        }
    }
	
	/**
     * Print quote 
     */
    public function printquotationAction() {
        try {   
			  

            $this->loadLayout();
            $error = false;
            $quoteId = $this->getRequest()->getParam('quote_id');
            $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);

            //create bundle product if not exists
            if (($quote->getproduct_id() == null) || ($quote->getproduct_id() == 0)) {
                if ($quote->getItems()->getSize() > 0)
                    $quote->commit();
                else {
                    $error = true;
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Impossible to print an empty quotation.'));
                }
            }

            //continue....
            if (!$error) {
                $pdf = Mage::getModel('Quotation/quotationpdf')->getPdf(array($quote));
                $name = Mage::helper('quotation')->__('quotation_') . $quote->getincrement_id() . '.pdf';
				//header ('Content-Type:', 'application/pdf');
				//header ('Content-Disposition:', 'inline;');
				 
				 
				$respnose =$this->getResponse()
                ->setHeader('Content-type', 'application/pdf', true)               
                ->setBody($pdf->render());
			    echo $respnose;exit;
				 
				
				// Output pdf
				//echo $pdf->render();
				//exit;						
				$this->_prepareDownloadResponse($name, $pdf->render(), 'application/pdf');
				
            }
            else
                $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteId));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            $this->_redirectReferer();
        }
    }


 /**
     * Print order
     */
    public function printorderAction() {
        
		 $order_id = $this->getRequest()->getParam('order_id');
         $layout = $this->loadLayout();
		
		 
		 //$quote = Mage::getModel('Quotation/Quotation')->load($QuoteId);
		   $order = Mage::getModel('sales/order')->load($order_id);
		  
		    $orderModel = Mage::getModel('sales/order')->load($order->getIncrement_id(), 'increment_id');
            $order_id = $orderModel->getEntity_id();			
		    $customerid = $orderModel->getCustomer_id();		 
		    $session = Mage::getModel('customer/session', array('name'=>'frontend'));		
	        $customer = Mage::getModel('customer/customer')->load($customerid);				
            $session->setCustomerAsLoggedIn($customer);
		 	
       //  $this->checkQuoteOwner($order);
				 
		 
		try {
            	
				
           // $comit = $order->commit();			 
            $pdf = Mage::getModel('Quotation/quotationpdf')->getOrderPdf(array($order));  
			$name = Mage::helper('quotation')->__('order_') . $order->getincrement_id() . '.pdf';
			
			$respnose =$this->getResponse()
                ->setHeader('Content-type', 'application/pdf', true)               
                ->setBody($pdf->render());
			   // echo $respnose; 
			
			  // exit;			
			$this->_prepareDownloadResponseV2($name, $pdf->render(), 'application/pdf');
        } catch (Exception $ex) {
            Mage::getSingleton('checkout/session')->addError($ex->getMessage());
            $this->_redirect('Quotation/Quote/View', array('order_id' => $order_id));
        }
    }

    /**
     * Add quote to cart
     */
    public function commitAction() {

        $quoteId = $this->getRequest()->getParam('quote_id');
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteId);
        $this->checkQuoteOwner($quote);

        try {
            $model = Mage::getModel('Quotation/Quotation_Cart');
            $model->addToCart($quote, $this);
            
            Mage::getSingleton('core/session')->setQuotedetails($quote->getIncrementId().'-'.$quoteId);

            Mage::getSingleton('checkout/session')->addSuccess($this->__('Quote added to cart'));
            $this->_redirect('checkout/cart');
        } catch (Exception $ex) {
            Mage::getSingleton('checkout/session')->addError($ex->getMessage());
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Custom download response method for magento multi version compatibility
     */
    protected function _prepareDownloadResponseV2($fileName, $content, $contentType = 'application/octet-stream') {
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', strlen($content))
                ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
                ->setBody($content);
    }

    /**
     * Display quotes grid
     */
    public function ListAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Redirect customer to authentication page if not logged in and action = CreateRequest
     */
    public function preDispatch() {
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
        if ($action == 'RequestFromCart') {
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->addError($this->__('You must be logged in to request for a quotation.'));
                Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('Quotation/Quote/RequestFromCart', array('_current' => true)));
                $this->_redirectUrl(Mage::helper('customer')->getLoginUrl());
            }
        }
        if ($action == 'IndividualRequest') {
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->addError($this->__('You must be logged in to request for a quotation.'));
                Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('Quotation/Quote/IndividualRequest', array('_current' => true, 'product_id' => $this->getRequest()->getParam('product_id'))));
                $this->_redirectUrl(Mage::helper('customer')->getLoginUrl());
            }
        }

        return $this;
    }

    /**
     * Return an array with quote options seralized for quotation module
     *
     * @param unknown_type $quoteItem
     */
    private function getQuoteOptions($quoteItem) {
        $retour = array();

        if ($optionIds = $quoteItem->getOptionByCode('option_ids')) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $quoteItem->getProduct()->getOptionById($optionId)) {

                    $quoteItemOption = $quoteItem->getOptionByCode('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                                    ->setOption($option)
                                    ->setQuoteItemOption($quoteItemOption);

                    $retour[$option->getId()] = $quoteItemOption->getValue();
                }
            }
        }

        $retour = Mage::helper('quotation/Serialization')->serializeObject($retour);
        return $retour;
    }

    /**
     * Authenticate customer, add quote to cart and redirect to cart
     *
     */
    public function DirectAuthAction() {
        $quote_id = $this->getRequest()->getParam('quote_id');
        $security_key = $this->getRequest()->getParam('security_key');
        $helper = Mage::helper('quotation/DirectAuth');
        $quote = $helper->getQuote($quote_id, $security_key);

        try {
            if ($quote == null)
                throw new Exception($this->__('Request invalid'));

            //authenticate customer
            $helper->authenticateCustomer($quote);

            //go in quote
            $this->_redirect('Quotation/Quote/View', array('quote_id' => $quote_id));
        } catch (Exception $ex) {
            Mage::getSingleton('customer/session')->addError($ex->getMessage());
            $this->_redirect('');
        }
    }

    //*********************************************************************************************************************************************************
    //*********************************************************************************************************************************************************
    //Customer REQUEST
    //*********************************************************************************************************************************************************
    //*********************************************************************************************************************************************************

    /**
     * Create a quote inquiry with cart's products
     *
     */
    public function RequestFromCartAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Create a quote inquiry with cart's products
     *
     */
    public function CreateIndividualRequestAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Quote request for one product
     *
     */
    public function IndividualRequestAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Send textual quote request
     *
     */
    public function SendTextualRequestAction() {

        //Create new quotation
        $customerId = Mage::Helper('customer')->getCustomer()->getId();
        $NewQuotation = Mage::getModel('Quotation/Quotation')
                        ->setcustomer_id($customerId)
                        ->setcaption($this->__('New request'))
                        ->setcustomer_msg($this->getRequest()->getPost('description'))
                        ->setcustomer_request(1)
                        ->setstatus(MDN_Quotation_Model_Quotation::STATUS_CUSTOMER_REQUEST)
                        ->save();

        //Notify admin
        $notificationModel = Mage::getModel('Quotation/Quotation_Notification');
        $notificationModel->NotifyCreationToAdmin($NewQuotation);

        //confirm & redirect
        Mage::getSingleton('customer/session')->addSuccess(__('You quotation request has been successfully sent. You will be notified once store administrator will have reply to your request'));
        $this->_redirect('Quotation/Quote/List/');
    }

    /**
     * 
     */
    public function SendIndividualRequestAction()
    {
        //Create new quotation
        $customerId = Mage::Helper('customer')->getCustomer()->getId();
        $NewQuotation = Mage::getModel('Quotation/Quotation')
                        ->setcustomer_id($customerId)
                        ->setcaption($this->__('New request'))
                        ->setcustomer_msg($this->getRequest()->getPost('description'))
                        ->setcustomer_request(1)
                        ->setstatus(MDN_Quotation_Model_Quotation::STATUS_CUSTOMER_REQUEST)
                        ->save();

        //Notify admin
        $notificationModel = Mage::getModel('Quotation/Quotation_Notification');
        $notificationModel->NotifyCreationToAdmin($NewQuotation);

        //add product
        $productId = $this->getRequest()->getPost('product_id');
        $qty = $this->getRequest()->getPost('qty');
        $options = $this->getRequest()->getPost('options');
        $quoteItem = $NewQuotation->addProduct($productId, $qty);
        $quoteItem->setoptions($options)->save();
        
        //confirm & redirect
        Mage::getSingleton('customer/session')->addSuccess(__('You quotation request has been successfully sent. You will be notified once store administrator will have reply to your request'));
        $this->_redirect('Quotation/Quote/List/');
        
    }

    /**
     * Submit request from cart
     */
    public function SendRequestFromCartAction() {
        
        //Create new quotation
        //echo $_REQUEST["customer_id"];
        //exit;
        //print_r($_REQUEST);
        
        /***************************** Start by dev ***************************************/
        if($_REQUEST["customer_id"] != '')
        {
                $_custom_address = array (
                'firstname' => $_REQUEST['firstname'],
                'lastname' => $_REQUEST['lastname'],
                'street' => array (
                    '0' => $_REQUEST['billing']['street'][0],
                    '1' => $_REQUEST['billing']['street'][1],
                ),
                'city' => $_REQUEST['billing']['city'],
                'region_id' => $_REQUEST['billing']['region_id'],
                'region' => $_REQUEST['billing']['region'],
                'postcode' => $_REQUEST['billing']['postcode'],
                'country_id' => $_REQUEST['billing']['country_id'], /* Croatia */
                'telephone' => $_REQUEST['billing']['telephone'],
                );
                
                $email     = (string) $_REQUEST['billing_email'];//18_02_2014
                
                $customAddress = Mage::getModel('customer/address');
                //$customAddress = new Mage_Customer_Model_Address();
                $customAddress->setData($_custom_address)
                            ->setCustomerId($_REQUEST["customer_id"])
                            ->setIsDefaultBilling('1')
                            ->setIsDefaultShipping('1')
                            ->setSaveInAddressBook('1');
                try {
                    $customAddress->save();
                }
                catch (Exception $ex) {
                    //Zend_Debug::dump($ex->getMessage());
                }
                
            Mage::getSingleton('customer/session')->loginById($_REQUEST["customer_id"]);
            //$customerId = Mage::Helper('customer')->getCustomer()->getId();
            $customerId = $_REQUEST["customer_id"];
        }
        else{
            
                $firstname     = (string) $_REQUEST['firstname'];
                $lastname     = (string) $_REQUEST['lastname'];
                $email     = (string) $_REQUEST['billing_email'];
             //exit;           
                        
                $pwd_length = 7; //auto-generated password length
                
                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                $customer->loadByEmail($email);
                
                if(!$customer->getId()) {
                
                  //We're good to go with customer registration process
                  $customer->setEmail($email); 
                  $customer->setFirstname($firstname);
                  $customer->setLastname($lastname);
                  $customer->setPassword($customer->generatePassword($pwd_length));
                
                }
                
                //if process fails, we don't want to break the page
                try{
                
                  $customer->save();
                  $customer->setConfirmation(null); //confirmation needed to register?
                  $customer->save(); //yes, this is also needed
                  $customer->sendNewAccountEmail(); //send confirmation email to customer?
                
                } catch(Exception $e){
                   //Mage::log($e->__toString());
                }
                
                
                $_custom_address = array (
                'firstname' => $_REQUEST['firstname'],
                'lastname' => $_REQUEST['lastname'],
                'street' => array (
                    '0' => $_REQUEST['billing']['street'][0],
                    '1' => $_REQUEST['billing']['street'][1],
                ),
                'city' => $_REQUEST['billing']['city'],
                'region_id' => $_REQUEST['billing']['region_id'],
                'region' => $_REQUEST['billing']['region'],
                'postcode' => $_REQUEST['billing']['postcode'],
                'country_id' => $_REQUEST['billing']['country_id'], /* Croatia */
                'telephone' => $_REQUEST['billing']['telephone'],
                );
                
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
                
            //Mage::getSingleton('customer/session')->loginById($customer->getId());
            //$customerId = Mage::Helper('customer')->getCustomer()->getId();
            $customerId = $customer->getId();
                     
        }
        /***************************** End by dev ***************************************/
        
        $ship_details = $_REQUEST['ship_method'];
        $ship = explode('***',$ship_details);
        
        $NewQuotation = Mage::getModel('Quotation/Quotation')
                        ->setcustomer_id($customerId)
                        ->setcaption($this->__('New request'))
                        ->setcustomer_msg($this->getRequest()->getPost('description'))
                        ->setshipping_method($ship[0])
                        ->setshipping_description($ship[1])
                        ->setshipping_rate($ship[2])
                        ->setcustomer_request(1)
                        //->setstatus(MDN_Quotation_Model_Quotation::STATUS_CUSTOMER_REQUEST)
                        ->setstatus(MDN_Quotation_Model_Quotation::STATUS_ACTIVE)
                        ->save();
                        
                        
                        
        /*************************** Start by dev ******************************************/
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $tableShipping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
	/*    
        $sqlPaymentSystem="INSERT INTO ".$tableBilling."  SET quotation_id = '".$NewQuotation->getId()."' ,
                                        firstname = '".$_REQUEST['firstname']."',
                                        lastname = '".$_REQUEST['lastname']."',
                                        email = '".$email."',
                                        company = '".$_REQUEST['company_name']."',
                                        phone = '".$_REQUEST['personal_phone']."',
                                        hearabout = '".$_REQUEST['acee_how_did_you_hear_about_us']."',
                                        repid = '".$_REQUEST['salesrep']."',
                                        street1 = '".$_REQUEST['billing']['street'][0]."',
                                        street2 = '".$_REQUEST['billing']['street'][1]."',
                                        city = '".$_REQUEST['billing']['city']."',
                                        region ='".$_REQUEST['billing']['region_id']."',
                                        postcode = '".$_REQUEST['billing']['postcode']."',
                                        country_id ='".$_REQUEST['billing']['country_id']."',
                                        telephone = '".$_REQUEST['billing']['telephone']."'";
        */                     
                    $connectionWrite->beginTransaction();
                            $data = array();
                            $data['quotation_id'] = $NewQuotation->getId();
                            if($_REQUEST['firstname'] != '')
                            $data['firstname']    = $_REQUEST['firstname'];
                            
                            if($_REQUEST['lastname'] != '')
                            $data['lastname']     = $_REQUEST['lastname'];
                            
                            if($email != '')
                            $data['email']        = $email;
                            
                            if($_REQUEST['company_name'] != '')
                            $data['company']      = $_REQUEST['company_name'];
                            
                            if($_REQUEST['personal_phone'] != '')
                            $data['phone']        = $_REQUEST['personal_phone'];
                            
                            if($_REQUEST['acee_how_did_you_hear_about_us'] != '')
                            $data['hearabout']    = $_REQUEST['acee_how_did_you_hear_about_us'];
                            if($_REQUEST['salesrep'] != '')
                            $data['repid ']       = $_REQUEST['salesrep'];
                            
                            if($_REQUEST['billing']['street'][0] != '')
                            $data['street1']      = $_REQUEST['billing']['street'][0];
                            
                            if($_REQUEST['billing']['street'][1] != '')
                            $data['street2']      = $_REQUEST['billing']['street'][1];
                            
                            if($_REQUEST['billing']['city'] != '')
                            $data['city']         = $_REQUEST['billing']['city'];
                            
                            if($_REQUEST['billing']['region_id'] != '')
                            $data['region']       = $_REQUEST['billing']['region_id'];
                            
                            if($_REQUEST['billing']['region_id'] != '')//11_03_2014
                            $data['region_id']       = $_REQUEST['billing']['region_id'];//11_03_2014
                            
                            if($_REQUEST['billing']['postcode'] != '')
                            $data['postcode']     = $_REQUEST['billing']['postcode'];
                            
                            if($_REQUEST['billing']['country_id'] != '')
                            $data['country_id']   = $_REQUEST['billing']['country_id'];
                            
                            if($_REQUEST['billing']['telephone'] != '')
                            $data['telephone']    = $_REQUEST['billing']['telephone'];
                            $connectionWrite->insert($tableBilling, $data);
                            $connectionWrite->commit(); 
         
         //try {
         //        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
         //} catch (Exception $e){
         ////echo $e->getMessage();
         //}
         
         /*
         $sqlPaymentSystem="INSERT INTO ".$tableShipping."  SET quotation_id = '".$NewQuotation->getId()."' , firstname = '".$_REQUEST['firstname']."',
         lastname = '".$_REQUEST['lastname']."', email = '".$email."', company = '".$_REQUEST['company_name']."', phone = '".$_REQUEST['personal_phone']."',
         hearabout = '".$_REQUEST['acee_how_did_you_hear_about_us']."', repid = '".$_REQUEST['salesrep']."' , street1 = '".$_REQUEST['shipping']['street'][0]."',
         street2 = '".$_REQUEST['shipping']['street'][1]."', city = '".$_REQUEST['shipping']['city']."', region ='".$_REQUEST['shipping']['region_id']."',
         postcode = '".$_REQUEST['shipping']['postcode']."', country_id ='".$_REQUEST['shipping']['country_id']."', telephone = '".$_REQUEST['shipping']['telephone']."' ,
         inhand = '".$_REQUEST['in_hand_date']."'";
                    
         */        
                    $connectionWrite->beginTransaction();
                            $data = array();
                            $data['quotation_id'] = $NewQuotation->getId();
                            
                            if($_REQUEST['firstname'] != '')
                            $data['firstname']    = $_REQUEST['firstname'];
                            
                            if($_REQUEST['lastname'] != '')
                            $data['lastname']     = $_REQUEST['lastname'];
                            
                            if($email != '')
                            $data['email']        = $email;
                            
                            if($_REQUEST['company_name'] != '')
                            $data['company']      = $_REQUEST['company_name'];
                            
                            if($_REQUEST['acee_how_did_you_hear_about_us'] != '')
                            $data['phone']        = $_REQUEST['personal_phone'];
                            
                            if($_REQUEST['billing']['telephone'] != '')
                            $data['hearabout']    = $_REQUEST['acee_how_did_you_hear_about_us'];
                            
                            if($_REQUEST['salesrep'] != '')
                            $data['repid ']       = $_REQUEST['salesrep'];
                            
                            if($_REQUEST['shipping']['street'][0] != '')
                            $data['street1']      = $_REQUEST['shipping']['street'][0];
                            
                            if($_REQUEST['shipping']['street'][1] != '')
                            $data['street2']      = $_REQUEST['shipping']['street'][1];
                            
                            if($_REQUEST['shipping']['city'] != '')
                            $data['city']         = $_REQUEST['shipping']['city'];
                            
                            if($_REQUEST['shipping']['region_id'] != '')
                            $data['region']       = $_REQUEST['shipping']['region_id'];
                            
                            if($_REQUEST['shipping']['region_id'] != '')//11_03_2014
                            $data['region_id']       = $_REQUEST['shipping']['region_id'];//11_03_2014
                            
                            if($_REQUEST['shipping']['postcode'] != '')
                            $data['postcode']     = $_REQUEST['shipping']['postcode'];
                            
                            if($_REQUEST['shipping']['country_id'] != '')
                            $data['country_id']   = $_REQUEST['shipping']['country_id'];
                            
                            if($_REQUEST['shipping']['telephone'] != '')
                            $data['telephone']    = $_REQUEST['shipping']['telephone'];
                            
                            if($_REQUEST['in_hand_date'] != '')
                            $data['inhand']       = $_REQUEST['in_hand_date'];
                            $connectionWrite->insert($tableShipping, $data);
                            $connectionWrite->commit(); 
        // try {
        //         $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
        // } catch (Exception $e){
        //// echo $e->getMessage();
        // }
         
         
        
        
        /********************************End by dev **********************************************/

        //add products to quote
        $cartProducts = Mage::helper('checkout/cart')->getCart()->getItems();
        foreach ($cartProducts as $cartProduct) {

            //skip group products
            //if (($cartProduct->getProduct()->gettype_id() == 'configurable') || ($cartProduct->getProduct()->gettype_id() == 'bundle') ||($cartProduct->getProduct()->gettype_id() == 'grouped'))
            //        continue;
            
            /**************************** Start bundle product 18_02_2014 ********************************************/
             if (($cartProduct->getProduct()->gettype_id() == 'configurable') ||($cartProduct->getProduct()->gettype_id() == 'grouped'))
                    continue;

            if($cartProduct->getParentItemId() == '' and ($cartProduct->getProductType() == 'simple' or $cartProduct->getProductType() == 'bundle'))
            {
                //set qty
                $qty = $cartProduct->getqty();
                if ($cartProduct->getParentItem())
                    $qty = $cartProduct->getqty() * $cartProduct->getParentItem()->getqty();
    
                //add product
                $quoteItem = $NewQuotation->addProduct($cartProduct->getproduct_id(), $qty);
    
                //set options
                $quoteItem->setoptions($this->setQuotItemOptionFromCartItem($cartProduct))->save();
            }
            else if($cartProduct->getParentItemId() != '' and $cartProduct->getProductType() == 'simple')
            {
                //set qty
                $qty = $cartProduct->getqty();
                //if ($cartProduct->getParentItem())
                //    $qty = $cartProduct->getqty() * $cartProduct->getParentItem()->getqty();
                //
                ////add product
                //$quoteItem = $NewQuotation->addProduct($cartProduct->getproduct_id(), $qty);
                //
                ////set options
                //$quoteItem->setoptions($this->setQuotItemOptionFromCartItem($cartProduct))->save();
                $temptableItem=Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
                $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                
                $connectionWrite->beginTransaction();
                $data1 = array();
                $data1['quotation_id']= $NewQuotation->getId();
                $data1['product_id']= $cartProduct->getProductId();
                $data1['parent_item_id']= $quoteItem->getId();
                $data1['qty']= $qty;
                $data1['price']= $cartProduct->getPrice() * $qty;
                $data1['caption'] = $cartProduct->getProduct()->getName();
                $data1['sku'] = $cartProduct->getProduct()->getSku();
                $data1['option'] = $this->setQuotItemOptionFromCartItem($cartProduct);
                $connectionWrite->insert($temptableItem, $data1);
                $connectionWrite->commit();
            }
            
            /**************************** End bundle product 18_02_2014 ********************************************/
        }
        
        /****************  Start by dev ***************************************/
        
         $quoteItems = $NewQuotation->getItems();
        $grand_price = 0;
        foreach($quoteItems as $quoteItem1)
        {
            $_newProduct = Mage::getModel('catalog/product')->load($quoteItem1->getProductId());
             $option_detail =0;
            $net_price = 0;
            
            $productOptions= $quoteItem1->getOptions();
            $productOptions = Mage::helper('quotation/Serialization')->unserializeObject($productOptions);
         
             //print_r($_newProduct->getOptions());
             if($_newProduct->getOptions())
             {
                foreach ($_newProduct->getOptions() as $o) {
                     
                    $values = $o->getValues();
                    foreach ($values as $value){
                       
                        if($productOptions[$o->getId()] == $value->getId())
                        {
                            $option_detail = $value->getPrice();
                            if($option_detail != 0)
                            {
                               $net_price += $option_detail ;
                                
                                
                            }
                        }
                    }
                }
            $grand_price += ($net_price + $_newProduct->getPrice()) * $quoteItem1->getqty();
                if($net_price != 0)
                $quoteItem1->setPriceHt($net_price + $_newProduct->getPrice());
             }
             else{
                $quoteItem1->setPriceHt($_newProduct->getPrice());
                $grand_price +=  $quoteItem1->getqty() * $quoteItem1->getPriceHt();
             }
            
            
            $quoteItem1->save();
        }
       
       
           $tableQutation = Mage::getSingleton('core/resource')->getTableName('quotation');
           
            //$sqlPaymentSystem="UPDATE  ".$tableQutation."  SET price_ht = '".$grand_price."' WHERE quotation_id = '".$NewQuotation->getId()."'";
            
            $connectionWrite->beginTransaction();
                $data = array();
                $data['price_ht'] = $grand_price;
                $where = $connectionWrite->quoteInto('quotation_id =?', $NewQuotation->getId());
                $connectionWrite->update($tableQutation, $data, $where);
                $connectionWrite->commit();
            
           //  try {
           //          $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
           //  } catch (Exception $e){
           ////  echo $e->getMessage();
           //  }
           
        /***************** End by dev *****************************************/
        
        
        /************************************* Start by Dev *********************************************/
        
        foreach ($quoteItems as $item) {
            
            $ProductId = $item->getProductId();
            
            $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
            if($connectionWrite->isTableExists($temptableOrganiger))
            {
                //$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'";
                $sqlOrganiger = $connectionRead->select()
                                        ->from($temptableOrganiger, array('*'))
                                        ->where("ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'");
                $chkOrganiger = $connectionRead->fetchAll($sqlOrganiger);
                
                if($chkOrganiger)
                {
                        
                    foreach($chkOrganiger as $chkOrganiger1)
                    {
                        if($chkOrganiger1['ot_day'] != '')
                        $finished_date = date ( 'Y-m-j', strtotime ( '+'.$chkOrganiger1['ot_day'].' day' . date('Y-m-d') ) );
                        else
                        $finished_date = date('Y-m-d');
                        
                        
                        $temptableNumber=Mage::getSingleton('core/resource')->getTableName('subadmin_task_number');
                        if($connectionWrite->isTableExists($temptableNumber))
                        {
                            //$sqlNumber="SELECT * FROM ".$temptableNumber." WHERE entity_id = '1' ";
                            $sqlNumber = $connectionRead->select()
                                                ->from($temptableNumber, array('*'))
                                                ->where('entity_id=?', 1);
                            $chkNumber = $connectionRead->fetchAll($sqlNumber);
                        }
                        
                        $flag = 0;
                        $finished_date = date ( 'Y-m-j');
                        if($chkNumber[0]['task_number'] != '')
                        {
                            while($flag == 0)
                            {
                                //$sqlTask2="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user = '".$chkOrganiger1['ot_target_user']."' AND ot_deadline ='".$finished_date."'";
                                $sqlTask2 = $connectionRead->select()
                                                        ->from($temptableOrganiger, array('*'))
                                                        ->where("ot_target_user = '".$chkOrganiger1['ot_target_user']."' AND ot_deadline ='".$finished_date."'");
                                
                                $chkTask2 = $connectionRead->fetchAll($sqlTask2);
                                
                                if(count($chkTask2) > $chkNumber[0]['task_number'])
                                {
                                    $finished_date = date ( 'Y-m-j', strtotime ( '+1 day' . $finished_date ) );
                                    
                                }
                                else{
                                    $flag = 1;
                                }
                            }
                        }
                        /*
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
                                       ot_entity_id = '".$NewQuotation->getId()."', 
                                       ot_entity_description = '".addslashes($chkOrganiger1['ot_entity_description'])."', 
                                       ot_notification_read = '".$chkOrganiger1['ot_notification_read']."',
                                       ot_task_type = '".$chkOrganiger1['ot_task_type']."'";
                                       
                       $chkOrganiger2 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlOrganiger1);
                        */
                       $connectionWrite->beginTransaction();
                                $data = array();
                                $data['ot_created_at']= NOW();
                                $data['ot_author_user'] = $chkOrganiger1['ot_author_user'];
                                $data['ot_target_user'] = $chkOrganiger1['ot_target_user']; 
                                $data['ot_caption'] = addslashes($chkOrganiger1['ot_caption']); 
                                $data['ot_description'] = addslashes($chkOrganiger1['ot_description']); 
                                $data['ot_deadline'] = $finished_date; 
                                $data['ot_notify_date'] = $chkOrganiger1['ot_notify_date']; 
                                $data['ot_priority'] = $chkOrganiger1['ot_priority']; 
                                $data['ot_finished'] = $chkOrganiger1['ot_finished']; 
                                $data['ot_read']= $chkOrganiger1['ot_read']; 
                                $data['ot_origin']= $chkOrganiger1['ot_origin']; 
                                $data['ot_category'] = $chkOrganiger1['ot_category']; 
                                $data['ot_entity_type'] = 'quote'; 
                                $data['ot_entity_id'] = $NewQuotation->getId(); 
                                $data['ot_entity_description'] = addslashes($chkOrganiger1['ot_entity_description']); 
                                $data['ot_notification_read'] = $chkOrganiger1['ot_notification_read'];
                                $data['ot_task_type']= $chkOrganiger1['ot_task_type'];
                                $connectionWrite->insert($temptableOrganiger, $data);
                                $connectionWrite->commit();
                       
                        //For chain task
                        $last_id = $connectionWrite->fetchOne('SELECT last_insert_id()');
                        
                        $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                        if($connectionWrite->isTableExists($temptableChain))
                        {
                            /*
                            $sqlChain="INSERT INTO ".$temptableChain." SET task_id = '$last_id', 
                                            order_quote_id = '".$NewQuotation->getId()."' ,
                                            product_id ='".$chkOrganiger1['ot_entity_id']."', 
                                            task_type = '".$chkOrganiger1['ot_task_type']."'";
                                            
                            $chkChain = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChain);
                            */
                            $connectionWrite->beginTransaction();
                            $data = array();
                            $data['task_id'] = $last_id;
                            $data['order_quote_id'] = $NewQuotation->getId();
                            $data['product_id'] = $chkOrganiger1['ot_entity_id'];
                            $data['task_type'] = $chkOrganiger1['ot_task_type'];
                            $connectionWrite->insert($temptableChain,$data);
                            $connectionWrite->commit();
                        }
                    }
                }
            }
            
            /*********************** add planning auto *********************************/
            
            $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
            if($connectionWrite->isTableExists($temptableShipping))
            {
            //$sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$NewQuotation->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'quote' ";
            $sqlShipping = $connectionRead->select()
                                        ->from($temptableShipping, array('*'))
                                        ->where("quote_id = '".$NewQuotation->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'quote'");
                        
            $chkShipping = $connectionRead->fetchAll($sqlShipping);
            }
                
            if(count($chkShipping) == 0)
            {
                $created_date = $NewQuotation->getCreatedTime();
                $artworkUploaded=false;

	        $productOptions= $item->getOptions();
		$productOptions = Mage::helper('quotation/Serialization')->unserializeObject($productOptions);
		foreach($productOptions as $productOption){
                        $productOption = Mage::helper('quotation/Serialization')->unserializeObject($productOption);
			if(file_exists($productOption[fullpath])){
				$artworkUploaded=true;
				// insert into proof folder
/*				$proofmodel= Mage::getModel('Quotation/proofs');
				$proofmodel->setArtwork($productOption[quote_path]);
				$proofmodel->setItemId($item->getId());
                                $proofmodel->setQuoteId($NewQuotation->getId());
				$proofmodel->save();
*/
			}
		}



            
                $req_delicery_date = $_REQUEST['in_hand_date'];
                
                $Product = Mage::getModel('catalog/product')->load($ProductId);
                
                $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                if($connectionWrite->isTableExists($temptableTimeline))
                {
                    //$sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
                    $sqlTimeline = $connectionRead->select()
                                        ->from($temptableTimeline, array('*'))
                                        ->where('product_id=?', $ProductId);
                    $chkTimeline = $connectionRead->fetchAll($sqlTimeline);
                }
                
                if(!$req_delicery_date)
                {
                    $order_placed_date =  $created_date;

		// if artwork recieved with order
		if($artworkUploaded == true)
			$artwork_date=$order_placed_date;
		else # if artwork is not received
			$artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);


		    $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                    $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                    $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                    $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                        //$artwork_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getArtworkDelay().' day' . $created_date ) );
                        //$proof_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getProofDelay().' day' . $created_date ) );
                        //$production_start_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getProductionDelay().' day' . $created_date ) );
                        //$shipping_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getShippingDelay().' day' . $created_date ) );
                        //$delivery_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getDeliveryDelay().' day' . $created_date ) );
                }
                else{
                        $order_placed_date = date ( 'Y-m-j', strtotime ( '-'.$chkTimeline[0]['delivary_day'].' day' . $req_delicery_date ) );
                        $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$order_placed_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$order_placed_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$order_placed_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$order_placed_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $req_delicery_date;
                }
                
                
                $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                if($connectionWrite->isTableExists($temptableShipping))
                {
                    //$sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$NewQuotation->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'quote' , order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
                    //$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                    
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['quote_id']= $NewQuotation->getId();
                    $data['item_id'] = $item->getId();
                    $data['product_id'] = $ProductId; 
                    $data['planning_type'] = 'quote'; 
                    $data['order_placed_date'] = $order_placed_date; 
                    $data['artwork_date'] = $artwork_date; 
                    $data['proof_date'] = $proof_date; 
                    $data['start_date'] = $production_start_date; 
                    $data['shipping_date'] = $shipping_date; 
                    $data['delivery_date']= $delivery_date; 
                    $connectionWrite->insert($temptableShipping, $data);
                    $connectionWrite->commit();
                }
            }
            
            /*********************** add planning auto *********************************/
            
            /************************ Get custom option value ******************************/
                
                //$productOptions= $item->getOptions();
                //$productOptions = Mage::helper('quotation/Serialization')->unserializeObject($productOptions);
                //
                //$_product =Mage::getModel('catalog/product')->load($ProductId);
                //
                //foreach ($_product->getOptions() as $option) { 
                //  
                //   $values = $option->getValues(); 
                //    foreach ($values as $value)
                //    {
                //        if($option->getTitle() == 'Graphic Design Service'){
                //           
                //            if($value->getId() == $productOptions[$option->getId()])
                //            {
                //                $title = explode(' ',$value->getTitle());
                //                
                //                if (is_numeric($title[0]))
                //                $revison_number = $title[0];
                //                else
                //                $revison_number = 10000;
                //            }
                //        }
                //    }
                //}
                
                
                //$temptableProduct=Mage::getSingleton('core/resource')->getTableName('catalog_product_designer');
                //if($connectionWrite->isTableExists($temptableProduct))
                //  {
                //       // $sqlProduct="SELECT * FROM ".$temptableProduct." WHERE product_id = '".$ProductId."'";
                //        $sqlProduct= $connectionRead->select()
                //                        ->from($temptableProduct,array('*'))
                //                        ->where('product_id=?',$ProductId);
                //        $chkProduct = $connectionRead->fetchAll($sqlProduct);
                //  }
                //
                //$adminid = $chkProduct[0]['user_id'];
                // 
                //$temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
                //if($connectionWrite->isTableExists($temptableDesign))
                //{
                //    //$sqlDesign="INSERT INTO  ".$temptableDesign." SET order_id = '".$NewQuotation->getId()."', type='quote', item_id ='".$item->getId()."', product_id = '".$ProductId."', revision_number = '".$revison_number."', assign_to = '".$adminid."', postdate = NOW() ";
                //    //$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlDesign);
                //    
                //    $connectionWrite->beginTransaction();
                //    $data = array();
                //    $data['order_id']= $NewQuotation->getId();
                //    $data['type'] = 'quote';
                //    $data['item_id'] = $item->getId();
                //    $data['product_id'] = $ProductId;
                //    if($revision_number!=""){
                //        $data['revision_number'] = $revision_number; 
                //    }
                //    $data['assign_to'] = $adminid; 
                //    $data['postdate'] = NOW(); 
                //    $connectionWrite->insert($temptableDesign, $data);
                //    $connectionWrite->commit();
                //}
                
                /************************ Get custom option value ******************************/
        

            
        }
        
        
        //Start for add comment 16_04_2014
        if($_REQUEST['comment'] != '')
        {
            $tableHistory = Mage::getSingleton('core/resource')->getTableName('quotation_history');
    
            $connectionWrite->beginTransaction();
            $data = array();
            $data['qh_quotation_id']= $NewQuotation->getId();
            $data['qh_message']=$_REQUEST['comment'];
            $data['qh_date']= NOW();
            $data['qh_user']= 'customer';
            $connectionWrite->insert($tableHistory, $data);
            $connectionWrite->commit();
        }
        //End for add comment 16_04_2014
        
        
        /************************************* End by dev ********************************************/
        
        //Notify admin
        $notificationModel = Mage::getModel('Quotation/Quotation_Notification');
        $notificationModel->NotifyCreationToAdmin($NewQuotation);

        //empty cart if configured
        if (Mage::getStoreConfig('quotation/cart_options/empty_cart_after_quote_request'))
            Mage::helper('quotation/Cart')->emptyCart(true);

        //confirm & redirect
        //Mage::getSingleton('customer/session')->addSuccess(__('You quotation request has been successfully sent. You will be notified once store administrator will have reply to your request'));
        //$this->_redirect('Quotation/Quote/List/');
        $this->_redirect($_REQUEST['redirect_url']);
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
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
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
                        if($connectionWrite->isTableExists($temptableHoliday))
                        {
                        //$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                        $sqlHoliday= $connectionRead->select()
                                            ->from($temptableHoliday,array('*'))
                                            ->where('h_date=?',$artwork_date);
                        $chkHoliday = $connectionWrite->fetchAll($sqlHoliday);
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
                    if($connectionWrite->isTableExists($temptableHoliday))
                    {
                        //$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                        $sqlHoliday= $connectionRead->select()
                                            ->from($temptableHoliday,array('*'))
                                            ->where('h_date=?',$artwork_date);
                        $chkHoliday = $connectionWrite->fetchAll($sqlHoliday);
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
     * 
     */
    protected function setQuotItemOptionFromCartItem($cartProduct)
    {
        $selectedOptions = array();

        if ($optionIds = $cartProduct->getOptionByCode('option_ids')) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $cartProduct->getProduct()->getOptionById($optionId)) {
                    $quoteItemOption = $cartProduct->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                                    ->setOption($option)
                                    ->setQuoteItemOption($quoteItemOption);
                    $selectedOptions[$optionId] = $quoteItemOption->getValue();
                }
            }
        }

        return Mage::helper('quotation/Serialization')->serializeObject($selectedOptions);
    }

    //*********************************************************************************************************************************************************
    //*********************************************************************************************************************************************************
    //ANONYMOUS REQUEST
    //*********************************************************************************************************************************************************
    //*********************************************************************************************************************************************************

    /**
     * Display quote request form for anonymous
     *
     */
    public function anonymousQuoteRequestAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Download attached PDF
     */
    public function DownloadAdditionalPdfAction() {
        $QuoteId = $this->getRequest()->getParam('quote_id');
        $quote = Mage::getModel('Quotation/Quotation')->load($QuoteId);
        $this->checkQuoteOwner($quote);
        $filePath = Mage::helper('quotation/Attachment')->getAttachmentPath($quote);
        $content = file_get_contents($filePath);
        $this->_prepareDownloadResponseV2($quote->getadditional_pdf() . '.pdf', $content, 'application/pdf');
    }
    
    /*************************** Start by dev ******************************************/
    public function saverequestAction() {
//exit('debug');
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
           $quote_id = $this->getRequest()->getParam('id');
           extract($_REQUEST);
		  // Zend_debug::dump($_REQUEST);
           $quote1 = Mage::getModel('Quotation/Quotation')->load($quote_id);
          $update = '';
        
        $tableItem = Mage::getSingleton('core/resource')->getTableName('quotation_items');
        
        $tableShipping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
        $tableQutation = Mage::getSingleton('core/resource')->getTableName('quotation');
        
        $ship = explode('***',$ship_method);
		
		//Zend_debug::dump($ship);    
		
		//exit;
		
        $grand_price = 0;
        $quoteItems = $quote1->getItems();
        
		foreach($quoteItems as $quoteItem)
        {
            $_newProduct = Mage::getModel('catalog/product')->load($quoteItem->getProductId());
            $option_detail =0;
            $net_price = 0;
            
         $productOptions= $quoteItem->getOptions();
	     $productOptions = Mage::helper('quotation/Serialization')->unserializeObject($productOptions);
                    
             //print_r($_newProduct->getOptions());
             if($_newProduct->getOptions())
             {
                foreach ($_newProduct->getOptions() as $o) {
                     
                    $values = $o->getValues();
                   
				    foreach ($values as $value){                       
                        if($options[$quoteItem->getProductId()][$o->getId()] == $value->getId())
                        {
                            $option_detail = $value->getPrice();
                            
							if($option_detail != 0)
                            {
                                $net_price += $option_detail + $_newProduct->getPrice(); 
                            }                            
                            
                        }                        
                       
                    }
                    //print_r($values[$options[$quoteItem->getProductId()][$o->getId()]]->getTitle()); exit;
                    
                    if($productOptions[$o->getId()] == '')
                    {
                        
                       //$update .= 'Item: <strong>'.$_newProduct->getName().'</strong> <br/>Add  <strong>'.$values[$options[$quoteItem->getProductId()][$o->getId()]]->getTitle().'</strong><br/>';
                    }
                    else if($productOptions[$o->getId()] != $options[$quoteItem->getProductId()][$o->getId()])
                    {
                      $update .= 'Item: <strong>'.$_newProduct->getName().'</strong> <br/>Changed <strong>'.$o->getTitle().'</strong> from <strong>'.$values[$productOptions[$o->getId()]]->getTitle().'</strong> to <strong>'.$values[$options[$quoteItem->getProductId()][$o->getId()]]->getTitle().'</strong><br/>';
                    }
                }
               
                
              $grand_price += $net_price * $item[$_newProduct->getId()];
              
                if($net_price != 0)
                $quoteItem->setPriceHt($net_price);
             }
             else{
                $quoteItem->setPriceHt($_newProduct->getPrice());
                $grand_price += $item[$_newProduct->getId()] * $quoteItem->getPriceHt();
             }
            $grand_price;
            
            $quoteItem->setOptions(Mage::helper('quotation/Serialization')->serializeObject($options[$quoteItem->getProductId()]));
            
            if($item[$quoteItem->getProductId()] != '')
            {
                if($quoteItem->getQty() != $item[$quoteItem->getProductId()])
                $update .= 'Item: <strong>'.$_newProduct->getName().'</strong> <br/>Changed Qty from <strong>'.$quoteItem->getQty().'</strong> to <strong>'.$item[$quoteItem->getProductId()].'</strong><br/>';
                $quoteItem->setQty($item[$quoteItem->getProductId()]);
            }
            
            $quoteItem->save();
        }
        $quotation_id = $quote_id;
        //$sqlQuotationSystem="SELECT * FROM ".$tableQutation."  WHERE quotation_id = '".$quote_id."'";
        $sqlQuotationSystem= $connectionRead->select()
                                        ->from($tableQutation,array('*'))
                                        ->where('quotation_id=?',$quotation_id);
        $chkSystem = $connectionRead->query($sqlQuotationSystem);       
        $fetchQutation = $chkSystem->fetch(); 		
		$shippingTitle = $fetchQutation['shipping_description'];
		
		if($fetchQutation['shipping_method']=='custom_price'){
				$shippingTitle = 'Courier Delivery';	
			}	
        
        if($ship[0] != $fetchQutation['shipping_method'] and $ship[0] != '')
        {
			if($ship[0]==$fetchQutation['shipping_method']){
				$shippingTitle2 = $fetchQutation['shipping_description'];
				}
            $update .= 'Shipping Method: changed from  &quot; <strong>'.$shippingTitle.'</strong>  &quot;  to  <strong>'.$shippingTitle2.'</strong><br/>';
        }
        
        //$sqlBillingSystem="SELECT * FROM ".$tableBilling."  WHERE quotation_id = '".$quote_id."'";
        $sqlBillingSystem= $connectionRead->select()
                                        ->from($tableBilling, array('*'))
                                        ->where('quotation_id=?',$quote_id);
        $chkBillingSystem = $connectionRead->query($sqlBillingSystem);
        $fetchBilling = $chkBillingSystem->fetch();
        
        $billing1 = $fetchBilling['firstname'].' '.$fetchBilling['lastname']."<br/>".$fetchBilling['street1']."<br/>".$fetchBilling['street2']."<br/>".$fetchBilling['city'].",".$fetchBilling['postcode']."<br/>".$fetchBilling['country_id']."<br/>".'T:'.$fetchBilling['telephone'];
        $update_billing = $fetchBilling['firstname'].' '.$fetchBilling['lastname']."<br/>".$quote['billing_address']['street'][0]."<br/>".$_REQUEST['billing_address']['street'][1]."<br/>".$quote['billing_address']['city'].",".$quote['billing_address']['postcode']."<br/>".$billing['country_id']."<br/>T:".$quote['billing_address']['telephone'];
        
        if($is_bill == 1)
        {
            $update .= 'Billing Address: changed from <br/>'.$billing1.'<br/><br/> to <br/><br/>'.$update_billing.'<br/>';
        }
        
       // $sqlShippingSystem="SELECT * FROM ".$tableShipping."  WHERE quotation_id = '".$quote_id."'";
        $sqlShippingSystem= $connectionRead->select()
                                ->from($tableShipping, array('*'))
                                ->where('quotation_id=?',$quote_id);
        $chkShippingSystem = $connectionRead->query($sqlShippingSystem);
        $fetchShipping = $chkShippingSystem->fetch();
        
        $shipping1 = $fetchShipping['firstname'].' '.$fetchShipping['lastname']."<br/>".$fetchShipping['street1']."<br/>".$fetchShipping['street2']."<br/>".$fetchShipping['city'].",".$fetchShipping['postcode']."<br/>".$fetchShipping['country_id']."<br/>".'T:'.$fetchShipping['telephone'];
        $update_shipping = $fetchShipping['firstname'].' '.$fetchShipping['lastname']."<br/>".$quote['shipping_address']['street'][0]."<br/>".$_REQUEST['shipping_address']['street'][1]."<br/>".$quote['shipping_address']['city'].",".$quote['shipping_address']['postcode']."<br/>".$shipping['country_id']."<br/>T:".$quote['shipping_address']['telephone'];
        
        if($is_ship == 1)
        {
            $update .= 'Shipping Address: changed from <br/>'.$shipping1.'<br/><br/> to <br/><br/>'.$update_shipping.'<br/>';
        }
        
        if($inhand == $fetchShipping['inhand'])
        {
            $update .= 'Changed In Hand Date: '.$inhand;
        }
        
        if($fetchShipping['inhand'] == '' and $inhand != '')
        {
            $update .= 'Added In Hand Date: '.$inhand;
        }
        
        //$sqlPaymentSystem="UPDATE  ".$tableQutation."  SET price_ht = '".$grand_price."',  shipping_method = '".$ship[0]."',shipping_rate = '".$ship[1]."' WHERE quotation_id = '".$quote_id."'";
        //$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
        
        $connectionWrite->beginTransaction();
        $data = array();
        $data['price_ht'] = $grand_price;
        $data['shipping_method'] = $ship[0];
        $data['shipping_rate'] = $ship[1];
        $data['shipping_description'] = $ship[2];
        $where = $connectionWrite->quoteInto('quotation_id =?', $quote_id);
        $connectionWrite->update($tableQutation, $data, $where);
        $connectionWrite->commit();
        
        
        $selectBilling = $connectionRead->select()
                        ->from($tableBilling, array('*'))
                        ->where('quotation_id=?',$quote_id);
        $rowBilling = $connectionRead->fetchAll($selectBilling);
       // print_r($rowBilling);
        //exit;
        
        if(count($rowBilling) > 0){
	    
            // $sqlPaymentSystem="UPDATE  ".$tableBilling."  SET  phone = '".$quote['billing_address']['telephone']."',   street1 = '".$quote['billing_address']['street'][0]."',street2 = '".$_REQUEST['billing_address']['street'][1]."', city = '".$quote['billing_address']['city']."', region ='".$quote['billing_address']['region']."', region_id ='".$quote['billing_address']['region_id']."', postcode = '".$quote['billing_address']['postcode']."', country_id ='".$billing['country_id']."', telephone = '".$quote['billing_address']['telephone']."' WHERE quotation_id = '".$quote_id."'";
            // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
     
             $connectionWrite->beginTransaction();
             $data = array();
             
             if($quote['billing_address']['telephone'] != '')
             $data['phone'] = $quote['billing_address']['telephone'];
             
             if($quote['billing_address']['street'][0] != '')
             $data['street1'] = $quote['billing_address']['street'][0];
             
             if($quote['billing_address']['street'][1] != '')
             $data['street2'] = $quote['billing_address']['street'][1];
             
             if($quote['billing_address']['city'] != '')
             $data['city'] = $quote['billing_address']['city'];
             
             //6-5-2014 S
             
             //if($quote['billing_address']['region'] != '')
             //$data['region'] =$quote['billing_address']['region'];
             
             if($quote['billing_address']['region_id'] != ''){
                $data['region_id'] = $quote['billing_address']['region_id'];
                $data['region'] = "";
             }else{
                $data['region'] = $quote['billing_address']['region'];
                
             }
             //6-5-2014 E
             
             
             
             
             
             
             
             if($quote['billing_address']['postcode'] != '')
             $data['postcode'] = $quote['billing_address']['postcode'];
             
             if($billing['country_id'] != '')
             $data['country_id'] =$billing['country_id'];
             
             if($quote['billing_address']['telephone'] != '')
             $data['telephone'] = $quote['billing_address']['telephone'];
             $where = $connectionWrite->quoteInto('quotation_id =?', $quote_id);
             $connectionWrite->update($tableBilling, $data, $where);
             $connectionWrite->commit();
         
        
        }else{
            
            $connectionWrite->beginTransaction();
            $data = array();
            $data['quotation_id'] = $quote_id;
            
            if($quote['billing_address']['telephone'] != '')
            $data['phone'] = $quote['billing_address']['telephone'];
            
            if($quote['billing_address']['street'][0] != '')
            $data['street1'] = $quote['billing_address']['street'][0];
            
            if($quote['billing_address']['street'][1] != ''){
                $data['street2'] = $quote['billing_address']['street'][1];
            }
            
            if($quote['billing_address']['city'] != '')
            $data['city'] = $quote['billing_address']['city'];
            
             //6-5-2014 S
             
            //if($quote['billing_address']['region'] != '')
            //$data['region'] =$quote['billing_address']['region'];
            
            if($quote['billing_address']['region_id'] != ''){
                $data['region_id'] =$quote['billing_address']['region_id'];
                $data['region'] = "";
            }else{
                $data['region'] =$quote['billing_address']['region'];
            }
             //6-5-2014 E
             
            
            
            
            if($quote['billing_address']['postcode'] != '')
            $data['postcode'] = $quote['billing_address']['postcode'];
            
            if($billing['country_id'] != '')
            $data['country_id'] =$billing['country_id'];
            
            if($quote['billing_address']['telephone'] != '')
            $data['telephone'] = $quote['billing_address']['telephone'];
            
            $connectionWrite->insert($tableBilling, $data);
            $connectionWrite->commit();
        }
        
        $selectShipping = $connectionRead->select()
                        ->from($tableShipping, array('*'))
                        ->where('quotation_id=?',$quote_id);
        $rowShipping = $connectionRead->fetchAll($selectShipping);
        
        $inhand1 = explode('/',$quote['in_hand_date']);
        $inhand = $inhand1[2].'-'.$inhand1[0].'-'.$inhand1[1];
        
        if(count($rowShipping) > 0){
       // $sqlPaymentSystem="UPDATE ".$tableShipping."  SET  phone = '".$quote['shipping_address']['telephone']."',   street1 = '".$quote['shipping_address']['street'][0]."',street2 = '".$_REQUEST['shipping_address']['street'][1]."', city = '".$quote['shipping_address']['city']."', region ='".$quote['shipping_address']['region']."', region_id ='".$quote['shipping_address']['region_id']."', postcode = '".$quote['shipping_address']['postcode']."', country_id ='".$shipping['country_id']."', telephone = '".$quote['shipping_address']['telephone']."' , inhand = '".$inhand."' WHERE quotation_id = '".$quote_id."'";
       // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
        //echo $inhand;
        //exit;
        $connectionWrite->beginTransaction();
        $data1 = array();
        if($quote['shipping_address']['telephone'] != '')
        $data1['phone'] = $quote['shipping_address']['telephone'];
        
        if($quote['shipping_address']['street'][0] != '')
        $data1['street1'] = $quote['shipping_address']['street'][0];
        
        if($quote['shipping_address']['street'][1] != '')
        $data1['street2'] = $quote['shipping_address']['street'][1];
        
        if($quote['shipping_address']['city'] != '')
        $data1['city'] = $quote['shipping_address']['city'];
        
        
        //6-5-2014 S
        //if($quote['shipping_address']['region'] != '')
        //$data1['region'] =$quote['shipping_address']['region'];
        
        if($quote['shipping_address']['region_id'] != ''){
            $data1['region_id'] =$quote['shipping_address']['region_id'];
            $data1['region'] = "";
        }else{
            $data1['region'] =$quote['shipping_address']['region'];
            
        }
        //6-5-2014 E
        
        
        
        
        
        
        if($quote['shipping_address']['postcode'] != '')
        $data1['postcode'] = $quote['shipping_address']['postcode'];
        
        if($shipping['country_id'] != '')
        $data1['country_id'] =$shipping['country_id'];
        
        if($quote['shipping_address']['telephone'] != '')
        $data1['telephone'] = $quote['shipping_address']['telephone'];
        
        if($inhand != '')
        $data1['inhand']= $inhand;
        $where = $connectionWrite->quoteInto('quotation_id =?', $quote_id);
        $connectionWrite->update($tableShipping, $data1, $where);
        
        }else{
            
            
            $connectionWrite->beginTransaction();
            $data1 = array();
            $data1['quotation_id'] = $quote_id;
            
            if($quote['shipping_address']['telephone'] != '')
            $data1['phone'] = $quote['shipping_address']['telephone'];
            
            if($quote['shipping_address']['street'][0] != '')
            $data1['street1'] = $quote['shipping_address']['street'][0];
            
            if($quote['shipping_address']['street'][1] != ''){
                $data1['street2'] = $quote['shipping_address']['street'][1];
            }
            
            if($quote['shipping_address']['city'] != '')
            $data1['city'] = $quote['shipping_address']['city'];
            
            //6-5-2014 S
            
            //if($quote['shipping_address']['region'] != '')
            //$data1['region'] =$quote['shipping_address']['region'];
            
            if($quote['shipping_address']['region_id'] != ''){
                $data1['region_id'] =$quote['shipping_address']['region_id'];
                $data1['region'] = "";
            }else{
               $data1['region'] =$quote['shipping_address']['region'];
            }
            
            
            //6-5-2014 E
            
            if($quote['shipping_address']['postcode'] != '')
            $data1['postcode'] = $quote['shipping_address']['postcode'];
            
            if($shipping['country_id'] != '')
            $data1['country_id'] =$shipping['country_id'];
            
            if($quote['shipping_address']['telephone'] != '')
            $data1['telephone'] = $quote['shipping_address']['telephone'];
            if($inhand != ''){
                $data1['inhand']= $inhand;
            }
            $connectionWrite->insert($tableShipping, $data1);
            $connectionWrite->commit();
            
        }
        $connectionWrite->commit();
        
        $update = addslashes($update);     
        
        if($update != '')
        {
            $tableHistory = Mage::getSingleton('core/resource')->getTableName('quotation_history');
            
            //$sqlHistorySystem="INSERT INTO   ".$tableHistory."  SET  qh_quotation_id = '".$quote_id."', qh_message = '".$update."', qh_date = NOW() , qh_user = 'customer'";
           // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHistorySystem);
            
            $connectionWrite->beginTransaction();
            $data = array();
            $data['qh_quotation_id']= $quote_id;
            $data['qh_message']=$update;
            $data['qh_date']= NOW();
            $data['qh_user']='customer';
            $connectionWrite->insert($tableHistory, $data);
            $connectionWrite->commit();
        }
        
         $created_date = $quote1->getCreatedTime();
        
        $req_delicery_date = $inhand;
        $quoteItems = $quote1->getItems();
        foreach ($quoteItems as $item) {
            
            $ProductId = $item->getProductId();
        
            $Product = Mage::getModel('catalog/product')->load($ProductId);
             $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
            if($connectionWrite->isTableExists($temptableTimeline))
            {
                //$sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
                $sqlTimeline = $connectionRead->select()
                                        ->from($temptableTimeline, array('*'))
                                        ->where('product_id=?', $ProductId);
                $chkTimeline = $connectionRead->fetchAll($sqlTimeline);
                
            }
                
                if(!$req_delicery_date)
                {
                    $order_placed_date =  $created_date;
                    
                    $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                    $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                    $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                    $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                    $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                        //$artwork_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getArtworkDelay().' day' . $created_date ) );
                        //$proof_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getProofDelay().' day' . $created_date ) );
                        //$production_start_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getProductionDelay().' day' . $created_date ) );
                        //$shipping_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getShippingDelay().' day' . $created_date ) );
                        //$delivery_date = date ( 'Y-m-j', strtotime ( '+'.$Product->getDeliveryDelay().' day' . $created_date ) );
                }
                else{
                        $order_placed_date = date ( 'Y-m-j', strtotime ( '-'.$chkTimeline[0]['delivary_day'].' day' . $req_delicery_date ) );
                        $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$order_placed_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$order_placed_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$order_placed_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$order_placed_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $req_delicery_date;
                }
            
            
            $temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
            if($connectionWrite->isTableExists($temptablePlanning))
            {
            //$sqlPlanning="UPDATE  ".$temptablePlanning." SET order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' WHERE quote_id = '".$quote_id."' AND item_id = '".$item->getId()."' AND planning_type = 'quote'";
           // $chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlPlanning);
            
            $connectionWrite->beginTransaction();
                $data = array();
                $data['order_placed_date'] = $order_placed_date;
                $data['artwork_date'] = $artwork_date;
                $data['proof_date'] = $proof_date;
                $data['start_date'] = $production_start_date;
                $data['shipping_date'] = $shipping_date;
                $data['delivery_date'] = $delivery_date;
                $where = $connectionWrite->quoteInto("quote_id =? AND item_id=? AND planning_type='quote'", $quote_id, $item->getId());
                $connectionWrite->update($temptablePlanning, $data, $where);
                $connectionWrite->commit();
            }
            
             $productOptions= $item->getOptions();
                $productOptions = Mage::helper('quotation/Serialization')->unserializeObject($productOptions);
                
                /*
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
            
            $temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
            if($connectionWrite->isTableExists($temptableDesign))
            {
               // $sqlDesign="UPDATE  ".$temptableDesign." SET revision_number = '".$revison_number."' WHERE order_id = '".$quote_id."' AND type='quote' AND item_id ='".$item->getId()."'  ";
               // $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlDesign);
                
                $connectionWrite->beginTransaction();
                $data = array();
                $data['revision_number'] = $revision_number;
                $where = $connectionWrite->quoteInto('order_id=? AND type=? AND item_id=?', $quote_id, 'quote', $item->getId());
                $connectionWrite->update($temptableDesign, $data, $where);
                $connectionWrite->commit();
            }
                */
            
            
            
        }
       
         
         $this->_redirect('*/*/View/quote_id/'.$quote_id.'/');
        
        /********************************End by dev **********************************************/
           
        }
        
     //public function addcommentAction() {
     //   
     //   extract($_REQUEST);
     //   $quote_id = $this->getRequest()->getParam('id');
     //   $tableHistory = Mage::getSingleton('core/resource')->getTableName('quotation_history');
     //   
     //   $sqlHistorySystem="INSERT INTO   ".$tableHistory."  SET  qh_quotation_id = '".$quote_id."', qh_message = '".$history['comment']."', qh_date = NOW() , qh_user = 'customer'";
     //    
     //    try {
     //            $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHistorySystem);
     //    } catch (Exception $e){
     //    //echo $e->getMessage();
     //    }
     //    
     //    $this->_redirect('*/*/View/quote_id/'.$quote_id.'/');
     //   }
     
     public function addcommentAction() {
        
        extract($_REQUEST);
        //$quote_id = $this->getRequest()->getParam('id');
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $quote_id = $id;
        $quote = Mage::getModel("Quotation/Quotation")->load($quote_id);
        $tableHistory = Mage::getSingleton('core/resource')->getTableName('quotation_history');
        
        //$sqlHistorySystem="INSERT INTO   ".$tableHistory."  SET  qh_quotation_id = '".$quote_id."', qh_message = '".$comment."', qh_date = NOW() , qh_user = 'customer'";
        // 
        // try {
        //         $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHistorySystem);
        // } catch (Exception $e){
        // //echo $e->getMessage();
        // }
         
        $connectionWrite->beginTransaction();
        $data = array();
        $data['qh_quotation_id']= $quote_id;
        $data['qh_message']=$comment;
        $data['qh_date']= NOW();
        $data['qh_user']= 'customer';
		$data['qh_readstatus']= 1;
		$data['is_customer_notified']=0;
		$data['is_visible_on_front'] = '1';
		$data['status']='sent';
		$data['entity_name'] = 'quote';
        $connectionWrite->insert($tableHistory, $data);
        
		$connectionWrite->commit();
         
        $temptableQuotation=Mage::getSingleton('core/resource')->getTableName('quotation');
		$sqlquotemessages = 'update '.$temptableQuotation.' set `readstatus`=1 WHERE `quotation_id`='.$quote_id;
		Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlquotemessages);
		
		
		$tableHistory = Mage::getSingleton('core/resource')->getTableName('quotation_history');
        
        $select = $connectionRead->select()
        ->from($tableHistory, array('*'))
        ->where('qh_quotation_id=?',$quote_id)
        ->where('qh_message!=?','Created')
		//->where('is_customer_notified=?',0)
		->where('is_visible_on_front!=?',0)
        //->order('qh_id DESC')
		->order('qh_id ASC')
		;
        
        $fetchHistory = $connectionRead->fetchAll($select);
    
        //$sqlHistorySystem="SELECT * FROM  ".$tableHistory."  WHERE  qh_quotation_id = '".$quote_id."' AND qh_message != 'Created' ORDER BY qh_id DESC";
        //
        //try {
        //$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHistorySystem);
        //$fetchHistory = $chkSystem->fetchall();
        //} catch (Exception $e){
        ////echo $e->getMessage();
        //}
        $quote_comments = '';
       
	    foreach($fetchHistory as $history) {
           
		    $storeTimezone = new DateTimeZone(Mage::getStoreConfig('general/locale/timezone'));
			$storenewDateTime = new DateTime($history['qh_date'], $storeTimezone);
		   
		   $usertype ='';
		   
		   if($history['qh_user'] == 'customer'){
			   $usertype .='<td class="comment-additional" width="35%" style="background-color:#99BB1E; padding:10px 22px">';
			$usertype .= $quote->getCustomerName();
		   }else{
			$usertype .='<td class="comment-additional" width="35%" style="padding:10px 22px">';
			$usertype .= $history['qh_user'];
		   }
		   
		   $quote_comments .= '<li class="clearer odd">
                                    <table width="100%">
                                    <tbody>
                                    <tr class="customer-comment">
                                            ';
           $quote_comments .=$usertype; 
			
			$old_zone_time = $history['qh_date'];		
		    $quote_comments .= '<span class="separator">|</span>
                                                    '. Mage::app()->getLocale()->date($history['qh_date'])->toString($format).'</td>
                              <td class="comment-content" style="padding:10px 22px">
                                                    '.nl2br($history['qh_message']).'
                                   </td>
                                    </tr>
                                    </tbody>
                                    </table>
                            </li>';
      
       				 }///end of foreach
         echo $quote_comments;
		 
		 
		 
		 
		 /* ----------------------------------------------- */
		 
		 
		  /*********************** Start for load the ticket **************************/
	/*
	$collection1 = Mage::getModel('CrmTicket/Ticket')
                ->getCollection()
                ->addFieldToFilter('ct_object_id', 'quote_' .$quote_id);
	if($collection1){
		foreach($collection1 as $ticket)
		{
			$ticket_id = $ticket->getId();
		}
	}
	/*********************** End for load the ticket **************************/
        /*        
        $messageData['ctm_content'] = $comment;

        //$needNotify = 'sales@vividads.com.au';
        $needNotify = 1;
        $newMessageData = $this->getRequest()->getPost('new_message');
        $newMessageData['ctm_content'] = $comment;
        
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customerData->getId();

        $isNewTicket = true;        
        
        // load ticket
        $ticket = mage::getModel('CrmTicket/Ticket');

         if($ticket_id == '')
         {
            $ticket->setData('ct_subject', 'Add new ticket by comment');
            $ticket->setData('ct_object_id', 'order_'.$order_id);
            $ticket->setct_manager(2);
            $ticket->setct_status(MDN_CrmTicket_Model_Email::STATUS_NEW);
            $ticket->setct_customer_id($customerId);
            $ticket->setct_email_account('sales@vividads.com.au');
            
            $currentDateTime = date('Y-m-d H:i:s');

            $ticket->setct_created_at($currentDateTime);
            $ticket->setct_updated_at($currentDateTime);
            
            $storeId = Mage::app()->getStore()->getStoreId();
            $ticket->setct_store_id($storeId);
            
            // save
            $ticket->save();
         }else{
            
            $ticket = mage::getModel('CrmTicket/Ticket')->load($ticket_id);;
            
         }
         
         //default is TYPE_FORM but can be overridde
            $additionalDatas = array();
            $additionalDatas['ctm_source_type'] = MDN_CrmTicket_Model_Message::TYPE_FORM;
          

            // $author, $content, $contentType, $isPublic
            $ticket->addMessage(
                    MDN_CrmTicket_Model_Message::AUTHOR_CUSTOMER, 
                    $messageData["ctm_content"],
                    MDN_CrmTicket_Model_Message::CONTENT_TYPE_TEXT,
                    $isPublic,
                    $additionalDatas,
                    true,
                    $attachments);

          $message = $ticket->notify(MDN_CrmTicket_Model_Message::AUTHOR_ADMIN);
            */
            /************************** End for add ticket in order 16_04_2014 *************************************/
         
        // $this->_redirect('*/*/View/quote_id/'.$quote_id.'/');
		 
		 //end comments history to the client 
		// Mage::getModel('Quotation/Quotation_Notification')->chatNotify($quote_comments,$quote_id);
        // $this->_redirect('*/*/View/quote_id/'.$quote_id.'/');
        } 
        
        
        public function submitAnonymousRequestAction() {
        //echo "<pre>";
        //print_r($_REQUEST);
        //echo "<pre>";
        


        $firstname = (string) $_REQUEST['firstname'];
        $lastname = (string) $_REQUEST['lastname'];
        $email = (string) $_REQUEST['email'];
        //exit;           

        $pwd_length = 7; //auto-generated password length

        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($email);

        if (!$customer->getId()) {

            //We're good to go with customer registration process
            $customer->setEmail($email);
            $customer->setFirstname($firstname);
            $customer->setLastname($lastname);
            $customer->setPassword($customer->generatePassword($pwd_length));
        }

        //if process fails, we don't want to break the page
        try {

            $customer->save();
            $customer->setConfirmation(null); //confirmation needed to register?
            $customer->save(); //yes, this is also needed
            $customer->sendNewAccountEmail(); //send confirmation email to customer?
        } catch (Exception $e) {
            Mage::log($e->__toString());
        }


        $_custom_address = array(
            'firstname' => $_REQUEST['firstname'],
            'lastname' => $_REQUEST['lastname'],
            'street' => array(
                '0' => $_REQUEST['street1'],
                '1' => $_REQUEST['street2'],
            ),
            'city' => $_REQUEST['city'],
            'postcode' => $_REQUEST['postcode'],
            'country_id' => $_REQUEST['billing']['country_id'], /* Croatia */
            'telephone' => $_REQUEST['telephone'],
            'fax' => $_REQUEST['fax'],
        );

        $customAddress = Mage::getModel('customer/address');
        //$customAddress = new Mage_Customer_Model_Address();
        $customAddress->setData($_custom_address)
                ->setCustomerId($customer->getId())
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1');
        try {
            $customAddress->save();
        } catch (Exception $ex) {
            //Zend_Debug::dump($ex->getMessage());
        }

        //Mage::getSingleton('customer/session')->loginById($customer->getId());
        //$customerId = Mage::Helper('customer')->getCustomer()->getId();
        $customerId = $customer->getId();
        /* ===================================================================== */
        $NewQuotation = Mage::getModel('Quotation/Quotation')
                ->setcustomer_id($customerId)
                ->setcaption($this->__('New request'))
                ->setcustomer_msg($this->getRequest()->getPost('comments'))
                ->setcustomer_request(1)
                //->setstatus(MDN_Quotation_Model_Quotation::STATUS_CUSTOMER_REQUEST)
                ->setstatus(MDN_Quotation_Model_Quotation::STATUS_ACTIVE)
                ->save();
        Mage::getSingleton('customer/session')->addSuccess(__('You quotation request has been successfully sent. You will be notified once store administrator will have reply to your request'));
        $this->_redirect('gallery');
    }
    
    
    public function quoteloginAction()
    {
       extract($_REQUEST);
       
        
        Mage::app($store);
               
            if($email && $pass)
            {
                        $email=$email;
                        $password=$pass;
                    $session = Mage::getSingleton('customer/session', array("name"=>"frontend"));
        
                    try {
                              $log = $session->login($email, $password);
                              $session->setCustomerAsLoggedIn($session->getCustomer());
                              $customer_id=$session->getCustomerId();
        
                              $send_data["success"]=true;
                              $send_data["message"]="Login Success";
                              $send_data["customer_id"]=$customer_id;
                              $customer = Mage::getSingleton('customer/session')->loginById($customer_id);
                        } 
        
                        catch (Exception $ex) {
                                    $send_data["success"]=false;
                                        $send_data["message"]=$ex->getMessage();
                                    }
            }
            else
            {
                    $send_data["success"]=false;
                    $send_data["message"]="Enter both Email and Password";
            }
            
           // echo json_encode($send_data);
        
        if($send_data["customer_id"] != '')
        {
            
            $countryCode = Mage::getStoreConfig('general/country/default');
            //echo "code :".$countryCode;
            $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
            
            $countryCollection = Mage::getModel('directory/country_api')->items();
            
            $session = Mage::getSingleton('customer/session');
           $customerData = Mage::getModel('customer/customer')->load($send_data["customer_id"])->getData();
           
           
           $Fname = $customerData['firstname'];
            $Lname = $customerData['lastname'];
            $Email = $customerData['email'];
            
            $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
           if ($customerAddressId){
                  $address = Mage::getModel('customer/address')->load($customerAddressId);
                 $address_data = $address->getData();
                 $street1 = $address_data['street'];
                 $telephone = $address_data['telephone'];
                 $country_id = $address_data['country_id'];
                 $postcode = $address_data['postcode'];
                 $region = $address_data['region'];
                 $city = $address_data['city'];
           }
         
          
           echo '<h2>Get a Quote</h2>';
                                        
                                         if($session->isLoggedIn()) { } else { 
                                        
                                        echo '<div class="login-form" id="customerquote-login">
                                        <h3>Returning Customers</h3>
                                            <form id="customerquote-login-form" method="post" action="">
                                                
         
                                                <ul class="form-list">
                                                <li class="email">
                                                    <label class="required" for="email"><em>*</em>Email Address</label>
                                                    <div class="input-box">
                                                    <input type="text" title="Email Address" class="input-text required-entry validate-email" id="email" value="" name="login[username]">
                                                    </div>
                                                </li>
                                                <li class="password">
                                                    <label class="required" for="pass"><em>*</em>Password</label>
                                                    <div class="input-box">
                                                    <input type="password" title="Password" id="pass" class="input-text required-entry validate-password" name="login[password]">
                                                    </div>
                                                </li>
                                                </ul>
                                            <button id="customerquote-login" name="login" title="Login" class="button login" onclick="quote_login()" type="button"><span><span>Login</span></span></button>
                                            </form>                    
                                        </div>
                                        <!--           End Login                     -->';
                                       } 
                                        
                                     if($session->isLoggedIn()) { 
                                        echo '<div id="customerquote-welcome">';
                                         echo $customerData['firstname'].' '.$customerData['lastname'];  
                                        echo '</div>';
                                        } 
                                        echo '<div id="customerquote-info-wrapper">
                                        <form method="post" id="customerquote-customer-form" action="'.Mage::getBaseUrl().'Quotation/Quote/SendRequestFromCart/">
                                            <div style="display:none;">
                                            <input name="customer_id" id="customer_id" value="'.$send_data["customer_id"].'"/>';
                                          
                                                $checkoutSession = Mage::getSingleton('checkout/session');
                                                
                                               
                                               echo '<table cellspacing="0" class="data-table" id="quotation-request-products">
        <thead>
                <tr>
                    <th>Product</th>
                    <th width="100">Qty</th>
                </tr>
        </thead>
        <tbody>';
               foreach($checkoutSession->getQuote()->getAllItems() as $item): 
                         if (($item->getProduct()->gettype_id() == 'simple') || ($item->getProduct()->gettype_id() == 'virtual') || ($item->getProduct()->gettype_id() == 'downloadable')): 
                    echo '<tr>
                        <td>
                                <a href="'.$item->getProduct()->getProductUrl().'">'.$item->getName().'</a>
                          
                        </td>
                        <td width="100"><input size="5" type="text" name="qty_'.$item->getProduct()->getId().'" id="qty_'.$item->getProduct()->getId().'" value="'.$item->getQty().'"></td>
                    </tr>';
                 endif; 
                endforeach;
        echo '</tbody>
        </table>
                                                <textarea cols="110" rows="10" id="description" name="description">test</textarea>
                                            </div>';
                                            
                                            $CurrentUrl = Mage::helper('core/url')->getCurrentUrl();
                                           $url = end(explode(Mage::getBaseUrl(),$CurrentUrl));
                                       
                                           echo '<input type="hidden" name="redirect_url" id="redirect_url" value="'.$redirect_url.'">
                                        <div class="new-customer">
                                        </div>
                                        
                                        <div class="returning-customer">
                                        <h3>'.$customerText.'</h3>
                                        <!--<form id="customerquote-return-customer-form">-->
                                        <fieldset>
                                        
                                        
                                        <div class="customer-infomation">
                                        <ul class="form-list">
                                        <li><label class="required" for="firstname"><em>*</em>First Name</label>
                                        <div class="input-box validation-passed" id="">
                                        <input type="text" class="input-text required-entry validate-length minimum-length-1 maximum-length-255 validation-passed" value="'.$Fname.'" name="firstname" id="firstname">
                                        </div>
                                        </li>
                                        <li><label class="required" for="lastname"><em>*</em>Last Name</label>
                                        <div class="input-box validation-passed" id="">
                                        <input type="text" class="input-text required-entry validate-length minimum-length-1 maximum-length-255 validation-passed" value="'. $Lname.'" name="lastname" id="lastname">
                                        </div>
                                        </li>
                                        <li><label class="required" for="email"><em>*</em>Email</label>
                                        <div class="input-box validation-passed" id="">
                                        <input type="text" class="input-text required-entry validate-email validation-passed" value="'.$Email.'" name="billing_email" id="billing_email">
                                        </div>
                                        </li>
                                        <li><label for="company_name">Company</label>
                                        <div class="input-box" id="">
                                        <input type="text" class="input-text validation-passed" value="" name="company_name" id="company_name">
                                        </div>
                                        </li>
                                        <li><label for="personal_phone">Phone Number</label>
                                        <div class="input-box" id="">
                                        <input type="text" class="input-text validation-passed" value="" name="personal_phone" id="personal_phone">
                                        </div>
                                        </li>
                                        <li><label class="required" for="acee_how_did_you_hear_about_us"><em>*</em>How did you hear about us?</label>
                                        <div class="input-box validation-passed" id="">
                                        <select class="required-entry validation-passed" name="acee_how_did_you_hear_about_us" id="acee_how_did_you_hear_about_us">
                                        <option value=""></option>
                                        <option selected="selected" value="Banner Advertisement">Banner Advertisement</option>
                                        <option value="Business.com">Business.com</option>
                                        <option value="eBay">eBay</option>
                                        <option value="Email">Email</option>
                                        <option value="Facebook">Facebook</option>
                                        <option value="Google">Google</option>
                                        <option value="LinkedIn">LinkedIn</option>
                                        <option value="MSN">MSN</option>
                                        <option value="Other">Other</option>
                                        <option value="Prior Customer">Prior Customer</option>
                                        <option value="RedZee.com">RedZee.com</option>
                                        <option value="Referral">Referral</option>
                                        <option value="Review Sites / Online Directories">Review Sites / Online Directories</option>
                                        <option value="Telemarketing">Telemarketing</option>
                                        <option value="Twitter">Twitter</option>
                                        <option value="Yahoo!">Yahoo!</option>
                                        </select>
                                        </div>
                                        </li>
                                        <li><label for="salesrep">Service Rep ID</label><input type="text" id="salesrep" name="salesrep" value="" class="salesrep input-text validate-length validation-passed"></li>
                                        <li><label for="salesrep">Comment</label><textarea  id="comment" name="comment" class="comment"></textarea></li>
                                        </ul>
                                        </div>
                                        
                                        </fieldset>
                                        
                                        
                                        <h3>Shipping Address</h3>
                                        <fieldset class="">
                                        <input type="hidden" id="top_shipping_address_id" value="" name="shipping_address_id">	
                                        <input type="hidden" id="top_shipping:address_id" value="" name="shipping[address_id]">
                                        <ul class="form-list">
                                        <li>
                                        <label class="required" for="top_shipping:street1"><em>*</em>Address</label>
                                        <div class="input-box validation-error" id="">
                                        <input type="text" onchange="customerQuote.checkSameAsBilling();" class="input-text required-entry street1" value="" id="top_shipping:street1" name="shipping[street][]" title="Street Address">
                                        </div>
                                        
                                        
                                        <label> </label>
                                        <div class="input-box" id="">
                                        <input type="text" onchange="customerQuote.checkSameAsBilling();" class="input-text validation-passed" value="" id="top_shipping:street2" name="shipping[street][]" title="Street Address 2">
                                        </div>
                                        
                                        </li>
                                        
                                        <li class="fields">
                                        <div class="field">
                                        <label class="required" for="top_shipping:city"><em>*</em>City</label>
                                        <div class="input-box validation-error" id="">
                                        <input type="text" onchange="customerQuote.checkSameAsBilling();" id="top_shipping:city" class="input-text required-entry shipping-city" value="" name="shipping[city]" title="City">
                                        </div>
                                        </div>
                                        <div class="field" id="">
                                        <label class="required" for="top_shipping:region"><em>*</em>State/Province</label>
                                        <div class="input-box validation-error" id="">
                                        
                                        <select style="" class="validate-select shipping-region-id" title="State/Province" name="shipping[region_id]" id="top_shipping:region_id" defaultvalue="">
                                        <option value="">Please select region, state or province</option>';
                                   
                                        foreach($regionCollection as $region) {
                                       
                                        echo '<option value="'.$region['code'].'" >'.$region['name'] .'</option>';
                                      
                                        }
                                      
                                        echo '</select>
                                        
                                        
                                        
                                        <input type="text" onchange="customerQuote.checkSameAsBilling();" style="display:none;" class="input-text shipping-region validation-passed" title="State/Province" value="" name="shipping[region]" id="top_shipping:region">
                                        </div>
                                        </div>
                                        </li>
                                        <li class="fields">
                                        <div class="field">
                                        <label class="required" for="top_shipping:postcode" id=""><em>*</em>Zip/Postal Code</label>
                                        <div class="input-box validation-error" id="">
                                        <input type="text" onchange="customerQuote.checkSameAsBilling();" class="input-text validate-zip-international required-entry " value="" id="top_shipping:postcode" name="shipping[postcode]" title="Zip/Postal Code">
                                        </div>
                                        </div>
                                        <div class="field">
                                        <label class="required" for="top_shipping:country_id"><em>*</em>Country</label>
                                        <div class="input-box validation-passed" id="">
                                        <select onchange="updateTopShippingRegion()" title="Country" class="validate-select shipping-country" id="top_shipping:country_id" name="shipping[country_id]">
                                            <option value=""> </option>';
                                           
                                                foreach($countryCollection as $country) {
                                               
                                               echo  '<option value="'.$country['country_id'].'" >'. $country['name'].'</option>';
                                              
                                                }
                                             
                                        echo '</select>
                                        </div>
                                        </div>
                                        </li>
                                        <li class="fields">
                                        <div class="field">
                                        <label class="required" for="shipping:telephone"><em>*</em>Telephone</label>
                                        <div class="input-box validation-error" id="">
                                        <input type="text" onchange="customerQuote.checkSameAsBilling();" id="top_shipping:telephone" class="input-text required-entry telephone " title="Telephone" value="" name="shipping[telephone]">
                                        </div>
                                        </div>
                                        <div class="field">
                                        <div class="inhanddate-select"><label for="inhanddate">In Hand Date &nbsp;&nbsp; </label><input type="text" style="margin-right:5px;" class="input-text validate-date validation-passed" value="" id="inhanddate" name="in_hand_date"> <img id="inhanddate_trig" title="Select Date" class="v-middle" alt="Select Date" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/default/default/images/grid-cal.gif"></div>
        
                                        </div>
                                        </li>     
                                        <li class="no-display"><input type="hidden" value="0" id="top_shipping_save_in_address_book" name="shipping[save_in_address_book]"></li>
                                        
                                        </ul>     
                                        
                                        </fieldset>					    <h3 class="billing-h3">Billing Address</h3>
                                        <span class="control">
                                        <input type="checkbox" class="checkbox validation-passed" onclick="same_billing(this.checked)" title="Same as Shipping Address" value="1" id="top_billing:same_as_shipping" name="billing[same_as_shipping]"><label for="top_billing:same_as_shipping">Same as Shipping Address</label>
                                        </span>
                                        <fieldset>
                                        <input type="hidden" id="top_billing_address_id" value="" name="billing_address_id">	
                                        <input type="hidden" id="top_billing:address_id" value="28649" name="billing[address_id]">
                                        <ul class="form-list">
                                        <li>
                                        <label class="required" for="top_billing:street1"><em>*</em>Address</label>
                                        <div class="input-box validation-passed" id="">
                                        <input type="text" onchange="customerQuote.setSameAsBilling(false);" class="input-text required-entry street1 validation-passed" value="'.$street1.'" id="top_billing:street1" name="billing[street][]" title="Street Address">
                                        </div>
                                        
                                        
                                        <label> </label>
                                        <div class="input-box" id="">
                                        <input type="text" onchange="customerQuote.setSameAsBilling(false);" class="input-text validation-passed" value="" id="top_billing:street2" name="billing[street][]" title="Street Address 2">
                                        </div>
                                        
                                        </li>
                                        
                                        <li class="fields">
                                        <div class="field">
                                        <label class="required" for="top_billing:city"><em>*</em>City</label>
                                        <div class="input-box validation-passed" id="">
                                        <input type="text" onchange="customerQuote.setSameAsBilling(false);" id="top_billing:city" class="input-text required-entry billing-city validation-passed" value="'.$city.'" name="billing[city]" title="City">
                                        </div>
                                        </div>
                                        <div class="field" id="">
                                        <label class="required" for="top_billing:region"><em>*</em>State/Province</label>
                                        <div class="input-box validation-passed" id="">
                                        <select onchange="customerQuote.setSameAsBilling(false);" style="" class="validate-select billing-region-id validation-passed" title="State/Province" name="billing[region_id]" id="top_billing:region_id" defaultvalue="23">
                                        <option value="">Please select region, state or province</option>';
                                        
                                   
                                        foreach($regionCollection as $region) {
                                           
                                           $selected = ''; 
                                        if($region == $region['code'])
                                        $selected = 'selected';
                                       echo ' <option value="'.$region['code'] .'"'.$selected.' >'.$region['name'].'</option>';
                                        
                                        }
                                   
                                        
                                       echo ' </select>
                                        
                                        <input type="text" style="display:none;" class="input-text billing-region validation-passed" title="State/Province" value="Illinois" name="billing[region]" id="top_billing:region">
                                        </div>
                                        </div>
                                        </li>
                                        <li class="fields">
                                        <div class="field">
                                        <label class="required" for="top_billing:postcode" id=""><em>*</em>Zip/Postal Code</label>
                                        <div class="input-box validation-passed" id="">
                                        <input type="text" onchange="customerQuote.setSameAsBilling(false);" class="input-text validate-zip-international required-entry validation-passed" value="'.$postcode.'" id="top_billing:postcode" name="billing[postcode]" title="Zip/Postal Code">
                                        </div>
                                        </div>
                                        <div class="field">
                                        <label class="required" for="top_billing:country_id"><em>*</em>Country</label>
                                        <div class="input-box validation-passed" id="">
                                        <select onchange="updateTopBillingRegion()" title="Country" class="validate-select billing-country validation-passed" id="top_billing:country_id" name="billing[country_id]"><option value=""> </option>';
                                       
                                            foreach($countryCollection as $country) {
                                               
                                               $selected = ''; 
                                            if($country_id == $country['country_id'])
                                            $selected = 'selected';
                                           echo ' <option value="'. $country['country_id'].'" '.$selected.' >'.$country['name'].'</option>';
                                           
                                            }
                                           
                                        echo '</select>
                                        </div>
                                        </div>
                                        </li>
                                        <li class="fields">
                                        <div class="field">
                                        <label class="required" for="billing:telephone"><em>*</em>Telephone</label>
                                        <div class="input-box validation-passed" id="">
                                        <input  type="text" onchange="customerQuote.setSameAsBilling(false);" id="top_billing:telephone" class="input-text required-entry telephone validation-passed" title="Telephone" value="'.$telephone.'" name="billing[telephone]">
                                        </div>
                                        </div>
                                        </li>     
                                        <li class="no-display">
                                        <input type="hidden" value="0" id="top_billing_save_in_address_book" name="billing[save_in_address_book]"></li>
                                        
                                        </ul>     
                                        
                                        </fieldset>					    					    	<!--			    </form>-->
                                        </div>
                                        <div style="display:none;" id="ship"></div><div id="ship_message" style="display:none; color:red;">Please select a option</div>
                                        </form> 
                                        </div>';
                
                                                                           $words  = "abcdefghijlmnopqrstvwyz";
                $vocals = "aeiou";
                
                $text  = "";
                $vocal = rand(0, 1);
                $length = rand(5, 8);
                for ($i=0; $i<$length; $i++) {
                    if ($vocal) {
                        $text .= substr($vocals, mt_rand(0, 4), 1);
                    } else {
                        $text .= substr($words, mt_rand(0, 22), 1);
                    }
                    $vocal = !$vocal;
                }
                Mage::getSingleton("core/session",array('name' => 'frontend'))->setCaptcha($text);
                
                
            echo '<div class="captcha_img">
                            <img src="'.str_replace('/13expo/','/',Mage::getBaseUrl()).'externalform/captcha.php?ts='.time().'&text='.$text.'" id="captcha" />	
                    </div>
                    
                    <div class="captcha_input">
                            <input type="text" name="captcha" id="captcha-form" autocomplete="off" />
                    </div>
                    <div class="req-head">
                         <span class="fontRed">*</span> Required
                     </div>
                </div>';     
                                        
                  
        
        
                echo '<div id="save_button" class="button-wrapper">
                        <button onclick="email_open();" type="button" title="Save &amp; Generate Quote" class="submit button save-quote" id="customerquote-save-button" value="Save &amp; GSubmitCreateForm1()enerate Quote"><span><span>Save &amp; Generate Quote</span></span></button>
                </div>
                
                <div id="email_button" style="display: none" class="button-wrapper">
                        <button onclick="SubmitCreateForm();" type="button" title="Email Quote" class="submit button email-quote" id="customerquote-email-button" value="Email Quote">
                                <span><span>Email Quote</span></span>
                        </button>
                </div>';
                 
        }
        else
        {
            echo 'wrong';
        } 
    }
    
    
    public function shippingAction()
    {
        extract($_REQUEST);

        //$zipcode = '2000';
        //$country = 'AU';
        
        $zipcode = $zipcode;
        $country = $country;
        
        $ses_captcha = Mage::getSingleton("core/session",array('name' => 'frontend'))->getCaptcha();
        if($ses_captcha == $captcha)
        {
            // Update the cart's quote.
            $cart = Mage::getSingleton('checkout/cart');
            $address = $cart->getQuote()->getShippingAddress();
            $address->setCountryId($country)
                    ->setPostcode($zipcode)
                    ->setRegion($state)
                    ->setRegionId($state)
                    ->setCity($city)
                    ->setCollectShippingrates(true);
                    
                   
            $cart->save();
            
            // Find if our shipping has been included.
            $rates = $address->collectShippingRates()
                             ->getGroupedAllShippingRates();
            
            //$carriers = Mage::getStoreConfig('carriers', Mage::app()->getStore()->getId());
            //foreach ($carriers as $carrierCode => $carrierConfig) {
            ////echo "<pre>";print_R($carrierConfig);echo "<pre>";
            //}
            
            
    //        foreach ($rates as $carrier) {
    //    foreach ($carrier as $rate) {
    //        print_r($rate->getData());
    //    }
    //}
            
            
            foreach ($rates as $carrier) {
                foreach ($carrier as $rate) {
                   // print_r($rate->getData());
                    $allData = $rate->getData();
                    echo '<div><input type="radio" name="ship_method" id="'.$allData['code'].'" value="'.$allData['code'].'***'.$allData['carrier_title'].'/'.$allData['method_title'].'***'.$allData['price'].'"/>'. $allData['carrier_title'].'/'.$allData['method_title'].' '.$allData['price'].'</div>';
                }
            }
        }
        else
        {
            echo 'not match';
            
            echo '@@@@@@';
            
                                                        $words  = "abcdefghijlmnopqrstvwyz";
                $vocals = "aeiou";
                
                $text  = "";
                $vocal = rand(0, 1);
                $length = rand(5, 8);
                for ($i=0; $i<$length; $i++) {
                    if ($vocal) {
                        $text .= substr($vocals, mt_rand(0, 4), 1);
                    } else {
                        $text .= substr($words, mt_rand(0, 22), 1);
                    }
                    $vocal = !$vocal;
                }
                Mage::getSingleton("core/session",array('name' => 'frontend'))->setCaptcha($text);
                
                
            echo '
                            <img src="'.str_replace('/13expo/','/',Mage::getBaseUrl()).'externalform/captcha.php?ts='.time().'&text='.$text.'" id="captcha" />	
                    </div>
                    
                    <div class="captcha_input">
                            <input type="text" name="captcha" id="captcha-form" autocomplete="off" />
                    </div>
                    <div class="req-head">
                         <span class="fontRed">*</span> Required
                     </div>
               ';
        }
    }
        
     
     public function createorderAction() {
            
        Mage::register('isSecureArea', 1);
        
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
                $temptableOrderItem = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
        
                
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
                        /********************** Start for bundle product 12_02_2014 ********************************/
                            $rowTotal = $_product->getPrice() * $product['qty'];
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
                                        ->setPrice($_product->getPrice())
                                        ->setBasePrice($_product->getPrice())
                                        ->setOriginalPrice($_product->getPrice())
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
                                        ->setPrice($product_bundle->getPrice())
                                        ->setBasePrice($product_bundle->getPrice())
                                        ->setOriginalPrice($product_bundle->getPrice())
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
                        $rowTotal = $_product->getPrice() * $product['qty'];
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
                                    ->setPrice($_product->getPrice())
                                    ->setBasePrice($_product->getPrice())
                                    ->setOriginalPrice($_product->getPrice())
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
                
                //$order->setUseInstallments(2);
                
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
                
                //start for add installment 14_04_2014
                //$temptableInstallment = Mage::getSingleton('core/resource')->getTableName('itweb_installments');
                //$temptableInstallmentPay = Mage::getSingleton('core/resource')->getTableName('itweb_installments_payments');
                //$temptableOrderNow = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
                //
                //$connectionWrite->beginTransaction();
                //$data2 = array();
                //$data2['use_installments'] = 2;
                //$where = $connectionWrite->quoteInto('entity_id =?', $order->getId());
                //$connectionWrite->update($temptableOrderNow, $data2, $where);
                //$connectionWrite->commit();
                //
                //
                //$connectionWrite->beginTransaction();
                //$data = array();
                //$data['order_id']= $order->getId();
                //$data['state']= 1;
                //$data['num_of_installments']=2;
                //$data['created_at']=NOW();
                //$data['total_due']=$order->getTotalDue();
                //$connectionWrite->insert($temptableInstallment, $data);
                //$connectionWrite->commit();
                //
                //$lastInsertId  = $connectionWrite->fetchOne('SELECT last_insert_id()');
                //
                //for($k=1;$k<=2;$k++)
                //{
                //    $connectionWrite->beginTransaction();
                //    $data1 = array();
                //    $data1['installment_id']= $lastInsertId;
                //    //$data['num_of_installments']=2;
                //    $data1['created_at']=NOW();
                //    $data1['amount_due']=$order->getTotalDue()/2;
                //    $data1['amount_paid']=$order->getTotalPaid();
                //    $connectionWrite->insert($temptableInstallmentPay, $data1);
                //    $connectionWrite->commit();
                //}
                
                //end for add installment 14_04_2014
                
                    
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
                
                
                
                //$alert=array('Create order From Quote');
                //Mage::getModel('systemalert/systemalert')->sendalert($alert,'order',$order);
                
                // For deleting the quote
                if($order->getId() != '')
                {
                   $quote = Mage::getModel("Quotation/Quotation")->load($quote_id);
                   $quote->delete();
                }
                
               // print_r($orderItem);
                
              
               // Mage::dispatchEvent('model_save_after', array('object'=>Mage::getModel("Quotation/Quotation")));
                //mage::helper('AdminLogger')->updatelog($quote_id,'Create order From Quote');
                
                
                       
               // exit();
                //$url = Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view/order_id/".$order->getId());
                //$url = str_replace('p//s','p/admin/s',$url);
                ////exit();
                //Mage::log($url);
                //Mage::app()->getResponse()->setRedirect($url);
                //exit;
                
                $this->_redirect('sales/order/view/order_id/'.$order->getId().'/');
        }
    }
    
    public function addcommentorderAction() {
        
        extract($_REQUEST);
		
      // $quote_id = $this->getRequest()->getParam('id');
	   
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $order_id = $id;
        $order = Mage::getModel("sales/order")->load($order_id);
        $tableHistory = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
        
        //$sqlHistorySystem="INSERT INTO   ".$tableHistory."  SET  qh_quotation_id = '".$quote_id."', qh_message = '".$comment."', qh_date = NOW() , qh_user = 'customer'";
        // 
        // try {
        //         $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHistorySystem);
        // } catch (Exception $e){
        // //echo $e->getMessage();
        // }
         
        $connectionWrite->beginTransaction();
        $data = array();
        $data['parent_id']= $order_id;
        $data['comment']='CUSTOMER -'.$comment;
        $data['created_at']= NOW();
        $data['entity_name']= 'order';
		$data['readstatus']= 1;
        $connectionWrite->insert($tableHistory, $data);
        $connectionWrite->commit();
		
		
		/////read status update in sales falt order  table as well
		$tableSalesOrder = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
        
		$connectionWrite->beginTransaction();
        $data = array();        
		$data['readstatus']= 1;
		$where = $connectionWrite->quoteInto('entity_id=?',$order_id);
        $connectionWrite->update($tableSalesOrder, $data, $where);
        $connectionWrite->commit();
        
		
		 /////selecting from tables order history 
        $tableHistory = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
        
        $select = $connectionRead->select()
        ->from($tableHistory, array('*'))
        ->where('parent_id=?',$order_id)
        ->where('comment!=?','Created')
        ->order('entity_id ASC');
        
        $fetchHistory = $connectionRead->fetchAll($select);
    
        //$sqlHistorySystem="SELECT * FROM  ".$tableHistory."  WHERE  qh_quotation_id = '".$quote_id."' AND qh_message != 'Created' ORDER BY qh_id DESC";
        //
        //try {
        //$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHistorySystem);
        //$fetchHistory = $chkSystem->fetchall();
        //} catch (Exception $e){
        ////echo $e->getMessage();
        //}
        
        foreach($fetchHistory as $history)
        {
           $message = nl2br($history['comment']);
		   if((strpos($message,'CUSTOMER') !== false)){
                                    echo '<li class="clearer odd">
                                    <table width="100%">
                                    <tbody>
                                    <tr class="customer-comment">';
									echo '<td class="comment-additional" width="35%" style="padding:10px 22px;background-color:#99BB1E;">';
                          	
								echo ' '. Mage::app()->getLocale()->date($history['created_at'])->toString($format).'</td>   <td class="comment-content" style="padding:10px 22px;';
								/*
									echo ' '. date_format(date_create($history['created_at']), 'm/d/Y H:i A').'
									  	 </td>   <td class="comment-content" style="padding:10px 22px;';
										 */				
								
								echo '">';
								 				   
								echo  nl2br($history['comment']).'
                                                                                            </td>
                                    </tr>
                                    </tbody>
                                    </table>
                            </li>';
		    } else {
                                    echo '<li class="clearer odd">
                                    <table width="100%">
                                    <tbody>
                                    <tr class="customer-comment">';
									echo '<td class="comment-additional" width="35%" style="padding:10px 22px;">';
                                	echo ' '. Mage::app()->getLocale()->date($history['created_at'])->toString($format).'
									  	 </td>   <td class="comment-content" style="padding:10px 22px; ';
								echo '">';
								 				   
								echo  nl2br($history['comment']).'
                                                                                            </td>
                                    </tr>
                                    </tbody>
                                    </table>
                            </li>';
				
				
				
			}
      
        }
        
        /************************** Start for add ticket in order 16_04_2014 *************************************/
                
        /*********************** Start for load the ticket **************************/
	$collection1 = Mage::getModel('CrmTicket/Ticket')
                ->getCollection()
                ->addFieldToFilter('ct_object_id', 'order_' .$order_id);
	if($collection1){
		foreach($collection1 as $ticket)
		{
			$ticket_id = $ticket->getId();
		}
	}
	/*********************** End for load the ticket **************************/
                 
        $messageData['ctm_content'] = $comment;

        //$needNotify = 'sales@13expo.com.au';
        $needNotify = 1;
        $newMessageData = $this->getRequest()->getPost('new_message');
        $newMessageData['ctm_content'] = $comment;
        
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customerData->getId();

        $isNewTicket = true;        
        
        // load ticket
        $ticket = mage::getModel('CrmTicket/Ticket');

         if($ticket_id == '')
         {
            $ticket->setData('ct_subject', 'Add new ticket by comment');
            $ticket->setData('ct_object_id', 'order_'.$order_id);
            $ticket->setct_manager(2);
            $ticket->setct_status(MDN_CrmTicket_Model_Email::STATUS_NEW);
            $ticket->setct_customer_id($customerId);
            $ticket->setct_email_account('sales@13expo.com.au');
            
            $currentDateTime = date('Y-m-d H:i:s');

            $ticket->setct_created_at($currentDateTime);
            $ticket->setct_updated_at($currentDateTime);
            
            $storeId = Mage::app()->getStore()->getStoreId();
            $ticket->setct_store_id($storeId);
            
            // save
            $ticket->save();
         }
         else{
            
            $ticket = mage::getModel('CrmTicket/Ticket')->load($ticket_id);;
            
         }
         
         //default is TYPE_FORM but can be overridde
            $additionalDatas = array();
            $additionalDatas['ctm_source_type'] = MDN_CrmTicket_Model_Message::TYPE_FORM;
          

            // $author, $content, $contentType, $isPublic
            $ticket->addMessage(
                    MDN_CrmTicket_Model_Message::AUTHOR_CUSTOMER, 
                    $messageData["ctm_content"],
                    MDN_CrmTicket_Model_Message::CONTENT_TYPE_TEXT,
                    $isPublic,
                    $additionalDatas,
                    true,
                    $attachments);

          $message = $ticket->notify(MDN_CrmTicket_Model_Message::AUTHOR_ADMIN);
             
            /************************** End for add ticket in order 16_04_2014 *************************************/
         
        // $this->_redirect('*/*/View/quote_id/'.$quote_id.'/');
        }    
}
