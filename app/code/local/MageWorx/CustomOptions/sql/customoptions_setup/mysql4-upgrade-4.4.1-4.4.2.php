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
 * @package    MageWorx_CustomOptions
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Advanced Product Options extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomOptions
 * @author     MageWorx Dev Team
 */

/* @var $installer MageWorx_CustomOptions_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

if (!$installer->getConnection()->tableColumnExists($installer->getTable('customoptions/group'), 'update_inventory')) {    
    $installer->getConnection()->addColumn(
        $installer->getTable('customoptions/group'),
        'update_inventory',
        "TINYINT (1) NOT NULL DEFAULT 0"
    );
}

// fix and clean up the debris of tables whith options
$installer->run("
    DELETE t1 FROM `{$installer->getTable('catalog_product_option')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_entity')}` WHERE `entity_id` = t1.`product_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('catalog_product_option_title')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option')}` WHERE `option_id` = t1.`option_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('catalog_product_option_price')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option')}` WHERE `option_id` = t1.`option_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('catalog_product_option_type_value')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option')}` WHERE `option_id` = t1.`option_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('catalog_product_option_type_title')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option_type_value')}` WHERE `option_type_id` = t1.`option_type_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('catalog_product_option_type_price')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option_type_value')}` WHERE `option_type_id` = t1.`option_type_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('customoptions/option_type_image')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option_type_value')}` WHERE `option_type_id` = t1.`option_type_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('custom_options_relation')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option')}` WHERE `option_id` = t1.`option_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('customoptions/option_view_mode')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option')}` WHERE `option_id` = t1.`option_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('customoptions/option_description')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option')}` WHERE `option_id` = t1.`option_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('customoptions/option_default')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option')}` WHERE `option_id` = t1.`option_id`) = 0;
    DELETE t1 FROM `{$installer->getTable('customoptions/option_type_tier_price')}` AS t1 WHERE (SELECT COUNT(*) FROM `{$installer->getTable('catalog_product_option_type_price')}` WHERE `option_type_price_id` = t1.`option_type_price_id`) = 0;
"); 
    
//$attribute = $this->getAttribute('catalog_product', 'options_container');
//if (!empty($attribute['attribute_id'])) {
//    $this->run("INSERT INTO `{$this->getTable('catalog_product_entity_varchar')}` (`entity_type_id`, `attribute_id`, `entity_id`, `value`) SELECT '{$attribute['entity_type_id']}' AS entity_type_id, '{$attribute['attribute_id']}' AS attribute_id, `entity_id`, 'container1' AS value FROM `{$this->getTable('catalog_product_entity')}` WHERE entity_id IN (SELECT entity_id FROM {$this->getTable('catalog_product_entity')}) ON DUPLICATE KEY UPDATE `value` = 'container1';");
//}

$installer->endSetup();