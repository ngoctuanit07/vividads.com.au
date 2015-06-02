<?php
/**
 * Mageplace Magesocial
 *
 * @category   Mageplace
 * @package    Mageplace_Magesocial
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Source_Backupprocessstatus extends Mageplace_Backup_Model_Source_Abstract
{
	const STATUS_RUNNING = 0;
	const STATUS_FINISHED = 1;
	const STATUS_ERROR = 2;
	const STATUS_INTERRUPTED_UNSUCCESSFULLY = 3;
	const STATUS_UNKNOWN = 4;
	
	
	public function toOptionArray()
	{
		return array(
			array('value' => self::STATUS_RUNNING, 'label' => $this->_getHelper()->__('Running')),
			array('value' => self::STATUS_FINISHED, 'label' => $this->_getHelper()->__('Finished')),
			array('value' => self::STATUS_ERROR, 'label' => $this->_getHelper()->__('Errors')),
			array('value' => self::STATUS_INTERRUPTED_UNSUCCESSFULLY, 'label' => $this->_getHelper()->__('Interrupted Unsuccessfully')),
			array('value' => self::STATUS_UNKNOWN, 'label' => $this->_getHelper()->__('Unknown')),
		);
	}
}
