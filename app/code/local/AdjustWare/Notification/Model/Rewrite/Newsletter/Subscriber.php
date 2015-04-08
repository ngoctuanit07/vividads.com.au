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
//class AdjustWare_Notification_Model_Rewrite_Newsletter_Subscriber extends Mage_Newsletter_Model_Subscriber
class AdjustWare_Notification_Model_Rewrite_Newsletter_Subscriber extends Aitoc_Aitemails_Model_Rewrite_NewsletterSubscriber
{
    /**
     * Subscribe customer to newsletter
     * Rewrite to add AdjustWare code
     *
     * @param object $customer
     * @return AdjustWare_Notification_Model_Rewrite_Newsletter_Subscriber 
     */
    public function sendConfirmationSuccessEmail()
    {
        parent::sendConfirmationSuccessEmail();
        
        if ($this->getImportMode()) {
            return $this;
        }
        if(!Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_TEMPLATE) || !Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY))  {
            return $this;
        }
        // AdjustWare code start
        if (Mage::getStoreConfig('adjnotification/new_newsletter/notification'))
        {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $params = array(
                'new_newsletter' => $this,
                'customer_name'=>$customer->getName(),
                );
            Mage::getModel('adjnotification/sender')->sendNotification('new_newsletter', $params);
        }
        // AdjustWare code finish
        return $this;
    }

    /**
     * Unsubscribe customer to newsletter
     * Rewrite to add AdjustWare code
     *
     * @param object $customer
     * @return AdjustWare_Notification_Model_Rewrite_Newsletter_Subscriber 
     */
    
    public function sendUnsubscriptionEmail()
    {
        parent::sendUnsubscriptionEmail();
        
        if ($this->getImportMode()) {
            return $this;
        }
        if(!Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE) || !Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY))  {
            return $this;
        }

        // AdjustWare code start
        if (Mage::getStoreConfig('adjnotification/unsubscribe_newsletter/notification'))
        {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $params = array(
                'unsubscribe_newsletter' => $this,
                'customer_name'=>$customer->getName(),
                );
            Mage::getModel('adjnotification/sender')->sendNotification('unsubscribe_newsletter', $params);
        }
        // AdjustWare code finish
        return $this;
    }
}