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

class MD_Quotemail_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function listAction() {
        $this->loadLayout();
        
        $this->renderLayout();
    }
	
	/*getStoreLogos*/
	
	public function getStoreLogosAction(){
		
		/*load all logos from model*/
		$_logos = Mage::getModel('quotemail/quotemail');
		$_logos = $_logos->getAllStoreLogos();		
		
		//var_dump($_logos);		
		
		return $_logos;
	}
	
	/*delete email attachements*/
	public function deleteAttachementAction(){
		
		$quote_id=$this->getRequest()->getPost('quoteid');
		$quote_attachement_id=$this->getRequest()->getPost('quoteattachementid');
		$filename=$this->getRequest()->getPost('filename');
		
		/*getting db resource */
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$quote_attachement_table=Mage::getSingleton('core/resource')->getTableName('quotemail_attachements');
		
		$connectionWrite->beginTransaction();
		$__condition = array($connectionWrite->quoteInto('email_attachment_id=?', $quote_attachement_id));
		
		$deleted = $connectionWrite->delete($quote_attachement_table, $__condition);		
		$connectionWrite->commit();
		
		/*delete file from hard disk*/
		
		$attachement_dir = Mage::getBaseDir('media').'\attachedfiles\\';
		
		
		$filename = $attachement_dir.$filename;
		
		
		  if (file_exists($filename)) {
				unlink($filename);
				echo 'File '.$filename.' has been deleted';
			  } else {
				echo 'Could not delete '.$filename.', file does not exist';
			  }
		
		if($deleted){
			return 1;
				}else{
			return 0;	
				}
		
		}	

}