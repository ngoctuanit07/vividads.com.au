<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Adminhtml_Adminnotes_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->_title($this->__('Page Notes'));
        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Dashboard'), Mage::helper('adminnotes')->__('Admin Page Notes'));
        $this->renderLayout();
	}
	
	public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminnotes/list_grid')->toHtml());
    }
	
    /**
     * Called via an AJAX request
     */
	public function saveAction(){
		if ($postData = $this->getRequest()->getPost()) {
			$session = Mage::getSingleton('admin/session');
    	
			if( isset( $postData['note'] ) ) {
				$data = array();
				$model = Mage::getModel('adminnotes/note');
				$model->setUserId( $session->getUser()->getId() );
				
				if( isset( $postData['id'] ) ){
					$id =  $postData['id'];
					$model->load( $id );
					
					if( !$model->isEditable() ){
						die('Access Denied.');
					}
				}else{
					if( !$session->isAllowed('admin/adminnotes/write') ){
						die('Access Denied');
					}
					$data['path_id'] = $postData['path_id'];
					$data['path'] = $postData['path'];
					$data['created_at'] = date('Y-m-d H:i:s');
					$data['created_by'] = $session->getUser()->getId();
					
					$model->setUsername( $session->getUser()->getUsername() );
				}

				$data['title'] = ucfirst($postData['type']);//$postData['title'];
				$data['type'] = $postData['type'];
				$data['note'] = $postData['note'];
				
				try {
					$model->addData( $data );
					
                	$model->save();
	            } catch (Exception $e) {
	                $session->addError($e->getMessage());
	            }
	            
	            $this->getResponse()->setBody($this->getLayout()->createBlock('adminnotes/page_note')
					->setTemplate('iceberg/adminnotes/note.phtml')
					->setNote( Mage::getModel('adminnotes/note')->load($model->getNoteId()) )
					->setDoNotHide(true)
					->toHtml());
				
			}            
        }
	}
	
	/**
     * Called via an AJAX request
     */
	public function deleteAction()
	{
		$postData = $this->getRequest()->getPost();
		
		if( isset( $postData['note_id'] ) ) {
			$model = Mage::getModel('adminnotes/note');
			$model->load( $postData['note_id'] );
			
			
			if( $model->isDeletable() ){
				$model->deleteRelations();
				$model->delete();
			}else{
				echo 'Access Denied';
			}
		}
	}
	
	public function newAction(){
		$this->getResponse()->setBody($this->getLayout()->createBlock('adminnotes/page_new')
			->setTemplate('iceberg/adminnotes/new.phtml')
			->toHtml());
	}
	
	public function setStatusAction()
	{
		if (!$postData = $this->getRequest()->getPost()) {
			//ERROR
			return;
		}
		if( isset( $postData['id'] ) && isset( $postData['status'] ) ) {
			$model = Mage::getModel('adminnotes/note');
			
			$model->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
			$model->load( $postData['id'] );
				
			if( !$model->getId() ){
				//Error			
				return;
			}

			try{
			
				$model->loadStatus();
				$model->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
				
				$model->updateStatus( $postData['status'] );
				
			}catch( Exception $e ){
				die( $e->getMessage() );
			}
			
			$this->getResponse()->setBody($this->getLayout()->createBlock('adminnotes/page_note')
					->setTemplate('iceberg/adminnotes/note.phtml')
					->setNote( $model )
					->toHtml());
		}
	}
	
	public function massDeleteAction()
	{
		$noteIds = $this->getRequest()->getPost('note_ids', array());
		
		if( !is_array( $noteIds ) )
		{
			$noteIds = array( $noteIds );
		}
		
        $count = 0;
        
        foreach ($noteIds as $noteId) {
	        $model = Mage::getModel('adminnotes/note')->load($noteId);
			
			if ($model && $model->getId() && $model->isDeletable() )
			{
				// Delete Label
				$model->deleteRelations();
				$model->delete();

				$count++;
			}
        }

        if ($count) {
            $this->_getSession()->addSuccess($this->__('%s notes(s) successfully deleted', $count));
        }else{
        	$this->_getSession()->addError($this->__('No notes were deleted.'));
        }
        $this->_redirect('*/*/');
	}
	
	public function massHideAction()
	{
		$noteIds = $this->getRequest()->getPost('note_ids', array());
		
		if( !is_array( $noteIds ) )
		{
			$noteIds = array( $noteIds );
		}
		
        $count = 0;
        
        foreach ($noteIds as $noteId) {
	        $model = Mage::getModel('adminnotes/note')->load($noteId);
			
			if ($model && $model->getId() && $model->isEditable() )
			{
				try{
					$model->loadStatus();
					$model->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
					
					$model->updateStatus( 1 );
					
					$count++;
				}catch( Exception $e ){
					die( $e->getMessage() );
				}
			}
        }

        if ($count) {
            $this->_getSession()->addSuccess($this->__('%s notes(s) successfully hidden', $count));
        }else{
        	$this->_getSession()->addError($this->__('No notes were changed.'));
        }
        $this->_redirect('*/*/');
	}
	
	public function massUnhideAction()
	{
		$noteIds = $this->getRequest()->getPost('note_ids', array());
		
		if( !is_array( $noteIds ) )
		{
			$noteIds = array( $noteIds );
		}
		
        $count = 0;
        
        foreach ($noteIds as $noteId) {
	        $model = Mage::getModel('adminnotes/note')->load($noteId);
			
			if ($model && $model->getId() && $model->isEditable() )
			{
				try{
				
					$model->loadStatus();
					$model->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() );
					
					$model->updateStatus( 0 );
					
					$count++;
				}catch( Exception $e ){
					die( $e->getMessage() );
				}
			}
        }

        if ($count) {
            $this->_getSession()->addSuccess($this->__('%s notes(s) successfully unhidden', $count));
        }else{
        	$this->_getSession()->addError($this->__('No notes were changed.'));
        }
        $this->_redirect('*/*/');
	}
	
	public function massTypeAction()
	{
		$noteIds = $this->getRequest()->getPost('note_ids', array());
		$type = $this->getRequest()->getPost('type' , '' );
		
		if( !is_array( $noteIds ) )
		{
			$noteIds = array( $noteIds );
		}
		
        $count = 0;
        
        foreach ($noteIds as $noteId) {
	        $model = Mage::getModel('adminnotes/note')->load($noteId);
			
			if ($model && $model->getId() && $model->isEditable() )
			{
				// Delete Label
				$model->setType( $type );
				$model->save();

				$count++;
			}
        }

        if ($count) {
            $this->_getSession()->addSuccess($this->__('%s notes(s) successfully updated', $count));
        }else{
        	$this->_getSession()->addError($this->__('No notes were changed.'));
        }
        $this->_redirect('*/*/');
	}
}