<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
 
$installer->startSetup();
 
$installer->run("
 
DROP TABLE IF EXISTS {$this->getTable('quotation')};
CREATE TABLE {$this->getTable('quotation')} (
  increment_id varchar(25) NOT NULL,
  `quotation_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` varchar(255) NOT NULL default '',
  `caption` varchar(255) NOT NULL default '',
  `valid_end_time` date NULL,
  `created_time` date NOT NULL,
  `update_time` date NOT NULL,
  `message` text NOT NULL,
  `product_id` int NULL,
  `user` varchar(255) NULL default 'inconnu',
  `show_detail_price` int(1) NOT NULL default 1,
  price_ht decimal(10,2) NOT NULL default 0,
  visible int(1) NOT null default 0,
  auto_calculate_price int(1) not null default 1,
  weight decimal(4,2) not null default 0,
  auto_calculate_weight int(1) NOT NULL DEFAULT '1',
  free_shipping int(1) NOT NULL DEFAULT '0',
  notification_date DATETIME  NULL,
  quotation_ref varchar(25) NOT NULL,
  promo_id int(11) null,
  PRIMARY KEY (`quotation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('quotation_items')};
CREATE TABLE {$this->getTable('quotation_items')} (
  `quotation_item_id` int(11) unsigned NOT NULL auto_increment,
  `quotation_id` int(11) unsigned NOT NULL,
  `order` int not null,
  `product_id` varchar(20) NULL,
  `qty` int not null default 1,
  discount_purcent decimal(4,2) NOT NULL default 0,
  discount_amount decimal(4,2) NOT NULL default 0,
  display_in_front int(0) NOT NULL default 1,
  price_ht decimal(6,2) not null default 0,
  caption varchar(255) not null,
  eco_tax decimal(6,2 ) NOT NULL DEFAULT '0',
  weight decimal(6,2) not null default 0,
  `exclude` TINYINT NOT NULL DEFAULT '0',
  `name` TINYINT NOT NULL DEFAULT '0',
  cost decimal(6,2) NULL DEFAULT 0,
  sku varchar(20),
  PRIMARY KEY (`quotation_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('quotation_billing')};
CREATE TABLE  {$this->getTable('quotation_billing')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `quotation_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `hearabout` varchar(255) NOT NULL,
  `repid` varchar(255) NOT NULL,
  `street1` varchar(255) NOT NULL,
  `street2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `region_id` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `country_id` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS {$this->getTable('quotation_shipping')};
CREATE TABLE {$this->getTable('quotation_shipping')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `quotation_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `hearabout` varchar(255) NOT NULL,
  `repid` varchar(255) NOT NULL,
  `street1` varchar(255) NOT NULL,
  `street2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `region_id` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `country_id` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `inhand1` date NOT NULL,
  `inhand` date NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

    ");
 
$installer->endSetup();