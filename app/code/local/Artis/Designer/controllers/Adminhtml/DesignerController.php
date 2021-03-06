<?php

class Artis_Designer_Adminhtml_DesignerController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		
		
		$this->loadLayout()
			->_setActiveMenu('designer/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('designer/designer')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('designer_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('designer/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('designer/adminhtml_designer_edit'))
				->_addLeft($this->getLayout()->createBlock('designer/adminhtml_designer_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('designer')->__('Item does not exist'));
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
	  			
	  			
			$model = Mage::getModel('designer/designer');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('designer')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('designer')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('designer/designer');
				 
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
        $designerIds = $this->getRequest()->getParam('designer');
        if(!is_array($designerIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($designerIds as $designerId) {
                    $designer = Mage::getModel('designer/designer')->load($designerId);
                    $designer->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($designerIds)
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
        $designerIds = $this->getRequest()->getParam('designer');
        if(!is_array($designerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($designerIds as $designerId) {
                    $designer = Mage::getSingleton('designer/designer')
                        ->load($designerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($designerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'designer.csv';
        $content    = $this->getLayout()->createBlock('designer/adminhtml_designer_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'designer.xml';
        $content    = $this->getLayout()->createBlock('designer/adminhtml_designer_grid')
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
    
    public function allcommentAction()
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	extract($_REQUEST);
	
	$url2 = Mage::helper('adminhtml')->getUrl('designer/adminhtml_designer/download');
	
	echo ' <table class="tooltipcom">
            <tr>
	     <td>
	      <div class="allcomment">'
	      ;
	
	$temptableItem=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
	//$sqlItem="SELECT *,DATE_FORMAT(postdate,'%D %M, %Y') AS p_date FROM  ".$temptableItem." WHERE parent_id ='".$id."' ORDER BY entity_id DESC ";
	$sqlItem = $connectionRead->select()
		->from($temptableItem, array("* ,DATE_FORMAT(postdate,'%D %M, %Y') AS p_date"))
		->where('parent_id=?',$id)
		->order('entity_id DESC');
	$chkItems = $connectionRead->fetchAll($sqlItem);
	foreach($chkItems as $chkItem)
	{
		if($chkItem['user_type'] == 'customer')
		{
			$customerData = Mage::getModel('customer/customer')->load($chkItem['user_id']);
			$name = $customerData->getName();
			$css = 'style="color:#8A4B08;"';
		}
		else if($chkItem['user_type'] == 'admin')
		{
		    $getadmin=Mage::getModel('admin/user')->load($chkItem['user_id']);
		    $name=$getadmin->getName();
		    $css = '"';
		    
		}
		
		echo '<div class="comtotal"><div '.$css.'><strong>'.$chkItem['p_date'].' - <span>'.$name.'</span></strong></div><div><a href="'.str_replace('//s','/admin/s',$url2).'file/'.$chkItem['file'].'/'.'">'.$chkItem['file'].'</a>  <br/>'.$chkItem['comment'].'</div></div>';
	       
	}
	echo      '</div>
	     </td>
	    </tr>
		</table>';
		Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
		mage::helper('AdminLogger')->updatelog($id,'Add Comment in Design');
    }
    
    public function addcommentAction()
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$user = Mage::getSingleton('admin/session');
	$user_id = $user->getUser()->getUserId();
	
	$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
	//$sqlComment="INSERT INTO  ".$temptableComment." SET parent_id = '".$entity_id."', comment = '".$comment."' , user_id = '".$user_id."', user_type = 'admin', status ='Waiting for approval', postdate = NOW() ";
	//$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
	$connectionWrite->beginTransaction();
	$data = array();
	$data['parent_id']= $entity_id;
	$data['comment']=$comment;
	$data['user_id']=$user_id;
	$data['user_type']='admin';
	$data['status']='Waiting for approval';
	$data['postdate']=NOW();
	$connectionWrite->insert($temptableComment, $data);
	$connectionWrite->commit();
	
	$lastInsertId = $connectionWrite->fetchOne('SELECT last_insert_id()');
	
	$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
	//$sqlDesign="UPDATE  ".$temptableDesign." SET  status='Waiting for approval' WHERE entity_id = '".$entity_id."' ";
	//$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
	$connectionWrite->beginTransaction();
	$data = array();
	$data['status'] = 'Waiting for approval';
	$where = $connectionWrite->quoteInto('entity_id =?', $entity_id);
	$connectionWrite->update($temptableDesign, $data, $where);
	$connectionWrite->commit();
	
	if(isset($_FILES['item_file']['name']) and (file_exists($_FILES['item_file']['tmp_name']))) {
		
		 $file_name=$_FILES['item_file']['name'];
		 
		 $expFilename=explode(".",$file_name);
		 $fileNameVal=time().".".end($expFilename);
		 
		 
		 $mediaPath=Mage::getBaseDir('media') . DS ;
		 //$path2 = $mediaPath.'upload_image/'.$fileNameVal;
		 $path2 = $mediaPath.'design/'.$fileNameVal;
		 chmod($path2,0777);
		 $filepath = $fileNameVal;
		 
		 //file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
		 if(move_uploaded_file($_FILES['item_file']['tmp_name'],$path2))
		 {
			$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
			//$sqlComment="UPDATE  ".$temptableComment." SET  file ='".$fileNameVal."' WHERE entity_id = '".$lastInsertId."' ";
			//$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
			$connectionWrite->beginTransaction();
			$data = array();
			$data['file'] = $fileNameVal;
			$where = $connectionWrite->quoteInto('entity_id =?', $lastInsertId);
			$connectionWrite->update($temptableComment, $data, $where);
			$connectionWrite->commit();
		   
		 }
	     }
	     
		//$sqlDesign1="SELECT * FROM  ".$temptableDesign." WHERE entity_id = '".$entity_id."' ";
		$sqlDesign1 = $connectionRead->select()
			->from($temptableDesign, array('*'))
			->where('entity_id=?',$entity_id);
		$chkDesign1 = $connectionWrite->fetchAll($sqlDesign1);
		
		$temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
                //$sqlDesign="SELECT * FROM  ".$temptableDesign." WHERE order_id = '".$chkDesign1[0]['order_quote_id']."' AND item_id ='".$chkDesign1[0]['item_id']."' ";
                $sqlDesign = $connectionRead->select()
			->from($temptableDesign, array('*'))
			->where("order_id = '".$chkDesign1[0]['order_quote_id']."' AND item_id ='".$chkDesign1[0]['item_id']."' ");
		$chkDesign = $connectionWrite->fetchAll($sqlDesign);
		
		$temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                //$sqlItem="SELECT * FROM  ".$temptableItem." WHERE item_id ='".$chkDesign1[0]['item_id']."' ";
                $sqlItem = $connectionRead->select()
			->from($temptableItem, array('*'))
			->where('item_id =?', $chkDesign1[0]['item_id']);
		$chkItem = $connectionWrite->fetchAll($sqlItem);
		
		if($chkDesign1[0]['proof_type'] == 'order')
		{
		    $order = Mage::getModel('sales/order')->load($chkDesign1[0]['order_quote_id']);
		    $id = $order->getIncrementId();
		    $url= Mage::helper("adminhtml")->getUrl('sales/order/view/order_id/'.$chkDesign1[0]['order_quote_id']);
		    $url = str_replace('p//s','p/admin/s',$url);
		    
		    $url3= Mage::getBaseUrl().'sales/order/view/order_id/'.$chkDesign1[0]['order_quote_id'];
		    
		}
		else if($chkDesign1[0]['proof_type'] == 'quote')
		{
		    $quote = Mage::getModel("Quotation/Quotation")->load($chkDesign1[0]['order_quote_id']);
		    $id = $quote->getIncrementId();
		    $url= Mage::helper("adminhtml")->getUrl('Quotation/Admin/edit/quote_id/'.$chkDesign1[0]['order_quote_id']);
		    
		    $url3= Mage::getBaseUrl().'Quotation/Quote/View/quote_id/'.$chkDesign1[0]['order_quote_id'];
		   
		}
		
		$user = $user->getUser();
		
		$customerData = Mage::getModel('customer/customer')->load($chkDesign1[0]['user_id']);
		
		$sales_email = Mage::getStoreConfig('trans_email/ident_sales/email'); 
		
		$mail = Mage::getModel('core/email');
		//$mail->setToName('test');
		$mail->setToEmail($customerData->getEmail());
		$mail->setBody('Hi,<br/><br/> '.$user->getName().' has been given a feedback for the product '.$chkItem[0]['name'].' and '.$chkDesign1[0]['proof_type'].' <a href="'.$url3.'">'.$id.'</a>. Please check this.');
		$mail->setSubject('Please check the customer design in Aceexhinits');
		$mail->setFromEmail($user->getEmail());
		//$mail->setFromName($customerData->getName());
		$mail->setType('html');// YOu can use Html or text as Mail format
		
		try {
			$mail->send();
		}
		catch (Exception $e) {
		//Mage::getSingleton('core/session')->addError('Unable to send.');
		}
		
		$mail1 = Mage::getModel('core/email');
		//$mail->setToName('test');
		$mail1->setToEmail($sales_email);
		$mail1->setBody('Hi,<br/><br/> '.$user->getName().' has been given a feedback for the product '.$chkItem[0]['name'].' and '.$chkDesign1[0]['proof_type'].' <a href="'.$url.'">'.$id.'</a>. Please check this.');
		$mail1->setSubject('Please check the customer design in Aceexhinits');
		$mail1->setFromEmail($user->getEmail());
		//$mail1->setFromName($customerData->getName());
		$mail1->setType('html');// YOu can use Html or text as Mail format
		
		try {
			$mail1->send();
		}
		catch (Exception $e) {
		//Mage::getSingleton('core/session')->addError('Unable to send.');
		}
	
		
	$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');   
	//$sqlDesign1="SELECT * FROM  ".$temptableDesign." WHERE entity_id = '".$entity_id."' ";
	//$chkDesign1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlDesign1);
	$sqlDesign1 = $connectionRead->select()
		->from($temptableDesign, array('*'))
		->where('entity_id=?',$entity_id);
	$chkDesign1 = $connectionRead->fetchAll($sqlDesign1);
	
	if($chkDesign1[0]['proof_type'] == 'order')
	{
		Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
		mage::helper('AdminLogger')->updatelog($order_id,'Add Feedback in Order');
		
		$url1 = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view/order_id/".$order_id);
		$url1 = str_replace('p//s','p/adminhtml/s',$url1);
		Mage::log($url1); //To check if URL is correct (and it is correct)
		Mage::app()->getResponse()->setRedirect($url1);
	}
	else if($chkDesign1[0]['proof_type'] == 'quote')
	{
		Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
		mage::helper('AdminLogger')->updatelog($order_id,'Add Feedback in Quote');
		
		$url1 = Mage::helper('adminhtml')->getUrl("Quotation/Admin/edit/quote_id/".$order_id);
		$url1 = str_replace('p//s','p/admin/s',$url1);
		Mage::log($url1); //To check if URL is correct (and it is correct)
		Mage::app()->getResponse()->setRedirect($url1);
	}
	
    }
    
    public function deletedesignAction()
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$user = Mage::getSingleton('admin/session');
	$user_id = $user->getUser()->getUserId();
	
	$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
	//$sqlComment="DELETE FROM  ".$temptableComment." WHERE parent_id = '".$id."' ";
	//$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
	$connectionWrite->beginTransaction();
	$sqlComment = array($connectionWrite->quoteInto('parent_id=?', $id));
	$connectionWrite->delete($temptableComment, $sqlComment);
	$connectionWrite->commit();
	
	$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
	//$sqlDesign="DELETE FROM  ".$temptableDesign." WHERE  entity_id = '".$id."' ";
	//$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
	
	$connectionWrite->beginTransaction();
	$sqlDesign = array($connectionWrite->quoteInto('entity_id=?', $id));
	$connectionWrite->delete($temptableDesign, $sqlDesign);
	$connectionWrite->commit();
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	mage::helper('AdminLogger')->updatelog($id,'Delete Design In Quote');
	
	echo ' <tr class="headcls">
                    <th> Date </th>
                    <th> Item </th>
                    <th> Comment</th>
                    <th> Design File</th>
                    <th> Status </th>
		    <th> Action </th>';
	$roleId = implode('', Mage::getSingleton('admin/session')->getUser()->getRoles());
    
	//Get the role name
	$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
	if($roleName == 'Administrators')
	{
     
		echo '<th> Assign To </th>';
     
	}
		    
                echo '</tr>';
		
		
                    $temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
                    //$sqlDesign="SELECT *,DATE_FORMAT(postdate,'%D %M, %Y') AS p_date FROM  ".$temptableDesign." WHERE order_quote_id ='".$orderid."' ORDER BY item_id ";
                    $sqlDesign = $connectionRead->select()
				->from($temptableDesign, array("* , DATE_FORMAT(postdate,'%D %M, %Y') AS p_date"))
				->where('order_quote_id=?',$orderid)
				->order('item_id');
		    $chkDesigns = $connectionRead->fetchAll($sqlDesign);
                    
                    foreach($chkDesigns as $chkDesign)
                    {
                        
                        $temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                       // $sqlItem="SELECT * FROM  ".$temptableItem." WHERE item_id ='".$chkDesign['item_id']."'  ";
                       // $chkItem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);
			$sqlItem = $connectionRead->select()
					->from($temptableItem, array('*'))
					->where('item_id=?', $chkDesign['item_id']);
			$chkItem = $connectionRead->fetchAll($sqlItem);
                        
                        //$_Product = Mage::getModel('catalog/product')->load($chkItem['product_id']);
              
                echo '<tr>
                    <td>'.$chkDesign['p_date'].'</td>
                    <td>'.$chkItem[0]['name'].'</td>
                    <td>'.$chkDesign['comment'].'</td>
                    <td>'.$chkDesign['file'].'</td>
                    <td>'.$chkDesign['status'].'</td>
		    <td> <a class="shoedetails" onclick="allcomment(\''.$chkDesign['entity_id'].'\',\''.$orderid.'\')">Show Details</a> <a onclick="deletedesign(\''.$chkDesign['entity_id'].'\',\''.$orderid.'\')" style="cursor:pointer;">Delete</a> </td>
		    ';
		    
		  if($roleName == 'Administrators')
		      {
		   
				echo '<td>
				 <select name="ot_author_user" id="ot_author_user" onchange="assignto(\''.$chkDesign['item_id'].'\',\''.$orderid.'\',this.value);">
				  <option value="">Assign To</option>';
				   
				   
				       $temptableService=Mage::getSingleton('core/resource')->getTableName('design_service');
				       //$sqlService="SELECT * FROM  ".$temptableService." WHERE order_id ='".$orderid."' AND item_id = '".$chkDesign['item_id']."'  ";
				       //$chkService = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlService);
				   
					$sqlService = $connectionRead->select()
						->from($temptableService, array('*'))
						->where("order_id ='".$orderid."' AND item_id = '".$chkDesign['item_id']."'");
					$chkService = $connectionRead->fetchAll($sqlService);
					
				       $adminUserModel = Mage::getModel('admin/user');
				       $userCollection = $adminUserModel->getCollection()->load();
				       foreach($userCollection as $user)
				       {
					   $selected = '';
					   if($user->getId() == $chkService[0]['assign_to'])
					   $selected = 'selected';
					   
					   echo '<option value="'.$user->getId().'" '.$selected.' >'.$user->getUsername().'</option>';
				       }
				 
				echo '</select>
				</td>';
		   
		    
		      }
		    
               echo'</tr>';
                 }
	
    }
    
    public function assigntoAction()
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$user = Mage::getSingleton('admin/session');
	$user_id = $user->getUser()->getUserId();
	
	$temptableService=Mage::getSingleton('core/resource')->getTableName('design_service');
	//$sqlService="UPDATE  ".$temptableService." SET assign_to = '".$userid."' WHERE item_id = '".$itemid."' AND order_id ='".$orderid."' AND type = '".$type."' ";
	//$chkService = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlService);
	
	
	$sqlDesign = $connectionRead->select()
		    ->from($temptableService, array("*"))
		    ->where("item_id= '".$itemid."' AND order_id= '".$orderid."' AND type='".$type."'");
	$chkDesigns = $connectionRead->fetchAll($sqlDesign);
	
	if(count($chkDesigns) > 0)
	{
		$connectionWrite->beginTransaction();
		$data = array();
		$data['assign_to'] = $userid;
		$where = $connectionWrite->quoteInto("item_id='".$itemid."' AND order_id='".$orderid."' AND type='".$type."'");
		$connectionWrite->update($temptableService, $data, $where);
		$connectionWrite->commit();
	}
	else{
		$connectionWrite->beginTransaction();
		$data = array();
		$data['order_id'] = $orderid;
		$data['item_id'] = $itemid;
		$data['type'] = $type;
		$data['revision_number'] = 100;
		$data['assign_to'] = $userid;
		$data['postdate'] = Now();
		$connectionWrite->insert($temptableService, $data);
		$connectionWrite->commit(); 
		
	}
				
	
	
	/*
	$connectionWrite->beginTransaction();
	$data = array();
	$data['assign_to'] = $userid;
	$where = $connectionWrite->quoteInto("item_id=? AND order_id=? AND type=?", $itemid, $orderid, $type);
	$connectionWrite->update($temptableService, $data, $where);
	$connectionWrite->commit();
	*/
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	mage::helper('AdminLogger')->updatelog($orderid,'Assign the Designer In '.$type);
	
	
	
    }
    
    public function setuserAction()
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	//print_r($user_id);exit;
	extract($_REQUEST);
	
	$temptableDesigner=Mage::getSingleton('core/resource')->getTableName('catalog_product_designer');
	//$sqlDesigner="SELECT * FROM ".$temptableDesigner." WHERE product_id = '".$product_id."' ";
	//$chkDesigner = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlDesigner);
	$sqlDesigner = $connectionRead->select()
			->from($temptableDesigner, array('*'))
			->where('product_id=?',$product_id);
	$chkDesigner = $connectionRead->fetchAll($sqlDesigner);
	
	if(count($chkDesigner) > 0)
	{
		//$sqlDesigner="UPDATE ".$temptableDesigner." SET user_id = '".$user_id."' WHERE product_id = '".$product_id."' ";
		//$chkDesigner = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesigner);
		$connectionWrite->beginTransaction();
		$data = array();
		$data['user_id'] = $user_id;
		$where = $connectionWrite->quoteInto('product_id =?', $product_id);
		$connectionWrite->update($temptableDesigner, $data, $where);
		$connectionWrite->commit();
	}
	else if(count($chkDesigner) == 0)
	{
		//$sqlDesigner="INSERT INTO ".$temptableDesigner." SET user_id = '".$user_id."' , product_id = '".$product_id."' ";
		//$chkDesigner = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesigner);
		$connectionWrite->beginTransaction();
		$data = array();
		$data['user_id']= $user_id;
		$data['product_id']=$product_id;
		$connectionWrite->insert($temptableDesigner, $data);
		$connectionWrite->commit();
	}
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	mage::helper('AdminLogger')->updatelog($product_id,'Assign the Designer In Product');
	
	$url = Mage::helper('adminhtml')->getUrl("adminhtml/catalog_product/edit/id/".$product_id);
	$url = str_replace('p//c','p/adminhtml/c',$url);
        Mage::log($url); //To check if URL is correct (and it is correct)
        Mage::app()->getResponse()->setRedirect($url);
	
    }
    
    public function downloadAction()
    {
        $file_path=Mage::getBaseDir('media').'/design/'.$this->getRequest()->getParam('file');
    
    
    //Call the download function with file path,file name and file type
    //download_file($file_path, ''.$_REQUEST['file'].'', 'text/plain');
    $this->download_file($file_path, ''.$this->getRequest()->getParam('file').'', 'text/plain');
    
    
    }
    
    public function download_file($file, $name, $mime_type='')
    {
       
        $size = filesize($file);
        $name = rawurldecode($name);
       
        $known_mime_types=array(
               "pdf" => "application/pdf",
               "txt" => "text/plain",
               "html" => "text/html",
               "htm" => "text/html",
               "exe" => "application/octet-stream",
               "zip" => "application/zip",
               "doc" => "application/msword",
               "xls" => "application/vnd.ms-excel",
               "ppt" => "application/vnd.ms-powerpoint",
               "gif" => "image/gif",
               "png" => "image/png",
               "jpeg"=> "image/jpg",
               "jpg" =>  "image/jpg",
               "php" => "text/plain"
        );
       
        if($mime_type==''){
                $file_extension = strtolower(substr(strrchr($file,"."),1));
                if(array_key_exists($file_extension, $known_mime_types)){
                       $mime_type=$known_mime_types[$file_extension];
                } else {
                       $mime_type="application/force-download";
                };
        };
       
        @ob_end_clean(); 
       
        // required for IE, otherwise Content-Disposition may be ignored
        if(ini_get('zlib.output_compression'))
         ini_set('zlib.output_compression', 'Off');
       
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="'.$name.'"');
        header("Content-Transfer-Encoding: binary");
        header('Accept-Ranges: bytes');
        header("Cache-control: private");
        header('Pragma: private');
        readfile($file); 
    }
    
    //design create  for order or quote from admin
    public function adddesignAction()
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        extract($_REQUEST);
	
	$order = Mage::getModel('sales/order')->load($orderid);
	
	if(!empty($item))
	{
	
	    if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
	    {
		$store_id = Mage::getModel('core/store')->load($code)->getId();
	    }
	    elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
	    {
		$website_id = Mage::getModel('core/website')->load($code)->getId();
		$store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
	    }
	    else // default level
	    {
		$store_id = 0;
	    }
	    
	    $user_id = Mage::getSingleton('customer/session')->getId();
	    
	$user1 = Mage::getSingleton('admin/session');
	$user_id1 = $user1->getUser()->getUserId();
	    
	    foreach($item as $key => $value)
	    {
		if($value != '')
		{
		$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
		//$sqlDesign="INSERT INTO  ".$temptableDesign." SET store_id = '".$store_id."', order_quote_id ='".$orderid."', user_id = '".$user_id1."', user_type = 'admin', item_id = '".$value."', status ='New', comment = '".$comment[$key]."', proof_type = '".$type."', postdate = NOW() ";
		//$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
		$connectionWrite->beginTransaction();
		$data = array();
		$data['store_id']= $store_id;
		$data['order_quote_id']=$orderid;
		$data['user_id']=$user_id1;
		$data['user_type']='admin';
		$data['item_id']=$value;
		$data['status']='New';
		$data['comment']=$comment[$key];
		$data['proof_type']=$type;
		$data['postdate']=Now();
		$connectionWrite->insert($temptableDesign, $data);
		$connectionWrite->commit(); 
		
		$lastInsertId = $connectionWrite->fetchOne('SELECT last_insert_id()'); 
		
		$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
		//$sqlComment="INSERT INTO  ".$temptableComment." SET parent_id = '".$lastInsertId."', comment = '".$comment[$key]."' , user_id = '".$user_id1."', user_type = 'admin', status ='New', postdate = NOW() ";
		//$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
		
		$connectionWrite->beginTransaction();
		$data = array();
		$data['parent_id']= $lastInsertId;
		$data['comment']=$comment[$key];
		$data['user_id']=$user_id1;
		$data['user_type']='admin';
		$data['status']='New';
		$data['postdate']=Now();
		$connectionWrite->insert($temptableComment, $data);
		$connectionWrite->commit();
		
		$lastInsertId1 = $connectionWrite->fetchOne('SELECT last_insert_id()');
		
		if(isset($_FILES['item_file']['name'][$key]) and (file_exists($_FILES['item_file']['tmp_name'][$key]))) {
		       
			$file_name=$_FILES['item_file']['name'][$key];
			
			$expFilename=explode(".",$file_name);
			$fileNameVal=time().".".end($expFilename);
			
			
			$mediaPath=Mage::getBaseDir('media') . DS ;
			//$path2 = $mediaPath.'upload_image/'.$fileNameVal;
			$path2 = $mediaPath.'design/'.$fileNameVal;
			chmod($path2,0777);
			$filepath = $fileNameVal;
			
			//file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
			if(move_uploaded_file($_FILES['item_file']['tmp_name'][$key],$path2))
			{
			    
			    $temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
			    //$sqlDesign="UPDATE  ".$temptableDesign." SET  file = '".$fileNameVal."' WHERE entity_id ='".$lastInsertId."' ";
			    //$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
			    $connectionWrite->beginTransaction();
				$data = array();
				$data['file'] = $fileNameVal;
				$where = $connectionWrite->quoteInto('entity_id =?', $lastInsertId);
				$connectionWrite->update($temptableDesign, $data, $where);
				$connectionWrite->commit();
			    
			    $temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
			    //$sqlComment="UPDATE  ".$temptableComment." SET file ='".$fileNameVal."' WHERE entity_id ='".$lastInsertId1."' ";
			   // $chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
			   $connectionWrite->beginTransaction();
				$data = array();
				$data['file'] = $fileNameVal;
				$where = $connectionWrite->quoteInto('entity_id =?', $lastInsertId1);
				$connectionWrite->update($temptableComment, $data, $where);
				$connectionWrite->commit();
			    
			}
		    }
		    
		    $temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
		   // $sqlDesign="SELECT * FROM  ".$temptableDesign." WHERE order_id = '".$orderid."' AND item_id ='".$value."' ";
		    $sqlDesign = $connectionRead->select()
				->from($temptableDesign, array('*'))
				->where("order_id = '".$orderid."' AND item_id ='".$value."' ");
		    
		    $temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		    //$sqlItem="SELECT * FROM  ".$temptableItem." WHERE item_id ='".$value."' ";
		    $sqlItem = $connectionRead->select()
				->from($temptableItem, array('*'))
				->where('item_id=?',$value);
		    $chkItem = $connectionRead->fetchAll($sqlItem);
		    
		    if($type == 'order')
		    {
			$order = Mage::getModel('sales/order')->load($orderid);
			$id = $order->getIncrementId();
			$url= Mage::helper("adminhtml")->getUrl('admin/sales_order/view/order_id/'.$orderid);
			$url = str_replace('/s','index.php/admin/s',$url);
			
			$url1= Mage::getBaseUrl().'sales/order/view/order_id/'.$orderid;
		    }
		    else if($type == 'quote')
		    {
			$quote = Mage::getModel("Quotation/Quotation")->load($orderid);
			$id = $quote->getIncrementId();
			$url= Mage::helper("adminhtml")->getUrl('Quotation/Admin/edit/quote_id/'.$orderid);
			$url = str_replace('/Quotation','/index.php/Quotation',$url);
			
			$url1= Mage::getBaseUrl().'Quotation/Quote/View/quote_id/'.$orderid;
		       
		    }
		    
		    $order = Mage::getModel('sales/order')->load($orderid);
		    
		    $user = Mage::getModel('admin/user')->load($chkDesign[0]['assign_to']);
		    
		    $customerData = Mage::getModel('customer/customer')->load($user_id);
		    
		    $sales_email = Mage::getStoreConfig('trans_email/ident_sales/email'); 
		    
		   // Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
		    
		    $mail = Mage::getModel('core/email');
		    //$mail->setToName('test');
		    $mail->setToEmail($order->getCustomerEmail());
		    $mail->setBody('Hi,<br/><br/> '.$order->getCustomerName().' has been uploaded a design for the product '.$chkItem[0]['name'].' and '.$type.' <a href="'.$url1.'">'.$id.'</a>. Please check this.');
		    $mail->setSubject('Please check the customer design in Aceexhinits');
		    $mail->setFromEmail($user->getEmail());
		    $mail->setFromName($user->getName());
		    $mail->setType('html');// YOu can use Html or text as Mail format
			    try {
		    $mail->send();
		    }
		    catch (Exception $e) {
		    //Mage::getSingleton('core/session')->addError('Unable to send.');
		    }
		    
		    $mail1 = Mage::getModel('core/email');
		    //$mail->setToName('test');
		    $mail1->setToEmail($sales_email);
		    $mail1->setBody('Hi,<br/><br/> '.$order->getCustomerName().' has been uploaded a design for the product '.$chkItem[0]['name'].' and '.$type.' <a href="'.$url.'">'.$id.'</a>. Please check this.');
		    $mail1->setSubject('Please check the customer design in Aceexhinits');
		    $mail1->setFromEmail($order->getCustomerEmail());
		    $mail1->setFromName($order->getCustomerName());
		    $mail1->setType('html');// YOu can use Html or text as Mail format
			    try {
		    $mail1->send();
		    }
		    catch (Exception $e) {
		    //Mage::getSingleton('core/session')->addError('Unable to send.');
		    }
		}
	       
	    }
	    
		Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
		mage::helper('AdminLogger')->updatelog($orderid,'Add New Design In '.$type);
	
	}
	else
	{
	    $this->_getSession()->addError($this->__('Please select the item.'));
	}
          
	if($type == 'order')
	{
	    $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderid));
	}
	else if($type == 'quote')
	{
	    $this->_redirect('Quotation/Admin/edit', array('quote_id' => $orderid));
	   
	}  
        
    }
	
	
	/*--------------------------------------------------------------------------------*/		
	
	/*Writing data from chat file*/
	
	public function savechatAction(){
		//  echo 'here';
		
		
		
		/*Fetching the post variables*/
		 $_entity_id = 	$this->getRequest()->getPost('short_order_id');
		 $_item_id 	 = 	$this->getRequest()->getPost('item_id');
		 $_user_id 	 = 	$this->getRequest()->getPost('user_id');
		 $_file_path = 	$this->getRequest()->getPost('file_path');
		 $_proof_type =  $this->getRequest()->getPost('proof_type');
		 
			 
		 /*update the chat data in the database*/		 
		 $_resource = Mage::getSingleton('core/resource');
		 $_read= $_resource->getConnection('core_read');
		 $_write= $_resource->getConnection('core_write');
		 
		 $_designer_chat_table = $_resource->getTableName('task_designer_chat');			
		 $_select = $_read->select()
			   ->from($_designer_chat_table,array('entity_id','item_id','user_id','client_posted_time','designer_posted_time'))
			   ->where('entity_id=?',$_entity_id)
			   ->where('item_id=?',$_item_id);	
			   		
		 $_chat_status = $_read->fetchAll($_select);
		 
		
		 
		/*checking if the chat is being updated by client or designer*/
		 
		 if($_proof_type=='customer'){
			 
			 $_designer_posted_status=0;
			 $_client_posted_status=1;
			 $_client_posted_time = NOW();
			 $_designer_posted_time=$_chat_status[0]['designer_posted_time'];
			 
			 
			 }else{
			 $_designer_posted_status=1;
			 $_client_posted_status=0;
			 $_designer_posted_time= NOW();
			 $_client_posted_time=$_chat_status[0]['client_posted_time'];
				 
		  }
		
		// print_r( $_designer_posted_time.'='.$_client_posted_time);
		
		//exit;
		
		 if(count($_chat_status) >0){
			 /*update the chat if exists*/
			 
			$_data = array('designer_posted_status'=>$_designer_posted_status,
						   'client_posted_status'=>$_client_posted_status,
						   'designer_posted_time'=>$_designer_posted_time,
						   'client_posted_time'=>$_client_posted_time,		
							);
			$_where = 'entity_id='.$_entity_id.' AND
					   item_id='.$_item_id.'  ';				
							
			$_data_updatted = $_write->update($_designer_chat_table,$_data,$_where);	
			 
			 }else{
			/*insert new record if dont exists*/
			$_data = array('entity_id'=>$_entity_id,
						   'item_id'=>$_item_id,
						   'user_id'=>$_user_id,
						   'designer_posted_status'=>$_designer_posted_status,
						   'client_posted_status'=>$_client_posted_status,
						   'designer_posted_time'=>$_designer_posted_time,
						   'client_posted_time'=>$_client_posted_time,	
						   'file_path'=>$_file_path,
							);
			$_data_updatted = $_write->insert($_designer_chat_table,$_data);	 
				 
			}
		
		
		
		
		 /*fetching data via ajax post vars*/
		 $_chat_file = $_POST['file_path'];
		 $_chat_data = $_POST['chat_data'];
		 
		 
		 
		 
		 if(is_file($_chat_file)){
			 
		     	$_chat_file_name = $_chat_file;
				$_file = @fopen($_chat_file,'a');
				$_file = fwrite($_file,$_chat_data);
				$_file = fclose($_file);
				echo 'data updated';
			 }	 
		 
		} ///end of isset data

	/*Reading chat status from chat file*/
   
	public function getChatStatusAction(){
		 
		 /*Fetching the post variables*/
		 $_entity_id = 	$this->getRequest()->getPost('order_id');
		 $_item_id 	 = 	$this->getRequest()->getPost('item_id');
		 $_user_id 	 = 	$this->getRequest()->getPost('user_id');
		 $_file_path = 	$this->getRequest()->getPost('file_path');
		 $_proof_type =  $this->getRequest()->getPost('proof_type');
		 
			 
		 /*update the chat data in the database*/		 
		 $_resource = Mage::getSingleton('core/resource');
		 $_read= $_resource->getConnection('core_read');
		 $_write= $_resource->getConnection('core_write');
		 
		 $_designer_chat_table = $_resource->getTableName('task_designer_chat');			
		
		
		 $_select = $_read->select()
			   ->from($_designer_chat_table,array('entity_id','item_id','user_id','designer_posted_status','client_posted_status','client_posted_time','designer_posted_time'))
			   ->where('item_id=?',$_item_id)
			   ->where('entity_id=?',$_entity_id)
			   ;	
		
		 $_where = 'entity_id='.$_entity_id.' AND item_id'.$_item_id;	   		
		 $_chat_status = $_read->fetchAll($_select) ;
		 
		 $_designer_posted = $_chat_status[0]['designer_posted_status'];
		 $_client_posted = $_chat_status[0]['client_posted_status'];		 
		
		 // echo '$_client_posted'.$_client_posted;
		// exit;
		 $_post_status = 0;
		 
		 if($_client_posted==1){			 
		   /*fetching data via ajax post vars*/
		 $_chat_file = $_POST['file_path'];
		 $_chat_data = $_POST['chat_data'];
		 
			 if(is_file($_chat_file)){
			 
		     	$_chat_file_name = $_chat_file;
				$_file = @fopen($_chat_file,'r');
				$data = fread($_file,filesize($_chat_file));
				echo $data;
				$_file = fclose($_chat_file);			
					 }	
		 			}else{
			  echo $_post_status ;
			 }
		 
		} ///end of isset data			
		
	/*Reading data from chat file*/
	
	public function readChatAction(){
		 
		 /*fetching data via ajax post vars*/
		 $_chat_file = $_POST['file_path'];
		 $_chat_data = $_POST['chat_data'];
		 
		 if(is_file($_chat_file)){
			 
		     	$_chat_file_name = $_chat_file;
				$_file = @fopen($_chat_file,'r');
				$data = fread($_file,filesize($_chat_file));
				echo $data;
				$_file = fclose($_chat_file);
				
			 }	 
		 
		} ///end of isset data	
		
	/*Uploading  file*/
	
	public function uploadFileAction(){
		 
		/*taking connection for reading and writing data to tables*/
		$_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		 
		 /*fetching data via ajax post vars*/
		$_file_id = $this->getRequest()->getParam('fileid');
		$_user_id = $this->getRequest()->getParam('user_id');
		$_store_id = $this->getRequest()->getParam('store_id');
		$_order_quote_id = $this->getRequest()->getParam('short_order_id') ;
		$_file_type = $this->getRequest()->getParam('file_type') ;
		$_file_size = $this->getRequest()->getParam('file_size') ;
		$_user_type = $this->getRequest()->getParam('user_type');
		$_item_id = $this->getRequest()->getParam('item_id');;
		$_file_name = $this->getRequest()->getParam('filename');
		$_status = 'New';
		$_comments = '';
		$_post_date =NOW();		
		$_proof_type = $this->getRequest()->getParam('proof_type'); 
		
		
		// echo 'file id='.$_file_id.'<br/>';
		 //exit;
		/*SAVING to tables*/
		
		//$_items_in_order = Mage::getModel('sales/order')->loadByIncrementId($_order_id);			 
		//$_items_obj = $_items_in_order->getAllVisibleItems();	
	    
	    /*
		$_temptableDesign = Mage::getSingleton('core/resource')->getTableName('task_designer');
		/*
		$sqlDesign="INSERT INTO  ".$temptableDesign." SET store_id = '".$store_id."', order_quote_id ='".$order_id."', user_id = '".$user_id."',
		user_type = 'customer', item_id = '".$value."', status ='New', comment = '".$comment[$key]."', proof_type = '".$type."', postdate = NOW() ";
		$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
		*/
		
		
		$_connectionWrite->beginTransaction();
		
		$_data = array();
		
		$_data['store_id']= $_store_id;
		$_data['order_quote_id']=$_order_quote_id;
		$_data['file']= $_file_name;
		$_data['user_id']=$_user_id;
		$_data['user_type']=$_user_type;
		$_data['item_id'] = $_item_id;
		$_data['status']=$_status;
		$_data['comment'] = $_comments;
		$_data['file_type'] = $_file_type;
		$_data['file_size'] = $_file_size;
		$_data['comment'] = $_comments;
		$_data['proof_type'] = $_proof_type;
		$_data['postdate']= $_post_date;
		
		$_temptableDesign = Mage::getSingleton('core/resource')->getTableName('task_designer');
		
		/*find if entity id is already exists */
		
		$_entity_id_exists = $this->entry_existsAction($_file_id , $_order_quote_id, $_item_id,$_proof_type);
		
		/*if entity id exists then update either insert new entry */
		
		if($_entity_id_exists){
			
			$_connectionWrite->update($_temptableDesign, $_data,'entity_id='.$_file_id);
		
		}else{
			$_connectionWrite->insert($_temptableDesign, $_data);		
		}
		$_connectionWrite->commit();
		
		$_lastInsertId = $_connectionWrite->fetchOne('SELECT last_insert_id()'); 
		
		return $_lastInsertId.'Data has been written successfully';
		
		//exit;
		
		/*
		$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
		/*
		$sqlComment="INSERT INTO  ".$temptableComment." SET parent_id = '".$lastInsertId."', comment = '".$comment[$key]."' , user_id = '".$user_id."',
		user_type = 'customer', status ='New', postdate = NOW() ";
		$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
		*/
		/*
		$connectionWrite->beginTransaction();
		$data = array();
		$data['parent_id']= $lastInsertId;
		$data['comment'] = $comment[$key];
		$data['user_id']=$user_id;
		$data['user_type']='customer';
		$data['status']='New';
		$data['postdate']=NOW();
		$connectionWrite->insert($temptableComment, $data);
		$connectionWrite->commit();
		
		$lastInsertId1 = $connectionWrite->fetchOne('SELECT last_insert_id()'); 
			*/
		
	
	
		} ///end of isset data	
		
	/*delete heigh resoluction file */
	
	public function deleteHeighResFileAction(){
		/*delete heigh resolution file*/
		/*taking connection for reading and writing data to tables*/
		$_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');		
		$_designer_table = Mage::getSingleton('core/resource')->getTableName('task_designer');
		$_vendor_item_table = Mage::getSingleton('core/resource')->getTableName('vendor_item');
		$_proofs_table = Mage::getSingleton('core/resource')->getTableName('proofs');		
		/*fetching data via ajax post vars*/
		$_file_id = $this->getRequest()->getParam('fileid');
		$_file_ids = explode('_', $_file_id);
		$_file_id = $_file_ids[1];
		
		
		
		$_data = array('heigh_res_file'=>'',
					   'heigh_res_post_date'=>NOW(),	
						);
		$_where = ' entity_id='.$_file_id;				
		try{
		
		   /*delete file */
		
			$_connectionWrite->beginTransaction();
			$deleted = $_connectionWrite->update($_designer_table, $_data, $_where);
			$_connectionWrite->commit();
			
			/*update vendor Items*/
			$_connectionWrite->beginTransaction();
			$_data = array('heigh_res_file'=>'',
					   'heigh_res_post_date'=>NOW(),	
						);
			$_where = ' task_designer_id='.$_file_id;
			$_update_vendor_item = $_connectionWrite->update($_vendor_item_table, $_data, $_where);			
			$_connectionWrite->commit();
			
			/*update proofs*/
			$_connectionWrite->beginTransaction();
			$_data = array('heigh_res_file'=>'',	
						);
			$_where = ' task_designer_id='.$_file_id;
			$_update_proofs = $_connectionWrite->update($_proofs_table, $_data, $_where);			
			$_connectionWrite->commit();
			
			
		}catch(Exception $e){
			print_r($e);
			}
		
		if($deleted){
			echo 'H-Res File Deleted';
			return 'deleted';
			}else{
			echo 'H-Res File not deleted';	
			return 'Not deleted';	
			}
		
		}
	
	/*Uploading  file*/
	
	public function uploadHeighResFileAction(){
		 
		/*taking connection for reading and writing data to tables*/
		$_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		$_designer_table = Mage::getSingleton('core/resource')->getTableName('task_designer');
		$_vendor_item_table = Mage::getSingleton('core/resource')->getTableName('vendor_item');
		$_proofs_table = Mage::getSingleton('core/resource')->getTableName('proofs');
		/*fetching data via ajax post vars*/
		$_file_id = $this->getRequest()->getParam('fileid');				
		$_file_name = $this->getRequest()->getParam('filename');
		$_file_path = $this->getRequest()->getParam('dir_path');
		
		$_uploaded_flag = false;	
		$_data = array();	
		
		$_media_url = Mage::getBaseUrl('media');
		$_str_pos = strpos($_file_path,'stores');
		//$_file_path = str_replace('media',$_media,$_file_path) ;
		$_file_path = substr($_file_path,$_str_pos);
			
		$_data['heigh_res_file'] = $_media_url.$_file_path.'/'.$_file_name;		
		$_data['heigh_res_post_date'] = NOW();
		
		
		
		try{
			/*update the file in database*/
			$_connectionWrite->beginTransaction();
			$_where = ' entity_id='.$_file_id;
			$_update_task_designer = $_connectionWrite->update($_designer_table, $_data, $_where);			
			$_connectionWrite->commit();
			
			if($_update_task_designer){
				$_uploaded_flag==true;
			}
			
			
			
			/*update proofs*/
			$_connectionWrite->beginTransaction();
			$_where = ' task_designer_id='.$_file_id;
			$_update_proofs = $_connectionWrite->update($_proofs_table, $_data, $_where);			
			$_connectionWrite->commit();
			
			if($_update_proofs){
				$_uploaded_flag==true;
			}
			
			/*update vendor Items*/
			$_connectionWrite->beginTransaction();
			$_data['file_visible'] = 1;
			$_where = ' task_designer_id='.$_file_id;
			$_update_vendor_item = $_connectionWrite->update($_vendor_item_table, $_data, $_where);			
			$_connectionWrite->commit();
			
			if($_update_vendor_item){
				$_uploaded_flag==true;
			}
			///uploading file 
		
			}catch(Exception $e){
			print_r($e);
			}
		
			return $_uploaded_flag;
		
		} ///end of isset data					
		
	 /*function entry_exists
	 @ params: entity id, order id , item it , user id
	 @ output: bolean true/false	 	 
	 */
	 
	 public function entry_existsAction($_entity_id=0, $_order_id=0, $_item_id=0, $_proof_type=''){
		
		/* if entity_id is not present already*/
		 	
		   if($_entity_id==0 || $_order_id==0 || $_item_id==0 ){ return false;	}
		
		/*select and found if entity id is already exists*/
		
			$_resource = Mage::getSingleton('core/resource');
			$_read= $_resource->getConnection('core_read');
			$_designerTable = $_resource->getTableName('task_designer');
				
			$_select = $_read->select()
			   ->from($_designerTable,array('entity_id','status'))
			   ->where('entity_id=?',$_entity_id)
			   ->where('order_quote_id=?',$_order_id)
			   ->where('item_id=?',$_item_id)
			   ->where('proof_type=?',$_proof_type)
			   ->order('entity_id ASC');			
			
			$_entity_ids = $_read->fetchAll($_select);
			
			$_entity_id_exists = count($_entity_ids); 
			 
		/*check if entity id is exists */
			if($_entity_id_exists !=0){
					return true;
				}else{
					return false;
				}
				 
		} //end of function file_exits
		
		
	/*function approvedesignAction
	 @ params: 
	 @ output: bolean true/false updated or not	 	 
	 */
	 
	 public function approvedesignAction(){
		
		
		/*taking connection for reading and writing data to tables*/
		$_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		//getting post vars		 
		
		$_entity_id = $_POST['entity_id'];
		$_approved_status = $_POST['approved_status'];
		$_item_id = $_POST['item_id'];
		$_order_id = $_POST['order_id'];
		$_signature = $_POST['signature'];
		$_quantity = $_POST['quantity'];
		$_proof_type = 'proof';
		
		$_data = array('approved_status'=>$_approved_status,
					   'signature'=>$_signature,
					   'quantity'=>$_quantity,
					   'approve_date'=>NOW(),	
						);
		//print_r($_data);				
		
		$_where = ' item_id ='.$_item_id.' 
		          AND order_quote_id ='.$_order_id.' 
				  AND entity_id='.$_entity_id.' 
				  AND proof_type="'.$_proof_type.'"';
										
		$_temptableDesign = Mage::getSingleton('core/resource')->getTableName('task_designer');	
		$_update = $_connectionWrite->update($_temptableDesign, $_data, $_where);
		print_r($_update);
		 
	 } //end of function update


		/*funciton : uploadImages*/
		
		public function uploadImagesAction(){
			
				
       // Mage::log($_FILES);
	   $_file=array();
	   foreach($_FILES as $fileUplaod){
		   foreach($fileUplaod as $file){
			  	 foreach($file as $key=>$fval){
					 $fileId = $key;					 
					 }				 
			   }
		   }
		    
           	$type = 'fileUpload';
			$target_path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/' ;	
			//$target_path = $_SERVER['DOCUMENT_ROOT'].'/tablethrows/media/' ;
		 	$file_path = $_POST['client_files'];
			//$file_path = str_replace('\\','/',$target_path);
			 $abs_target = strpos($file_path, 'stores');
			
		 	$target_path = $target_path.substr($file_path,$abs_target).'/';
			
			
			try{
			
			$file_name = $_FILES[$type]['name'][$fileId];
			//$target_path = Mage::getBaseUrl('media').'upload/';
			  
			//echo'<br/>';
			//echo $_FILES[$type]['tmp_name'][$fileId].'<br/>'. $target_path. $file_name;
			 
			 
			 if($_uploaded = move_uploaded_file($_FILES[$type]['tmp_name'][$fileId],$target_path. $file_name)){
				echo $filename.' has been uploaded to '.$target_path. $file_name; 
				 }else{
					 
				echo $filename.' could not be uploaded to '.$target_path. $file_name; 	 
				 }
				 
		}catch(Exception $e){
			
			  print_r($e);	
			}
           
           // echo $filename;
       
    
				//print_r($_FILES);
				
			
			}//end of function imagefilesupload
}