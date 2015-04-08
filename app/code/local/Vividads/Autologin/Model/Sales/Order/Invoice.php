<?php
class Vividads_Autologin_Model_Sales_Order_Invoice extends Mage_Sales_Model_Order_Invoice
{
public function sendEmail($notifyCustomer = true, $comment = '')
    {
        
		$order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewInvoiceEmail($storeId)) {
            return $this;
        }
		
		
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }
       
	    
		//$hash=md5('Lets have party'.$this->getIncrementId().$this->getCustomerId().$storeId.$this->getEntityId());
        $incrementId = $order->getIncrement_id();
		$customerid =  $order->getCustomer_id();
		$hash =md5('vividexhibits'.$incrementId.$customerid);
	   
	    $storeinfo = Mage::getModel('core/store')->load($order->getStore_id());
		$store_url = $this->getStoreUrl($storeinfo->getWebsite_id());
	   	
		//var_dump('vividexhibits'.$incrementId.$customerid);
	//	var_dump($hash);
		//exit;
	   
	    ////order pdf download
	    $pdf_attachment_link = $store_url.'Quotation/Quote/printorder/order_id/'.$order->getEntity_id();		
		$order_pdf_attachment = '<a style="clear:both;" href="'.$pdf_attachment_link.'" target="_blank" title="Click to download PDF '.$pdf_attachment_link.'"><img style="display: block; clear:both;" border="0" src="http://tablethrows.co.nz/media/pdf_attach_icon.png"  alt="attachement icon"  > PDF-'.$this->getIncrementId().'</a>';
		
        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'invoice'      => $this,
				'comment'      => $comment,
                'billing'      => $order->getBillingAddress(),
				'orderpdflink'	=> $order_pdf_attachment,
                'payment_html' => $paymentBlockHtml,
		        'hash'	=> $hash
            )
        );
        $mailer->send();
        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }

    /**
     * Send email with invoice update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendInvoiceCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }
       // $hash=md5('Lets have party'.$order->getIncrementId().$order->getCustomerId().$storeId.$order->getEntityId());
//$hash=md5('Lets have party'.$this->getIncrementId().$this->getCustomerId().$storeId.$this->getEntityId());
        $incrementId = $order->getIncrement_id();
		$customerid =  $order->getCustomer_id();
		$hash =md5('vividexhibits'.$incrementId.$customerid);
	   
	    $storeinfo = Mage::getModel('core/store')->load($order->getStore_id());
		$store_url = $this->getStoreUrl($storeinfo->getWebsite_id());
	   
	   
	    ////order pdf download
	    $pdf_attachment_link = $store_url.'Quotation/Quote/printorder/order_id/'.$order->getEntity_id();		
		$order_pdf_attachment = '<a style="clear:both;" href="'.$pdf_attachment_link.'" target="_blank" title="Click to download PDF '.$pdf_attachment_link.'"><img style="display: block; clear:both;" border="0" src="http://tablethrows.co.nz/media/pdf_attach_icon.png"  alt="attachement icon"  > PDF-'.$this->getIncrementId().'</a>';
		
        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'invoice'      => $this,
                'comment'      => $comment,
				'orderpdflink'	=> $order_pdf_attachment,
                'billing'      => $order->getBillingAddress(),
	            'hash'	=> $hash

            )
        );
        $mailer->send();

        return $this;
    }
	
		public function getCurrentStoreId($website_id=0){
		
		$store_id = Mage::app()
    					->getWebsite($website_id)
    					 ->getDefaultGroup()
    					 ->getDefaultStoreId()
						;
		return $store_id;
		
		}
	
	///getProductUrlstore wise		
	public function getStoreUrl($website_id=0){
		
		$website = Mage::app()
    					->getWebsite($website_id)
    					// ->getDefaultGroup()
    					// ->getDefaultStoreId()
						;
			 
			return $website->getName();			
		}		
	
}
