<?php

$this->startSetup()
->run("ALTER TABLE {$this->getTable('testimonial')}
    ADD COLUMN `media_url` varchar(500) NOT NULL default ''")
->endSetup();