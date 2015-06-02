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
		//echo "<pre>";print_r($_REQUEST); exit;
		
		$order = Mage::getModel('sales/order')->load($orderIdP);
		$totalqtyOrdered=round($order->getTotalQtyOrdered());
		$qtyShipped=0;
		foreach($_REQUEST['shipment'] as $key=>$val){
				
			foreach($val as $k=>$qty){
				$qtyShipped=$qtyShipped+$qty;	
			}
			
		}
		
		if( $qtyShipped < $totalqtyOrdered || $qtyShipped != 0){
			//$data = $this->getRequest()->getPost('shipment');
			$shipAd=$_REQUEST['sel_adr'];
			$shipMd=$_REQUEST['sel_metd'];
			$ordId=$_REQUEST['orderIdP'];
			$ordincrId=$_REQUEST['orderIncrementId'];
			$custmerId=$_REQUEST['customerId'];
			$shM=explode("__",$shipMd);
			$shMethod=$shM[0];
			$shPrice=$shM[1];
			
			$shpAmt=$order->getShippingAmount();
			$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
			$tableName2 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
			
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
		
			//if($baseTotalDueN != $baseTotalDue){
			//    
			//    $connectionWrite->beginTransaction();
			//    $data = array();
			//    $data['base_total_due'] = $baseTotalDueN;
			//    $data['total_due'] = $TotalDueN;
			//    $data['base_grand_total'] = $baseGrandTotalN;
			//    $data['grand_total'] = $GrandTotalN;
			//    
			//    $where = $connectionWrite->quoteInto('entity_id =?', $orderIdP);
			//    $connectionWrite->update($tableName2, $data, $where);
			//    $connectionWrite->commit();
			//    
			//}
			
			//echo "A :".$shipAd; echo "<br>";
			//echo "B :".$shipMd; exit;
			if($shipAd !='' && $shipMd !='' ){
				$tableName4 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment');
				$tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
				//$tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
				$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
				$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
				$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
				
				//$created_at = date("Y-m-d h:m:s");
				$t=time();
				//$created_at = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
				
				$created_at = date("Y-m-d H:i:s", $t);
				$date_post = strtotime($order->getCreatedAtDate()); 
				$Ordtime=date('Y-m-d H:i:s',$date_post );
				$addressLoadId = Mage::getModel('customer/address')->load($sel_adr);
				$Name = $addressLoadId['firstname'].' '.$addressLoadId['lastname'];
				
				$connectionWrite->beginTransaction();
				$data = array();
				//echo "store id : ".$order->getId(); exit;
				
				if($order->getStoreId()) $data['store_id']= $order->getStoreId();
				if($qtyShipped) $data['total_qty']=$qtyShipped;
				if($orderIdP) $data['order_id']=$orderIdP;
				
				if($qtyShipped < $totalqtyOrdered) $data['status']='Partially Shipped';
				elseif($qtyShipped == $totalqtyOrdered) $data['status']='Shipped';
				else $data['status']='Pending Shipment';
				
				//$data['ship_id']=$shPrice;
				$select = $connectionRead->select()->from($tableName3, array('*'))->where('order_id=?',$orderIdP)->order('entity_id DESC')->limit(1);
				$row = $connectionRead->fetchRow($select);
				//echo "<pre>";print_r($row);
				//exit;
				
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
				
				//echo $shipmenIncrmnttId;
				//exit;
				if($shipmenIncrmnttId) $data['increment_id']=$shipmenIncrmnttId;
				if($orderIncrementId) $data['order_increment_id']=$orderIncrementId;
				if($created_at) $data['created_at']=$created_at;
				if($created_at) $data['order_created_at']=$Ordtime;
				if($Name) $data['shipping_name']=$Name;
				
				$connectionWrite->insert($tableName3, $data);
				$connectionWrite->commit();  
				
				
				
				foreach($_REQUEST['shipment'] as $key=>$val){
					
					foreach($val as $k=>$qty){
						//echo "PRC: ".$k;
						$connectionWrite->beginTransaction();
						$_Product = Mage::getModel('catalog/product')->load($k);
						
						//echo "<pre>";print_r($_Product);
						$price=$_Product->getPrice();
						$data1 = array();
						if($shipmentId) $data1['parent_id']= $shipmentId;
						$tPrice=$price*$qty;
						if($tPrice) $data1['price']= ($tPrice);
						if($_Product->getWeight()) $data1['weight']= $_Product->getWeight();
						if($qty) $data1['qty']=$qty;
						if($k) $data1['product_id']=$k;
						if($key) $data1['order_item_id']=$key;
						if($_Product->getDescription()) $data1['description']=$_Product->getDescription();
						if($_Product->getName()) $data1['name']=$_Product->getName();
						if($_Product->getSku()) $data1['sku']=$_Product->getSku();
						
						//echo $data1['weight']= $_Product->getWeight(); exit;
						//$data1['qty']=$qty;
						//$data1['product_id']=$k;
						//$data1['order_item_id']=$key;
						//$data1['description']=$_Product->getDescription();
						//$data1['name']=$_Product->getName();
						//$data1['sku']=$_Product->getSku();
						
						$connectionWrite->insert($tableName, $data1);
						$connectionWrite->commit();
					}
				
				}
				
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
				
				$connectionWrite->insert($tableName4, $data3);
				$connectionWrite->commit();
				
				
				///////////////// 26-11-2013 SOC
				$msg='Shipment Created Successfully.';
				$this->_getSession()->addSuccess($msg);
				$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
			
			}else{
				$msg='Please select a shipping address and and shipping method.';
				$this->_getSession()->addError($msg);
				$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
				
			}
			
		}else {
			
			$msg='Shipped quantity is greater than total quantity ordered';
			$this->_getSession()->addError($msg);
			$this->_redirect('adminhtml/sales_shipment/view', array('shipment_id' => $shipmentId));
		}
		
	}
	//16-11-2013 EOC
	
	///29-11-2013
	public function deletepartshipAction(){
		
		$partShipID=$this->getRequest()->getParam('shipId'); //exit;
		
		$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment');
		$tableName1 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
		$tableName2 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_item');
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
			
		$select = $connectionRead->select()->from($tableName1, array('*'))->where('entity_id=?',$partShipID);
		$row = $connectionRead->fetchRow($select);
		
		$pos1 = strpos($row['increment_id'], '-');	
		if($pos1 != false) {
			$sincr=explode('-',$row['increment_id']);
			$a=$sincr[0];
			
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
	
	////2-12-2013 SOC
	public function getauspostmethodAction(){
		extract($_REQUEST);
		$addressLoadId = Mage::getModel('customer/address')->load($addrsid);
		$destCountry=$addressLoadId['country_id'];
		//$toPostCode=$addressLoadId['postcode'];
		$toPostCode='3207';
		$order = Mage::getModel('sales/order')->load($ordId);
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
							$strVal="Australiapost__".$shipping_method."__".$result['charge']."__".$result['days'];
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
						$strVal="Australiapost__".$shipping_method."__".$result['charge']."__".$result['days'];
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
	////2-12-2013 EOC
	
}