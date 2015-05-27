<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @copyright   Copyright (c) 2010 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Download for direct access Magento.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
class Artio_MTurbo_Model_DownloadMethods_Direct extends Artio_MTurbo_Model_DownloadMethods_Abstract
{
	
	
	/**
	 * Model for patching Magento for used "direct" method.
	 * 
	 * @var Mage_MTurbo_Model_Patch
	 */
	private $patchModel = null;

	
	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::getName()
	 */
	public function getName() {
		return Mage::helper('mturbo')->__('Direct access (created new instance Magento)');
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::isReady()
	 */
	public function isReady() {
		
		if (!isset($this->patchModel))
			$this->patchModel = Mage::getModel('mturbo/patch');

		if ($this->patchModel->isPatched()) {
			$this->errorMsg = '';
			return true;
		} else {
			$this->errorMsg = Mage::helper('mturbo')->__("File Mage.php is not patched");
			return false;
		}
		
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::allowParallelDownloading()
	 */
	public function allowParallelDownloading() {
		return false;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::downloadPages()
	 */
	public function downloadPages($urls) {
	
		$res = array();
	
		foreach ($urls as $url)
			$res[$url] = $this->_downloadPage($url);
	
		return $res;
	}
	

	/**
	 * Download HTML of page specified by $url and return it.
	 * 
	 * Method uses "direct access". There is created a Magento
	 * instance with REQUEST_URI equals to $url. Magento is executed
	 * and its output is caught by ob_xxx functions.
	 * 
	 * If $url is empty or method is not ready to execute (class MAge
	 * is not patched see Artio_MTurbo_Model_DownloadMethods_Direct::isReady)
	 * method returns empty string.
	 *
	 * @param string $url
	 * @return string contents of downloaded page
	 */
	protected function _downloadPage($url) {
		
		if (!$url || !$this->isReady())
			return '';
		
		/* remember Magento state and reset Magento */	    				
    	$prevURI = $_SERVER['REQUEST_URI'];
    	$state = Mage::getStaticState();
    	Mage::reset();
    			
		/* start catching output */	
    	ob_start();
    				
    	/* run new Magento */
		$_SERVER['REQUEST_URI'] = preg_replace('/http(s){0,1}:\/\/[^\/]*/', '', $url);
    	Mage::run();
    					
		/* get output */
    	$html = ob_get_contents();
    				
		/* end catching output */
		ob_end_clean();

		/* restore Magento state */		
    	Mage::restoreState($state);
    	
    	return $html;
	}

}