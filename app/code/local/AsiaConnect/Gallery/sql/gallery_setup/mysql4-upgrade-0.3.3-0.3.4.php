<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->getConnection()->addColumn($resource->getTableName('gallery/gallery'), 'rate',
    'tinyint(4) NOT NULL AFTER update_time'
);

$installer->endSetup();

