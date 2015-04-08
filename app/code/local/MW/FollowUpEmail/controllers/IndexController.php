<?php

class MW_FollowUpEmail_IndexController extends Mage_Core_Controller_Front_Action

{

    public function indexAction()

    {		

		$this->loadLayout();     

		$this->renderLayout();

    }

	

	public function directAction(){

		if ($code = $this->getRequest()->getParam('code')) {

			$code = str_replace(' ','+',$code); 				
			$code = str_replace('special','/',$code); 				
			$value = MW_FollowUpEmail_Helper_Data::decryptCode($code);			

			$pos = strrpos($value, ",");
			if ($pos === false) { // note: three equal signs

			    if (!$queue = Mage::getModel('followupemail/emailqueue')->loadByCode($code)) {

	                Mage::getSingleton('core/session')->addError($this->__('Wrong direct code specified'));

	                $this->_redirect('/');

	                return;

	            }				

	            $customer = Mage::getModel('customer/customer')

	                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())

	                ->loadByEmail($queue->getRecipientEmail());

			



	            if ($customerId = $customer->getId()) {

	                $session = Mage::getSingleton('customer/session');

	                if ($session->isLoggedIn() && $customerId != $session->getCustomerId())

	                    $session->logout();



	                try {

	                    $session->setCustomerAsLoggedIn($customer);

	                } catch (Exception $ex) {

	                    Mage::getSingleton('core/session')->addError($this->__('Your account isn\'t confirmed'));

	                    $this->_redirect('/');

	                }

	            }            

				if($queue->getOrderId() > 0)

					$this->getResponse()->setRedirect(Mage::getUrl("sales/order/view", array('order_id'=>$queue->getOrderId())));

				else

	            	$this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));

			}

			else{

				$arrValue = explode(',',$value);

				$customer = Mage::getModel('customer/customer')

                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())

                ->loadByEmail($arrValue[0]);

	            if ($customerId = $customer->getId()) {

	                $session = Mage::getSingleton('customer/session');

	                if ($session->isLoggedIn() && $customerId != $session->getCustomerId())

	                    $session->logout();



	                try {

	                    $session->setCustomerAsLoggedIn($customer);

	                } catch (Exception $ex) {

	                    Mage::getSingleton('core/session')->addError($this->__('Your account isn\'t confirmed'));

	                    $this->_redirect('/');

	                }

	            }            

				if($arrValue[1] == 'order')

					$this->getResponse()->setRedirect(Mage::getUrl("sales/order/view", array('order_id'=>$arrValue[2])));			

				else            	

	            	$this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));	

			}              

        }

        else

        {

            Mage::getSingleton('core/session')->addError($this->__('No resume code cpecified'));

            $this->_redirect('/');

        }

	}

}