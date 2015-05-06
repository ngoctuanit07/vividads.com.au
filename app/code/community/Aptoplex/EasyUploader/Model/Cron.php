<?php
/**
 * Class Aptoplex_EasyUploader_Model_Cron
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Cron {

    protected $helper;

    /**
     * Internal constructor
     */
    public function __construct() {
        $this->helper = Mage::helper('aptoplex_easyuploader');
    }

    /**
     * Clears out the uploads and remove entries from the database if we're in DEMO mode and
     * FLUSH_UPLOADS_IN_DEMO_MODE in 'app/code/community/Aptoplex/EasyUploader/Helper/Data.php' is true.
     */
    public function flushUploads() {
        if (Aptoplex_EasyUploader_Helper_Data::RUN_IN_DEMO_MODE && Aptoplex_EasyUploader_Helper_Data::FLUSH_UPLOADS_IN_DEMO_MODE) {
            $collection = Mage::getResourceModel('aptoplex_easyuploader/upload_collection');
            $data = $collection->getData();

            foreach ($data as $obj) {
                $model = Mage::getModel('aptoplex_easyuploader/upload');
                $model->load($obj['entity_id']);
                $filename = $model->getData('new_filename');
                $filepath = $model->getData('file_path');

                if (is_file($filepath . $filename) && is_readable($filepath . $filename)) {
                    unlink($filepath . $filename);
                    @rmdir($filepath);
                    $model->delete();
                }
            }
        }
    }
}