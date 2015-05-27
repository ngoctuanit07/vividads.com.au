<?php
  $installer = $this;
  $installer->startSetup();

  $installer->run("
   
    DROP TABLE IF EXISTS {$this->getTable('proofs')};
CREATE TABLE IF NOT EXISTS {$this->getTable('proofs')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `postdate` datetime NOT NULL,
  `approve_date` datetime NOT NULL,
  `proof_type` varchar(255) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('quote_planning')};
CREATE TABLE IF NOT EXISTS {$this->getTable('quote_planning')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `planning_type` varchar(255) NOT NULL,
  `order_placed_date` date NOT NULL,
  `artwork_date` date NOT NULL,
  `proof_date` date NOT NULL,
  `start_date` date NOT NULL,
  `shipping_date` date NOT NULL,
  `delivery_date` date NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
SET FOREIGN_KEY_CHECKS=1;
  ");

  $installer->endSetup();