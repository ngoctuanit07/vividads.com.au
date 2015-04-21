<?php

/**
 * MD_Vividslider.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Vividslider
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php

class MD_Vividslider_Adminhtml_VividsliderController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('vividslider/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Email Template Manager'), Mage::helper('adminhtml')->__('Email Template Manager'));

        return $this;
    }

    public function indexAction() {
        
		
		$layout = $this->_initAction()
                ->renderLayout();
				// Zend_debug::dump($layout);
				
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('vividslider/vividslider')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('vividslider_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('vividslider/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vividslider/adminhtml_vividslider_edit'))
                    ->_addLeft($this->getLayout()->createBlock('vividslider/adminhtml_vividslider_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vividslider')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveoldAction() {
        if ($data = $this->getRequest()->getPost()) {
            
         
		    if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');
                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(true);
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(true);
                    // We set media as the upload dir                    
                    $newDir = "vividslider";

                    $newdirPath = Mage::getBaseDir('media') . DS . "vividslider";

                    if (!file_exists($newdirPath)) {
                        mkdir($newdirPath, 0777);
                    }

                    $path = Mage::getBaseDir('media') . DS . $newDir . DS;
                    $resizedPath = Mage::getBaseDir('media') . DS . $newDir;
                    $uploader->save($path, $_FILES['filename']['name']);

                    /* Resize the image start */



                    $uploadedFileName = $uploader->getUploadedFileName();
                    $_imgUrl = $resizedPath . $uploadedFileName;

                    if (Mage::getStoreConfig('vividslider/general/vividsliderresizeenabled') == 1) {
                        $imgHeight = Mage::getStoreConfig('vividslider/general/logoheight');
                        $imgWidth = Mage::getStoreConfig('vividslider/general/logowidth');

                        if (file_exists($_imgUrl)) {
                            $imageObj = new Varien_Image($_imgUrl);
                            $imageObj->constrainOnly(TRUE);
                            $imageObj->keepAspectRatio(FALSE);
                            $imageObj->keepFrame(FALSE);
                            $imageObj->resize($imgWidth, $imgHeight);
                            $imageObj->save($_imgUrl);
                        }
                    }
                } catch (Exception $e) {
                    
                }
                //this way the name is saved in DB
                $data['filename'] = $uploader->getUploadedFileName();
            }


            $model = Mage::getModel('vividslider/vividslider');
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
                
                if (isset($data['stores'])) {
                    $stores = $data['stores'];
                } else {
                    $stores = array(null);
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vividslider')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vividslider')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
	
	 public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            
			 
         
		   $model = Mage::getModel('vividslider/vividslider');
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
                
                if (isset($data['stores'])) {
                    $stores = $data['stores'];
                } else {
                    $stores = array(null);
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vividslider')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vividslider')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('vividslider/vividslider');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete()	;
				
				$model->deleteFiles($this->getRequest()->getParam('id'));		

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
        $vividsliderIds = $this->getRequest()->getParam('vividslider');
        if (!is_array($vividsliderIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vividsliderIds as $vividsliderId) {
                    $vividslider = Mage::getModel('vividslider/vividslider')->load($vividsliderId);
                    $vividslider->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($vividsliderIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $vividsliderIds = $this->getRequest()->getParam('vividslider');
        if (!is_array($vividsliderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vividsliderIds as $vividsliderId) {
                    $vividslider = Mage::getSingleton('vividslider/vividslider')
                            ->load($vividsliderId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($vividsliderIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'vividslider.csv';
        $content = $this->getLayout()->createBlock('vividslider/adminhtml_vividslider_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'vividslider.xml';
        $content = $this->getLayout()->createBlock('vividslider/adminhtml_vividslider_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

   
   protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') 						
   	{
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	
   /*function loadQuotEmail*/
   public function loadQuotEmailAction(){
	   
	   $_vividslider_id = $this->getRequest()->getPost('vividslider_id');
	   
	   $_collection = Mage::getModel('vividslider/vividslider')->load($_vividslider_id);
	  // $_vividslider = $_collection->getData();
	  	$_html ='';
		$_html .= $_collection->getTemplate_text();
		echo $_html;
		 return;
	   }	
	
  /*function loadQuotEmail*/
   public function loadQuotEmailFilesAction(){
	   
	   $_vividslider_id = $this->getRequest()->getPost('vividslider_id');
	   
	   
	   $_connection_read = Mage::getSingleton('core/resource')->getConnection('core_read');
	   $_vividslider_attachements = Mage::getSingleton('core/resource')->getTableName('vividslider_attachements');
	   ///output ////	
	   $_html ='';
	   $attachment_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'attachedfiles/';
	   $icons_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'upload/fileicons/icons/';
		
		
		$_file_icons = array('png'=>'png_icon.png',
							 'doc'=>'docx_icon.png',
							 'docx'=>'docx_icon.png', 
							 'fla'=>'fla_icon.png',
							 'psd'=>'psd_icon.png',
							 'pdf'=>'pdf_icon.png',
							 'rar'=>'rar_icon.png',
							 'txt'=>'txt_icon.png',
							 'ai'=>'ai_icon.png',
							 'xlx'=>'xlxs_icon.png',
							 'xlxs'=>'xlxs_icon.png',
							 'ttf'=>'ttf_icon.png',
							 'gif'=>'gif_icon.png',
							 'jpg'=>'jpg_icon.png',
							 'jpeg'=>'jpg_icon.png',
							 'sql'=>'other_icon.png',
							 'eps'=>'other_icon.png'
							 );
	   try {
			$_transaction = $_connection_read->beginTransaction();
			$_select = $_transaction->select()
								   ->from(array($_vividslider_attachements))
								   ->where('vividslider_id=?',$_vividslider_id)	
									;	
			$_html .='<br/><h3>Email Attachement(s)</h3>: <ul>';						
			 $_files = $_transaction->fetchAll($_select);
			 
			 
			 
	foreach($_files as $_file){
			
			$_exten = substr(basename($_file['email_attachment']),strpos(basename($_file['email_attachment']),'.')+1);	
				
				$_html .='<li id="email_attachement_'.$_file['email_attachment_id'].'" style="float:left; text-align:center; width:100px;"> <a href="'.$attachment_dir.$_file['email_attachment'].'" target="_blank"><img src="'.$icons_dir.$_file_icons[$_exten].'" /><br/>'.$_file['email_attachment'].'</a><br/><!--div style="cursor:pointer;" onclick="deleteAttachement('.$quote_id.','.$_file['email_attachment_id'].',\''.$_file['email_attachment'].'\')" title="Click to remove '.$_file['email_attachment'].'">X Remove</div-->';
			
			$_html .='<input type="hidden" id="email_files_'.$_image['email_attachment_id'].'"  name="email_attachements[]" value="'.$_file['email_attachment'].'" />';
			
			$_html .='</li>';
				
		}
									
			$_connection_read->commit();
			} catch (Exception $e) {
				echo 'Oops! Could not read from the file...'.$e;
	}
	   
	   $_html .='</ul>';
	   
	   echo $_html; 
	   return $_html;
	  }	   
 	
	/*function upload attachements*/	   
	
	public function uploadAction(){
		
		
		//var_dump($stream);exit;
		///writing files in database ////		
		
		
		//$_quote_id = isset($this->getRequest()->getParam('id'))?$this->getRequest()->getParam('id'):0;
		
		///gettting db resources for adding in database 
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$vividslider_files	=Mage::getSingleton('core/resource')->getTableName('vividslider_files');
		$vividslider_table = Mage::getSingleton('core/resource')->getTableName('vividslider');
		
		//fetching quote id if not exists///
		
		if($this->getRequest()->getParam('id')){
			$_slider_id = $this->getRequest()->getParam('id');
			
			$category_id = Mage::getModel('vividslider/vividslider')
									->load($_slider_id)
									->getCategory_id();
			
		//var_dump($category_id);
		$_file_obj = $this->getRequest()->getParams('qqfile');		
		$_file_name = $_file_obj['qqfile'];		
		
		$input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
      	$file_directory = Mage::getBaseDir('media').'/sliderfiles';
		if(!is_dir($file_directory.'/'.$category_id))	{
			$old_umask = umask(0);			
			if(@mkdir($file_directory.'/'.$category_id,0777)){
				$file_directory = $file_directory.'/'.$category_id;
				//var_dump($file_directory);
				}			
			umask($old_umask);	
			 $file_directory = Mage::getBaseDir('media').'/sliderfiles/'.$category_id.'/';
			}else{
				$file_directory = Mage::getBaseDir('media').'/sliderfiles/'.$category_id.'/';
				}
				
       
	    $path = $file_directory.$_file_name;		
		
		// chmod($path,0777);
        $target = fopen($path, "w") ;        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target) ;
        fclose($target);
			
			
			
			}else{
				
		
		try{
		$connectionRead->beginTransaction();
		$sql = 'SELECT `vividslider_id` from '.$vividslider_table.' ORDER BY vividslider_id DESC limit 1';
		$sliders = $connectionRead->query($sql);
		
		if(count($sliders)>0){
		foreach($sliders as $slider){
			$_slider_id = $slider['vividslider_id']+1;
			}
		}else{
			$_slider_id = 1;
			}
        $connectionRead->commit();
			
		}catch(Exception $ex){
			echo $ex;
			}
		}
		
		
		
		///making data to insert data
		
		$data['vividslider_id']= $_slider_id;
		$data['slider_file']=$_file_name;
		
		try{
			$connectionWrite->beginTransaction();
			$connectionWrite->insert($vividslider_files, $data);
			$connectionWrite->commit();
		}catch(Exception $ex){
			var_dump($ex);
			}
		
		return true;
		}
		
	

}