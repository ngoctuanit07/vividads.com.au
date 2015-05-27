<?php

/**
 * MD_Quotemail.
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
 * @package    Quotemail
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php

class MD_Quotemail_Adminhtml_QuotemailController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('quotemail/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Email Template Manager'), Mage::helper('adminhtml')->__('Email Template Manager'));

        return $this;
    }

    public function indexAction() {
        
		
		$layout = $this->_initAction()
                ->renderLayout();
				//Zend_debug::dump($layout);
				
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('quotemail/quotemail')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('quotemail_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('quotemail/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('quotemail/adminhtml_quotemail_edit'))
                    ->_addLeft($this->getLayout()->createBlock('quotemail/adminhtml_quotemail_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('quotemail')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
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
                    $newDir = "quotemail";

                    $newdirPath = Mage::getBaseDir('media') . DS . "quotemail";

                    if (!file_exists($newdirPath)) {
                        mkdir($newdirPath, 0777);
                    }

                    $path = Mage::getBaseDir('media') . DS . $newDir . DS;
                    $resizedPath = Mage::getBaseDir('media') . DS . $newDir;
                    $uploader->save($path, $_FILES['filename']['name']);

                    /* Resize the image start */



                    $uploadedFileName = $uploader->getUploadedFileName();
                    $_imgUrl = $resizedPath . $uploadedFileName;

                    if (Mage::getStoreConfig('quotemail/general/quotemailresizeenabled') == 1) {
                        $imgHeight = Mage::getStoreConfig('quotemail/general/logoheight');
                        $imgWidth = Mage::getStoreConfig('quotemail/general/logowidth');

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


            $model = Mage::getModel('quotemail/quotemail');
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
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('quotemail')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('quotemail')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('quotemail/quotemail');

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
        $quotemailIds = $this->getRequest()->getParam('quotemail');
        if (!is_array($quotemailIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($quotemailIds as $quotemailId) {
                    $quotemail = Mage::getModel('quotemail/quotemail')->load($quotemailId);
                    $quotemail->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($quotemailIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $quotemailIds = $this->getRequest()->getParam('quotemail');
        if (!is_array($quotemailIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($quotemailIds as $quotemailId) {
                    $quotemail = Mage::getSingleton('quotemail/quotemail')
                            ->load($quotemailId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($quotemailIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'quotemail.csv';
        $content = $this->getLayout()->createBlock('quotemail/adminhtml_quotemail_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'quotemail.xml';
        $content = $this->getLayout()->createBlock('quotemail/adminhtml_quotemail_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

   
   protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') 		{
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
	   
	   $_quotemail_id = $this->getRequest()->getPost('quotemail_id');
	   
	   $_collection = Mage::getModel('quotemail/quotemail')->load($_quotemail_id);
	  // $_quotemail = $_collection->getData();
	  	$_html ='';
		$_html .= $_collection->getTemplate_text();
		echo $_html;
		 return;
	   }	
	
  /*function loadQuotEmail*/
   public function loadQuotEmailFilesAction(){
	   
	   $_quotemail_id = $this->getRequest()->getPost('quotemail_id');
	   
	   
	   $_connection_read = Mage::getSingleton('core/resource')->getConnection('core_read');
	   $_quotemail_attachements = Mage::getSingleton('core/resource')->getTableName('quotemail_attachements');
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
								   ->from(array($_quotemail_attachements))
								   ->where('quotemail_id=?',$_quotemail_id)	
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
		
		$_file_obj = $this->getRequest()->getParams('qqfile');
		
		
		$_file_name = $_file_obj['qqfile'];
		
		
		$input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
      	$file_directory = Mage::getBaseDir('media').'/attachedfiles/';
		
        $path = $file_directory.$_file_name;
		
		
		
		//chmod($path,777);
        $target = fopen($path, "w") ;        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target) ;
        fclose($target);
		//var_dump($stream);exit;
		///writing files in database ////		
		
		
		//$_quote_id = isset($this->getRequest()->getParam('id'))?$this->getRequest()->getParam('id'):0;
		
		///gettting db resources for adding in database 
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$quotemail_attachments	=Mage::getSingleton('core/resource')->getTableName('quotemail_attachements');
		$quotemail_table = Mage::getSingleton('core/resource')->getTableName('quotemail');
		
		//fetching quote id if not exists///
		
		if($this->getRequest()->getParam('id')){
			$_quote_id = $this->getRequest()->getParam('id');
			}else{
				
		
		try{
		$connectionRead->beginTransaction();
		$sql = 'SELECT `quotemail_id` from '.$quotemail_table.' ORDER BY quotemail_id DESC limit 1';
		$quotes = $connectionRead->query($sql);
		
		foreach($quotes as $quote){
			$_quote_id = $quote['quotemail_id']+1;
			}
        $connectionRead->commit();
			
		}catch(Exception $ex){
			echo $ex;
			}
		}
		
		///making data to insert data
		
		$data['quotemail_id']= $_quote_id;
		$data['email_attachment']=$_file_name;
		
		try{
        $connectionWrite->beginTransaction();
		$connectionWrite->insert($quotemail_attachments, $data);
        $connectionWrite->commit();
		}catch(Exception $xe){
			
			}
		
		return true;
		}
		
	

}