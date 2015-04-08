<?php
/**
 * Mageplace Backup
 *
 * @category   Mageplace
 * @package    Mageplace_Backup
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Mysql4_Backup extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('mpbackup/backup', 'backup_id');
	}

	/**
	 * Sets the creation and update timestamps
	 *
	 * @param	Mage_Core_Model_Abstract $object Current profile
	 * @return	Mageplace_Backup_Model_Mysql4_Backup
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		$id = $object->getBackupId();
	    if(!$id) {
			$object->setBackupCreationDate(Mage::getSingleton('core/date')->gmtDate());
		}
		$object->setBackupUpdateDate(Mage::getSingleton('core/date')->gmtDate());

		return $this;
	}
	
	public function getCurrentBackupId()
	{
		$read = $this->_getReadAdapter();

		$backup_id_select = $read->select()
			->from($this->getMainTable(), array($this->getIdFieldName()))
			->where("`backup_finish_date` = '0000-00-00 00:00:00'")
			->order($this->getIdFieldName().' DESC')
			->limit(1);

		$backup_id = (int) $read->fetchOne($backup_id_select);
		
		return $backup_id;
	}
    
	public function trySetStatusAtomic($backupId, $status, $statusValue)
    {
        $write = $this->_getWriteAdapter();
        $result = $write->update(
           $this->getMainTable(),
            array($status => $statusValue),
            array('backup_id = ?' => $backupId)
        );
        
		if ($result == 1) {
            return true;
        }
        
		return false;
    } 
}
