<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'url_key',
    'VARCHAR( 255 ) NOT NULL AFTER url_rewrite_id'
);

$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'photo_slide_show_size',
    'VARCHAR( 255 ) NOT NULL AFTER thumbnail_size'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/gallery'), 'url_key',
    'VARCHAR( 255 ) NOT NULL AFTER url_rewrite_id'
);
$installer->endSetup();

