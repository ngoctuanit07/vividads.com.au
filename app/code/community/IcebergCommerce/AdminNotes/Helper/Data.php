<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Helper_Data extends Mage_Core_Helper_Data
{
	public function getCurrentPathId()
	{
		$_request = Mage::app()->getRequest();
		$fullAction = Mage::app()->getFrontController()->getAction()->getFullActionName();
		
		
		$pathId = $fullAction;
		
		foreach( $_request->getParams() as $name => $param )
		{
			if ($name == 'id' || preg_match('#(\w+)_id#' , $name ))
			{
				$pathId .= '/' /*. $name . '/'*/ . $param;
			}
			
			if (stristr($fullAction,'system_config') && $name == 'section')
			{
				$pathId .= '/' . $name .'_'. $param;
			}
		}

		return $pathId;
	}
	
	public function getCurrentPath()
	{
		$frontname = Mage::app()->getRequest()->getModuleName();
		$controller = Mage::app()->getRequest()->getControllerName();
		$action = Mage::app()->getRequest()->getActionName();
		$params = Mage::app()->getRequest()->getParams();
		
		if (isset($params[Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME]))
		{
			unset($params[Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME]);
		}
		
		$paramStr = '';
	
		if (!empty($params))
		{
			foreach ($params as $k=>$v)
			{
				$paramStr .= $k . '/' . $v . '/';
			}
		}
		
		$path = 'adminhtml/';
		
		$adminFrontName = Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName');
		$adminFrontName = $adminFrontName ? $adminFrontName : 'admin';
		
		if ($frontname != $adminFrontName)
		{
			// Note, the starting "adminhtml/" has been overwritten
			$path = $frontname . '/';
		}
		
		$path .= $controller . '/' .  $action . '/' . $paramStr;
		
		/*
		$fullAction = Mage::app()->getFrontController()->getAction()->getFullActionName();
		
		$params = Mage::app()->getRequest()->getParams();
		$paramStr = '';
		if (isset($params[Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME]))
		{
			unset($params[Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME]);
		}
		if (!empty($params))
		{
			foreach ($params as $k=>$v)
			{
				$paramStr .= $k . '/' . $v . '/';
			}
		}
		
		$path = str_replace('_','/',$fullAction) . '/' . $paramStr;
		*/
		
		return $path;
	}
}