<?php
/**
 * Faqs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 
 * @category   FME
 * @package    Faqs
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
class FME_Faqs_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
	{
		$this->loadLayout();		
		$this->renderLayout();
    }
        
    public function viewAction()
   {
	   
		$post = $this->getRequest()->getPost();
		if($post){
			$sterm=$post['faqssearch'];
			$this->_redirect('*/*/search', array('term' => $sterm));
				return;   
		}
		
		$topicId = $this->_request->getParam('id', null);
	
    	if ( is_numeric($topicId) ) {
			
			$faqsTable = Mage::getSingleton('core/resource')->getTableName('faqs');
			$faqsTopicTable = Mage::getSingleton('core/resource')->getTableName('faqs_topics');
			$faqsStoreTable = Mage::getSingleton('core/resource')->getTableName('faqs_store');
		
			$sqry = "select f.*,t.title as cat from ".$faqsTable." f, ".$faqsTopicTable." t where f.topic_id='$topicId' and f.status=1 and t.topic_id='$topicId'"; 
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$select = $connection->query($sqry);
			$collection = $select->fetchAll();
			if(count($collection) != 0){
				Mage::register('faqs', $collection);
			} else {
				Mage::register('faqs', NULL); 
			}
			
    	} else {
			
			Mage::register('faqs', NULL); 
		}
		
		$this->loadLayout();   
		$this->renderLayout();	
    }
    
    public function searchAction()
    {
    	
		$faqsTable = Mage::getSingleton('core/resource')->getTableName('faqs');
		$faqsTopicTable = Mage::getSingleton('core/resource')->getTableName('faqs_topics');
		$faqsStoreTable = Mage::getSingleton('core/resource')->getTableName('faqs_store');
		
		$sterm = $this->getRequest()->getParam('term');
		$post = $this->getRequest()->getPost();
		if($post){  
			$sterm=$post['faqssearch'];    
		}
		
		if(isset($sterm)){
			$sqry = "select * from ".$faqsTable." f,".$faqsStoreTable." fs where (f.title like '%$sterm%' or f.faq_answar like '%$sterm%') and (status=1)
			and f.topic_id = fs.topic_id
			and (fs.store_id =".Mage::app()->getStore()->getId()." OR fs.store_id=0)";
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$select = $connection->query($sqry);
			$sfaqs = $select->fetchAll();
			if(count($sfaqs) != 0){
				Mage::register('faqs', $sfaqs);
			} 
		}
		$this->loadLayout();   
		$this->renderLayout();

    }

    
	public function addAction() {
		$post = $this->getRequest()->getPost();
		if ( $post ) {
			
			$store = Mage::app()->getStore();
            $storeId = $store->getId();
			
			$translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
           
		    $postObject = new Varien_Object();
            $postObject->setData($post);
			
			
			
			try {
				
				/***************************************************************
				these variables are set default value as false
				further they will be used as to check which required fields
				are not validating
				***************************************************************/
				$nameerror = false;
				$emailerror = false;
				$questionerror = false;
				$captchaerror = false;
				
				/***************************************************************
				zend validator validates the required fields
				***************************************************************/
				if (!Zend_Validate::is(trim($post['contact_name']) , 'NotEmpty')) { 
					$nameerror = true;
				}	
				if (!Zend_Validate::is(trim($post['contact_email']), 'EmailAddress')) {
					$emailerror = true;
				}
				if (!Zend_Validate::is(trim($post['title']) , 'NotEmpty')) {
					$questionerror = true;
				}
				if (!Zend_Validate::is(trim($post['security_code']) , 'NotEmpty')) { 
					$captchaerror = true;
				}
				/***************************************************************
				if error returned by zend validator then add an error message
				***************************************************************/
				if ($nameerror) {
					$translate->setTranslateInline(true);
					echo '<ul class="messages"><li class="error-msg"><ul><li>'.Mage::helper('faqs')->__('Please Enter your Name.').'</li></ul></li><ul>';
					return;
				}
				if ($emailerror) {
					$translate->setTranslateInline(true);
					echo '<ul class="messages"><li class="error-msg"><ul><li>'.Mage::helper('faqs')->__('Please Enter a valid Email Address.').'</li></ul></li><ul>';
					return;
				}
				if ($questionerror) {
					$translate->setTranslateInline(true);
					echo '<ul class="messages"><li class="error-msg"><ul><li>'.Mage::helper('faqs')->__('Please ask some thing.').'</li></ul></li><ul>';
					return;
				}
				if ($captchaerror) {
					$translate->setTranslateInline(true);
					echo '<ul class="messages"><li class="error-msg"><ul><li>'.Mage::helper('faqs')->__('Please Enter verification text.').'</li></ul></li><ul>';
					return;
				}	
				
				if (!$captchaerror && $post['security_code']!= $post['captacha_code']) {
					$translate->setTranslateInline(true);
					echo '<ul class="messages"><li class="error-msg"><ul><li>'.Mage::helper('faqs')->__('Sorry The Security Code You Entered Was Incorrect.').'</li></ul></li><ul>';
					return;
				}
				
			} catch (Exception $e) {
                    Mage::logException($e);
                    Mage::log($e);
            }
			
			
			$model = Mage::getModel('faqs/faqs');		
			$model->setData($post)
				->setId($this->getRequest()->getParam('id'));
			
			if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
				$model->setCreatedTime(now())
					->setUpdateTime(now());
			} else {
				$model->setUpdateTime(now());
			}
			
			$model->save();	
			
			/* Now send email to admin about new question */
			try {
			
			$question = Mage::getModel('faqs/faqs')->setData($post);
			
			$mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate
				->setDesignConfig(array('area' => 'frontend', 'store' => $store))
				->setReplyTo($post['contact_email'])
				->sendTransactional(
					Mage::getStoreConfig(FME_Faqs_Model_Source_Config_Path::EMAIL_ADMIN_TEMPLATE ),
					Mage::getStoreConfig(FME_Faqs_Model_Source_Config_Path::EMAIL_SENDER),
					Mage::getStoreConfig(FME_Faqs_Model_Source_Config_Path::EMAIL_RECIPIENT),
					null,
					array('data' => $question),
					$storeId
				);

			if (!$mailTemplate->getSentSuccess()) { //throw new Exception(); 
				Mage::log($this->__('An error occured while sending Product Questions email from \'%s\' to admin \'%s\' using template \'%s\', asked by \'%s\', the question is \'%s\''));
				echo "Success";
				//echo '<ul class="messages"><li class="error-msg"><ul><li>An error occured while sending Product Questions email.</li></ul></li><ul>';
				return;
			}
			} catch (Exception $e) {
                    Mage::logException($e);
                    Mage::log($e);
					echo "Success";
					//echo '<ul class="messages"><li class="error-msg"><ul><li>An error occured while sending Product Questions email.</li></ul></li><ul>';
					return;
            }
			
			echo "Success";
		} else {
			echo '<ul class="messages"><li class="error-msg"><ul><li>'.Mage::helper('faqs')->__('There is some issue try again later.').'</li></ul></li><ul>';
		}
	}
	
	public function refreshAction() {
		
		$code=Mage::helper('faqs')->getNewrandCode(6);
		$img = Mage::helper('faqs')->getSecureImageUrl();
		
		$htmlCaptacha = '	<input name="captacha_code" type="hidden" id="captacha_code" value="'.$code.'" />
								<label for="image"><img src="'.$img.'CaptchaSecurityImages.php?width=180&height=40&code='.$code.'" /></label><br/>
								<div class="input-box">
								<label for="security_code" class="required"><em>*</em><b>'.Mage::helper('faqs')->__('Security Code Message:').'</b></label><br />
								<input id="security_code" name="security_code" type="text" /> <br />
								'.Mage::helper('faqs')->__('If you have difficulty in reading the image above then refresh your browser a few times until you see an image that is clear enough to copy.').'
							</div>';
		echo $htmlCaptacha;
	}
	
    public function topicsAction()
    {
		$this->loadLayout();   
		$this->renderLayout();
    }
}
