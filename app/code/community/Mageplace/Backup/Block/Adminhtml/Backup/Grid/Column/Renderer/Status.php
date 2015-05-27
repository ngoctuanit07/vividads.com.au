<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Block_Adminhtml_Backup_Grid_Column_Renderer_Status
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Renders grid column
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		if($row->getData('backup_errors')) {
			return '<strong style="color:red;">' . Mage::helper('mpbackup')->__('Errors') . '</strong>';
		} else if($row->getData('backup_finished') == 1 && ($row->getData('backup_files') || $row->getData('backup_cloud_files')) && $row->getData('backup_log_file')) {
			return '<strong style="color:green;">' . Mage::helper('mpbackup')->__('Finished') . '</strong>';
		} else if($row->getData('backup_finished') == 0) {
			$backup_creation_date = strtotime($row->getData('backup_creation_date'));
			$lifeCicle = $backup_creation_date + Mageplace_Backup_Helper_Const::CRON_SCHEDULES_RUN_LIFETIME_CYCLE*60;
			if($lifeCicle <= time()) {
				return '<strong style="color:orange;">' . Mage::helper('mpbackup')->__('Running for too long or Interrupted Unsuccessfully') . '</strong>';
			}
			return '<strong style="color:#406A83;">' . Mage::helper('mpbackup')->__('Running') . '</strong>';
		} else {
			return '<strong>' . Mage::helper('mpbackup')->__('Unknown') . '</strong>';
		}
	}
}
