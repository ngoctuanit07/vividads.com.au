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
		//echo "<pre>";print_r($_REQUEST);exit;
	
		
		//$data = $this->getRequest()->getPost('shipment');
		$shipAd=$_REQUEST['sel_adr'];
		$shipMd=$_REQUEST['sel_metd'];
		$ordId=$_REQUEST['orderId'];
		$ordincrId=$_REQUEST['orderIncrementId'];
		$custmerId=$_REQUEST['customerId'];
		$shM=explode("__",$shipMd);
		$shMethod=$shM[0];
		$shPrice=$shM[1];
		
		
		$order = Mage::getModel('sales/order')->load($orderId);
		$shpAmt=$order->getShippingAmount();
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$tableName2 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		
		$select2 = $connectionRead->select()->from($tableName2, array('*'))->where('entity_id=?',$orderId);
		$row2 = $connectionRead->fetchRow($select2);
		$baseTotalDue=$row2['base_total_due'];
		$TotalDue=$row2['total_due'];
		$baseGrandTotal=$row2['base_grand_total'];
		$GrandTotal=$row2['grand_total'];
		
		//echo "old : ".$baseTotalDue; echo "<br>";
		//echo "old : ".$TotalDue; echo "<br>";
		
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
		
		if($baseTotalDueN != $baseTotalDue){
		    
		    $connectionWrite->beginTransaction();
		    $data = array();
		    $data['base_total_due'] = $baseTotalDueN;
		    $data['total_due'] = $TotalDueN;
		    $data['base_grand_total'] = $baseGrandTotalN;
		    $data['grand_total'] = $GrandTotalN;
		    
		    $where = $connectionWrite->quoteInto('entity_id =?', $ordId);
		    $connectionWrite->update($tableName2, $data, $where);
		    $connectionWrite->commit();
		    
		}
		
		
		if($shipAd !='' || $shipMd !='' ){
			
			
		   $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		   $tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		   
		   $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		   $tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_details');
		   
		   
		   
		   foreach($_REQUEST['shipment'] as $key=>$qty){
			//echo "QTY : ".$qty;echo "<br>";
			
			if($qty != 0){
			    
			    $select = $connectionRead->select()->from($tableName1, array('*'))->where('item_id=?',$key);
			    $row = $connectionRead->fetchRow($select);
			    
			    $connectionWrite->beginTransaction();
			    $data = array();
			    $data['order_id']= $ordId;
			    $data['order_increment_id']=$ordincrId;
			    $data['shipping_address_id']=$shipAd;
			    $data['shipping_method']=$shMethod;
			    $data['shiping_price']=$shPrice;
			    $data['product_id']=$row['product_id'];
			    $data['product_qty']=$qty;
			    $data['customer_id']=$custmerId;
			    $connectionWrite->insert($tableName, $data);
			    $connectionWrite->commit();
			}
		    
		   }
		   
		}
		$this->_redirect('*/sales_shipment/view', array('shipment_id' => $shipmentId));
	}
	//16-11-2013 EOC
}