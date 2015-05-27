<?php

class Artis_Vendor_Adminhtml_VendorController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendor/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendor/vendor')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('vendor_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('vendor/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_vendor_edit'))
				->_addLeft($this->getLayout()->createBlock('vendor/adminhtml_vendor_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendor')->__('Item does not exist'));
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
	  			
	  			
			$model = Mage::getModel('vendor/vendor');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendor')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendor')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('vendor/vendor');
				 
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
        $vendorIds = $this->getRequest()->getParam('vendor');
        if(!is_array($vendorIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorIds as $vendorId) {
                    $vendor = Mage::getModel('vendor/vendor')->load($vendorId);
                    $vendor->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorIds)
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
        $vendorIds = $this->getRequest()->getParam('vendor');
        if(!is_array($vendorIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorIds as $vendorId) {
                    $vendor = Mage::getSingleton('vendor/vendor')
                        ->load($vendorId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($vendorIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'vendor.csv';
        $content    = $this->getLayout()->createBlock('vendor/adminhtml_vendor_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendor.xml';
        $content    = $this->getLayout()->createBlock('vendor/adminhtml_vendor_grid')
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
    
    public function setvendorAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	$temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_product');
	$sqlVendor="SELECT * FROM ".$temptableVendor." WHERE product_id = '".$product_id."' ";
	$chkVendor = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlVendor);
	
	if(count($chkVendor) > 0)
	{
		$sqlVendor="UPDATE ".$temptableVendor." SET vendor_id = '".$user_id."' WHERE product_id = '".$product_id."' ";
		$chkVendor = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlVendor);
	}
	else if(count($chkVendor) == 0)
	{
		$sqlVendor="INSERT INTO ".$temptableVendor." SET vendor_id = '".$user_id."' , product_id = '".$product_id."' ";
		$chkVendor = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlVendor);
	}
	
	$url = Mage::helper('adminhtml')->getUrl("adminhtml/catalog_product/edit/id/".$product_id);
	$url = str_replace('p//c','p/admin/c',$url);
        Mage::log($url); //To check if URL is correct (and it is correct)
        Mage::app()->getResponse()->setRedirect($url);
    }
    
    public function assigntoAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	
	$temptableService=Mage::getSingleton('core/resource')->getTableName('vendor_order');
	$temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
	
	$sqlService="SELECT * FROM  ".$temptableService." WHERE  item_id = '".$itemid."' AND order_id ='".$orderid."' ";
	$chkService = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlService);
	
	$sqlItem="SELECT * FROM  ".$temptableItem." WHERE  item_id = '".$itemid."' ";
	$chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlItem);
	
	if(count($chkService))
	{
		$sqlService="UPDATE  ".$temptableService." SET assign_to = '".$userid."' WHERE item_id = '".$itemid."' AND order_id ='".$orderid."' ";
		$chkService = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlService);
	}
	else
	{
		$sqlService="INSERT INTO  ".$temptableService." SET assign_to = '".$userid."' , item_id = '".$itemid."' , order_id ='".$orderid."', product_id ='".$chkItem[0]['product_id']."' ";
		$chkService = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlService);
	}
	
	
	
    }
    
    public function addchatAction()
    {
	//print_r($_REQUEST);
	
	extract($_REQUEST);
	
	$user = Mage::getSingleton('admin/session');
	$userId = $user->getUser()->getUserId();
	
	$user_role = Mage::getSingleton('admin/session')->getUser();
	 //Get the role id of the user
	$roleId = implode('', $user_role->getRoles());
	
	$temptableChat=Mage::getSingleton('core/resource')->getTableName('vendor_chat');
	
	$sqlChat="INSERT INTO  ".$temptableChat." SET  vendor_list_id ='".$listid."', user_id ='".$userId."', roll_type_id = '".$roleId."', comment ='".$comment."', is_read = '1', postdate = NOW() ";
	$chkChat = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChat);
	
	$sqlChat="SELECT * FROM  ".$temptableChat." WHERE  vendor_list_id ='".$listid."' ORDER BY entity_id DESC ";
	$chkChat = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChat);
	
	foreach($chkChat as $chat)
	{
		$user = Mage::getModel('admin/user')->load($chat['user_id']);
		echo '<div>'.$chat['postdate'].'  '.$user->getName().' : '.$chat['comment'].'</div>';
	}
	
	
    }
    
    public function chatloadAction()
    {
	//print_r($_REQUEST);
	
	extract($_REQUEST);
	
	
	$temptableChat=Mage::getSingleton('core/resource')->getTableName('vendor_chat');
	
	if($read == 1)
	{
	$sqlChat="UPDATE  ".$temptableChat." SET  is_read = '0' WHERE  vendor_list_id ='".$listid."' ";
	$chkChat = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChat);
	}
	
	$sqlChat="SELECT * FROM  ".$temptableChat." WHERE  vendor_list_id ='".$listid."' ORDER BY entity_id DESC ";
	$chkChat = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChat);
	
	foreach($chkChat as $chat)
	{
		$user = Mage::getModel('admin/user')->load($chat['user_id']);
		echo '<div>'.$chat['postdate'].'  '.$user->getName().' : '.$chat['comment'].'</div>';
	}
    }
    
    public function printloadAction()
    {
	//print_r($_REQUEST);
	extract($_REQUEST);
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$temptableProof=Mage::getSingleton('core/resource')->getTableName('proofs');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
        {
		//$sqlProof="SELECT * FROM  ".$temptableProof." WHERE  order_id ='".$orderid."' AND item_id ='".$itemid."' AND status = 'Approved' ";
		//$chkProof = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlProof);
		
		$select = $connectionRead->select()
		->from($temptableProof, array('*'))
		->where('order_id=?',$orderid)
		->where('item_id=?',$itemid)
		->where('status=?','Approved')
		->order('entity_id DESC')
		->limit(1);
		
		$chkProof = $connectionRead->fetchAll($select);
	}
	
	$temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
	
	$sqlItem="SELECT * FROM  ".$temptableItem." WHERE  item_id = '".$itemid."' ";
	$chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlItem);
	
	$_Product = Mage::getModel('catalog/product')->load($chkItem[0]['product_id']);
	
	$temptableOption=Mage::getSingleton('core/resource')->getTableName('vendor_option');
	
	
	$select = $connectionRead->select()
	  ->from($temptableOption, array('*'))
	  ->where('type=?',1)
	  ->where('product_id=?',$chkItem[0]['product_id']);
	  
	$result = $connectionRead->fetchAll($select);
	
	//Finish Size
	$finish_size = $this->getcustomvalue($result,'Finish Size');
	
	if(!empty($finish_size))
	{
		foreach($finish_size as $size)
		{
			$allsize .= $size['sub_value'].'<br/>';
		}
	}
	
	//Material
	$finish_size = $this->getcustomvalue($result,'Material');
	
	if(!empty($finish_size))
	{
		foreach($finish_size as $size)
		{
			$material .= $size['sub_value'].'<br/>';
		}
	}
	
	//Requirements
	$finish_size = $this->getcustomvalue($result,'Requirements');
	
	if(!empty($finish_size))
	{
		foreach($finish_size as $size)
		{
			$requirements .= '<strong>'.$size['sub_title'].'</strong> : '.$size['sub_value'].'<br/>';
		}
	}
	
	$url2 = Mage::helper('adminhtml')->getUrl('vendor/adminhtml_vendor/download');
	echo '<div class="printtotal">
        <div class="printlist">
            <table class="printtab" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <td>Qty:</td>
                        <td>Finish Size :</td>
                        <td>Description:</td>
                        <td>Metarial</td>
                        <td>Requirements</td>
                    </tr>
                </thead>
                <tbody>';
		
                    echo '<tr>
                        <td>'.$chkProof[0]['quantity'].'</td>
                        <td>'.$allsize.'</td>
                        <td>'.$_Product->getDescription().'</td>
                        <td>'.$material.'</td>
                        <td>'.$requirements.'</td>
                    </tr>';
		    
                echo '</tbody>
            </table>
        </div>
	<div class="totalaction">
        <div><a href="'.str_replace('//s','/admin/s',$url2).'file/'.$chkProof[0]['file'].'/'.'">Download artwork</a></div>
	<div onclick="backlist();" class="printbk">Back</div>
	</div>
    </div>';
    }
    
	public function getcustomvalue($list, $needle)
	{
	   foreach($list as $key => $list)
	   {
	      if ( $list['title'] === $needle )
	      {
		$temptableOption=Mage::getSingleton('core/resource')->getTableName('vendor_option');
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$select = $connectionRead->select()
		  ->from($temptableOption, array('*'))
		  ->where('type=?',1)
		  ->where('parent_id=?',$list['entity_id']);
		  
		$result = $connectionRead->fetchAll($select);
		
		return $result;
	      }
	   }
	   return false;
	}
    
    public function docketloadAction()
    {
	extract($_REQUEST);
	$order = Mage::getModel('sales/order')->load($orderid);
	
	$temptableDocket=Mage::getSingleton('core/resource')->getTableName('vendor_docket');
	
	$sqlDocket="SELECT * FROM  ".$temptableDocket." WHERE vendor_list_id = '".$listid."' ";
	$chkDocket = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlDocket);
	
	$temptableItem=Mage::getSingleton('core/resource')->getTableName('vendor_item');
	
	$sqlItem="SELECT * FROM  ".$temptableItem."  WHERE entity_id ='".$listid."' ";
	$chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlItem);
	
	$url = Mage::helper("adminhtml")->getUrl("vendor/adminhtml_vendor/docketsubmit");
	
	$url1 = Mage::helper('adminhtml')->getUrl('BarcodeLabel/Admin/LabelPreviewOrder/order_id/'.$orderid.'/product_id/'.$chkItem[0]['product_id']);
	
	echo    '<script type="text/javascript">
   
    
    
</script><div class="totaldoc"><input  id="barurl" value="'.$this->getUrl('BarcodeLabel/Admin/PrintProductLabelsOrder', array('product_id' => $chkItem[0]['product_id'],'order_id' => $orderid )).'count/"  type="hidden">
	<form action="'.str_replace('//s','/admin/s',$url).'" method="post" enctype="multipart/form-data">
             <input type="hidden" name="form_key" value="'.Mage::getSingleton('core/session')->getFormKey().'" />
             <input name="listid" id="listid" value="'.$listid.'"  type="hidden">
	       <table class="doccss">
	        
		<tr>
		    <td class="tagname">Customer Name</td>
		    <td class="taginput"><input type="text" name="customer_name" value="'.$order->getCustomerName().'"/></td>
		</tr>
		<tr>
		    <td class="tagname">Order Number</td>
		    <td class="taginput">'.$order->getIncrementId().'</td>
		</tr>
		<tr>
		    <td class="tagname">Supplier Invoice</td>
		    <td class="taginput"><input type="text" name="invoice" value="'.$chkDocket[0]['supplier_invoice'].'"/></td>
		</tr>
		<tr>
		    <td class="tagname">Connote Number</td>
		    <td class="taginput"><input type="text" name="connote" value="'.$chkDocket[0]['connote_number'].'"/></td>
		</tr>
		<tr>
		    <td class="tagname">Shipping : Carton</td>
		    <td class="taginput">
			<select name="carton">';
			
			for($i=1;$i<=4;$i++)
			{
				$selected = '';
				if($chkDocket[0]['carton'] == 'C'.$i)
				$selected = 'selected';
				echo '<option value="C'.$i.'" '.$selected.'>C'.$i.'</option>';
			}
			
			if($chkDocket[0]['status'] == '' or $chkDocket[0]['status'] == 'prod')
			$checked1 = 'checked';
			elseif($chkDocket[0]['status'] == 'packed')
			$checked2 = 'checked';
			elseif($chkDocket[0]['status'] == 'sent')
			$checked3 = 'checked';
			   
		echo '</select>
		    </td>
		</tr>
		<tr>
		    <td></td>
		    <td class="taginput">
			<span><input type="radio" name="status" value="prod" '.$checked1.' /> Prod</span>
			<span><input type="radio" name="status" value="packed" '.$checked2.' /> Packed</span>
			<span><input type="radio" name="status" value="sent" '.$checked3.' /> Sent</span>
		    </td>
		</tr>
		
		<tr>
		    <td colspan="2" >
			<input type="submit" name="submit1" value="Submit" class="docsub"/>
			<span onclick="backdocket();" class="docback">Back</span>
		    </td>
		</tr>
		<tr>
		    <td></td>
		    <td class="taginput">
			<img src="'.$url1.'" border="1"/>
		    </td>
		</tr>
		<tr>
		    <td colspan="2" >
			<table cellspacing="0" class="form-list">
                    <tbody>
                        <tr>
                            <td class="label"><label for="label_count">'.$this->__('Label count').'</label></td>
                            <td class="value">
                                <input name="label_count" id="label_count" value="" class=" input-text" type="text"></td>
                            <td class="scope-label"><span class="nobr"></span></td>
                        </tr>
                        <tr>
                            <td class="label"><label for="label_count">'.$this->__('Print').'</label></td>
                            <td class="value">
                                <input type="button" id="print_button" value="'.$this->__('Print').'" onclick="urlMaker()"></td>
                            <td class="scope-label"><span class="nobr"></span></td>
                        </tr>
                    </tbody>
                </table>
		    </td>
		</tr>
		
	       </table> </form>
	       
	       
	    </div>';
    }
    
    public function docketsubmitAction()
    {
	//print_r($_REQUEST);
	
	extract($_REQUEST);
	
	$temptableDocket=Mage::getSingleton('core/resource')->getTableName('vendor_docket');
	
	$sqlDocket="SELECT * FROM  ".$temptableDocket." WHERE vendor_list_id = '".$listid."' ";
	$chkDocket = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlDocket);
	
	if(count($chkDocket) > 0)
	{
		$sqlDocket="UPDATE  ".$temptableDocket." SET customer_name = '".$customer_name."',  supplier_invoice ='".$invoice."', connote_number = '".$connote."', carton ='".$carton."', status = '".$status."' WHERE vendor_list_id ='".$listid."' ";
		$chkDocket = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDocket);	
	}
	else{
	
		$sqlDocket="INSERT INTO  ".$temptableDocket." SET customer_name = '".$customer_name."', vendor_list_id ='".$listid."', supplier_invoice ='".$invoice."', connote_number = '".$connote."', carton ='".$carton."', status = '".$status."', postdate = NOW() ";
		$chkDocket = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDocket);
		
	}
	
	
	
	$temptableItem=Mage::getSingleton('core/resource')->getTableName('vendor_item');
	
	$sqlItem="UPDATE  ".$temptableItem." SET  progress = '".$status."' WHERE entity_id ='".$listid."' ";
	$chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlItem);
	
	$this->_redirect('*/*/');
	
    }
        
    public function printassignAction()
    {
	//print_r($_REQUEST);exit;
	extract($_REQUEST);
	
	foreach($user as $userid => $print)
	{
		$temptablePrint=Mage::getSingleton('core/resource')->getTableName('vendor_user_print');
		$sqlPrint="SELECT * FROM  ".$temptablePrint." WHERE  user_id ='".$userid."' ";
		$chkPrint = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlPrint);
		
		if(count($chkPrint)>0)
		{
			$sqlPrint="UPDATE  ".$temptablePrint." SET print_number = '".$print."' WHERE  user_id ='".$userid."' ";
			$chkPrint = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPrint);
		}
		else
		{
			$sqlPrint="INSERT INTO  ".$temptablePrint." SET print_number = '".$print."' , user_id ='".$userid."' ";
			$chkPrint = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPrint);
		}
	}
	
	$this->_redirect('*/*/');
    }
    
    public function downloadAction()
    {
        $file_path=Mage::getBaseDir('media').'/proofs/'.$this->getRequest()->getParam('file');
    
    
    //Call the download function with file path,file name and file type
    //download_file($file_path, ''.$_REQUEST['file'].'', 'text/plain');
    $this->download_file($file_path, ''.$this->getRequest()->getParam('file').'', 'text/plain');
    
    
    }
    
    public function downloadcustomAction()
    {
        $file_path=Mage::getBaseDir('media').'/custom_product/'.$this->getRequest()->getParam('file');
    
    
    //Call the download function with file path,file name and file type
    //download_file($file_path, ''.$_REQUEST['file'].'', 'text/plain');
    $this->download_file($file_path, ''.$this->getRequest()->getParam('file').'', 'text/plain');
    
    
    }
    
    public function setwarehouseAction()
    {
	//print_r($_REQUEST);exit;
	
	extract($_REQUEST);
	
	$temptableWarehouse=Mage::getSingleton('core/resource')->getTableName('warehouse_instraction');
	$sqlWarehouse="DELETE FROM ".$temptableWarehouse." WHERE product_id = '".$productid."' ";
	$chkWarehouse = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlWarehouse);
	
	foreach($caption as $key=>$value1)
	{
		$title = addslashes($value1);
		if($title != '')
		{
			$sqlWarehouse="INSERT INTO ".$temptableWarehouse." SET product_id = '".$productid."', caption = '".$title."', caption_value = '".$value[$key]."' ";
			$chkWarehouse = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlWarehouse);
		}
	}
	
	$url = Mage::helper('adminhtml')->getUrl("adminhtml/catalog_product/edit/id/".$productid);
	$url = str_replace('p//c','p/admin/c',$url);
        Mage::log($url); //To check if URL is correct (and it is correct)
        Mage::app()->getResponse()->setRedirect($url);
    }
    
    public function setoptionAction()
    {
	//print_r($_REQUEST);exit;
	
	extract($_REQUEST);
	
	$temptableOption=Mage::getSingleton('core/resource')->getTableName('vendor_option');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$connectionWrite->beginTransaction();
	$condition = array($connectionWrite->quoteInto('product_id=?', $product_id),$connectionWrite->quoteInto('type=?', $type));
	$connectionWrite->delete($temptableOption, $condition);
	
	$connectionWrite->commit();
	
	$_Product = Mage::getModel('catalog/product')->load($product_id);

	
	foreach($title as $key=>$value1)
	{
		$title2 = addslashes($value1);
		if($title2 != '')
		{
			$connectionWrite->beginTransaction();
			$data = array();
			$data['title'] = $title2;
			$data['product_id'] = $product_id;
			$data['type'] = $type;
			$data['postdate'] = date('Y-m-d H:i:s');
			$connectionWrite->insert($temptableOption, $data);
			$connectionWrite->commit();
			
			$lastInsertId = $connectionWrite->fetchOne('SELECT last_insert_id()');
			
			
			foreach($customvalue[$key] as $key2=>$value2)
			{
				$fileNameVal = '';
				if(isset($_FILES['customfile']['name'][$key][$key2]) and (file_exists($_FILES['customfile']['tmp_name'][$key][$key2])))
				{
                   
					$file_name=$_FILES['customfile']['name'][$key][$key2];
					
					$expFilename=explode(".",$file_name);
					$fileNameVal= $_Product->getSku().'_'.$type.'_'.$lastInsertId.".".end($expFilename);
					
					
					$mediaPath=Mage::getBaseDir('media') . DS ;
					//$path2 = $mediaPath.'upload_image/'.$fileNameVal;
					$path2 = $mediaPath.'custom_product/'.$fileNameVal;
					chmod($path2,0777);
					$filepath = $fileNameVal;
					
					//file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
					move_uploaded_file($_FILES['customfile']['tmp_name'][$key][$key2],$path2);
				}
				else
				$fileNameVal = $existfile[$key][$key2];
		
				$connectionWrite->beginTransaction();
				$data1 = array();
				$data1['sub_title'] = $customtitle[$key][$key2];
				$data1['sub_value'] = $value2;
				$data1['parent_id'] = $lastInsertId;
				$data1['product_id'] = $product_id;
				if($fileNameVal != '')
				$data1['file'] = $fileNameVal;
				$data1['type'] = $type;
				$data1['postdate'] = date('Y-m-d H:i:s');
				$connectionWrite->insert($temptableOption, $data1);
				$connectionWrite->commit(); 
			}
		}
	}
	
	$url = Mage::helper('adminhtml')->getUrl("adminhtml/catalog_product/edit/id/".$product_id);
	$url = str_replace('p//c','p/admin/c',$url);
        Mage::log($url); //To check if URL is correct (and it is correct)
        Mage::app()->getResponse()->setRedirect($url);
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