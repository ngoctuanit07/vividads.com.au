<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once 'Mage/Checkout/controllers/OnepageController.php';

class Artis_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    protected $_sectionUpdateFunctions = array(
        'payment-method'  => '_getPaymentMethodsHtml',
        'shipping-method' => '_getShippingMethodsHtml',
        'review'          => '_getReviewHtml',
    );

    /** @var Mage_Sales_Model_Order */
    protected $_order;

    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();

        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }

        if(!$this->_canShowForUnregisteredUsers()){
            $this->norouteAction();
            $this->setFlag('',self::FLAG_NO_DISPATCH,true);
            return;
        }

        return $this;
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    /**
     * Get shipping method step html
     *
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getAdditionalHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        Mage::getSingleton('core/translate_inline')->processResponseBody($output);
        return $output;
    }

    /**
     * Get order review step html
     *
     * @return string
     */
    protected function _getReviewHtml()
    {
        return $this->getLayout()->getBlock('root')->toHtml();
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Checkout page
     */
    public function indexAction()
    {
       
		
		if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                Mage::getStoreConfig('sales/minimum_order/error_message') :
                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function progressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function shippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Order success action
     */
    public function successAction()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }
		
		$order = Mage::getModel('sales/order')->load($lastOrderId);
		
		
		////creating invoice ///
		
		$hasinvoice = $order->hasInvoices();
	  ///saving payment ///
	  if(!$hasinvoice){
	  	$this->Savepayment($lastOrderId, 1);
	  }
		
        $session->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
         
		$this->renderLayout();
		
    }

    public function failureAction()
    {
        $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }


    public function getAdditionalAction()
    {
        $this->getResponse()->setBody($this->_getAdditionalHtml());
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = $this->getOnepage()->getAddress($addressId);

            if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
                $this->getResponse()->setHeader('Content-type', 'application/x-json');
                $this->getResponse()->setBody($address->toJson());
            } else {
                $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            }
        }
    }

    /**
     * Save checkout method
     */
    public function saveMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
//            $postData = $this->getRequest()->getPost('billing', array());
//            $data = $this->_filterPostData($postData);
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
		
	//	return true;
		
        if ($this->getRequest()->isPost()) {
         
		    $data = $this->getRequest()->getPost('shipping', array());
         
		    $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            
			
			
			if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
           
		   
		   
		    $result = $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			
        }
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Save payment ajax action
     *
     * Sets either redirect or a JSON response
     */
    public function savePaymentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Get Order by quoteId
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');
            if (!$this->_order->getId()) {
                throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
            }
        }
        return $this->_order;
    }

    /**
     * Create invoice
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice()
    {
        $items = array();
        foreach ($this->_getOrder()->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }
        /* @var $invoice Mage_Sales_Model_Service_Order */
        $invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice($items);
        $invoice->setEmailSent(true)->register();

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * Create order action
     */
    public function saveOrderAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        
		try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                
				if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            
			if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
            
			$this->getOnepage()->saveOrder();	

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
        
		} catch (Mage_Payment_Model_Info_Exception $e) {
            
			$message = $e->getMessage();
            
			if( !empty($message) ) {
                $result['error_messages'] = $message;
            }
            
			$result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
			
			
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

				if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
					
					if (isset($this->_sectionUpdateFunctions[$updateSection])) {
						$updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
						$result['update_section'] = array(
							'name' => $updateSection,
							'html' => $this->$updateSectionFunction()
						);
					}
					$this->getOnepage()->getCheckout()->setUpdateSection(null);
				}
        
		 
		 
		 } catch (Exception $e) {
            
			Mage::logException($e);
          
		  //  Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
			//Zend_debug::dump($e);
			
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }
		
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }

    /**
     * Check can page show for unregistered users
     *
     * @return boolean
     */
    protected function _canShowForUnregisteredUsers()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn()
            || $this->getRequest()->getActionName() == 'index'
            || Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote())
            || !Mage::helper('checkout')->isCustomerMustBeLogged();
    }
	
	
	
	/**
     * Change sales order payment (from sales order shee)
     *
     */
    public function Savepayment($order_id=0, $payment_validated=0) {
        //recupere les infos
        
		//$orderId = $this->getRequest()->getParam('order_id');
        //$value = $this->getRequest()->getParam('payment_validated');
		$orderId = $order_id;
		$value  =  $payment_validated; 		
		
        //Charge la commande et modifie
        $order = mage::getModel('sales/order')->load($orderId);
		$order_validated = $order->getpayment_validated();
		
        /********************* Start for update all module for order 12_03_2014 *******************************/
        if($value == '1' && $order_validated !=1 )
        {
           // echo $value;
            $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
      		
			 $order->setpayment_validated($value)->save();  
		   
		    //Start 03_03_2014
	        $invoice = $order->prepareInvoice();
            
            $invoice->register();
            Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->save();
            
          //  $invoice->sendEmail(true, '');
	    //End 03_03_2014
            
            $items = $order->getAllItems();
            
			foreach ($items as $item) {
                
                $ProductId = $item->getProductId();
                
                $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
                if($connectionWrite->isTableExists($temptableOrganiger))
                {
                    if($connectionWrite->isTableExists($temptableOrganiger))
                    {
                        //$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'";
                        $sqlOrganiger = $connectionRead->select()
					->from($temptableOrganiger, array('*'))
					->where("ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'");
                        $chkOrganiger = $connectionRead->fetchAll($sqlOrganiger);
                    }
                    
                    if($chkOrganiger)
                    {
                        if($connectionWrite->isTableExists($temptableOrganiger))
                        {
                           
                           $connectionWrite->beginTransaction();
                                           $data = array();
                                           $data['ot_created_at'] = NOW(); 
                                           $data['ot_author_user'] = $chkOrganiger[0]['ot_author_user'];
                                           $data['ot_target_user'] = $chkOrganiger[0]['ot_target_user']; 
                                           $data['ot_caption']= addslashes($chkOrganiger[0]['ot_caption']); 
                                           $data['ot_description'] = addslashes($chkOrganiger[0]['ot_description']); 
                                           $data['ot_deadline'] = $chkOrganiger[0]['ot_deadline']; 
                                           $data['ot_notify_date'] = $chkOrganiger[0]['ot_notify_date']; 
                                           $data['ot_priority'] = $chkOrganiger[0]['ot_priority']; 
                                           $data['ot_finished'] = $chkOrganiger[0]['ot_finished'];
                                           $data['ot_read'] =$chkOrganiger[0]['ot_read']; 
                                           $data['ot_origin'] =$chkOrganiger[0]['ot_origin']; 
                                           $data['ot_category'] = $chkOrganiger[0]['ot_category']; 
                                           $data['ot_entity_type'] ='order'; 
                                           $data['ot_entity_id'] = $order->getId(); 
                                           $data['ot_entity_description'] = addslashes($chkOrganiger[0]['ot_entity_description']); 
                                           $data['ot_notification_read'] = $chkOrganiger[0]['ot_notification_read'];
                                           $data['ot_task_type'] = $chkOrganiger[0]['ot_task_type'];
                                           $connectionWrite->insert($temptableOrganiger, $data);
                                           $connectionWrite->commit(); 
                        }
                        
                        //For chain task
                        $last_id = $connectionWrite->fetchOne('SELECT last_insert_id()');
                        
                        $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                        if($connectionWrite->isTableExists($temptableChain))
                        {
                            $connectionWrite->beginTransaction();
                                $data = array();
                                $data['task_id']=$last_id;
                                $data['order_quote_id']=$order->getId();
                                $data['product_id']=$chkOrganiger[0]['ot_entity_id'];
                                $data['task_type']=$chkOrganiger[0]['ot_task_type'];
                                $connectionWrite->insert($temptableChain,$data);
                                $connectionWrite->commit();
                        }
                    }
                }
                
                /*********************** add planning auto *********************************/
                $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                if($connectionWrite->isTableExists($temptableShipping))
                {
                    if($connectionWrite->isTableExists($temptableShipping))
                    {
                        //$sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ";
                        $sqlShipping = $connectionRead->select()
                                                ->from($temptableShipping, array('*'))
                                                ->where("quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ");
                        $chkShipping = $connectionRead->fetchAll($sqlShipping);
                    }
                    
                    if(count($chkShipping) == 0)
                    {
                    
                        $created_date = $order->getCreatedAt();
                        
                        $Product = Mage::getModel('catalog/product')->load($ProductId);
                        
                        $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                       // $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
                        $sqlTimeline = $connectionRead->select()
                                        ->from($temptableTimeline, array('*'))
                                        ->where('product_id=?', $ProductId);
                        $chkTimeline = $connectionRead->fetchAll($sqlTimeline);
			 
			if($order->getTotalPaid() > 0)
                        {
                    
                        $stockobj = new MDN_AdvancedStock_MiscController();
						$order_placed_date =  $created_date;
                        $artwork_date = $stockobj->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $stockobj->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $stockobj->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $stockobj->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $stockobj->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
			
			}
			else
			{
			    $order_placed_date = '';
			     $artwork_date = '';
			    $proof_date = '';
			    $production_start_date = '';
			    $shipping_date = '';
			    $delivery_date = '';
			}
                        
                                          
                        $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                        
                        if($connectionWrite->isTableExists($temptableShipping))
                        {
                            //$sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$order->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'order', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
                            //$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                            
                            $connectionWrite->beginTransaction();
                            $data = array();
                            $data['quote_id']= $order->getId();
                            $data['item_id'] = $item->getId();
                            $data['product_id'] = $ProductId; 
                            $data['planning_type'] = 'order'; 
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
                }
                
                     $ProductId = $item->getproductId();
                    $_options = $item->getProductOptions();
                   //if(!empty($_options))
                   //{
                   //
                   //        //print_r($_options['options']);
                   //   
                   //        foreach($_options['options'] as $option){
                   //               
                   //           
                   //                if($option['label'] == 'Graphic Design Service'){
                   //                   
                   //                    if($option['value'] != '')
                   //                    {
                   //                        $title = explode(' ',$option['value']);
                   //                        
                   //                        if (is_numeric($title[0]))
                   //                        $revison_number = $title[0];
                   //                        else
                   //                        $revison_number = 10000;
                   //                    }
                   //                }
                   //           
                   //        }
                           
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
                               $sqlDesign="INSERT INTO  ".$temptableDesign." SET order_id = '".$order->getId()."', type='order', item_id ='".$item->getId()."', product_id = '".$ProductId."', revision_number = '100', assign_to = '".$adminid."', postdate = NOW() ";
                               $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlDesign);
                           }
                  // }
                   
                    /************************** Add the vendor option to individual item in order ********************************************/

                       
                       $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                       $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                       $temptableProofs = Mage::getSingleton('core/resource')->getTableName('proofs');
                       
                       $select = $connectionRead->select()
                       ->from($temptableProofs, array('*'))
                       ->where('order_id=?',$order->getId())
                       ->where('item_id=?',$item->getId())
                       ->where('proof_type=?','order');
                       //$row =$connectionRead->fetchRow($select);
                       $result = $connectionRead->fetchAll($select);
                       
                       if(count($result) > 0)
                       $file_recieved = 'yes';
                       else
                       $file_recieved = 'no';
                       
                       if($result[0]['status'] == 'Approved')
                       $proof_approved = 'yes';
                       else
                       $proof_approved = 'no';
                       
                       if($item->getProductType() != 'bundle')//31_01_2014
                       {                  
                               $temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_item');
                               
                               $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                               
                               $name = $_product->getAttributeText('vendor_id');
                               $target_vendor = Mage::getResourceModel('catalog/product')->getAttribute("vendor_id")->getSource()->getOptionId($name);
                               
                               $connectionWrite->beginTransaction();
                               $data2 = array();
                               $data2['target_user']= $target_vendor;
                               $data2['item_id'] = $item->getId();
                               $data2['order_id'] = $order->getId();
                               $data2['product_id'] = $item->getProductId();
                               $data2['product_sku'] = addslashes($_product->getSku());
                               $data2['postdate'] = NOW();
                               $data2['order_status'] = $order->getStatus();
                               
                               $data2['file_recieved'] = $file_recieved;
                               $data2['proof_approved'] = $proof_approved;
                               $data2['proof_approve_date'] = Now();
                               if($row['quantity'] != '')
                               $data2['qty'] = $result[0]['quantity'];
                               
                               $connectionWrite->insert($temptableVendor, $data2);
                               $connectionWrite->commit();
                       }
                   /************************** Add the vendor option to individual item in order ********************************************/
                
               
                        
            }
            
            


            $_order = Mage::getModel('sales/order')->load($order->getId());
            $paymentName = $_order->getPayment()->getMethodInstance()->getCode();
            
            ///18-11-2013 SOC
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            $tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
            if($_order->getTotalPaid() > 0){
                    //26-11-2013 SOC
                    try {
                        //if($order->canShip()) {
                            
                            //$shipmentid = Mage::getModel('sales/order_shipment_api')
                            //                ->create($order->getIncrementId(), array());
                            //
                            //$ship = Mage::getModel('sales/order_shipment_api')
                            //                ->addTrack($order->getIncrementId(), array());       
                       // }
                    }catch (Mage_Core_Exception $e) {
                    // print_r($e);
                    }
                    //26-11-2013 EOC
                    
                    ///4-3-2014 S ///11-3-2014
                    $totalOrder = 0;
                    $total_qty = 0;
                    $order_items=$order->getAllItems();
                    $itemQty = 0; ///11-3-2014
                    foreach($order_items as $orderDetails)
                    {
                        $prodid=$orderDetails->getProductId();
                        $product = Mage::getModel('catalog/product')->load($prodid);
                        
                        if($product->getTypeId() != 'bundle'){
                            
                            $itemQty += $orderDetails->getQtyOrdered();
                         
                         }
                    }
                    $total_qty = $itemQty;
                    
                    
                    
                    ///4-3-2014 E
                    
                    //21-11-2013 SOC
                    //$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                    //$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
                    
                    $date_post = strtotime($order->getCreatedAtStoreDate()); 
                    $time=date('Y-m-d H:i:s',$date_post );
                    
                    $addressLoadId = $order->getShippingAddress()->getData();
                    $Name = $addressLoadId['firstname'].' '.$addressLoadId['lastname'];
                    
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['store_id']= $order->getStoreId();
                    $data['total_qty']= $total_qty; ///4-3-2014
                    $data['order_id']= $order->getId();
                    $data['status']= 'Pending Shipment';
                    $data['increment_id']=$order->getIncrementId();;
                    $data['order_increment_id']=$order->getIncrementId();
                    $data['created_at']=$time;
                    $data['order_created_at']=$order->getCreatedAt(); ///12-3-2014
                    $data['shipping_name']=$Name;

                    $data['payment_status']=$order->getStatusLabel();
                    //echo "<pre>";print_r($data); exit;
                    $connectionWrite->insert($tableName, $data);
                    $connectionWrite->commit();
                    //21-11-2013 EOC
                    $lastInsertId = $connectionWrite->lastInsertId(); ///27-1-2014
            }
            ///18-11-2013 EOC
            
            
            
            
            if($_order->getTotalDue() == 0)
            {
                $_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                $_order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                
                    ///// 27-1-2014 S//////
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['payment_status']='Paid';
                    $where = $connectionWrite->quoteInto('order_id =?', $_order->getId());
                    $connectionWrite->update($tableName, $data, $where);
                    $connectionWrite->commit();
                    ///// 27-1-2014 E//////
                
                
            }
            else if($_order->getTotalPaid() == 0)
            {
               // $_order->setState('new', true);
               
                $temptableOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
                
                 $connectionWrite->beginTransaction();
                $data3 = array();
               $_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                
                if($paymentName == 'purchaseorder')
                {
                    $_order->setStatus('purchaseorder_pending_payment', true);
                     $data3['status'] = 'purchaseorder_pending_payment';
                }
                elseif($paymentName == 'checkmo')
               {
                    $_order->setStatus('checkmo_pending_payment', true);
                    $data3['status'] = 'checkmo_pending_payment';
                }
                elseif($paymentName == 'directdeposit_au')
                 {
                    $_order->setStatus('awaiting_direct_deposit', true);
                    $data3['status'] = 'awaiting_direct_deposit';
                }
                else
                {
                    $_order->setStatus('pending', true);
                    $data3['status'] = 'pending';
                }
                
                 $where = $connectionWrite->quoteInto('parent_id =?', $order->getId());
                $connectionWrite->update($temptableOrder, $data3, $where);
                $connectionWrite->commit();
            }
            else if($_order->getTotalDue() > 0)
            {
                $temptableOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
               // $_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                $_order->setStatus('partial_payment', true);
                $data3 = array();
                $data3['status'] = 'partial_payment';
                
                $where = $connectionWrite->quoteInto('parent_id =?', $order->getId());
                $connectionWrite->update($temptableOrder, $data3, $where);
                $connectionWrite->commit();
                
                     ///// 27-1-2014 S//////
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['payment_status']='Partial Paid';
                    $where = $connectionWrite->quoteInto('order_id =?', $_order->getId());
                    $connectionWrite->update($tableName, $data, $where);
                    $connectionWrite->commit();
                    ///// 27-1-2014 E//////
            }
            $_order->save();
        }
		$order->setpayment_validated($value)->save();
        //exit;
        /********************* End for update all module for order 12_03_2014 *******************************/
		
        //Confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payment state updated'));
		$output = 1;
<<<<<<< HEAD
		//echo $output;			   
=======
		echo $output;			   
>>>>>>> cc38cda73b9c48c553a60557cdc77660f71f1e30
		return $output;
        //redirige
       // $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }    
}
