<?php
/**
 * Aptoplex Easy Uploader
 * Database table install script
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 *
 * @var $installer Mage_Core_Model_Resource_Setup
 */

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('aptoplex_easyuploader/upload'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true
    ), 'Entity id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Order Id')
    ->addColumn('uploaded_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false,
    ), 'Uploaded at')
    ->addColumn('original_filename', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false
    ), 'Original Filename')
    ->addColumn('new_filename', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false
    ), 'New Filename')
    ->addColumn('file_path', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false
    ), 'File Path')
    ->addColumn('additional_comments', Varien_Db_Ddl_Table::TYPE_TEXT, 4096, array(
        'nullable' => true
    ), 'Additional Comments')
    ->addColumn('email_address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false
    ), 'Email Address')
    ->addColumn('ip_address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 39, array(
        'nullable' => false
    ), 'Ip Address')
    ->addIndex($installer->getIdxName(
            $installer->getTable('aptoplex_easyuploader/upload'),
            array('uploaded_at'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('uploaded_at'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->setComment('Upload');

$table->setOption('type', 'InnoDB');
$table->setOption('charset', 'utf8');

if (!$installer->tableExists('aptoplex_easyuploader/upload')) {
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();