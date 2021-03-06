<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('vendor')};
CREATE TABLE {$this->getTable('vendor')} (
  `vendor_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('vendor_item')};
CREATE TABLE IF NOT EXISTS {$this->getTable('vendor_item')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `target_user` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_sku` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `progress` varchar(255) NOT NULL,
  `proof_approve_date` datetime NOT NULL,
  `shipping_time` date NOT NULL,
  `file_recieved` varchar(255) NOT NULL,
  `proof_approved` varchar(255) NOT NULL,
  `file_size` double NOT NULL,
  `courier_name` varchar(255) NOT NULL,
  `con_note` varchar(255) NOT NULL,
  `shipping_carton` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('vendor_product')};
CREATE TABLE IF NOT EXISTS {$this->getTable('vendor_product')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('vendor_chat')};
CREATE TABLE IF NOT EXISTS {$this->getTable('vendor_chat')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_list_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `roll_type_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `is_read` int(11) NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('vendor_docket')};
CREATE TABLE IF NOT EXISTS {$this->getTable('vendor_docket')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `vendor_list_id` int(11) NOT NULL,
  `supplier_invoice` varchar(255) NOT NULL,
  `connote_number` varchar(255) NOT NULL,
  `carton` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('vendor_order')};
CREATE TABLE IF NOT EXISTS {$this->getTable('vendor_order')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `revision_number` int(11) NOT NULL,
  `assign_to` int(11) NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('vendor_user_print')};
CREATE TABLE IF NOT EXISTS {$this->getTable('vendor_user_print')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `print_number` int(11) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
SET FOREIGN_KEY_CHECKS=1;

DROP TABLE IF EXISTS {$this->getTable('vendor_option')};
CREATE TABLE IF NOT EXISTS {$this->getTable('vendor_option')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sub_title` varchar(255) NOT NULL,
  `sub_value` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
SET FOREIGN_KEY_CHECKS=1;


    ");

$installer->endSetup(); 