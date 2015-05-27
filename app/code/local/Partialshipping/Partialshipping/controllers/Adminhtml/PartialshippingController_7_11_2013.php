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
			//if(!$SelACodeRes['oid'] || !$SelACodeRes['customerid']){
				$InsertCustomerAddressEntityVarchar = "INSERT INTO $customer_address_entity_varchar (value_id, entity_type_id, attribute_id, entity_id, value)
VALUES ('','".$EntityId."', '".$SelACodeRes['attribute_id']."','".$EntitiSecondId."','".$value."')";
				$WriteConn->query($InsertCustomerAddressEntityVarchar);
			//}
		}
	}
	echo "New address Successfully saved";
	//exit;
        //Mage::app()->getResponse()->setRedirect($url);

	}
}