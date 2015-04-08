<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Cloud_Application extends Varien_Object
{
	const CACHE_APPLICATIONS_XML	= 'mpbackup_applications_xml';
	const APP_PATH_NAME				= 'apps';
	
	
	/**
	 * Load Applications XML config files from etc/apps directory and cache it
	 *
	 * @return Varien_Simplexml_Config
	 */
	public function getXmlConfig()
	{
		$cachedXml = Mage::app()->loadCache(self::CACHE_APPLICATIONS_XML);
		if ($cachedXml) {
			$xmlApps = new Varien_Simplexml_Config($cachedXml);
		} else {
			$files = array();
			foreach (new DirectoryIterator(Mage::helper('mpbackup')->getApplicationXmlPath()) as $fileinfo) {
				/* @var $fileinfo DirectoryIterator */
				if (!$fileinfo->isDot()
					&& ($filename = $fileinfo->getFilename())
					&& (strtolower(strval(preg_replace("#.*\.([^\.]*)$#is", "\\1", $filename))) == 'xml'))
				{
					$files[] = self::APP_PATH_NAME.DS.$filename;
				}
			}

			$config = new Varien_Simplexml_Config();
			$config->loadString('<?xml version="1.0"?><application></application>');
			foreach($files as $file) {
				Mage::getConfig()->loadModulesConfiguration($file, $config);
			}
			$xmlApps = $config;
			if (Mage::app()->useCache('config')) {
				Mage::app()->saveCache($config->getXmlString(), self::CACHE_APPLICATIONS_XML, array(Mage_Core_Model_Config::CACHE_TAG));
			}
		}
		
		return $xmlApps;
	}

	/**
	 * Return filtered list of applications as SimpleXml object
	 *
	 * @param array $filters Key-value array of filters for application node properties
	 * @return Varien_Simplexml_Element
	 */
	public function getAppsXml($filters = array())
	{
		$apps = $this->getXmlConfig()->getNode();
		$result = clone $apps;

		// filter applications by params
		if (is_array($filters) && count($filters) > 0) {
			foreach ($apps as $code => $app) {
				try {
					$reflection = new ReflectionObject($app);
					foreach ($filters as $field => $value) {
						if (!$reflection->hasProperty($field) || (string)$app->{$field} != $value) {
							throw new Exception();
						}
					}
				} catch (Exception $e) {
					unset($result->{$code});
					continue;
				}
			}
		}

		return $result;
	}
	

	/**
	 * Return list of applications as array
	 *
	 * @param array $filters Key-value array of filters for application node properties
	 * @return array
	 */
	public function getAppsArray($filters = array())
	{
		if (!$this->_getData('apps_array')) {
			$result = array();
			foreach ($this->getAppsXml($filters) as $app) {
				/* @var $app Varien_Simplexml_Element */
				$helper = Mage::helper('mpbackup');
				$app_name = $app->getName();
				$result[$app_name] = $app->asArray();
				$result[$app_name]['name'] = $app->getName();
				$result[$app_name]['label'] = $helper->__((string)$app->label);
				
				if((empty($result[$app_name]['settings']) || !is_array($result[$app_name]['settings']))
					&& !empty($app->settings) && ($app->settings instanceof Varien_Simplexml_Element))
				{
					$result[$app_name]['settings'] = $app->settings->asArray();
				}
				
				if(empty($result[$app_name]['settings'])) {
					$result[$app_name]['settings'] = array();
				}
				
				if(!empty($result[$app_name]['auth'])) {
					$result[$app_name]['auth'] = (boolean) intval($result[$app_name]['auth']);
				} else {
					$result[$app_name]['auth'] = false;
				}
			}
			
			uasort($result, array($this, "_sortApplications"));
			$this->setData('apps_array', $result);
		}
		
		return $this->_getData('apps_array');
	}

	/**
	 * Return list of applications as names array
	 *
	 * @param array $filters Key-value array of filters for application node properties
	 * @return array
	 */
	public function getApps($filters = array())
	{
		return array_keys($this->getAppsArray($filters));
	}
	

    /**
     * User-defined applications sorting by Label
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortApplications($a, $b)
    {
        return strcmp($a['label'], $b['label']);
    }
}
