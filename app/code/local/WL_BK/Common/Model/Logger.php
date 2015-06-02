<?php
/**
 * @description Logger Object for White Labelled custom modules
 * @namespace WL
 * @package WL_Common
 * @name WL_Common_Model_Logger
 * @author kennguyen
*/
class WL_Common_Model_Logger
{
	/*
	const WHITELABELLED_LOG_BASE_DIR = "C:/wamp/www/sites/Bevilles_FULL_SVN/trunk/magento/var/wl_log/";
	const LOG_IMPORT_LOCATION_PATH = "import/";
	const LOG_EXPORT_LOCATION_PATH = "export/";
	const LOG_SLI_LOCATION_PATH = "sli/";
	const LOG_GENERAL_LOCATION_PATH = "general/";
	const MAGENTO_DEFAULT_LOG_PATH = '/var/log/';
	*/
	
	protected $_logger = null;
	protected $_stream = null;
	protected $_writter = null;
	protected $_logPath = null;
	protected $_logFileName = null;
	protected $_loggingProcess = 'export'; // 'import', 'export'

	protected $_enable_firebug_log = false;
	protected $_firebug_logger = null;
	protected $_firebug_writter = null;
	protected $_firebug_request = null;
	protected $_firebug_response = null;
	protected $_firebug_channel = null;



	public function __construct( $logPath, $logFileName )
	{
		$this->_logPath = $logPath;
		$this->_logFileName = $logFileName;
		$this->initLogger();
	}

	private function initLogger()
	{
		$this->_stream = $this->initLogStream($this->_logFileName);
		if ( !empty ($this->_stream) )
		{
			$this->_writer = new Zend_Log_Writer_Stream($this->_stream);
			$this->_logger = new Zend_Log($this->_writer);
		}
	}

	public function getLogger()
	{
		return $this->_logger;
	}

	public function enableFirebugLog()
	{
		$this->_enable_firebug_log = true;
		$this->_firebug_writer = new Zend_Log_Writer_Firebug();
		$this->_firebug_logger = new Zend_Log($this->_firebug_writer);

		//start the wildfire component
		$this->_firebug_request = new Zend_Controller_Request_Http();
		$this->_firebug_response = new Zend_Controller_Response_Http();
		$this->_firebug_channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
		$this->_firebug_channel->setRequest($this->_firebug_request);
		$this->_firebug_channel->setResponse($this->_firebug_response);

		// Start output buffering
		ob_start();
	}

	public function disableFirebugLog()
	{
		$this->_enable_firebug_log = false;
		$this->_firebug_writer = null;
		$this->_firebug_logger = null;
		$this->_firebug_response = null;
		$this->_firebug_request = null;
		$this->_firebug_channel = null;
	}

	public function finished()
	{
		if ( $this->_enable_firebug_log )
			$this->disableFirebugLog();

		@fclose($this->_stream);
	}

	public function initLogStream ($logFileName)
	{
		$logStream = null;

		/*
		// remove the dependency between the logger object and the path of log file.
		// The path should be passed in constructor
		if ( $this->_loggingProcess == 'import' )
			$logPath = self::WHITELABELLED_LOG_BASE_DIR . self::LOG_IMPORT_LOCATION_PATH;
		elseif ( $this->_loggingProcess == 'export' )
			$logPath = self::WHITELABELLED_LOG_BASE_DIR . self::LOG_EXPORT_LOCATION_PATH;
		elseif ( $this->_loggingProcess == 'sli' )
			$logPath = self::WHITELABELLED_LOG_BASE_DIR . self::LOG_SLI_LOCATION_PATH;
		else 
			$logPath = self::WHITELABELLED_LOG_BASE_DIR . self::LOG_GENERAL_LOCATION_PATH;
		*/

		try
		{
			$logStream = @fopen( $this->_logPath . $logFileName, 'x');
		} catch (Exception $e) {
			$logStream = @fopen(Mage::getBaseDir('base') . self::MAGENTO_DEFAULT_LOG_PATH . $logFileName, 'x');
		}

		return $logStream;
	}

	public function log ( $message, $messageType = Zend_Log::INFO )
	{
		if ( $this->getLogger() != null )
		{
			$this->getLogger()->log( $message, $messageType );
		} else {
			Mage::log( date('Ymd-His') . ' - ' . $messageType . ':' . $message );
		}

		if ( $this->_enable_firebug_log )
		{
			$this->_firebug_logger->log( $message, $messageType );
			// Flush log data to browser
			$this->_firebug_channel->flush();
			$this->_firebug_response->sendHeaders();
		}
	}
}
