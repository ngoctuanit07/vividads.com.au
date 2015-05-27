<?php

class Partialshipping_Partialshipping_Adminhtml_PartialshippingController extends Mage_Adminhtml_Controller_Action
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
	
	//16-11-2013 SOC  ////modified on 18-2-2014
	public function partshipformAction(){
		extract($_REQUEST);
		$box = explode('@@@@',$_REQUEST['box']);
		$tot=count($box)-1;
		///25-1-2014 S
		$tnt= 0;
		$isTnt = strpos($method,"TNT");
		if($isTnt === false){
			$tnt= 0;
		}else{
			$tnt= 1;
		}
		///25-1-2014 E
		
		//$method = explode("__",$shipping_method_tntl);
		
		$tnt_methos = array('Express'=>'EX','Fashion Express'=>'FE','General'=>'GE','Sameday'=>'701','9:00 Express'=>'712','10:00 Express'=>'X10','12:00 Express'=>'X12','CIT Pay As You Use'=>'73','Overnight Express'=>'75','Road Express'=>'76','Air/Road Combo'=>'77','Technology Express'=>'717','Fashion Express'=>'718');
		
		
		$order = Mage::getModel('sales/order')->load($ordId);
		$totalqtyOrdered=round($order->getTotalQtyOrdered());
		$qtyShipped=0;
		$qtyS=$flg=$l=0;
		
		foreach($box as $bx){
			$l++;
			if($l >1){
				$boxArr=explode("__",$bx);
				$qtyPos =stripos($boxArr[1], ":");
				$qtyP = $qtyPos+1;
				$qtyS=$qtyS + substr($boxArr[1],$qtyP);
			}
			
		}
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
		$tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		$tableName2 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		$tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
		$tableName4 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment');
		$tableNameItem = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_item');
		$tableNameTrack = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_track');
		
		$select = $connectionRead->select()->from($tableName, array('*'))->where('shipment_grid_id=?',$shipId);
		$row = $connectionRead->fetchAll($select);
		
		$itemsall = $order->getAllItems();
		
		foreach($row as $res){
			$select1 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$res['order_item_id']);
			$row1 = $connectionRead->fetchRow($select1);
			$pId = $row1['product_id'];
			$_product = Mage::getModel('catalog/product')->load($pId);
			$weight = $_product->getWeight();
			$totweight +=$weight;
			$qtny += $res['qty'];
			
			foreach($itemsall as $orderDetails)
                        {
				if($orderDetails->getId() == $res['order_item_id'])
				Mage::getModel('timeline/timeline')->UpdateTimeline('shipment',$order->getId(),$orderDetails,'order');//28_02_2014
			}
			
		}
		if( $qtyS > $qtny){
			$msg='Shipped quantity is greater than quantity ordered.';
			$this->_getSession()->addError($msg);
			$url = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view/order_id/".$ordId);
			Mage::log($url); 
       			Mage::app()->getResponse()->setRedirect($url);
			//echo "error";
			
		}else{
			
			//echo "QTY S : ".$qtyS ."<br>".$qtny;
			if(strpos($_REQUEST['addrsid'],'_S'))
			{
				$addr = explode('_S',$_REQUEST['addrsid']);
				$addressLoadId = $order->getShippingAddress();
			}
			else
			{
				$addr = array(0 => $_REQUEST['addrsid']);
				$addressLoadId = Mage::getModel('customer/address')->load($addr[0]);
			}
			//echo "<pre>"; print_r($addressLoadId->getData());echo "</pre>"; exit;
			
			$shipAd=$addr[0];
			$shipMd=$_REQUEST['method'];
			$ordId=$_REQUEST['ordId'];
			$shM=explode("__",$shipMd);
			$shMethod=$shM[0];
			$shPrice=$shM[1];
			$shpAmt=$order->getShippingAmount();
			$ordincrId=$order->getIncrementId();
			
			$sel = $connectionRead->select()->from($tableName3, array('increment_id'))->where('entity_id=?',$shipId);
			$ro = $connectionRead->fetchRow($sel);
			$shipmenIncrmnttId = $ro['increment_id'];
			//$custmerId=$_REQUEST['customerId'];
			
			$select1 = $connectionRead->select()->from($tableName3, array('*'))->where("order_id = '".$ordId."' AND ship_amount_updated != 0");
			$row1 = $connectionRead->fetchall($select1);
			
			//echo "<pre>";print_r($row1);exit;
			
			$select2 = $connectionRead->select()->from($tableName2, array('*'))->where('entity_id=?',$ordId);
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
			
			
			if(count($row1) > 0){
				
			}else{
				
				if($baseTotalDueN != $baseTotalDue){
		    
					$connectionWrite->beginTransaction();
					$data = array();
					$data1 = array();
					if($baseTotalDueN) $data['base_total_due'] = $baseTotalDueN;
					if($TotalDueN) $data['total_due'] = $TotalDueN;
					if($baseGrandTotalN) $data['base_grand_total'] = $baseGrandTotalN;
					if($GrandTotalN) $data['grand_total'] = $GrandTotalN;
					
					$where = $connectionWrite->quoteInto('entity_id =?', $ordId);
					$connectionWrite->update($tableName2, $data, $where);
					$connectionWrite->commit();
					
					$data1['ship_amount_updated']=1;
					$where1 = $connectionWrite->quoteInto('entity_id =?', $shipId);
					$connectionWrite->update($tableName3, $data1, $where1);
					$connectionWrite->commit();
				
				}
			
			}
			
			
			
			
			if($shipAd !='' && $shipMd !='' && $_REQUEST['box'] != '' ){
				
				$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
				$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
				
				//$created_at = date("Y-m-d h:m:s");
				$t=time();
				//$created_at = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
				
				$created_at = date("Y-m-d H:i:s", $t);
				$date_post = strtotime($order->getCreatedAtDate()); 
				$Ordtime=date('Y-m-d H:i:s',$date_post );
				
				$Name = $addressLoadId['firstname'].' '.$addressLoadId['lastname'];
				
				
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
				
				
				//echo "INVI : ".$ordincrId;exit;
				$postCode = $addressLoadId['postcode'];
				$city = $addressLoadId['city'];
				$firstName = $addressLoadId['firstname'];
				$lastName = $addressLoadId['lastname'];
				$region = $addressLoadId['region'];
				$regionId = $addressLoadId['region_id'];
				
				$regionModel = Mage::getModel('directory/region')->load($regionId);
				$regCode=$regionModel->getCode();
				
				$street = $addressLoadId['street'];
				$telephone = $addressLoadId['telephone'];
				$company = $addressLoadId['company'];
				$country_code=$addressLoadId['country_id'];
				
				$countryModel = Mage::getModel('directory/country')->loadByCode($country_code);
				$destCountry = $countryModel->getName();
				
				if($company) {$cmp=",<br>".$company; $cmp1=",".$company;} else{$cmp='';$cmp1='';} ///24-2-2014
				
				
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
				
								
					
				
				$total = count($box)-1; ///24-2-2014
				$i=0;
				
		/********************* Start upload file to ft server *****************************/
			$b=$l=0;
	
		
			$sel1 = $connectionRead->select()->from($tableName4, array('entity_id'))->where("order_id = '".$ordId."' AND increment_id= '".$shipmenIncrmnttId."'");
			$ro1 = $connectionRead->fetchRow($sel1);
			$ship1 = $ro1['entity_id'];
			
			
			$lastId = $ship1;
			
			//$lastId = $connectionWrite->fetchOne('SELECT last_insert_id()');
			
			$conNoteNumber="VVD".(str_pad($lastId,9,"0",STR_PAD_LEFT));
					
		/******************* Start to insert the shipment table *************************/
		$tableName4 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment');
		$connectionWrite->beginTransaction();
		$data3=array();
		
		
		if($addr[0]) $data3['shipping_address_id']= $addr[0];
		if($order->getBillingAddressId()) $data3['billing_address_id']= $order->getBillingAddressId();
		
		//$ship=explode('__',$sel_metd);
		if($shMethod) $data3['shippingmethod']= $shMethod;
		if($shPrice) $data3['shippingrate']= $shPrice;
		
		if($shipmenIncrmnttId) $data3['increment_id']= $shipmenIncrmnttId;
		if($created_at) $data3['updated_at']= $created_at;
		
	       
		$where3 = $connectionWrite->quoteInto("order_id = '".$ordId."' AND increment_id= '".$shipmenIncrmnttId."'");					
		$connectionWrite->update($tableName4, $data3, $where3);
		$connectionWrite->commit();
		/******************* End to insert the shipment table*************************/					
		/******************* Start to insert the shipment grid table *************************/
		$connectionWrite->beginTransaction();
		$data = array();
		
		if($Name) $data['shipping_name']=$Name;
		if($order->getStatusLabel()) $data['payment_status']=$order->getStatusLabel();
		if($conNoteNumber) $data['track_number']=$conNoteNumber;
		$data['label_created']=1; ///20-2-2014 s
		
		$data['created_at']=$shipdate; ///3-3-2014 s
		
		$where13 = $connectionWrite->quoteInto('entity_id=?',$shipId);					
		$connectionWrite->update($tableName3, $data, $where13);
		$connectionWrite->commit();
		
		//$connectionWrite->insert($tableName3, $data);
		//$connectionWrite->commit();
		
		//$new_ship_id  = $connectionWrite->fetchOne('SELECT last_insert_id()');
		$new_ship_id  = $shipId;
		/******************* End to insert the shipment grid table*************************/
					
		
		/********************* Start for shipment track **************************/
		$sel_metd2 = explode('__',$_REQUEST['method']);
						
		if(strpos('_'.$sel_metd2[0],'tnt'))
		$title = 'Tnt Australia';
		
		
		$sel14 = $connectionRead->select()->from($tableName, array('parent_id'))->where("shipment_grid_id = '".$shipId."'");
		$ro14 = $connectionRead->fetchRow($sel14);
		$ship14 = $ro14['parent_id'];
		
		
		try
		{
			
			$Insert = "INSERT INTO ".$tableNameTrack." SET parent_id = '".$ship14."',  weight = '".$row1['weight']."', qty = '".$qty."', order_id = '".$ordId."', track_number = '30015', title = '".$title."', carrier_code = '".$sel_metd2[0]."', created_at= NOW(), updated_at = NOW(); commit; ";
			$connectionWrite->query($Insert);
			//$connectionWrite->commit();
			//exit;
		}
		catch(Exception $e)
		{
			print_r($e);///exit;
		    
		}
		/********************* Start for shipment track **************************/
		
			if($invIncrementID == '')
			$invIncrementID = '';
			
			///27-2-2014 S
			
			if($tnt == 1){
					
				if($country_code == 'AU'){
					Mage::getModel('partialshipping/partialshipping')->getTNTlebal($box,$conNoteNumber,$addressLoadId,$_REQUEST,$method,$shipId,$invIncrementID,$order);
					Mage::getModel('partialshipping/partialshipping')->getTNTet($box,$conNoteNumber,$addressLoadId,$_REQUEST,$method,$shipId,$invIncrementID,$order,$addr);	
					
				}else{
					$conNoteNumber=str_pad($lastId,9,"0",STR_PAD_LEFT);
					$res = Mage::getModel('partialshipping/partialshipping')->getTNTintllebal($box,$conNoteNumber,$addressLoadId,$_REQUEST,$method,$shipId,$invIncrementID,$order);
				//print_r($res);
				//exit;
				}
				
			
			}
			///27-2-2014 E
			
			$msg='Shipment Created Successfully.';
			$this->_getSession()->addSuccess($msg);
			$this->_redirect('adminhtml/sales_order/view', array('order_id' => $ordId));	
			//echo "Success";
			
			}
			else
			{
				$msg='Please save the confirm box and select the shipping address and shipping method.';
				$this->_getSession()->addError($msg);
				$this->_redirect('adminhtml/sales_order/view', array('order_id' => $ordId));
				//echo "error";
			}
			
		}	
		
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
	
	
	
	
	
	
	
	//// 2-12-2013 modified on 12-12-2013 SOC  ///modified on 13-2-2014
	public function getauspostmethodAction(){
		extract($_REQUEST);
		//echo "<pre>"; print_r($_REQUEST);echo "<pre>"; exit;
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
		
	//	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	//        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	//	$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
	//	$tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
	//	
	//	$select = $connectionRead->select()->from($tableName, array('*'))->where('shipment_grid_id=?',$shipId);
	//	$row = $connectionRead->fetchAll($select);
	//	$tWeight = 0;
	//	foreach($row as $res){
	//		$select1 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$res['order_item_id']);
	//		$row1 = $connectionRead->fetchRow($select1);
	//		$pId = $row1['product_id'];
	//		$_product = Mage::getModel('catalog/product')->load($pId);
	//		$weight = $_product->getWeight();
	//		$tWeight +=$weight;
	//		
	//	}
		
		
		$fromPostCode = '3207';
		$result = Mage::getModel('shipping/rate_result');
		$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId());
		//$aus=Mage::getModel('shipping/carrier/australiapost')->_drcRequest();
		$ret=array();
		$res='';
		
		$arrc=explode("__",$unit);
		
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
							////17-2-2014 GC S
							$charge = $result['charge'];
							$margin = Mage::getStoreConfig('carriers/australiapost/profit_margin')/100;
							$charge = $charge + ($charge*$margin);
							////17-2-2014 GC E
							
							$strVal="Australiapost ".$shipping_method." ".$result['days']."__".number_format($charge,2);
							$strLabel=$shipping_method." ".$result['days']." Days $".number_format($charge,2);
						} else {
							return array('err_msg' => 'Parsing error on Australia Post results');
						}
					}
					array_push($ret,$result);
					$res.='<input name="shipping_method_aus" type="radio" value="'.$strVal.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$strLabel.'</br>';
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
						////17-2-2014 GC S
						$charge = $result['charge'];
						$margin = Mage::getStoreConfig('carriers/australiapost/profit_margin')/100;
						$charge = $charge + ($charge*$margin);
						////17-2-2014 GC E
						$strVal="Australiapost ".$shipping_method." ".$result['days']."__".number_format($charge,2);
						$strLabel="Australiapost ".$shipping_method." $".number_format($charge,2);
					} else {
						return array('err_msg' => 'Parsing error on Australia Post results');
					}
				}
				array_push($ret,$result);
				$res.='<input name="shipping_method_aus" type="radio" value="'.$strVal.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$strLabel.'</br>';
			
			}
			
		}
		
		}else{
			
			echo "Weight exceeds maximum allowed weight (20kgs)";
		}
		
		echo $res;
		
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
			else if ($isready == 'ntready') $data['status'] = 'Not Ready'; //28_01_2014
			else if ($isready == 'shipmented') $data['status'] = 'Shipped'; //28_01_2014

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
	////modified on 13-2-2014////
	public function getconfirmboxAction(){
		extract($_REQUEST);
		$totweight = $qtny = 0;
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
		$tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		
		$tableNameBox = Mage::getSingleton('core/resource')->getTableName('partialshipping_confirmbox');
		
		//$tableName2 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
		//$tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
		
		$select = $connectionRead->select()->from($tableName, array('*'))->where('shipment_grid_id=?',$sId);
		$row = $connectionRead->fetchAll($select);
		
		$bLength=$bWidth=$bHeight=$bvol=$totVol=0;
		foreach($row as $res){
			$select1 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$res['order_item_id']);
			$row1 = $connectionRead->fetchRow($select1);
			$pId = $row1['product_id'];
			$_product = Mage::getModel('catalog/product')->load($pId);
			$weight = $_product->getWeight();
			$totweight +=$weight;
			$qtny += $res['qty'];
			
			///24-2-2014 S
			
			$select = $connectionRead->select()->from($tableNameBox, array('*'))->where('product_id=?',$_product->getId());
							
			$row2 = $connectionRead->fetchRow($select);
			if(!empty($row2))
			{
				$bLength = $row2['lenght'];
				$bWidth = $row2['width'];
				$bHeight = $row2['height'];
				
				
			}
			else{
				$bLength = $_product->getLength();
				$bWidth = $_product->getWidth();
				$bHeight = $_product->getHeight();
				
			}
			
			$bvol = $bLength* $bWidth*$bHeight;
			$totVol += $bvol;
			///24-2-2014 S
		}
		
		
		$i=$j=$p=$k=1;
		$bLenght=$bWidth=$bHeight=$bWeight=0;
		
		$urlact = Mage::helper("adminhtml")->getUrl("partialshipping/adminhtml_partialshipping/confiboxform");
		$str='<form id="cnfrm" name="cnfrm" action="'.$urlact.'" method="post">
			<input name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'">
			<input name="store_code" type="hidden" value="'.Mage::app()->getStore()->getCode().'">
			<input name="store_id" type="hidden" value="'.Mage::app()->getStore()->getStoreId().'">
			<input name="ship_grid_id" type="hidden" value="'.$sId.'">
			<table width="750px" id="tabl_'.$j.'">
			<tr>
			<td>Total Weight : <input type="text" name="confirmtotalweight" id="confirmtotalweight" value="'.$totweight.'"/></td>
			<td>Total Volum : <input type="text" name="confirmtotalvolum" id="confirmtotalvolum" value="'.$totVol.'"/></td>
			<input type="hidden" name="confirmtotqty" id="confirmtotqty" value="'.$qtny.'"/>
			</tr>
			<tr>
			<td colspan="2"> 
				<table id="tabl_row_'.$k.'">
					<tr class="headings">
					<th>Boxes</th><th>Qty</th><th>Lenght</th><th>Width</th><th>Height</th><th>weight</th><th>Title</th><th>Action</th>
					</tr>';
		
					foreach($row as $item){
						
						$str.='<tr id="row_'.$i.'">';
						$select2 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$item['order_item_id']);
						$row2 = $connectionRead->fetchRow($select2);
						$pId1 = $row2['product_id'];
						$_product = Mage::getModel('catalog/product')->load($pId1);
							
						
						if($_product->getTypeId() == 'simple'){
							
							/************* Start for auto populate the data in confirmbox *************/
							$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
							$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
							$tableNameBox = Mage::getSingleton('core/resource')->getTableName('partialshipping_confirmbox');
							$select = $connectionRead->select()->from($tableNameBox, array('*'))->where('product_id=?',$_product->getId());
							
							$row2 = $connectionRead->fetchRow($select);
							if(!empty($row2))
							{
								$length = $row2['lenght'];
								$width = $row2['width'];
								$height = $row2['height'];
								$weight = $row2['weight'];
							}
							else{
								$length = $_product->getLength();
								$width = $_product->getWidth();
								$height = $_product->getHeight();
								$weight = $_product->getWeight();
							}
							/************* End for auto populate the data in confirmbox *************/
						$str.='
						<td><input type="hidden" name="confirmproduct[]" value="'.$_product->getId().'" /><input type="text" name="confirmbox[]" id="confirmbox" value="1" style="width : 80px"/></td>
						<td><input type="text" name="confirmqty[]" id="confirmqty" value="'.$item['qty'].'" style="width : 80px"/></td>
						<td><input type="text" name="confirmlength[]" id="confirmlength" value="'.$length.'" style="width : 80px"/></td>
						<td><input type="text" name="confirmwidth[]" id="confirmwidth" value="'.$width.'" style="width : 80px"/></td>
						<td><input type="text" name="confirmheight[]" id="confirmheight" value="'.$height.'" style="width : 80px"/></td>
						<td><input type="text" name="confirmweight[]" id="confirmheight" value="'.number_format($weight,2).'" style="width : 80px"/></td>';
						//<td><input type="text" name="confirmtitle[]" id="confirmtitle"  value="Vivid '.$p.'" style="width : 80px"/> </td>//20_02_2014
						
						$str.='<td><input type="text" name="confirmtitle[]" id="confirmtitle"  value="'.$_product->getSku().'" style="width : 80px"/> </td>
						<td><span style="cursor:pointer;float: right;" title="Add More" class="addanother" onclick="remove_r('.$i.');">Remove</span></td>';
						}
						$str.='</tr>';
						$i++;
						$p++;
						$j++;
					
					}
				$str.='</table>
			</td>	
			</tr>
			<tr>
			<td colspan="2" style="text-align: right">
			</td></tr>
			<tr>
			<td colspan="2" style="text-align: left">
			<span title="Add More" style="cursor:pointer;" class="addanother" onclick="selMethod('.$sId.');">Save</span>
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
			
			/************* Start for auto populate the data in confirmbox *************/
			$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
			$tableNameBox = Mage::getSingleton('core/resource')->getTableName('partialshipping_confirmbox');
			
			$connectionWrite->beginTransaction();
			$condition = array($connectionWrite->quoteInto('product_id=?', $confirmproduct[$k]));
			$connectionWrite->delete($tableNameBox, $condition);
			$connectionWrite->commit();
		
			if($confirmwidth[$k] != '' or $confirmlength[$k] != '' or $confirmheight[$k] != '' or $confirmweight[$k] != '')
			{
				$connectionWrite->beginTransaction();
				$data1 = array();
				$data1['product_id']= $confirmproduct[$k];
				$data1['lenght']= $confirmlength[$k];
				$data1['width']= $confirmwidth[$k];
				$data1['height']= $confirmheight[$k];
				$data1['weight']= $confirmweight[$k];
				
				$connectionWrite->insert($tableNameBox, $data1);
				$connectionWrite->commit();
			}
			
			/************** End for auto populate the data in confirmbox **************/
			
			
			
		}
		if(is_numeric($confirmtotalweight)) $confirmtotalweight=$confirmtotalweight; else { $errN++;}
		if(is_numeric($confirmtotalvolum)) $confirmtotalvolum=$confirmtotalvolum; else { $errN++;}
		
		$str="box:".$box."__qty:".$qty."__len:".$lengh."__hght:".$hght."__width:".$width."__title:".$title."__totwght:".$confirmtotalweight."__totvol:".$confirmtotalvolum;
		
		if( $qty > $confirmtotqty){
			
			echo "qtyerror";
		}else{
			if($errN > 0){
			
			echo "numerror";
			
			}elseif( $errC > 0){
				
				echo "charerror";
			}else{
				echo $str.'####'.$str1.'####'.'Product measurement has been saved.';
			}
		}
		
		
		
	}
	////modified on 13-2-2014
	public function getauspostmethodolAction(){
		
		extract($_REQUEST);
		//print_r($_REQUEST);exit;
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
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
		$tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		
		$select = $connectionRead->select()->from($tableName, array('*'))->where('shipment_grid_id=?',$shipId);
		$row = $connectionRead->fetchAll($select);
		$tWeight = 0;
		foreach($row as $res){
			$select1 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$res['order_item_id']);
			$row1 = $connectionRead->fetchRow($select1);
			$pId = $row1['product_id'];
			$_product = Mage::getModel('catalog/product')->load($pId);
			$weight = $_product->getWeight();
			$tWeight +=$weight;
			//$qtny += $res['qty'];
			
		}
		$fromPostCode = '3207';
		$result = Mage::getModel('shipping/rate_result');
		$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId());
		$ret=array();
		$res='';
		$weight = $tWeight;
		$height = $width = $length = 10;
		$shipping_num_boxes = 1;
		$allowedShippingMethods = explode(',', Mage::getStoreConfig('carriers/australiapost/shipping_methods'));
		if($weight <= 20 ){
		if ($destCountry == "AU") {
			$shipping_methods = array('STANDARD', 'EXPRESS');
			
			
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
						'timeout'   => 35    //Timeout in no of seconds
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
							
							////17-2-2014 GC S
							$charge = $result['charge'];
							$margin = Mage::getStoreConfig('carriers/australiapost/profit_margin')/100;
							$charge = $charge + ($charge*$margin);
							////17-2-2014 GC E
							
							$strVal="Australiapost ".$shipping_method." ".$result['days']."__".number_format($charge,2);
							$strLabel=$shipping_method." ".$result['days']." Days $".number_format($charge,2);
						} else {
							return array('err_msg' => 'Parsing error on Australia Post results');
						}
					}
					array_push($ret,$result);
					$res.='<input name="shipping_method_aus" type="radio" value="'.$strVal.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$strLabel.'</br>';
				
				
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
						////17-2-2014 GC S
						$charge = $result['charge'];
						$margin = Mage::getStoreConfig('carriers/australiapost/profit_margin')/100;
						$charge = $charge + ($charge*$margin);
						////17-2-2014 GC E
						
						$strVal="Australiapost ".$shipping_method." ".$result['days']."__".number_format($charge,2);
						$strLabel="Australiapost ".$shipping_method." $".number_format($charge,2);
						
						//$strVal="Australiapost ".$shipping_method." ".$result['days']."__".$result['charge'];
						//$strLabel="Australiapost ".$shipping_method." $".$result['charge'];
					} else {
						return array('err_msg' => 'Parsing error on Australia Post results');
					}
				}
				array_push($ret,$result);
				$res.='<input name="shipping_method_aus" type="radio" value="'.$strVal.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$strLabel.'</br>';
			
			}
			
		}
		
		}else{
			
			echo "<strong>Weight exceeds maximum allowed weight (20kgs)</strong>";
		}
		
		
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
	
	///////modified on 13-2-2014
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
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
	$tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
	
	$select = $connectionRead->select()->from($tableName, array('*'))->where('shipment_grid_id=?',$shipId);
	$row = $connectionRead->fetchAll($select);
	$tWeight = 0;
	foreach($row as $res){
		$select1 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$res['order_item_id']);
		$row1 = $connectionRead->fetchRow($select1);
		$pId = $row1['product_id'];
		$_product = Mage::getModel('catalog/product')->load($pId);
		$weight = $_product->getWeight();
		$tWeight +=$weight;
		
	}
	$fromPostCode = '3207';
	$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId());
	
	$ret=array();
	$res='';
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
					<state>'.trim($region).'</state>
				  </deliveryAddress>
				  <shippingDate>'.trim($date).'</shippingDate>
				  <userCurrentLocalDateTime>
					2007-11-05T10:00:00
				  </userCurrentLocalDateTime>
				  <dangerousGoods>
					<dangerous>false</dangerous>
				  </dangerousGoods>
				  <packageLines packageType="D">';
				  
				  
				  
			foreach($row as $item){
				
				$select2 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$item['order_item_id']);
				$row2 = $connectionRead->fetchRow($select2);
				$pId1 = $row2['product_id'];
				$_product = Mage::getModel('catalog/product')->load($pId1);
				
				if($item['qty']) $qty = $item['qty']; else $qty = 1;
				if($_product->getWeight()) $weight = round($_product->getWeight()); else $weight = 10;
				if($_product->getLength()) $length = round($_product->getLength()); else $length = 10;
				if($_product->getWidth())  $width = round($_product->getWidth()); else $width = 10;
				if($_product->getHeight()) $height = round($_product->getHeight()); else $height = 10;
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
						///17-2-2014 GC S
						$charge = $ratedProduct['quote']['price'];
						$margin = Mage::getStoreConfig('carriers/excellence/profit_margin')/100;
						$charge = $charge + ($charge*$margin);
						
						///17-2-2014 GC E
						
						$rate="$".number_format($charge,2);
					}else{
						$rate=0;
					}
					
					
					$val = "TNT ".$mt."__".number_format($charge,2);
					$mt1=$mt." ".$rate;
					$res .= '<input name="shipping_method_tntl" type="radio" value="'.$val.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$mt1.'</br>';
					}
				}
			}
			
			echo $res;
			
	}else{
		
		echo "Selected address country is other than Australia. Select Tnt International";
	}
		
	}
	
	////27-12-2013 //21-1-2014 ////modified on 14-2-2014
	public function gettntlocalmethodAction(){
		
		extract($_REQUEST);
		//print_r($_REQUEST);exit;
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
		
		//$tWeight=$order->getWeight();
		$fromPostCode = '3207';
		$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $order->getStoreId());
		
		$ret=array();
		$res='';
		$arrc=explode("@@@@",$unit);
		$l=0;
		
		$shipping_num_boxes = 1;
		//$allItems = $order->getAllItems();
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
						<state>'.trim($region).'</state>
					  </deliveryAddress>
					  <shippingDate>'.trim($date).'</shippingDate>
					  <userCurrentLocalDateTime>
						'.trim(str_replace(" ","T",$shipDate)).'
					  </userCurrentLocalDateTime>
					  <dangerousGoods>
						<dangerous>false</dangerous>
					  </dangerousGoods>
					  <packageLines packageType="D">';
				
				foreach($arrc as $a){
					$l++;
					
					if($l > 1){
						
						$boxArr=explode("__",$a);
						
						$boxPos =stripos($boxArr[0], ":");
						$boxP = $boxPos+1;
						$box=substr($boxArr[0],$boxP);
						
						$qtyPos =stripos($boxArr[1], ":");
						$qtyP = $qtyPos+1;
						$qty=substr($boxArr[1],$qtyP);
						
						$lenPos =stripos($boxArr[2], ":");
						$lenP = $lenPos+1;
						$length=substr($boxArr[2],$lenP);
						
						$heighPos =stripos($boxArr[3], ":");
						$heighP = $heighPos+1;
						$height=substr($boxArr[3],$heighP);
						
						$widPos =stripos($boxArr[4], ":");
						$widP = $widPos+1;
						$width=substr($boxArr[4],$widP);
						
						$weiPos =stripos($boxArr[5], ":");
						$weiP = $weiPos+1;
						$weight=substr($boxArr[5],$weiP);
						
							$requestXml .=
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
				//echo "<pre>";print_r($array); exit;
				if($array['ratedTransitTimeResponse']['brokenRules'] && !$array['ratedTransitTimeResponse']['ratedProducts']){
					
					$res= $array['ratedTransitTimeResponse']['brokenRules']['brokenRule']['description'];
					
				}else{
					
					$ratedArray = $array['ratedTransitTimeResponse']['ratedProducts']['ratedProduct'];
					//3-3-2014 S
					$mth = $ratedArray['product']['description'];
					$chge = $ratedArray['quote']['price'];
					$margin = Mage::getStoreConfig('carriers/excellence/profit_margin')/100;
					$chge = $chge + ($chge*$margin);
					
					
					
					if($ratedArray['quote']['price']){
						$rte="$".number_format($chge,2);
					}else{
						$rte=0;
					}
					$valh = "TNT ".$mth."__".number_format($chge,2);
						$mth1=$mth." ".$rte;
					
					
					$res .= '<input name="shipping_method_tntl" type="radio" value="'.$valh.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$mth1.'</br>';
					
					//3-3-2014 S
					foreach($ratedArray as $key=>$ratedProduct){
						if(is_numeric($key)){
						$mt = $ratedProduct['product']['description'];
						
						///17-2-2014 GC S
						$charge = $ratedProduct['quote']['price'];
						$margin = Mage::getStoreConfig('carriers/excellence/profit_margin')/100;
						$charge = $charge + ($charge*$margin);
						
						///17-2-2014 GC E
						if($ratedProduct['quote']['price']){
							$rate="$".number_format($charge,2);
						}else{
							$rate=0;
						}
						
						
						$val = "TNT ".$mt."__".number_format($charge,2);
						$mt1=$mt." ".$rate;
						$res .= '<input name="shipping_method_tntl" type="radio" value="'.$val.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$mt1.'</br>';
						}
					}
				}
				
				echo $res;
				
		}else{
			
			echo "Selected address country is other than Australia. Select Tnt International";
		}
	}
	
	
	////24-12-2013 EOC
	///26-12-2013
	// modified on 13-2-2014
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
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
		$tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		
		$select = $connectionRead->select()->from($tableName, array('*'))->where('shipment_grid_id=?',$shipId);
		$row = $connectionRead->fetchAll($select);
		$tWeight = 0;
		foreach($row as $res){
			$select1 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$res['order_item_id']);
			$row1 = $connectionRead->fetchRow($select1);
			$pId = $row1['product_id'];
			$_product = Mage::getModel('catalog/product')->load($pId);
			$weight = $_product->getWeight();
			$tWeight +=$weight;
			
		}
		
		$qty = $weight = $height = $width = $length = 0;
		$date = '2014-10-10';
		$weight = $tWeight;
		foreach($row as $item){
			$select2 = $connectionRead->select()->from($tableName1, array('product_id'))->where('item_id =?',$item['order_item_id']);
			$row2 = $connectionRead->fetchRow($select2);
			$pId1 = $row2['product_id'];
			$_product = Mage::getModel('catalog/product')->load($pId1);
			
			if($item['qty']) $qty = $item['qty']; else $qty = 1;
			if($_product->getWeight()) $weight = round($_product->getWeight()); else $weight = 10;
			if($_product->getLength()) $length = round($_product->getLength()); else $length = 10;
			if($_product->getWidth())  $width = round($_product->getWidth()); else $width = 10;
			if($_product->getHeight()) $height = round($_product->getHeight()); else $height = 10;
		
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
		    <WEIGHT>'.trim($tWeight).'</WEIGHT>
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
					///17-2-2014 GC S
					$charge = $array['PRICE'][$key]['RATE'];
					$margin = Mage::getStoreConfig('carriers/excellence/profit_margin')/100;
					$charge = $charge + ($charge*$margin);
					
					///17-2-2014 GC E
					
					$rate="$".number_format($charge,2);
				}else{
					$rate=0;
				}	
				$mt = $array['PRICE'][$key]['SERVICE']." ".$array['PRICE'][$key]['SERVICEDESC']." ".$rate;
				$val= $array['PRICE'][$key]['SERVICE']." ".$array['PRICE'][$key]['SERVICEDESC']."__".number_format($charge,2);
				$res .= '<input name="shipping_method_tntl" type="radio" value="'.$val.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$mt.'</br>';
			}		
		}
		echo $res;
			
		}
		else{
			echo "Selected address country is Australia. Select Tnt Australia";
			
		}
	}
	
	////27-12-2013 ////modified on 14-2-2014
	public function gettntintlmethodAction(){
		extract($_REQUEST);
		$arrc=explode("@@@@",$unit);
		$l=0;
		$order = Mage::getModel('sales/order')->load($ordId);
		$tWeight=$order->getWeight();
		$qty = $weight = $height = $width = $length = 0;
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
		
		
		
		
		$date = date('Y-m-d');
		//$date = '2014-10-10';
		
		
		
		$qty = $qtyArr[1];
		
		$qty = $weight = $height = $width = $length = 0;
		foreach($arrc as $a){
			$l++;
			$arrc1 = explode("__",$a);					
			if($l > 1){
				
			$boxArr=explode("__",$a);
						
			$boxPos =stripos($boxArr[0], ":");
			$boxP = $boxPos+1;
			$box= $box + substr($boxArr[0],$boxP);
			
			$qtyPos =stripos($boxArr[1], ":");
			$qtyP = $qtyPos+1;
			$qty=$qty + substr($boxArr[1],$qtyP);
			
			$lenPos =stripos($boxArr[2], ":");
			$lenP = $lenPos+1;
			$length=$length + substr($boxArr[2],$lenP);
			
			$heighPos =stripos($boxArr[3], ":");
			$heighP = $heighPos+1;
			$height=$height + substr($boxArr[3],$heighP);
			
			$widPos =stripos($boxArr[4], ":");
			$widP = $widPos+1;
			$width= $width + substr($boxArr[4],$widP);
			
			$weiPos =stripos($boxArr[5], ":");
			$weiP = $weiPos+1;
			$weight=$weight + substr($boxArr[5],$weiP);
			
			
			//foreach($arrc1 as $a1){
			//	$boxArr=explode(":",$a1);
			//	if($boxArr[0]== 'qty') $qty = $qty + $boxArr[1];
			//	if($boxArr[0]== 'box') $box = $box + $boxArr[1]; 
			//	if($boxArr[0]== 'len') $length = $length + $boxArr[1]; 
			//	if($boxArr[0]== 'hght')  $height = $height + $boxArr[1]; 
			//	if($boxArr[0]== 'width')  $width = $width + $boxArr[1]; 
			//	if($boxArr[0]== 'weight')  $weight = $weight + $boxArr[1]; 
			//	
			//	echo $qty; echo "<br>";
			//	
			//}
			
			}
				
		}
		//exit;
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
					///17-2-2014 GC S
					$charge = $array['PRICE'][$key]['RATE'];
					$margin = Mage::getStoreConfig('carriers/excellence/profit_margin')/100;
					$charge = $charge + ($charge*$margin);
					
					///17-2-2014 GC E
					$rate="$".number_format($charge,2);
				}else{
					$rate=0;
				}	
				$mt = "TNT ".$array['PRICE'][$key]['SERVICE']." ".$array['PRICE'][$key]['SERVICEDESC']." ".$rate;
				$val= "TNT ".$array['PRICE'][$key]['SERVICE']." ".$array['PRICE'][$key]['SERVICEDESC']."__".number_format($charge,2);
				$res .= '<input name="shipping_method_tntl" type="radio" value="'.$val.'" id="" class="radio" onclick="slmthd(this.value,'.$shipId.');"/>  '.$mt.'</br>';
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
	///////11-2-2014 GC S
	public function partshipformnewAction(){
		extract($_REQUEST);
		$order = Mage::getModel('sales/order')->load($orderIdP);
		$totalqtyOrdered=round($order->getTotalQtyOrdered());
		$qtyShipped=0;
		$tWeight=0;
		$flg=0;
		
		foreach($_REQUEST['shipment'] as $key=>$val){
			 
			foreach($val as $k=>$qty){
				
				$qtyord=$qtyorderd[$key][$k];
				
				$_Product = Mage::getModel('catalog/product')->load($k);
				if($_Product) $pWeight=$_Product->getWeight()*$qty;
				
				if($qty > $qtyord ){
					$flg++;
					
				}else{
					$qtyShipped=$qtyShipped+$qty;
					$tWeight = $tWeight+$pWeight;
				}
				
			}
			
		}
		if($flg > 0){
			
			$msg='Shipped quantity is greater than total quantity ordered.';
			$this->_getSession()->addError($msg);
			
			$url = Mage::helper('adminhtml')->getUrl("adminhtml/sales_shipment/view/shipment_id/".$shipmentId);
			Mage::log($url); 
       			Mage::app()->getResponse()->setRedirect($url);
			
		}else{
			$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
			$tableName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
			$tableName1 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment');
			$tableName2 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
			$tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
			
			$select = $connectionRead->select()->from($tableName2, array('*'))->where('order_id=?',$orderIdP)->order('entity_id DESC')->limit(1);
			$row = $connectionRead->fetchRow($select);
			if(count($row) > 0){
				$shipmenIncrmnttId=$row['increment_id'];
				$pos1 = strpos($shipmenIncrmnttId, '-');
			
				if($pos1 != false) {
					$sincr=explode('-',$shipmenIncrmnttId);
					$a=$sincr[1]+1;
					$shipmenIncrmnttId=$sincr[0]."-".$a;
				}
				else {
					$shipmenIncrmnttId=$shipmenIncrmnttId."-1";
				}
			}
			
			$t=time();
			$created_at = date("Y-m-d H:i:s", $t);
			$date_post = strtotime($order->getCreatedAtDate()); 
			$Ordtime=date('Y-m-d H:i:s',$date_post );
			
			if($qtyShipped > 0){
				/////////////////Insert into partialshipping_shipment_grid ////////////////////////
				$connectionWrite->beginTransaction();
				$data = array();
				if($order->getStoreId()) $data['store_id']= $order->getStoreId();
				if($qtyShipped) $data['total_qty']=$qtyShipped;
				if($orderIdP) $data['order_id']=$orderIdP;
				
				if($qtyShipped < $totalqtyOrdered) $data['status']='Partially Shipped';
				elseif($qtyShipped == $totalqtyOrdered) $data['status']='Shipped';
				else $data['status']='Pending Shipment';
				
				if($shipmenIncrmnttId) $data['increment_id']=$shipmenIncrmnttId;
				if($orderIncrementId) $data['order_increment_id']=$orderIncrementId;
				if($created_at) $data['created_at']=$created_at;
				if($order->getCreatedAtDate()) $data['order_created_at']=$order->getCreatedAtDate();
				
				
				$connectionWrite->insert($tableName2, $data);
				$connectionWrite->commit();  
				$lastId = $connectionWrite->fetchOne('SELECT last_insert_id()');
				/////////////////Insert into partialshipping_shipment ////////////////////////
				$connectionWrite->beginTransaction();
				$data3=array();
				if($order->getStoreId()) $data3['store_id']= $order->getStoreId();
				if($order->getweight()) $data3['total_weight']= $tWeight;
				if($qtyShipped) $data3['total_qty']= $qtyShipped;
				if($orderIdP) $data3['order_id']= $orderIdP;
				if($customerid) $data3['customer_id']= $customerid;
				
				if($shipmenIncrmnttId) $data3['increment_id']= $shipmenIncrmnttId;
				if($created_at) $data3['created_at']= $created_at;
				if($created_at) $data3['updated_at']= $created_at;
				
				$connectionWrite->insert($tableName1, $data3);
				$connectionWrite->commit();
				
				/////////////////Insert into partialshipping_shipment_item ////////////////////////
				foreach($_REQUEST['shipment'] as $key=>$val){
				
							
				foreach($val as $k=>$qty){
					if( $qty > 0){
					$connectionWrite->beginTransaction();
					$_Product = Mage::getModel('catalog/product')->load($k);
					
					$price=$_Product->getPrice();
					$data1 = array();
					if($shipmentId) $data1['parent_id']= $shipmentId;
					if($lastId) $data1['shipment_grid_id']= $lastId;
					
					$tPrice=$price*$qty;
					if($tPrice) $data1['price']= ($tPrice);
					if($_Product->getWeight()) $data1['weight']= $_Product->getWeight();
					if($qty) $data1['qty']=$qty;
					if($k) $data1['product_id']=$k;
					if($key) $data1['order_item_id']=$key;
					if($_Product->getDescription()) $data1['description']=$_Product->getDescription();
					if($_Product->getName()) $data1['name']=$_Product->getName();
					if($_Product->getSku()) $data1['sku']=$_Product->getSku();
					
					$connectionWrite->insert($tableName3, $data1);
					$connectionWrite->commit();
					}
				}
			
				}
				
				/////4-3-2014 S
				$selectD = $connectionRead->select()->from($tableName2, array('*'))->where('order_id=?',$orderIdP)->order('entity_id ASC')->limit(1);
				$rowD = $connectionRead->fetchRow($selectD);
				$totalOrdered = $rowD['total_qty'];
				
				$select1D = $connectionRead->select()->from($tableName2, array('*'))->where("order_id = '".$orderIdP."' AND increment_id != '".$rowD['increment_id']."'")->order('entity_id');
				$row1D = $connectionRead->fetchAll($select1D);
				$totalShp = 0;
				
				foreach( $row1D as $rws){
					$totalShp += $rws['total_qty'];
				}
				
				//echo "TOT : ".$totalOrdered;
				//echo "<br>";
				//echo "TOT : ".$totalShp;
				//exit;
				if($totalOrdered == $totalShp){
					$connectionWrite->beginTransaction();
					$data=array();
					
					$data['allshipped']=1;
					$where = $connectionWrite->quoteInto("order_id = '".$orderIdP."' AND entity_id= '".$rowD['entity_id']."'");					
					$connectionWrite->update($tableName2, $data, $where);
					$connectionWrite->commit();
				}
				/////4-3-2014 E
				
				$msg='Shipment Created Successfully.';
				$this->_getSession()->addSuccess($msg);
				$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
			
			}else{
				$msg='Please enter proper value for shipment';
				$this->_getSession()->addError($msg);
				$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));	
			}
		}
	}
	
	///////11-2-2014 GC E 
		
}