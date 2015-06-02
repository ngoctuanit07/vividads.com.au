<?php
/**
 * Mageplace Backup
 *
 * @category   Mageplace
 * @package    Mageplace_Backup
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Mysql4_Temp extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('mpbackup/temp', 'temp_id');
	}
	
	public function insertMessage($backup_id, $type, $message, $date=null)
	{
		$write = $this->_getWriteAdapter();

		$data = array(
			'backup_id'		=> $backup_id,
			'type'			=> $type,
			'message'		=> $message,
			'created'		=> ($date ? $date : date('Y-m-d H:i:s'))
		);
		
		$return = Mage::getModel('mpbackup/interceptor')->checkMethodCall($write, 'insert', $this->getMainTable(), $data);
		/* the same as $return = $this->_getWriteAdapter()->insert($this->getMainTable(), $data); */
		
		return $return;
	}

	public function getMessages($backup_id, $type=null)
	{
		if(!$backup_id) {
			return null;
		}
		
		$read = $this->_getReadAdapter();
		$write = $this->_getWriteAdapter();
		
		$select = $read->select()
			->from($this->getMainTable(), ($type ? 'message' : array('type', 'message')));
		
		$select->where($write->quoteInto('backup_id = ?', $backup_id));
		
		if($type) {
			$select->where($write->quoteInto('type = ?', $type));
		}
		
		if($type) {
			$data = Mage::getModel('mpbackup/interceptor')->checkMethodCall($read, 'fetchCol', $select);
			/* the same as $data = $read->fetchCol($select); */
		} else {
			$data = Mage::getModel('mpbackup/interceptor')->checkMethodCall($read, 'fetchAll', $select);
			/* the same as $data = $read->fetchAll($select); */
		}
		
		return $data;
	}

	public function clearMessages($backup_id)
	{
		$write = $this->_getWriteAdapter();
		
		$return = Mage::getModel('mpbackup/interceptor')->checkMethodCall(
			$write,
			'delete',
			$this->getMainTable(),
			$this->_getWriteAdapter()->quoteInto('backup_id = ?', $backup_id)
		);
		/* the same as $return = $this->_getWriteAdapter()->delete(
			$this->getMainTable(),
			$this->_getWriteAdapter()->quoteInto('backup_id = ?', $backup_id)
		); */
		
		return $return;
	}

	public function clearAllMessages()
	{
		return $this->_getWriteAdapter()->truncate($this->getMainTable()); 
	}
}
