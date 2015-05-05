<?php
/**
 * Class Aptoplex_EasyUploader_Adminhtml_Easyuploader_UploadController
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Adminhtml_Easyuploader_UploadController extends Mage_Adminhtml_Controller_Action {

    protected $_helper;

    /**
     * Internal constructor
     */
    public function _construct() {
        parent::_construct();
        $this->_helper = Mage::helper('aptoplex_easyuploader');
    }

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Called when a row's 'Download' link is clicked
     *
     * @return $this|bool|Mage_Core_Controller_Varien_Action
     */
    public function downloadAction() {
        // Get the Upload model.
        $upload = Mage::getModel('aptoplex_easyuploader/upload');

        // Load in the data corresponding to the passed in entity_id.
        $upload->load($this->getRequest()->getParam('entity_id'));

        // Get the full file path.
        $filePath = $upload->getData('file_path') . $upload->getData('new_filename');

        if (is_file($filePath) && is_readable($filePath)) {
            try {
                $this->getResponse()
                    ->setHttpResponseCode (200)
                    ->setHeader ('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                    ->setHeader ('Pragma', 'public', true)
                    ->setHeader ('Content-type', 'application/force-download')
                    ->setHeader ('Content-Length', filesize($filePath))
                    ->setHeader ('Content-Disposition', 'attachment' . '; filename=' . basename($filePath));
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                readfile($filePath);

                exit;
            }
            catch (Exception $e) {
                return false;
            }
            return true;
        }
        else {
            return $this->_redirect('*/*/'); 
        }
    }

    public function massDeleteAction() {
        $entityIds = $this->getRequest()->getParam('entity_ids');
        foreach($entityIds as $entityId) {
            $model = Mage::getModel('aptoplex_easyuploader/upload');
            $model->load($entityId);
            $filename = $model->getData('new_filename');
            $filepath = $model->getData('file_path');

            if (is_file($filepath . $filename) && is_readable($filepath . $filename)) {

                // Delete the file.
                unlink($filepath . $filename);

                // Delete the directory if empty.
                @rmdir($filepath);
            }

            // Delete entry from the database.
            $model->delete();
        }

        return $this->_redirect('*/*/');
    }

    protected function _isAllowed() {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'download':
                $aclResource = 'aptoplex_easyuploader/upload/download';
                break;
            case 'delete':
                $aclResource = 'aptoplex_easyuploader/upload/delete';
                break;
            default:
                $aclResource = 'aptoplex_easyuploader/upload';
                break;
        }

        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aptoplex_easyuploader/adminhtml_upload_grid')->toHtml()
        );
    }
}