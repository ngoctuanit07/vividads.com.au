<?php
class Fishpig_Proofs_Adminhtml_ProofsController extends Mage_Adminhtml_Controller_Action
{
     //Proof create update for order
    public function proofsAction()
    {
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        extract($_REQUEST);
        
        $order = Mage::getModel('sales/order')->load($orderid);
        $storId =  $order->getStoreId();
        $customer_id =  $order->getCustomerId();
      
        
            foreach($_FILES['item_file']['name'] as $key=>$value)
            {
                if(trim($item[$key]) != '')
                {
                
                    if(isset($_FILES['item_file']['name'][$key]) and (file_exists($_FILES['item_file']['tmp_name'][$key]))) {
                       
                        $file_name=$_FILES['item_file']['name'][$key];
                        
                        $expFilename=explode(".",$file_name);
                        $fileNameVal=time().".".end($expFilename);
                        
                        
                        $mediaPath=Mage::getBaseDir('media') . DS ;
                        //$path2 = $mediaPath.'upload_image/'.$fileNameVal;
                        $path2 = $mediaPath.'proofs/'.$fileNameVal;
                        chmod($path2,0777);
                        $filepath = $fileNameVal;
                        
                        //file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
                        if(move_uploaded_file($_FILES['item_file']['tmp_name'][$key],$path2))
                        {
                            //$tableName = Mage::getSingleton('core/resource')->getTableName('upload_image');
                            $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                            
                            //$sqlProofsSystem="INSERT INTO ".$tableName."  SET store_id = '".$storId."', order_id = '".$order->getId()."', customer_id = '".$customer_id."' , item_id = '".$item[$key]."', file = '".$filepath."', status = 'Awaiting Proof Approval',   postdate = NOW(), proof_type = 'order'";
                            //$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                            //$sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$file_name."',status = 2, created_time = NOW()";
                           // $sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$fileNameVal."',status = 2, created_time = NOW()";
                            $connectionWrite->beginTransaction();
                            $data = array();
                            $data['store_id']= $storId;
                            $data['order_id']=$order->getId();
			    if($customer_id != '')
                            $data['customer_id']= $customer_id;
                            $data['item_id']=$item[$key];
                            $data['file']= $filepath;
                            $data['status']='Awaiting Proof Approval';
                            $data['postdate']=Now();
                            $data['proof_type'] = 'order';
                            $connectionWrite->insert($tableName, $data);
                            $connectionWrite->commit();
			    
			    /****************** Start for add vendor 04_03_2014 ***************************/
			    
			    $lastInsertId  = $connectionWrite->fetchOne('SELECT last_insert_id()');
			    
			    $items = $order->getAllItems();
			    foreach($items as $item1)
			    {
				if($item1->getId() == $item[$key])
				$orderItem = $item1;
			    }
			    
			    $temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_item');
                
			    $_product = Mage::getModel('catalog/product')->load($orderItem->getProductId());
			    
			    //$name = $_product->getAttributeText('vendor_id');
			    //$target_vendor = Mage::getResourceModel('catalog/product')->getAttribute("vendor_id")->getSource()->getOptionId($name);
			    
			    $sqlVendor= $connectionRead->select()
                                        ->from($temptableVendor,array('*'))
                                        ->where('item_id= "'.$orderItem->getId().'" AND order_id = "'.$order->getId().'" ');
			    $chkVendor = $connectionWrite->fetchRow($sqlVendor);
			    
			    $connectionWrite->beginTransaction();
			    $data2 = array();
			    $data2['target_user']= $chkVendor['target_user'];
			    $data2['proof_id'] = $lastInsertId;
			    $data2['item_id'] = $orderItem->getId();
			    $data2['order_id'] = $order->getId();
			    $data2['product_id'] = $orderItem->getProductId();
			    $data2['product_sku'] = addslashes($_product->getSku());
			    $data2['postdate'] = NOW();
			    $data2['order_status'] = $order->getStatus();
			    //
			    //$data2['file_recieved'] = $file_recieved;
			    //$data2['proof_approved'] = $proof_approved;
			    $data2['proof_approve_date'] = Now();
			    /*if($row['quantity'] != '')
			    $data2['qty'] = $result[0]['quantity'];*/
			    
			    $connectionWrite->insert($temptableVendor, $data2);
			    $connectionWrite->commit();
			    
			    if($chkVendor['target_user'] != '')
			    {
				$user = Mage::getSingleton('admin/session');
				$userId = $user->getUser()->getUserId();
				//$userEmail = $user->getUser()->getEmail();
				//$userFirstname = $user->getUser()->getFirstname();
				//$userLastname = $user->getUser()->getLastname();
				//$userUsername = $user->getUser()->getUsername();
				//$userPassword = $user->getUser()->getPassword();
				
				$dataall= array('user_id'=>$userId,
						'target_id'=>$chkVendor['target_user'],
						'caption'=>'A job has veen added in you account.',
						'description'=>'A Job has been added in you account. The order id is '.$order->getIncrementId().'.',
						'type'=>'order',
						'entity_id'=>$order->getId(),
						'entity_description'=>'order #'.$order->getIncrementId(),
						'task_type'=>'Independent',
						'task_create'=>'yes',
						'admin'=>'yes'
						);
				
				Mage::getModel('systemalert/systemalert')->sendallalert($dataall);
			    }
			    
			    /****************** End for add vendor 04_03_2014 ***************************/
                          
                        }
                    }
                }
            }
            
        //$this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	mage::helper('AdminLogger')->updatelog($order->getId(),'Add New Proof In Order');
        
            $url1 = Mage::helper('adminhtml')->getUrl("zulfe/sales_order/view/order_id/".$order->getId());
            Mage::log($url1); //To check if URL is correct (and it is correct)
            Mage::app()->getResponse()->setRedirect($url1);
    }
    
    //Proof create update for quote
    public function proofsquoteAction()
    {
        extract($_REQUEST);
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteid);
        $storId =  $quote->getStoreId();
        $customer_id =  $quote->getCustomerId();
      
        
            foreach($_FILES['item_file']['name'] as $key=>$value)
            {
                
                if(isset($_FILES['item_file']['name'][$key]) and (file_exists($_FILES['item_file']['tmp_name'][$key]))) {
                   
                    $file_name=$_FILES['item_file']['name'][$key];
                    
                    $expFilename=explode(".",$file_name);
                    $fileNameVal=time().".".end($expFilename);
                    
                    
                    $mediaPath=Mage::getBaseDir('media') . DS ;
                    //$path2 = $mediaPath.'upload_image/'.$fileNameVal;
                    $path2 = $mediaPath.'proofs/'.$fileNameVal;
                    chmod($path2,0777);
                    $filepath = $fileNameVal;
                    
                    //file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
                    if(move_uploaded_file($_FILES['item_file']['tmp_name'][$key],$path2))
                    {
                        //$tableName = Mage::getSingleton('core/resource')->getTableName('upload_image');
                        $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                        
                         //$sqlProofsSystem="INSERT INTO ".$tableName."  SET store_id = '".$storId."', order_id = '".$quoteid."', customer_id = '".$customer_id."' , item_id = '".$item[$key]."', file = '".$filepath."', status = 'Awaiting Proof Approval',   postdate = NOW(), proof_type = 'quote'";
                         //$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                        //$sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$file_name."',status = 2, created_time = NOW()";
                       // $sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$fileNameVal."',status = 2, created_time = NOW()";
                        $connectionWrite->beginTransaction();
                        $data = array();
                        $data['store_id']= $storId;
                        $data['order_id']=$quoteid;
                        $data['customer_id']= $customer_id;
                        $data['item_id']=$item[$key];
                        $data['file']= $filepath;
                        $data['status']='Awaiting Proof Approval';
                        $data['postdate']=Now();
                        $data['proof_type'] = 'quote';
                        $connectionWrite->insert($tableName, $data);
                        $connectionWrite->commit(); 
                      
                    }
                }
            }
	    
	    Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	    mage::helper('AdminLogger')->updatelog($quoteid,'Add New Proof In Quote');
            
        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteid));
    }
    
    public function downloadAction()
    {
        $file_path=Mage::getBaseDir('media').'/proofs/'.$this->getRequest()->getParam('file');
    
    
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
        header('Content-Disposition: attachment; file="'.$name.'"');
        header("Content-Transfer-Encoding: binary");
        header('Accept-Ranges: bytes');
        header("Cache-control: private");
        header('Pragma: private');
        readfile($file); 
    }
    
    //Proof quantity update for quote
    public function quantityupdateAction()
    {
        extract($_REQUEST);
        //print_r($_REQUEST);
        $flag = 0;
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	 $order = Mage::getModel('sales/order')->load($orderid);
        
        foreach($quantity as $key=>$qty)
        {
            $tableItemName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
            //$sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE item_id = '".$item[$key]."'";
            $sqlItemSystem = $connectionRead->select()
				->from($tableItemName, array('*'))
				->where('item_id =?', $item[$key])
				->where('order_id =?', $orderid);
            $fetchItem = $connectionRead->fetchRow($sqlItemSystem);
            //$fetchItem = $chkItem->fetch();
            
            if($fetchItem['qty_ordered'] >= $qty)
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                //$sqlProofsSystem="UPDATE ".$tableName."  SET quantity = '".$qty."', status = '".$status[$key]."' WHERE entity_id = '".$key."'";
                //$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                
                $connectionWrite->beginTransaction();
                $data = array();
                $data['quantity'] = $qty;
                $data['status'] = $status[$key];
		$data['approve_date'] = Now();
                $where = $connectionWrite->quoteInto('entity_id =?', $key);
                $connectionWrite->update($tableName, $data, $where);
                $connectionWrite->commit();
		
		/********************** Set vendor in item table *******************************/
		if($status[$key] == 'Approved')
		{
		  
		    $temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_item');
		    
		    //Start 05_03_2014
		    $sqlVendor = $connectionRead->select()
				     ->from($temptableVendor, array('*'))
				     ->where('proof_id = "'.$key.'"');
		     $chkVendor = $connectionRead->fetchRow($sqlVendor);
		     
		    $pre_status = $chkVendor['proof_approved'];
		    //End 05_03_2014
		    
		    $connectionWrite->beginTransaction();
		    $data2 = array();
		    $data2['file_recieved'] = 'yes';
		    $data2['proof_approved'] = 'yes';
		    $data2['proof_approve_date'] = Now();
		    $data2['qty'] = $qty;
		    //$where = $connectionWrite->quoteInto('order_id =? AND item_id ="'.$item[$key].'"', $fetchItem['order_id']);
		    $where = $connectionWrite->quoteInto('proof_id =?', $key);//04_03_2014
		    $connectionWrite->update($temptableVendor, $data2, $where);
		    $connectionWrite->commit();
		    
		    /********* Start 03_01_2014 *******************/
		    $items = $order->getAllItems();
		    foreach ($items as $item1) {
			//echo 'sdgfdsg';exit;
			if($item1->getId() == $item[$key])
			{
			    if($pre_status == '' or $pre_status == 'no')//05_03_2014
			    {
				Mage::getModel('timeline/timeline')->UpdateTimeline('proof_approve',$orderid,$item1,'order');
				Mage::getModel('timeline/timeline')->UpdateTimeline('production_start',$orderid,$item1,'order');
			    }
			}
		    }
		    /********* End 03_01_2014 *******************/
		    
		    /**********************************/
		    Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
		    mage::helper('AdminLogger')->updatelog($orderid,'Job Added for Vendor');
		    
		    $alert=array('Job Added for Vendor');
		    Mage::getModel('systemalert/systemalert')->sendalert($alert,'order',$order,$item[$key]);
		    
		    /**********************************/
		
		}
		else{
		    $temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_item');
		    
		    $connectionWrite->beginTransaction();
		    $data2 = array();
		    $data2['file_recieved'] = 'no';
		    $data2['proof_approved'] = 'no';
		    $data2['proof_approve_date'] = '';
		    $where = $connectionWrite->quoteInto('order_id =? AND item_id ="'.$item[$key].'"',$fetchItem['order_id']);
		    $connectionWrite->update($temptableVendor, $data2, $where);
		    $connectionWrite->commit();
		}
		/********************** Set vendor in item table *******************************/
            }
            else
            {
                $flag =1;
            }
        }
	
        if($flag == 1)
        Mage::getSingleton('adminhtml/session')->addError($this->__('Proofs quantity are not more than item quantity.'));
	else{
	    Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	    mage::helper('AdminLogger')->updatelog($orderid,'Edit Proof Item In Order');
	}
            
        //$this->_redirect('*/sales_order/view', array('order_id' => $orderid));
        
        $url1 = Mage::helper('adminhtml')->getUrl("zulfe/sales_order/view/order_id/".$orderid);
        Mage::log($url1); //To check if URL is correct (and it is correct)
        Mage::app()->getResponse()->setRedirect($url1);
    }
    
    //Proof quantity update for order
     public function quantityupdatequoteAction()
    {
        extract($_REQUEST);
        //print_r($_REQUEST);
        $flag = 0;
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        foreach($quantity as $key=>$qty)
        {
            $tableItemName = Mage::getSingleton('core/resource')->getTableName('quotation_items');
            //$sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE quotation_item_id = '".$item[$key]."'";
            $sqlItemSystem = $connectionRead->select()
				->from($tableItemName, array('*'))
				->where('quotation_item_id=?', $item[$key]);
				
            $chkItem = $connectionRead->query($sqlItemSystem);
            $fetchItem = $chkItem->fetch();
	    
	    
            
            if($fetchItem['qty'] >= $qty)
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                //$sqlProofsSystem="UPDATE ".$tableName."  SET quantity = '".$qty."', status = '".$status[$key]."' WHERE entity_id = '".$key."'";
                $connectionWrite->beginTransaction();
                $data = array();
                $data['quantity'] = $qty;
                $data['status'] = $status[$key];
		$data['approve_date'] = Now();
                $where = $connectionWrite->quoteInto('entity_id =?', $key);
                $connectionWrite->update($tableName, $data, $where);
                $connectionWrite->commit();
            }
            else
            {
                $flag =1;
            }
        }
	
        if($flag == 1)
        Mage::getSingleton('adminhtml/session')->addError($this->__('Proofs quantity are not more than item quantity.'));
	else{
	    Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	    mage::helper('AdminLogger')->updatelog($quoteid,'Edit Proof Item In Quote');
	}
            
       $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteid));
    }
    
     public function UpdateAction()
	{
		//create planning
                
                extract($_REQUEST);
                
                $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                
		$orderId = $_REQUEST['order_id'];
		
		try 
		{
			$order = Mage::getModel('sales/order')->load($orderId);
			$created_date = $order->getCreatedAt();
			foreach($order_date as $key=>$value)
                        {
			
                            $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                            //$sqlShipping="UPDATE  ".$temptableShipping." SET order_placed_date = '$value', artwork_date = '$artwork[$key]', proof_date = '$proof[$key]', start_date ='$start[$key]', shipping_date = '$shipping_date[$key]', delivery_date = '$delivery_date[$key]' WHERE entity_id = '".$key."'";
                            //$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                            
                            $connectionWrite->beginTransaction();
                            $data = array();
                            $data['order_placed_date'] = $value;
                            $data['artwork_date'] = $artwork[$key];
                            $data['proof_date'] = $proof[$key];
                            $data['start_date'] = $start[$key];
                            $data['shipping_date'] = $shipping_date[$key];
                            $data['delivery_date'] = $delivery_date[$key];
                            $where = $connectionWrite->quoteInto('entity_id =?', $key);
                            $connectionWrite->update($temptableShipping, $data, $where);
                            $connectionWrite->commit();
                        
                        }
			

			
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Planning created'));
		}
		catch (Exception $ex)
		{
			Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
		}
		
		//redirect
		
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	mage::helper('AdminLogger')->updatelog($orderId,'Edit Planning In Order');
        
        $url1 = Mage::helper('adminhtml')->getUrl("zulfe/sales_order/view/order_id/".$orderId);
        Mage::log($url1); //To check if URL is correct (and it is correct)
        Mage::app()->getResponse()->setRedirect($url1);
        
    	//$this->_redirect($url, array('order_id' => $orderId));
    	
	}
        
        public function deleteproofsAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$user = Mage::getSingleton('admin/session');
	$user_id = $user->getUser()->getUserId();
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
        $temptableProof=Mage::getSingleton('core/resource')->getTableName('proofs');
        
        $connectionWrite->beginTransaction();
        $condition = array($connectionWrite->quoteInto('entity_id=?', $id));
        $connectionWrite->delete($temptableProof, $condition);
        $connectionWrite->commit();
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('designer/designer')));
	mage::helper('AdminLogger')->updatelog($orderid,'Delete Proofs In '.$type);
        
        $select = $connectionRead->select()
                    ->from($temptableProof, array('*'))
                    ->where('order_id=?',$orderid)
                    ->where('proof_type=?',$type);
       
        $url2 = Mage::helper('adminhtml')->getUrl('admin/sales_order/download');
        $result = $connectionRead->fetchAll($select);
	 

        foreach($result as $proof)
        {
     
            echo '<tr class="border">
               <td class="a-center"><a href="'.str_replace('//s','/admin/s',$url2).'file/'.$proof['file'].'/'.'">'.$proof['file'].'</a></td>
               <td class="a-center">';
              
               $tableName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
               
                $select = $connectionRead->select()
                            ->from($tableName, array('*'))
                            ->where('item_id=?',$proof['item_id']);
                
                $itempro = $connectionRead->fetchRow($select);
                
                  $_product = Mage::getModel('catalog/product')->load($itempro['product_id']);
                  echo $_product->getName();
              
               echo '</td>
               <td class="a-center">'.$proof['status'].'</td>
               <td class="a-center"><input type="hidden" name="item['.$proof['entity_id'].']" value="'.$proof['item_id'].'"/><input type="text" style="width: 30px;" name="quantity['.$proof['entity_id'].']" value="'.$proof['quantity'].'"/></td>
               <td class="a-center">'.$proof['comment'].'</td>
               <td class="a-center">'.$proof['p_date'].'</td>
               <td class="a-center">'.$proof['a_date'].'</td>
	       <td class="a-center">
			   <select name="status['.$proof['entity_id'].']"  >
			    <option value="">Select Status</option>';
			    
			    $selected1 = '';
			    $selected2 = '';
			    $selected3 = '';
			    
			    if($proof['status'] == 'Awaiting Proof Approval')
			    $selected1 = 'selected';
			    else if($proof['status'] == 'Approved')
			    $selected2 = 'selected';
			    else if($proof['status'] == 'Disapproved')
			    $selected3 = 'selected';
			 echo '   <option value="Awaiting Proof Approval" '.$selected1.'>Awaiting Proof Approval</option>
			    <option value="Approved" '.$selected2.'>Approved</option>
			    <option value="Disapproved" '.$selected3.'>Disapproved</option>
			   </select>
			  </td>
               <td class="a-center"><a onclick="deleteproof(\''.$proof['entity_id'].'\',\''. $orderid.'\')" style="cursor:pointer;">Delete</a></td>
            </tr>';
     
        }
	
    }
    /************************************ End by dev ***********************************************/
}
?>