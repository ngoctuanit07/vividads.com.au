<?php

class Artis_Vendor_Model_Printingstatus extends Varien_Object
{
    //const STATUS_ENABLED	= 1;
    //const STATUS_DISABLED	= 2;
    
    const STATUS_PROD	= 'prod';
    const STATUS_PACKED	= 'packed';
    const STATUS_SENT	= 'sent';

    
	
	static public function getOptionArray()
    {
        return array(
            //self::STATUS_ENABLED    => Mage::helper('vendor')->__('Enabled'),
            //self::STATUS_DISABLED   => Mage::helper('vendor')->__('Disabled')
            
            self::STATUS_PROD   => Mage::helper('vendor')->__('Prod'),
            self::STATUS_PACKED   => Mage::helper('vendor')->__('Packed'),
            self::STATUS_SENT   => Mage::helper('vendor')->__('Sent')
        );
    }
	
	
	/*printing status */	
	static public function getPrintingStatus($from_date){
		
		/* fetching current  date*/
		$current_date = new DateTime(date('Y-m-d H:i:s'));	
		$timezone = new DateTimeZone('Australia/Sydney');
		$current_date->setTimezone($timezone);
		//$_current_date = $current_date->format('Y-m-d H:i:s');
		
		/*from date */
		$from_date = new DateTime($from_date);
		$_from_date = $from_date->setTimezone($timezone);		
		$date_difference =  $_from_date->diff($current_date) ;
		
		//Zend_debug::dump($date_difference->h);
		//return $_from_date;
		}
		
		
	/*Vendor Printing Status detail*/	
		
	static public function getVendorPrintingDetail(){
			
			/* if user is registered*/
			$_session_user = Mage::getSingleton('admin/session')->getUser();
			$_user_id = $_session_user->getUserId();
			$_c_user = Mage::getSingleton('admin/user')->load($_user_id);
			$_c_user_role = $_c_user->getRole();
			$tModel = Mage::getSingleton('vendor/printingstatus');
						
		/*if role_name is administrator or vendor*/
		if($_c_user_role['role_name'] == 'Administrators'){
				
			//$vhtml = '<h3>Vendor Detail</h3>';	
			/*Fetch admin users*/
			$users = Mage::getModel('admin/user')->getCollection()->load();			
				$vhtml .= '<table class="printing_status_tbl">';
				$vhtml .='<tr style="border-bottom:1px dotted #ccc;">
							  <td width="200px;" align="left"><b>Printer Name</b></td>
							  <td width="100" align="center"><b>Assigned Files</b></td>
							  <td width="100" align="center"><b>Jobs Qty</b></td>
							  <td width="100" align="center"><b>Pending Jobs</b></td>
							  <td width="100" align="center"><b>In Process</b></td>
							  <td width="100" align="center"><b>Packed</b></td>
							  <td width="100" align="center"><b>Sent</b></td>
							  </tr>';
			
			$i=0;
			foreach($users as $user){				
				 $user_role = $user->getRole()->getData();		
				 	
					if($user_role['role_name']=='Vendor'){
						
						$i++;
						$vendor_users = $user;
						$_user_id = $user->getUserId();
						/*detail of vendor users*/
						 $vhtml .='<tr ';
						 if($i%2==1){ $vhtml .='style="background-color:#e3e3e3"'; }
						 $vhtml .=' >';
						 $vhtml .='<td align="left"><b>'.$user->getUsername().'</b></td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorAssignedJobs($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorAssignedJobsQty($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorPendingJobs($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorInProcessJobs($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorPackedJobs($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorSentJobs($_user_id).'</td>';
						 $vhtml .='</tr>';						
						}
				}
				$vhtml .='</table>';
			}elseif($_c_user_role['role_name'] == 'Vendor'){
			//	$vhtml = '<h3>Vendor Detail</h3>';	
				$vhtml .= '<table class="printing_status_tbl">';
				$vhtml .='<tr style="border-bottom:1px dotted #ccc;">
							  <td width="200px;" align="left"><b></b></td>
							  <td width="100" align="center"><b>Assigned Files</b></td>
							  <td width="100" align="center"><b>Jobs Qty</b></td>
							  <td width="100" align="center"><b>Pending Jobs</b></td>
							  <td width="100" align="center"><b>In Process</b></td>
							  <td width="100" align="center"><b>Packed</b></td>
							  <td width="100" align="center"><b>Sent</b></td>
							  </tr>';
				$vhtml .= '<tr>';
				// $vhtml .='<td align="left"><b>'.$user->getUsername().'</b></td>';
						 $vhtml .='<td align="left"><b>'.$_session_user->getUsername().'</b></td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorAssignedJobs($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorAssignedJobsQty($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorPendingJobs($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorInProcessJobs($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorPackedJobs($_user_id).'</td>';
						 $vhtml .='<td align="center">'.$tModel->getVendorSentJobs($_user_id).'</td>';
				$vhtml .='</tr>';
				$vhtml .= '</table>';			
							
			}
			//	echo $vhtml;
				
			return $vhtml;
			
			}
			
	///function getVendorAssignedJobs()
		
		public function getVendorAssignedJobs($vendor_id=0){
			$_sql_array = 'qty as quantity';
			$total_assinged_jobs = Mage::getModel('vendor/vendor')->getVendorJobsInfo($vendor_id, $_sql_array);			
			//var_dump($total_assinged_jobs);			
			if(count($total_assinged_jobs)>0){					
					foreach($total_assinged_jobs as $total_assinged_job){
						$t_jobs[] = $total_assinged_job['quantity'];
						}		
						if(count($t_jobs)){
							return count($t_jobs);
							}else{
								return 0;
								}
							
			}else{
					return 0;
				}
			}		
	
	///function getVendorAssignedJobsQty()
		
		public function getVendorAssignedJobsQty($vendor_id=0){
			$_sql_array = 'sum(qty) as quantity';
			$total_assinged_jobs_qty = Mage::getModel('vendor/vendor')->getVendorJobsInfo($vendor_id, $_sql_array);			
			
			if(count($total_assinged_jobs_qty)>0){
			
			foreach($total_assinged_jobs_qty as $total_assinged_job_qty){
				
				if(count($total_assinged_job_qty['quantity'])>0){
					
					return $total_assinged_job_qty['quantity'];
									}else{
											return 0;
								}
				}					
			}else{
					return 0;
				} 
			 			
			}	
	
	///function getVendorPendingJobs()
		
		public function getVendorPendingJobs($vendor_id=0){
			$_sql_array = 'progress';	
			$condition = 'progress=\'pending\'';		
			$total_pendingjobs = Mage::getModel('vendor/vendor')->getVendorJobsInfo($vendor_id,$_sql_array, $condition);			
			//var_dump($total_pendingjobs);
			if(count($total_pendingjobs)>0){
				foreach($total_pendingjobs as $total_pendingjob){
				$pendingjobs[] = $total_pendingjob['progress'];
				}
				return 	count($pendingjobs);
					}else{
				return 	0;	
				}
				
			}	
			
		///function getVendorInProcessJobs()
		
		public function getVendorInProcessJobs($vendor_id=0){
			
			$_sql_array = 'progress';	
			$condition = 'progress=\'prod\'';			
			$total_inprocessjobs = Mage::getModel('vendor/vendor')->getVendorJobsInfo($vendor_id,$_sql_array,$condition);			
			//var_dump($total_inprocessjobs);
			if(count($total_inprocessjobs)>0){
				foreach($total_inprocessjobs as $total_inprocessjob){
				$inprocessjobs[] = $total_inprocessjob['progress'];
					}
					return 	count($inprocessjobs);	
				}else{
					return 0;
				}
				
			
		}	
		
		
		///function getVendorPackedJobs()
		
		public function getVendorPackedJobs($vendor_id=0){
			
			$_sql_array = 'progress';	
			$condition = 'progress=\'packed\'';			
			$total_packedjobs = Mage::getModel('vendor/vendor')->getVendorJobsInfo($vendor_id,$_sql_array,$condition);			
			//var_dump($total_packedjobs);
			if(count($total_packedjobs)>0){
				foreach($total_packedjobs as $total_packedjob){
				$packedjobs[] = $total_packedjob['progress'];
				}
				return 	count($packedjobs);
				}else{
					return 0;
					}
				
			
			}	
			
			
		///function getVendorSentJobs()
		
		public function getVendorSentJobs($vendor_id=0){
			
			$_sql_array = 'progress';	
			$condition = 'progress=\'sent\'';			
			$total_sentjobs = Mage::getModel('vendor/vendor')->getVendorJobsInfo($vendor_id,$_sql_array,$condition);			
			//var_dump($total_pendingjobs);
			if(count($total_sentjobs)>0){
				foreach($total_sentjobs as $total_sentjob){
				$sentjobs[] = $total_sentjob['progress'];
					}
					return 	count($sentjobs);
				}else{
					return 	0;
					}
				
			
			}	
					
			
}