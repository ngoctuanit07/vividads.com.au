<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Model_Note extends Mage_Core_Model_Abstract
{
	protected function _construct()
    {
    	parent::_construct();
        $this->_init('adminnotes/note');
        $this->setIdFieldName('note_id');
        
        $this->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
    }
	
	public function loadStatus()
	{
		$this->getResource()->loadStatus($this );
	}
    
    public function updateStatus( $status )
    {
    	$this->getResource()->updateStatus( $this , $status );
    	
    	$this->setStatus( $status );
    	
    	return $this;
    }
    
    public function setSeen()
    {
    	if( $this->getStatus() == null )
    	{
    		$this->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
    		$this->updateStatus( 0 );
    	}
    	
    	return $this;
    }
    
    public function isHidden(){
    	return $this->getStatus() > 0;
    }
    
    public function getTypes()
    {
    	return array(
    		'note'     => 'Note',
    		'comment'  => 'Comment',
    		'message'  => 'Message',
    		'warning'  => 'Warning',
    		'question' => 'Question',
    	);
    }
    
    public function deleteRelations(){
    	$this->getResource()->deleteRelations( $this );
    	
    	return $this;
    }
    
	public function isEditable(){
		if( Mage::getSingleton('admin/session')->getUser()->getId() == $this->getCreatedBy() || Mage::getSingleton('admin/session')->isAllowed('admin/adminnotes/edit') ){
			return true;
		}
		return false;
	}
	
	public function isDeletable(){
		if( Mage::getSingleton('admin/session')->getUser()->getId() == $this->getCreatedBy() || Mage::getSingleton('admin/session')->isAllowed('admin/adminnotes/delete') ){
			return true;
		}
		return false;
	}
}