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

    /*Post Action */
	
	public function postAction()
    {
        
    // Create transport
    $transport = new Zend_Mail_Transport_Smtp();
     
    $protocol = new Zend_Mail_Protocol_Smtp('mail.tablethrows.com.au');
    $protocol->connect();
    $protocol->helo('mail.tablethrows.com.au');
     
    $transport->setConnection($protocol);
     
    // Loop through messages
     
        $mail = new Zend_Mail();
        $mail->addTo('ashfaq@tablethrows.co.nz', 'Test');
        $mail->setFrom('ashfaq@tablethrows.co.nz', 'Test');
        $mail->setSubject(
            'Email sending problem has resolved'
        );
        $mail->setBodyText('...This is just for tst email...');
     
        // Manually control the connection
        $protocol->rset();
        	
		
		
		
		if( $this->_helper()->extensionEnabled("AW_Helpdeskultimate")){
            /** @var Magpleasure_Ajaxcontacts_Helper_Tools_Helpdesk $helpdesk  */
            $helpdesk = Mage::helper('ajaxcontacts/tools_helpdesk');
            $helpdesk->contactForm();
        }

        if ($this->_helper()->extensionEnabled("AW_Helpdeskultimate") && !Mage::getStoreConfig(AW_Helpdeskultimate_Helper_Config::XML_PATH_CONTACTFORM_DISABLE_STANDART_EMAIL)){
            
			parent::postAction();
			
        } elseif (!$this->_helper()->extensionEnabled("AW_Helpdeskultimate")) {
            
			//$this->_postEmailAction();
			
			//exit;
			
			
			
			$email_sent = $mail->send($transport);
		
		if($email_sent){
			echo 'success';
			
			}else{
				echo 'error';
				}
     
     
    	$protocol->quit();
    	$protocol->disconnect();   
		
		
		
		exit;
			
			
			
			//parent::postAction();
			
        }

        $this->_ajaxResponse(array(
            'message' => $this->_getMessageBlockHtml(),
        ));
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

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                
				//var_dump($error);
				//var_dump($postObject);
				
				
				$mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                
				//var_dump($mailTemplate);
				//echo '  =>furhter mail template ';
				$_data = array('email_config'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
							   'email_sender'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
							   'email_receipent'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),								
								);
				
				//print_r($post);
				
				
				
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
				// var_dump($mailTemplate);
				// exit;
				
				
				$template = $translate->setTranslateInline(true);
				
				$name = '{{var data.name}}';
				$email='{{var data.email}}';
				$telephone='{{var data.telephone}}';
				$comment='{{var data.comment}}';
				
				
				// var_dump($mail_template);
				// exit;
				//var_dump($postObject);
				
				$bodydata	=	$mail_template->getData();
				$bodydata 	= 	$bodydata['template_text'];
				$bodydata 	= 	str_replace($name, $post['name'],$bodydata);
				$bodydata 	= 	str_replace($email, $post['email'],$bodydata);
				$bodydata 	= 	str_replace($telephone, $post['telephone'],$bodydata);
				$bodydata 	= 	str_replace($comment, $post['comment'],$bodydata);
				 
				 
				 
				//////////////start mail function////////////////////
				
				$from = $_data['email_receipent']; // sender
				$subject = 'Contact us form response';
				$message = 'Hi '.$post['name'].',';
				$message .='';
				$message .= $bodydata;
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				
				$_mail_sent = mail($post['email'],$subject,$message,"From: $from\n");
				
				
				
				if($_mail_sent){					
				Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
					
					}
				
				///////////////end of mail function/////////////////////////
			 //  var_dump($mailTemplate->getData());
				
			//	exit;
			   
			    if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }


}


