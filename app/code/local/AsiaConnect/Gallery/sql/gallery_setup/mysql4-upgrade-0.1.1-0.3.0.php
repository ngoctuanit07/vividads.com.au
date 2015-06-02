<?php


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();


$installer->getConnection()->addColumn($resource->getTableName('gallery/gallery'), 'photo_link',
    'VARCHAR( 255 ) NOT NULL AFTER content'
);

$installer->getConnection()->addColumn($resource->getTableName('gallery/gallery'), 'url_rewrite_id',
    'int( 11 ) NOT NULL AFTER album_id'
);

$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'bottom_description',
    'text NOT NULL AFTER content'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'parent_id',
    'int( 11 ) NOT NULL AFTER content'
);

$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'store_id',
    'smallint( 6 ) NOT NULL AFTER parent_id'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'url_rewrite_id',
    'int( 11 ) NOT NULL AFTER store_id'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'featured',
    'tinyint( 4 ) NOT NULL AFTER store_id'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'thumbnail_size',
    'VARCHAR( 255 ) NOT NULL AFTER featured'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'show_photo_title',
    'tinyint( 4 ) NOT NULL AFTER thumbnail_size'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'show_photo_description',
    'tinyint( 4 ) NOT NULL AFTER show_photo_title'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'show_photo_link',
    'tinyint( 4 ) NOT NULL AFTER show_photo_description'
);
$installer->getConnection()->addColumn($resource->getTableName('gallery/album'), 'default_config',
    'tinyint( 4 ) NOT NULL AFTER show_photo_link'
);

$installer->run("
-- DROP TABLE IF EXISTS {$resource->getTableName('gallery/gallery_store')};
CREATE TABLE {$resource->getTableName('gallery/gallery_store')} (
  `album_id` int(11) NOT NULL,
  `store_id` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$resource->getTableName('gallery/review')} (
  `review_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gallery_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `rate` tinyint(4) NOT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`review_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

UPDATE {$resource->getTableName('gallery/album')} SET `album_id`= `album_id` + 1;

INSERT INTO {$resource->getTableName('gallery/album')} (`album_id`,`title`, `content`, `bottom_description`, `store_id`, `default_config`, `status`, `order`) 
VALUES (1,'Gallery', 'this is content of root album', 'this is bottom description of root album', '0', '1', '1', '0');

UPDATE {$resource->getTableName('gallery/album')} SET `parent_id`=1 WHERE `album_id`<>1 AND `parent_id` = 0;

INSERT INTO {$resource->getTableName('gallery/gallery_store')} (`album_id`, `store_id`) VALUES ('1', '0');
INSERT INTO {$resource->getTableName('gallery/core_url_rewrite')} (`id_path`, `request_path`, `target_path`, `is_system`) VALUES ('gallery/album/1', 'gallery/gallery.html','gallery/view/album/id/1',1);
UPDATE {$resource->getTableName('gallery/album')} SET `url_rewrite_id` = (SELECT `url_rewrite_id` FROM {$resource->getTableName('gallery/core_url_rewrite')} WHERE `id_path`='gallery/album/1') WHERE `album_id`=1
    ");

$installer->endSetup();
$installer = $this;

