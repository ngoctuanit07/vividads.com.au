<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_MultiFees
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Multi Fees extension
 *
 * @category   MageWorx
 * @package    MageWorx_MultiFees
 * @author     MageWorx Dev Team
 */

class MageWorx_MultiFees_Model_Mysql4_Language_Fee extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() {
        $this->_init('multifees/language_fee', 'fee_lang_id');
    }
    
    public function loadByFeeAndStore($object, $feeId, $storeId) {
	$object->unsetData();
        $read = $this->_getReadAdapter();
        if ($read) {  
            $select = $read->select()
                ->from($this->getMainTable())
                ->where('fee_id = ?', $feeId)
                ->where('store_id = ?', $storeId)
                ->limit(1); 
            $data = $read->fetchRow($select);
            if ($data) $object->addData($data);
        }
    }

    public function getTitle($feeId, $storeId = 0) {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getMainTable(), 'title')
            ->where('fee_id = ?', $feeId)
            ->where('store_id = ?', $storeId);
        $title = $read->fetchOne($select);
        
        if (!$title && $storeId!=0) {
            $select = $read->select()
                ->from($this->getMainTable(), 'title')
                ->where('fee_id = ?', $feeId)
                ->where('store_id = 0');
            $title = $read->fetchOne($select);
        }
        return $title;
    }
}