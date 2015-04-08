<?php
class Artis_Designer_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/designer?id=15 
    	 *  or
    	 * http://site.com/designer/id/15 	
    	 */
    	/* 
		$designer_id = $this->getRequest()->getParam('id');

  		if($designer_id != null && $designer_id != '')	{
			$designer = Mage::getModel('designer/designer')->load($designer_id)->getData();
		} else {
			$designer = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
		 
		/*
		 if($designer == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$designerTable = $resource->getTableName('designer');
			
			$select = $read->select()
			   ->from($designerTable,array('designer_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$designer = $read->fetchRow($select);
		}
		Mage::register('designer', $designer);
		 
		*/ 
    			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function saveAction()
    {
	
	extract($_REQUEST);
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
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
	    
	    foreach($item as $key => $value)
	    {
		if($value != '')
		{
		$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
		/*
		$sqlDesign="INSERT INTO  ".$temptableDesign." SET store_id = '".$store_id."', order_quote_id ='".$order_id."', user_id = '".$user_id."',
		user_type = 'customer', item_id = '".$value."', status ='New', comment = '".$comment[$key]."', proof_type = '".$type."', postdate = NOW() ";
		$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
		*/
		
		$connectionWrite->beginTransaction();
		$data = array();
		$data['store_id']= $store_id;
		$data['order_quote_id']=$order_id;
		$data['user_id']=$user_id;
		$data['user_type']='customer';
		$data['item_id'] = $value;
		$data['status']='New';
		$data['comment'] = $comment[$key];
		$data['proof_type'] = $type;
		$data['postdate']=NOW();
		$connectionWrite->insert($temptableDesign, $data);
		$connectionWrite->commit();
		
		$lastInsertId = $connectionWrite->fetchOne('SELECT last_insert_id()'); 
		
		$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
		/*
		$sqlComment="INSERT INTO  ".$temptableComment." SET parent_id = '".$lastInsertId."', comment = '".$comment[$key]."' , user_id = '".$user_id."',
		user_type = 'customer', status ='New', postdate = NOW() ";
		$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
		*/
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
		    //$sqlDesign="SELECT * FROM  ".$temptableDesign." WHERE order_id = '".$order_id."' AND item_id ='".$value."' ";
		    $sqlDesign = $connectionRead->select()
				    ->from($temptableDesign, array('*'))
				    ->where("order_id = '".$order_id."' AND item_id ='".$value."'");
		    $chkDesign = $connectionRead->fetchAll($sqlDesign);
		    
		    $temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		    //$sqlItem="SELECT * FROM  ".$temptableItem." WHERE item_id ='".$value."' ";
		    $sqlItem = $connectionRead->select()
				    ->from($temptableItem, array('*'))
				    ->where('item_id =?', $value);
		    $chkItem = $connectionWrite->fetchAll($sqlItem);
		    
		    
		    if($type == 'order')
		    {
			$order = Mage::getModel('sales/order')->load($order_id);
			$id = $order->getIncrementId();
			$url= Mage::helper("adminhtml")->getUrl('admin/sales_order/view/order_id/'.$order_id);
			$url = str_replace('/s','index.php/admin/s',$url);
		    }
		    else if($type == 'quote')
		    {
			$quote = Mage::getModel("Quotation/Quotation")->load($order_id);
			$id = $quote->getIncrementId();
			$url= Mage::helper("adminhtml")->getUrl('Quotation/Admin/edit/quote_id/'.$order_id);
			$url = str_replace('/Quotation','/index.php/Quotation',$url);
		       
		    }
		    
		    $order = Mage::getModel('sales/order')->load($order_id);
		    
		    $user = Mage::getModel('admin/user')->load($chkDesign[0]['assign_to']);
		    
		    $customerData = Mage::getModel('customer/customer')->load($user_id);
		    
		    $sales_email = Mage::getStoreConfig('trans_email/ident_sales/email'); 
		    
		    $mail = Mage::getModel('core/email');
		    //$mail->setToName('test');
		    $mail->setToEmail($user->getEmail());
		    $mail->setBody('Hi,<br/><br/> '.$customerData->getName().' has been uploaded a design for the product '.$chkItem[0]['name'].' and '.$type.' <a href="'.$url.'">'.$id.'</a>. Please check this.');
		    $mail->setSubject('Please check the customer design in Aceexhinits');
		    $mail->setFromEmail($customerData->getEmail());
		    $mail->setFromName($customerData->getName());
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
		    $mail1->setBody('Hi,<br/><br/> '.$customerData->getName().' has been uploaded a design for the product '.$chkItem[0]['name'].' and '.$type.' <a href="'.$url.'">'.$id.'</a>. Please check this.');
		    $mail1->setSubject('Please check the customer design in Aceexhinits');
		    $mail1->setFromEmail($customerData->getEmail());
		    $mail1->setFromName($customerData->getName());
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
	$this->_redirect('sales/order/view/order_id/'.$order_id);
	else if($type == 'quote')
	$this->_redirect('Quotation/Quote/View/quote_id/'.$order_id);
    }
    
    public function allcommentAction()
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	extract($_REQUEST);
	
	$url2 = Mage::helper('adminhtml')->getUrl('designer/index/download');
	echo ' <table class="tooltipcom">
            <tr>
	     <td>
	      <div class="allcomment">'
	      ;
	
	$temptableItem=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
	//$sqlItem="SELECT *,DATE_FORMAT(postdate,'%D %M, %Y') AS p_date FROM  ".$temptableItem." WHERE parent_id ='".$id."' ORDER BY entity_id DESC  ";
	$sqlItem = $connectionRead->select()
				->from($temptableItem, array("* , DATE_FORMAT(postdate,'%D %M, %Y') AS p_date"))
				->where('parent_id=?',$id)
				->order('entity_id DESC');
	$chkItems = $connectionRead->fetchAll($sqlItem);
	foreach($chkItems as $chkItem)
	{
	    if($chkItem['user_type'] == 'customer')
	    {
		    $customerData = Mage::getModel('customer/customer')->load($chkItem['user_id']);
		    //$name = $customerData->getName();
		    
		    $name = 'Me';
	    }
	    else if($chkItem['user_type'] == 'admin')
	    {
		$getadmin=Mage::getModel('admin/user')->load($chkItem['user_id']);
		$name=$getadmin->getName();
		
	    }
		
	    
	    echo '<div class="comtotal"><div class="dateidenty"><strong>'.$chkItem['p_date'].' - <span>'.$name.'</span></strong></div>';
	    if($chkItem['file'] != ''){
	    echo '<div class="imageidenty"><img style="width: 104px; height: 100px;" src="'.Mage::getBaseUrl().'media/design/'.$chkItem['file'].'" /><a href="'.str_replace('//s','/admin/s',$url2).'file/'.$chkItem['file'].'/'.'"><img title="Download Now" style="width: 20px;height: 15px;" src="'.Mage::getDesign()->getSkinUrl().'images/load.png'.'"/></a> </div>';
	    }
	    echo '<div class="scomment">'.$chkItem['comment'].'</div></div>';
	       
	}
	echo      '</div>
	     </td>
	    </tr>
		</table>';
		
	$temptableItem=Mage::getSingleton('core/resource')->getTableName('task_designer');
	//$sqlItem="SELECT *,DATE_FORMAT(postdate,'%D %M, %Y') AS p_date FROM  ".$temptableItem." WHERE entity_id ='".$id."'  ";
	$sqlItem = $connectionRead->select()
				->from($temptableItem, array("* , DATE_FORMAT(postdate,'%D %M, %Y') AS p_date"))
				->where('entity_id=?',$id);
				
	$chkItems = $connectionRead->fetchAll($sqlItem);
	
	 echo '<div class="fromtool">';
   
        $url = Mage::helper("adminhtml")->getUrl("designer/index/addcomment");
        $url = str_replace('p//s','p/admin/s',$url);
        
       
   
echo '<form enctype="multipart/form-data" name="form_planning" id="form_planning" method="POST" action="'.$url.'">
<input name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'">
   <table class="total_proof" style="width: 100%;">
       <tr class="txt"><input type="hidden" id="form_count" />
	   <td valign="top" class="fromclass">
	    <input type="hidden" name="entity_id" id="entity_id" value="'.$id.'"/>
	    <input type="hidden" name="order_id" id="dorderid" value="'.$chkItems[0]['order_quote_id'].'"/>
	    <div id="dvFile">';
                
                    $temptableCount=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
                    $temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
                    $temptableService=Mage::getSingleton('core/resource')->getTableName('design_service');
                    
                    //$sqlCount="SELECT * FROM  ".$temptableCount." WHERE parent_id IN(SELECT entity_id FROM ".$temptableDesign." WHERE order_quote_id = '".$chkItems[0]['order_quote_id']."' AND item_id = '".$chkItems[0]['item_id']."' )  AND file != '' AND user_type = 'customer'  ";
                    //$sqlCount1="SELECT entity_id FROM ".$temptableDesign." WHERE order_quote_id = '".$chkItems[0]['order_quote_id']."' AND item_id = '".$chkItems[0]['item_id']."'";
                    $sqlCount1 = $connectionRead->select()
				    ->from($temptableDesign, array('entity_id'))
				    ->where("order_quote_id = '".$chkItems[0]['order_quote_id']."' AND item_id = '".$chkItems[0]['item_id']."'");
		    $chkCount1 = $connectionRead->fetchAll($sqlCount1);
		    
		    $entity="";
		    
		    if(count( $chkCount1)){
			for($i=0; $i<count($chkCount1); $i++){
			    
			    if($i==0){
			    $entity=$chkCount1[$i]['entity_id'];
			    }else{
			    $entity.=','.$chkCount1[$i]['entity_id'];
			    }	
		    
			}
		    }
		    
		    //$sqlCount="SELECT * FROM  ".$temptableCount." WHERE parent_id IN(".$entity.")  AND file != '' AND user_type = 'customer'  ";
		    $sqlCount = $connectionRead->select()
				    ->from($temptableCount, array('entity_id'))
				    ->where("parent_id IN(".$entity.")  AND file != '' AND user_type = 'customer'");
		    $chkCount = $connectionRead->fetchAll($sqlCount);
                    
                    //$sqlService="SELECT SUM(revision_number) AS r_number FROM  ".$temptableService." WHERE order_id ='".$chkItems[0]['order_quote_id']."' AND item_id = '".$chkItems[0]['item_id']."'  ";
                    $sqlService = $connectionRead->select()
				    ->from($temptableService, array('SUM(revision_number) AS r_number'))
				    ->where("order_id ='".$chkItems[0]['order_quote_id']."' AND item_id = '".$chkItems[0]['item_id']."'");
		    $chkService = $connectionRead->fetchAll($sqlService);
                    
                    
                    
                    if(count($chkCount) < $chkService[0]['r_number'])
                    {
               
		    echo '<div class="file_class">
		     <input type="file" name="item_file">
		     </div>';
            
                   }
		   
		   echo '<div class="file_class">
			 <select name="status">
			    <option value="">Select Status</option>
			    <option value="Disapproved">Disapproved</option>
			    <option value="Approved">Approved</option>
			 </select>
		      </div>';
            
		    echo '<div class="item_comment">
			 <textarea name="comment"></textarea>
		      </div>
		     </div>
		    </td>
		</tr>
	 <tr><td class="clssubmit">
		     <button class="submit" >Submit</button>
		    </td></tr>
	    </table>
	    </form>
	    
	   </div>';
    }
    
    public function addcommentAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$user_id = Mage::getSingleton('customer/session')->getId();
	
	$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
	$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
	
	if($status == 'Approved')
	{
	    //$sqlLast="SELECT * FROM ".$temptableComment." WHERE parent_id = '".$entity_id."' AND file != '' ORDER BY entity_id DESC LIMIT 1";
	    $sqlLast = $connectionRead->select()
			    ->from($temptableComment, array('*'))
			    ->where("parent_id = '".$entity_id."' AND file != ''")
			    ->order('entity_id DESC');
	    $chkLast = $connectionRead->fetchAll($sqlLast);
	    
	    //$sqlDesign1="SELECT * FROM  ".$temptableDesign." WHERE entity_id = '".$entity_id."' ";
	    $sqlDesign1 = $connectionRead->select()
			    ->from($temptableDesign, array('*'))
			    ->where('entity_id=?', $entity_id);
	    $chkDesign1 = $connectionRead->fetchAll($sqlDesign1);
	    
	    $temptableProofs=Mage::getSingleton('core/resource')->getTableName('proofs');
	    if($connectionWrite->isTableExists($temptableProofs))
	    {
		$mediaPath=Mage::getBaseDir('media') . DS ;
		$path1 = $mediaPath.'design/'.$chkLast[0]['file'];
		chmod($path1,0777);
		$path2 = $mediaPath.'proofs/'.$chkLast[0]['file'];
                chmod($path2,0777);
		
		if(copy($path1,$path2))
                {
		    /*
		    $sqlProofsSystem="INSERT INTO ".$temptableProofs."  SET store_id = '".$chkDesign1[0]['store_id']."',
					order_id = '".$chkDesign1[0]['order_quote_id']."',
					customer_id = '".$chkDesign1[0]['user_id']."' ,
					item_id = '".$chkDesign1[0]['item_id']."',
					file = '".$chkLast[0]['file']."',
					status = 'Awaiting Proof Approval',
					postdate = NOW(),
					proof_type = '".$chkDesign1[0]['proof_type']."'";
		    $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
		    */
		    $connectionWrite->beginTransaction();
		    $data = array();
		    $data['store_id']= $chkDesign1[0]['store_id'];
		    $data['order_id']=$chkDesign1[0]['order_quote_id'];
		    $data['customer_id']=$chkDesign1[0]['user_id'];
		    $data['item_id']= $chkDesign1[0]['item_id'];
		    $data['file']= $chkLast[0]['file'];
		    $data['status']='Awaiting Proof Approval';
		    $data['postdate']=NOW();
		    $data['proof_type'] = $chkDesign1[0]['proof_type'];
		    $connectionWrite->insert($temptableProofs, $data);
		    $connectionWrite->commit();
		}
	    }
	}

	/*
	$sqlComment="INSERT INTO  ".$temptableComment." SET parent_id = '".$entity_id."', comment = '".$comment."', user_id = '".$user_id."',
	user_type = 'customer', status='".$status."', postdate = NOW() ";
	$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
	*/
	$connectionWrite->beginTransaction();
	$data = array();
	$data['parent_id']= $entity_id;
	$data['comment']=$comment;
	$data['user_id']=$user_id;
	$data['user_type']='customer';
	$data['status']= $status;
	$data['postdate']=NOW();
	$connectionWrite->insert($temptableComment, $data);
	$connectionWrite->commit();
	
	$lastInsertId = $connectionWrite->fetchOne('SELECT last_insert_id()');
	
	
	//$sqlDesign="UPDATE  ".$temptableDesign." SET  status='".$status."' WHERE entity_id = '".$entity_id."' ";
	//$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
	
	$connectionWrite->beginTransaction();
	$data = array();
	$data['status'] = $status;
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
		    //$sqlComment="UPDATE  ".$temptableComment." SET file ='".$fileNameVal."' WHERE entity_id = '".$lastInsertId."' ";
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
		    $url= Mage::helper("adminhtml")->getUrl('admin/sales_order/view/order_id/'.$chkDesign1[0]['order_quote_id']);
		    $url = str_replace('/s','index.php/admin/s',$url);
		}
		else if($chkDesign1[0]['proof_type'] == 'quote')
		{
		    $quote = Mage::getModel("Quotation/Quotation")->load($chkDesign1[0]['order_quote_id']);
		    $id = $quote->getIncrementId();
		    $url= Mage::helper("adminhtml")->getUrl('Quotation/Admin/edit/quote_id/'.$chkDesign1[0]['order_quote_id']);
		    $url = str_replace('/Quotation','/index.php/Quotation',$url);
		   
		}
		
		$user = Mage::getModel('admin/user')->load($chkDesign[0]['assign_to']);
		
		$customerData = Mage::getModel('customer/customer')->load($user_id);
		
		$sales_email = Mage::getStoreConfig('trans_email/ident_sales/email'); 
		
		$mail = Mage::getModel('core/email');
		//$mail->setToName('test');
		$mail->setToEmail($user->getEmail());
		$mail->setBody('Hi,<br/><br/> '.$customerData->getName().' has been given a feedback for the product '.$chkItem[0]['name'].' and '.$chkDesign1[0]['proof_type'].' <a href="'.$url.'">'.$id.'</a>. Please check this.');
		$mail->setSubject('Please check the customer design in Aceexhinits');
		$mail->setFromEmail($customerData->getEmail());
		$mail->setFromName($customerData->getName());
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
		$mail1->setBody('Hi,<br/><br/> '.$customerData->getName().' has been given a feedback for the product '.$chkItem[0]['name'].' and '.$chkDesign1[0]['proof_type'].' <a href="'.$url.'">'.$id.'</a>. Please check this.');
		$mail1->setSubject('Please check the customer design in Aceexhinits');
		$mail1->setFromEmail($customerData->getEmail());
		$mail1->setFromName($customerData->getName());
		$mail1->setType('html');// YOu can use Html or text as Mail format
		
		try {
		$mail1->send();
		}
		catch (Exception $e) {
		//Mage::getSingleton('core/session')->addError('Unable to send.');
		}
		
	//echo $chkDesign1[0]['proof_type'];
	if($chkDesign1[0]['proof_type'] == 'order')
	$this->_redirect('sales/order/view/order_id/'.$order_id);
	else if($chkDesign1[0]['proof_type'] == 'quote')
	$this->_redirect('Quotation/Quote/View/quote_id/'.$order_id);
	
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
	
	

}