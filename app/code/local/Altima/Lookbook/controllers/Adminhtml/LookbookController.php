<?php
/**
 * Altima Lookbook Free Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Altima
 * @package    Altima_LookbookFree
 * @author     Altima Web Systems http://altimawebsystems.com/
 * @email      support@altima.net.au
 * @copyright  Copyright (c) 2012 Altima Web Systems (http://altimawebsystems.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Altima_Lookbook_Adminhtml_LookbookController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('cms')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Hot Spot manager'), Mage::helper('adminhtml')->__('Hot Spot manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		
		$products = Mage::getModel('catalog/product')->getCollection();
		$products->addAttributeToFilter('status', 1);//enabled
		$products->addAttributeToFilter('visibility', 4);//catalog, search
		 $products->addAttributeToSelect('*');
		$prodIds=$products->getAllIds();
		
		//echo "<pre>";
		//print_r($prodIds);
		//echo "</pre>";
		
		
		for($i=0;$i<count($prodIds);$i++)
		{
			$temptableLookbook=Mage::getSingleton('core/resource')->getTableName('lookbook');
			
			$sqlLookbook="select *  from ".$temptableLookbook." where product_id='".$prodIds[$i]."'";
			try {
				$chkLookbook = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlLookbook);
				
				if(count($chkLookbook)>0)
				{
					foreach($chkLookbook as $res_Lookbook) 
					{
						
						$_previewWidth   = intval(Mage::getStoreConfig('amzoom/size/preview_width'));
						$_previewHeight  = intval(Mage::getStoreConfig('amzoom/size/preview_height'));
						
						$_product=Mage::getModel('catalog/product')->load($prodIds[$i]);
						// get  Product's name
						//echo $_product->getName();
						////get product's short description
						//echo $_product->getShortDescription();
						////get Product's Long Description
						//echo $_product->getDescription();
						////get  Product's Regular Price
						//echo $_product->getPrice();
						////get  Product's Special price
						//echo $_product->getSpecialPrice();
						////get Product's Url
						//echo $_product->getProductUrl();
						////get Product's image Url
						//echo $_product->getImageUrl();
						$search_txt="/media/";
						$start_pos=strpos($_product->getSmallImageUrl($_previewWidth,$_previewHeight),$search_txt);
						
						$sub=substr($_product->getSmallImageUrl($_previewWidth,$_previewHeight),($start_pos+7));
						
						
						
						
						
						$sqlLookbook="update ".$temptableLookbook." set image='".$sub."' where product_id='".$prodIds[$i]."'";
						$chkLookbook = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlLookbook);
						
						
						
						
						
						
						
						
					}	
				}
				else
				{
					$_previewWidth   = intval(Mage::getStoreConfig('amzoom/size/preview_width'));
					$_previewHeight  = intval(Mage::getStoreConfig('amzoom/size/preview_height'));
					$_product=Mage::getModel('catalog/product')->load($prodIds[$i]);
					
					$search_txt="/media/";
					$start_pos=strpos($_product->getSmallImageUrl($_previewWidth,$_previewHeight),$search_txt);
					
					$sub=substr($_product->getSmallImageUrl($_previewWidth,$_previewHeight),($start_pos+7));
					
					
					
					$sqlLookbook="insert into ".$temptableLookbook." set lookbook_id='',name='".addslashes($_product->getName())."',image='".$sub."',hotspots='[]',position='0',status='1',product_id='".$prodIds[$i]."'";
					$chkLookbook = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlLookbook);
					
				}
				
				
			} catch (Exception $e){
				//echo $e>getMessage();
			}
			
		}
		
		
		
		
		//$temptableLookbook=Mage::getSingleton('core/resource')->getTableName('lookbook');
		//$sqlLookbook="UPDATE ".$temptableSaleOrderGrid." SET status='invoice_downloaded' WHERE increment_id='".$invoiceNumber."'";
		//try {
		//$chkSaleOrderGrid = Mage::getSingleton('core/resource')>getConnection('core_write')>query($sqlSaleOrderGrid);
		//} catch (Exception $e){
		////echo $e>getMessage();
		//}
		
		
		
		
		
		$this->_initAction()
			->renderLayout();
			
		
	}

	public function editAction() {
        $slides_count = Mage::getModel('lookbook/lookbook')->getCollection()
                        ->getSize();
        $id     = $this->getRequest()->getParam('id');
        if ($id) {                

    		$model  = Mage::getModel('lookbook/lookbook')->load($id);
    
    		if ($model->getId() || $id == 0) {
    			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
    			if (!empty($data)) {
    				$model->setData($data);
    			}
    
    			Mage::register('lookbook_data', $model);
    
    			$this->loadLayout();
    			$this->_setActiveMenu('cms');
    
    			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Hot Spot manager'), Mage::helper('adminhtml')->__('Hot Spot manager'));
    
    			$this->_addContent($this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit'))
    				->_addLeft($this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit_tabs'));
    
    			$this->renderLayout();
    		} else {
    			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('lookbook')->__('Hot Spot does not exist'));
    			$this->_redirect('*/*/');
    		}
      }
      
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {	
	  			
			$model = Mage::getModel('lookbook/lookbook');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				
			 
                 if ($model->getId() && isset($data['identifier_create_redirect']))
                 {
                        $model->setData('save_rewrites_history', (bool)$data['identifier_create_redirect']);
                 }
             
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('lookbook')->__('Hot Spot was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('lookbook')->__('Unable to find Hot Spot to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('lookbook/lookbook');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Hot Spot was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function uploadAction()
	{

           $upload_dir  = Mage::getBaseDir('media').'/lookbook/';
           if (!file_exists($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $uploader = Mage::getModel('lookbook/fileuploader');

            $config_check = $uploader->checkServerSettings();

            if ($config_check === true){
               $result = $uploader->handleUpload($upload_dir); 
            } 
            else
            {
                $result = $config_check;
            }

            // to pass data through iframe you will need to encode all html tags
            $this->getResponse()->setBody(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
	}

    
    public function getproductAction(){
        //	$sku     = $this->getRequest()->getParam('text');
        //    $product_id = Mage::getModel('catalog/product')->getIdBySku($sku);
        //    $status =  Mage::getModel('catalog/product')->load($product_id)->getStatus();
        //    if ($product_id) {
        //        if ($status==1) 
        //        {
        //          $result= 1;  
        //        }
        //        else
        //        {
        //          $result = "is disabled";  
        //        }
        //        
        //    }
        //    else
        //    {
        //        $result = "doesn't exists"; 
        //    }
	$result= 1;  
    $this->getResponse()->setBody($result);
    }
    
    public function massDeleteAction() {
        $lookbookIds = $this->getRequest()->getParam('lookbook');
        if(!is_array($lookbookIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Hot Spots'));
        } else {
            try {
                foreach ($lookbookIds as $lookbookId) {
                    $lookbook = Mage::getModel('lookbook/lookbook')->load($lookbookId);
                    $lookbook->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($lookbookIds)
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
        $lookbookIds = $this->getRequest()->getParam('lookbook');
        if(!is_array($lookbookIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select slide(s)'));
        } else {
            try {
                foreach ($lookbookIds as $lookbookId) {
                    $lookbook = Mage::getSingleton('lookbook/lookbook')
                        ->load($lookbookId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($lookbookIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'hotspot.csv';
        $content    = $this->getLayout()->createBlock('lookbook/adminhtml_lookbook_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'hotspot.xml';
        $content    = $this->getLayout()->createBlock('lookbook/adminhtml_lookbook_grid')
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
}