<?php
class AsiaConnect_Gallery_UploadController extends AsiaConnect_Gallery_Controller_Action
{
	
	protected function canUploadPhotos()
	{
		$config = Mage::getStoreConfig('gallery/info/upload_photos');
		if($config)
		{
			$currentCustomer = Mage::getSingleton('customer/session')->getCustomer();
			return (($currentCustomer->getId() && $config=='2') || Mage::getStoreConfig('gallery/info/upload_photos')=='1')?true:false;
		}
		return false;
	}
	protected function _init()
	{
		$this->loadLayout();
		$breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs');
		$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Home'), Mage::helper('gallery')->__('home'),Mage::getBaseUrl(),true);
		$rootAlbum = Mage::getModel("gallery/album")->load(1);
		$this->_addCrumb($breadcrumbBlock, $rootAlbum->getTitle(), $rootAlbum->getTitle(),$rootAlbum->getUrlRewrite(),true);
		$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Upload Photo'), Mage::helper('gallery')->__('Upload Photo'),null,true);
		
		$albumId = $this->getRequest()->getParam('album');
		if($albumId)
		{
			$currentAlbum = Mage::getModel('gallery/album')->load($albumId);
			if($currentAlbum->getId()) Mage::register('current_album',$currentAlbum);
			else {
				Mage::getSingleton('core/session')->addError($this->__("Request is invalid"));
				$this->_redirectUrl($rootAlbum->getUrlRewrite());
				return;
			}
		}else
		{
			Mage::getSingleton('core/session')->addError($this->__("Request is invalid"));
			$this->_redirectUrl($rootAlbum->getUrlRewrite());
			return;
		}
		
		return $this;
	}
	
	public function indexAction()
	{
		if(!$this->canUploadPhotos())
		{
			$this->norouteAction();
			return;
		}
		if($this->_init())
			$this->renderLayout();
	}
	
	public function saveAction()
	{
		if(!$this->canUploadPhotos())
		{
			$this->norouteAction();
			return;
		}
		
		$data = $this->getRequest()->getPost();
		if ($data){
			$collection = Mage::getModel('gallery/gallery')->getCollection()
						->addFieldToFilter('title',array('eq'=>$data['title']));
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
				} else {
					unset($data['filename']);
				}
		  		$data['status'] = 2;
		  			
				$model = Mage::getModel('gallery/gallery');		
				$model->setData($data);
	
				try {
					$model->setCreatedTime(now())
						  ->setUpdateTime(now());
					
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
					/**/
					
					
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('gallery')->__('Photo was uploaded successfully '));
	
					$this->_redirectUrl(Mage::getModel('gallery/album')->load(1)->getUrlRewrite());
					return;
	            } catch (Exception $e) {
	                Mage::getSingleton('core/session')->addError($e->getMessage());
	                Mage::getSingleton('core/session')->setFormData($data);
	                return;
	            }
	        }else{
				Mage::getSingleton('core/session')->addError(Mage::helper('gallery')->__('The photo title is exist, it do not save'));
				$this->_redirect('*/*/index',array('album' => $this->getRequest()->getParam('album_id')));
				return;
			}
		}
	}
}