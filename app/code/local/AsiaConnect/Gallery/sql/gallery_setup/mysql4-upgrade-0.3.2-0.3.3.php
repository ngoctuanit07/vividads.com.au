<?php


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->getConnection()->addColumn($resource->getTableName('gallery/review'), 'review_type',
    'tinyint(4) NOT NULL AFTER gallery_id'
);

$installer->getConnection()->addColumn($resource->getTableName('gallery/gallery'), 'url_key',
    'varchar(255) NOT NULL AFTER url_rewrite_id'
);

$installer->endSetup();

