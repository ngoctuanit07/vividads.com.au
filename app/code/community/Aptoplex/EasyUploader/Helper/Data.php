<?php
/**
 * Class Aptoplex_EasyUploader_Helper_Data
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */

class Aptoplex_EasyUploader_Helper_Data extends Mage_Core_Helper_Data {

    /**
     * Array of default allowed file extensions if no setting is found in config.xml
     *
     * @var array
     */
    protected $_allowedFileExtensions                           = array('pdf', 'eps', 'tif', 'tiff', 'psd', 'ai');

    /**
     * Default file upload path if node not found in admin config
     *
     * @var string
     */
    const DEFAULT_UPLOAD_PATH                                   = 'aptoplex_easyuploader/uploads/customer';

    /**
     * Whether the module should run in demo mode (config options on the backend will not be saved etc).
     */
    const RUN_IN_DEMO_MODE                                      = false;

    /**
     * Careful with this one...
     *
     * If you accidentally set this to true when NOT running in demo mode,
     * when the cron scheduler gets run all of your uploads will then get
     * deleted from the server and any record of them in the database will
     * be removed. YOU HAVE BEEN WARNED!
     *
     * @var bool
     */
    const FLUSH_UPLOADS_IN_DEMO_MODE                            = false;

    /**
     * Utility helper class
     *
     * @var Aptoplex_EasyUploader_Helper_Utility
     */
    protected $_utilityHelper;


    /**************************
     * FRONTEND RESPONSE CODES
     **************************/

    /**
     * Ok, no errors
     *
     * @var integer
     */
    const OK                                                    = 0;

    /**
     * Order not found
     *
     * @var integer
     */
    const ORDER_NOT_FOUND                                       = 10;

    /**
     * File not saved
     *
     * @var integer
     */
    const FILE_SAVE_FAILED                                      = 20;

    /**
     * File integrity check failed
     *
     * @var integer
     */
    const FILE_INTEGRITY_CHECK_FAILED                           = 30;

    /**
     * Upload not permitted
     *
     * @var integer
     */
    const UPLOAD_NOT_PERMITTED                                  = 40;

    /**
     * Unknown error
     *
     * @var integer
     */
    const UNKNOWN_ERROR                                         = 100;


    /*******************
     * GENERAL SETTINGS
     *******************/

    /**
     * Path to store config of the URL of the uploader
     *
     * @var string
     */
    const XML_PATH_UPLOADER_URL                                 = 'aptoplex_easyuploader/general/frontend_uploader_url';

    /**
     * Path to store config of the option to add a link to the uploader in the main menu
     *
     * @var string
     */
    const XML_PATH_ADD_LINK_TO_MENU                             = 'aptoplex_easyuploader/general/add_link_to_menu';

    /**
     * Path to store config of the title of the link to the uploader that appears in the main menu
     *
     * @var string
     */
    const XML_PATH_MENU_LINK_TITLE                              = 'aptoplex_easyuploader/general/menu_link_title';

    /**
     * Path to store config of the uploader runtime fallback order
     *
     * @var string
     */
    const XML_PATH_RUNTIME_FALLBACK_ORDER                       = 'aptoplex_easyuploader/general/runtime_fallback_order';

    /**
     * Path to store config of flag which sets whether we allow uploads for selected order statuses
     *
     * @var string
     */
    const XML_PATH_UPLOADING_DEPENDS_ON_ORDER_STATUS            = 'aptoplex_easyuploader/general/uploading_depends_on_order_status';

    /**
     * Path to store config of which order statuses an order needs to be set to in order to allow file uploading
     *
     * @var string
     */
    const XML_PATH_PERMITTED_ORDER_STATUSES                     = 'aptoplex_easyuploader/general/permitted_order_statuses';

    /**
     * Path to store config of flag which allows the use of non-standard order numbers as employed by some third party modules.
     *
     * @var string
     */
    const XML_PATH_NON_STANDARD_ORDER_NUMBER_FORMAT             = 'aptoplex_easyuploader/general/non_standard_order_number_format';


    /****************
     * FILE SETTINGS
     ****************/

    /**
     * Path to store config of allowed file extensions
     *
     * @var string
     */
    const XML_PATH_ALLOWED_FILE_EXTENSIONS                      = 'aptoplex_easyuploader/file_settings/allowed_file_extensions';

    /**
     * Path to store config of minimum file size
     *
     * @var string
     */
    const XML_PATH_MIN_FILE_SIZE                                = 'aptoplex_easyuploader/file_settings/min_file_size';

    /**
     * Path to store config of maximum file size
     *
     * @var string
     */
    const XML_PATH_MAX_FILE_SIZE                                = 'aptoplex_easyuploader/file_settings/max_file_size';

    /**
     * Path to store config of file chunk size
     *
     * @var string
     */
    const XML_PATH_FILE_CHUNK_SIZE                              = 'aptoplex_easyuploader/file_settings/file_chunk_size';

    /**
     * Path to store config of the file path for uploaded files
     *
     * @var string
     */
    const XML_PATH_UPLOAD_PATH                                  = 'aptoplex_easyuploader/file_settings/upload_path';


    /*******************************
     * E-MAIL NOTIFICATION SETTINGS
     *******************************/

    /**
     * Path to store config of the e-mail notifications enable flag
     *
     * @var string
     */
    const XML_PATH_EMAIL_ENABLE                                 = 'aptoplex_easyuploader/email_notification_settings/enable';

    /**
     * Path to store config of the use custom smtp server enable flag
     *
     * @var string
     */
    const XML_PATH_EMAIL_USE_CUSTOM_SMTP_SERVER                 = 'aptoplex_easyuploader/email_notification_settings/use_custom_smtp';

    /**
     * Path to store config of the e-mail authentication type
     *
     * @var string
     */
    const XML_PATH_EMAIL_AUTHENTICATION_TYPE                    = 'aptoplex_easyuploader/email_notification_settings/authentication_type';

    /**
     * Path to store config of the e-mail username
     *
     * @var string
     */
    const XML_PATH_EMAIL_USERNAME                               = 'aptoplex_easyuploader/email_notification_settings/username';

    /**
     * Path to store config of the e-mail password
     *
     * @var string
     */
    const XML_PATH_EMAIL_PASSWORD                               = 'aptoplex_easyuploader/email_notification_settings/password';

    /**
     * Path to store config of the e-mail hostname
     *
     * @var string
     */
    const XML_PATH_EMAIL_HOST                                   = 'aptoplex_easyuploader/email_notification_settings/host';

    /**
     * Path to store config of the e-mail port number
     *
     * @var string
     */
    const XML_PATH_EMAIL_PORT                                   = 'aptoplex_easyuploader/email_notification_settings/port';

    /**
     * Path to store config of the e-mail SSL mode
     *
     * @var string
     */
    const XML_PATH_EMAIL_SSL                                    = 'aptoplex_easyuploader/email_notification_settings/ssl';

    /**
     * Path to store config of the e-mail sender name
     *
     * @var string
     */
    const XML_PATH_EMAIL_SENDER_NAME                            = 'aptoplex_easyuploader/email_notification_settings/sender_name';

    /**
     * Path to store config of the e-mail sender address
     *
     * @var string
     */
    const XML_PATH_EMAIL_SENDER_ADDRESS                         = 'aptoplex_easyuploader/email_notification_settings/sender_address';

    /**
     * Path to store config of the e-mail recipient addresses
     *
     * @var string
     */
    const XML_PATH_EMAIL_RECIPIENT_ADDRESSES                    = 'aptoplex_easyuploader/email_notification_settings/recipient_addresses';

    /**
     * Path to store config of the e-mail subject
     *
     * @var string
     */
    const XML_PATH_EMAIL_SUBJECT                                = 'aptoplex_easyuploader/email_notification_settings/subject';


    /******************************************
     * LAYOUT SETTINGS - CHECKOUT SUCCESS PAGE
     ******************************************/

    /**
     * Path to store config of the enable flag for the easy uploader section on the checkout success page
     *
     * @var string
     */
    const XML_PATH_CHECKOUT_SUCCESS_ENABLE                      = 'aptoplex_easyuploader/checkout_success_presentation/enable';

    /**
     * Path to store config of the html that renders in the easy uploader section on the checkout success page
     *
     * @var string
     */
    const XML_PATH_CHECKOUT_SUCCESS_MAIN_HTML                   = 'aptoplex_easyuploader/checkout_success_presentation/content_html';

    /**
     * Path to store config of the button title that appears in the easy uploader section on the checkout success page
     *
     * @var string
     */
    const XML_PATH_CHECKOUT_SUCCESS_UPLOAD_BTN_TITLE            = 'aptoplex_easyuploader/checkout_success_presentation/upload_button_title';

    /**
     * Path to store config of the CSS class name for the button that appears in the easy uploader section on the checkout success page
     *
     * @var string
     */
    const XML_PATH_CHECKOUT_SUCCESS_UPLOAD_BTN_CSS_CLASS_NAME   = 'aptoplex_easyuploader/checkout_success_presentation/upload_button_css_class_name';


    /***************************************
     * LAYOUT SETTINGS - EASY UPLOADER PAGE
     ***************************************/

    /**
     * Path to store config of the html that renders the page title on the frontend
     *
     * @var string
     */
    const XML_PATH_PAGE_TITLE                                   = 'aptoplex_easyuploader/frontend_presentation/page_title';

    /**
     * Path to store config of the html that renders the intro on the frontend
     *
     * @var string
     */
    const XML_PATH_INTRO_HTML                                   = 'aptoplex_easyuploader/frontend_presentation/intro_html';

    /**
     * Path to store config of the terms and conditions acceptance dropdown
     *
     * @var string
     */
    const XML_PATH_TERMS_AND_CONDITIONS_ACCEPTANCE              = 'aptoplex_easyuploader/frontend_presentation/terms_and_conditions_acceptance';

    /**
     * Path to store config of the html that renders the terms and conditions on the frontend
     *
     * @var string
     */
    const XML_PATH_TERMS_AND_CONDITIONS_HTML                    = 'aptoplex_easyuploader/frontend_presentation/terms_and_conditions_html';

    /**
     * Path to store config of the label text next to the terms and conditions checkbox
     *
     * @var string
     */
    const XML_PATH_TERMS_AND_CONDITIONS_CHECKBOX_LABEL          = 'aptoplex_easyuploader/frontend_presentation/terms_and_conditions_acceptance_checkbox_label';

    /**
     * Path to store config of the html that renders the header inside the file upload queue on the frontend
     *
     * @var string
     */
    const XML_PATH_FILE_UPLOAD_QUEUE_HEADER_HTML                = 'aptoplex_easyuploader/frontend_presentation/file_upload_queue_header_html';

    /**
     * Path to store config of the html that renders the uploader initialisation failure message on the frontend
     *
     * @var string
     */
    const XML_PATH_UPLOADER_INIT_FAILURE_HTML                   = 'aptoplex_easyuploader/frontend_presentation/uploader_init_failure_html';

    /**
     * Path to store config of the html that renders the footer inside the file uploader queue on the frontend
     *
     * @var string
     */
    const XML_PATH_FILE_UPLOAD_QUEUE_FOOTER_HTML                = 'aptoplex_easyuploader/frontend_presentation/file_upload_queue_footer_html';

    /**
     * Path to store config of the button title that appears under the upload queue on the frontend
     *
     * @var string
     */
    const XML_PATH_UPLOAD_BTN_TITLE                             = 'aptoplex_easyuploader/frontend_presentation/upload_button_title';

    /**
     * Path to store config of the CSS class name for the button that appears under the upload queue on the frontend
     *
     * @var string
     */
    const XML_PATH_UPLOAD_BTN_CSS_CLASS_NAME                    = 'aptoplex_easyuploader/frontend_presentation/upload_button_css_class_name';



    /**
     * Internal constructor
     */
    public function __construct() {
        $this->_utilityHelper = Mage::helper('aptoplex_easyuploader/utility');
    }

    /**
     * Checks for a valid order
     *
     * @param int $orderNumber
     * @param string $emailAddress
     * @return mixed
     */
    public function checkOrderValidity($orderNumber, $emailAddress) {

      $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
        ///adding for quote as well
	  if(count($order->getData()) == 0 ){
		$my_order = Mage::getModel('Quotation/Quotation')->getCollection()
									->addFieldToFilter('increment_id', $orderNumber);
					$my_order_data = $my_order->getData();	
					$customer = Mage::getModel('customer/customer')->load($my_order_data[0]['customer_id']);
					$customerEmail = $customer->getEmail();			
			}else{
				$customerEmail = $order->getCustomerEmail();
				}
		
        $canUpload = true;
        if (Mage::getStoreConfig(self::XML_PATH_UPLOADING_DEPENDS_ON_ORDER_STATUS, null)) {
            $canUpload = $this->checkOrderStatus($orderNumber);
        }

        if (isset($order)) {
            if ($customerEmail == $emailAddress) {
                if ($canUpload) {
                    return self::OK;
                }
                else {
                    return self::UPLOAD_NOT_PERMITTED;
                }
            }
        }
        else {
            return self::ORDER_NOT_FOUND;
        }

        return self::ORDER_NOT_FOUND;
    }

    /**
     * Checks the status of an order
     *
     * @param int $orderNumber
     * @return bool 
     */   
    public function checkOrderStatus($orderNumber) {

        $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
        $orderStatus = $order->getStatus();
		
        $foo = Mage::getStoreConfig(self::XML_PATH_PERMITTED_ORDER_STATUSES, null);
        $orderStatuses = explode(',', $foo);

        return (array_search($orderStatus, $orderStatuses) !== false) ? true : false;
    }

    /**
     * Returns an array of allowed file extensions
     *
     * @return array|mixed
     */
    private function getAllowedFileExtensions() {

        $extensions = Mage::getStoreConfig(self::XML_PATH_ALLOWED_FILE_EXTENSIONS, null);

        if (isset($extensions)) {
            $temp = explode(',', $extensions);
            $allowedExtensions = str_replace(' ', '', $temp);
            return $allowedExtensions;
        }
        else {
            return $this->$_allowedFileExtensions;
        }
    }

    /**
     * STUB
     * Returns an array of allowed mime types
     *
     * @return array
     */
    private function getAllowedMIMETypes() {

    }

    /**
     * STUB
     * Checks the passed in file's header
     *
     * @param string $filename
     * @return bool
     */
    private function checkFileHeader($filename) {

    }

    /**
     * Checks the passed in file's extension, mime type, and header
     *
     * @param string $filename
     * @return bool
     */
    public function checkFileIntegrity($filename) {

        $extensionCheckDidPass = false;
        $mimeTypeCheckDidPass = true;   // Needs setting to false when mime check function is implemented.
        $headerCheckDidPass = true;     // Needs setting to false when header check function is implemented.

        /*
         * Check file extension
         */
        $allowedExts = $this->getAllowedFileExtensions();
        $fileComponents = explode('.', $filename);
        $fileExt = strtolower($fileComponents[sizeof($fileComponents) - 1]);
        $extensionCheckDidPass = in_array($fileExt, $allowedExts);

        /*
         * TODO: Check MIME type
         */


        /*
         * TODO: Check file header
         */


        return ($extensionCheckDidPass && $mimeTypeCheckDidPass && $headerCheckDidPass) ? true: false;
    }

    /**
     * Saves an uploaded file to disk
     *
     * @param string $filename
     * @param array $data
     * @return bool
     */
    public function saveFile($filename, $data = array()) {
        $orderNumber = $data['order-number'];
        $emailAddress = $data['email-address'];
        $additionalComments = $data['additional-comments'];

        if (!isset($orderNumber) && !isset($emailAddress)) {
            return false;
        }

        $fileDataName = 'file'; //'Filedata';

        $folder = Mage::getStoreConfig(self::XML_PATH_UPLOAD_PATH, null);
        if (!isset($folder)) $folder = self::DEFAULT_UPLOAD_PATH;

        $path = Mage::getBaseDir() . DS . $folder . DS . $orderNumber . DS;

        try {
            $uploader = new Varien_File_Uploader($fileDataName);
            $uploader->setAllowedExtensions($this->getAllowedFileExtensions());
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);
            $uploader->save($path, $filename);

            $newFilename = $uploader->getUploadedFileName();

            // TODO: move this elsewhere
            $dbData = array(
                'order_id' => $orderNumber,
                'original_filename' => $filename,
                'new_filename' => $newFilename,
                'file_path' => $path,
                'additional_comments' => $additionalComments,
                'email_address' => $emailAddress,
                'ip_address' => Mage::helper('core/http')->getRemoteAddr(),
                'uploaded_at' => Mage::app()->getLocale()->storeTimeStamp()
            );
            $this->commitToDatabase($dbData);

            return true;
        }
        catch (Exception $e) {
            Mage::log('Exception:' . $e);
            return false;
        }

        return false;
    }

    /**
     * Saves a chunk of an uploaded file to disk
     *
     * @param $filename
     * @param array $data
     * @return bool
     */
    public function saveFileChunk($filename, $data = array()) {
        $orderNumber = $data['order-number'];
        $emailAddress = $data['email-address'];
        $additionalComments = $data['additional-comments'];
        $chunkNumber = $data['chunk-number'];
        $chunkCount = $data['chunk-count'];
        $folder = Mage::getStoreConfig(self::XML_PATH_UPLOAD_PATH, null);
        if (!isset($folder)) $folder = self::DEFAULT_UPLOAD_PATH;
        $path = Mage::getBaseDir() . DS . $folder . DS . $orderNumber . DS;

        $chunkFilename = $filename . '.chunk' . $chunkNumber;
        $in_path_components = explode(DS, $_FILES['file']['tmp_name']);
        $in_path = dirname($_FILES['file']['tmp_name']);
        $in_filename = array_pop($in_path_components);

        try {
            // Open input stream
            $in = new Varien_Io_File();
            $in->open(array('path' => $in_path));
            $in->streamOpen($in_filename, "rb");

            // Open output stream
            $out = new Varien_Io_File();
            $out->setAllowCreateFolders(true);
            $out->open(array('path' => $path));
            $out->streamOpen($chunkFilename, "wb");

            // Read from input and write to output
            $data = null;
            while ($data = $in->streamRead()) {
                $out->streamWrite($data);
            }

            // Close output stream
            $out->streamClose();
            $out->close();

            // Close input stream
            $in->streamClose();
            $in->close();

            // Is this the last chunk? Join all chunks if so...
            if ($chunkNumber == $chunkCount - 1) {

                // TODO: check if all of the chunks were saved first before trying to join them

                // Open output stream
                $joinedFile = new Varien_Io_File();
                $joinedFile->setAllowCreateFolders(true);
                $joinedFile->open(array('path' => $path));
                $newFilename = Varien_File_Uploader::getNewFileName($path . DS . $filename);
                $joinedFile->streamOpen($newFilename, "wb");

                for ($i = 0; $i < $chunkCount; ++$i) {

                    // Open chunk
                    $chunk = new Varien_Io_File();
                    $chunk->setAllowCreateFolders(true);
                    $chunk->open(array('path' => $path));
                    $chunkFn = $filename . ".chunk$i";
                    $chunk->streamOpen($chunkFn, "rb");

                    // Read from chunk and write to output
                    $chunkData = null;
                    while ($chunkData = $chunk->streamRead()) {
                        $joinedFile->streamWrite($chunkData);
                    }

                    // Close and delete chunk
                    $chunk->streamClose();
                    $chunk->close();
                    $chunk->rm($chunkFn);
                }

                // Close assembled output
                $joinedFile->streamClose();
                $joinedFile->close();

                // TODO: move this elsewhere
                $dbData = array(
                    'order_id' => $orderNumber,
                    'original_filename' => $filename,
                    'new_filename' => $newFilename,
                    'file_path' => $path,
                    'additional_comments' => $additionalComments,
                    'email_address' => $emailAddress,
                    'ip_address' => Mage::helper('core/http')->getRemoteAddr(),
                    'uploaded_at' => Mage::app()->getLocale()->storeTimeStamp()
                );
                $this->commitToDatabase($dbData);
            }

            return true;
        }
        catch (Exception $e) {
            Mage::log('Exception:' . $e);
            return false;
        }

        return false;
    }

    private function commitToDatabase($data) {
        $model = Mage::getModel('aptoplex_easyuploader/upload');
        $model->setData($data);
        $model->save();
    }

    /**
     * Moves existing uploaded files to new $destination
     *
     * @param $destination
     * @param $mode
     * @param $commitToDb
     */
    public function moveExistingUploads($destination, $mode = 0755, $commitToDb = false) {
        $model = Mage::getModel('aptoplex_easyuploader/upload');
        $collection = $model->getCollection();
        $collectionData = $collection->getData();

        for ($i = 0; $i < count($collectionData); ++$i) {
            $data = $collectionData[$i];

            $filePath = $data['file_path'];
            $newPath = $destination . DS . $data['order_id'] . DS;
            if (!file_exists($newPath)) {
                mkdir($newPath, $mode, true);
                rename($filePath, $newPath); // This moves the directory and contents of $filePath to $newPath
            }

            if ($commitToDb) {
                $model->load($data['entity_id']);
                $model->setData('file_path', $newPath);
                $model->save();
            }
        }
    }

    /**
     * Returns a response message
     *
     * @param int $responseCode
     * @return array|null
     */
    public static function getResponseMessage($responseCode) {

        $response = null;

        $helper = Mage::helper('aptoplex_easyuploader/data');

        switch ($responseCode) {
            case self::OK:
                $message = $helper->__("OK: No errors.");
                $response = array(
                    "code" => self::OK,
                    "message" => $message
                );
                break;
            case self::ORDER_NOT_FOUND:
                $message =  $helper->__("ERROR: Order not found.") . "\n\n";
                $message .= $helper->__("The order number or e-mail address that you entered has not been recognised.") . "\n\n";
                $message .= $helper->__("Please ensure that you have entered your full order number and e-mail address correctly.");
                $response = array(
                    "code" => self::ORDER_NOT_FOUND,
                    "message" => $message
                );
                break;
            case self::FILE_SAVE_FAILED:
                $message = $helper->__("ERROR: Upload failed.");
                $response = array(
                    "code" => self::FILE_SAVE_FAILED,
                    "message" => $message
                );
                break;
            case self::FILE_INTEGRITY_CHECK_FAILED:
                $message = $helper->__("ERROR: File integrity check failed.");
                $response = array(
                    "code" => self::FILE_INTEGRITY_CHECK_FAILED,
                    "message" => $message
                );
                break;
            case self::UPLOAD_NOT_PERMITTED:
                $message =  $helper->__("ERROR: Uploading not permitted for this order at this time.") . "\n\n";
                $message .= $helper->__("Please check the status of your order in your Account Area.");
                $response = array(
                    "code" => self::UPLOAD_NOT_PERMITTED,
                    "message" => $message
                );
                break;
            case self::UNKNOWN_ERROR:
                $message = $helper->__("ERROR: Unknown error. Please contact support for assistance.");
                $response = array(
                    "code" => self::UNKNOWN_ERROR,
                    "message" => $message
                );
                break;
            default:
                break;
        }

        return $response;
    }

    /**
     * Sends a notification e-mail
     *
     * @param $data
     */
    public function sendNotificationEmail($data) {
        $senderAddress = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_SENDER_ADDRESS, null);
        $senderName = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_SENDER_NAME, null);
        $recipients = explode(',', str_replace(' ', '', Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_RECIPIENT_ADDRESSES, null)));
        $subject = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_SUBJECT, null);
        $body =     $this->__("A customer has uploaded some files to your store:") . "\n\n";
        $body .=    $this->__("Order Number:") . "\t\t" . $data['order_number'] . "\n";
        $body .=    $this->__("E-mail Address:") . "\t\t" . $data['customer_email'] . "\n\n";
        $body .=    $this->__("To view them, please log in to your store administration area and select") . "\n";
        $body .=    $this->__("'Easy Uploader->Uploads' from the main menu.");
        $host = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_HOST, null);
        $port = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_PORT, null);
        $authType = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_AUTHENTICATION_TYPE, null);
        $ssl = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_SSL, null);
        $username = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_USERNAME, null);
        $password = Mage::helper('core')->decrypt(Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_PASSWORD, null));

        $mail = new Zend_Mail();
        $mail->setFrom($senderAddress, $senderName);
        $mail->addTo($recipients);
        $mail->setSubject($subject);
        $mail->setBodyText($body);

        if (Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_EMAIL_USE_CUSTOM_SMTP_SERVER, null)) {
            $config = array(
                'port' => $port,
                'auth' => ($authType != 'none') ? $authType : null,
                'ssl' => $ssl,
                'username' => $username,
                'password' => $password
            );
            $mailTransport = new Zend_Mail_Transport_Smtp($host, $config);
            Zend_Mail::setDefaultTransport($mailTransport);
            try {
                $mail->send($mailTransport);
                return true;
            } catch (Zend_Mail_Exception $exception) {
                Mage::log($exception);
                return false;
            }
        }
        else {
            $mailTransport = new Zend_Mail_Transport_Sendmail();
            Zend_Mail::setDefaultTransport($mailTransport);
            try {
                $mail->send();
                return true;
            }
            catch (Zend_Mail_Transport_Exception $exception) {
                Mage::log($exception);
                return false;
            }
        }

        return true;
    }
}