<?php

/**
 * Testimonial submit controller
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Hm_Testimonial_SubmitController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle('Testimonial submit');                                               
        }           
             
        $this->renderLayout();
    }
    
    public function postAction()
    {

        $data = $this->getRequest()->getPost();
		//var_dump($data);
        //var_dump($_FILES['media1']['name']); exit;
        if (!empty($data)) {
            $session = Mage::getSingleton('core/session', array('name'=>'frontend'));
            /* @var $session Mage_Core_Model_Session */
        	if(isset($_FILES['media1']['name']) && $_FILES['media1']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('media1');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','avi','flv','swf','mp3','mp4'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media').DS.'testimonial'.DS;
					$result= $uploader->save($path, $_FILES['media1']['name'] );
					
					//$data['media'] = 'testimonial/'. $result['file'];
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
		        $data['media'] = 'testimonial/' . $result['file'];
			}
            
            // Set store view
            if (!Mage::app()->isSingleStoreMode()) {
            	$data['stores'] = array(Mage::app()->getStore()->getId());
            }
            
            $testimonial     = Mage::getModel('testimonial/testimonial')->setData($data);
			
            /* @var $review Mage_Review_Model_Review */
                        
            // set CreatedTime and UpdateTime value:
        	if ($testimonial->getCreatedTime() == NULL || $testimonial->getUpdateTime() == NULL) {
				$testimonial->setCreatedTime(now())
					->setUpdateTime(now());
			} else {
				$testimonial->setUpdateTime(now());
			}
			
            $validate = $testimonial->validate();
            if ($validate === true) {
                try {
                    $testimonial->save();
                    $session->addSuccess($this->__('Your testimonial has been accepted for moderation'));
                   	if(Mage::getStoreConfig('hm_testimonial/email/enable')){
                   		$this->sendmail($data);
                   	}
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
                }
            }
            else {
                try{
                $session->setFormData($data);
                }
                catch(Exception $e){
                    Mage::log($e->getMessage());
                }                  
                if (is_array($validate)) {                   
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }                 
                }
                else {
                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
                }
            }
        }
		//$this->_redirect('*/*/');
        if ($redirectUrl = Mage::getSingleton('core/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
    }
    
	public function sendmail($data)
	{
	    // Transactional Email Template's ID
	    $templateId = Mage::getStoreConfig('hm_testimonial/email/template_email');
	 
	    // Set sender information          
	    $senderName = $data['client_name'];
	    $senderEmail = $data['email'];    
	    $sender = array('name' => $senderName,
	                'email' => $senderEmail);
	     
	    // Set recepient information
	    $recepientEmail = Mage::getStoreConfig('hm_testimonial/email/admin_email');
	    $recepientName = Mage::getStoreConfig('hm_testimonial/email/admin_name');       
	     
	    // Get Store ID    
	    $storeId = Mage::app()->getStore()->getId();
	 
	    // Set variables that can be used in email template
	    
	    $vars = array('client_name' => $data['client_name'],
	              'client_email' => $data['email'],
	    		'company' => $data['company'],
	    		'website' => $data['website'],
	    		'address' => $data['address'],
	    		'testimonial' => $data['description'],
	    		);
	             
	    $translate  = Mage::getSingleton('core/translate');
	 
	    // Send Transactional Email
	    Mage::getModel('core/email_template')
	        ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
	             
	    $translate->setTranslateInline(true);   
	}
    
}
