<?php

class Artis_Systemalert_Adminhtml_SystemalertController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('systemalert/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('systemalert/systemalert')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('systemalert_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('systemalert/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('systemalert/adminhtml_systemalert_edit'))
				->_addLeft($this->getLayout()->createBlock('systemalert/adminhtml_systemalert_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('systemalert')->__('Item does not exist'));
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
	  			
	  			
			$model = Mage::getModel('systemalert/systemalert');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('systemalert')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('systemalert')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('systemalert/systemalert');
				 
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
        $systemalertIds = $this->getRequest()->getParam('systemalert');
        if(!is_array($systemalertIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($systemalertIds as $systemalertId) {
                    $systemalert = Mage::getModel('systemalert/systemalert')->load($systemalertId);
                    $systemalert->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($systemalertIds)
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
        $systemalertIds = $this->getRequest()->getParam('systemalert');
        if(!is_array($systemalertIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($systemalertIds as $systemalertId) {
                    $systemalert = Mage::getSingleton('systemalert/systemalert')
                        ->load($systemalertId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($systemalertIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'systemalert.csv';
        $content    = $this->getLayout()->createBlock('systemalert/adminhtml_systemalert_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'systemalert.xml';
        $content    = $this->getLayout()->createBlock('systemalert/adminhtml_systemalert_grid')
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
    
    public function setalertAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$temptableAlert=Mage::getSingleton('core/resource')->getTableName('system_alert');
	
	if($type == 'add')
	{
		$connectionWrite->beginTransaction();
		$data = array();
		$data['task_id']=$taskid;
		$connectionWrite->insert($temptableAlert, $data);
		$connectionWrite->commit();
		
		$lastInsertId  = $connectionWrite->fetchOne('SELECT last_insert_id()');
		
		Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('systemalert/systemalert')));
		mage::helper('AdminLogger')->updatelog($lastInsertId,'Add New System Alert');
	}
	else if($type == 'edit')
	{
		$connectionWrite->beginTransaction();
		$data = array();
		$data['task_id']=$taskid;
		$where = $connectionWrite->quoteInto('entity_id =?', $alertid);
		$connectionWrite->update($temptableAlert, $data, $where);
		$connectionWrite->commit();
		
		Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('systemalert/systemalert')));
		mage::helper('AdminLogger')->updatelog($alertid,'Change The Event in System Alert');
	}
	
	
	
	
	//$this->_redirect('*/*/');
	
    }
    
    public function savealerttaskAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$temptableAlert=Mage::getSingleton('core/resource')->getTableName('systemalert_task');
	
	$userid = Mage::getSingleton('admin/session')->getUser()->getId();
	
	if($duration_type == 'hour')
	{
		$duration_sec = $duration*3600;
		$time = time();
		$duration_time = $time+$duration_sec;
		
		$desdlinedate = date('Y-m-d H:i:s', $duration_time);
		
	}
	else if($duration_type == 'day')
	{
		$desdlinedate = date('Y-m-d H:i:s',strtotime("+".$duration." day", time()));
	}
	
	
	$connectionWrite->beginTransaction();
	$data = array();
	$data['parent_id']=$alertid;
	$data['target_id']=$target_user;
	$data['user_id']=$userid;
	$data['deadline_date']=$desdlinedate;
	$data['caption']=$caption;
	$data['description']=$description;
	$data['postdate']=NOW();
	$data['duration_type']=$duration_type;
	$data['duration']=$duration;
	$data['email_template_id']=$email_template;
	if($alert_sent != '')
	$data['is_mail']=$alert_sent;
	$connectionWrite->insert($temptableAlert, $data);
	$connectionWrite->commit();
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('systemalert/systemalert')));
	mage::helper('AdminLogger')->updatelog($alertid,'Add New Task in System Alert');
	
	$this->_redirect('*/*/');
    }
    
    public function loadtaskAction()
    {
	$url = Mage::helper("adminhtml")->getUrl("systemalert/adminhtml_systemalert/editalerttask");
	
	extract($_REQUEST);
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$temptableAlertTask=Mage::getSingleton('core/resource')->getTableName('systemalert_task');
	
	$select = $connectionRead->select()
		 ->from($temptableAlertTask, array('*'))
		 ->where('entity_id=?',$taskid);
		 
	$result = $connectionRead->fetchRow($select);
	
	if($result['complete'] == 1)
	$checked = 'checked';
	
	if($result['is_mail'] == 1)
	$checked_mail = 'checked';
	
	echo '<form id="edit_form_task_" name="edit_form_task_" method="POST" action="'.$url.'">
		<input name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'">
		<table cellspacing="5" cellpadding="0">
		 <input type="hidden" name="alertid" id="alertid" value="'.$result['entity_id'].'" />
		 <tr>
		  <td>Target User : </td>
		  <td>
		    <select name="target_user" id="target_user" >
		     <option value="">Assign To</option>';
		    
		       $adminUserModel = Mage::getModel('admin/user');
		       $userCollection = $adminUserModel->getCollection()->load();
		       foreach($userCollection as $user)
		       {
				$selected = '';
				if($result['target_id'] == $user->getId())
				$selected = 'selected';
			
				echo '<option value="'.$user->getId().'" '.$selected.' >'.$user->getUsername().'</option>';
		       }
		       
		       if($result['duration_type'] == 'day')
		       $selectedday = 'selected';
		       else if($result['duration_type'] == 'hour')
		       $selectedhour = 'selected';
		  
	echo	     '</select>
		  </td>
		 </tr>
		 <tr>
		  <td>Deadline Duration : </td>
		  <td>
		  <select name="duration_type" >
			<option value="">Select Type</option>
			<option value="day" '.$selectedday.' >Days</option>
			<option value="hour" '.$selectedhour.'>Hours</option>
		       </select>
		       
		       <select name="duration" >
			<option value="">Select Duration</option>';
		
			 for($i=1;$i<=10;$i++)
			 {
				$selected = '';
				if($result['duration'] == $i)
				$selected = 'selected';
			   echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			 }
			
	echo	      '</select>
		  </td>
		 </tr>
		 <tr>
		  <td>Complete : </td>
		  <td>
		   <input size="6" type="checkbox" id="complete" name="complete" value="1" '.$checked.' >
		  </td>
		 </tr>
		<tr>
		     <td>Send Alert Mail : </td>
		     <td><input type="checkbox" name="alert_sent" id="alert_sent" value="1" '.$checked_mail.'/></td>
		    </tr>
		 <tr>
		  <td>Caption : </td>
		  <td><input type="text" name="caption" id="caption" value="'.$result['caption'].'"/></td>
		 </tr>
		 <tr>
			<td>Staff Email Template : </td>
			<td>
			 <select name="email_template">
			  <option value="">Select Email Template</option>';
			  
			    $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			    $temptableEmail=Mage::getSingleton('core/resource')->getTableName('core_email_template');
			    
			    $select = $connectionRead->select()
				     ->from($temptableEmail, array('*'));
				     
			    $result1 = $connectionRead->fetchAll($select);
			    foreach($result1 as $email)
			    {
				   $selected = '';
				   if($result['email_template_id'] == $email['template_id'])
				   $selected = 'selected';
			      echo '<option value="'.$email['template_id'].'" '.$selected.'>'.$email['template_code'].'</option>';
			    }
			 
			 echo '</select>
			</td>
		       </tr>
		 <tr>
		  <td>Alert Description : </td>
		  <td><textarea name="description" id="description">'.$result['description'].'</textarea></td>
		 </tr>
		 <tr>
		  <td colspan="2" class="edtask"><button type="submit" name="submit" id="submit">Save</button></td>
		 </tr>
		</table>
	       </form>';
    }
    
    public function editalerttaskAction()
    {
	//print_r($_REQUEST);exit();
	
	extract($_REQUEST);
	
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$temptableAlert=Mage::getSingleton('core/resource')->getTableName('systemalert_task');
	
	$userid = Mage::getSingleton('admin/session')->getUser()->getId();
	
	if($duration_type == 'hour')
	{
		$duration_sec = $duration*3600;
		$time = time();
		$duration_time = $time+$duration_sec;
		
		$desdlinedate = date('Y-m-d H:i:s', $duration_time);
		
	}
	else if($duration_type == 'day')
	{
		$desdlinedate = date('Y-m-d H:i:s',strtotime("+".$duration." day", time()));
	}
	
	
	$connectionWrite->beginTransaction();
	$data = array();
	$data['target_id']=$target_user;
	$data['deadline_date']=$desdlinedate;
	$data['caption']=$caption;
	$data['description']=$description;
	$data['complete']=$complete;
	$data['duration_type']=$duration_type;
	$data['duration']=$duration;
	$data['email_template_id']=$email_template;
	if($alert_sent != '')
	$data['is_mail']=$alert_sent;
	else
	$data['is_mail']=0;
	
	$where = $connectionWrite->quoteInto('entity_id =?', $alertid);
	$connectionWrite->update($temptableAlert, $data, $where);
	$connectionWrite->commit();
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('systemalert/systemalert')));
	mage::helper('AdminLogger')->updatelog($alertid,'Edit Task in System Alert');
	
	$this->_redirect('*/*/');
    }
    
    public function deletealertAction()
    {
	//print_r($_REQUEST);exit();
	extract($_REQUEST);
	
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$temptableAlertTask=Mage::getSingleton('core/resource')->getTableName('systemalert_task');
	$temptableAlertCondition=Mage::getSingleton('core/resource')->getTableName('systemalert_condition');
	$temptableAlert=Mage::getSingleton('core/resource')->getTableName('system_alert');
	
	$connectionWrite->beginTransaction();
	$condition = array($connectionWrite->quoteInto('parent_id=?', $alertid));
	$connectionWrite->delete($temptableAlertTask, $condition);
	$connectionWrite->commit();
	
	$connectionWrite->beginTransaction();
	$condition = array($connectionWrite->quoteInto('parent_id=?', $alertid));
	$connectionWrite->delete($temptableAlertCondition, $condition);
	$connectionWrite->commit();
	
	$connectionWrite->beginTransaction();
	$condition = array($connectionWrite->quoteInto('entity_id=?', $alertid));
	$connectionWrite->delete($temptableAlert, $condition);
	$connectionWrite->commit();
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('systemalert/systemalert')));
	mage::helper('AdminLogger')->updatelog($alertid,'Delete System Alert');
	
    }
    
    public function loadattrAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	//print_r($this->getfield($model));
	
	foreach($this->getfield($model) as $key=>$column)
	{
		if(!is_array($column))
		echo '<option value="'.$column.'">'.$column.'</option>';
	}
	
	
    }
    
    public function getfield($model)
    {
	$table['quotation'] = Mage::getSingleton('core/resource')->getTableName('quotation');
	$table['order'] = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
	$table['customer'] = Mage::getSingleton('core/resource')->getTableName('customer_entity');
	$table['invoice'] = Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice');
	$table['shipment'] = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');//06_02_2014
	$table['vendor'] = Mage::getSingleton('core/resource')->getTableName('vendor_item');
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	
	
	if($model == 'product')
	{
		$attributes = Mage::getModel('catalog/product')->getAttributes();
		//$attributeArray = array();
	    
		foreach($attributes as $a){
		    foreach ($a->getEntityType()->getAttributeCodes() as $attributeName) {
			//$attributeArray[$attributeName] = $attributeName;
			//echo '<option value="'.$attributeName.'">'.$attributeName.'</option>';
			
			$result[] = $attributeName;
		    }
		}
	}
	else{
		
		$select = $connectionRead->select()
			->from('information_schema.columns', array('column_name'))
			->where('table_name =?',$table[$model])
			->group('column_name');
			
		$result = $connectionRead->fetchAll($select);
	
		foreach($result as $column)
		{
			//echo '<option value="'.$column['column_name'].'">'.$column['column_name'].'</option>';
			
			$result[] = $column['column_name'];
		}
	}
	
	return $result;
    }
    
    public function savealertconditionAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$temptableCondition=Mage::getSingleton('core/resource')->getTableName('systemalert_condition');
	
	$connectionWrite->beginTransaction();
	$condition1 = array($connectionWrite->quoteInto('parent_id=?', $con_alertid));
	$connectionWrite->delete($temptableCondition, $condition1);
	$connectionWrite->commit();
	
	$userid = Mage::getSingleton('admin/session')->getUser()->getId();
	
	foreach($model as $key=>$model_value)
	{
		if($model_value != '' and $attribute[$key] != '' and $condition[$key] != '' and $attr_value[$key] != '')
		{
			$connectionWrite->beginTransaction();
			$data = array();
			$data['parent_id']=$con_alertid;
			$data['attr_model']=$model_value;
			$data['attr_field']=$attribute[$key];
			$data['attr_condition']=$condition[$key];
			$data['attr_value']=$attr_value[$key];
			$data['attr_action']=$action[$key];
			$connectionWrite->insert($temptableCondition, $data);
			$connectionWrite->commit();
		}
	}
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('systemalert/systemalert')));
	mage::helper('AdminLogger')->updatelog($con_alertid,'Add Alert Condition in System Alert');
	
	$this->_redirect('*/*/');
    }
    
    public function loadconditionAction()
    {
	extract($_REQUEST);
	//print_r($_REQUEST);exit;
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$temptableCondition=Mage::getSingleton('core/resource')->getTableName('systemalert_condition');
	
	$select = $connectionRead->select()
			->from($temptableCondition, array('*'))
			->where('parent_id =?',$id);
	$result = $connectionRead->fetchAll($select);
			
	$url = Mage::helper("adminhtml")->getUrl("systemalert/adminhtml_systemalert/savealertcondition");		
			
		echo '   <form id="form_task_1" name="form_task_1" method="POST" action="'.$url.'">
	   <input name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'">
	  <table style="width:100%" cellspacing="5" cellpadding="0">
	   <input type="hidden" name="con_alertid" value="'.$id.'"/>
	   <thead>
	   <tr>
	    <th>Model</th>
	    <th>Attribute</th>
	    <th>Condition</th>
	    <th>Value</th>
	    <th>Action</th>
	    <th ><button type="button" onclick="add_another3();">Add Another Condition</button></th>
	   </tr>
	   </thead>
	   
	    <tbody id="content3">';
	$i = 100;   
	foreach($result as $con)
	{
		$selected_pro = $selected_quote = $selected_order = $selected_customer = $selected_invoice = $selected_shipment = '';
		
		if($con['attr_model'] == 'product')
		$selected_pro = 'selected';
		else if($con['attr_model'] == 'quotation')
		$selected_quote = 'selected';
		else if($con['attr_model'] == 'order')
		$selected_order = 'selected';
		else if($con['attr_model'] == 'customer')
		$selected_customer = 'selected';
		else if($con['attr_model'] == 'invoice')
		$selected_invoice = 'selected';
		else if($con['attr_model'] == 'shipment')
		$selected_shipment = 'selected';
		else if($con['attr_model'] == 'vendor')
		$selected_vendor = 'selected';
		
	  	echo '<tr id="taball_'.$i.'">
	     <td>
	      <select id="model_'.$i.'" name="model[]" onchange="loadattr(this.value,\''.$i.'\')">
	       <option value="">Select Model</option>
	       <option value="product" '.$selected_pro.'>Product</option>
	       <option value="quotation" '.$selected_quote.'>Quotation</option>
	       <option value="order" '.$selected_order.'>Order</option>
	       <option value="customer" '.$selected_customer.'>Customer</option>
	       <option value="invoice" '.$selected_invoice.'>Invoice</option>
	       <option value="shipment" '.$selected_shipment.'>Shipment</option>
	       <option value="vendor" '.$selected_vendor.'>Vendor</option>
	      </select>
	     </td>
	     <td>
	      <select id="attribute_'.$i.'" name="attribute[]" class="attributecss"><option value="">Select Attribute</option>';
	      
	foreach($this->getfield($con['attr_model']) as $key=>$column)
	{
		if(!is_array($column))
		{
			$selected = '';
			if($column == $con['attr_field'])
			$selected = 'selected';
			
			echo '<option value="'.$column.'" '.$selected.'>'.$column.'</option>';
		}
	}
	       
	
	
	$selected_equal = $selected_nequal = $selected_greater = $selected_greater_equal = $selected_less = $selected_less_equal = $selected_and = $selected_or = '';
	if($con['attr_condition'] == '=')
	$selected_equal = 'selected';
	else if($con['attr_condition'] == '!=')
	$selected_nequal = 'selected';
	else if($con['attr_condition'] == '>')
	$selected_greater = 'selected';
	else if($con['attr_condition'] == '>=')
	$selected_greater_equal = 'selected';
	else if($con['attr_condition'] == '<')
	$selected_less = 'selected';
	else if($con['attr_condition'] == '<=')
	$selected_less_equal = 'selected';
	
	if($con['attr_action'] == 'AND')
	$selected_and = 'selected';
	else if($con['attr_action'] == 'OR')
	$selected_or = 'selected';
	
	echo '      </select>
	     </td>
	     <td>
	      <select id="condition_'.$i.'" name="condition[]">
	       <option value="">Select Condition</option>
	       <option value="=" '.$selected_equal.'>Is Equal</option>
	       <option value="!=" '.$selected_nequal.'>Is Not Equal</option>
	       <option value=">" '.$selected_greater.'>Is Greater Than</option>
	       <option value=">=" '.$selected_greater_equal.'>Is Greater Than Or Equal To</option>
	       <option value="<" '.$selected_less.'>Is less Than</option>
	       <option value="<=" '.$selected_less_equal.'>Is less Than Or Equal To</option>
	      </select>
	     </td>
	     <td>
	      <input type="text" id="attr_value_'.$i.'" name="attr_value[]" value="'.$con['attr_value'].'" />
	     </td>
	     <td>
	      <select id="action_'.$i.'" name="action[]">
	       <option value="">Select Action</option>
	       <option value="OR" '.$selected_or.'>OR</option>
	       <option value="AND" '.$selected_and.'>AND</option>
	      </select>
	     </td>
	     <td><span class="removeitem" onclick="div_remove2('.$i.')"  style="cursor:pointer;">Remove</span></td>
	    </tr>';
	    
	    $i++;
	}
	
	echo '  </tbody>
	    <tr>
	     <td></td>
	     <td></td>
	     <td></td>
	     <td></td>
	     <td></td>
	     <td><button type="submit" name="submit" id="submit"/>Save</button></td>
	    </tr>
	  </table>
	  </form><input type="hidden" id="condition_count3" value="'.$i.'"/>';
    }
       
   
}