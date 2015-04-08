<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Change columns
 */
$installer->getConnection()->addColumn(
    $installer->getTable('cron/schedule'),
    'reported_at',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable'  => true,
        'comment'   => 'Reported At'
    )
);

$installer->endSetup();