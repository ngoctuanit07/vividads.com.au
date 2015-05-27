<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'meta_keyword',
    'text NOT NULL AFTER update_time'
);

$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'meta_description',
    'text NOT NULL AFTER update_time'
);

$installer->endSetup();


