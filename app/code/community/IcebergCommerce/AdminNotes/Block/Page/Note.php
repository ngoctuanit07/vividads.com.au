<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Block_Page_Note extends Mage_Adminhtml_Block_Abstract
{   	
	public function getUserId()
	{
		if( !$this->hasUserId() ){
			$this->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
		}
		return $this->getData('user_id');
	}
	
	public function getSaveUrl(){
		return $this->getUrl('adminnotes/admin/save');
	}
	
	public function getStatusUrl(){
		return $this->getUrl('adminnotes/admin/setStatus');
	}
}