<?php
/**
 * Phone order
 */

class Cateyes_Phoneorder_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Add phone order action
     */
    public function addPhoneOrderAction()
    {
        $_d = array();
        $_d['url'] = Mage::app()->getRequest()->getParam('url');
        $_d['phone'] = Mage::app()->getRequest()->getParam('phone');
        $_redirectUrl = Mage::app()->getRequest()->getParam('url');
		$_d['name'] = Mage::app()->getRequest()->getParam('cusName');
				
        $str =  $_d['url']. " ".$_d['phone']." ".$_redirectUrl." ".$_d['name'];

        if(!empty($_d['url']) AND !empty($_d['phone'])) {
			
         echo   $check = $this->validateForm($_d);
            if($check === true) {    
                $_d['date'] = date("Y-m-d H:i:s");
                Mage::getModel('phoneorder/phoneorder')->saveRequest($_d);

           	//Mage::getSingleton('core/session')->addSuccess($this->__('Thank you, We will be in touch shortly.'));
			$active = Mage::getStoreConfig('phoneorder/configuration/notification');
	        $email = Mage::getStoreConfig('phoneorder/configuration/notificationemail');
				if($active){
			$_data = array();
	        $_data['storename'] = Mage::getStoreConfig('general/store_information/name');
	        $_data['subject'] = $this->__('New Phone Order In Your Store');
	        $_data['body'] = $this->__("<b>Dear Administrator</b>,<br>There are new Phone Order requests in your store.<br>Please login in to admin panel and manage your requests.");

			$templateId = "phoneorder_notification";
			$emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);       
			$emailTemplate->setTemplateSubject($_data['subject']);
			
			$storeId = Mage::app()->getStore()->getStoreId();
//			$sendTo = 'ijaz@vividads.com.au';
			$sendTo = Mage::getStoreConfig('phoneorder/configuration/notificationemail', $storeId);
			$body = $emailTemplate->getProcessedTemplate($_data);       
			$from = Mage::getStoreConfig('trans_email/ident_general/email', $storeId);
			$fromName = $_data['storename'] . ' ' . $this->__("Phone Order")." Name : ".$_d['name']." Phone : ".$_d['phone'];
			$send = $sendTo." ".$fromName;
				$name = $_d['name'];
				$telephone= $_d['phone'];
				$url = $_d['url'];

				$bodydata = $this->__("<b>Dear Administrator</b>,<br>Someone has requested to be contacted over phone.<br>Please login in to admin panel and manage your requests.");
				$bodydata .= "<br> Name: ".$name."<br> Phone: ".$telephone."<br> URL: ".$url;
				//////////////start mail function////////////////////
				$subject = 'Phone request callback response';
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($bodydata, 70);
				// send mail
				
				//print_r($bodydata);
				
				$headers = 'From: Online Phone Inquiry <' . $from . ">\r\n";
				$headers .= 'To: ' . $sendTo.','.$sendTo . "\r\n";    
				$headers .= 'Return-Path: ' . $from . "\r\n";
				$headers .='Content-type: text/html; charset=\"UTF-8\" \r\n';
				$headers .='Reply-To: '.$from.'\r\n';
				
				
				$_mail_sent = mail($post['email'],$subject,$message,$headers);
				/*echo "post...";
				print_r($template);
				print_r($bodydata);
				print_r($_template_data);
				exit;*/
				if($_mail_sent){
					return "Email Send Succesfully.";		
				  }
  
            }
		return true;
			}

		}else {
                Mage::getSingleton('core/session')->addError($this->__($check['meassage']));
		return "Kindly enter proper phone and name";				
        }


    

	}
    /**
     * validate form function
     * @param  array  $data form data
     * @return if there is an error returns message / if all is ok returns true
     */
    private function validateForm($data = array())
    {
        if(empty($data)) {
            return false;
        }

        if(!Zend_Validate::is(trim($data['phone']), 'notEmpty')) {
            $result = array(
                'meassage' => $this->__('Phone field can not be empty and should contain only digits'),
            );
            return $result;
        }

        if(!Zend_Validate::is(trim($data['phone']), 'int')) {
            $result = array(
                'meassage' => $this->__('Phone field can not be empty and should contain only digits'),
            );
            return $result;
        }

        if( strlen($data['phone'])<9 ) {
            $result = array(
                'meassage' => $this->__('Phone number must have a minimum 9 digits'),
            );
            return $result;
        }

        return true;
    }
    
    /**
     * send admin nottification
     */
    private function sendEmailNotification() 
    {
        if(!$this->checkNotificationActive()){
			return "notification not active";
//            return false;
        }

        $_data = array();
        $_data['storename'] = Mage::getStoreConfig('general/store_information/name');
        $_data['subject'] = $this->__('New Phone Order In Your Store');
        $_data['body'] = $this->__("<b>Dear Administrator</b>,<br>There are new Phone Order requests in your store.<br>Please login in to admin panel and manage your requests.");

        $templateId = "phoneorder_notification";
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);       
        $emailTemplate->setTemplateSubject($_data['subject']);
        
        $storeId = Mage::app()->getStore()->getStoreId();
        $sendTo = Mage::getStoreConfig('phoneorder/configuration/notificationemail');
        $body = $emailTemplate->getProcessedTemplate($_data);       
        $from = Mage::getStoreConfig('trans_email/ident_general/email', $storeId);
        $fromName = $_data['storename'] . ' ' . $this->__("Phone Order");
        
        // smtp settings
        $host = Mage::getStoreConfig('phoneorder/smtp/host');
        $authentication = Mage::getStoreConfig('phoneorder/smtp/authentication');
        $username = Mage::getStoreConfig('phoneorder/smtp/username');
        $password = Mage::getStoreConfig('phoneorder/smtp/password');
        $port = Mage::getStoreConfig('phoneorder/smtp/port');
        $ssl = Mage::getStoreConfig('phoneorder/smtp/ssl');

        $config = array(
            'ssl' =>  $ssl,
            'port' => $port,
            'auth' => $authentication,
        );
        
        if($authentication != 'none') {
            $config['username'] = $username;
            $config['password'] = $password;
        }

        try {
            $transport = new Zend_Mail_Transport_Smtp($host, $config);
            $mail = new Zend_Mail();      
            $mail->setBodyHtml($body);

            $mail->setFrom($from, $fromName)
                ->addTo($sendTo, $sendTo)
                ->setSubject($_data['subject']);
            $mail->send($transport);     
        }
        catch(Exception $e) {
            Mage::log($e, null, 'cateyes_phoneorder.log');
        }
    }   

    /** 
     * check configuration - is notification anabled 
     * @return boolean
     */
    private function checkNotificationActive()
    {
        $active = Mage::getStoreConfig('phoneorder/configuration/notification');
        $email = Mage::getStoreConfig('phoneorder/configuration/notificationemail');

        if(!empty($active) && !empty($email)) {
            return true;
        }
        return false;
    }     


}
