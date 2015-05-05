<?php
/**
 * Class Aptoplex_EasyUploader_IndexController
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Data helper
     *
     * @var Aptoplex_EasyUploader_Helper_Data
     */
    private $_helper;

    /**
     * Utility helper
     *
     * @var Aptoplex_EasyUploader_Helper_Utility
     */
    private $_utilityHelper;

    /**
     * Internal constructor
     */
    public function _construct() {
        parent::_construct();
        $this->_helper = Mage::helper('aptoplex_easyuploader/data');
        $this->_utilityHelper = Mage::helper('aptoplex_easyuploader/utility');
    }

    /**
     * Index action
     */
    public function indexAction() {
        $title = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_PAGE_TITLE, null);

        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__($title));

        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        if ($breadcrumbs !== false) {
            $breadcrumbs->addCrumb(
                "home",
                array(
                    "label" => $this->__("Home Page"),
                    "title" => $this->__("Home Page"),
                    "link" => Mage::getBaseUrl()
                )
            );

            $breadcrumbs->addCrumb(
                "upload your files",
                array(
                    "label" => $this->__($title),
                    "title" => $this->__($title)
                )
            );
        }

        $this->renderLayout();
    }

    /**
     * Check order action
     */
    public function checkOrderAction() {
        $data = $this->getRequest()->getPost();

        $orderNumber = $this->_helper->stripTags($data['order-number'], null, true);
        $emailAddress = $this->_helper->stripTags($data['email-address'], null, true);

        $orderCheckResult = $this->_helper->checkOrderValidity($orderNumber, $emailAddress);
        $responseMessage = $this->_helper->getResponseMessage($orderCheckResult);

        echo json_encode($responseMessage);
    }

    /**
     * Upload action
     */
    public function uploadAction() {
        $postData = $this->getRequest()->getPost();

        $orderNumber = $this->_helper->stripTags($postData['order-number'], null, true);
        $emailAddress = $this->_helper->stripTags($postData['email-address'], null, true);
        $additionalComments = $this->_helper->stripTags($postData['additional-comments'], null, true);

        // TODO: need to check if stripping the tags from the filename might remove characters that we might need...
        // EDIT - I think we're ok.
        $filename = $this->_helper->stripTags($postData['name'], null, true);

        $additionalData = array(
            'order-number' => $orderNumber,
            'email-address' => $emailAddress,
            'additional-comments' => $additionalComments
        );

        $chunkNum = 0;
        if (isset($postData['chunk'])) {
            $chunkNum = $this->_helper->stripTags($postData['chunk'], null, true);
        }
        $chunkCount = 1;
        if (isset($postData['chunks'])) {
            $chunkCount = $this->_helper->stripTags($postData['chunks'], null, true);
        }

        if ($chunkCount > 1) {
            $additionalData['chunk-number'] = $chunkNum;
            $additionalData['chunk-count'] = $chunkCount;

            // TODO: file integrity check after all file chunks have been assembled?

            if ($this->_helper->saveFileChunk($filename, $additionalData)) {
                echo json_encode($this->_helper->getResponseMessage(Aptoplex_EasyUploader_Helper_Data::OK));
            }
            else {
                echo json_encode($this->_helper->getResponseMessage(Aptoplex_EasyUploader_Helper_Data::FILE_SAVE_FAILED));
            }
        }
        else {
            if ($this->_helper->checkFileIntegrity($filename)) {
                if ($this->_helper->saveFile($filename, $additionalData)) {
                    echo json_encode($this->_helper->getResponseMessage(Aptoplex_EasyUploader_Helper_Data::OK));
                }
                else {
                    echo json_encode($this->_helper->getResponseMessage(Aptoplex_EasyUploader_Helper_Data::FILE_SAVE_FAILED));
                }
            }
            else {
                echo json_encode($this->_helper->getResponseMessage(Aptoplex_EasyUploader_Helper_Data::FILE_INTEGRITY_CHECK_FAILED));
            }
        }
    }

    /**
     * Called when the upload queue completes.
     */
    public function uploadQueueDidCompleteAction() {
        $data = $this->getRequest()->getPost();
        $emailData = array(
            'order_number'      => $this->_helper->stripTags($data['order-number'], null, true),
            'customer_email'    => $this->_helper->stripTags($data['email-address'], null, true)
        );
        $this->_helper->sendNotificationEmail($emailData);
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
}