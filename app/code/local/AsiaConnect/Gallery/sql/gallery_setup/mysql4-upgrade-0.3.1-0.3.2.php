<?php


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'show_photo_update_date',
    'tinyint(4) NOT NULL AFTER show_photo_link'
);

$installer->getConnection()->addColumn($resource->getTableName('gallery/review'), 'status',
    'tinyint(4) NOT NULL AFTER create_time'
);

$installer->endSetup();

