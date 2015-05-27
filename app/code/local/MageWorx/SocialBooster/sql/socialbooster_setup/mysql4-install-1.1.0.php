<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Social Booster extension
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @author     MageWorx Dev Team
 */

$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('socialbooster_bookmark')};
CREATE TABLE IF NOT EXISTS {$this->getTable('socialbooster_bookmark')} ( 
  `bookmark_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `bookmark_code` varchar(20) NOT NULL DEFAULT '',
  `bookmark_title` varchar(50) NOT NULL DEFAULT '',
  `clicks` mediumint(8) NOT NULL DEFAULT '0',
  `last_click` datetime DEFAULT NULL,
  PRIMARY KEY (`bookmark_id`),
  UNIQUE KEY `bookmark_name` (`bookmark_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SocialBooster Bookmarks';



INSERT IGNORE INTO {$this->getTable('socialbooster_bookmark')} (`bookmark_id`, `bookmark_code`, `bookmark_title`, `clicks`, `last_click`) VALUES
(1, '100zakladok', '100 bookmarks (Russian)', 0, NULL),
(2, 'bebo', 'Bebo', 0, NULL),
(3, 'bitacoras', 'Bitacoras (Spanish)', 0, NULL),
(4, 'blinklist', 'Blinklist', 0, NULL),
(5, 'blogengage', 'BlogEngage', 0, NULL),
(6, 'blogger', 'Blogger', 0, NULL),
(7, 'blogmarks', 'BlogMarks', 0, NULL),
(8, 'blogospherenews', 'Blogosphere News', 0, NULL),
(9, 'bobrdobr', 'BobrDobr (Russian)', 0, NULL),
(10, 'current', 'Current', 0, NULL),
(11, 'delicious', 'Delicious', 0, NULL),
(12, 'designbump', 'Design Bump', 0, NULL),
(13, 'designfloat', 'DesignFloat', 0, NULL),
(14, 'designmoo', 'DesignMoo', 0, NULL),
(15, 'devmarks', 'Devmarks', 0, NULL),
(16, 'digg', 'Digg', 0, NULL),
(17, 'diigo', 'Diigo', 0, NULL),
(18, 'ekudos', 'eKudos (Dutch)', 0, NULL),
(19, 'evernote', 'Evernote', 0, NULL),
(20, 'facebook', 'Facebook', 0, NULL),
(21, 'faqpal', 'FAQpal', 0, NULL),
(22, 'fleck', 'Fleck', 0, NULL),
(23, 'friendfeed', 'FriendFeed', 0, NULL),
(24, 'fwisp', 'Fwisp', 0, NULL),
(25, 'globalgrind', 'Global Grind', 0, NULL),
(26, 'google', 'Google Bookmarks', 0, NULL),
(27, 'googlebuzz', 'Google Buzz', 0, NULL),
(28, 'googleplusone', 'Google +1', 0, NULL),
(29, 'hackernews', 'Hacker News', 0, NULL),
(30, 'hatena', 'Hatena Bookmarks (Japanese)', 0, NULL),
(31, 'hyves', 'Hyves', 0, NULL),
(32, 'identica', 'Identica', 0, NULL),
(33, 'izeby', 'Izeby', 0, NULL),
(34, 'jumptags', 'JumpTags', 0, NULL),
(35, 'linkedin', 'Linkedin', 0, NULL),
(36, 'memoryru', 'Memory.ru (Russian)', 0, NULL),
(37, 'meneame', 'Meneame (Spanish)', 0, NULL),
(38, 'misterwong', 'Mister Wong', 0, NULL),
(39, 'mixx', 'Mixx', 0, NULL),
(40, 'moemesto', 'MyPlace (Russian)', 0, NULL),
(41, 'mylinkvault', 'MyLinkVault', 0, NULL),
(42, 'myspace', 'MySpace', 0, NULL),
(43, 'n4g', 'N4G', 0, NULL),
(44, 'netvibes', 'Netvibes', 0, NULL),
(45, 'netvouz', 'Netvouz', 0, NULL),
(46, 'newsvine', 'Newsvine', 0, NULL),
(47, 'ning', 'Ning', 0, NULL),
(48, 'nujij', 'NUjij (Dutch)', 0, NULL),
(49, 'orkut', 'Orkut', 0, NULL),
(50, 'pfbuzz', 'PFBuzz', 0, NULL),
(51, 'pingfm', 'Ping.fm', 0, NULL),
(52, 'plurk', 'Plurk', 0, NULL),
(53, 'posterous', 'Posterous', 0, NULL),
(54, 'printfriendly', 'Print Friendly', 0, NULL),
(55, 'propeller', 'Propeller', 0, NULL),
(56, 'pusha', 'Pusha (Swedish)', 0, NULL),
(57, 'reddit', 'Reddit', 0, NULL),
(58, 'scriptstyle', 'Script &amp; Style', 0, NULL),
(59, 'slashdot', 'SlashDot', 0, NULL),
(60, 'sphinn', 'Sphinn', 0, NULL),
(61, 'squidoo', 'Squidoo', 0, NULL),
(62, 'strands', 'Strands', 0, NULL),
(63, 'stumbleupon', 'StumbleUpon', 0, NULL),
(64, 'stumpedia', 'Stumpedia', 0, NULL),
(65, 'techmeme', 'TechMeme', 0, NULL),
(66, 'technorati', 'Technorati', 0, NULL),
(67, 'tipd', 'Tipd', 0, NULL),
(68, 'tumblr', 'Tumblr', 0, NULL),
(69, 'twitter', 'Twitter', 0, NULL),
(70, 'webblend', 'Web Blend', 0, NULL),
(71, 'wikio', 'Wikio', 0, NULL),
(72, 'wykop', 'Wykop (Polish)', 0, NULL),
(73, 'xerpi', 'Xerpi', 0, NULL),
(74, 'yandex', 'Yandex.Bookmarks (Russian)', 0, NULL);



-- DROP TABLE IF EXISTS {$this->getTable('socialbooster_counter')};
CREATE TABLE IF NOT EXISTS {$this->getTable('socialbooster_counter')} ( 
  `counter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bookmark_id` tinyint(3) unsigned NOT NULL,
  `url` varchar(1024) NOT NULL DEFAULT '',
  `count` smallint(5) NOT NULL DEFAULT '0',
  `last` datetime DEFAULT NULL,
  PRIMARY KEY (`counter_id`),
  UNIQUE KEY `IDX_BOOKMARK_ID_URL` (`bookmark_id`,`url`(255)),
  CONSTRAINT `FK_SOCIALBOOSTER_BOOKMARK` FOREIGN KEY (`bookmark_id`) REFERENCES {$this->getTable('socialbooster_bookmark')} (`bookmark_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SocialBooster Counters';


");

$installer->endSetup();