<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('permissions')};
CREATE TABLE {$this->getTable('permissions')} (
  `permissions_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`permissions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('user_task_relation')};
CREATE TABLE IF NOT EXISTS {$this->getTable('user_task_relation')} (
  `task_rel_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  PRIMARY KEY (`task_rel_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('permission_task_list')};
CREATE TABLE IF NOT EXISTS {$this->getTable('permission_task_list')} (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(255) NOT NULL,
  `task_status` int(11) NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO {$this->getTable('permission_task_list')} (`task_id`, `task_name`, `task_status`) VALUES
(1, 'Able to see tasks for following user', 1),
(2, 'Able to view following calanders', 1),
(3, 'Able to force mark a day a holiday', 1),
(4, 'Able to force unmark a day a not a holiday', 1),
(5, 'Able to add tasks on calander of following members', 1),
(6, 'Able to delete task for following members', 1),
(7, ' Able to mark task as complete for folloing members', 1),
(8, 'Able to edit author', 1),
(9, 'Able to edit taget user', 1),
(10, 'Able to edit caption', 1),
(11, 'Able to view caption', 1),
(12, 'Able to view description', 1),
(13, 'Able to edit description', 1),
(14, 'Able to edit notification date', 1),
(15, 'Able to view notification date', 1),
(16, 'Able to view deadline', 1),
(17, 'Able to edit deadline', 1),
(18, 'Able to view priority', 1),
(19, 'Able to edit priority', 1),
(20, 'Able to view mark as complete', 1),
(22, 'Can view Day wish', 1),
(23, 'Can view Week wish', 1),
(24, 'Can view 4 Days wish', 1),
(25, 'Can view Month wish', 1),
(26, 'Can view Year wish', 1),
(27, 'Able to see ''My Calendar''', 1),
(28, 'Able to see ''Other Calendar''', 1),
(29, 'Able to see Todo list', 1),
(30, 'Able to see World clock', 1),
(31, 'Able to see Small Calendar', 1);
SET FOREIGN_KEY_CHECKS=1;

    ");

$installer->endSetup(); 