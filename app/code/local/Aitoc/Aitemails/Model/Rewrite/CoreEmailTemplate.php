<?php
/**
 * Email Template Manager
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitemails
 * @version      2.0.13
 * @license:     POwWoRanFs2vVHb6wy050abrHO3ftlyCeMSf01dXU3
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
class Aitoc_Aitemails_Model_Rewrite_CoreEmailTemplate extends Mage_Core_Model_Email_Template
{
    
    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null)
    {
        $templatePath = Mage::helper('aitemails')->getPathByEmailTemplateCode('sales_email_order_template');
        $collection  = Mage::getResourceSingleton('aitemails/aittemplate_collection');
        $aCollection = $collection->load()->toArray();
        $aCollection = $aCollection['items'];
        
        $this->load($templateId);
        
        foreach ($aCollection as $template)
        {
            $templatePath = Mage::helper('aitemails')->getPathByEmailTemplateCode($template['code']);
            if (!$this->getId() && is_numeric($templateId)) 
            {
                $config_value = Mage::getStoreConfig($templatePath);

                if ($config_value == $templateId)
                {
                    $templateId = $template['code'];
                }
            }
        }
        
        $this->setAitmailsStoreId($storeId);
        return parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
    }
    
    protected function _afterSave()
    {
        $res = parent::_afterSave();
        $observer = new Varien_Event_Observer();
        $observer->setObject($this);
        Mage::getSingleton('aitemails/observer')->performSaveCommitAfter($observer);
        return $res;
    }
    
    public function getAitmailsStoreId()
    {
        $storeId = $this->getData('aitmails_store_id');
        if (!$storeId)
        {
            $storeId = Mage::app()->getStore()->getId();
        }
        return $storeId;
    }
    
    /**
     * Send mail to recipient
     *
     * @param   string      $email          E-mail
     * @param   string|null $name         receiver name
     * @param   array       $variables    template variables
     * @return  boolean
     **/
    public function send($email, $name=null, array $variables = array())
    {
        if(!$this->isValidForSend()) {
            return false;
        }
        
        if (version_compare(Mage::getVersion(), '1.4.2', '>='))
        {
            $emails = array_values((array)$email);
            $names = is_array($name) ? $name : (array)$name;
            $names = array_values($names);
            
            foreach ($emails as $key => $email) {
                if (!isset($names[$key])) {
                    $names[$key] = substr($email, 0, strpos($email, '@'));
                }
            }
            $variables['email'] = $emails;
            $variables['name'] = $names;
        }
        else
        {
            if (is_null($name))
            {
                $name = substr($email, 0, strpos($email, '@'));
            }
            $variables['email'] = $email;
            $variables['name'] = $name;
        }

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();

        $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getSenderEmail();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if ($returnPathEmail !== null)
        {
            if (version_compare(Mage::getVersion(), '1.4.2', '>='))
            {
                $mailTransport = new Zend_Mail_Transport_Sendmail($returnPathEmail);
                Zend_Mail::setDefaultTransport($mailTransport);
            } else {
                $mail->setReturnPath($returnPathEmail);    
            }                
        }
 
        if (version_compare(Mage::getVersion(), '1.4.2', '>='))
        {
            foreach ($emails as $key => $email) 
            {
                $mail->addTo($email, '=?utf-8?B?' . base64_encode($names[$key]) . '?=');
            }
        }
        else
        {
            if (is_array($email)) 
            {
                foreach ($email as $emailOne) 
                {
                    $mail->addTo($emailOne, $name);
                }
            } 
            else
            {
                $mail->addTo($email, '=?utf-8?B?' . base64_encode($name) . '?=');
            }
        }

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);
        
        if($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $textplain = str_replace('<br />', "\n", $text);
            $textplain = str_replace('<br/>', "\n", $text);
            $textplain = strip_tags($textplain);
            $mail->setBodyText($textplain);
            
            // adding html tag
            if (false === strpos($text, '<html>'))
            {
                $text = '<html>' . $text;
            }
            if (false === strpos($text, '</html>'))
            {
                $text = $text . '</html>';
            }
            
            $mail->setBodyHTML($text);
        }

        $mail->setSubject('=?utf-8?B?'.base64_encode($this->getProcessedTemplateSubject($variables)).'?=');
        $mail->setFrom($this->getSenderEmail(), $this->getSenderName());
        
        // adding attachments
        $attachmentCollection = Mage::getModel('aitemails/aitattachment')->getTemplateAttachments($this->getId());
        if (count($attachmentCollection) > 0)
        {
            foreach ($attachmentCollection as $attachment)
            {
                if ($attachment->getAttachmentFile())
                {
                    $sFileExt = substr($attachment->getAttachmentFile(), strrpos($attachment->getAttachmentFile(), '.'));
                    if ($attachment->getData('store_title'))
                    {
                        $sFileName = $this->normalizeFilename($attachment->getData('store_title')) . $sFileExt;
                    } else 
                    {
                        $sFileName = substr($attachment->getAttachmentFile(), 1 + strrpos($attachment->getAttachmentFile(), '/'));
                    }
                    $att = $mail->createAttachment(file_get_contents(Aitoc_Aitemails_Model_Aitattachment::getBasePath() . $attachment->getAttachmentFile()));
                    $att->filename = $sFileName;
                }
            }
        }

        try
        {
            $mail->send(); // Zend_Mail warning..
            $this->_mail = null;
        }
        catch (Exception $e)
        {
            return false;
        }
        
        return true;
    }
    
    public function normalizeFilename($sFileName)
    {
        $sFileName = preg_replace('@[^a-zA-Z0-9_]@', '_', $sFileName);
        $sFileName = preg_replace('@_+@', '_', $sFileName);
        return $sFileName;
    }
}