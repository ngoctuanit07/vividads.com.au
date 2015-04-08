<?php

class FMA_Reviewsplus_Model_Feedback
{
	public function getFeedbackEmail()
	
	{
		try 
		{
			$logdir= 	Mage::getModuleDir('', 'FMA_Reviewsplus'). DS . 'log'.DS;
		 	if(is_writable($logdir))
		 	{
		 	
			 	$files=$this->getDirectoryList(Mage::getModuleDir('', 'FMA_Reviewsplus'). DS . 'log'.DS);
			 	if (count($files) > 0) 
			 	{
			 		
			 	 	$newline="\n";
		         	sort($files);
		            Mage::log('if count');
		            foreach ($files as $file)
		            {
		            	$orderXMLFile=Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. $file;
		            	//load data from xml file
		            	$orderData=simplexml_load_file(Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. $file);
		            	//$order = $orderData->order_id;
		                // load order
		                $order = Mage::getModel('sales/order')->load($orderData->order_id);
		                $orderStatus=$orderData->status;
		                if (!$this->validateOrder($orderData,$orderStatus,$orderXMLFile))
		                 {continue; } // keep on looping 
		             	$orderDatTimeStamp=$orderData->timestamp;
		             	$storeID=trim($orderData->Store_id);
					// determine time (in hours) elapsed since order
				        $elaspedHoursSinceOrder= floor((time()-(int)$orderDatTimeStamp)/3600);
				        // waiting period in days from config
				        $emailNotificationWaitingPeriod=(int)Mage::getStoreConfig('reviewsplus_sec/feedback_config/time_delay');
				        // change from days to hours
				        $emailNotificationWaitingPeriod=$emailNotificationWaitingPeriod*24;
		        		if ($elaspedHoursSinceOrder >= $emailNotificationWaitingPeriod)
		            		{
		                      
		          			if (Mage::getStoreConfig('reviewsplus_sec/feedback_config/test_mode_enabled')) 
		          			{
								$toName=Mage::getStoreConfig('trans_email/ident_general/name');
								$to = Mage::getStoreConfig('trans_email/ident_general/email');
							} else {
								$to= $orderData->customer_Email;
								$toName= $orderData->customer_Name;
							}
                                        $from = Mage::getStoreConfig('trans_email/ident_sales/email',$storeID);
							$fromName = Mage::getStoreConfig('trans_email/ident_sales/name',$storeID);
							$subject=Mage::getStoreConfig('reviewsplus_sec/feedback_config/email_subject',$storeID);
							$intro =  Mage::getStoreConfig('reviewsplus_sec/feedback_config/email_body',$storeID);
							$products = file_get_contents (Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. trim($orderData->template));
							$footerText=Mage::getStoreConfig('reviewsplus_sec/feedback_config/email_footer_link',$storeID);
                                                        $footerTextHtml='<strong><a href="'. Mage::getUrl().'">'. $footerText .'</a></strong>';
                                                        $footer_email = Mage::getStoreConfig('reviewsplus_sec/feedback_config/email_footer',$storeID);
                                                        //$body=$style.$header.$greeting.$intro.$orderInfo.$products.$footer;
                                                        $emailTemplate  = Mage::getModel('core/email_template')
                                                        ->loadDefault('customer_feedback_email');
                                                        //Create an array of variables to assign to template
							$emailTemplateVariables = array();
							$emailTemplateVariables['toName'] = $toName;
							$emailTemplateVariables['order_id'] = trim($orderData->order_id);
							$emailTemplateVariables['orderdate'] = date("j.n.Y h:i:s A",(int)$orderData->timestamp);
							$emailTemplateVariables['intro'] = $intro;
							$emailTemplateVariables['template'] = $products;
							$emailTemplateVariables['footer_email'] = $footer_email;
							$emailTemplateVariables['footerTextHtml'] = $footerTextHtml;
							$emailTemplate->setSenderName($fromName);
							$emailTemplate->setSenderEmail($from);
							$emailTemplate->setTemplateSubject($subject);
							$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
							$emailTemplate->send($to,$toName, $emailTemplateVariables);
							if (!$emailTemplate) 
                                                        { // mail send error
                                                            // clean up
                                                           Mage::log('An error occurred trying to send customer feedback email, command : '.$orderData->order_id.' erasing file: '.$orderDatFile.' and associated .html file');
                                                           unlink($orderXMLFile);
                                                           unlink(Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. trim($orderData->template));
                                                           throw new Exception('An error occurred trying to send customer feedback email, command : '.$orderData->order_id.' erasing file: '.$orderDatFile.' and associated .html file'); 
                                                        }
                                                        if (Mage::getStoreConfig('reviewsplus_sec/feedback_config/test_mode_enabled')) 
                                                        {
                                                        // dont update order if in test mode								
                                                        } else {
                                                        // add order note
                                                        $order->addStatusToHistory($order->getStatus(), '<b><i>ReviewsplusCustomerFeedback </i></b><br/>Customer feedback email sent to <strong>'. $to. '</strong>.', true);
                                                        $order->save();
                                                        }
                                                unlink($orderXMLFile);
                                                unlink(Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. trim($orderData->template));
					} // elapsed time check
					}// file array loop
				  } // file array contains files
				} else { // folder permissions check failed
		            throw new Exception('The GetCustomerFeedback module cache folder -'. $cacheFolder. ' is not writable, please check the folder permissions.');
	        }
	    	}
	         catch (Exception $e) {
	         	Mage::log('Error Catched');
	            Mage::log($e);
	            return false;
	        } 
       
        return true;
	}
        

	private function validateOrder($orderData,$orderStatus,$orderXMLFile)
	{
		// determine order status
		$order = Mage::getModel('sales/order')->load(trim($orderData->order_id));
		if (!$order->getEntityId()) 
		{
			if (file_exists($orderXMLFile)) 
			{
				unlink($orderXMLFile);
				unlink(Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. trim($orderData->template));
			}
			return false;				
		} 
			
		if (empty($orderStatus))
		{
			if (file_exists($orderXMLFile)) {
				unlink($orderXMLFile);
				unlink(Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. trim($orderData->template));
				throw new Exception('Empty order status????');
			}
			return false;
		}										
			
		if ($orderStatus==="canceled" || $orderStatus==="cancelled") // which spelling is correct?
		{
			if (file_exists($orderXMLFile)) {
				unlink($orderXMLFile);
				unlink(Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. trim($orderData->template));
			}
			return false;
		}
		
		if ($orderStatus==="fraud")
		{
			if (file_exists($orderXMLFile)) {
				unlink($orderXMLFile);
				unlink(Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS. trim($orderData->template));
			}
			return false;
		}	
	
			return true;
	}

	private function getDirectoryList ($directory) 
	{
	  
	  	$results = array(); // array for directory list
	 	$handler = opendir($directory); // directory handler
	  	while ($file = readdir($handler))  // open directory and walk through the filenames
	  	{
	  		// read files and match against the files we are interested in
		    if (substr($file, 0, 11) === "reviewsplus") 
		    {
				
				if (substr($file, -4) === ".xml") {
					$results[] = $file;
				}
				
				if (substr($file, -10) === ".emailtest") {
					$this->sendAlertEmail('Test email from GetCustomerFeedback Module!');
				}
			}
		}
		// tidy up: close the handler
	  	closedir($handler);
	  	// return array containing filenames
	  	return $results;
	}
}

?>