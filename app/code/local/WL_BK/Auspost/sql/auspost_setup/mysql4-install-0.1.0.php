<?php

/*
  * @category       WL
  * @package        Auspost
  * @description    Add the extra field to order table which is used to store the Auspost shipping delivery options dates
 */

$installer = $this;
$installer->startSetup();

$eav = new Mage_Eav_Model_Entity_Setup('sales_setup');
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_order'), 'auspost_extra_options', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'nullable'  => true,
        'comment'   => 'Auspost Extra Options'
    ));
$eav->addAttribute('order', 'auspost_extra_options', array('type'=>'static'));
$installer->endSetup();