<?php
/*
	@ Class Renderer_FileImage
	@ prepare column for image loader
*/

class Artis_Vendor_Block_Adminhtml_Vendor_Renderer_TimeSinceOrder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   /*function public renderer*/
   public function render(Varien_Object $row){
	   
	  
	   $html = '';
	   $_row = $row->getData();
	   
	   // Zend_debug::dump($_row['heigh_res_post_date']);
	   
	   $proof_approved_date = new DateTime($_row['proof_approve_date']);
	 //  $timeZone = new DateTimeZone('Australia/Sydney'); 
	   
	   if($_row['heigh_res_post_date'] !='0000-00-00 00:00:00'){
		   $heigh_res_post_date = new DateTime($_row['heigh_res_post_date']);
		 //  $heigh_res_post_date->setTimezone($timeZone);	   
		   $heigh_res_post_date = $heigh_res_post_date->format('Y-m-d H:i:s');
		   
		    $html .='<script>
	  			
				function time_since_hres_file(){
				 jQuery(function(){
					
					var formvars ={ sessionId:\''.Mage::getModel('core/session')->getFormKey().'\',
									from_date:"'.$heigh_res_post_date.'",		
									timezone:"Australia/Sydney",				
					 } ;
					jQuery.ajax({
						
					  url:\''.Mage::getBaseUrl().'vendor/index/getprintingstatus\', 
					  type:\'POST\',
					  data: formvars,
					  dataType:"HTML",
					  beforeSend: function(xhr) { 
							// console.log(xhr);
							//jQuery("#since_hres_approved_'.$_row['entity_id'].'").html("loading...");						
							},
					  success: function(data){	
							//console.log(data);	
							jQuery("#since_hres_approved_'.$_row['entity_id'].'").html(data);			  	
						  },
							}); 
						//console.log(formvars);
					
					});
				}
					setInterval(time_since_hres_file,10000);
	  </script>';
	  $hres_file_since_date= 'loading...';
		   
	   }else{
		   $heigh_res_post_date='Yet Not Uploaded ';
		   $hres_file_since_date= 'Not Uploaded';
		   }
	   
	   $postdate = new DateTime($_row['postdate']);
	   
	   if($_row['proof_approve_date'] != '0000-00-00 00:00:00'){
		   $proof_approved_date = new DateTime($_row['proof_approve_date']);
		 //  $proof_approved_date->setTimezone($timeZone);	   
		   $proof_approved_date = $proof_approved_date->format('Y-m-d H:i:s');
		 
		  $html .='<script>
	  			
				function time_since_proof(){
				 jQuery(function(){
					
					var formvars ={ sessionId:\''.Mage::getModel('core/session')->getFormKey().'\',
									from_date:"'.$proof_approved_date.'",		
									timezone:"Australia/Sydney",				
					 } ;
					jQuery.ajax({
						
					  url:\''.Mage::getBaseUrl().'vendor/index/getprintingstatus\', 
					  type:\'POST\',
					  data: formvars,
					  dataType:"HTML",
					  beforeSend: function(xhr) { 
							// console.log(xhr);
							//jQuery("#since_proof_approved").html("loading...");						
							},
					  success: function(data){	
							//console.log(data);	
							jQuery("#since_proof_approved_'.$_row['entity_id'].'").html(data);			  	
						  },
							}); 
						//console.log(formvars);
					
					});
				}
					setInterval(time_since_proof,10000);
	  </script>';
	  $proof_approve_since_date ='loading...';
		   
	   }else{
		   $proof_approved_date = 'No Proof Approved';
		   $proof_approve_since_date ='Not Uploaded';
		   }
	  	 
	 // $time_left = Mage::getSingleton('vendor/printingstatus')->getPrintingStatus($proof_approved_date);
	  $html .='<font color="#009900">Sydney Timestamp</font>';
	  $html .='<br/><div style="text-align:left; margin-left:5px;">Proof Approved On: &nbsp;&nbsp;<font color="#FF0000">'.$proof_approved_date.'</font>';
	  $html .='<br/>Time From Proof: <font color="#6600CC"><span id="since_proof_approved_'.$_row['entity_id'].'">';	  	 
		  $html .= $proof_approve_since_date;	  
	  $html .='</span></font>';
	  $html .='<br/><br/>Heigh Res Uploaded: &nbsp;&nbsp;<font color="#FF0000">'.$heigh_res_post_date.'</font>';
	  $html .='<br/>H-Res File Uploaded: <font color="#6600CC"><span id="since_hres_approved_'.$_row['entity_id'].'">';	
	  $html .= $hres_file_since_date;	  
	  $html .='</span></font>';	  
	  $html .='</div>';
	   
	   return $html;
	  }
}