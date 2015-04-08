<?php

class Artis_Systemalert_Model_Systemalert extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('systemalert/systemalert');
    }
    
    public function sendalert($eventall,$type,$object,$item_id=0)
    {
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$temptableAlertTask=Mage::getSingleton('core/resource')->getTableName('systemalert_task');
        $temptableAlertCondition=Mage::getSingleton('core/resource')->getTableName('systemalert_condition');
	$temptableAlert=Mage::getSingleton('core/resource')->getTableName('system_alert');
        //echo $type;print_r($eventall);exit;
        foreach($eventall as $event)
        {
	   // echo $event;exit;
                $select = $connectionRead->select()
                        ->from($temptableAlert, array('*'))
                        ->where('task_id=?',$event);
                        
                $result = $connectionRead->fetchAll($select);
                
                foreach($result as $alert)
                {
                    $select = $connectionRead->select()
                            ->from($temptableAlertTask, array('*'))
                            ->where('parent_id=?',$alert['entity_id']);
                            
                    $task = $connectionRead->fetchRow($select);
                    
                    $select = $connectionRead->select()
                            ->from($temptableAlertCondition, array('*'))
                            ->where('parent_id=?',$alert['entity_id']);
                            
                    $Condition = $connectionRead->fetchAll($select);
                    
                    $flag_con = '';
                    foreach($Condition as $con)
                    {
                        if($con['attr_model'] == 'order')
                        {
                            $temptableOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
                            
                            $select = $connectionRead->select()
                            ->from($temptableOrder, array('*'))
                            ->where($con['attr_field'].' '.$con['attr_condition'].'?',$con['attr_value'])
                            ->where('entity_id=?',$object->getId());
                            
                            $Condition = $connectionRead->fetchAll($select);
                            
                            if(count($Condition) > 0)
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= '1';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '1 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '1 and';
                            }
                            else
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= '0';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '0 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '0 and';
                            }
                        }
			else if($con['attr_model'] == 'invoice')
                        { 
                            $temptableOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice');
                            
                            $select = $connectionRead->select()
                            ->from($temptableOrder, array('*'))
                            ->where($con['attr_field'].' '.$con['attr_condition'].'?',$con['attr_value'])
                            ->where('entity_id=?',$object->getId());
                            
                            $Condition = $connectionRead->fetchAll($select);
                           
                            if(count($Condition) > 0)
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= '1';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '1 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '1 and';
                            }
                            else
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= '0';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '0 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '0 and';
                            }
                        }
			else if($con['attr_model'] == 'vendor')
                        { 
                            $temptableOrder=Mage::getSingleton('core/resource')->getTableName('vendor_item');
                            
                            $select = $connectionRead->select()
                            ->from($temptableOrder, array('*'))
                            ->where($con['attr_field'].' '.$con['attr_condition'].'?',$con['attr_value'])
                            ->where('order_id=?',$object->getId())
			    ->where('item_id=?',$item_id);
                            
                            $Condition = $connectionRead->fetchAll($select);
                           
                            if(count($Condition) > 0)
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= '1';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '1 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '1 and';
                            }
                            else
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= '0';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '0 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '0 and';
                            }
                        }
                        else if($con['attr_model'] == 'quotation')
                        {
                            $temptableOrder=Mage::getSingleton('core/resource')->getTableName('quotation');
                            
                            $select = $connectionRead->select()
                            ->from($temptableOrder, array('*'))
                            ->where($con['attr_field'].' '.$con['attr_condition'].'?',$con['attr_value'])
                            ->where('quotation_id=?',$object->getId());
                            
                            $Condition = $connectionRead->fetchAll($select);
                            
                            if(count($Condition) > 0)
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= '1';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '1 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '1 and';
                            }
                            else
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= '0';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '0 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '0 and';
                            }
                        }
                        else if($con['attr_model'] == 'product')
                        {
                            $attr = explode('_',$con['attr_field']);
                            $str = 'get';
                            foreach ($attr as $key=>$val){
                                $val = ucfirst($val);
                                $str .= $val;
                            }
                            
                            $value =  $object->$str();
                            
                             if($value.' '.$con['attr_condition'].$con['attr_value'])
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= ' 1';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '1 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '1 and';
                            }
                            else
                            {
                                if($con['attr_action'] == '')
                                $flag_con .= ' 0';
                                elseif($con['attr_action'] == 'OR')
                                $flag_con .= '0 or';
                                elseif($con['attr_action'] == 'AND')
                                $flag_con .= '0 and';
                            }
                        }
                    }
                   // echo $flag_con;
                   
                    if($flag_con)
                    {
                        $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
                       
                        $connectionWrite->beginTransaction();
                        $data = array();
                        $data['ot_created_at']= NOW();
                        $data['ot_author_user'] = $task['user_id'];
                        $data['ot_target_user'] = $task['target_id']; 
                        $data['ot_caption'] = addslashes($task['caption']); 
                        $data['ot_description'] = addslashes($task['description']); 
                        $data['ot_deadline'] = $finished_date; 
                        
                        $data['ot_entity_type'] = $type; 
                        $data['ot_entity_id'] = $object->getId(); 
                        $data['ot_entity_description'] = addslashes($type.' #'.$object->getIncrementId()); 
                        $data['ot_task_type']= 'Independent';
                        $connectionWrite->insert($temptableOrganiger, $data);
                        $connectionWrite->commit();
                        
        //For chain task
                        $last_id = $connectionWrite->fetchOne('SELECT last_insert_id()');
                        
                            $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                            if($connectionWrite->isTableExists($temptableChain))
                            {
                                $connectionWrite->beginTransaction();
                                $data = array();
                                $data['task_id']=$last_id;
                                $data['order_quote_id']=$object->getId();
                                $data['task_type']='Independent';
                                $connectionWrite->insert($temptableChain,$data);
                                $connectionWrite->commit();
                                
                            }
                            
                        if($task['is_mail'] == 1)
                        {                       
                        
                                ///* Sender Name */
                                $supportName = Mage::getStoreConfig('trans_email/ident_support/name'); 
                                ///* Sender Email */
                                $supportEmail = Mage::getStoreConfig('trans_email/ident_support/email');    
                                    
                                $userdata = Mage::getModel('admin/user')->load($task['target_id']);
                                //$mail = Mage::getModel('core/email');
                                //$mail->setToName($userdata->getName());
                                //$mail->setToEmail($userdata->getEmail());
                                //$mail->setBody($task['description']);
                                //$mail->setSubject($task['caption']);
                                //$mail->setFromEmail($supportEmail);
                                //$mail->setFromName($supportName);
                                //$mail->setType('html');// YOu can use Html or text as Mail format
                                //$mail->send();
                                
                                  $mailTemplate = Mage::getModel('core/email_template'); 
                                    /* @var $mailTemplate Mage_Core_Model_Email_Template */ 
                              
                                    $translate  = Mage::getSingleton('core/translate'); 
                                      
                                    $templateId =$task['email_template_id']; //template for sending customer data 
                                    $template_collection =  $mailTemplate->load($templateId);                                
                                    $template_data = $template_collection->getData(); 
                                    if(!empty($template_data)) 
                                    { 
                                        $templateId = $template_data['template_id']; 
                                        $mailSubject = $template_data['template_subject'];                          
                                          
                                        //fetch sender data from Adminend > System > Configuration > Store Email Addresses > General Contact 
                                        $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email 
                                        $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name 
                                  
                                        $sender = array('name'  => $from_name, 
                                                        'email' => $from_email);
                                        
                                        //$dataall = array('subject'=>$task['caption'],'description'=>$task['description']);
                                          
                                        $vars = array('subject'=>$task['caption'],'description'=>$task['description']); //for replacing the variables in email with data                   
                                        /*This is optional*/ 
                                        $storeId = Mage::app()->getStore()->getId(); 
                                        $model = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject); 
                                        $email = $userdata->getEmail(); 
                                        $name = $userdata->getName();                                            
                                        $model->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);                     
                                        if (!$mailTemplate->getSentSuccess()) { 
                                                throw new Exception(); 
                                        } 
                                        $translate->setTranslateInline(true);
                                    }
                        }
                    }
                }
        
        }
        
        
    }
    
    
    
    public function sendallalert($dataAll)
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
       
       if($dataAll['task_create'] == 'yes')
       {
	$connectionWrite->beginTransaction();
	$data = array();
	$data['ot_created_at']= NOW();
	$data['ot_author_user'] = $dataAll['user_id'];
	$data['ot_target_user'] = $dataAll['target_id']; 
	$data['ot_caption'] = addslashes($dataAll['caption']); 
	$data['ot_description'] = addslashes($dataAll['description']); 
	//$data['ot_deadline'] = $finished_date; 
	
	$data['ot_entity_type'] = $dataAll['type']; 
	$data['ot_entity_id'] = $dataAll['entity_id']; 
	$data['ot_entity_description'] = addslashes($dataAll['entity_description']); 
	$data['ot_task_type']= $dataAll['task_type'];
	$connectionWrite->insert($temptableOrganiger, $data);
	$connectionWrite->commit();
	
//For chain task
	$last_id = $connectionWrite->fetchOne('SELECT last_insert_id()');
	
	    $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
	    if($connectionWrite->isTableExists($temptableChain))
	    {
		$connectionWrite->beginTransaction();
		$data = array();
		$data['task_id']=$last_id;
		$data['order_quote_id']=$dataAll['entity_id'];
		$data['task_type']=$dataAll['task_type'];
		$connectionWrite->insert($temptableChain,$data);
		$connectionWrite->commit();
		
	    }
       }
	    
	    if($dataAll['target_id'] != '')
	    {
		///* Sender Name */
		$supportName = Mage::getStoreConfig('trans_email/ident_support/name'); 
		///* Sender Email */
		$supportEmail = Mage::getStoreConfig('trans_email/ident_support/email');    
		    
		$userdata = Mage::getModel('admin/user')->load($dataAll['target_id']);
		$mail = Mage::getModel('core/email');
		$mail->setToName($userdata->getName());
		$mail->setToEmail($userdata->getEmail());
		$mail->setBody($dataAll['description']);
		$mail->setSubject($dataAll['caption']);
		$mail->setFromEmail($supportEmail);
		$mail->setFromName($supportName);
		$mail->setType('html');// YOu can use Html or text as Mail format
		$mail->send();
	    }
	    
	    if($dataAll['admin'] == 'yes')
	    {
		    $roles_users = Mage::getResourceModel('admin/roles_user_collection');
							
		   $roles = Mage::getModel('admin/roles')->getCollection();
		  foreach($roles as $role):
	   
		  if($role->getRoleName() == 'Administrators'){
			  $temptableUser=Mage::getSingleton('core/resource')->getTableName('admin_role');
			  $sqlUser = $connectionRead->select()
				  ->from($temptableUser, array('*'))
				  ->where("parent_id = '".$role->getId()."'");
			  $chkUser = $connectionRead->fetchAll($sqlUser);
			  foreach($chkUser as $User)
			  {
				  //echo $User['user_id'];
				  $usernow = Mage::getModel('admin/user')->load($User['user_id']);
				 // $email[]= $usernow->getEmail();
				  $mail1 = Mage::getModel('core/email');
				   $mail1->setToName($usernow->getFirstname().' '.$usernow->getLastname());
				   $mail1->setToEmail($usernow->getEmail());
				   $mail1->setBody($dataAll['description']);
				   $mail1->setSubject($dataAll['caption']);
				   $mail1->setFromEmail($supportEmail);
				   $mail1->setFromName($supportName);
				   $mail1->setType('html');// YOu can use Html or text as Mail format
				   $mail1->send();
       
			  }
		  }
		  
		  endforeach;
	    }
	    
		
		//  $mailTemplate = Mage::getModel('core/email_template'); 
		//    /* @var $mailTemplate Mage_Core_Model_Email_Template */ 
		//     
		//    $translate  = Mage::getSingleton('core/translate'); 
		//      
		//    $templateId =$task['email_template_id']; //template for sending customer data 
		//    $template_collection =  $mailTemplate->load($templateId);                                
		//    $template_data = $template_collection->getData(); 
		//    if(!empty($template_data)) 
		//    { 
		//	$templateId = $template_data['template_id']; 
		//	$mailSubject = $template_data['template_subject'];                          
		//	  
		//	//fetch sender data from Adminend > System > Configuration > Store Email Addresses > General Contact 
		//	$from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email 
		//	$from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name 
		//  
		//	$sender = array('name'  => $from_name, 
		//			'email' => $from_email);
		//	
		//	//$dataall = array('subject'=>$task['caption'],'description'=>$task['description']);
		//	  
		//	$vars = array('subject'=>$task['caption'],'description'=>$task['description']); //for replacing the variables in email with data                   
		//	/*This is optional*/ 
		//	$storeId = Mage::app()->getStore()->getId(); 
		//	$model = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject); 
		//	$email = $userdata->getEmail(); 
		//	$name = $userdata->getName();                                            
		//	$model->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);                     
		//	if (!$mailTemplate->getSentSuccess()) { 
		//		throw new Exception(); 
		//	} 
		//	$translate->setTranslateInline(true);
		//    }
	
                  
    }
}