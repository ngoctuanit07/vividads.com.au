<?php
/*
	@ Class Renderer_Filevisible
	@ prepare column for filevisible
*/

class Artis_Vendor_Block_Adminhtml_Vendor_Renderer_Filevisible extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   /*function public renderer*/
   public function render(Varien_Object $row){
	   
	   $html = '';
	   $_row = $row->getData();
	   $file_visible = $row->getData($this->getColumn()->getIndex());
	  
	   if($file_visible == 1){
	  	   $html  .= '<font style="color:#009900">Visible</font>';
	   }else{
		    $html='<font style="color:#f00">Not Visible</font>';
		   }
	   
	   return $html;
	   
	   }
	
}