<?php
/**
 * Aptoplex Easy Uploader
 * Database table upgrade script
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 *
 * @var $installer Mage_Core_Model_Resource_Setup
 */

$installer = $this;

$installer->startSetup();

// We're changing the column data type to 'TEXT' so it works with a custom alphanumeric order number.
$installer->getConnection()->modifyColumn($this->getTable('aptoplex_easyuploader/upload'), 'order_id', Varien_Db_Ddl_Table::TYPE_TEXT);

$installer->endSetup();