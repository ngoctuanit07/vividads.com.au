<?php
/**
 * Email Template Manager
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitemails
 * @version      2.0.13
 * @license:     POwWoRanFs2vVHb6wy050abrHO3ftlyCeMSf01dXU3
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_aitemails_attachment')} (
  `attachment_id` int(10) unsigned NOT NULL auto_increment,
  `template_id` int(10) unsigned NOT NULL default '0',
  `attachment_file` varchar(255) NOT NULL default '',
  `attachment_url` varchar(255) NOT NULL default '',
  `attachment_type` varchar(20) NOT NULL default '',
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attachment_id`),
  KEY `AITEMAILS_TEMPLATE` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE {$this->getTable('aitoc_aitemails_attachment_title')} (
  `title_id` int(10) unsigned NOT NULL auto_increment,
  `attachment_id` int(10) unsigned NOT NULL default '0',
  `store_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`title_id`),
  KEY `AITEMAILS_ATTACHMENT` (`attachment_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

");

$installer->endSetup();