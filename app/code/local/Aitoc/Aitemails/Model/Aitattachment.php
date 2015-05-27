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
class Aitoc_Aitemails_Model_Aitattachment extends Mage_Core_Model_Abstract
{
    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('aitemails/aitattachment');
    }
    
    /**
     * Enter description here...
     *
     * @return Mage_Downloadable_Model_Sample
     */
    protected function _afterSave()
    {
        $this->getResource()->saveItemTitle($this);
        return parent::_afterSave();
    }
    
    public static function getBaseTmpPath()
    {
        return Mage::getBaseDir('media') . DS . 'aitemails' . DS . 'attachments' . DS . 'tmp' ;      
    }

    public static function getBasePath()
    {
        return Mage::getBaseDir('media') . DS . 'aitemails' . DS . 'attachments' . DS . 'attachment' ;      
    }
    
    public function getTemplateAttachments($iTemplateId)
    {
        $rc = $this->getResourceCollection();
        $rc->addFieldToFilter('template_id', $iTemplateId)->addTitleToResult();
        return $rc->load();
    }
    
    public function getFileUrl()
    {
        return Mage::getBaseUrl('media') . 'aitemails' . DS . 'attachments' . DS . 'attachment' . $this->getAttachmentFile();
    }
    
}