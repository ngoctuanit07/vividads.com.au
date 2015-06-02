<?php

class Artis_Designer_Adminhtml_DesignerController extends Mage_Adminhtml_Controller_action
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
	
	extract($_REQUEST);
	
	$url2 = Mage::helper('adminhtml')->getUrl('designer/adminhtml_designer/download');
	
	echo ' <table class="tooltipcom">
            <tr>
	     <td>
	      <div class="allcomment">'
	      ;
	
	$temptableItem=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
	$sqlItem="SELECT *,DATE_FORMAT(postdate,'%D %M, %Y') AS p_date FROM  ".$temptableItem." WHERE parent_id ='".$id."' ORDER BY entity_id DESC ";
	$chkItems = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);
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
    }
    
    public function addcommentAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$user = Mage::getSingleton('admin/session');
	$user_id = $user->getUser()->getUserId();
	
	$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
	$sqlComment="INSERT INTO  ".$temptableComment." SET parent_id = '".$entity_id."', comment = '".$comment."' , user_id = '".$user_id."', user_type = 'admin', status ='Waiting for approval', postdate = NOW() ";
	$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
	$lastInsertId = Mage::getSingleton('core/resource')->getConnection('core_write')->lastInsertId();
	
	$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
	$sqlDesign="UPDATE  ".$temptableDesign." SET  status='Waiting for approval' WHERE entity_id = '".$entity_id."' ";
	$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
	
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
			$sqlComment="UPDATE  ".$temptableComment." SET  file ='".$fileNameVal."' WHERE entity_id = '".$lastInsertId."' ";
			$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
		   
		 }
	     }
	     
		$sqlDesign1="SELECT * FROM  ".$temptableDesign." WHERE entity_id = '".$entity_id."' ";
		$chkDesign1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlDesign1);
		
		$temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
                $sqlDesign="SELECT * FROM  ".$temptableDesign." WHERE order_id = '".$chkDesign1[0]['order_quote_id']."' AND item_id ='".$chkDesign1[0]['item_id']."' ";
                $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlDesign);
		
		$temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                $sqlItem="SELECT * FROM  ".$temptableItem." WHERE item_id ='".$chkDesign1[0]['item_id']."' ";
                $chkItem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);
		
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
	$sqlDesign1="SELECT * FROM  ".$temptableDesign." WHERE entity_id = '".$entity_id."' ";
	$chkDesign1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlDesign1);
	
	if($chkDesign1[0]['proof_type'] == 'order')
	{
		$url1 = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view/order_id/".$order_id);
		$url1 = str_replace('p//s','p/adminhtml/s',$url1);
		Mage::log($url1); //To check if URL is correct (and it is correct)
		Mage::app()->getResponse()->setRedirect($url1);
	}
	else if($chkDesign1[0]['proof_type'] == 'quote')
	{
		$url1 = Mage::helper('adminhtml')->getUrl("Quotation/Admin/edit/quote_id/".$order_id);
		$url1 = str_replace('p//s','p/admin/s',$url1);
		Mage::log($url1); //To check if URL is correct (and it is correct)
		Mage::app()->getResponse()->setRedirect($url1);
	}
	
    }
    
    public function deletedesignAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$user = Mage::getSingleton('admin/session');
	$user_id = $user->getUser()->getUserId();
	
	$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
	$sqlComment="DELETE FROM  ".$temptableComment." WHERE parent_id = '".$id."' ";
	$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
	
	$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
	$sqlDesign="DELETE FROM  ".$temptableDesign." WHERE  entity_id = '".$id."' ";
	$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
	
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
                    $sqlDesign="SELECT *,DATE_FORMAT(postdate,'%D %M, %Y') AS p_date FROM  ".$temptableDesign." WHERE order_quote_id ='".$orderid."' ORDER BY item_id ";
                    $chkDesigns = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlDesign);
                    
                    foreach($chkDesigns as $chkDesign)
                    {
                        
                        $temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                        $sqlItem="SELECT * FROM  ".$temptableItem." WHERE item_id ='".$chkDesign['item_id']."'  ";
                        $chkItem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);
                        
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
				       $sqlService="SELECT * FROM  ".$temptableService." WHERE order_id ='".$orderid."' AND item_id = '".$chkDesign['item_id']."'  ";
				       $chkService = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlService);
				   
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
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$user = Mage::getSingleton('admin/session');
	$user_id = $user->getUser()->getUserId();
	
	$temptableService=Mage::getSingleton('core/resource')->getTableName('design_service');
	$sqlService="UPDATE  ".$temptableService." SET assign_to = '".$userid."' WHERE item_id = '".$itemid."' AND order_id ='".$orderid."' AND type = '".$type."' ";
	$chkService = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlService);
	
	
	
    }
    
    public function setuserAction()
    {
	//print_r($user_id);exit;
	extract($_REQUEST);
	
	$temptableDesigner=Mage::getSingleton('core/resource')->getTableName('catalog_product_designer');
	$sqlDesigner="SELECT * FROM ".$temptableDesigner." WHERE product_id = '".$product_id."' ";
	$chkDesigner = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlDesigner);
	
	if(count($chkDesigner) > 0)
	{
		$sqlDesigner="UPDATE ".$temptableDesigner." SET user_id = '".$user_id."' WHERE product_id = '".$product_id."' ";
		$chkDesigner = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesigner);
	}
	else if(count($chkDesigner) == 0)
	{
		$sqlDesigner="INSERT INTO ".$temptableDesigner." SET user_id = '".$user_id."' , product_id = '".$product_id."' ";
		$chkDesigner = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesigner);
	}
	
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
		$sqlDesign="INSERT INTO  ".$temptableDesign." SET store_id = '".$store_id."', order_quote_id ='".$orderid."', user_id = '".$user_id1."', user_type = 'admin', item_id = '".$value."', status ='New', comment = '".$comment[$key]."', proof_type = '".$type."', postdate = NOW() ";
		$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
		
		$lastInsertId = Mage::getSingleton('core/resource')->getConnection('core_write')->lastInsertId();  
		
		$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
		$sqlComment="INSERT INTO  ".$temptableComment." SET parent_id = '".$lastInsertId."', comment = '".$comment[$key]."' , user_id = '".$user_id1."', user_type = 'admin', status ='New', postdate = NOW() ";
		$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
		
		$lastInsertId1 = Mage::getSingleton('core/resource')->getConnection('core_write')->lastInsertId();
		
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
			    $sqlDesign="UPDATE  ".$temptableDesign." SET  file = '".$fileNameVal."' WHERE entity_id ='".$lastInsertId."' ";
			    $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
			    
			    $temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
			    $sqlComment="UPDATE  ".$temptableComment." SET file ='".$fileNameVal."' WHERE entity_id ='".$lastInsertId1."' ";
			    $chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
			    
			}
		    }
		    
		    $temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
		    $sqlDesign="SELECT * FROM  ".$temptableDesign." WHERE order_id = '".$orderid."' AND item_id ='".$value."' ";
		    $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlDesign);
		    
		    $temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		    $sqlItem="SELECT * FROM  ".$temptableItem." WHERE item_id ='".$value."' ";
		    $chkItem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);
		    
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
}