<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Block_Page_New extends Mage_Adminhtml_Block_Abstract
{   	
	public function getUserId()
	{
		if( !$this->hasUserId() ){
			$this->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
		}
		return $this->getData('user_id');
	}
	
	public function getTypes(){
		return Mage::getModel('adminnotes/note')->getTypes();
	}
	
	public function getPath(){
		if( !$this->hasPath() ){
			$this->setPath( Mage::helper('adminnotes')->getCurrentPathId() );
		}
		return $this->getData('path');
	}
	
	public function getSaveUrl(){
		return $this->getUrl('adminnotes/admin/save');
	}
	
	public function getStatusUrl(){
		return $this->getUrl('adminnotes/admin/setStatus');
	}
}