<?php

/**
 * MD_Vividslider.
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
 * @package    Vividslider
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */

$installer = $this;

$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('vividslider')};
    CREATE TABLE IF NOT EXISTS `vividslider` (
  `vividslider_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category_id` int(6) NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `pageurl` varchar(255) NOT NULL,
  `store_id` int(11) NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `hlink` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`vividslider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('vividslider_store')};
    CREATE TABLE {$this->getTable('vividslider_store')} (
      `vividslider_id` int(11) unsigned NOT NULL,
      `store_id` int(6) NOT NULL  
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
    
$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('vividslider_files')};
    CREATE TABLE IF NOT EXISTS `vividslider_files` (
  `slider_file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vividslider_id` int(11) NOT NULL,
  `slider_file` varchar(100) NOT NULL,
  `slider_file_title` varchar(255) NOT NULL,
  `slider_url` varchar(255) NOT NULL,
  PRIMARY KEY (`slider_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");
    

$installer->endSetup(); 
