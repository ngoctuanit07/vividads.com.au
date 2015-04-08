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
class Aitoc_Aitemails_Model_Rewrite_CoreMysql4EmailTemplate extends Mage_Core_Model_Mysql4_Email_Template
{
    public function save(Mage_Core_Model_Abstract $template)
    {
        parent::save($template);
        $this->afterSave($template);
    }            

    public function delete(Mage_Core_Model_Abstract $template)
    {
        $templateId = $template->getId();
        // removing attachments
        $attachmentCollection = Mage::getModel('aitemails/aitattachment')->getTemplateAttachments($templateId);

        if (count($attachmentCollection) > 0)
        {
            foreach ($attachmentCollection as $attachment)
            {
                @unlink(Aitoc_Aitemails_Model_Aitattachment::getBasePath() . $attachment->getAttachmentFile());
                @unlink(Aitoc_Aitemails_Model_Aitattachment::getBaseTmpPath() . $attachment->getAttachmentFile());
                Mage::getModel('aitemails/aitattachment')->load($attachment->getId())->delete();
            }
        }
        return parent::delete($template);
    }

    protected function _redirectToList($message = '')
    {
        if ($message)
        {
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        }
        $curWebsite = Mage::app()->getRequest()->getParam('website');
        $curStore   = Mage::app()->getRequest()->getParam('store');
        $aParams    = array();
        if ($curWebsite) $aParams['website'] = $curWebsite;
        if ($curStore) $aParams['store'] = $curStore;
        Mage::app()->getResponse()->setRedirect(Mage::getModel('adminhtml/url')->getUrl('aitemails/index', $aParams));
        Mage::app()->getResponse()->sendHeaders();
        exit(); // if not to exit, this headers will be cleared in controller, and we will have no redirect
    }

    protected function _addToConfig(Mage_Core_Model_Email_Template $template, $templateCode = '')
    {
        Mage::getModel('aitemails/aitemails')->addTemplateToConfig($template, $templateCode);
    }

    public function afterSave(Mage_Core_Model_Email_Template $template)
    {
        if (true !== Mage::registry('aitemails_template_save_noaddconfig'))
        {
            $template_data = $template->getData();
            $this->_addToConfig($template, $template_data['orig_template_code']);
        }

        $oReq = Mage::app()->getFrontController()->getRequest();

        /**
        * saving attachments
        */
        if ($data = $oReq->getPost('aitemails')) 
        {
            if (isset($data['aitattachment'])) 
            {
                $_deleteItems = array();
                foreach ($data['aitattachment'] as $aitattachmentItem) 
                {
                    if ($aitattachmentItem['is_delete'] == '1') 
                    {
                        if ($aitattachmentItem['aitattachment_id']) 
                        {
                            $_deleteItems[] = $aitattachmentItem['aitattachment_id'];
                        }
                    } 
                    else 
                    {
                        $aitattachmentModel = Mage::getModel('aitemails/aitattachment');
                        $files = array();
                        if (isset($aitattachmentItem['file'])) {
                            $files = Zend_Json::decode($aitattachmentItem['file']);
                        }

                        $aitattachmentModel->setData($aitattachmentItem)
                            ->setAttachmentType($aitattachmentItem['type'])
                            ->setTemplateId($template->getId())
                            ->setStoreId(0);

                        /* If file is new - its id = 0 */
                        if ($aitattachmentItem['aitattachment_id'] != 0)
                        {
                            $aitattachmentModel->setAttachmentId($aitattachmentItem['aitattachment_id']);
                        }

                        if ($aitattachmentModel->getAttachmentType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) 
                        {
                            
                            $aitattachmentFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                $aitattachmentModel->getBaseTmpPath(),
                                $aitattachmentModel->getBasePath(),
                                $files
                            );
                            $aitattachmentModel->setAttachmentFile($aitattachmentFileName);
                        }

                        $aitattachmentModel->save();
                    }
                }
                if ($_deleteItems) 
                {
                    Mage::getResourceModel('aitemails/aitattachment')->deleteItems($_deleteItems);
                }
            }
        } 
    }
}