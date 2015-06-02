<?php
class IWD_OrderManager_Model_Notify_Notification extends Mage_Core_Model_Abstract
{
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/order/identity';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/order/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/order/copy_method';

    const EMAIL_NOTIFY_TEMPLATE = 'iwd_ordermanager/edit/transaction_email';
    const EMAIL_NOTIFY_TEMPLATE_GUEST = 'iwd_ordermanager/edit/transaction_email_guest';
    const EMAIL_CONFIRM_TEMPLATE = 'iwd_ordermanager/edit/confirm_transaction_email';
    const EMAIL_CONFIRM_TEMPLATE_GUEST = 'iwd_ordermanager/edit/confirm_transaction_email_guest';

    public function sendConfirmEmail($order_id, $log, $message = null)
    {
        $store_id = Mage::getModel("sales/order")->load($order_id)->getStoreId();
        $cancel_link = Mage::getUrl('iwd_order_manager/confirm/edit', array('action'=>'cancel', 'pid'=>$log->getConfirmLink(), "_store"=>$store_id));
        $confirm_link = Mage::getUrl('iwd_order_manager/confirm/edit', array('action'=>'confirm', 'pid'=>$log->getConfirmLink(), "_store"=>$store_id));

        $template_params = array(
            "changes_log"=> $log->getLogOperations(),
            "cancel_link"=> $cancel_link,
            "confirm_link"=> $confirm_link,
        );

        return $this->sendEmailBase(
            $order_id,
            $message,
            self::EMAIL_CONFIRM_TEMPLATE,
            self::EMAIL_CONFIRM_TEMPLATE_GUEST,
            $template_params
        );
    }

    public function sendNotifyEmail($order_id, $message = null)
    {
        return $this->sendEmailBase(
            $order_id,
            $message,
            self::EMAIL_NOTIFY_TEMPLATE,
            self::EMAIL_NOTIFY_TEMPLATE_GUEST
        );
    }

    protected function sendEmailBase($order_id, $message, $template, $template_guest, $template_params=array())
    {
       try {
            $order = Mage::getModel('sales/order')->load($order_id);
            $store_id = $order->getStore()->getId();

            // Retrieve corresponding email template id and customer name
            if ($order->getCustomerIsGuest()) {
                $template = Mage::getStoreConfig($template_guest, $store_id);
                $customer_name = $order->getBillingAddress()->getName();
            } else {
                $template = Mage::getStoreConfig($template, $store_id);
                $customer_name = $order->getCustomerName();
            }

            $payment_html = $this->paymentBlockHtml($order);
            $customer_email = $order->getCustomerEmail();
            $_template_params = array(
                'order' => $order,
                'billing' => $order->getBillingAddress(),
                'payment_html' => $payment_html,
                'note_message' => $message
            );

            $template_params = array_merge($template_params, $_template_params);

            $mail = new Varien_Object();
            $mail->setTemplateId($template);
            $mail->setCustomerName($customer_name);
            $mail->setCustomerEmail($customer_email);
            $mail->setTemplateParams($template_params);
            $mail->setStoreId($store_id);

            $this->sendEmail($mail);

        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            return false;
        }
        return true;
    }

    protected function paymentBlockHtml($order)
    {
        $store_id = $order->getStore()->getId();
        $payment = $order->getPayment();

        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($store_id);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($payment)->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($store_id);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            Mage::log($exception->getMessage(), null, 'iwd_order_manager.log');

            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $paymentBlockHtml;
    }

    protected function sendEmail($mail)
    {
        $sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $mail->getStoreId());

        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO, $mail->getStoreId());
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $mail->getStoreId());

        $mailer = Mage::getModel('core/email_template_mailer');

        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($mail->getCustomerEmail(), $mail->getCustomerName());

        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }

        $mailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender($sender);
        $mailer->setStoreId($mail->getStoreId());
        $mailer->setTemplateId($mail->getTemplateId());
        $mailer->setTemplateParams($mail->getTemplateParams());
        $mailer->send();
    }

    protected function _getEmails($config_path, $store_id)
    {
        $data = Mage::getStoreConfig($config_path, $store_id);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }
}