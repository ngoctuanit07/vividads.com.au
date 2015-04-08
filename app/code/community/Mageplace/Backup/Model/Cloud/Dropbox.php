<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package        Mageplace_Backup
 * @copyright    Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license        http://www.mageplace.com/disclaimer.html
 */
/**
 * @method string getRoot()
 */

class Mageplace_Backup_Model_Cloud_Dropbox extends Mageplace_Backup_Model_Cloud
{
	const FILE_PART_MAX_SIZE = 100;

	const HMAC_SHA1 = 'HMAC-SHA1';
	const RSA_SHA1 = 'RSA-SHA1';

	const CONSUMER_KEY = 'consumerKey';
	const CONSUMER_SECRET = 'consumerSecret';
	const OAUTH_ACCESS_TOKEN = 'oauth_access_token';
	const OAUTH_ACCESS_TOKEN_SECRET = 'oauth_access_token_secret';
	const DROPBOX_DIRECTORY = 'appPath';
	const TIMEOUT = 'connTimeOut';
	const FILEPARTMAXSIZE = 'filepartmaxsize';

	const URI = 'https://api.dropbox.com/1/';
	const URI_REQUEST_TOKEN = 'https://api.dropbox.com/1/oauth/request_token';
	const URI_ACCESS_TOKEN = 'https://api.dropbox.com/1/oauth/access_token';
	const URI_AUTHORIZE = 'https://www.dropbox.com/1/oauth/authorize';
	const URI_CONTENT = 'https://api-content.dropbox.com/1/';

	const URI_SUFFIX_ACCOUNT_INFO = 'account/info';
	const URI_SUFFIX_FILES = 'files';
	const URI_SUFFIX_FILES_PUT = 'files_put';
	const URI_SUFFIX_FILE_DELETE = 'fileops/delete';

	const ROOT_DROPBOX = 'dropbox';
	const ROOT_SANDBOX = 'sandbox';

	protected function _construct()
	{
		parent::_construct();

		$this->setRoot(self::ROOT_DROPBOX);
	}

	/**
	 * Get max size of file for upload to cloud server (Mb)
	 *
	 * @return int
	 */
	public function getMaxSize()
	{
		$filepartmaxsize = (int)$this->getConfigValue(self::FILEPARTMAXSIZE);

		return $filepartmaxsize ? $filepartmaxsize : self::FILE_PART_MAX_SIZE;
	}

	public function getRedirectUrl()
	{
		if (!$this->getOauthInfo()) {
			return null;
		}

		return $this->getConsumer()->getRedirectUrl(array('oauth_callback' => Mage::helper("adminhtml")->getUrl('*/*/callback')));
	}

	public function callback($request, $response)
	{
		if (!$this->getOauthInfo()) {
			return null;
		}

		return true;
	}

	public function getConsumer($config = null)
	{
		if (!$consumer = $this->_getData('consumer')) {
			if (is_null($config)) {
				$config = $this->_getConsumerConfig();
			}

			$httpClient = new Zend_Http_Client();
			$httpClient->setAdapter($this->_getAdapter());

			$timeOut = (int)$this->getConfigValue(self::TIMEOUT);
			if ($timeOut > 0) {
				$httpClient->setConfig(array('timeout' => $timeOut));
			}

			$consumer = new Zend_Oauth_Consumer($config);
			$consumer->setHttpClient($httpClient);

			$this->setData('consumer', $consumer);
		}

		return $consumer;
	}

	/**
	 * @return Zend_Http_Client_Adapter_Curl
	 */
	protected function _getAdapter()
	{
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$adapter->setCurlOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$adapter->setCurlOption(CURLOPT_SSLVERSION, 3);
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, 2);

		return $adapter;
	}

	protected function _getConsumerConfig()
	{
		return array(
			'consumerKey' => $this->getConfigValue(self::CONSUMER_KEY),
			'consumerSecret' => $this->getConfigValue(self::CONSUMER_SECRET),
			'requestTokenUrl' => self::URI_REQUEST_TOKEN,
			'accessTokenUrl' => self::URI_ACCESS_TOKEN,
			'authorizeUrl' => self::URI_AUTHORIZE,
			'requestScheme' => Zend_Oauth::REQUEST_SCHEME_QUERYSTRING,
			'requestMethod' => Zend_Oauth::GET,
			'signatureMethod' => self::HMAC_SHA1,
		);
	}

	public function getOauthInfo()
	{
		$oauth_token_secret = $oauth_token = NULL;

		$config = $this->_getConsumerConfig();
		$consumer = $this->getConsumer($config);

		$session = $this->_getSession();
		/* @var $session Mageplace_Backup_Model_Session */
		if ($session->checkCloud($config)) {
			$oauth_token = $session->getOauthToken();
			$oauth_token_secret = $session->getOauthTokenSecret();
		}

		if ($oauth_token && $oauth_token_secret) {
			$requestToken = new Zend_Oauth_Token_Request();
			$requestToken->setToken($oauth_token);
			$requestToken->setTokenSecret($oauth_token_secret);

			$accessToken = $consumer->getAccessToken($_GET, $requestToken);
			if (!($token_key = $accessToken->getToken()) || !($token_secret = $accessToken->getTokenSecret())) {
				return false;
			}

			$this->saveConfigValue(self::OAUTH_ACCESS_TOKEN, $token_key);
			$this->saveConfigValue(self::OAUTH_ACCESS_TOKEN_SECRET, $token_secret);

			$session->setAccessToken($accessToken);

			return true;
		}

		try {
			$token_request = $consumer->getRequestToken();
		} catch (Exception $e) {
			Mage::logException($e);
			return null;
		}

		$response = $token_request->getResponse();
		parse_str($response->getBody());

		if (!$oauth_token || !$oauth_token_secret) {
			try {
				$body = Zend_Json::decode($response->getBody());
				switch ($response->getStatus()) {
					case 304:
						$error = 'Empty response body.';
						break;

					case 403:
						$error = 'Forbidden. This could mean a bad OAuth request.' . @$body["error"];
						break;

					case 404:
						$error = 'Resource at uri: ' . self::URI_REQUEST_TOKEN . ' could not be found. ' . @$body["error"];
						break;

					case 507:
						$error = 'This dropbox is full. ' . @$body["error"];
						break;
				}
				if (isset($error)) {
					$e = new Mage_Exception($error, null);
					Mage::logException($e);
					Mage::getSingleton('adminhtml/session')->addError($error);
					return null;
				}
			} catch (Exception $e) {
				Mage::logException($e);
				return null;
			}
		}

		$this->setData('consumer', $consumer);
		$this->setData('oauth_token', $oauth_token);
		$this->setData('oauth_token_secret', $oauth_token_secret);

		$session->setCloudId($config)
			->setOauthToken($oauth_token)
			->setOauthTokenSecret($oauth_token_secret);

		return true;
	}

	public function getAccessToken($reset_session = false)
	{
		if (!$accessToken = $this->_getData('access_token')) {
			$session = $this->_getSession();
			if ($reset_session) {
				$session->unsAccessToken();
			} else {
				$accessToken = $session->getAccessToken();
			}

			if (empty($accessToken)) {
				$oauth_token = $this->getConfigValue(self::OAUTH_ACCESS_TOKEN);
				$oauth_token_secret = $this->getConfigValue(self::OAUTH_ACCESS_TOKEN_SECRET);
				if (!$oauth_token || !$oauth_token_secret) {
					return null;
				}

				$accessToken = new Zend_Oauth_Token_Access();
				$accessToken->setToken($oauth_token);
				$accessToken->setTokenSecret($oauth_token_secret);

				$session->setAccessToken($accessToken);
			}

			$this->setAccessToken($accessToken);
		}

		return $accessToken;
	}

	public function checkConnection()
	{
		try {
			$info = $this->getAccountInfo();
		} catch (Exception $e) {
			$this->getAccessToken(true);
			try {
				$info = $this->getAccountInfo();
			} catch (Exception $e) {
				return false;
			}
		}


		if (!empty($info['uid'])) {
			return true;
		}

		return false;
	}

	public function resetAuthData()
	{
		$this->saveConfigValue(self::OAUTH_ACCESS_TOKEN, '');
		$this->saveConfigValue(self::OAUTH_ACCESS_TOKEN_SECRET, '');

		$this->_getSession()->unsetData();

		return $this;
	}

	/**
	 * Process request to Dropbox API
	 *
	 * @return array
	 */
	public function process($uri, $arguments = array(), $method = Zend_Oauth::GET, $httpHeaders = array())
	{
		ini_set('max_execution_time', '10000');
		set_time_limit(10000);
		ignore_user_abort(true);

		$token = $this->getAccessToken();
		/* @var $token Zend_Oauth_Token_Access */
		if (!($token instanceof Zend_Oauth_Token_Access)) {
			return array();
		}

		$oauthOptions = array(
			'consumerKey' => $this->getConfigValue(self::CONSUMER_KEY),
			'consumerSecret' => $this->getConfigValue(self::CONSUMER_SECRET),
			'signatureMethod' => self::HMAC_SHA1,
		);

		$oauthClient = $token->getHttpClient($oauthOptions);
		/* @var $oauthClient Zend_Oauth_Client */
		$oauthClient->setAdapter($this->_getAdapter());
		$oauthClient->setMethod($method);

		if (is_array($arguments)) {
			$oauthClient->setUri($uri);
			if ($method == Zend_Oauth::GET) {
				$method = "setParameterGet";
			} else {
				$method = "setParameterPost";
			}

			foreach ($arguments as $param => $value) {
				$oauthClient->$method($param, $value);
			}

		} elseif (is_string($arguments)) {
			preg_match("/\?file=(.*)$/i", $uri, $matches);
			if (isset($matches[1])) {
				$uri = str_replace($matches[0], "", $uri);
				$filename = $matches[1];
				$uri = Zend_Uri::factory($uri);
				if (method_exists($uri, 'addReplaceQueryParameters')) {
					$uri->addReplaceQueryParameters(array("file" => $filename));
				} else {
					$this->addReplaceQueryParameters($uri, array("file" => $filename));
				}
				$oauthClient->setParameterGet("file", $filename);
			}
			$oauthClient->setUri($uri);
			$oauthClient->setRawData($arguments);
		} elseif (is_resource($arguments)) {
			$oauthClient->setUri($uri);
			/** Placeholder for Oauth streaming support. */
		}

		if (count($httpHeaders)) {
			foreach ($httpHeaders as $k => $v) {
				$oauthClient->setHeaders($k, $v);
			}
		}

		$response = $oauthClient->request();
		$body = Zend_Json::decode($response->getBody());

		switch ($response->getStatus()) {
			case 304 :
				return array();
				break;

			case 403 :
				$error = 'Forbidden. This could mean a bad OAuth request, or a file or folder already existing at the target location. Error: ' . @$body["error"];
				break;

			case 404 :
				$error = 'Resource at uri: ' . $uri . ' could not be found. Error: ' . @$body["error"];
				break;

			case 507 :
				$error = 'This dropbox is full. Error: ' . @$body["error"];
				break;
		}

		if (isset($error)) {
			$e = Mage::exception('Mageplace_Backup', $error);
			Mage::logException($e);
			throw $e;
		}

		return $body;
	}

	/**
	 * Returns information about the current dropbox account
	 *
	 * @return array
	 */
	public function getAccountInfo()
	{
		return $this->process(self::URI . self::URI_SUFFIX_ACCOUNT_INFO);
	}

	/**
	 * Uploads a new file
	 *
	 * @param string $path Target path (including filename)
	 * @param string $file Either a path to a file or a stream resource
	 * @return string
	 */
	public function putFile($path, $file)
	{
		if (is_string($file)) {
			$file = fopen($file, 'r');
		} elseif (!is_resource($file)) {
			Mage::throwException($this->_helper->__('File "%s" must be a file-resource or a string', strval($file)));
		}

		$filename = basename($path);
		$root = $this->getRoot();
		$file_cloud_path = trim($this->getConfigValue(self::DROPBOX_DIRECTORY), '/');
		$file_cloud_path = str_replace(' ', '_', trim($file_cloud_path));
		$result = $this->multipartProcess(self::URI_CONTENT . self::URI_SUFFIX_FILES . '/' . $root . '/' . $file_cloud_path, $file, $filename);
		if (empty($result['bytes'])) {
			return false;
		}

		return $file_cloud_path . '/' . $filename;
	}

	/**
	 * This method is used to generate multipart POST requests for file upload
	 *
	 * @param string $uri
	 * @param resource $file
	 * @param string $filename
	 * @return bool
	 */
	protected function multipartProcess($uri, $file, $filename)
	{
		$boundary = 'eiiHUH23EFef23f65jk8979jakhJKH8934JGGggVtE5675rcvuwcf7w6e2DB56e6dc6DYD';

		$headers = array(
			'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
		);

		$body = "--" . $boundary . "\r\n";
		$body .= "Content-Disposition: form-data; name=file; filename=" . rawurldecode($filename) . "\r\n";
		$body .= "Content-type: application/octet-stream\r\n";
		$body .= "\r\n";
		$body .= stream_get_contents($file);
		$body .= "\r\n";
		$body .= "--" . $boundary . "--";

		// Dropbox requires the filename to also be part of the regular arguments, so it becomes
		// part of the signature. 
		$uri .= '?file=' . $filename;

		return $this->process($uri, $body, Zend_Oauth::POST, $headers);
	}

	/**
	 * Magento version < 1.4.2
	 */
	public function addReplaceQueryParameters(&$uri, $queryParams)
	{
		$queryParams = array_merge($this->getQueryAsArray($uri), $queryParams);
		return $uri->setQuery($queryParams);
	}

	public function getQueryAsArray(&$uri)
	{
		$query = $uri->getQuery();
		$querryArray = array();
		if ($query !== false) {
			parse_str($query, $querryArray);
		}
		return $querryArray;
	}

	/**
	 * Delete file
	 *
	 * @param string $path Target path (including filename)
	 * @return bool
	 */
	public function deleteFile($path)
	{
		$result = $this->process(self::URI . self::URI_SUFFIX_FILE_DELETE, array('path' => $path, 'root' => $this->getRoot()));
		return !empty($result['is_deleted']);
	}
}
