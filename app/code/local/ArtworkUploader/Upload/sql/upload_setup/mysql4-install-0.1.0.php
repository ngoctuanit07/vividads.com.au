<?php

$installer = $this;

$installer->startSetup();

$installer->run("

 DROP TABLE IF EXISTS {$this->getTable('proofs')};
CREATE TABLE {$this->getTable('proofs')} (
   `entity_id`     int AUTO_INCREMENT NOT NULL,
  `store_id`      int NOT NULL,
  `order_id`      int NOT NULL,
  `customer_id`   int NOT NULL,
  `item_id`       int NOT NULL,
  `file`          varchar(255) NOT NULL,
  `quantity`      int NOT NULL,
  `status`        varchar(255) NOT NULL,
  `comment`       text NOT NULL,
  `postdate`      datetime NOT NULL,
  `approve_date`  datetime NOT NULL,
  `proof_type`    varchar(255) NOT NULL,
  `artwork`       varchar(255) NOT NULL,
  /* Keys */
  PRIMARY KEY (`entity_id`)
) ENGINE = InnoDB;

 DROP TABLE IF EXISTS {$this->getTable('task_designer')};
CREATE TABLE {$this->getTable('task_designer')} (
    `entity_id`        int AUTO_INCREMENT NOT NULL,
  `store_id`           int NOT NULL,
  `order_quote_id`     int NOT NULL,
  `user_id`            int NOT NULL,
  `user_type`          varchar(255) NOT NULL,
  `item_id`            int NOT NULL,
  `file`               varchar(255) NOT NULL,
  `status`             varchar(255) NOT NULL,
  `comment`            text NOT NULL,
  `postdate`           datetime NOT NULL,
  `approve_date`       datetime NOT NULL,
  `proof_type`         varchar(255) NOT NULL,
  `file_size`          float,
  `file_type`          varchar(20),
  `approved_status`    int DEFAULT '0',
  `quantity`           int DEFAULT '0',
  `signature`          varchar(500) DEFAULT 'none',
  `reason`             text,
  `total_ordered_qty`  int,
  /* Keys */
  PRIMARY KEY (`entity_id`)
) ENGINE = InnoDB;
SET FOREIGN_KEY_CHECKS=1;
    ");

$installer->endSetup(); 