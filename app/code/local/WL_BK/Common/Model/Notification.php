<?php
/**
* @description Notification Object for White Labelled custom modules
* @name WL_Common_Model_Notification
* @package WL_Common
* @author kennguyen
*/
class WL_Common_Model_Notification
{
	protected $_notification_email = null;
	
	public function __construct( $email )
	{
		$this->_notification_email = $email;		
	}

	public function getNotificationEmail()
	{
		return $this->_notification_email;
	}

	// This function is used to get admin email address
	public function getAdminEmail()
	{
		$model = Mage::getModel('admin/user');
		$admins = $model->getCollection();
		$list_emails = array();
		
		foreach($admins as $admin)
		{
			$list_emails[] = $admin->getEmail();
		}
		
		return (isset($list_emails[0])) ? $list_emails[0] : '';
	} 
	
		
	// This function is used to notify admin in case of error
	public function sendErrorNotificationToAdmin($message, $senderName = 'WL Notification', $senderEmail = 'no-reply@whitelabelled.com.au', $subject = 'Notification')
	{
		$emailTemplate  = Mage::getModel('core/email_template')->loadDefault('custom_admin_error_notification');
		$emailTemplate->setSenderName($senderName);
		$emailTemplate->setSenderEmail($senderEmail);
		$emailTemplate->setTemplateSubject($subject);
	
		$emailTemplateVariables = array( 'message'=>$message );
			
		$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
	
		$email = $this->getNotificationEmail();
		if ( empty($email) )
			$email = $this->getAdminEmail();

		if($email) {
			$emailTemplate->send($email,'WL Admin', $emailTemplateVariables);
		}
	
	}
}