<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT


DROP table if EXISTS reviewsplus;
CREATE TABLE `reviewsplus` (
	  `vote_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `customer_id` int(10) unsigned NOT NULL,
	  `review_id` int(20) unsigned NOT NULL,
	  `votes` int(1) NOT NULL,
	  
	  PRIMARY KEY (`vote_id`),
	  
	  CONSTRAINT `reviewsplus_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=latin1;



	
	alter table review_detail
		add(
		review_reply text
		
		);

	alter table review
			add(
			votes int(10) not null default '0'
			);




		
SQLTEXT;

$installer->run($sql);
 
$installer->endSetup();
	 