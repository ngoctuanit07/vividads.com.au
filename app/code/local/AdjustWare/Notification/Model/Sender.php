<?php
/**
 * Admin Email Notifications
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Notification
 * @version      2.2.1
 * @license:     v0W0smQGrPoeX1hZVQIwwD5o2pIQVJHfDanN67jvqj
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * Save evenys's observer
 *
 * @author Aitoc
 */
class AdjustWare_Notification_Model_Sender extends Mage_Core_Model_Abstract
{
    /**
     * Send email notification
     * 
     * @param string $to
     * @param string $template
     * @param array $params
     */
    private function _sendNotification($to, $template, $params=array())
    {
        if (!$to)
        {
            return;
        }
        if (!$template)
        {
            return;
        }
        $to = str_replace(array(',',';','|',':'), ',', $to);
        $to = explode(',', $to);
        // if admin is aleady logged in, this prevents
        // redirect to dashboard
		if(isset($params['url']))
		{
			$pos = strpos($params['url'],'key');
			if ($pos){
				$params['url'] = substr($params['url'], 0, $pos);    
			}
		}
		
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        try {
            $name = null;
            if ( version_compare(Mage::getVersion(), '1.4.2.0', '<') ) {
                $email = reset($to);
                $name = substr($email, 0, strpos($email, '@'));
            }
            
            $emailModel = Mage::getModel('core/email_template');
            $emailModel->sendTransactional(
                $template, //Mage::getStoreConfig('adjnotification/'.$eventType.'/template'),
                Mage::getStoreConfig('adjnotification/sender/identity'),
                $to,
                $name,
                $params
            );            

            $translate->setTranslateInline(true);
        
        } catch (Exception $e) {
            $translate->setTranslateInline(true);
			foreach($to as $recipient) {
                mail($recipient, 'Notification error', $e->getMessage());
            }
        }
    }

    /**
     * Prepare data and template email to send
     *
     * @param string $eventType
     * @param array $params
     * @param int|null $order_status
     */
    public function sendNotification($eventType, $params, $order_status=null)
    {
        if ($order_status)
        {
            $to = trim(Mage::getStoreConfig('adjnotification/'.$eventType.'/send_to_'.$order_status));
            $template = Mage::getStoreConfig('adjnotification/'.$eventType.'/template_'.$order_status);
        }
        else
        {
            $to = trim(Mage::getStoreConfig('adjnotification/'.$eventType.'/send_to'));
            $template = Mage::getStoreConfig('adjnotification/'.$eventType.'/template'); 
        }
        $this->_sendNotification($to, $template, $params);
    }
}