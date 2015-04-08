<?php

/**
 * MD_Quotemail.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Quotemail
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */

$installer = $this;

$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('quotemail')};
    CREATE TABLE {$this->getTable('quotemail')} (
      `quotemail_id` int(11) unsigned NOT NULL auto_increment,
      `category_id` int(6) NOT NULL, 
      `title` varchar(255) NOT NULL default '',
      `filename` varchar(255) NOT NULL default '',
      `url` varchar(255) NOT NULL default '',
      `status` smallint(6) NOT NULL default '0',
      PRIMARY KEY (`quotemail_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('quotemail_store')};
    CREATE TABLE {$this->getTable('quotemail_store')} (
      `quotemail_id` int(11) unsigned NOT NULL,
      `store_id` int(6) NOT NULL  
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
    
$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('quotemail_category')};
    CREATE TABLE {$this->getTable('quotemail_category')} (
      `category_id` int(11) unsigned NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `status` smallint(6) NOT NULL default '0',
      PRIMARY KEY (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
    

$installer->endSetup(); 
