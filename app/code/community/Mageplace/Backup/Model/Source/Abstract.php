<?php
/**
 * Mageplace Magesocial
 *
 * @category   Mageplace
 * @package    Mageplace_Magesocial
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

abstract class Mageplace_Backup_Model_Source_Abstract
{
	abstract public function toOptionArray();

	protected function _getHelper()
	{
		return Mage::helper('mpbackup');
	}

	public function toOptionHash()
	{
		$hash = array();
		foreach($this->toOptionArray() as $item) {
			$hash[$item['value']] = $item['label'];
		}
		
		return $hash;
	}
}
