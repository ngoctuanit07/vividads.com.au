<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('eventcalendar')};
CREATE TABLE {$this->getTable('eventcalendar')} (
  `eventcalendar_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`eventcalendar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



        
        DROP TABLE IF EXISTS {$this->getTable('holiday')};
        CREATE TABLE IF NOT EXISTS {$this->getTable('holiday')} (
        `entity_id` int(11) NOT NULL AUTO_INCREMENT,
        `country_name` varchar(255) NOT NULL,
        `h_date` date NOT NULL,
        `event` varchar(255) NOT NULL,
        `color` varchar(255) NOT NULL,
        `addby` varchar(255) NOT NULL,
        PRIMARY KEY (`entity_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
      
      DROP TABLE IF EXISTS {$this->getTable('todolist')};
        CREATE TABLE IF NOT EXISTS {$this->getTable('todolist')} (
          `entity_id` int(11) NOT NULL AUTO_INCREMENT,
          `ot_id` int(11) NOT NULL,
          `user_id` int(11) NOT NULL,
          `list_id` int(11) NOT NULL,
          `complete` int(11) NOT NULL,
          PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
        
        
        DROP TABLE IF EXISTS {$this->getTable('todotasklist')};
        CREATE TABLE IF NOT EXISTS {$this->getTable('todotasklist')} (
          `list_id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `user_id` int(11) NOT NULL,
          `active` int(11) NOT NULL,
          PRIMARY KEY (`list_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
        
        DROP TABLE IF EXISTS {$this->getTable('clock')};
        CREATE TABLE IF NOT EXISTS {$this->getTable('clock')} (
          `user_id` int(11) NOT NULL,
          `clock_id` varchar(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        
        
        
        
        DROP TABLE IF EXISTS {$this->getTable('task_chain')};
        CREATE TABLE IF NOT EXISTS {$this->getTable('task_chain')} (
          `entity_id` int(11) NOT NULL AUTO_INCREMENT,
          `task_id` int(11) NOT NULL,
          `order_quote_id` int(11) NOT NULL,
          `product_id` int(11) NOT NULL,
          `task_type` varchar(255) NOT NULL,
          `task_status` varchar(255) NOT NULL,
          PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        
        
        

    ");

$installer->run("
ALTER TABLE  {$this->getTable('sales_flat_order')} ADD  `is_event_set_to_google_calendar` INT( 1 ) NOT NULL DEFAULT  '0' COMMENT '0=>If not sent to google calendar event, 1 => If already sent to google calendar event'

    ");

$installer->endSetup(); 