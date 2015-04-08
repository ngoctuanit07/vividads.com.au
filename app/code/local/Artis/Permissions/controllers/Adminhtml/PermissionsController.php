<?php

class Artis_Permissions_Adminhtml_PermissionsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('permissions/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('permissions/permissions')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('permissions_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('permissions/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('permissions/adminhtml_permissions_edit'))
				->_addLeft($this->getLayout()->createBlock('permissions/adminhtml_permissions_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('permissions')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('permissions/permissions');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('permissions')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('permissions')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('permissions/permissions');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $permissionsIds = $this->getRequest()->getParam('permissions');
        if(!is_array($permissionsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($permissionsIds as $permissionsId) {
                    $permissions = Mage::getModel('permissions/permissions')->load($permissionsId);
                    $permissions->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($permissionsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $permissionsIds = $this->getRequest()->getParam('permissions');
        if(!is_array($permissionsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($permissionsIds as $permissionsId) {
                    $permissions = Mage::getSingleton('permissions/permissions')
                        ->load($permissionsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($permissionsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'permissions.csv';
        $content    = $this->getLayout()->createBlock('permissions/adminhtml_permissions_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'permissions.xml';
        $content    = $this->getLayout()->createBlock('permissions/adminhtml_permissions_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
    public function loadpermissionAction()
    {
	$user_id = $_REQUEST['user_id'];
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        	
	$temptableTask=Mage::getSingleton('core/resource')->getTableName('permission_task_list');
	$sqlHoli = $connectionRead->select()
                    ->from($temptableTask, array('*'))
                    ->where('task_status=?','1');
	$chkselect = $connectionRead->fetchAll($sqlHoli);
	
	$temptableUserTask=Mage::getSingleton('core/resource')->getTableName('user_task_relation');
	$sqlUserTask = $connectionRead->select()
                    ->from($temptableUserTask, array('*'))
                    ->where('user_id=?', $user_id);
	$chkUserTask = $connectionRead->fetchAll($sqlUserTask);
	
	
	//$all_permission = '';
	
	foreach($chkUserTask as $usertask)
	{
		$all_permission[$usertask['task_id']] = $usertask['task_id'];	
	}
		
	echo '<table class="taskpermission">';
	
	foreach($chkselect as $permission)
	{
		$checked = '';
		
		if(in_array($permission['task_id'], $all_permission))
		$checked = 'checked';
		
	    echo '<tr>
			<td>
				<input type="checkbox" name="permission['.$permission['task_id'].']" value="'.$permission['task_id'].'" '.$checked.'/>
			</td>
			<td>
				'.$permission['task_name'].'
			</td>
		</tr>';
	}
	echo '</table>';
    }
    
    public function permissionsaveAction()
    {
	extract($_REQUEST);
	//print_r($_REQUEST);
	//exit;
	
	Mage::getSingleton('core/session')->setPermissionuser($user);
	
	$temptableUser=Mage::getSingleton('core/resource')->getTableName('admin_role');
	$temptableUserTask=Mage::getSingleton('core/resource')->getTableName('user_task_relation');
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$select = $connectionRead->select()
		->from($temptableUser, array('*'))
		->where('parent_id=?',$roleid);
	$result = $connectionRead->fetchAll($select);
                
	
	if($result)
	{
		foreach($result as $user)
		{
			
			
			//$sqlUserTask="DELETE FROM ".$temptableUserTask." WHERE user_id = '".$user['user_id']."' ";
			//$chkUserTask = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlUserTask);
			
			$connectionWrite->beginTransaction();
			$condition = array($connectionWrite->quoteInto('user_id=?', $user['user_id']));
			$connectionWrite->delete($temptableUserTask, $condition);
			$connectionWrite->commit();
			
			foreach($permission as $key=>$value)
			{
				$connectionWrite->beginTransaction();
				$data = array();
				$data['task_id']= $value;
				$data['user_id']=$user['user_id'];
				$data['role_id']=$roleid;
				$connectionWrite->insert($temptableUserTask, $data);
				$connectionWrite->commit();
				
				//$sqlUserTask="INSERT INTO ".$temptableUserTask." SET user_id = '".$user['user_id']."', task_id = '".$value."', role_id = '".$roleid."'";
				//$chkUserTask = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlUserTask);
			}
		}
		
	}
	
			
	//$sqlUserTask="DELETE FROM  ".$temptableUserTask." WHERE user_id = '0' AND role_id = '".$roleid."'";
	//$chkUserTask = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlUserTask);
	
	$connectionWrite->beginTransaction();
	$sqlUserTask = array($connectionWrite->quoteInto('user_id=?', 0), $connectionWrite->quoteInto('role_id=?', $roleid));
	$connectionWrite->delete($temptableUserTask, $sqlUserTask);
	$connectionWrite->commit();
	
	foreach($permission as $key=>$value)
	{
		$connectionWrite->beginTransaction();
		$data = array();
		$data['task_id']= $value;
		$data['user_id']=0;
		$data['role_id']=$roleid;
		$connectionWrite->insert($temptableUserTask, $data);
		$connectionWrite->commit();
		
		//$sqlUserTask="INSERT INTO ".$temptableUserTask." SET user_id = '0', task_id = '".$value."', role_id = '".$roleid."'";
		//$chkUserTask = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlUserTask);
	}
	
	
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('permissions/permissions')));
	mage::helper('AdminLogger')->updatelog($roleid,'Edit Role Permissions');
	
	$this->_getSession()->addSuccess(
                    $this->__('Change this user permission now.')
                );
	
	//$this->_redirect('*/*/index');
	$this->_redirect('adminhtml/permissions_role/editrole/', array('rid' => $roleid));
    }
}