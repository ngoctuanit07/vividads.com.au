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

class MD_Vividslider_Model_Vividslider extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('vividslider/vividslider');
    }
	
	
	/* function get all cat_vividsliders*/
	
	public function getAllVividSliders(){
		
		/*getting collection of vividsliders*/
		
		$collection = Mage::getModel('vividslider/vividslider')->getCollection(); 
        $vividsliders = $collection->getData();
		
		return $vividsliders;
		 
		}
		
	/* function get all catsvividsliders*/
	
	public function getAllCatsVividSliders($category_id=0){
		
		if($category_id ==0){
			return false;
			}
		/*getting collection of all emails*/
		$connectionRead 		= Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite 		= Mage::getSingleton('core/resource')->getConnection('core_write');
    	$tbl_vividslider_files	= Mage::getSingleton('core/resource')->getTableName('vividslider_files');
		
		$collection = Mage::getModel('vividslider/vividslider')
							->getCollection()
							->addFieldToFilter('store_id',Mage::app()->getStore()->getStoreId())
							->addFieldToFilter('category_id',$category_id)							 
							->getSelect()
							->join(
									array('t2' => $tbl_vividslider_files),
									'main_table.vividslider_id = t2.vividslider_id',
									't2.*')
							;	
		$collection = $connectionRead->fetchAll($collection);
		
		return $collection;
		 
		}	
	
	

/*function showVividSliderImages()*/
	
	public function showVividSliderImages($slider_id=0){
		
		/*getting db resources*/
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$slider_files_table=Mage::getSingleton('core/resource')->getTableName('vividslider_files');
    	
		/*show all attachements*/
		$connectionRead->beginTransaction();
		$_slider_files_obj = $connectionRead->select()
		                   ->from($slider_files_table, array('*'))
						   ->where('vividslider_id=?',$slider_id)
						   ;
		$_slider_files_list = $connectionRead->fetchAll($_slider_files_obj);
		$connectionRead->commit();
		
		$category_id = Mage::getModel('vividslider/vividslider')
									->load($slider_id)
									->getCategory_id();
		
		
		$media_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$slider_files_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'sliderfiles/'.$category_id.'/';
		$icons_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'upload/fileicons/icons/';
		
		$html='';
		$html.='<script>
				function deleteFile(slider_id, slider_file_id,file_name){
					jQuery(document).ready(function(){
						
				    var wantdel = confirm(\'Delete file: "\'+file_name+\'"\');
					if(!wantdel){
					return false;
					}
					var form_vars = {formkey:"'.Mage::getSingleton('core/session')->getFormKey().'",
									 slider_id:slider_id,
									 slider_file_id:slider_file_id,
									 file_name:file_name,
									 
									 };
						
						jQuery.ajax({
							
						// The link we are accessing.
						url: "'.Mage::getUrl().'vividslider/index/deletefile/",	
						type: "post",
						dataType: "html",
						data:form_vars,
						error: function(){							
						},						
						beforeSend: function(){
							
						},						
						complete: function(){
							jQuery(\'#slider_file_\'+slider_file_id).hide();
						},						
						success: function( strData ){
							// Load the content in to the page.
							//console.log( strData );
						}
					
					}
						);
					});
				}
				
				
				function saveFile(slider_id=0, slider_file_id=0,file_name=0){
					
					jQuery(document).ready(function(){
					
						
				    var wantsave = confirm(\'really want to save this file : "\'+file_name+\'"\');
					var c_slider_file_title = jQuery(\'#slider_file_title_\'+slider_file_id).val();
					var c_slider_url = jQuery(\'#slider_url_\'+slider_file_id).val();
					 
										 
					if(!wantsave){
					return false;
					}
					var form_vars = {formkey:"'.Mage::getSingleton('core/session')->getFormKey().'",
									 slider_id			:slider_id,
									 slider_file_id 	: slider_file_id,
									 file_name 			: file_name,
									 slider_file_title	: c_slider_file_title,
									 slider_url 		: c_slider_url,
									 
									 };
						
						jQuery.ajax({
							
						// The link we are accessing.
						url: "'.Mage::getUrl().'vividslider/index/savefile/",	
						type: "post",
						dataType: "html",
						data:form_vars,
						error: function(){							
						},						
						beforeSend: function(){
						var html = \'<img src="'.$media_dir.'/loading6.gif" height="15"/>\';
							jQuery(\'#slider_info_\'+slider_file_id).html(html);	
						},						
						complete: function(){
							//jQuery(\'#slider_file_\'+slider_file_id).hide();
							var html = \'slider info saved\';
							jQuery(\'#slider_info_\'+slider_file_id).html(html);
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
							 'gif'=>'gif_icon.png',
							 'jpg'=>'jpg_icon.png',
							 'jpeg'=>'jpg_icon.png',							 
							 'eps'=>'other_icon.png'
							 );
		
		if(count($_slider_files_list)>0){
			$html .='<ul>';
			$html .='<li style="float:left; width:200px; border:1px solid #ccc; font-weight:bold; text-align:center; background-color:#f4f4f4;">Slider File</li>
					<li style="float:left; width:200px; border:1px solid #ccc; font-weight:bold;text-align:center; background-color:#f4f4f4;">File Name</li>
					<li style="float:left; width:200px; border:1px solid #ccc; font-weight:bold;text-align:center; background-color:#f4f4f4;">Title</li>
					<li style="float:left; width:200px; border:1px solid #ccc; font-weight:bold;text-align:center; background-color:#f4f4f4;">Link</li>
					<li style="float:left; width:90px; border:1px solid #ccc; font-weight:bold;text-align:center; background-color:#f4f4f4;">Action</li>
					';
			$html .='<li style="clear:both; width:900px;"></li>';		
			
	
	foreach($_slider_files_list as $_image){		
			
			 
			$_exten = substr(basename($_image['slider_file']),strpos(basename($_image['slider_file']),'.')+1);
		
		$file_url = $slider_files_dir.$_image['slider_file'];
		
		$html .='<li id="slider_file_'.$_image['slider_file_id'].'" style="text-align:center; width:900px; clear:both; border-bottom:1px solid #ccc !important; height:85px; "> 
		<ul><li style="float:left; width:200px;"><a href="'.$file_url.'" target="_blank">		
		<img src="'.$file_url.'" height="55"  /></a>
		<br/>
		<div id="slider_info_'.$_image['slider_file_id'].'" style="height:15px; color:green; font-size:12px;"></div>
		</li>
		<li style="float:left;width:200px;height:50px; "><a href="'.$file_url.'" target="_blank">'.$_image['slider_file'].'</a></li>	
		<li style="float:left;width:200px;height:50px; ">
		<input id="slider_file_title_'.$_image['slider_file_id'].'" style="border:1px solid #ccc; width:190px; height:30px;  padding-left:4px;" type="text" value="'.$_image['slider_file_title'].'" /></li>	
		<li style="float:left;width:200px;height:50px; ">
		<input id="slider_url_'.$_image['slider_file_id'].'"  style="border:1px solid #ccc; width:190px; height:30px; padding-left:4px;"  type="text" value="'.$_image['slider_url'].'" /></li>
		<li style="float:left;width:90px;height:50px; text-align:center; padding-left:5px;">
		<div style="cursor:pointer; text-align:center; font-size:10px; float:left; width:40px; color:green;" onclick="saveFile('.$slider_id.', '.$_image['slider_file_id'].', \''.$_image['slider_file'].'\')" title="Click to save \''.$_image['slider_file'].'\'">Save</div>  
		<div style="cursor:pointer; text-align:center;  font-size:11px; float:left; width:40px; color:red;" onclick="deleteFile('.$slider_id.','.$_image['slider_file_id'].',\''.$_image['slider_file'].'\')" title="Click to delete file \''.$_image['slider_file'].'\'">X Del</div>
		</li>';
			
			$html .='</ul></li>';
			}
		$html .='</ul>';	
		}
		
		
		
		return $html;
		
	}
	
	
	/*function showSliderFiles()*/
	
	public function showSliderFiles($slider_id=0){
		
		/*getting db resources*/
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$quote_attachement_table=Mage::getSingleton('core/resource')->getTableName('vividslider_attachements');
    	
		/*show all attachements*/
		$connectionRead->beginTransaction();
		$_attachment_obj = $connectionRead->select()
		                   ->from($quote_attachement_table, array('*'))
						   ->where('vividslider_id=?',$slider_id)
						   ;
		$_attachment_list = $connectionRead->fetchAll($_attachment_obj);
		$connectionRead->commit();
		
		$attachment_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'sliderfiles/';
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
			
			
			$html .='<li id="email_attachement_'.$_image['email_attachment_id'].'" style="float:left; text-align:center; width:100px;"> <a href="'.$attachment_dir.$_image['email_attachment'].'" target="_blank"><img src="'.$icons_dir.$_file_icons[$_exten].'" /><br/>'.$_image['email_attachment'].'</a><br/><!--div style="cursor:pointer;" onclick="deleteAttachement('.$slider_id.','.$_image['email_attachment_id'].',\''.$_image['email_attachment'].'\')" title="Click to remove '.$_image['email_attachment'].'">X Remove</div-->';
			
			$html .='</li>';
			}
		$html .='</ul>';	
		}
		
		return $html;
		
	}
	
	
	///function delete files of slider 
	
	public function deleteFiles($slider_id=0){		
		
		
		/*getting db resource */
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$slider_files_table=Mage::getSingleton('core/resource')->getTableName('vividslider_files');
		
		//$connectionWrite->beginTransaction();
		$_condition = array($connectionWrite->quoteInto('vividslider_id=?', $slider_id));
		
		$deleted = $connectionWrite->delete($slider_files_table, $_condition);		
		//var_dump($deleted);
		//$connectionWrite->commit();
		
		/*delete file from hard disk*/
		
		$category_id = Mage::getModel('vividslider/vividslider')
									->load($slider_id)
									->getCategory_id();
	
		$slider_files_dir = Mage::getBaseDir('media').'\sliderfiles\''.$category_id.'\'';		
		$file_name = $slider_files_dir.$file_name;		
		
		  if (file_exists($file_name)) {
				unlink($file_name);
				 
			  }		
		
		}	

}