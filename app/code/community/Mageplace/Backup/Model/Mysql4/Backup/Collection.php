<?php
/**
 * Mageplace Magesocial
 *
 * @category   Mageplace
 * @package    Mageplace_Magesocial
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Mysql4_Backup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * Constructor
	 */
	protected function _construct()
	{
		$this->_init('mpbackup/backup');
	}

	/**
	 * Creates an options array for grid filter functionality
	 *
	 * @return array Options array
	 */
	public function toOptionHash()
	{
		return $this->_toOptionHash('backup_id', 'backup_name');
	}
	
	/**
	 * Creates an options array for edit functionality
	 *
	 * @return array Options array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('backup_id', 'backup_name');
	}
	
	/**
	 * Add Filter by profile
	 * 
	 * @param int|Mageplace_Backup_Model_Profile|Mageplace_Backup_Model_Backup $profile Profile to be filtered
	 * @return Mageplace_Backup_Model_Mysql4_Backup_Collection
	 */
	public function addProfileFilter($profile)
	{
		if($profile instanceof Mageplace_Backup_Model_Profile) {
			$profile = $profile->getId();
		} else if($profile instanceof Mageplace_Backup_Model_Backup) {
			$profile = $profile->getProfileId();
		}
		
		$profile = (int)$profile;
		if(!$profile) {
			return $this;
		}
		
		$select = $this->getSelect();

		$select->join(
				array(
					'profile_table' => $this->getTable('mpbackup/profile')
				),
				'main_table.profile_id = profile_table.profile_id',
				array()
			);

		$select->where(
				'profile_table.profile_id IN (?)',
				array (
					0, 
					$profile
				)
			)->group(
				'main_table.backup_id'
			);

		return $this;
	}

	/**
	 * Add Filter by errors
	 * 
	 * @param int
	 * @return Mageplace_Backup_Model_Mysql4_Backup_Collection
	 */
	public function addErrorFilter($value)
	{
		if($value) {
			$this->addFieldToFilter('backup_errors', array('neq' => ""));
		} else {
			$this->addFilter('backup_errors', '');
		}
		
//		var_dump($this->getSelect()->assemble()); die;

		
		return $this;
	}
}
