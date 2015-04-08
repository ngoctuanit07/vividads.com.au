<?php
/*
	@ Class Renderer_HeighResFileimage
	@ prepare column for image loader
*/

class Artis_Vendor_Block_Adminhtml_Vendor_Renderer_HeighResFileimage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   /*function public renderer*/
   public function render(Varien_Object $row){
	   
	   $html = '';
	   $_row = $row->getData();
	   $file = $row->getData($this->getColumn()->getIndex());
	   if($file !=''){
	   /*the file path of the printed file*/	   
	  // $_file_path = Mage::getBaseUrl('media').'stores/';	   
	   /*$html  .= $_row['heigh_res_post_date'].'
	   			  <div style="width:72px; box-shadow:1px 2px 2px #666; margin:auto; margin-top:5px;margin-bottom:5px;">
	   			  <a href="'.$file.'" target="_blank">
				  <img src="'.$file.'" width="70" alt="'.basename($file).'" /></a>
				  </div>
				  <a href="'.$file.'" target="_blank" title="'.basename($file).'">Download File</a>
				  ';
				  */
				  
		$html  .= $_row['heigh_res_post_date'].'
	   			  
				  <a href="'.$file.'" target="_blank" title="'.basename($file).'">Download (HRes) File</a>
				  ';		  
	   }else{
		   $html='H-Res Not Uploaded';
		   
		   }
	   
	   return $html;
	   
	   }
	
}