<?php
/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Ajaxcontacts
 * @version    1.0
 * @copyright  Copyright (c) 2011 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

require_once "Mage/Contacts/controllers/IndexController.php";
class Magpleasure_Ajaxcontacts_IndexController extends Mage_Contacts_IndexController
{

    /**
     * Response for Ajax Request
     *
     * @param array $result
     */
    
	public function indexAction(){
		
		
		$this->postAction();
		
		}
	
	protected function _ajaxResponse($result = array())
    {
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    /**
     * Helper
     *
     * @return Magpleasure_Ajaxcontacts_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('ajaxcontacts');
    }

    /**
     * Set redirect into response
     *
     * @param   string $path
     * @param   array $arguments
     * @return Magpleasure_Ajaxcontacts_IndexController
     */
    protected function _redirect($path, $arguments=array())
    {
        return $this;
    }

    public function formAction()
    {
        $window = $this->getLayout()->createBlock('ajaxcontacts/window');
        if ($window){
            $this->getResponse()->setBody($window->toHtml());
        }
    }

    protected function _getMessageBlockHtml()
    {
        return $this->getLayout()->getMessagesBlock()->addMessages(Mage::getSingleton('customer/session')->getMessages(true))->toHtml();
    }

public function myPostAction()
    {
		
	 // echo 'message called';		
		$post = $this->getRequest()->getPost();        
	
		//Zend_debug::dump($post);
	//	exit;
		
	if ( $post ) {
           
		    $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
             $translate->setTranslateInline(false);		  	   
		    try {
                $postObject = new Varien_Object();
                $postObject->setData($post);				
				
                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                
				
				$mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
				//var_dump($mailTemplate);
				//echo '  =>furhter mail template ';
				$_data = array('email_config'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
							   'email_sender'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
							   'email_receipent'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),								
								);				
				
				/*
				$mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                       // Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
					   'sales@tablethrows.com.au',
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );
				 */	
					
				// var_dump($mailTemplate);
				// exit;
				
				
				 $mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
				 				 ->load($_data['email_config'])
					 			 ->setReplyTo($post['email'])
					 			 ;
				$collection =  Mage::getResourceSingleton('core/email_template_collection');
				
				$_current_template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
				
				foreach($collection as $value)
					{
						//echo $value->getTemplateId().'<br/>';						
						if($value->getOrig_template_code()== $_current_template){
							//echo $value->getTemplateCode().'<br/>';
							 // print_r($value->getData());
							 $_template_data = $value->getData();
						}
					}
				
				$template = $translate->setTranslateInline(true);
				
				$name = '{{var data.name}}';
				$email='{{var data.email}}';
				$telephone='{{var data.telephone}}';
				$comment='{{var data.comment}}';				
				
				$bodydata = $_template_data['template_text'];		
				
				$bodydata 	= 	str_replace($name, $post['name'],$bodydata);
				$bodydata 	= 	str_replace($email, $post['email'],$bodydata);
				$bodydata 	= 	str_replace($telephone, $post['telephone'],$bodydata);
				$bodydata 	= 	str_replace($comment, $post['comment'],$bodydata);
				
				
				  
				 
				 
				//////////////start mail function////////////////////
				
				$from = $_data['email_receipent']; // sender
				$subject = 'Contact us form response';
				
				$message .= $bodydata;
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				
				//print_r($bodydata);
				
				$headers = 'From: Online Sales Team<' . $post['email'] . ">\r\n";
				$headers .= 'To: ' . $post['email'].','.$from . "\r\n";    
				$headers .= 'Return-Path: ' . $from . "\r\n";
				$headers.='Reply-To: '.$from.'\r\n';
				
				
				$_mail_sent = mail($post['email'],$subject,$message,$headers);
				
				if($_mail_sent){
										
					  Mage::getSingleton('customer/session')->addSuccess(
					  Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
					// $this->_redirect('*/*/');
					//return;
					
					
					
					}
				
				///////////////end of mail function/////////////////////////
			
			   
			   
			   /*
			    if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }
				*/

                $translate->setTranslateInline(true);
				
				

             //    Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            //    $this->_redirect('*/*/');
					
				header('location:http://tablethrows.com.au/');
					
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

               // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                //$this->_redirect('*/*/');
                return;
            }
			
			
			
			$this->_redirect('*/*/');

        }  
  	}
	
public function expoPostAction()
    {
		
	 // echo 'message called';		
		$post = $this->getRequest()->getPost();        
	
		//Zend_debug::dump($post);
	//	exit;
		
	if ( $post ) {
           
		    $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
             $translate->setTranslateInline(false);		  	   
		    try {
                $postObject = new Varien_Object();
                $postObject->setData($post);				
				
                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                
				
				$mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
				//var_dump($mailTemplate);
				//echo '  =>furhter mail template ';
				$_data = array('email_config'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
							   'email_sender'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
							   'email_receipent'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),								
								);				
				
				/*
				$mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                       // Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
					   'sales@tablethrows.com.au',
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );
				 */	
					
				// var_dump($mailTemplate);
				// exit;
				
				
				 $mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
				 				 ->load($_data['email_config'])
					 			 ->setReplyTo($post['email'])
					 			 ;
				$collection =  Mage::getResourceSingleton('core/email_template_collection');
				
				$_current_template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
				
				foreach($collection as $value)
					{
						//echo $value->getTemplateId().'<br/>';						
						if($value->getOrig_template_code()== $_current_template){
							//echo $value->getTemplateCode().'<br/>';
							 // print_r($value->getData());
							 $_template_data = $value->getData();
						}
					}
				
				$template = $translate->setTranslateInline(true);
				
				$name = '{{var data.name}}';
				$email='{{var data.email}}';
				$telephone='{{var data.telephone}}';
				$comment='{{var data.comment}}';				
				
				$bodydata = $_template_data['template_text'];		
				
				$bodydata 	= 	str_replace($name, $post['name'],$bodydata);
				$bodydata 	= 	str_replace($email, $post['email'],$bodydata);
				$bodydata 	= 	str_replace($telephone, $post['telephone'],$bodydata);
				$bodydata 	= 	str_replace($comment, $post['comment'],$bodydata);
				
				
				  
				 
				 
				//////////////start mail function////////////////////
				
				$from = $_data['email_receipent']; // sender
				$subject = 'Contact us form response';
				
				$message .= $bodydata;
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				
				//print_r($bodydata);
				
				$headers = 'From: Team 13Expo<' . $post['email'] . ">\r\n";
				$headers .= 'To: ' . $post['email'].','.$from . "\r\n";    
				$headers .= 'Return-Path: ' . $from . "\r\n";
				$headers.='Reply-To: '.$from.'\r\n';
				
				
				$_mail_sent = mail($post['email'],$subject,$message,$headers);
				
				if($_mail_sent){					
					  Mage::getSingleton('customer/session')->addSuccess(
					  Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
					// $this->_redirect('*/*/');
					//return;
					
					
					
					}
				
				///////////////end of mail function/////////////////////////
			
			   
			   
			   /*
			    if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }
				*/

                $translate->setTranslateInline(true);
				
				

             //    Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            //    $this->_redirect('*/*/');
					
				header('location:http://13expo.com.au/');
					
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

               // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                //$this->_redirect('*/*/');
                return;
            }
			
			
			
			$this->_redirect('*/*/');

        }  
  	}	
	
	
public function outdoorPostAction()
    {
		
	 // echo 'message called';		
		$post = $this->getRequest()->getPost();        
//	echo '<pre>';
	//print_r($post);
		 
	//	exit;
		
	if ( $post ) {
           
		    $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
             $translate->setTranslateInline(false);		  	   
		    try {
                $postObject = new Varien_Object();
                $postObject->setData($post);				
				
                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                 
                if ($error) {
                    throw new Exception();
                }
                
				
				$mailTemplate = Mage::getModel('core/email_template');
                
				$_data = array('email_config'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
							   'email_sender'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
							   'email_receipent'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),								
								);				
				 
				
				
				 $mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
				 				 ->load($_data['email_config'])
					 			 ->setReplyTo($post['email'])
					 			 ;
				$collection =  Mage::getResourceSingleton('core/email_template_collection');
				
				$_current_template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
				
				foreach($collection as $value)
					{
						 $mycode = intval($value->getTemplateId());	
						$ct = intval($_current_template);					
						if($mycode == $ct){
							//echo "ijaz ali";
						//	echo $value->getTemplateCode().'<br/>';
						//	 print_r($value->getData());
							 $_template_data = $value->getData();
						}
					}
				
				$template = $translate->setTranslateInline(true);
				
				$name = '{{var data.name}}';
				$email='{{var data.email}}';
				$telephone='{{var data.telephone}}';
				$comment='{{var data.comment}}';				
				
				$bodydata = $_template_data['template_text'];		
				
				$bodydata 	= 	str_replace($name, $post['name'],$bodydata);
				$bodydata 	= 	str_replace($email, $post['email'],$bodydata);
				$bodydata 	= 	str_replace($telephone, $post['telephone'],$bodydata);
				$bodydata 	= 	str_replace($comment, $post['comment'],$bodydata);
				
				
				  
				 
				 
				//////////////start mail function////////////////////
				
				$from = $_data['email_receipent']; // sender
				$subject = 'Contact us form response';
				
				$message .= $bodydata;
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				
				//print_r($bodydata);
				
				$headers = 'From: Online Sales Team <' . $post['email'] . ">\r\n";
				$headers .= 'To: ' . $post['email'].','.$from . "\r\n";    
				$headers .= 'Return-Path: ' . $from . "\r\n";
				$headers.='Reply-To: '.$from.'\r\n';
				
				
				$_mail_sent = mail($post['email'],$subject,$message,$headers);
				/*print_r($bodydata);
				print_r($_template_data);
				exit;*/
				if($_mail_sent){					
					  Mage::getSingleton('customer/session')->addSuccess(
					  Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
					// $this->_redirect('*/*/');
					//return;
					
					
					
					}
				
				///////////////end of mail function/////////////////////////
			
			   
			   
			   /*
			    if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }
				*/

                $translate->setTranslateInline(true);
				
				

             //    Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            //    $this->_redirect('*/*/');
					
				header('location:http://outdoorbannershop.com.au/');
					
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

               // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                //$this->_redirect('*/*/');
                return;
            }
			
			
			
			$this->_redirect('*/*/');

        }  
  	}		
	
	public function NzPostAction()
    {
		
	 // echo 'message called';		
		$post = $this->getRequest()->getPost();
		$u = $post['hideit']; 
		$isAjax = $post['isAjax']; 
		
	/*echo '<pre>';
	print_r($post);
		 
		//exit;*/
		
	if ($post) {
           
		    $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
             $translate->setTranslateInline(false);		  	   
		    try {
                $postObject = new Varien_Object();
                $postObject->setData($post);				
				
                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                 
                if ($error) {
                    throw new Exception();
                }
                
				
				$mailTemplate = Mage::getModel('core/email_template');
                
				$_data = array('email_config'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
							   'email_sender'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
							   'email_receipent'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),								
								);				
				 
				
				
				 $mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
				 				 ->load($_data['email_config'])
					 			 ->setReplyTo($post['email'])
					 			 ;
				$collection =  Mage::getResourceSingleton('core/email_template_collection');
				
				$_current_template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
				
				foreach($collection as $value)
					{
							if($value->getOrig_template_code()== $_current_template){
							//echo $value->getTemplateCode().'<br/>';
							 // print_r($value->getData());
							 $_template_data = $value->getData();
						}
					}
					
				foreach($collection as $value)
				{	
				$mycode = intval($value->getTemplateId());	
						$ct = intval($_current_template);					
						if($mycode == $ct){
							//echo "ijaz ali";
						//	echo $value->getTemplateCode().'<br/>';
						//	 print_r($value->getData());
							 $_template_data = $value->getData();
						}
			   }
				$template = $translate->setTranslateInline(true);
				
				$name = '{{var data.name}}';
				$email='{{var data.email}}';
				$telephone='{{var data.telephone}}';
				$comment='{{var data.comment}}';				
				$bodydata = $_template_data['template_text'];		
				$bodydata 	= 	str_replace($name, $post['name'],$bodydata);
				$bodydata 	= 	str_replace($email, $post['email'],$bodydata);
				$bodydata 	= 	str_replace($telephone, $post['telephone'],$bodydata);
				$bodydata 	= 	str_replace($comment, $post['comment'],$bodydata);
				//////////////start mail function////////////////////
				
				$from = $_data['email_receipent']; // sender
				$subject = 'Contact us form response';
				
				$message .= $bodydata;
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				
				//print_r($bodydata);
				
				$headers = 'From: Online Sales Team <' . $post['email'] . ">\r\n";
				$headers .= 'To: ' . $post['email'].','.$from . "\r\n";    
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
					if(!($isAjax)){ 			
					  Mage::getSingleton('customer/session')->addSuccess(
					  Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
  				header("Location:".$u);
                return;

					}else{
				$message = 'Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.';
                return $message;				

						}
					// $this->_redirect('*/*/');
					//return;
					
					
					
					}
				
				///////////////end of mail function/////////////////////////
			
			   
			   
			   /*
			    if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }
				*/

                $translate->setTranslateInline(true);
				
				

             //    Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            //    $this->_redirect('*/*/');
/*				if($isAjax){ 
				$message = 'Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.';
                return $message;				
				} else{	
				header("Location:".$u);
                return;
				}*/

            } catch (Exception $e) {
                $translate->setTranslateInline(true);

               // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                //$this->_redirect('*/*/');
                return;
            }
			
			
			
			$this->_redirect('*/*/');

        }  
  	}		
	
	
    
public function calculatorPostAction()
    {
		
	 // echo 'message called';		
		$post = $this->getRequest()->getPost();        
	   /* echo '<pre>';
		print_r($post);*/
		
				
	if ( $post ) {
           
		    $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
             $translate->setTranslateInline(false);		  	   
		    try {
                $postObject = new Varien_Object();
                $postObject->setData($post);				
				
                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

               /* if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }*/

                if ($error) {
                    throw new Exception();
                }
                
				
				$mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
				
				//echo '  =>furhter mail template ';
				$_data = array('email_config'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
							   'email_sender'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
							   'email_receipent'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),								
								);				
	
				 $mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
				 				 ->load($_data['email_config'])
					 			 ->setReplyTo($post['email'])
					 			 ;
				$collection =  Mage::getResourceSingleton('core/email_template_collection');
				
				$_current_template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
				
				foreach($collection as $value)
					{
						 $mycode = intval($value->getTemplateId());	
						$ct = intval($_current_template);					
						if($mycode == $ct){
							//echo "ijaz ali";
						//	echo $value->getTemplateCode().'<br/>';
						//	 print_r($value->getData());
							 $_template_data = $value->getData();
						}
					}
					
					 
				
				$template = $translate->setTranslateInline(true);
				
				
				
				
				
				$name = '{{var data.name}}';
				$email='{{var data.email}}';
				$telephone='{{var data.telephone}}';
				$comment='{{var data.comment}}';				
				
				$bodydata = $_template_data['template_text'];		
				
				$bodydata 	= 	str_replace($name, $post['name'],$bodydata);
				$bodydata 	= 	str_replace($email, $post['email'],$bodydata);
				$bodydata 	= 	str_replace($telephone, $post['telephone'],$bodydata);
				
				if($post['product']=='1538:610'){
					 
					$product = $post['comment']."\nYou Enquire The Product: Custom Mesh Banner \n Width: ".$post['size_w']." m\n Height: ".$post['size_h'].
					" m \n Quantity: ".$post['qty']."\n Price: $".$post['price'];
				$bodydata 	= 	str_replace($comment, $product,$bodydata);	
					
				}else { 
				
				$bodydata 	= 	str_replace($comment, $post['comment'],$bodydata);
				}
				
				/*print_r($_template_data);
				print_r($_current_template);
				var_dump($mailTemplate); */
				/*print_r($bodydata);
				exit;
				 */
				 
				 
				//////////////start mail function////////////////////
				
				$from = $_data['email_receipent']; // sender
				$subject = 'Contact us form response';
				
				$message .= $bodydata;
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				
				/*print_r($bodydata);
				exit;*/
				$headers = 'From: Online Sales Team<' . $post['email'] . ">\r\n";
				$headers .= 'To: ' . $post['email'].','.$from . "\r\n";    
				$headers .= 'Return-Path: ' . $from . "\r\n";
				$headers.='Reply-To: '.$from.'\r\n';
				
				
				$_mail_sent = mail($post['email'],$subject,$message,$headers);
				
				if($_mail_sent){					
					  Mage::getSingleton('customer/session')->addSuccess(
					  Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
					// $this->_redirect('*/*/');
					//return;
					
					
					
					}
				
				///////////////end of mail function/////////////////////////
			
			   
			   
			   /*
			    if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }
				*/

                $translate->setTranslateInline(true);
				
				

             //    Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            //    $this->_redirect('*/*/');
					
				header('location:http://outdoorbannershop.com.au/');
					
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

               // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                //$this->_redirect('*/*/');
                return;
            }
			
			
			
			$this->_redirect('*/*/');

        }  
  	}	
	
	/*Post Action */
	
	public function postAction()
    {
        
		
		if( $this->_helper()->extensionEnabled("AW_Helpdeskultimate")){
            /** @var Magpleasure_Ajaxcontacts_Helper_Tools_Helpdesk $helpdesk  */
            $helpdesk = Mage::helper('ajaxcontacts/tools_helpdesk');
            $helpdesk->contactForm();
			
        }

        if ($this->_helper()->extensionEnabled("AW_Helpdeskultimate") && !Mage::getStoreConfig(AW_Helpdeskultimate_Helper_Config::XML_PATH_CONTACTFORM_DISABLE_STANDART_EMAIL)){
            
			parent::postAction();
			
        } elseif (!$this->_helper()->extensionEnabled("AW_Helpdeskultimate")) {
            
			$this->_postEmailAction();
			
				
			
			//parent::postAction();
			
        }

        $this->_ajaxResponse(array(
            'message' => $this->_getMessageBlockHtml(),
        ));
    }
	
	public function _postMyEmailAction(){
		$post = $this->getRequest()->getPost(); 
		 
	
	if ( $post ) {
           
		    $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
             $translate->setTranslateInline(false);		  	   
		    try {
                $postObject = new Varien_Object();
                $postObject->setData($post);				
				
                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                
				
				$mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
				//var_dump($mailTemplate);
				//echo '  =>furhter mail template ';
				$_data = array('email_config'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
							   'email_sender'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
							   'email_receipent'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),								
								);				
				
				/*
				$mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                       // Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
					   'sales@tablethrows.com.au',
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );
				 */	
					
				// var_dump($mailTemplate);
				// exit;
				
				
				 $mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
				 				 ->load($_data['email_config'])
					 			 ->setReplyTo($post['email'])
					 			 ;
				$collection =  Mage::getResourceSingleton('core/email_template_collection');
				
				$_current_template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
				
				foreach($collection as $value)
					{
						//echo $value->getTemplateId().'<br/>';						
						if($value->getOrig_template_code()== $_current_template){
							//echo $value->getTemplateCode().'<br/>';
							 // print_r($value->getData());
							 $_template_data = $value->getData();
						}
					}
				
				$template = $translate->setTranslateInline(true);
				
				$name = '{{var data.name}}';
				$email='{{var data.email}}';
				$telephone='{{var data.telephone}}';
				$comment='{{var data.comment}}';				
				
				$bodydata = $_template_data['template_text'];		
				
				$bodydata 	= 	str_replace($name, $post['name'],$bodydata);
				$bodydata 	= 	str_replace($email, $post['email'],$bodydata);
				$bodydata 	= 	str_replace($telephone, $post['telephone'],$bodydata);
				$bodydata 	= 	str_replace($comment, $post['comment'],$bodydata);
				
				
				  
				 
				 
				//////////////start mail function////////////////////
				
				$from = $_data['email_receipent']; // sender
				$subject = 'Contact us form response';
				
				$message .= $bodydata;
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				
				//print_r($bodydata);
				
				$headers = 'From: Online Sales Team<' . $post['email'] . ">\r\n";
				$headers .= 'To: ' . $post['email'].','.$from . "\r\n";    
				$headers .= 'Return-Path: ' . $from . "\r\n";
				$headers.='Reply-To: '.$from.'\r\n';
				
				
				$_mail_sent = mail($post['email'],$subject,$message,$headers);
				
				if($_mail_sent){					
					// Mage::getSingleton('customer/session')->addSuccess(
					// Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
					 $this->_redirect('*/*/');
					 return;
					
					}
				
				///////////////end of mail function/////////////////////////
			
			   
			    if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

              //  Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

               // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                //$this->_redirect('*/*/');
                return;
            }

        }
		
		
		
		
		
		
		
		}
	
	
	//* postEmail*/
	public function _postEmailAction()
    {
       // echo 'message called';		
		$post = $this->getRequest()->getPost();        
	
	if ( $post ) {
           
		    $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
             $translate->setTranslateInline(false);		  	   
		    try {
                $postObject = new Varien_Object();
                $postObject->setData($post);				
				
                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

               /* if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }*/

                if ($error) {
                    throw new Exception();
                }
                
				
				$mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
				//var_dump($mailTemplate);
				//echo '  =>furhter mail template ';
				$_data = array('email_config'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
							   'email_sender'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
							   'email_receipent'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),								
								);				
				
				/*
				$mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                       // Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
					   'sales@tablethrows.com.au',
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );
				 */	
					
				// var_dump($mailTemplate);
				// exit;
				
				
				 $mail_template = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
				 				 ->load($_data['email_config'])
					 			 ->setReplyTo($post['email'])
					 			 ;
				$collection =  Mage::getResourceSingleton('core/email_template_collection');
				
				$_current_template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
				
				foreach($collection as $value)
					{
						 $mycode = intval($value->getTemplateId());	
						$ct = intval($_current_template);					
						if($mycode == $ct){
							//echo "ijaz ali";
						//	echo $value->getTemplateCode().'<br/>';
						//	 print_r($value->getData());
							 $_template_data = $value->getData();
						}
					}
				
				$template = $translate->setTranslateInline(true);
				
				$name = '{{var data.name}}';
				$email='{{var data.email}}';
				$telephone='{{var data.telephone}}';
				$comment='{{var data.comment}}';				
				
				$bodydata = $_template_data['template_text'];		
				
				$bodydata 	= 	str_replace($name, $post['name'],$bodydata);
				$bodydata 	= 	str_replace($email, $post['email'],$bodydata);
				$bodydata 	= 	str_replace($telephone, $post['telephone'],$bodydata);
				$bodydata 	= 	str_replace($comment, $post['comment'],$bodydata);
				
				
				  
				 
				 
				//////////////start mail function////////////////////
				
				$from = $_data['email_receipent']; // sender
				$subject = 'Contact us form response';
				
				$message .= $bodydata;
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				
				//print_r($bodydata);
				
				$headers = 'From: Online Sales Team<' . $post['email'] . ">\r\n";
				$headers .= 'To: ' . $post['email'].','.$from . "\r\n";    
				$headers .= 'Return-Path: ' . $from . "\r\n";
				$headers.='Reply-To: '.$from.'\r\n';
				
				
				$_mail_sent = mail($post['email'],$subject,$message,$headers);
				
				if($_mail_sent){					
					// Mage::getSingleton('customer/session')->addSuccess(
					// Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
					 $this->_redirect('*/*/');
					 return;
					
					}
				
				///////////////end of mail function/////////////////////////
			
			   
			    if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

              //  Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

               // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                //$this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }


}