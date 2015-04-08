<?php
/**
 * @description Helper with common activities
 * @namespace WL
 * @package WL_Common
 * @name WL_Common_Helper_Data
 * @author kennguyen
 */
class WL_Common_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 *
	 * FTP a file to a server
	 * @param unknown_type $ftpservername
	 * @param unknown_type $ftpusername
	 * @param unknown_type $ftppassword
	 * @param unknown_type $ftpsourcefile
	 * @param unknown_type $ftpdirectory
	 * @param unknown_type $ftpdestinationfile
	 * @param unknown_type $feedname
	 * @param unknown_type $logger
	 * @param unknown_type $error_email
	 *
	 * @return bool
	 */
	public function ftpFile( $ftpservername, $ftpusername, $ftppassword, $ftpsourcefile, $ftpdirectory, $ftpdestinationfile, $feedname, $logger = null, $error_email = null ) {
		if ( is_null($logger) )
			$logger = new WL_Common_Model_Logger('ftp_log' . date('YmdHis') . '.log');
		
		$wl_notification = new WL_Common_Model_Notification( $error_email );
	
		// set up basic connection
		$conn_id = ftp_connect( $ftpservername );
	
	
		if ( $conn_id == false )
		{
			$errorMessage = "FTP open connection failed for " . $feedname . " to " . $ftpservername;
			$logger->log( $errorMessage, Zend_Log::ERR );
			$wl_notification->sendErrorNotificationToAdmin( $errorMessage );
			return false;
		}
	
		// login with username and password
		$login_result = ftp_login( $conn_id, $ftpusername, $ftppassword );
	
		// check connection
		if ((!$conn_id) || (!$login_result))
		{
			$errorMessage = "FTP connection has failed for " . $feedname . "! Attempted to connect to " . $ftpservername . " for user " . $ftpusername . "failed";
			$logger->log( $errorMessage, Zend_Log::ERR );
			$wl_notification->sendErrorNotificationToAdmin( $errorMessage );
			return false;
		} else {
			$successMessage = "Connected to " . $ftpservername . ", for user " . $ftpusername . " for " . $feedname;
			$logger->log( $successMessage, Zend_Log::INFO );
		}
	
		if ( strlen( $ftpdirectory ) > 0 )
		{
			if (ftp_chdir($conn_id, $ftpdirectory ))
			{
				$successMessage = "Current directory is now: " . ftp_pwd($conn_id) . " for " . $feedname;
				$logger->log( $successMessage, Zend_Log::INFO );
			} else {
				$errorMessage = "Couldn't change directory on " . $ftpservername . " for " . $ftpdirectory;
				$logger->log( $errorMessage, Zend_Log::ERR );
				$wl_notification->sendErrorNotificationToAdmin( $errorMessage );
				return false;
			}
		}
	
		ftp_pasv( $conn_id, true );
			
		// upload the file
		$upload = ftp_put($conn_id, $ftpdestinationfile, $ftpsourcefile, FTP_BINARY);
	
		// check upload status
		if (!$upload)
		{
			$errorMessage = $ftpservername . ": FTP upload has failed! for " . $ftpsourcefile;
			$logger->log( $errorMessage, Zend_Log::ERR );
			$wl_notification->sendErrorNotificationToAdmin( $errorMessage );
			ftp_close($conn_id);
			return false;
		} else {
			$successMessage = "Uploaded " . $ftpsourcefile . " to " . $ftpservername . " as " . $ftpdestinationfile . " for " . $feedname;
			$logger->log( $successMessage, Zend_Log::INFO );
		}
	
		// close the FTP stream
		ftp_close($conn_id);
		return true;
	}
}
?>