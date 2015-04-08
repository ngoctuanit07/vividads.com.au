<?php

$this->startSetup()
->run("ALTER TABLE {$this->getTable('testimonial')}
    ADD COLUMN `media` varchar(500) NOT NULL default ''")
->endSetup();