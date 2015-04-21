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

class MD_Vividslider_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function listAction() {
        $this->loadLayout();
        
        $this->renderLayout();
		//
    }
	
	
	
	/*delete deleteFileAction*/
	public function deleteFileAction(){
		
		$slider_file_id = $this->getRequest()->getPost('slider_file_id');					
		$slider_id 		= $this->getRequest()->getPost('slider_id');		
		$file_name		= $this->getRequest()->getPost('file_name');
		
		/*getting db resource */
		$connectionRead 	= 	Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite 	= 	Mage::getSingleton('core/resource')->getConnection('core_write');
    	$slider_files_table	=	Mage::getSingleton('core/resource')->getTableName('vividslider_files');
		
		//var_dump($slider_file_id);
		
		//$connectionWrite->beginTransaction();
		$__condition = array($connectionWrite->quoteInto('slider_file_id=?', $slider_file_id));
		
		$deleted = $connectionWrite->delete($slider_files_table, $__condition);		
		//$connectionWrite->commit();
		//var_dump($deleted);
		/*delete file from hard disk*/
		
		$category_id = Mage::getModel('vividslider/vividslider')
									->load($slider_id)
									->getCategory_id();
		
		$slider_files_dir = Mage::getBaseDir('media').'/sliderfiles/'.$category_id.'/';
		
		
		$file_name = $slider_files_dir.$file_name;
		
		
		  if (file_exists($file_name)) {
				unlink($file_name);
				echo 'File '.$file_name.' has been deleted';
			  } else {
				echo 'Could not delete '.$file_name.', file does not exist';
			  }
		
		if($deleted){
			return 1;
				}else{
			return 0;	
				}
		
		}	
		
	/*save Slider File*/
	public function saveFileAction(){
		
		$slider_id = $this->getRequest()->getPost('slider_id');
		$slider_file_id = $this->getRequest()->getPost('slider_file_id');
		$file_name = $this->getRequest()->getPost('file_name');
		$slider_url = $this->getRequest()->getPost('slider_url');
		$slider_file_title = $this->getRequest()->getPost('slider_file_title');
		
		$data = array('slider_url'=>$slider_url,
					  'slider_file_title'=>$slider_file_title,
						);
		
		/*getting db resource */
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$slider_files_table = Mage::getSingleton('core/resource')->getTableName('vividslider_files');
		
		$connectionWrite->beginTransaction();
		$_condition = $connectionWrite->quoteInto('slider_file_id=?', $slider_file_id);				
		$saved = $connectionWrite->update($slider_files_table, $data, $_condition);		
		$connectionWrite->commit();
		//var_dump($data); var_dump($_condition);
		return $saved;
		
		}	
		
	 //function getStore		
	 
	 public function getstoreAction(){
		 
		 $_type = $this->getRequest()->getPost('s_type');
		 if($_type=='store'){
		 $_store_id = $this->getRequest()->getPost('sid');
		 
		 $_c_store = Mage::getSingleton('core/store')->load($_store_id);
		 $_store_name = $_c_store->getName();
		 $_data['store_name'] =  $_store_name;
		 
		 echo json_encode($_data);
		 return json_encode($_data);
		 
		 }else{
			 $_category_id = $this->getRequest()->getPost('sid');
			 $_category = Mage::getModel('catalog/category')->load($_category_id);
			 $_category_name = $_category->getName();
			 $_data['category_name'] = $_category_name; 
			  echo json_encode($_data);
		 	return json_encode($_data);
			 }
		 
		 }


}