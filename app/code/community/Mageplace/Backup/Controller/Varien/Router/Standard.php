<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Controller_Varien_Router_Standard extends Mage_Core_Controller_Varien_Router_Standard
{
	protected static $ALLOWED_ACTIONS = array('stage', 'start', 'multiStart', 'checkMemoryLimit');
	
	public function match(Zend_Controller_Request_Http $request)
	{
		$path = explode('/', trim($request->getPathInfo(), '/'));
		// If path doesn't match your module requirements
		if(($path[0] != 'mpbackup') || ($path[1] != 'backup') || (!in_array($path[2], self::$ALLOWED_ACTIONS))) {
			return parent::match($request);
		}
		
		require_once 'Mageplace/Backup/controllers/BackupController.php';
		
		$controllerClassName = 'Mageplace_Backup_BackupController';

		// Instantiate controller class
		$controllerInstance = Mage::getControllerInstance(
			$controllerClassName,
			$request,
			$this->getFront()->getResponse()
		);
		
		$request->setDispatched(true);

 		$actionMethodName = $controllerInstance->getActionMethodName($path[2]);
		$controllerInstance->preDispatch();
		$controllerInstance->$actionMethodName();

		return true;
	}
}
