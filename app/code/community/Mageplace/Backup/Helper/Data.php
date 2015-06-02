<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

if(Mage::helper('mpbackup/version')->isEE()) {
	class Mageplace_Backup_Helper_Data extends Mageplace_Backup_Helper_Enterprise
	{
	}
} else {
	class Mageplace_Backup_Helper_Data extends Mageplace_Backup_Helper_Community
	{
	}
}
