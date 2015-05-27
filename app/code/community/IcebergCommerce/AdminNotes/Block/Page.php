<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Block_Page extends Mage_Adminhtml_Block_Abstract
{   	
	public function getNotes()
	{
		if( !$this->hasNotes() ) 
		{
			$collection = Mage::getResourceModel('adminnotes/note_collection');
			
			$collection->setUserId( $this->getUserId() )
						->setPathId( $this->getPathId() )
						->addUsernameToSelect();
						
			$this->setNotes( $collection);
		}
		
		return $this->getData('notes');
	}
	
	public function getNoteHtml( $note )
	{
		return $this->getLayout()->createBlock('adminnotes/page_note')
			->setTemplate('iceberg/adminnotes/note.phtml')
			->setNote( $note )
			->toHtml();
	}
	
	public function getUserId()
	{
		if( !$this->hasUserId() )
		{
			$this->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
		}
		return $this->getData('user_id');
	}
	
	public function getPathId()
	{
		if ( !$this->hasPathId() )
		{
			$this->setPathId( Mage::helper('adminnotes')->getCurrentPathId() );
		}
		return $this->getData('path_id');
	}
	
	public function getPath()
	{
		if (!$this->hasPath() )
		{
			$this->setPath(Mage::helper('adminnotes')->getCurrentPath());
		}
		return $this->getData('path');
	}
	
	public function getSaveUrl()
	{
		return $this->getUrl('adminhtml/adminnotes_index/save');
	}
	
	public function getNewUrl()
	{
		return $this->getUrl('adminhtml/adminnotes_index/new');
	}
	
	public function getDeleteUrl()
	{
		return $this->getUrl('adminhtml/adminnotes_index/delete');
	}
	
	public function getStatusUrl()
	{
		return $this->getUrl('adminhtml/adminnotes_index/setStatus');
	}
	
	public function canWrite()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/adminnotes/write');
	}
}