<?php

class Artis_Timeline_Adminhtml_TimelineController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('timeline/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('timeline/timeline')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('timeline_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('timeline/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('timeline/adminhtml_timeline_edit'))
				->_addLeft($this->getLayout()->createBlock('timeline/adminhtml_timeline_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('timeline')->__('Item does not exist'));
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
	  			
	  			
			$model = Mage::getModel('timeline/timeline');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('timeline')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('timeline')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('timeline/timeline');
				 
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
        $timelineIds = $this->getRequest()->getParam('timeline');
        if(!is_array($timelineIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($timelineIds as $timelineId) {
                    $timeline = Mage::getModel('timeline/timeline')->load($timelineId);
                    $timeline->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($timelineIds)
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
        $timelineIds = $this->getRequest()->getParam('timeline');
        if(!is_array($timelineIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($timelineIds as $timelineId) {
                    $timeline = Mage::getSingleton('timeline/timeline')
                        ->load($timelineId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($timelineIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'timeline.csv';
        $content    = $this->getLayout()->createBlock('timeline/adminhtml_timeline_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'timeline.xml';
        $content    = $this->getLayout()->createBlock('timeline/adminhtml_timeline_grid')
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
    
    public function savetimeAction()
    {
	//print_r($_REQUEST);//exit;
	extract($_REQUEST);
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
	//$sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '0' ";
	$sqlTimeline = $connectionRead->select()
                                ->from($temptableTimeline, array('*'))
                                ->where('product_id=?',0); 
	$chkTimeline = $connectionRead->fetchAll($sqlTimeline);
	
	if(count($chkTimeline) > 0)
	{
		//$sqlTimeline="UPDATE ".$temptableTimeline." SET artwork_day = '".$artwork."', proof_day = '".$proof."', production_day = '".$production."', delivary_day = '".$delivary."', shipping_day = '".$shipping."', sunday_artwork = '".$sunday_artwork."', holiday_artwork = '".$holiday_artwork."', sunday_proof = '".$sunday_proof."', holiday_proof = '".$holiday_proof."', sunday_production = '".$sunday_production."', holiday_production = '".$holiday_production."', sunday_delivary = '".$sunday_delivary."', holiday_delivary = '".$holiday_delivary."', sunday_shipping = '".$sunday_shipping."', holiday_shipping = '".$holiday_shipping."' WHERE product_id = '0' ";
		//$chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTimeline);
		
		$connectionWrite->beginTransaction();
                $data = array();
		$data['artwork_day'] = $artwork;
                $data['proof_day'] = $proof;
                $data['production_day'] = $production;
		$data['delivary_day'] = $delivary;
		$data['shipping_day'] = $shipping;
		$data['sunday_artwork'] = $sunday_artwork;
		$data['holiday_artwork'] = $holiday_artwork;
		$data['sunday_proof'] = $sunday_proof;
		$data['holiday_proof'] = $holiday_proof;
		$data['sunday_production'] = $sunday_production;
		$data['holiday_production'] = $holiday_production;
		$data['sunday_delivary'] = $sunday_delivary;
		$data['holiday_delivary'] = $holiday_delivary;
		$data['sunday_shipping'] = $sunday_shipping;
		$data['holiday_shipping'] = $holiday_shipping;
                $where = $connectionWrite->quoteInto('product_id =?', 0);
                $connectionWrite->update($temptableTimeline, $data, $where);
                $connectionWrite->commit();
	
	}
	else{
		//$sqlTimeline="INSERT INTO ".$temptableTimeline." SET artwork_day = '".$artwork."', proof_day = '".$proof."', production_day = '".$production."', delivary_day = '".$delivary."', shipping_day = '".$shipping."', sunday_artwork = '".$sunday_artwork."', holiday_artwork = '".$holiday_artwork."', sunday_proof = '".$sunday_proof."', holiday_proof = '".$holiday_proof."', sunday_production = '".$sunday_production."', holiday_production = '".$holiday_production."', sunday_delivary = '".$sunday_delivary."', holiday_delivary = '".$holiday_delivary."', sunday_shipping = '".$sunday_shipping."', holiday_shipping = '".$holiday_shipping."' , product_id = '0' ";
		//$chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTimeline);
		
		$connectionWrite->beginTransaction();
                $data = array();
		if($artwork !='' && $proof !='' && $production !='' && $delivary !='' && $shipping !='' && $sunday_artwork !='' && $holiday_artwork !=''
&& $sunday_proof !='' && $holiday_proof !='' && $sunday_production !='' && $holiday_production !='' && $sunday_delivary !='' && $holiday_delivary !=''
&& $sunday_shipping !='' && $holiday_shipping !='' && $holiday_delivary !=''){
			$data['artwork_day'] = $artwork;
			$data['proof_day'] = $proof;
			$data['production_day'] = $production;
			$data['delivary_day'] = $delivary;
			$data['shipping_day'] = $shipping;
			$data['sunday_artwork'] = $sunday_artwork;
			$data['holiday_artwork'] = $holiday_artwork;
			$data['sunday_proof'] = $sunday_proof;
			$data['holiday_proof'] = $holiday_proof;
			$data['sunday_production'] = $sunday_production;
			$data['holiday_production'] = $holiday_production;
			$data['sunday_delivary'] = $sunday_delivary;
			$data['holiday_delivary'] = $holiday_delivary;
			$data['sunday_shipping'] = $sunday_shipping;
			$data['holiday_shipping'] = $holiday_shipping;
			$data['product_id'] = 0;
			$connectionWrite->insert($temptableTimeline, $data);
			$connectionWrite->commit();
		}
	
	}
	
	$products = Mage::getModel('catalog/product')->getCollection();
	//Magento does not load all attributes by default
	//Add as many as you like
	foreach($products as $product) {
	//do something
	
		//$sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$product->getId()."' ";
		$sqlTimeline = $connectionRead->select()
                                ->from($temptableTimeline, array('*'))
                                ->where('product_id=?',$product->getId());   
		$chkTimeline = $connectionRead->fetchAll($sqlTimeline);
		
		if(count($chkTimeline) > 0)
		{
			//$sqlTimeline="UPDATE ".$temptableTimeline." SET artwork_day = '".$artwork."', proof_day = '".$proof."', production_day = '".$production."', delivary_day = '".$delivary."', shipping_day = '".$shipping."', sunday_artwork = '".$sunday_artwork."', holiday_artwork = '".$holiday_artwork."', sunday_proof = '".$sunday_proof."', holiday_proof = '".$holiday_proof."', sunday_production = '".$sunday_production."', holiday_production = '".$holiday_production."', sunday_delivary = '".$sunday_delivary."', holiday_delivary = '".$holiday_delivary."', sunday_shipping = '".$sunday_shipping."', holiday_shipping = '".$holiday_shipping."' WHERE product_id = '".$product->getId()."' ";
			//$chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTimeline);
		
			$connectionWrite->beginTransaction();
			$data = array();
			$data['artwork_day'] = $artwork;
			$data['proof_day'] = $proof;
			$data['production_day'] = $production;
			$data['delivary_day'] = $delivary;
			$data['shipping_day'] = $shipping;
			$data['sunday_artwork'] = $sunday_artwork;
			$data['holiday_artwork'] = $holiday_artwork;
			$data['sunday_proof'] = $sunday_proof;
			$data['holiday_proof'] = $holiday_proof;
			$data['sunday_production'] = $sunday_production;
			$data['holiday_production'] = $holiday_production;
			$data['sunday_delivary'] = $sunday_delivary;
			$data['holiday_delivary'] = $holiday_delivary;
			$data['sunday_shipping'] = $sunday_shipping;
			$data['holiday_shipping'] = $holiday_shipping;
			$where = $connectionWrite->quoteInto('product_id =?', $product->getId());
			$connectionWrite->update($temptableTimeline, $data, $where);
			$connectionWrite->commit();
			
		}
		else{
			//$sqlTimeline="INSERT INTO ".$temptableTimeline." SET artwork_day = '".$artwork."', proof_day = '".$proof."', production_day = '".$production."', delivary_day = '".$delivary."', shipping_day = '".$shipping."', sunday_artwork = '".$sunday_artwork."', holiday_artwork = '".$holiday_artwork."', sunday_proof = '".$sunday_proof."', holiday_proof = '".$holiday_proof."', sunday_production = '".$sunday_production."', holiday_production = '".$holiday_production."', sunday_delivary = '".$sunday_delivary."', holiday_delivary = '".$holiday_delivary."', sunday_shipping = '".$sunday_shipping."', holiday_shipping = '".$holiday_shipping."' , product_id = '".$product->getId()."' ";
			//$chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTimeline);
			
			$connectionWrite->beginTransaction();
			$data = array();
			if($artwork !='' && $proof !='' && $production !='' && $delivary !='' && $shipping !=''){
				
				$data['artwork_day'] = $artwork;
				$data['proof_day'] = $proof;
				$data['production_day'] = $production;
				$data['delivary_day'] = $delivary;
				$data['shipping_day'] = $shipping;
				if($sunday_artwork != '')
				$data['sunday_artwork'] = $sunday_artwork;
				if($holiday_artwork != '')
				$data['holiday_artwork'] = $holiday_artwork;
				if($sunday_proof != '')
				$data['sunday_proof'] = $sunday_proof;
				if($holiday_proof != '')
				$data['holiday_proof'] = $holiday_proof;
				if($sunday_production != '')
				$data['sunday_production'] = $sunday_production;
				if($holiday_production != '')
				$data['holiday_production'] = $holiday_production;
				if($sunday_delivary != '')
				$data['sunday_delivary'] = $sunday_delivary;
				if($holiday_delivary != '')
				$data['holiday_delivary'] = $holiday_delivary;
				if($sunday_shipping != '')
				$data['sunday_shipping'] = $sunday_shipping;
				if($holiday_shipping != '')
				$data['holiday_shipping'] = $holiday_shipping;
				$data['product_id'] = $product->getId();
				$connectionWrite->insert($temptableTimeline, $data);
				$connectionWrite->commit();
			}
		}
	}
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('timeline/timeline')));
	mage::helper('AdminLogger')->updatelog('','Edit The Timeline');
	
	//exit;
	$this->_redirect('*/*/');
    }
    
    public function savetimeproductAction()
    {
	extract($_REQUEST);
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
	//$sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$product_id."' ";
	
	$sqlTimeline = $connectionRead->select()
                                ->from($temptableTimeline, array('*'))
                                ->where('product_id=?',$product_id);  
	$chkTimeline = $connectionRead->fetchAll($sqlTimeline);
	
	if(count($chkTimeline) > 0)
	{
		//$sqlTimeline="UPDATE ".$temptableTimeline." SET artwork_day = '".$artwork."', proof_day = '".$proof."', production_day = '".$production."', delivary_day = '".$delivary."', shipping_day = '".$shipping."', sunday_artwork = '".$sunday_artwork."', holiday_artwork = '".$holiday_artwork."', sunday_proof = '".$sunday_proof."', holiday_proof = '".$holiday_proof."', sunday_production = '".$sunday_production."', holiday_production = '".$holiday_production."', sunday_delivary = '".$sunday_delivary."', holiday_delivary = '".$holiday_delivary."', sunday_shipping = '".$sunday_shipping."', holiday_shipping = '".$holiday_shipping."' WHERE product_id = '".$product_id."' ";
		//$chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTimeline);
		
		$connectionWrite->beginTransaction();
                $data = array();
		$data['artwork_day'] = $artwork;
                $data['proof_day'] = $proof;
                $data['production_day'] = $production;
		$data['delivary_day'] = $delivary;
		$data['shipping_day'] = $shipping;
		$data['sunday_artwork'] = $sunday_artwork;
		$data['holiday_artwork'] = $holiday_artwork;
		$data['sunday_proof'] = $sunday_proof;
		$data['holiday_proof'] = $holiday_proof;
		$data['sunday_production'] = $sunday_production;
		$data['holiday_production'] = $holiday_production;
		$data['sunday_delivary'] = $sunday_delivary;
		$data['holiday_delivary'] = $holiday_delivary;
		$data['sunday_shipping'] = $sunday_shipping;
		$data['holiday_shipping'] = $holiday_shipping;
                $where = $connectionWrite->quoteInto('product_id =?', $product_id);
                $connectionWrite->update($temptableTimeline, $data, $where);
                $connectionWrite->commit();
	
	}
	else{
		//$sqlTimeline="INSERT INTO ".$temptableTimeline." SET artwork_day = '".$artwork."', proof_day = '".$proof."', production_day = '".$production."', delivary_day = '".$delivary."', shipping_day = '".$shipping."', sunday_artwork = '".$sunday_artwork."', holiday_artwork = '".$holiday_artwork."', sunday_proof = '".$sunday_proof."', holiday_proof = '".$holiday_proof."', sunday_production = '".$sunday_production."', holiday_production = '".$holiday_production."', sunday_delivary = '".$sunday_delivary."', holiday_delivary = '".$holiday_delivary."', sunday_shipping = '".$sunday_shipping."', holiday_shipping = '".$holiday_shipping."' , product_id = '".$product_id."' ";
		//$chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTimeline);
	
		$connectionWrite->beginTransaction();
                $data = array();
		if($artwork !='' && $proof !='' && $production !='' && $delivary !='' && $shipping !='' ){
			
			$data['artwork_day'] = $artwork;
			$data['proof_day'] = $proof;
			$data['production_day'] = $production;
			$data['delivary_day'] = $delivary;
			$data['shipping_day'] = $shipping;
			if($sunday_artwork != '')
			$data['sunday_artwork'] = $sunday_artwork;
			if($holiday_artwork != '')
			$data['holiday_artwork'] = $holiday_artwork;
			if($sunday_proof != '')
			$data['sunday_proof'] = $sunday_proof;
			if($holiday_proof != '')
			$data['holiday_proof'] = $holiday_proof;
			if($sunday_production != '')
			$data['sunday_production'] = $sunday_production;
			if($holiday_production != '')
			$data['holiday_production'] = $holiday_production;
			if($sunday_delivary != '')
			$data['sunday_delivary'] = $sunday_delivary;
			if($holiday_delivary != '')
			$data['holiday_delivary'] = $holiday_delivary;
			if($sunday_shipping != '')
			$data['sunday_shipping'] = $sunday_shipping;
			if($holiday_shipping != '')
			$data['holiday_shipping'] = $holiday_shipping;
			$data['product_id'] = $product_id;
			$connectionWrite->insert($temptableTimeline, $data);
			$connectionWrite->commit();
		}
	}
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('timeline/timeline')));
	mage::helper('AdminLogger')->updatelog($product_id,'Edit The Timeline for Product');
	
	$url = Mage::helper('adminhtml')->getUrl("adminhtml/catalog_product/edit/id/".$product_id);
	$url = str_replace('p//c','p/admin/c',$url);
        Mage::log($url); //To check if URL is correct (and it is correct)
        Mage::app()->getResponse()->setRedirect($url);
    }
    
    public function savetasknumberAction()
    {
	extract($_REQUEST);
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$temptableTask=Mage::getSingleton('core/resource')->getTableName('subadmin_task_number');
	
	
	$connectionWrite->beginTransaction();
	$condition = array($connectionWrite->quoteInto('user_id=?', $subadmin));
	
	$connectionWrite->delete($temptableTask, $condition);
	$connectionWrite->commit();
	
	foreach($product as $productid)
	{
		$connectionWrite->beginTransaction();
		$data = array();
		$data['product_id']= $productid;
		$data['user_id']=$subadmin;
		$data['task_number']=$task_number;
		$connectionWrite->insert($temptableTask, $data);
		$connectionWrite->commit();
	}

	//$sqlTask="SELECT * FROM ".$temptableTask." WHERE product_id = '".$product."' AND user_id = '".$subadmin."' ";
	//$chkTask = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTask);
	//
	//if(count($chkTask) > 0)
	//{
	//	$sqlTask="UPDATE ".$temptableTask." SET  task_number = '".$task_number."' WHERE product_id = '".$product."' AND user_id = '".$subadmin."', ";
	//	$chkTask = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTask);
	//}
	//else
	//{
	//	$sqlTask="INSERT INTO ".$temptableTask." SET product_id = '".$product."', user_id = '".$subadmin."', task_number = '".$task_number."' ";
	//	$chkTask = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTask);
	//	
	//}
	
	Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'The data has been inserted'
                    )
                );
	
	Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('timeline/timeline')));
	mage::helper('AdminLogger')->updatelog($subadmin,'Set The Task for User');
	
	$this->_redirect('*/*/');
    }
    
    public function loadtasknumberAction()
    {
	extract($_REQUEST);
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$temptableTask = Mage::getSingleton('core/resource')->getTableName('subadmin_task_number');
	
	$select = $connectionRead->select()
	->from($temptableTask, array('*'))
	->where('user_id=?',$subadmin);
	
	$result = $connectionRead->fetchAll($select);
	
	foreach($result as $tasknumber)
	{
		$productall[] = $tasknumber['product_id'];	
	}
	
	echo '<select name="product[]" multiple="multiple">
			<option value="">Product Sku</option>';
	$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSort('name', 'ASC');
			   // $collection->addAttributeToFilter('status',1);
			   
	foreach($collection as $_product)
	{
	    $_newProduct = Mage::getModel('catalog/product')->load($_product->getId());
	    
	    if(trim($_newProduct->getName()) != '')
	    {
		$selected = '';
		if(in_array($_product->getId(),$productall))
		$selected = 'selected';
		
		echo '<option value="'.$_product->getId().'" '.$selected.' />'.$_newProduct->getSku().'</option>';
	    }
	    
	}
			
	
	echo	'</select>';
	
	echo '@@'.$result[0]['task_number'];
    }
}