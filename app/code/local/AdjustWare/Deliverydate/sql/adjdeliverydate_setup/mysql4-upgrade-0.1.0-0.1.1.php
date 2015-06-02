<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     5aaeipF7ooGEnIcLG9rWordPqCAsU9nCJvGBjy56tC
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
{
    $installer = $this;

    $typeId = Mage::getModel('eav/entity_type')->loadByCode('order')->getEntityTypeId();

    $installer->startSetup();

    $installer->run("
    DELETE FROM `{$this->getTable('eav/attribute')}` WHERE `attribute_code`='delivery_comment' limit 1;

    INSERT INTO `{$this->getTable('eav/attribute')}` (entity_type_id, attribute_code, backend_type, frontend_input, frontend_label) VALUES ($typeId, 'delivery_comment', 'text', 'text', 'Delivery Comment');
    ");

    $installer->endSetup();
}
elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
{

    /**
    * Will use this function to flush cache storage
    */
    function emptyDir($dirname = null)
    {
        if(!is_null($dirname)) {
            if (is_dir($dirname)) {
                if ($handle = @opendir($dirname)) {
                    while (($file = readdir($handle)) !== false) {
                        if ($file != "." && $file != "..") {
                            $fullpath = $dirname . '/' . $file;
                            if (is_dir($fullpath)) {
                                emptyDir($fullpath);
                                @rmdir($fullpath);
                            }
                            else {
                                @unlink($fullpath);
                            }
                        }
                    }
                    closedir($handle);
                }
            }
        }
    }

    $installer = $this;

    $installer->startSetup();


    $attr = array(
        'input'    => 'text',
        'type'     => 'text',
        'grid'     => true,
        'label'    => 'Delivery Comment',
    );
    $installer->addAttribute('order', 'delivery_comment', $attr);


    /*
    $installer->run("
    DELETE FROM `{$this->getTable('eav/attribute')}` WHERE `attribute_code`='delivery_comment' limit 1;

    INSERT INTO `{$this->getTable('eav/attribute')}` (entity_type_id, attribute_code, backend_type, frontend_input, frontend_label) VALUES ($typeId, 'delivery_comment', 'text', 'text', 'Delivery Comment');
    ");
    */


    /**
    * Need to fluch cache storage now
    */
    emptyDir(Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);


    $installer->endSetup(); 
}