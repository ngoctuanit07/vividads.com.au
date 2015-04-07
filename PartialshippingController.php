<?php

class Partialshipping_Partialshipping_Adminhtml_PartialshippingController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('partialshipping/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('partialshipping/partialshipping')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('partialshipping_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('partialshipping/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('partialshipping/adminhtml_partialshipping_edit'))
				->_addLeft($this->getLayout()->createBlock('partialshipping/adminhtml_partialshipping_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('partialshipping')->__('Item does not exist'));
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
	  			
	  			
			$model = Mage::getModel('partialshipping/partialshipping');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('partialshipping')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('partialshipping')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('partialshipping/partialshipping');
				 
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
        $partialshippingIds = $this->getRequest()->getParam('partialshipping');
        if(!is_array($partialshippingIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($partialshippingIds as $partialshippingId) {
                    $partialshipping = Mage::getModel('partialshipping/partialshipping')->load($partialshippingId);
                    $partialshipping->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($partialshippingIds)
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
        $partialshippingIds = $this->getRequest()->getParam('partialshipping');
        if(!is_array($partialshippingIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($partialshippingIds as $partialshippingId) {
                    $partialshipping = Mage::getSingleton('partialshipping/partialshipping')
                        ->load($partialshippingId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($partialshippingIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'partialshipping.csv';
        $content    = $this->getLayout()->createBlock('partialshipping/adminhtml_partialshipping_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'partialshipping.xml';
        $content    = $this->getLayout()->createBlock('partialshipping/adminhtml_partialshipping_grid')
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
    
    
    
    /************************* Custom Function **************************/
    
	public function newformPostAction()
	{
		//echo "<pre>";print_r($_REQUEST);echo "</pre><br>";
		$CusId = $_REQUEST['customerid'];
		
		$Street = $_REQUEST['street'];
		
		$url = "zulfe/sales_order_shipment/new/order_id/".$_REQUEST['oid'];
		$url = str_replace('p//s','p/admin/s',$url);
		$ReadConn = Mage::getSingleton('core/resource')->getConnection('core_read');
		$WriteConn = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$eav_entity_type = Mage::getSingleton('core/resource')->getTableName('eav_entity_type');
		$eav_attribute = Mage::getSingleton('core/resource')->getTableName('eav_attribute ');
		$customer_address_entity = Mage::getSingleton('core/resource')->getTableName('customer_address_entity');
		$customer_address_entity_int = Mage::getSingleton('core/resource')->getTableName('customer_address_entity_int');
		$customer_address_entity_text = Mage::getSingleton('core/resource')->getTableName('customer_address_entity_text');
		$customer_address_entity_varchar = Mage::getSingleton('core/resource')->getTableName('customer_address_entity_varchar');
		
		
		$SelectEntityId = "SELECT entity_type_id FROM $eav_entity_type WHERE entity_type_code='customer_address'";
		try {
		    $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($SelectEntityId);
		    $fetchSelectEI = $chkSystem->fetch();
		    } catch (Exception $e){
		    echo $e->getMessage();
		}
		$EntityId = $fetchSelectEI['entity_type_id'];
		
		$SelectAttributeId = "SELECT attribute_id FROM $eav_attribute WHERE entity_type_id ='".$EntityId."' AND attribute_code='region_id'";
		try {
		    $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($SelectAttributeId);
		    $fetchSelectAI = $chkSystem->fetch();
		    } catch (Exception $e){
		    echo $e->getMessage();
		}
		$AttributeId = $fetchSelectAI['attribute_id'];
		
		$SelectAttributeStreetId = "SELECT attribute_id FROM $eav_attribute WHERE entity_type_id ='".$EntityId."' AND attribute_code='street'";
		try {
		    $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($SelectAttributeStreetId);
		    $fetchSelectSI = $chkSystem->fetch();
		    } catch (Exception $e){
		    echo $e->getMessage();
		}
		$StreetAttributeId = $fetchSelectSI['attribute_id'];
		
		$created_at = date("Y-m-d h:m:s");
		
		
		$InsertCustomerAddressEntity = "INSERT INTO $customer_address_entity (`entity_id` ,`entity_type_id` ,`attribute_set_id` ,
		`increment_id` ,`parent_id` ,`created_at` ,
		`updated_at` ,`is_active`)VALUES (
		'', '".$EntityId."', '0', NULL ,  '".$CusId."' ,'".$created_at."' ,'".$created_at."', '1')";
		$WriteConn->query($InsertCustomerAddressEntity);
		
		$SelectSecondEntityId = "SELECT MAX(entity_id) FROM $customer_address_entity WHERE entity_type_id ='".$EntityId."' AND parent_id='".$CusId."'";
		try {
		    $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($SelectSecondEntityId);
		    $fetchSelectEntityI = $chkSystem->fetch();
		    } catch (Exception $e){
		    echo $e->getMessage();
		}
		$EntitiSecondId = $fetchSelectEntityI['MAX(entity_id)'];
		
		
		$InsertCustomerAddressEntityInt = "INSERT INTO $customer_address_entity_int (value_id, entity_type_id, attribute_id, entity_id, value)
		VALUES ('','".$EntityId."', '".$AttributeId."','".$EntitiSecondId."',0)";
		$WriteConn->query($InsertCustomerAddressEntityInt);
		
		$InsertCustomerAddressEntityText = "INSERT INTO $customer_address_entity_text (value_id, entity_type_id, attribute_id, entity_id, value)
		VALUES ('','".$EntityId."', '".$StreetAttributeId."','".$EntitiSecondId."','".$Street."')";
		$WriteConn->query($InsertCustomerAddressEntityText);
		
		foreach($_REQUEST as $key=>$value){
			//echo "<br>K: ".$key;
			//echo "<br>V: ".$value;
			$SelectAttributeCode = "SELECT attribute_id FROM $eav_attribute WHERE entity_type_id='".$EntityId."' AND attribute_code ='".$key."' ";
			$SelACodeResults = $WriteConn->query($SelectAttributeCode);
			foreach($SelACodeResults as $SelACodeRes){
				//print_r($SelACodeRes);
				$InsertCustomerAddressEntityVarchar = "INSERT INTO $customer_address_entity_varchar (value_id, entity_type_id, attribute_id, entity_id, value)
		VALUES ('','".$EntityId."', '".$SelACodeRes['attribute_id']."','".$EntitiSecondId."','".$value."')";
					$WriteConn->query($InsertCustomerAddressEntityVarchar);
			}
		}
	}
	
	//7-11-2013 SOC 
	public function setaddressAction(){
		
		$AdrsId = $_REQUEST['addrsid'];
		
		$addressLoadId = Mage::getModel('customer/address')->load($AdrsId);
                $country_name=Mage::app()->getLocale()->getCountryTranslation($addressLoadId['country_id']);
                $Name = $addressLoadId['firstname'].' '.$addressLoadId['lastname'];
                $AdrInfo = $addressLoadId['city'].', '.$addressLoadId['region'].', '.$addressLoadId['postcode']; 
		
		$str='<input type="radio" name="shipAddress" value="'.$AdrsId.'"/><address>'.$Name.'<br/>'.$addressLoadId["street"].'<br/>'.$AdrInfo.'<br/>'.$country_name.'<br/>T : '.$addressLoadId["telephone"].'</address>';
		echo $str;
	}
	//7-11-2013 EOC
	
	//8-11-2013 SOC 
	public function setshippingAction(){
		$shipcode=$_REQUEST['shipcode'];
		$shippingTitle = Mage::getStoreConfig('carriers/'.$shipcode.'/title');
	        $shippingPrice = Mage::getStoreConfig('carriers/'.$shipcode.'/price');
		
		
		$str='<input type="radio" value="'.$shippingTitle.'__'.$shippingPrice.'" name="shipmethod"><strong>'.$shippingTitle.'</strong>Total Shipping Charges:<span class="price">AU$'.number_format($shippingPrice,2).'</span>';     
		    
                echo $str;    
                
	}
	//8-11-2013 EOC
	
	//16-11-2013 SOC
	public function partshipformAction(){
		extract($_REQUEST);
		//echo "<pre>";print_r($_REQUEST);
		//exit;
		$box = explode('@@@@',$_REQUEST['boxstr']);
		
		$method = explode("__",$shipping_method_tntl);
		
		$tnt_methos = array('Express'=>'EX','Fashion Express'=>'FE','General'=>'GE','Sameday'=>'701','9:00 Express'=>'712','10:00 Express'=>'X10','12:00 Express'=>'X12','CIT Pay As You Use'=>'73','Overnight Express'=>'75','Road Express'=>'76','Air/Road Combo'=>'77','Technology Express'=>'717','Fashion Express'=>'718');
		//print_r($_REQUEST['boxstr']);
		//exit;
		
		$order = Mage::getModel('sales/order')->load($orderIdP);
		$totalqtyOrdered=round($order->getTotalQtyOrdered());
		$qtyShipped=0;
		$flg=0;
		
		
		foreach($_REQUEST['shipment'] as $key=>$val){
			 
			foreach($val as $k=>$qty){
				
				$qtyord=$qtyorderd[$key][$k];
				//15-1-2014 S
				if($qty > $qtyord ){
					$flg++;
					
				}else{
					$qtyShipped=$qtyShipped+$qty;	
				}
				//15-1-2014 E
			}
			
		}
		if($flg > 0){ //15-1-2014 added
			
			$msg='Shipped quantity is greater than total quantity ordered.';
			$this->_getSession()->addError($msg);
			
			$url = Mage::helper('adminhtml')->getUrl("adminhtml/sales_shipment/view/shipment_id/".$shipmentId);
			Mage::log($url); 
       			Mage::app()->getResponse()->setRedirect($url);
			//echo $this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
			//exit;
		}else{
		//exit;
		//if( $qtyShipped < $totalqtyOrdered || $qtyShipped != 0){
			//$data = $this->getRequest()->getPost('shipment');
			
			if(strpos($_REQUEST['sel_adr'],'_S'))
			{
				$addr = explode('_S',$_REQUEST['sel_adr']);
				$addressLoadId = $order->getShippingAddress();
			}
			else
			{
				$addr = array(0 => $_REQUEST['sel_adr']);
				$addressLoadId = Mage::getModel('customer/address')->load($addr[0]);
			}
			
			
			$shipAd=$addr[0];
			$shipMd=$_REQUEST['sel_metd'];
			$ordId=$_REQUEST['orderIdP'];
			$ordincrId=$_REQUEST['orderIncrementId'];
			$custmerId=$_REQUEST['customerId'];
			$shM=explode("__",$shipMd);
			$shMethod=$shM[0];
			$shPrice=$shM[1];
			//exit;
			$shpAmt=$order->getShippingAmount();
			$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
			$tableName2 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
			$tableName4 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment');
			$tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
			$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
			$tableNameItem = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_item');
			$tableNameTrack = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_track');
			
			
			
			$select = $connectionRead->select()->from($tableName3, array('*'))->where('order_id=?',$orderIdP)->order('entity_id DESC')->limit(1);
			$row = $connectionRead->fetchRow($select);
			
			
			$select2 = $connectionRead->select()->from($tableName2, array('*'))->where('entity_id=?',$orderIdP);
			$row2 = $connectionRead->fetchRow($select2);
			$baseTotalDue=$row2['base_total_due'];
			$TotalDue=$row2['total_due'];
			$baseGrandTotal=$row2['base_grand_total'];
			$GrandTotal=$row2['grand_total'];
			
			if($shPrice > $shpAmt){
			    $diff=$shPrice-$shpAmt;
			    $baseTotalDueN=$baseTotalDue + $diff;
			    $TotalDueN=$TotalDue + $diff;
			    $baseGrandTotalN=$baseGrandTotal + $diff;
			    $GrandTotalN=$GrandTotal + $diff;
			    
			}elseif($shpAmt > $shPrice){
			    $diff=$shpAmt - $shPrice;
			    $baseTotalDueN=$baseTotalDue - $diff;
			    $TotalDueN=$TotalDue - $diff;
			    $baseGrandTotalN=$baseGrandTotal - $diff;
			    $GrandTotalN=$GrandTotal - $diff;
			    
			}else{
			    
			}
			
			if(count($row) > 0){
				$shipmenIncrmnttId=$row['increment_id'];
				$pos1 = strpos($shipmenIncrmnttId, '-');
			
				if($pos1 != false) {
					$sincr=explode('-',$shipmenIncrmnttId);
					$a=$sincr[1]+1;
					$shipmenIncrmnttId=$sincr[0]."-".$a;
				}
				else {
					if($baseTotalDueN != $baseTotalDue){
			    
						$connectionWrite->beginTransaction();
						$data = array();
						if($baseTotalDueN) $data['base_total_due'] = $baseTotalDueN;
						if($TotalDueN) $data['total_due'] = $TotalDueN;
						if($baseGrandTotalN) $data['base_grand_total'] = $baseGrandTotalN;
						if($GrandTotalN) $data['grand_total'] = $GrandTotalN;
						
						$where = $connectionWrite->quoteInto('entity_id =?', $orderIdP);
						$connectionWrite->update($tableName2, $data, $where);
						$connectionWrite->commit();
					
					}
					
					
					$shipmenIncrmnttId=$shipmenIncrmnttId."-1";
				}
			}
			
			if($shipAd !='' && $shipMd !='' && $_REQUEST['boxstr'] != '' ){
				
				$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
				$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
				
				//$created_at = date("Y-m-d h:m:s");
				$t=time();
				//$created_at = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
				
				$created_at = date("Y-m-d H:i:s", $t);
				$date_post = strtotime($order->getCreatedAtDate()); 
				$Ordtime=date('Y-m-d H:i:s',$date_post );
				
				$Name = $addressLoadId['firstname'].' '.$addressLoadId['lastname'];
				
				
				
				
				
				//foreach($_REQUEST['shipment'] as $key=>$val){
				//	
				//	foreach($val as $k=>$qty){
				//		//echo "PRC: ".$k;
				//		$connectionWrite->beginTransaction();
				//		$_Product = Mage::getModel('catalog/product')->load($k);
				//		
				//		//echo "<pre>";print_r($_Product);
				//		$price=$_Product->getPrice();
				//		$data1 = array();
				//		if($shipmentId) $data1['parent_id']= $shipmentId;
				//		$tPrice=$price*$qty;
				//		if($tPrice) $data1['price']= ($tPrice);
				//		if($_Product->getWeight()) $data1['weight']= $_Product->getWeight();
				//		if($qty) $data1['qty']=$qty;
				//		if($k) $data1['product_id']=$k;
				//		if($key) $data1['order_item_id']=$key;
				//		if($_Product->getDescription()) $data1['description']=$_Product->getDescription();
				//		if($_Product->getName()) $data1['name']=$_Product->getName();
				//		if($_Product->getSku()) $data1['sku']=$_Product->getSku();
				//		
				//		//echo $data1['weight']= $_Product->getWeight(); exit;
				//		//$data1['qty']=$qty;
				//		//$data1['product_id']=$k;
				//		//$data1['order_item_id']=$key;
				//		//$data1['description']=$_Product->getDescription();
				//		//$data1['name']=$_Product->getName();
				//		//$data1['sku']=$_Product->getSku();
				//		
				//		$connectionWrite->insert($tableName, $data1);
				//		$connectionWrite->commit();
				//	}
				//
				//}
				
				////27-12-2013 for pdf  S 
				$vivid['identifier']='VIVID';
				$vivid['account']='21664906';
				$vivid['company']='VIVID ADS';
				$vivid['address1'] ='302 BRIDGE STREET';
				$vivid['address2']='';
				$vivid['city']='PORT MELBOURNE' ;
				$vivid['state']='VIC';
				$vivid['zip']='3207';
				$vivid['name']='DESPATCH';
				$vivid['email']="support@vividads.com.au";	
				$date = date('d-m-Y');
				
				
				$count = 0;	$paid = 0.00;			
				$status = $order->getStatus();
				$subtotal = $order->getSubtotal();
				$totalamt = $order->getGrandTotal();			
				$totalDues = $order->getTotalDue();
				$paid = $totalamt-$totalDues;
				if($paid < $totalamt)					 
					$stamp_url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/NOT PAID.gif";				
				if($paid > 0 && $paid == $totalamt)								
					$stamp_url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/Paid.gif";
				if($paid > 0 && $paid < $totalamt)								
					$stamp_url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/Partial Paid.gif";
					//echo $stamp_url;exit;
					
				//$shippingAddressId = $shipmentsModel['shipping_address_id'];	
				//$addressModel = Mage::getModel('sales/order_address')->load($shippingAddressId);
				
				$order = Mage::getModel('sales/order')->load($ordincrId, 'increment_id');
				if ($order->hasInvoices()) {
				    $invIncrementIDs = array();
				    foreach ($order->getInvoiceCollection() as $inv) {
					$invIncrementID = $inv->getIncrementId();
				    //other invoice details...
				    } Mage::log($invIncrementID);
				}
				
				
				//echo $invIncrementID;exit;
				$postCode = $addressLoadId['postcode'];
				$city = $addressLoadId['city'];
				$firstName = $addressLoadId['firstname'];
				$lastName = $addressLoadId['lastname'];
				$region = $addressLoadId['region'];
				$street = $addressLoadId['street'];
				$telephone = $addressLoadId['telephone'];
				$company = $addressLoadId['company'];
				
				$collection = Mage::getModel('sales/order_shipment_comment')->getCollection()->addFieldToFilter('parent_id',array('eq' => $shipId));
				foreach($collection as $_collection){
					$commentId = $_collection->getId(); 	
					$commentModel = Mage::getModel('sales/order_shipment_comment')->load($commentId);
					$comment = $commentModel->getComment();
				}
				
				$style = array(
					'position' => '',
					'align' => 'C',
					'stretch' => false,
					'fitwidth' => true,
					'cellfitalign' => '',
					'border' => true,
					'hpadding' => 'auto',
					'vpadding' => 'auto',
					'fgcolor' => array(0,0,0),
					'bgcolor' => false, //array(255,255,255),
					'text' => true,
					'font' => 'helvetica',
					'fontsize' => 8,
					'stretchtext' => 4					
				);															
				//require_once('fpdf/fpdf.php');
				require_once('tcpdf/tcpdf.php');
				//$pdf = new FPDF('p','cm','A4');
				$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				$pdf->SetFont('helvetica', '', 8);				
					
				
				$total = count($_REQUEST['shipment']);
				$i=0;
				
				/********************* Start upload file to ft server *****************************/
					$l=0;
					$my_file = 'file.txt';
					
					
					$date1 = date('dMY');
					do{
						$file = Mage::getBaseDir('media') . DS ."shiplabel" . DS ."ET-".$date1.$f.'.txt';
						$remote_file = '/outbox/'."ET-".$date1.$f.'.txt';
						$f++;
					}while(file_exists($file));
					
					$handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
					$manifat_number = 1297;
					
					/******************* Start to insert the shipment table *************************/
					$connectionWrite->beginTransaction();
					$data3=array();
					if($order->getStoreId()) $data3['store_id']= $order->getStoreId();
					if($order->getweight()) $data3['total_weight']= $order->getweight();
					if($qtyShipped) $data3['total_qty']= $qtyShipped;
					if($orderIdP) $data3['order_id']= $orderIdP;
					if($customerid) $data3['customer_id']= $customerid;
					if($sel_adr) $data3['shipping_address_id']= $sel_adr;
					if($order->getBillingAddressId()) $data3['billing_address_id']= $order->getBillingAddressId();
					
					$ship=explode('__',$sel_metd);
					if($ship[0]) $data3['shippingmethod']= $ship[0];
					if($ship[1]) $data3['shippingrate']= $ship[1];
					
					if($shipmenIncrmnttId) $data3['increment_id']= $shipmenIncrmnttId;
					if($created_at) $data3['created_at']= $created_at;
					if($created_at) $data3['updated_at']= $created_at;
					
					if(($f-1) == 0)
					$data3['et_file']= "ET-".$date1.'.txt';
					else
					$data3['et_file']= "ET-".$date1.$f.'.txt';
					
					$connectionWrite->insert($tableName4, $data3);
					$connectionWrite->commit();
					/******************* End to insert the shipment table*************************/
					
					$lastId = $connectionWrite->fetchOne('SELECT last_insert_id()');
					
					$conNoteNumber="VVD".(str_pad($lastId,9,"0",STR_PAD_LEFT));
					
					
					/******************* Start to insert the shipment grid table *************************/
					$connectionWrite->beginTransaction();
					$data = array();
					//echo "store id : ".$order->getId(); exit;
					
					if($order->getStoreId()) $data['store_id']= $order->getStoreId();
					if($qtyShipped) $data['total_qty']=$qtyShipped;
					if($orderIdP) $data['order_id']=$orderIdP;
					
					if($qtyShipped < $totalqtyOrdered) $data['status']='Partially Shipped';
					elseif($qtyShipped == $totalqtyOrdered) $data['status']='OK for Shipping';
					else $data['status']='Pending Shipment';
					
					//$data['ship_id']=$shPrice;
					
					//echo "<pre>";print_r($row);
					//exit;
					
					if($shipmenIncrmnttId) $data['increment_id']=$shipmenIncrmnttId;
					if($orderIncrementId) $data['order_increment_id']=$orderIncrementId;
					if($created_at) $data['created_at']=$created_at;
					if($order->getCreatedAtDate()) $data['order_created_at']=$order->getCreatedAtDate();
					if($Name) $data['shipping_name']=$Name;
					if($order->getStatusLabel()) $data['payment_status']=$order->getStatusLabel();
					if($conNoteNumber) $data['track_number']=$conNoteNumber;
					
					$connectionWrite->insert($tableName3, $data);
					$connectionWrite->commit();
					
					/******************* End to insert the shipment grid table*************************/

					$data = "A".(str_pad($manifat_number,20,"0",STR_PAD_LEFT))."                                          TNT".date("YmdHi")."12";
					$l++;
					$data .="
					
					B".(str_pad($manifat_number,10,"0",STR_PAD_LEFT))."VIVID          21664906  ".$vivid['company']."             ".$vivid['address1']."     ".$vivid['city']."     ".$vivid['state']."     ".$vivid['zip'];
					$l++;
					$data .="
					
					C".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($counter,3,"0",STR_PAD_LEFT))."                         ".$firstName."    ".$lastName."     ".$company."      ".$street.'      '.$city."     ".$region."        ".$postcode."  ".$firstName."    ".$lastName."  ".$telephone."  ".date("dmY").$tnt_methos[$method[0]]."  0S0000000000 ";
					$l++;
				
				foreach($box as $q=>$boxvalue)
				{
					$j=1;
					$boxdata = explode('__',$boxvalue);
					foreach($boxdata as $boxdataall)
					{
						$boxdatain = explode(':',$boxdataall);
						$boxdata_item[$boxdatain[0]] = $boxdatain[1];
					}
					
					
					
					
					
					if($boxvalue != '')
					{
						$data .="
						F".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($counter,3,"0",STR_PAD_LEFT)).$invIncrementID."    BOX                       ".(str_pad($counter,5,"0",STR_PAD_LEFT)).(str_pad($counter,5,"0",STR_PAD_LEFT)).(str_pad($boxdata_item['weight'],9,"0",STR_PAD_LEFT))."KG".(str_pad($boxdata_item['length'],7,"0",STR_PAD_LEFT)).(str_pad($boxdata_item['width'],7,"0",STR_PAD_LEFT)).(str_pad($boxdata_item['height'],7,"0",STR_PAD_LEFT))."CM"
						.(str_pad(($boxdata_item['height']*$boxdata_item['width']*$boxdata_item['length']),10,"0",STR_PAD_LEFT))."CC"
						;
						$l++;
					}
					
					for($j ; $j<=$boxdata_item['box'] ; $j++)
					{
						
						//$conNoteNumber="VVD000055714";
						//$conNoteNumber=$row['increment_id'];
						$conNote=str_replace("VVD","00313113",$conNoteNumber);// It wil be dynamic
						//$conNote=$conNoteNumber;// It wil be dynamic
						$barcode = "6104".$conNote.(str_pad($counter,3,"0",STR_PAD_LEFT))."0".(str_pad($postCode,5,"0",STR_PAD_RIGHT));						   
						$params = TCPDF_STATIC::serializeTCPDFtagParameters(array($barcode, 'C128', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
						$itemno = (str_pad($q,3,"0",STR_PAD_LEFT)) . $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
						//$itemno =  $key;	
						//$pdf->AddPage("P","A4");		
$tbl[] = <<<EOD
<table width="240" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
  <td colspan="1" align="left" ><table width="100%" border="" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="50%" style="border-bottom:#000 2px solid"><table  border="0" align="left" cellpadding="0" cellspacing="0" height="10px">
	<tr>
	  <td align="left" valign="top"><span style="line-height:0.8em;font-size:38;font-weight:bold">{$postCode}</span>
	  <span style="font-size:13;font-weight:bold">{$region} </span></td>
	</tr>
      </table></td>
      <td  align="right" style="border-bottom:#000 2px solid"><table border="0" cellpadding="0" cellspacing="0">
	<tr>
	  <td align="right" style="font-size:15"><strong>LV3</strong></td>
	</tr>
	<tr>
	  <td align="right" style="font-size:13"><strong>{$conNoteNumber}</strong></td>
	</tr>
	<tr>
	  <td align="right" valign="bottom" style="font-size:8">Itm:$itemno </td>
	</tr>
      </table></td>
    </tr>
    <tr>
  <td style="font-size:13"><strong>{$method[0]}</strong></td>
    <td align="right">
<table border="0" cellspacing="0" cellpadding="0" align="right">
	   <tr>
	      <td style="font-size:7">Sort</td>
	      <td rowspan="2" style="font-size:13"><strong>30015</strong></td>
	    </tr>
	    <tr>
	      <td style="font-size:7">Bin:</td>
	      </tr>
	  </table>
</td>
  </tr>
  <tr>
    <td colspan="2"  style="border-top:#000 2px solid"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
	<td align="left" style="font-size:9">$date</td>
	<td align="center" style="font-size:9">{$j} of {$boxdata_item['box']}</td>
	<td align="center"style="font-size:9">Item Wt:{$boxdata_item['weight']} Kg</td>
	<td align="right" style="font-size:9">Ex LV3</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" align="center" style="border-bottom:#000 2px solid;border-top:#000 2px solid;font-size:9">Does not contain any dangerous goods</td>
  </tr>
  <tr>
    <td colspan="2" align="left"  >
    <table width="100%" border="0" align="left">
      <tr>
	<td valign="top" style="font-size:9" width="20">To:</td>
	<td style="font-size:14;font-weight:bold;" width="220" height="80">{$firstName} {$lastName},<br/> {$company},<br/> {$street}, , {$city}, {$region}<br/></td>
      </tr>
    </table>
      </td>
  </tr>
  <tr>
	<td valign="top" width="30" style="border-top:#000 2px solid;font-size:8">From:</td>
	<td width="220" style="border-top:#000 2px solid;font-size:7">{$vivid['company']}, {$vivid['address1']}, {$vivid['city']} {$vivid['state']}<br>
Senders Ref: {$invIncrementID}</td>
      </tr>
<tr> <td colspan="2"></td></tr>
  <tr>
    <td colspan="2" >
	<table border="0" width="230" align="center" cellpadding="0" cellspacing="0" >
	<tr>
		<td colspan="2" align="left" style="font-size:7" height="20">
		<strong>Special Instructions: $comment</strong>
		</td>
	</tr>
	<tr>
	  <td  width="50%" valign="top" height="75" style="line-height:0.9;border:#000 2px solid;font-size:7;" align="left"><strong>CN:{$conNoteNumber}<br>Itm:{$itemno}<br>{$count} of {$total}<br>TO:</strong><BR>{$firstName} {$lastName}, {$company}, {$street}, , {$city}, {$region} {$postcode}</td>
	  <td width="50%" valign="top" align="left" style="line-height:0.9;border-bottom:#000 2px solid; border-right:#000 2px solid;font-size:6;border-top:#000 2px solid"><strong>Service Title here<br>Con Note Wt.: {$boxdata_item['weight']} Kg.<br><br>FROM:</span><BR>{$vivid['company']}, {$vivid['address1']}, {$vivid['city']} {$vivid['state']} {$vivid['zip']}</td></tr>

<tr><td colspan="2"></td></tr>       
 <tr>
	  <td colspan="2"><br><tcpdf method="write1DBarcode" params="{$params}" /></td>
	  </tr>
    </table>
	</td></tr>
	</table>
	</td>
  </tr>
</table>
EOD;


$paidstamp=<<<EOD
<table width="240" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr><td align="left">Company:</td><td align="left" ><strong> {$company}</strong></td></tr>
	<tr><td align="left">Name:</td><td align="left" ><strong> {$firstName} {$lastName}</strong></td></tr>
	<tr><td align="left">Phone:</td><td align="left" ><strong> {$telephone}</strong></td></tr>
	<tr><td align="left"><h1 style="font-size:28">{$invoicecode}</h1></td></tr>
	<tr><td align="left" colspan="2"><h1 style="font-size:28">{$invIncrementID}</h1></td></tr>
	<tr><td align="left"><img width="100" height="100" border="0" src="{$stamp_url}"></td></tr>
	<tr><td align="left"><h1 style="font-size:35">{$pickup}{$urgent}</h1></td></tr></table>
EOD;



//$o_items=<<<EOD
//<table width="240" border="0" align="center" cellpadding="0" cellspacing="0">
//	<tr>
//		<td>
//			<h1>Shipped Items</h1>
//		</td>
//	</tr>
//EOD;

//echo $tbl;exit;
//$pdf->Cell(0, 0, '', '', $tbl, 0, 1, 0, true, '', true);
//$pdf->Cell(10, 10, $tbl, 0, 10 , '', 0, '');
//$pdf->writeHTML($tbl, true, false, false, false, '');
//$pdf->writeHTML($paidstamp, true, false, false, false, '');


	
if($counter==1){
	//$pdf->writeHTML($o_items, true, false, false, false, '');
	$order_items=$order->getAllItems();																	
		foreach($order_items as $orderDetails){
			$prodid=$orderDetails->getProductId();
			$product = Mage::getModel('catalog/product')->load($prodid);															
			$prodname=$product->getName();	
			$quantity = $product->getQty();											
			$thumbnail = $product->getThumbnailUrl();												
			$url = explode("://", $thumbnail);
			$url[0]."</br>"; 						
			$url1 = $url[1];
			//$imageurl = substr($url1,25);																															
			$imageurl = substr($url1,21);																															

$o_items_img.=<<<EOD
	<tr>			
		<td><strong>$quantity</strong>
			<img src="{$thumbnail}" alt="Image not shown" width="75px" height="75px" />{$prodname}			
		</td>		
	</tr>
EOD;
				
	//$pdf->writeHTML($o_items_img, true, false, true, false, '');
	}//end of foreach	
	

}//END of if




//$stamp=<<<EOD
//	<tr>
//		<td>
//			<img src="{$stamp_url}" alt="Stamp not Shown" width="75px" height="75px" />
//		</td>
//	</tr>
//</table>
//
//EOD;
//$pdf->Ln();
			//$pdf->writeHTML($stamp, true, false, true, false, '');
			
			
			
			
			
//			$data = "A".(str_pad($manifat_number,20,"0",STR_PAD_LEFT))."                                          TNT20131127123712           
//
//B0000001297VIVID          21664906  ".$vivid['company']."             ".$vivid['address1']."     ".$vivid['city']."     ".$vivid['state']."     ".$vivid['zip']." DESPATCH            1300 721614  
//
//C0000001297".$conNoteNumber."   001                         ".$firstName."    ".$lastName."     ".$company."      ".$street.'      '.$city."     ".$region."        ".$postcode."    ".$telephone."  0S0000000000                000000.00000000.00                              
//
//F0000001297".$conNoteNumber."   001EVE-89734-3    BOX                       0000100200.000KG000.000000.000000.000CM00411.1250CC0000000000
//
//H0000001297".$conNoteNumber."   ".$itemno."                                                                                                                                           
//
//Z00000000000000001297006";

if($j%7 == 0)
{
	$allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
	$data .="
	H".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($q,3,"0",STR_PAD_LEFT)).$allitem;
	$l++;
	$allitem = '';
	
}
elseif($j == $boxdata_item['box'])
{
	$allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
	$data .="
	H".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad(($q+1),3,"0",STR_PAD_LEFT)).$allitem;
	$l++;
	$allitem = '';
}
else
$allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));


			



			$counter++;
			
					}
					
				}
				
				foreach($_REQUEST['shipment'] as $key=>$val){
					
					foreach($val as $k=>$qty){
						
						$_Product = Mage::getModel('catalog/product')->load($k);
						
						/******************* Start for shipped the item ****************************/
						//Mage::register('isSecureArea', 1);
						$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
						$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
						$tableNameItem = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
						$tableNameTrack = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_track');
						
						$select = $connectionRead->select()
						->from($tableNameItem, array('*'))
						->where('item_id=?',$key);
						
						$row1 =$connectionRead->fetchRow($select);
						
						$_order = Mage::getModel('sales/order')->load($ordId);
						
						try
						{						
						//$connectionWrite->beginTransaction();
						//$data2 = array();
						//$data2['parent_id']=$ordId;
						//if($row1['price'] != '')
						//$data2['price']=$row1['price'];
						//if($row1['weight'] != '')
						//$data2['weight']=$row1['weight'];
						//$data2['qty']=$qty;
						//$data2['product_id']=$k;
						//if($row1['order_item_id'] != '')
						//$data2['order_item_id']=$row1['item_id'];
						//if($row1['name'] != '')
						//$data2['name']=$row1['name'];
						//if($row1['sku'] != '')
						//$data2['sku']=$row1['sku'];
						//
						//$connectionWrite->insert($tableName, $data2);
						//$connectionWrite->commit();
						
						$Insert = "INSERT INTO ".$tableName." SET parent_id = '".$ordId."', price = '".$row1['price']."', weight = '".$row1['weight']."', qty = '".$qty."', product_id = '".$k."', order_item_id = '".$row1['item_id']."', name = '".$row1['name']."', sku = '".$row1['sku']."'; commit;";
						$connectionWrite->query($Insert);
						//$connectionWrite->commit();
						}
						catch(Exception $e)
						{
							//print_r($e);
						    //woah, sorry, I didn't mean to hit you.  Let me do something here instead of 
						    //having the program stop
						}
						
						
						/******************* End for shipped the item ****************************/
						
						
						
						$qty = $qty;
						$name = $_Product->getName();
						$unitPrice = $_Product->getPrice();
						$sku = $_Product->getSku();
						$weight = $_Product->getWeight();										
						$count++;
						
						
					}
			

				
				}
		
					$l++;
$data .="                                                                                                                                       

Z".(str_pad($manifat_number,20,"0",STR_PAD_LEFT)).(str_pad($l,5,"0",STR_PAD_LEFT));
			fwrite($handle, $data);
			
			chmod($file, 0777);
			$fp = fopen($file, 'r');
			
			// set up basic connection
			//$conn_id = ftp_connect('ftp.tnt.com.au') or die("Could not connect");
			//
			//// login with username and password
			//$login_result = ftp_login($conn_id, 'vivid548', 'myV^@d7M0');
			//ftp_pasv($conn_id, true);
			//$ret = ftp_nb_put($conn_id, $remote_file, $file, FTP_ASCII, FTP_AUTORESUME);
			//
			//while ($ret == FTP_MOREDATA) {
			//
			//   // Do whatever you want
			//  // echo ".";
			//
			//   // Continue upload...
			//   $ret = ftp_nb_continue($conn_id);
			//}
			//
			//if ($ret != FTP_FINISHED) {
			//   ///echo "There was an error uploading the file...";
			//  // exit(1);
			//}
			
			fclose($fp);
			// close the connection
			ftp_close($conn_id);
			
			/********************* End upload file to ft server *****************************/
		
		/********************* Start for shipment track **************************/
		$sel_metd2 = explode('__',$sel_metd1);
						
		if(strpos('_'.$sel_metd2[0],'tnt'))
		$title = 'Tnt Australia';
		
		try
		{
			//$connectionWrite->beginTransaction();
			//$data1 = array();
			//$data1['parent_id']=$_REQUEST['shipmentId'];
			////$data1['weight']=$row1['weight'];
			////$data1['qty']=$qty;
			//$data1['order_id']=$ordId;
			//$data1['track_number']= '30015';
			//$data1['title']=$title;
			//$data1['carrier_code']=$sel_metd2[0];
			//$data1['created_at']=NOW();
			//$data1['updated_at']=NOW();
			//
			//$connectionWrite->insert($tableNameTrack, $data1);
			//$connectionWrite->commit();
			
			$Insert = "INSERT INTO ".$tableNameTrack." SET parent_id = '".$_REQUEST['shipmentId']."',  weight = '".$row1['weight']."', qty = '".$qty."', order_id = '".$ordId."', track_number = '30015', title = '".$title."', carrier_code = '".$sel_metd2[0]."', created_at= NOW(), updated_at = NOW(); commit; ";
			$connectionWrite->query($Insert);
			//$connectionWrite->commit();
			//exit;
		}
		catch(Exception $e)
		{
			print_r($e);///exit;
		    //woah, sorry, I didn't mean to hit you.  Let me do something here instead of 
		    //having the program stop
		}
		/********************* Start for shipment track **************************/
		
	$chunks=array_chunk($tbl,2);
        $pdf->SetMargins(12,7,0,0);
	$pdf->SetPageOrientation("P",true,0);
	$size="A4";




foreach($chunks as $page)
{
	
	$pdf->AddPage("P",$size);
//$firstpage="";
if($i == 0)
$paidstamp1="<h1>Ordered Items</h1>".$o_items_img;
else 
$paidstamp1=$paidstamp;

$firstpage=<<<EOD
<tr><td height="30"></td><td></td><td></td></tr>
<tr><td height="30"></td><td></td><td></td></tr>
<tr><td valign="middle">
{$paidstamp1}</td><td></td><td>{$paidstamp}</td>
</tr>
EOD;

	$outtable=<<<EOD
<table width="600" cellpadding="0" cellspacing="0" border="0">
<tr><td>$page[0]</td><td width="120"> </td><td>$page[1]</td></tr>
{$firstpage}
</table>
EOD;

	$pdf->writeHTML($outtable, true, false, false, false, '');
$i++;

//if($page[1]=="") 
//	$extra=false;
//else
//	 $extra=true;
}
			$filename=$shipmenIncrmnttId.".pdf";
			$path = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$shipmenIncrmnttId.".pdf";
			$pdf->Output($path,'F');
			
			
			
			
			//$pdf->Output('example_001.pdf', 'I');		
				
				
				
				
				//foreach($items as $item){
				
				//}//end of foreach
				//echo "<pre>";print_r($commentModel);
				//exit;
				////27-12-2013 for pdf E
				
				
				
				
				//$connectionWrite->beginTransaction();
				//$data3=array();
				//if($order->getStoreId()) $data3['store_id']= $order->getStoreId();
				//if($order->getweight()) $data3['total_weight']= $order->getweight();
				//if($qtyShipped) $data3['total_qty']= $qtyShipped;
				//if($orderIdP) $data3['order_id']= $orderIdP;
				//if($customerid) $data3['customer_id']= $customerid;
				//if($sel_adr) $data3['shipping_address_id']= $sel_adr;
				//if($order->getBillingAddressId()) $data3['billing_address_id']= $order->getBillingAddressId();
				//
				//$ship=explode('__',$sel_metd);
				//if($ship[0]) $data3['shippingmethod']= $ship[0];
				//if($ship[1]) $data3['shippingrate']= $ship[1];
				//
				//if($shipmenIncrmnttId) $data3['increment_id']= $shipmenIncrmnttId;
				//if($created_at) $data3['created_at']= $created_at;
				//if($created_at) $data3['updated_at']= $created_at;
				//
				//if(($f-1) == 0)
				//$data3['et_file']= "ET-".$date.'.txt';
				//else
				//$data3['et_file']= "ET-".$date.$f.'.txt';
				//
				//$connectionWrite->insert($tableName4, $data3);
				//$connectionWrite->commit();
				//
				//$conNoteNumber="VVD000055714";
				
				
				///////////////// 26-11-2013 SOC
				//$msg='Shipment Created Successfully.';
				//$this->_getSession()->addSuccess($msg);
				//$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
			}/////15-1-2014
			
			//}else{
			//	$msg='Please select a shipping address ,shipping method and put the confirm box.';
			//	$this->_getSession()->addError($msg);
			//	$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
			//	
			//}
			$msg='Shipment Created Successfully.';
			$this->_getSession()->addSuccess($msg);
			$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));	
		}/*else {
			
			$msg='Shipped quantity is greater than total quantity ordered';
			$this->_getSession()->addError($msg);
			$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
		}*/
		
	}
	//16-11-2013 EOC
	
	
	/********************************* Start For download pdf *****************************************************/
	public function downloadAction()
	{
	    $file_path=Mage::getBaseDir('media').'/shiplabel/'.$this->getRequest()->getParam('file');
	
	
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
	/********************************* End For download pdf *****************************************************/
	///29-11-2013
	public function deletepartshipAction(){
		
		$partShipID=$this->getRequest()->getParam('shipId'); //exit;
		
		$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment');
		$tableName1 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
		$tableName2 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
		$tableName3 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
			
		$select = $connectionRead->select()->from($tableName1, array('*'))->where('entity_id=?',$partShipID);
		$row = $connectionRead->fetchRow($select);
		
		$pos1 = strpos($row['increment_id'], '-');	
		if($pos1 != false) {
			$sincr=explode('-',$row['increment_id']);
			$a=$sincr[0];
			
			/////6-12-2013 SOC
			$b=$sincr[1];
			if($b==1){
				
				$orderId=$row['order_id'];
				$_shipmenIncrtId=$row['increment_id'];
				$_order = Mage::getModel('sales/order')->load($orderId);
				$ordShipAmt=$_order->getShippingAmount();
				
				$sel = $connectionRead->select()->from($tableName, array('*'))->where('order_id=?',$orderId)->order('entity_id ASc')->limit(1);
				$ro = $connectionRead->fetchRow($sel);
				$shiprate=$ro['shippingrate'];
				
				/********************* Start Delete ET File ******************************/
				$file = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$ro['et_file'];
				chmod($file,0777);
				unlink($file);
				
				/********************* End Delete ET File ******************************/
				
				$select2 = $connectionRead->select()->from($tableName3, array('*'))->where('entity_id=?',$orderId);
				$row2 = $connectionRead->fetchRow($select2);
				$baseTotalDue=$row2['base_total_due'];
				$TotalDue=$row2['total_due'];
				$baseGrandTotal=$row2['base_grand_total'];
				$GrandTotal=$row2['grand_total'];
				
				
				if( $ordShipAmt > $shiprate ){
					
				    $diff=$ordShipAmt-$shiprate;
				    $baseTotalDueN=$baseTotalDue + $diff;
				    $TotalDueN=$TotalDue + $diff;
				    $baseGrandTotalN=$baseGrandTotal + $diff;
				    $GrandTotalN=$GrandTotal + $diff;
				    
				}elseif($shiprate > $ordShipAmt){
					
				    $diff=$shiprate - $ordShipAmt;
				    $baseTotalDueN=$baseTotalDue - $diff;
				    $TotalDueN=$TotalDue - $diff;
				    $baseGrandTotalN=$baseGrandTotal - $diff;
				    $GrandTotalN=$GrandTotal - $diff;
				    
				}else{
				    
				}
				
				if($baseTotalDueN != $baseTotalDue){
			    
					$connectionWrite->beginTransaction();
					$data = array();
					if($baseTotalDueN) $data['base_total_due'] = $baseTotalDueN;
					if($TotalDueN) $data['total_due'] = $TotalDueN;
					if($baseGrandTotalN) $data['base_grand_total'] = $baseGrandTotalN;
					if($GrandTotalN) $data['grand_total'] = $GrandTotalN;
					
					$where = $connectionWrite->quoteInto('entity_id =?', $orderId);
					$connectionWrite->update($tableName3, $data, $where);
					$connectionWrite->commit();
				
					//echo "update not same: ".$diff;
					//exit;
				}
			
				
				
				
			}
			/////6-12-2013 EOC
			
			$select1 = $connectionRead->select()->from($tableName1, array('*'))->where('increment_id=?',$a);
			$row1 = $connectionRead->fetchRow($select1);
			$parentId=$row1['entity_id']; 
			
			
			///delete from grid
			$connectionWrite->beginTransaction();
			$condition = array($connectionWrite->quoteInto('entity_id=?', $partShipID));
			$connectionWrite->delete($tableName1, $condition);
			$connectionWrite->commit();
			
			///delete from item
			$connectionWrite->beginTransaction();
			$condition1 = array($connectionWrite->quoteInto('parent_id=?', $parentId));
			$connectionWrite->delete($tableName2, $condition1);
			$connectionWrite->commit();
			
			
			///delete from shipment
			$connectionWrite->beginTransaction();
			$condition2 = array($connectionWrite->quoteInto('increment_id=?', $row['increment_id']));
			$connectionWrite->delete($tableName, $condition2); 
			$connectionWrite->commit();
			
			$msg='Shipment deleted successfully';
			$this->_getSession()->addSuccess($msg);
			$this->_redirect('adminhtml/sales_shipment/index');
			
		}
		else {
			$msg='Can not delete a parent shipment';
			$this->_getSession()->addError($msg);
			$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $partShipID));
		}
		
	}
	
	
	
	
	////////////////////////////////
	
	
	
	
	
	
	
	//// 2-12-2013 modified on 12-12-2013 SOC
	public function getauspostmethodAction(){
		extract($_REQUEST);
		$order = Mage::getModel('sales/order')->load($ordId);
		///21-1-2013  S 
		$pos1 = stripos($addrsid, '_S');
		if($pos1 != false){
			$adr= explode("_",$addrsid);
			$addrsid = $adr[0];
			$_shippingAddress = $order->getShippingAddress();
			$destCountry = $_shippingAddress->getCountry_id();
			$toPostCode = $_shippingAddress->getPostcode();
		}else{
			
			$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
			$destCountry=$addressLoadId['country_id'];
			$toPostCode=$addressLoadId['postcode'];
		}
		///21-1-2013 E
		
		//$toPostCode='3207';
		
		$tWeight=$order->getWeight();
		$fromPostCode = '3207';
		$result = Mage::getModel('shipping/rate_result');
		$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId());
		//$aus=Mage::getModel('shipping/carrier/australiapost')->_drcRequest();
		$ret=array();
		$res='';
		//$weight = 10;
		
		
		//12-12-2013 S
		//$weight = $tWeight;
		//$height = $width = $length = 100;
		//$shipping_num_boxes = 1;
		$arrc=explode("__",$unit);
		
		//print_r($arrc);
		$boxArr=explode(":",$arrc[0]);
		$qtyArr=explode(":",$arrc[1]);
		$lengArr=explode(":",$arrc[2]);
		$hghtArr=explode(":",$arrc[3]);
		$widtArr=explode(":",$arrc[4]);
		$totwghtArr=explode(":",$arrc[6]);
		
		$weight = $totwghtArr[1];
		$height = $hghtArr[1];
		$width = $widtArr[1];
		$length = $lengArr[1];
		$shipping_num_boxes = $boxArr[1];
		
		//echo  $shipping_num_boxes;
		//exit;
		
		//12-12-2013 E
		
		
		$allowedShippingMethods = explode(',', Mage::getStoreConfig('carriers/australiapost/shipping_methods'));
		//echo $destCountry;
		if($weight < 20 ){
		if ($destCountry == "AU") {  ///domestic
			//echo "domestic";
			$shipping_methods = array('STANDARD', 'EXPRESS');
			
			
			foreach ($shipping_methods as $shipping_method) {
				
				//$drc = $aus->_drcRequest($shipping_method, $frompcode, $topcode, $destCountry, $sweight, $slength, $swidth, $sheight, $shipping_num_boxes);
				//if (in_array($shipping_method, $allowedShippingMethods)) {
					
					$url = "http://drc.edeliver.com.au/ratecalc.asp?" . 
					"Pickup_Postcode=" . rawurlencode($fromPostCode) .
					"&Destination_Postcode=" . rawurlencode($toPostCode) .
					"&Country=" . rawurlencode($destCountry) .
					"&Weight=" . rawurlencode($weight) .
					"&Service_Type=" . rawurlencode($shipping_method) . 
					"&Height=" . rawurlencode($height) . 
					"&Width=" . rawurlencode($width) . 
					"&Length=" . rawurlencode($length) .
					"&Quantity=" . rawurlencode($shipping_num_boxes);
					
					$curl = new Varien_Http_Adapter_Curl();
					$curl->setConfig(array(
						'timeout'   => 15    //Timeout in no of seconds
					));
					$curl->write(Zend_Http_Client::GET, $url);
					$curlData = $curl->read();
					$drc_result = Zend_Http_Response::extractBody($curlData);
					$curl->close();
					
					$drc_result = explode("\n",$drc_result);
					//clean up array
					$drc_result = array_map('trim', $drc_result);
					$drc_result = array_filter($drc_result); 
					
					$result = array();
					foreach($drc_result as $vals)
					{
						$tokens = explode("=", $vals);
						if(isset($tokens[1])) {
							$result[$tokens[0]] = trim($tokens[1]);
							//$strVal="Australiapost__".$shipping_method."__".$result['charge']."__".$result['days'];
							$strVal="Australiapost ".$shipping_method." ".$result['days']."__".$result['charge'];
							$strLabel=$shipping_method." ".$result['days']." Days $".$result['charge'];
						} else {
							return array('err_msg' => 'Parsing error on Australia Post results');
						}
					}
					array_push($ret,$result);
					$res.='<input name="shipping_method_aus" type="radio" value="'.$strVal.'" id="" class="radio" onclick="slmthd(this.value);"/>  '.$strLabel.'</br>';
				//}
				
			}
			
		}else{  ////international
			$shipping_methods = array('SEA', 'AIR' , 'EPI');
			foreach ($shipping_methods as $shipping_method) {
				$url = "http://drc.edeliver.com.au/ratecalc.asp?" . 
				"Pickup_Postcode=" . rawurlencode($fromPostCode) .
				"&Destination_Postcode=" . rawurlencode($toPostCode) .
				"&Country=" . rawurlencode($destCountry) .
				"&Weight=" . rawurlencode($weight) .
				"&Service_Type=" . rawurlencode($shipping_method) . 
				"&Height=" . rawurlencode($height) . 
				"&Width=" . rawurlencode($width) . 
				"&Length=" . rawurlencode($length) .
				"&Quantity=" . rawurlencode($shipping_num_boxes);
				
				$curl = new Varien_Http_Adapter_Curl();
				$curl->setConfig(array(
					'timeout'   => 15    //Timeout in no of seconds
				));
				$curl->write(Zend_Http_Client::GET, $url);
				$curlData = $curl->read();
				$drc_result = Zend_Http_Response::extractBody($curlData);
				$curl->close();
				
				$drc_result = explode("\n",$drc_result);
				//clean up array
				$drc_result = array_map('trim', $drc_result);
				$drc_result = array_filter($drc_result); 
				
				$result = array();
				foreach($drc_result as $vals)
				{
					$tokens = explode("=", $vals);
					if(isset($tokens[1])) {
						$result[$tokens[0]] = trim($tokens[1]);
						//$strVal="Australiapost__".$shipping_method."__".$result['charge']."__".$result['days'];
						$strVal="Australiapost ".$shipping_method." ".$result['days']."__".$result['charge'];
						$strLabel="Australiapost ".$shipping_method." $".$result['charge'];
					} else {
						return array('err_msg' => 'Parsing error on Australia Post results');
					}
				}
				array_push($ret,$result);
				$res.='<input name="shipping_method_aus" type="radio" value="'.$strVal.'" id="" class="radio" onclick="slmthd(this.value);"/>  '.$strLabel.'</br>';
			
			}
			
		}
		
		}else{
			
			echo "Weight exceeds maximum allowed weight (20kgs)";
		}
		
		//print_r($result);
		echo $res;
		//print_r($drc_result);
		
	}
	////2-12-2013 EOC
	
	
	
	////6-12-2013 SOC
	public function warehouseformAction(){
		extract($_REQUEST);
		$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		//if(isset($isready) && $isready=='Yes'){
		//	$connectionWrite->beginTransaction();
		//	$data = array();
		//	$data['status'] = 'Ready to ship';
		//	
		//	$where = $connectionWrite->quoteInto('entity_id =?', $shipmentId);
		//	$connectionWrite->update($tableName, $data, $where);
		//	$connectionWrite->commit();	
		//	
		//	$msg='Shipment Status Updated Successfully';
		//	$this->_getSession()->addSuccess($msg);
		//	$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
		//}
		/////// 13-12-13 S
		if(isset($isready)){
			$connectionWrite->beginTransaction();
			$data = array();
			if($isready == 'oktoship') $data['status'] = 'OK For Shipping';
			else if ($isready == 'readytodespatch') $data['status'] = 'Ready To Disptch';
			else if ($isready == 'picked') $data['status'] = 'Picked Up';  ///17-12-2013
			
			$where = $connectionWrite->quoteInto('entity_id =?', $shipmentId);
			$connectionWrite->update($tableName, $data, $where);
			$connectionWrite->commit();	
			
			$msg='Shipment Status Updated Successfully';
			$this->_getSession()->addSuccess($msg);
			$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
		}
		/////// 13-12-13 E
	}
	
	////6-12-2013 EOC
	
	////17-1-2014 20-1-2014 SOC
	public function getconfirmboxAction(){
		extract($_REQUEST);
		$order = Mage::getModel('sales/order')->load($oId);
		$items = $order->getAllItems();
		$qtny = count($items);
		$totweight=0;
		foreach($items as $item){
			$_product = Mage::getModel('catalog/product')->load($item->getProductId());
			$weight = $_product->getWeight();
			$totweight +=$weight;
			//echo "id : ".$_product->getTypeId();echo "<br>";
		}
		
		////////////////////////////////////////
		$i=1;
		$j=1;
		$p=1;
		$k=1;
		$urlact = Mage::helper("adminhtml")->getUrl("partialshipping/adminhtml_partialshipping/confiboxform");
		$str='<form id="cnfrm" name="cnfrm" action="'.$urlact.'" method="post">
			<input name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'">
			<table width="750px" id="tabl_'.$j.'">
			<tr>
			<td>Total Weight : <input type="text" name="confirmtotalweight" id="confirmtotalweight" value="'.$totweight.'"/></td>
			<td>Total Volum : <input type="text" name="confirmtotalvolum" id="confirmtotalvolum" value="'.$qtny.'"/></td>
			<input type="hidden" name="confirmtotqty" id="confirmtotqty" value="'.count($items).'"/>
			</tr>
			<tr>
			<td colspan="2"> 
				<table id="tabl_row_'.$k.'">
					<tr class="headings">
					<th>Boxes</th><th>Qty</th><th>Lenght</th><th>Width</th><th>Height</th><th>weight</th><th>Title</th><th>Action</th>
					</tr>';
					$bLenght=0;
					$bWidth=0;
					$bHeight=0;
					$bWeight=0;
					foreach($items as $item){
						
					$str.='<tr id="row_'.$i.'">';
					$_product = Mage::getModel('catalog/product')->load($item->getProductId());
					
					//start 23_01_2014
					
					$tableNameItem = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
					$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
					$select2 = $connectionRead->select()->from($tableNameItem, array('SUM(qty) AS SHIP_COUNT'))->where('order_item_id=? AND product_id="'.$item->getProductId().'"',$item->getId());
					$row2 = $connectionRead->fetchRow($select2);
					
					
					if($row2['SHIP_COUNT'] < round($item->getQtyOrdered()))
					{
					//end 23_01_2014
						
						if($_product->getTypeId() == 'bundle'){
							//echo "PID : ".$_product->getSku();
							$bundled_product = new Mage_Catalog_Model_Product();
							$bundled_product->load($_product->getId());
							$selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
							    $bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
							);
							$bundled_items = array();
							foreach($selectionCollection as $option)
							{
							    //$bundled_items[] =  $option->getWidth();
							    $bLenght = $bLenght + $option->getLength();
							    $bWidth = $bWidth + $option->getWidth();
							    $bHeight = $bHeight + $option->getHeight();
							    $bWeight = $bWeight + $option->getWeight();
							    
							}
							//echo "<pre>";print_r($bundled_items);
							//echo "H : ".$bWidth; 
							$str.='
							<td><input type="hidden" name="confirmproduct[]" value="'.$_product->getId().'" /><input type="text" name="confirmbox[]" id="confirmbox" value="1" style="width : 80px"/></td>
							<td><input type="text" name="confirmqty[]" id="confirmqty" value="'.round($item->getQtyOrdered()).'" style="width : 80px"/></td>
							<td><input type="text" name="confirmlength[]" id="confirmlength" value="'.$bLenght.'" style="width : 80px"/></td>
							<td><input type="text" name="confirmwidth[]" id="confirmwidth" value="'.$bWidth.'" style="width : 80px"/></td>
							<td><input type="text" name="confirmheight[]" id="confirmheight" value="'.$bHeight.'" style="width : 80px"/></td>
							<td><input type="text" name="confirmweight[]" id="confirmheight" value="'.number_format($bWeight,2).'" style="width : 80px"/></td>
							<td><input type="text" name="confirmtitle[]" id="confirmtitle"  value="Vivid '.$p.'" style="width : 80px"/> </td>
							<td><span style="cursor:pointer;float: right;" title="Add More" class="addanother" onclick="remove_r('.$i.');">Remove</span></td>';
						}
						//exit;
						if($_product->getTypeId() == 'simple'){
						$str.='
						<td><input type="hidden" name="confirmproduct[]" value="'.$_product->getId().'" /><input type="text" name="confirmbox[]" id="confirmbox" value="1" style="width : 80px"/></td>
						<td><input type="text" name="confirmqty[]" id="confirmqty" value="'.(round($item->getQtyOrdered())-$row2['SHIP_COUNT']).'" style="width : 80px"/></td>
						<td><input type="text" name="confirmlength[]" id="confirmlength" value="'.$_product->getLength().'" style="width : 80px"/></td>
						<td><input type="text" name="confirmwidth[]" id="confirmwidth" value="'.$_product->getWidth().'" style="width : 80px"/></td>
						<td><input type="text" name="confirmheight[]" id="confirmheight" value="'.$_product->getHeight().'" style="width : 80px"/></td>
						<td><input type="text" name="confirmweight[]" id="confirmheight" value="'.number_format($_product->getWeight(),2).'" style="width : 80px"/></td>
						<td><input type="text" name="confirmtitle[]" id="confirmtitle"  value="Vivid '.$p.'" style="width : 80px"/> </td>
						<td><span style="cursor:pointer;float: right;" title="Add More" class="addanother" onclick="remove_r('.$i.');">Remove</span></td>';
						}
						$str.='</tr>';
						$i++;
						$p++;
						$j++;
						}
					}
				$str.='</table>
			</td>	
			</tr>
			<tr>
			<td colspan="2" style="text-align: right">
			</td></tr>
			<tr>
			<td colspan="2" style="text-align: left">
			<span title="Add More" style="cursor:pointer;" class="addanother" onclick="selMethod();">Save</span>
			<span title="Add More" style="cursor:pointer;" class="addanother" onclick="urlMaker()">Print</span>
			<span style="display : none;" id="spnlblnumber"> Label Number : <input type="text" name="labelnumber" id="labelnumber" value="'.$qtny.'" style="width : 80px"/></span>
			</td></tr>
			</table></form>';
		echo $str;
		
		
	}
	////17-1-2014 EOC
	///12-12-2013 SOC
	public function confiboxformAction(){
		extract($_POST);
		$str='';
		$str1='';
		$box=0;
		$qty=0;
		$lengh=0;
		$hght=0;
		$width=0;
		$weight=0;
		$title='';
		$errN=0;
		$errC=0;
		foreach($confirmbox as $k=>$v){
			if(is_numeric($v)) $box += $v; else { $errN++;}
			if(is_numeric($confirmqty[$k])) $qty += $confirmqty[$k]; else { $errN++;}
			if(is_numeric($confirmlength[$k])) $lengh += $confirmlength[$k]; else { $errN++;}
			if(is_numeric($confirmheight[$k])) $hght += $confirmheight[$k]; else { $errN++;}
			if(is_numeric($confirmwidth[$k])) $width += $confirmwidth[$k]; else { $errN++;}
			if(is_numeric($confirmweight[$k])) $weight += $confirmweight[$k]; else { $errN++;}	
			if(!is_numeric($confirmtitle[$k])) $title .= "*".$confirmtitle[$k]; else { $errC++;}
			
			$str1.="@@@@box:".$v."__qty:".$confirmqty[$k]."__len:".$confirmlength[$k]."__hght:".$confirmheight[$k]."__width:".$confirmwidth[$k]."__weight:".$confirmweight[$k]."__title:".$confirmtitle[$k];
			
			
			if($confirmproduct[$k])
			{
				
				$_product = Mage::registry('catalog/product')->load($confirmproduct[$k]);
				$this->model = Mage::registry('catalog/product')->load($confirmproduct[$k]);
				
				if($_product->getLength() != '')
				{
					$this->model->setLength($confirmlength[$k]);
				}
				
				if($_product->getWidth() != '')
				{
					$this->model->setWidth($confirmwidth[$k]);
				}
				
				if($_product->getHeight() != '')
				{
					$this->model->setHeight($confirmheight[$k]);
				}
				
				if($_product->getWeight() != '')
				{
					$this->model->setWeight($confirmweight[$k]);
				}
				
				try {
					Mage::app('default');
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$this->model->save();
				    }
				    catch (Exception $ex) {
					echo $ex->getMessage();
				    }
				
			}
			
		}
		if(is_numeric($confirmtotalweight)) $confirmtotalweight=$confirmtotalweight; else { $errN++;}
		if(is_numeric($confirmtotalvolum)) $confirmtotalvolum=$confirmtotalvolum; else { $errN++;}
		
		$str="box:".$box."__qty:".$qty."__len:".$lengh."__hght:".$hght."__width:".$width."__title:".$title."__totwght:".$confirmtotalweight."__totvol:".$confirmtotalvolum;
		
		if( $confirmtotalvolum > $confirmtotqty){
			
			echo "qtyerror";
		}else{
			if($errN > 0){
			
			echo "numerror";
			
			}elseif( $errC > 0){
				
				echo "charerror";
			}else{
				echo $str.'####'.$str1;
			}
		}
		
		
		
	}
	
	public function getauspostmethodolAction(){
		
		extract($_REQUEST);
		$order = Mage::getModel('sales/order')->load($ordId);
		///21-1-2013  S 
		$pos1 = stripos($addrsid, '_S');
		if($pos1 != false){
			$adr= explode("_",$addrsid);
			$addrsid = $adr[0];
			$_shippingAddress = $order->getShippingAddress();
			$destCountry = $_shippingAddress->getCountry_id();
			$toPostCode = $_shippingAddress->getPostcode();
		}else{
			
			$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
			$destCountry=$addressLoadId['country_id'];
			$toPostCode=$addressLoadId['postcode'];
		}
		///21-1-2013 E
		
		//$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
		//$destCountry=$addressLoadId['country_id'];
		//$toPostCode=$addressLoadId['postcode'];
		//$toPostCode='3207';
		
		$tWeight=$order->getWeight();
		$fromPostCode = '3207';
		$result = Mage::getModel('shipping/rate_result');
		$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId());
		//$aus=Mage::getModel('shipping/carrier/australiapost')->_drcRequest();
		$ret=array();
		$res='';
		//$weight = 10;
		$weight = $tWeight;
		$height = $width = $length = 100;
		$shipping_num_boxes = 1;
		$allowedShippingMethods = explode(',', Mage::getStoreConfig('carriers/australiapost/shipping_methods'));
		//echo $destCountry;
		if($weight <= 20 ){
		if ($destCountry == "AU") {
			$shipping_methods = array('STANDARD', 'EXPRESS');
			
			
			foreach ($shipping_methods as $shipping_method) {
				
				//$drc = $aus->_drcRequest($shipping_method, $frompcode, $topcode, $destCountry, $sweight, $slength, $swidth, $sheight, $shipping_num_boxes);
				//if (in_array($shipping_method, $allowedShippingMethods)) {
					
					$url = "http://drc.edeliver.com.au/ratecalc.asp?" . 
					"Pickup_Postcode=" . rawurlencode($fromPostCode) .
					"&Destination_Postcode=" . rawurlencode($toPostCode) .
					"&Country=" . rawurlencode($destCountry) .
					"&Weight=" . rawurlencode($weight) .
					"&Service_Type=" . rawurlencode($shipping_method) . 
					"&Height=" . rawurlencode($height) . 
					"&Width=" . rawurlencode($width) . 
					"&Length=" . rawurlencode($length) .
					"&Quantity=" . rawurlencode($shipping_num_boxes);
					
					$curl = new Varien_Http_Adapter_Curl();
					$curl->setConfig(array(
						'timeout'   => 15    //Timeout in no of seconds
					));
					$curl->write(Zend_Http_Client::GET, $url);
					$curlData = $curl->read();
					$drc_result = Zend_Http_Response::extractBody($curlData);
					$curl->close();
					
					$drc_result = explode("\n",$drc_result);
					//clean up array
					$drc_result = array_map('trim', $drc_result);
					$drc_result = array_filter($drc_result); 
					
					$result = array();
					foreach($drc_result as $vals)
					{
						$tokens = explode("=", $vals);
						if(isset($tokens[1])) {
							$result[$tokens[0]] = trim($tokens[1]);
							$strVal="Australiapost ".$shipping_method." ".$result['days']."__".$result['charge'];
							$strLabel=$shipping_method." ".$result['days']." Days $".$result['charge'];
						} else {
							return array('err_msg' => 'Parsing error on Australia Post results');
						}
					}
					array_push($ret,$result);
					$res.='<input name="shipping_method_aus" type="radio" value="'.$strVal.'" id="" class="radio" onclick="slmthd(this.value);"/>  '.$strLabel.'</br>';
				//}
				
			}
			
		}else{
			$shipping_methods = array('SEA', 'AIR' , 'EPI');
			foreach ($shipping_methods as $shipping_method) {
				$url = "http://drc.edeliver.com.au/ratecalc.asp?" . 
				"Pickup_Postcode=" . rawurlencode($fromPostCode) .
				"&Destination_Postcode=" . rawurlencode($toPostCode) .
				"&Country=" . rawurlencode($destCountry) .
				"&Weight=" . rawurlencode($weight) .
				"&Service_Type=" . rawurlencode($shipping_method) . 
				"&Height=" . rawurlencode($height) . 
				"&Width=" . rawurlencode($width) . 
				"&Length=" . rawurlencode($length) .
				"&Quantity=" . rawurlencode($shipping_num_boxes);
				
				$curl = new Varien_Http_Adapter_Curl();
				$curl->setConfig(array(
					'timeout'   => 15    //Timeout in no of seconds
				));
				$curl->write(Zend_Http_Client::GET, $url);
				$curlData = $curl->read();
				$drc_result = Zend_Http_Response::extractBody($curlData);
				$curl->close();
				
				$drc_result = explode("\n",$drc_result);
				//clean up array
				$drc_result = array_map('trim', $drc_result);
				$drc_result = array_filter($drc_result); 
				
				$result = array();
				foreach($drc_result as $vals)
				{
					$tokens = explode("=", $vals);
					if(isset($tokens[1])) {
						$result[$tokens[0]] = trim($tokens[1]);
						$strVal="Australiapost ".$shipping_method." ".$result['days']."__".$result['charge'];
						$strLabel="Australiapost ".$shipping_method." $".$result['charge'];
					} else {
						return array('err_msg' => 'Parsing error on Australia Post results');
					}
				}
				array_push($ret,$result);
				$res.='<input name="shipping_method_aus" type="radio" value="'.$strVal.'" id="" class="radio" onclick="slmthd(this.value);"/>  '.$strLabel.'</br>';
			
			}
			
		}
		
		}else{
			
			echo "Weight exceeds maximum allowed weight (20kgs)";
		}
		
		//print_r($result);
		echo $res;
		
	}
	///12-12-2013 EOC
	
	////24-12-2013 SOC
	public function XML2Array(SimpleXMLElement $parent)
	{
	    $array = array();
	
	    foreach ($parent as $name => $element) {
		($node = & $array[$name])
		    && (1 === count($node) ? $node = array($node) : 1)
		    && $node = & $node[];
	
		$node = $element->count() ? $this->XML2Array($element) : trim($element);
	    }
	
	    return $array;
	}
	
	
	public function gettntlocalAction(){
	
	extract($_REQUEST);
	$order = Mage::getModel('sales/order')->load($ordId);
	
	///21-1-2013  S 
	$pos1 = stripos($addrsid, '_S');
	if($pos1 != false){
		$adr= explode("_",$addrsid);
		$addrsid = $adr[0];
		$_shippingAddress = $order->getShippingAddress();
		$destCountry = $_shippingAddress->getCountry_id();
		$postCode = $_shippingAddress->getPostcode();
		$city = $_shippingAddress->getCity();
		$region = $_shippingAddress->getRegion();
	}else{
		
		$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
		$destCountry=$addressLoadId['country_id'];
		$postCode=$addressLoadId['postcode'];
		$city = $addressLoadId['city'];
		$region = $addressLoadId['region'];
	}
	///21-1-2013 E
	
	
	
	
	//$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
	//$destCountry=$addressLoadId['country_id']; //exit;
	//$postCode=$addressLoadId['postcode'];
	//$city = $addressLoadId['city'];
	//$region = $addressLoadId['region'];
	
	//echo "dfdsfds : ".$addressLoadId; exit;
	
	
	$tWeight=$order->getWeight();
	$fromPostCode = '3207';
	$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId());
	//$aus=Mage::getModel('shipping/carrier/australiapost')->_drcRequest();
	$ret=array();
	$res='';
	//$weight = 10;
	$weight = $tWeight;
	$height = $width = $length = 10;
	$shipping_num_boxes = 1;
	$allItems = $order->getAllItems();
	//$date = date('Y-m-d');
	$date = '2014-10-10';
	
	if ($destCountry == "AU") {
		$requestXml='<?xml version="1.0"?>
			<enquiry xmlns="http://www.tntexpress.com.au">
			  <ratedTransitTimeEnquiry>
				<cutOffTimeEnquiry>
				  <collectionAddress>
					<suburb>Port Melbourne</suburb>
					<postCode>'.trim($fromPostCode).'</postCode>
					<state>VIC</state>
				  </collectionAddress>
				  <deliveryAddress>
					<suburb>'.trim($city).'</suburb>
					<postCode>'.trim($postCode).'</postCode>
					<state>VIC</state>
				  </deliveryAddress>
				  <shippingDate>'.trim($date).'</shippingDate>
				  <userCurrentLocalDateTime>
					2007-11-05T10:00:00
				  </userCurrentLocalDateTime>
				  <dangerousGoods>
					<dangerous>false</dangerous>
				  </dangerousGoods>
				  <packageLines packageType="D">';
			foreach($allItems as $item){
				$itemId = $item->getId();									
				$quoteId = $item->getQuoteId();				
				$productId = $item->getProductId();				
				$quoteModel = Mage::getModel('sales/quote')->load($quoteId);				
				$addresses = $quoteModel->getAllShippingAddresses();
				$product = Mage::getModel('catalog/product')->load($productId);				
				if($item->getQty()) $qty = $item->getQty(); else $qty = 1;
				if($product->getWeight()) $weight = round($product->getWeight()); else $weight = 10;
				if($product->getLength()) $length = round($product->getLength()); else $length = 10;
				if($product->getWidth())  $width = round($product->getWidth()); else $width = 10;
				if($product->getHeight()) $height = round($product->getHeight()); else $height = 10;
				
				//$qty = $item->getQty();		
				//$weight = $product->getWeight();
				//$length = $product->getLength();
				//$width = $product->getWidth();
				//$height = $product->getHeight();
				
				//$qty = 3;		
				//$weight = 10;
				//$length = 12;
				//$width = 13;
				//$height = 14;
				
				  $requestXml.=
					'
					<packageLine>
					  <numberOfPackages>'.intval($qty).'</numberOfPackages>
					  <dimensions unit="cm">
						<length>'.trim($length).'</length>
						<width>'.trim($width).'</width>
						<height>'.trim($height).'</height>
					  </dimensions>
					  <weight unit="kg">
						<weight>'.trim($weight).'</weight>
					  </weight>
					</packageLine>';
			}
			//echo $qty; exit;
			$requestXml.='
			</packageLines>
				</cutOffTimeEnquiry>
				<termsOfPayment>
				  <senderAccount>21664906</senderAccount>
				  <payer>S</payer>
				</termsOfPayment>
			  </ratedTransitTimeEnquiry>
			</enquiry>';
			//Mage::getSingleton('core/session')->addSuccess($requestXml);
			$xml['Username']='CIT00000000000035655';
			$xml['Password']='toyota11';
 			$xml['XMLRequest']=$requestXml;
			//$response=$this->requestTntResponse($xml);
			
			
			//echo $requestXml;
			$iClient = new Varien_Http_Client();
			
			
			$iClient->setUri('https://www.tntexpress.com.au/Rtt/inputRequest.asp')
				  ->setMethod('POST')
				  ->setConfig(array(
					    'maxredirects'=>0,
					    'timeout'=>30,
				  ));
			$iClient->setParameterPost($xml);    
			$response = $iClient->request();
			$results = $response->getBody();
			
			//$results = $response->getBrokenrules();
			$xml1=simplexml_load_string($results);
			
			$array = $this->XML2Array($xml1);
			$res='';
			//echo "<pre>";print_r($array);
			/*if($array['ratedTransitTimeResponse']['brokenRules'] && !$array['ratedTransitTimeResponse']['ratedProducts']){
				
				$res= $array['ratedTransitTimeResponse']['brokenRules']['brokenRule']['description'];
				
			}*/if($array['error']){
				$res= $array['error']['description'];
				$resarr=explode("error: decimal:",$res);
				echo $resarr[1];
			}else{
				
				$ratedArray = $array['ratedTransitTimeResponse']['ratedProducts']['ratedProduct'];
			
				foreach($ratedArray as $key=>$ratedProduct){
					if(is_numeric($key)){
					$mt = $ratedProduct['product']['description'];
					
					if($ratedProduct['quote']['price']){
						$rate="$".$ratedProduct['quote']['price'];
					}else{
						$rate=0;
					}
					
					
					$val = "TNT ".$mt."__".$ratedProduct['quote']['price'];
					$mt1=$mt." ".$rate;
					$res .= '<input name="shipping_method_tntl" type="radio" value="'.$val.'" id="" class="radio" onclick="slmthd(this.value);"/>  '.$mt1.'</br>';
					}
				}
			}
			
			echo $res;
			
			//$ratedArray = $xml1;
			
		
	}else{
		
		echo "Selected address country is other than Australia. Select Tnt International";
	}
		
	}
	
	////27-12-2013 //21-1-2014
	public function gettntlocalmethodAction(){
		
		extract($_REQUEST);
		///21-1-2013  S
		$order = Mage::getModel('sales/order')->load($ordId);
		$pos1 = stripos($addrsid, '_S');
		if($pos1 != false){
			$adr= explode("_",$addrsid);
			$addrsid = $adr[0];
			$_shippingAddress = $order->getShippingAddress();
			$destCountry = $_shippingAddress->getCountry_id();
			$postCode = $_shippingAddress->getPostcode();
			$city = $_shippingAddress->getCity();
			$region = $_shippingAddress->getRegion();
		}else{
			
			$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
			$destCountry=$addressLoadId['country_id'];
			$postCode=$addressLoadId['postcode'];
			$city = $addressLoadId['city'];
			$region = $addressLoadId['region'];
		}
		///21-1-2013 E
		
		//$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
		//$destCountry=$addressLoadId['country_id'];
		//$postCode=$addressLoadId['postcode'];
		//$city = $addressLoadId['city'];
		//$region = $addressLoadId['region'];
		
		
		
		$tWeight=$order->getWeight();
		$fromPostCode = '3207';
		$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId());
		//$aus=Mage::getModel('shipping/carrier/australiapost')->_drcRequest();
		$ret=array();
		$res='';
		$arrc=explode("@@@@",$unit);
		$l=0;
		//$arrc=explode("__",$unit);
		
		//print_r($arrc);
		//$boxArr=explode(":",$arrc[0]);
		//$qtyArr=explode(":",$arrc[1]);
		//$lengArr=explode(":",$arrc[2]);
		//$hghtArr=explode(":",$arrc[3]);
		//$widtArr=explode(":",$arrc[4]);
		//$totwghtArr=explode(":",$arrc[6]);
		//
		//$weight = $totwghtArr[1];
		//$height = $hghtArr[1];
		//$width = $widtArr[1];
		//$length = $lengArr[1];
		//$shipping_num_boxes = $boxArr[1];
		
		
		//$weight = $tWeight;
		//$height = $width = $length = 100;
		
		$shipping_num_boxes = 1;
		$allItems = $order->getAllItems();
		$date = date('Y-m-d');
		//$date = '2014-10-10';
		
		if ($destCountry == "AU") {
			$requestXml='<?xml version="1.0"?>
				<enquiry xmlns="http://www.tntexpress.com.au">
				  <ratedTransitTimeEnquiry>
					<cutOffTimeEnquiry>
					  <collectionAddress>
						<suburb>Port Melbourne</suburb>
						<postCode>'.trim($fromPostCode).'</postCode>
						<state>VIC</state>
					  </collectionAddress>
					  <deliveryAddress>
						<suburb>'.trim($city).'</suburb>
						<postCode>'.trim($postCode).'</postCode>
						<state>VIC</state>
					  </deliveryAddress>
					  <shippingDate>'.trim($date).'</shippingDate>
					  <userCurrentLocalDateTime>
						2007-11-05T10:00:00
					  </userCurrentLocalDateTime>
					  <dangerousGoods>
						<dangerous>false</dangerous>
					  </dangerousGoods>
					  <packageLines packageType="D">';
				//foreach($allItems as $item){
				//	$itemId = $item->getId();									
				//	$quoteId = $item->getQuoteId();				
				//	$productId = $item->getProductId();				
				//	$quoteModel = Mage::getModel('sales/quote')->load($quoteId);				
				//	$addresses = $quoteModel->getAllShippingAddresses();
				//	$product = Mage::getModel('catalog/product')->load($productId);
				//	
				//	if($item->getQty()) $qty = $item->getQty(); else $qty = $qtyArr[1];
				//	if($product->getWeight()) $weight = $product->getWeight(); else $weight = $weight;
				//	if($product->getLength()) $length = $product->getLength(); else $length = $length;
				//	if($product->getWidth())  $width = $product->getWidth(); else $width = $width;
				//	if($product->getHeight()) $height = $item->getQty(); else $height = $height;
				//	
				//		
				//	  $requestXml.=
				//		'
				//		<packageLine>
				//		  <numberOfPackages>'.intval($qty).'</numberOfPackages>
				//		  <dimensions unit="cm">
				//			<length>'.trim($length).'</length>
				//			<width>'.trim($width).'</width>
				//			<height>'.trim($height).'</height>
				//		  </dimensions>
				//		  <weight unit="kg">
				//			<weight>'.trim($weight).'</weight>
				//		  </weight>
				//		</packageLine>';
				//}
				foreach($arrc as $a){
					$l++;
					$arrc1 = explode("__",$a);					
					if($l > 1){
					foreach($arrc1 as $a1){
						$boxArr=explode(":",$a1);
						if($boxArr[0]== 'qty') $qty = $boxArr[1];
						if($boxArr[0]== 'box') $box = $boxArr[1]; 
						if($boxArr[0]== 'len') $length = $boxArr[1]; 
						if($boxArr[0]== 'hght')  $height = $boxArr[1]; 
						if($boxArr[0]== 'width')  $width = $boxArr[1]; 
						if($boxArr[0]== 'weight')  $weight = $boxArr[1]; 
						
						$requestXml.=
						'
						<packageLine>
						  <numberOfPackages>'.intval($box).'</numberOfPackages>
						  <dimensions unit="cm">
							<length>'.trim($length).'</length>
							<width>'.trim($width).'</width>
							<height>'.trim($height).'</height>
						  </dimensions>
						  <weight unit="kg">
							<weight>'.trim(round($weight)).'</weight>
						  </weight>
						</packageLine>';
					}
					}
				
				}
				$requestXml.='
				</packageLines>
					</cutOffTimeEnquiry>
					<termsOfPayment>
					  <senderAccount>21664906</senderAccount>
					  <payer>S</payer>
					</termsOfPayment>
				  </ratedTransitTimeEnquiry>
				</enquiry>';
				//Mage::getSingleton('core/session')->addSuccess($requestXml);
				$xml['Username']='CIT00000000000035655';
				$xml['Password']='toyota11';
				$xml['XMLRequest']=$requestXml;
				//$response=$this->requestTntResponse($xml);
				
				
				//echo $requestXml;
				$iClient = new Varien_Http_Client();
				
				
				$iClient->setUri('https://www.tntexpress.com.au/Rtt/inputRequest.asp')
					  ->setMethod('POST')
					  ->setConfig(array(
						    'maxredirects'=>0,
						    'timeout'=>30,
					  ));
				$iClient->setParameterPost($xml);    
				$response = $iClient->request();
				$results = $response->getBody();
				
				//$results = $response->getBrokenrules();
				$xml1=simplexml_load_string($results);
				
				$array = $this->XML2Array($xml1);
				$res='';
				//echo "<pre>";print_r($array);
				if($array['ratedTransitTimeResponse']['brokenRules'] && !$array['ratedTransitTimeResponse']['ratedProducts']){
					
					$res= $array['ratedTransitTimeResponse']['brokenRules']['brokenRule']['description'];
					
				}else{
					
					$ratedArray = $array['ratedTransitTimeResponse']['ratedProducts']['ratedProduct'];
				
					foreach($ratedArray as $key=>$ratedProduct){
						if(is_numeric($key)){
						$mt = $ratedProduct['product']['description'];
						
						if($ratedProduct['quote']['price']){
							$rate="$".$ratedProduct['quote']['price'];
						}else{
							$rate=0;
						}
						
						
						$val = "TNT ".$mt."__".$ratedProduct['quote']['price'];
						$mt1=$mt." ".$rate;
						$res .= '<input name="shipping_method_tntl" type="radio" value="'.$val.'" id="" class="radio" onclick="slmthd(this.value);"/>  '.$mt1.'</br>';
						}
					}
				}
				
				echo $res;
				
				//$ratedArray = $xml1;
				
			
		}else{
			
			echo "Selected address country is other than Australia. Select Tnt International";
		}
	}
	
	
	////24-12-2013 EOC
	///26-12-2013
	public function gettntintlAction(){
		extract($_REQUEST);
		///21-1-2013  S
		$order = Mage::getModel('sales/order')->load($ordId);
		$pos1 = stripos($addrsid, '_S');
		if($pos1 != false){
			$adr= explode("_",$addrsid);
			$addrsid = $adr[0];
			$_shippingAddress = $order->getShippingAddress();
			$destCountry = $_shippingAddress->getCountry_id();
			$postCode = $_shippingAddress->getPostcode();
			$city = $_shippingAddress->getCity();
			$region = $_shippingAddress->getRegion();
		}else{
			
			$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
			$destCountry=$addressLoadId['country_id'];
			$postCode=$addressLoadId['postcode'];
			$city = $addressLoadId['city'];
			$region = $addressLoadId['region'];
		}
		///21-1-2013 E
		//$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
		//$destCountry=$addressLoadId['country_id'];
		//$postCode=$addressLoadId['postcode'];
		//$city = $addressLoadId['city'];
		//$region = $addressLoadId['region'];
		
		
		$tWeight=$order->getWeight();
		$qty = $weight = $height = $width = $length = 0;
		//$date = date('Y-m-d');
		$date = '2014-10-10';
		
		$weight = $tWeight;
		//$weight = $request->getPackageVolume();
		$qty = 3;
		$allItems = $order->getAllItems();		
		foreach($allItems as $item){
			$itemId = $item->getId();									
			$quoteId = $item->getQuoteId();				
			$productId = $item->getProductId();				
			$quoteModel = Mage::getModel('sales/quote')->load($quoteId);				
			$addresses = $quoteModel->getAllShippingAddresses();
			$product = Mage::getModel('catalog/product')->load($productId);				
			if($item->getQty()) $qty = $item->getQty(); else $qty = 1;
			if($product->getWeight()) $weight = $product->getWeight(); else $weight = 10;
			if($product->getLength()) $length = $product->getLength(); else $length = 10;
			if($product->getWidth())  $width = $product->getWidth(); else $width = 10;
			if($product->getHeight()) $height = $item->getQty(); else $height = 10;
			
			
			//$qty = $item->getQty();		
			//$weight = $product->getWeight();
			//$length = $product->getLength();
			//$width = $product->getWidth();
			//$height = $product->getHeight();
			$volume+= (($length*$width*$height)/4000 )*$qty;
		}
		
		$requestXml=
		'<?xml version="1.0" encoding="UTF-8"?>
		<PRICEREQUEST>
		  <LOGIN>
		    <COMPANY>vividintT</COMPANY>
		    <PASSWORD>tnt12345</PASSWORD>
		    <APPID>PC</APPID>
		  </LOGIN>
		  <PRICECHECK>
		    <RATEID>rate1</RATEID>
		    <ORIGINCOUNTRY>AU</ORIGINCOUNTRY>
		    <ORIGINTOWNNAME>Sydney</ORIGINTOWNNAME>
		    <ORIGINPOSTCODE>2000</ORIGINPOSTCODE>
		    <ORIGINTOWNGROUP/>
		    <DESTCOUNTRY>'.trim($destCountry).'</DESTCOUNTRY>
		    <DESTTOWNNAME>'.trim($city).'</DESTTOWNNAME>
		    <DESTPOSTCODE>'.trim($postCode).'</DESTPOSTCODE>
		    <DESTTOWNGROUP/>
		    <CONTYPE>N</CONTYPE>
		    <CURRENCY>AUD</CURRENCY>
		    <WEIGHT>'.trim($weight).'</WEIGHT>
		    <VOLUME>'.trim($volume).'</VOLUME>
			<ACCOUNT accountcountry="AU">021664906</ACCOUNT>
		    <ITEMS>1</ITEMS>
		  </PRICECHECK>
		</PRICEREQUEST>';
		if ($destCountry != "AU") {
		$xml=array();
		$xml['xml_in']=$requestXml;
		
		$iClient = new Varien_Http_Client();
		$iClient->setUri('https://express.tnt.com/expressconnect/pricing/getprice')
			->setMethod('POST')
			->setConfig(array(
				'maxredirects'=>0,
				'timeout'=>30,
			));
		$iClient->setParameterPost($xml);    
		$response = $iClient->request();
	
		if ($response->isSuccessful())  $results = $response->getBody();
		    
		$xml1=simplexml_load_string($results);
		$ratedArray = $xml1->PRICE;
		$array = $this->XML2Array($xml1);
		$res='';
		
		foreach($array['PRICE'] as $key=>$arrProduct){
					
			if(is_numeric($key)){
				if($array['PRICE'][$key]['RATE']) {
					$rate="$".$array['PRICE'][$key]['RATE'];
				}else{
					$rate=0;
				}	
				$mt = $array['PRICE'][$key]['SERVICE']." ".$array['PRICE'][$key]['SERVICEDESC']." ".$rate;
				$val= $array['PRICE'][$key]['SERVICE']." ".$array['PRICE'][$key]['SERVICEDESC']."__".$array['PRICE'][$key]['RATE'];
				$res .= '<input name="shipping_method_tntl" type="radio" value="'.$val.'" id="" class="radio" onclick="slmthd(this.value);"/>  '.$mt.'</br>';
			}		
		}
		echo $res;
		//echo "<pre>";print_r($array['PRICE']);
		//echo $xml1;
			
		}
		else{
			echo "Selected address country is Australia. Select Tnt Australia";
			
		}
	}
	
	////27-12-2013
	public function gettntintlmethodAction(){
		extract($_REQUEST);
		$arrc=explode("@@@@",$unit);
		$l=0;
		///21-1-2013  S 
		$pos1 = stripos($addrsid, '_S');
		if($pos1 != false){
			$adr= explode("_",$addrsid);
			$addrsid = $adr[0];
			$_shippingAddress = $order->getShippingAddress();
			$destCountry = $_shippingAddress->getCountry_id();
			$postCode = $_shippingAddress->getPostcode();
			$city = $_shippingAddress->getCity();
			$region = $_shippingAddress->getRegion();
		}else{
			
			$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
			$destCountry=$addressLoadId['country_id'];
			$postCode=$addressLoadId['postcode'];
			$city = $addressLoadId['city'];
			$region = $addressLoadId['region'];
		}
		///21-1-2013 E
		//$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
		//$destCountry=$addressLoadId['country_id'];
		//$postCode=$addressLoadId['postcode'];
		//$city = $addressLoadId['city'];
		//$region = $addressLoadId['region'];
		
		$order = Mage::getModel('sales/order')->load($ordId);
		$tWeight=$order->getWeight();
		$qty = $weight = $height = $width = $length = 0;
		
		//$arrc=explode("__",$unit);
		//
		//print_r($arrc);
		//$boxArr=explode(":",$arrc[0]);
		//$qtyArr=explode(":",$arrc[1]);
		//$lengArr=explode(":",$arrc[2]);
		//$hghtArr=explode(":",$arrc[3]);
		//$widtArr=explode(":",$arrc[4]);
		//$totwghtArr=explode(":",$arrc[6]);
		//
		//$weight = $totwghtArr[1];
		//$height = $hghtArr[1];
		//$width = $widtArr[1];
		//$length = $lengArr[1];
		//$shipping_num_boxes = $boxArr[1];
		
		
		$date = date('Y-m-d');
		//$date = '2014-10-10';
		
		$weight = $tWeight;
		//$weight = $request->getPackageVolume();
		$qty = $qtyArr[1];
		$allItems = $order->getAllItems();		
		//foreach($allItems as $item){
		//	$itemId = $item->getId();									
		//	$quoteId = $item->getQuoteId();				
		//	$productId = $item->getProductId();				
		//	$quoteModel = Mage::getModel('sales/quote')->load($quoteId);				
		//	$addresses = $quoteModel->getAllShippingAddresses();
		//	$product = Mage::getModel('catalog/product')->load($productId);				
		//	if($item->getQty()) $qty = $item->getQty(); else $qty = $qty;
		//	if($product->getWeight()) $weight = $product->getWeight(); else $weight = $weight;
		//	if($product->getLength()) $length = $product->getLength(); else $length = $length;
		//	if($product->getWidth())  $width = $product->getWidth(); else $width = $width;
		//	if($product->getHeight()) $height = $item->getQty(); else $height = $height;
		//	
		//	
		//	$volume+= (($length*$width*$height)/4000 )*$qty;
		//}
		$qty = $weight = $height = $width = $length = 0;
		foreach($arrc as $a){
			$l++;
			$arrc1 = explode("__",$a);					
			if($l > 1){
			foreach($arrc1 as $a1){
				$boxArr=explode(":",$a1);
				if($boxArr[0]== 'qty') $qty = $qty + $boxArr[1];
				if($boxArr[0]== 'box') $box = $box + $boxArr[1]; 
				if($boxArr[0]== 'len') $length = $length + $boxArr[1]; 
				if($boxArr[0]== 'hght')  $height = $height + $boxArr[1]; 
				if($boxArr[0]== 'width')  $width = $width + $boxArr[1]; 
				if($boxArr[0]== 'weight')  $weight = $weight + $boxArr[1]; 
				
				
			}
			}
				
		}
		$volume= (($length*$width*$height)/4000 )*$qty;
		$requestXml=
		'<?xml version="1.0" encoding="UTF-8"?>
		<PRICEREQUEST>
		  <LOGIN>
		    <COMPANY>vividintT</COMPANY>
		    <PASSWORD>tnt12345</PASSWORD>
		    <APPID>PC</APPID>
		  </LOGIN>
		  <PRICECHECK>
		    <RATEID>rate1</RATEID>
		    <ORIGINCOUNTRY>AU</ORIGINCOUNTRY>
		    <ORIGINTOWNNAME>Sydney</ORIGINTOWNNAME>
		    <ORIGINPOSTCODE>2000</ORIGINPOSTCODE>
		    <ORIGINTOWNGROUP/>
		    <DESTCOUNTRY>'.trim($destCountry).'</DESTCOUNTRY>
		    <DESTTOWNNAME>'.trim($city).'</DESTTOWNNAME>
		    <DESTPOSTCODE>'.trim($postCode).'</DESTPOSTCODE>
		    <DESTTOWNGROUP/>
		    <CONTYPE>N</CONTYPE>
		    <CURRENCY>AUD</CURRENCY>
		    <WEIGHT>'.trim($weight).'</WEIGHT>
		    <VOLUME>'.trim($volume).'</VOLUME>
			<ACCOUNT accountcountry="AU">021664906</ACCOUNT>
		    <ITEMS>1</ITEMS>
		  </PRICECHECK>
		</PRICEREQUEST>';
		if ($destCountry != "AU") {
		$xml=array();
		$xml['xml_in']=$requestXml;
		
		$iClient = new Varien_Http_Client();
		$iClient->setUri('https://express.tnt.com/expressconnect/pricing/getprice')
			->setMethod('POST')
			->setConfig(array(
				'maxredirects'=>0,
				'timeout'=>30,
			));
		$iClient->setParameterPost($xml);    
		$response = $iClient->request();
	
		if ($response->isSuccessful())  $results = $response->getBody();
		    
		$xml1=simplexml_load_string($results);
		$ratedArray = $xml1->PRICE;
		$array = $this->XML2Array($xml1);
		$res='';
		
		foreach($array['PRICE'] as $key=>$arrProduct){
					
			if(is_numeric($key)){
				if($array['PRICE'][$key]['RATE']) {
					$rate="$".$array['PRICE'][$key]['RATE'];
				}else{
					$rate=0;
				}	
				$mt = $array['PRICE'][$key]['SERVICE']." ".$array['PRICE'][$key]['SERVICEDESC']." ".$rate;
				$val= $array['PRICE'][$key]['SERVICE']." ".$array['PRICE'][$key]['SERVICEDESC']."__".$array['PRICE'][$key]['RATE'];
				$res .= '<input name="shipping_method_tntl" type="radio" value="'.$val.'" id="" class="radio" onclick="slmthd(this.value);"/>  '.$mt.'</br>';
			}		
		}
		echo $res;
		//echo "<pre>";print_r($array['PRICE']);
		//echo $xml1;
			
		}
		else{
			echo "Selected address country is Australia. Select Tnt Australia";
			
		}
		
		
	}
	
		
}