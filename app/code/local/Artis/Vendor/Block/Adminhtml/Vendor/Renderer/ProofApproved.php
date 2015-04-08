<?php
/*
	@ Class Renderer_ProofApproved
	@ prepare column for ProofApproved
*/

class Artis_Vendor_Block_Adminhtml_Vendor_Renderer_ProofApproved extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   /*function public renderer*/
   public function render(Varien_Object $row){
	   
	   $html = '';	   
	   $proof_approved = $row->getData($this->getColumn()->getIndex());
	//  echo $proof_approved;
	   if($proof_approved == '' || $proof_approved == 'No'){
	   	$html  .= 'Dis-Approved';
	   }else{
		    $html  .= 'Proof Approved';
		   }
	   
	   return $html;
	   
	   }
	
}