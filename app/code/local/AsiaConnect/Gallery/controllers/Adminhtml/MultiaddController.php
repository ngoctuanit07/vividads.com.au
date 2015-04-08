<?php

class AsiaConnect_Gallery_Adminhtml_MultiaddController extends Mage_Adminhtml_Controller_action
{
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }	
	public function multiaddAction() 
	{
		$this->loadLayout();
		$maxUploadSize = Mage::helper('importexport')->getMaxUploadSize();
        $this->_getSession()->addNotice(
            $this->__('Total size of uploadable files must not exceed %s', $maxUploadSize)
        );  
		$this->_setActiveMenu('gallery/items');

		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('gallery/adminhtml_gallery_multiadd'))
			->_addLeft($this->getLayout()->createBlock('gallery/adminhtml_gallery_multiadd_tabs'));

		$this->renderLayout();
		Mage::getSingleton('adminhtml/session')->clear();
	}
	
	public function multisaveAction() {
		$data = $this->getRequest();
		//var_dump($data);die;
	}
    public function uploadAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
        	//upload
        	if($_FILES['import_image_archive']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('import_image_archive');
	           		$uploader->setAllowedExtensions(array('zip'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);
					$uploadPath = Mage::getBaseDir('media') . DS . 'gallery' .DS.(($data['album_id'] != '1') ? $data['album_id'].DS : '');
					$uploadResult = $uploader->save($uploadPath, $_FILES['import_image_archive']['name'] );
					
					//decompress
				    $compress = $uploadResult['path'].$uploadResult['file'];
				    if(file_exists($compress)){
				    	$tmpPath = tempnam(sys_get_temp_dir(), 'Gal');
					    $filter = new Zend_Filter_Decompress(array(
					    	'adapter' => 'Zip',
					   		'options' => array(
		 						'target' => $tmpPath
							)
					    ));
					    $decomResult = $filter->filter($compress);
					    
					    $destPath = Mage::getBaseDir('media') . DS . 'gallery' .DS.(($data['album_id'] != '1') ? $data['album_id'].DS : '');
					    $this->moveDecompressedImage($decomResult,$destPath);
						$this->_getSession()->addSuccess('successful!');
				    }
				    else $this->_getSession()->addError($this->__('Image archive file not found'));
				    unlink($compress);
	                $this->_redirect('*/*/multiadd');
				} catch (Exception $e) {
		      		$message = $e->getMessage();
	            	if($e->getMessage() =='Disallowed file type.')
	            	{
	            		$message = $this->__("only 'zip' file extension is supported");
	            	}
	                $this->_getSession()->addError($message);
	                $this->_redirect('*/*/multiadd');
			    }

			}
			else{
				try {
					$compress = $data['filepath'];
				    if(file_exists($compress)){
				    	//decompress
				    	$tmpPath = tempnam(sys_get_temp_dir(), 'Gal');
					    $filter = new Zend_Filter_Decompress(array(
					    	'adapter' => 'Zip',
					   		'options' => array(
		 						'target' => $tmpPath
							)
					    ));				    	
					    $decomResult = $filter->filter($compress);
					    //var_dump($decomResult);die;
					    $destPath = Mage::getBaseDir('media') . DS . 'gallery' .DS.(($data['album_id'] != '1') ? $data['album_id'].DS : '');
					    $this->moveDecompressedImage($decomResult,$destPath);
						$this->_getSession()->addSuccess('Successful!');
				    }
				    else $this->_getSession()->addError($this->__('Image archive file not found'));
	                $this->_redirect('*/*/multiadd');
				} catch (Exception $e) {
	                $this->_getSession()->addError($e->getMessage());
	                $this->_redirect('*/*/multiadd');
			    }			
			}
			
        }
    }
    
    public function moveDecompressedImage($tmpPath,$destPath)
    {
    	try 
    	{
    		$count = $total = 0;
			if($tmpPath){
				$nameSuffixNum = 1;
				$data = $this->getRequest()->getPost();
				
				//iterate through all image in folder
				$dir = new DirectoryIterator($tmpPath);
				foreach($dir as $fileInfo) {
					//get photo title
					$title = ($data['title']) ? $data['title'].$nameSuffixNum:null;
					
					$ext = strtolower(pathinfo($fileInfo->getFilename(),PATHINFO_EXTENSION));
					if($fileInfo->isDot() OR $fileInfo->isDir() OR (! in_array($ext, array('jpg','jpeg','gif','png'))) ) {
					} else {
						//get correct name (turn space and other non-alphanumeric char into underscore)
						$fileName = Varien_File_Uploader::getCorrectFileName($fileInfo->getFilename());
						//rename if needed
						$fileName = Varien_File_Uploader::getNewFileName($destPath . $fileName);
						
						//check if destination exist
						$path = Mage::getBaseDir('media') . DS . 'gallery' . DS;
						if( !mkdir($destPath) AND !file_exists($destPath) ){
						}else{
						//move
							if( rename($fileInfo->getPathname(), $destPath.$fileName) )
							{
								$total++;
								if($this->saveMovedImage($destPath.$fileName,$title))
								{
									$count++;
									$nameSuffixNum++;
								}
							}
						}
					}
				}
				$this->_getSession()->addSuccess($this->__($count.' valid image ( of total '.$total.' image ) saved.'));
				if($total == 0){
					$this->_getSession()->addNotice($this->__('Please check your archive and make sure album folder exist ('.$destPath.')'));
				}
			}
    	}catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
		}
    }    
    
    public function saveMovedImage($img, $title)
    {
    	try
    	{
	    	$data = $this->getRequest()->getPost();
	    	$data['title'] = ($title) ? $title:pathinfo($img,PATHINFO_FILENAME);
	    	$data['filename'] = 'gallery/'.(($data['album_id'] != '1') ? $data['album_id'].'/' : '').pathinfo($img,PATHINFO_BASENAME);
	    	$data['status'] = 1;
	    	$model = Mage::getModel('gallery/gallery');
	    	$model->setData($data);
	    	$model->setCreatedTime(now())
	    		->setUpdateTime(now());
	    	$model->save();
	    	$album_id = $data['album_id'];
			$album = Mage::getModel('gallery/album')->load($album_id);
			
			/* insert new url rewrite */
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
			//check if request path exist
			if($rewriteModel->loadByRequestPath($rq_path)->getId()){
				$rq_path = $model->getId().$rq_path; 
			}
			
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
			
			return true;
    	}catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return false;
		}
    }
    
}
