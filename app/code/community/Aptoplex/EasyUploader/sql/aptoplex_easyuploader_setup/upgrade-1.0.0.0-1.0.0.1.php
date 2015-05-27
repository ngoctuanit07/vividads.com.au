<?php
/**
 * Aptoplex Easy Uploader
 * Database table upgrade script
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 *
 * @var $installer Mage_Core_Model_Resource_Setup
 */

$installer = $this;

$installer->startSetup();

/**
 * BACKWARDS COMPATIBILITY FIX for pre v1.0.0.1 versions.
 *
 * Prepends the path component 'media' to Easy Uploader's 'Uploaded Files Path' store config setting.
 *
 * We need this because previous versions of Easy Uploader assumed that whatever path was specified in the
 * 'Uploaded Files Path' was ALWAYS going to be inside of the 'media' directory. This is no longer a requirement
 * as the uploads directory can now reside anywhere in your Magento installation directory.
 *
 * NOTE: The fix below will only be applied if the current 'Uploaded Files Path' is still at it's factory default
 * setting and hasn't been changed since the module's initial installation.
 */
$currentUploadedFilesPath = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_UPLOAD_PATH, null);
if ($currentUploadedFilesPath === Aptoplex_EasyUploader_Helper_Data::DEFAULT_UPLOAD_PATH) {
    $installer->setConfigData(Aptoplex_EasyUploader_Helper_Data::XML_PATH_UPLOAD_PATH, 'media' . DS . $currentUploadedFilesPath);
}

$installer->endSetup();