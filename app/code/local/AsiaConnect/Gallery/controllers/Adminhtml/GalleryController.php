<?php

class AsiaConnect_Gallery_Adminhtml_GalleryController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('gallery/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Photo Manager'), Mage::helper('adminhtml')->__('Photo Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('gallery/gallery')->load($id);
		$error_data = Mage::getSingleton('adminhtml/session')->getData('error_data');
		
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if(!empty($error_data)) {
				unset($error_data['form_key']);
				if(!empty($error_data['filename'])) $error_data['filename'] = $error_data['filename']['value'];
				$model->setData($error_data);
			}

			Mage::register('gallery_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('gallery/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('gallery/adminhtml_gallery_edit'))
				->_addLeft($this->getLayout()->createBlock('gallery/adminhtml_gallery_edit_tabs'));

			$this->renderLayout();
			Mage::getSingleton('adminhtml/session')->clear();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$collection = Mage::getModel('gallery/gallery')->getCollection()
						->addFieldToFilter('title',array('eq'=>$data['title']));
						if($this->getRequest()->getParam('id'))$collection ->addFieldToFilter('gallery_id',array('neq'=>$this->getRequest()->getParam('id')))
						;

			if(!sizeof($collection)){
				if($_FILES['filename']['name'] != '') {
					try {	
						/* Starting upload */	
						$uploader = new Varien_File_Uploader('filename');
						
						// Any extention would work
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(true);
						
						// Set the file upload mode 
						// false -> get the file directly in the specified folder
						// true -> get the file in the product like folders 
						//	(file.jpg will go in something like /media/f/i/file.jpg)
						$uploader->setFilesDispersion(false);
								
						// We set media as the upload dir 
						$path = Mage::getBaseDir('media') . DS . 'gallery' . DS.(($data['album_id'] != '1') ? $data['album_id'].DS : '');
						$result = $uploader->save($path);
						
					} catch (Exception $e) {
			      
			        }
		        
			        //this way the name is saved in DB
		  			$data['filename'] = 'gallery/'.(($data['album_id'] != '1') ? $data['album_id'].'/' : '').$result['file'];
		  			//var_dump($data['filename']);die;
				} else {
					if(isset($data['filename']['delete']) && $data['filename']['delete'] == 1) {
						 $data['filename'] = '';
					} else {
						unset($data['filename']);
					}
				}
		  			
		  			
				$model = Mage::getModel('gallery/gallery');		
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
					
					$album_id = $data['album_id'];
					$album = Mage::getModel('gallery/album')->load($album_id);
					
					// insert new url rewrite
					$suffix = Mage::getStoreConfig('gallery/info/photo_suffix');
					$suffix = strlen($suffix)?$suffix:".html";
					if(!strlen($model->getUrlKey()))
					{
						$rq_path = $model->getTitle();
					}else
					{
						$rq_path = $model->getUrlKey();
					}
					
					$rq_path = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($rq_path));
					$rq_path = strtolower($rq_path);
					$rq_path = trim($rq_path, '-').$suffix;
					$url_rewrite_id = $data['url_rewrite_id']?$data['url_rewrite_id']:null;
					$rewriteModel = Mage::getModel('core/url_rewrite');
					
					$data = array(
						'url_rewrite_id'=>$url_rewrite_id,
						'is_system'		=> 1,
						'id_path'		=> 'gallery/photo/'.$model->getId().'/album/'.$album->getId(),
						'request_path'	=> $rq_path,
						'target_path'	=> 'gallery/view/photo/id/'.$model->getId().'/album/'.$album->getId(),
					);
					$rewriteModel->setData($data);
		            $rewriteModel->save();
					$data = $model->getData();
	                $data['url_key'] = str_replace($suffix,'',$rq_path);
	                $data['url_rewrite_id'] = $rewriteModel->getId();
	
					Mage::getModel('gallery/gallery')->setData($data)->save();
	
					
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('Photo was successfully saved'));
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
	        }else{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('The photo title is exist, it do not save'));
				Mage::getSingleton('adminhtml/session')->setData('error_data',$data);
				$this->_redirect('*/*/edit',array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Unable to find photo to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('gallery/gallery')->load($this->getRequest()->getParam('id'));
				$rewriteModel = Mage::getModel('core/url_rewrite')->load($model->getUrlRewriteId())->delete();
				$model->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Photo was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $galleryIds = $this->getRequest()->getParam('gallery');
        if(!is_array($galleryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select photo(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
					$model = Mage::getModel('gallery/gallery')->load($galleryId);
					$rewriteModel = Mage::getModel('core/url_rewrite')->load($model->getUrlRewriteId())->delete();
					$model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($galleryIds)
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
        $galleryIds = $this->getRequest()->getParam('gallery');
        if(!is_array($galleryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select photo(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getSingleton('gallery/gallery')
                        ->load($galleryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($galleryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'gallery.csv';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'gallery.xml';
        $content    = $this->getLayout()->createBlock('gallery/adminhtml_gallery_grid')
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
    public function saveOrderAction()
    {
    	$items = explode('|',$this->getRequest()->getParam('items'));
    	$model = Mage::getModel('gallery/gallery');
    	foreach($items as $item)
    	{
    		$_item = explode('_',$item);	
    		$model->load($_item[0]);
    		$model->setOrder($_item[1]);
    		$model->save();
    	}
    	$this->_redirect('*/*/index');
    }
    
	public function updateUrlRewriteAction()
    {
    	$collection = Mage::getModel("gallery/gallery")->getCollection();
    	foreach($collection as $photo)
    	{
    		$suffix = Mage::getStoreConfig('gallery/info/photo_suffix');
			$suffix = strlen($suffix)?$suffix:".html";
			if(!strlen($photo->getUrlKey()))
			{
				$rq_path = $photo->getTitle();
			}else
			{
				$rq_path = $photo->getUrlKey();
			}
			$urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($rq_path));
			$urlKey = strtolower($urlKey);
			$rq_path = trim($urlKey, '-').$suffix;
			$data = $photo->getData();
			$url_rewrite_id = $data['url_rewrite_id']?$data['url_rewrite_id']:null;
			$album_id = $photo->getAlbumId();
			$album = Mage::getModel('gallery/album')->load($album_id);
			$rewriteModel = Mage::getModel('core/url_rewrite');
			$data = array(
				'url_rewrite_id'=>$url_rewrite_id,
				'is_system'		=> 1,
				'id_path'		=> 'gallery/photo/'.$photo->getId().'/album/'.$album->getId(),
				'request_path'	=> $rq_path,
				'target_path'	=> 'gallery/view/photo/id/'.$photo->getId().'/album/'.$album->getId(),
			);
			$rewriteModel->setData($data);
            $rewriteModel->save();
            
            $data = $photo->getData();
            $data['url_key'] = str_replace($suffix,'',$rq_path);
            $data['url_rewrite_id'] = $rewriteModel->getId();
            $photo->setData($data)->save();
    	}
    	$this->_redirect('*/*/index');
    }
}
