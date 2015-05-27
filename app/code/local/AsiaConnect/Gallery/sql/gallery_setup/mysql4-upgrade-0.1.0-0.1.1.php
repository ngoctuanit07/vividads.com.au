<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->getConnection()->addColumn($resource->getTableName('gallery/gallery'), 'order',
    'tinyint(4) NOT NULL DEFAULT 0 AFTER update_time'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'order',
    'tinyint(4) NOT NULL DEFAULT 0 AFTER update_time'
);
$installer->endSetup();
