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

class MD_Quotemail_Model_Quotemail extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('quotemail/quotemail');
    }
	
	
	/* function get all quote emails*/
	
	public function getAllQuoteMails(){
		
		/*getting collection of all emails*/
		
		$collection = Mage::getModel('quotemail/quotemail')->getCollection(); 
        $quotemails = $collection->getData();
		
		return $quotemails;
		 
		}
	
	
	

    public function getAllLogos() {
        $store_id = Mage::app()->getStore()->getId();
        /*
        $store_id = 0;
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) { // store level
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) { // website level
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        } else { // default level
            $store_id = 0;
        }
        echo $store_id;
        */
        $collection = Mage::getModel('quotemail/quotemail')->getCollection()
                ->addStoreFilter($store_id)
                ->addCategoryFilter();
        $brandLogos = $collection->getData();
        
		
		
		return $brandLogos;
    }
	
	
	public function getAllStoreLogos() {
        $store_id = Mage::app()->getStore()->getId();
        /*
        $store_id = 0;
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) { // store level
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) { // website level
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        } else { // default level
            $store_id = 0;
        }
        echo $store_id;
        */
        $collection = Mage::getModel('quotemail/quotemail')->getCollection()
                ->addStoreFilter($store_id)
                ->addCategoryFilter();
        $brandLogos = $collection->getData();
        
		/*total logo items*/
		
		$media = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		
		$_total_logos = count($brandLogos);
		$_page_limit = Mage::app()->getRequest()->getParam('pLimit');		
		$_current_page = Mage::app()->getRequest()->getParam('cPage');
		
		$_total_pages = ceil($_total_logos/$_page_limit);
		/*page start from */
		$pageend = $_current_page * $_page_limit;
		/*page end here */
		$pagestart=$pageend-$_page_limit;
		
		/*looop through the logo items*/
		for($i=$pagestart; $i<$pageend; $i++){
			$_b_logo = $brandLogos[$i];
			
			$output='';
			$output.='<li class=" item ';
			
			if($i==1){
			$output.='first';
			}
			if($i%3==0){
				$output.='last';
				}
			$output.=' fadeIn animated" style="min-height: 100px;padding: 25px 5px 0;margin: 0 25px 10px 0;">
			<img src="'.$media.'quotemail'.$_b_logo['filename'].'" title="'.$_b_logo['title'].'" alt="'.$_b_logo['title'].'"/>
				
			</li>';
		echo $output;	
			}
		
		exit;
    }
	
	
	public function _getTotalLogos() {
        $store_id = Mage::app()->getStore()->getId();        
        $collection = Mage::getModel('quotemail/quotemail')->getCollection()
                ->addStoreFilter($store_id)
                ->addCategoryFilter();
        $brandLogos = $collection->getData();
        		
		return  count($brandLogos);
    }
	

/*function showEmailAttachements()*/
	
	public function showEmailAttachements($quote_id=0){
		
		/*getting db resources*/
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$quote_attachement_table=Mage::getSingleton('core/resource')->getTableName('quotemail_attachements');
    	
		/*show all attachements*/
		$connectionRead->beginTransaction();
		$_attachment_obj = $connectionRead->select()
		                   ->from($quote_attachement_table, array('*'))
						   ->where('quotemail_id=?',$quote_id)
						   ;
		$_attachment_list = $connectionRead->fetchAll($_attachment_obj);
		$connectionRead->commit();
		
		$attachment_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'attachedfiles/';
		$icons_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'upload/fileicons/icons/';
		
		$html='';
		$html.='<script>
				function deleteAttachement(quote_id, quote_attachement_id,file_name){
					jQuery(document).ready(function(){
						
				    var wantdel = confirm(\'Delete file: "\'+file_name+\'"\');
					if(!wantdel){
					return false;
					}
					var form_vars = {formkey:"'.Mage::getSingleton('core/session')->getFormKey().'",
									 quoteid:quote_id,
									 quoteattachementid:quote_attachement_id,
									 filename:file_name,
									 
									 };
						
						jQuery.ajax({
							
						// The link we are accessing.
						url: "'.Mage::getUrl().'quotemail/index/deleteattachement/",	
						type: "post",
						dataType: "html",
						data:form_vars,
						error: function(){							
						},						
						beforeSend: function(){
							
						},						
						complete: function(){
							jQuery(\'#email_attachement_\'+quote_attachement_id).hide();
						},						
						success: function( strData ){
							// Load the content in to the page.
							//console.log( strData );
						}
					
					}
						);
					});
				}
		</script>';
		
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
		
		if(count($_attachment_list)>0){
			$html .='<ul>';
			foreach($_attachment_list as $_image){			
			
			$_exten = substr(basename($_image['email_attachment']),strpos(basename($_image['email_attachment']),'.')+1);
			
			
			$html .='<li id="email_attachement_'.$_image['email_attachment_id'].'" style="float:left; text-align:center; width:100px;"> <a href="'.$attachment_dir.$_image['email_attachment'].'" target="_blank"><img src="'.$icons_dir.$_file_icons[$_exten].'" /><br/>'.$_image['email_attachment'].'</a><br/><div style="cursor:pointer;" onclick="deleteAttachement('.$quote_id.','.$_image['email_attachment_id'].',\''.$_image['email_attachment'].'\')" title="Click to remove '.$_image['email_attachment'].'">X Remove</div>';
			
			$html .='</li>';
			}
		$html .='</ul>';	
		}
		
		
		
		return $html;
		
	}
	
	
	/*function showEmailFiles()*/
	
	public function showEmailFiles($quote_id=0){
		
		/*getting db resources*/
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$quote_attachement_table=Mage::getSingleton('core/resource')->getTableName('quotemail_attachements');
    	
		/*show all attachements*/
		$connectionRead->beginTransaction();
		$_attachment_obj = $connectionRead->select()
		                   ->from($quote_attachement_table, array('*'))
						   ->where('quotemail_id=?',$quote_id)
						   ;
		$_attachment_list = $connectionRead->fetchAll($_attachment_obj);
		$connectionRead->commit();
		
		$attachment_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'attachedfiles/';
		$icons_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'upload/fileicons/icons/';
		
		$html='';
		
		
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
		
		if(count($_attachment_list)>0){
			$html .='<ul>';
			foreach($_attachment_list as $_image){			
			
			$_exten = substr(basename($_image['email_attachment']),strpos(basename($_image['email_attachment']),'.')+1);
			
			
			$html .='<li id="email_attachement_'.$_image['email_attachment_id'].'" style="float:left; text-align:center; width:100px;"> <a href="'.$attachment_dir.$_image['email_attachment'].'" target="_blank"><img src="'.$icons_dir.$_file_icons[$_exten].'" /><br/>'.$_image['email_attachment'].'</a><br/><!--div style="cursor:pointer;" onclick="deleteAttachement('.$quote_id.','.$_image['email_attachment_id'].',\''.$_image['email_attachment'].'\')" title="Click to remove '.$_image['email_attachment'].'">X Remove</div-->';
			
			$html .='</li>';
			}
		$html .='</ul>';	
		}
		
		
		
		return $html;
		
	}
	

}