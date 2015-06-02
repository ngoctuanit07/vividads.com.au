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

class Aitoc_Aitemails_Model_Mysql4_Aitattachment extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Varien class constructor
     *
     */
    protected function  _construct()
    {
        $this->_init('aitemails/aitattachment', 'attachment_id');
    }

    public function saveItemTitle($aitattachmentObject)
    {
        $stmt = $this->_getReadAdapter()->select()
            ->from($this->getTable('aitemails/aitattachment_title'))
            ->where('attachment_id = ?', $aitattachmentObject->getId())
            ->where('store_id = ?', $aitattachmentObject->getStoreId());
        if ($this->_getReadAdapter()->fetchOne($stmt)) {
            $where = $this->_getReadAdapter()->quoteInto('attachment_id = ?', $aitattachmentObject->getId()) .
                ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $aitattachmentObject->getStoreId());
            if ($aitattachmentObject->getUseDefaultTitle()) {
                $this->_getWriteAdapter()->delete(
                    $this->getTable('aitemails/aitattachment_title'), $where);
            } else {
                $this->_getWriteAdapter()->update(
                    $this->getTable('aitemails/aitattachment_title'),
                    array('title' => $aitattachmentObject->getTitle()), $where);
            }
        } else {
            if (!$aitattachmentObject->getUseDefaultTitle()) {
                $this->_getWriteAdapter()->insert(
                    $this->getTable('aitemails/aitattachment_title'),
                    array(
                        'attachment_id' => $aitattachmentObject->getId(),
                        'store_id' => $aitattachmentObject->getStoreId(),
                        'title' => Mage::getModel('core/email_template')->normalizeFilename($aitattachmentObject->getTitle()),
                    ));
            }
        }
        return $this;
    }

    public function deleteItems($items)
    {
        $where = '';
        if ($items instanceof Aitoc_Aitdownloadablefiles_Model_Aitfile) {
            $where = $this->_getReadAdapter()->quoteInto('attachment_id = ?', $items->getId());
        }
        elseif (is_array($items)) {
            $where = $this->_getReadAdapter()->quoteInto('attachment_id in (?)', $items);
        }
        else {
            $where = $this->_getReadAdapter()->quoteInto('attachment_id = ?', $items);
        }
        if ($where) {
            $this->_getReadAdapter()->delete(
                $this->getTable('aitemails/aitattachment'),$where);
            $this->_getReadAdapter()->delete(
                $this->getTable('aitemails/aitattachment_title'), $where);
        }
        return $this;
    }
    
}