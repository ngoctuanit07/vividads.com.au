<?php
class MW_Mwcore_Adminhtml_IndexController extends Mage_Adminhtml_Controller_action
{
    public function indexAction()
    {    		
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function extendtrialAction()
    {       		    	 
    	$module = $this->getRequest()->getParam('module');       		
    	if(Mage::helper('mwcore')->checkExistModule($module))    	
    	{				
    						
    						$strmcore = Mage::getStoreConfig(Mage::helper('mwcore')->encryptModuleName($module));
    						$mod_infs_value =  Mage::helper('core')->decrypt($strmcore);
    						$mod_infs = explode(',',$mod_infs_value);
	    					if(intval($mod_infs[1])==0 || intval($mod_infs[1])==4 )
	    					{
	    						$timenow = strtotime(date('Y-m-d H:i:s'));
	    						$timeend = $timenow + Mage::helper('mwcore')->timeExtendTrial();
	    						$mod_infs[2] = $timeend;  
	    						$mod_infs[1] = 1;
	    						$strmod = implode($mod_infs,',');
	    						$module_infs_value = Mage::helper('core')->encrypt($strmod);
	    						Mage::getModel('core/config')->saveConfig(Mage::helper('mwcore')->encryptModuleName($module),$module_infs_value);
								Mage::getConfig()->reinit();
	    						Mage::helper('mwcore')->enableConfig($module);	
	    						Mage::helper('mwcore')->updatestatus($module,$timeend);		
	    						$result = 	Mage::helper('mwcore')->getCommentExtendTrial($module,$timeend);	    						
	    						echo $result;	    						    						
	    					}
         }
         else
         {						
         						$mod_infs= array();
	    						$timenow = strtotime(date('Y-m-d H:i:s'));
	    						$timeend = $timenow + Mage::helper('mwcore')->timeTrial();
	    						$mod_infs[2] = $timeend;  
	    						$mod_infs[1] = 1;
	    						$mod_infs[0] = $timenow;
	    						$strmod = implode($mod_infs,',');
	    						$module_infs_value = Mage::helper('core')->encrypt($strmod);
	    						Mage::getModel('core/config')->saveConfig(Mage::helper('mwcore')->encryptModuleName($module),$module_infs_value);	
								Mage::getConfig()->reinit();
	    						Mage::helper('mwcore')->enableConfig($module);
	    						Mage::helper('mwcore')->updatestatus($module,$timeend);					
	    						echo Mage::helper('mwcore')->getCommentExtendTrial($module,$timeend); 
         }   
			 
    	return;    	
    }
    
    public function trialAction()
    {
    	$modulename= $this->getRequest()->getParam('module');
    	$modules = Mage::helper('mwcore')->getModules();    	
    	$module = ""; 
    	try {    	
	    	foreach ($modules as $row)
	    	{   	    				
	    		if(md5(strtolower($row).'/')==$modulename)
	    		{	    		
	    			$module = strtolower(str_replace('/','',$row));    		
	    		}    		
	    	}
	    	
	    	if(Mage::helper('mwcore')->checkExistModule($module) && $module!="")    	
	    	{			
	    					$strmcore = Mage::getStoreConfig(Mage::helper('mwcore')->encryptModuleName($module));
    						$mod_infs_value =  Mage::helper('core')->decrypt($strmcore);
    						$mod_infs = explode(',',$mod_infs_value);
	    					if(intval($mod_infs[1])==0 || intval($mod_infs[1])==4)
	    					{
	    						$timenow = strtotime(date('Y-m-d H:i:s'));
	    						$timeend = $timenow + Mage::helper('mwcore')->timeExtendTrial();
	    						$mod_infs[2] = $timeend;  
	    						$mod_infs[1] = 1;
	    						$strmod = implode($mod_infs,',');
	    						$module_infs_value = Mage::helper('core')->encrypt($strmod);
	    						Mage::getModel('core/config')->saveConfig(Mage::helper('mwcore')->encryptModuleName($module),$module_infs_value);	    						
								Mage::getConfig()->reinit();	    						   						
	    						Mage::helper('mwcore')->enableConfig($module);	
	    						Mage::helper('mwcore')->updatestatus($module,$timeend);	
	    						    						    						
	    					}
	         }
	         else if( $module!="" )
	         {					
         						// trial with data null (rarely happen)
         						$mod_infs= array();
	    						$timenow = strtotime(date('Y-m-d H:i:s'));
	    						$timeend = $timenow + Mage::helper('mwcore')->timeTrial();
	    						$mod_infs[2] = $timeend;  
	    						$mod_infs[1] = 1;
	    						$mod_infs[0] = $timenow;
	    						$strmod = implode($mod_infs,',');
	    						$module_infs_value = Mage::helper('core')->encrypt($strmod);
	    						Mage::getModel('core/config')->saveConfig(Mage::helper('mwcore')->encryptModuleName($module),$module_infs_value);	    						
								Mage::getConfig()->reinit();	    						 						
								Mage::helper('mwcore')->enableConfig($module);		    									
	    						Mage::helper('mwcore')->updatestatus($module,$timeend);
	         }  
	         $redirectUrl = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section'=>'mwcore'));
	         $this->_redirectUrl($redirectUrl);
			 return;
    	}
    	catch (Exception $e)
    	{    	
    	 $redirectUrl = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section'=>'mwcore'));
         $this->_redirectUrl($redirectUrl);
		 return;
    	}  
    }
        
    public function activeAction()
    {
    	try {
			 	$module = $this->getRequest()->getParam('module'); 			 	
				$orderid = $this->getRequest()->getParam('orderid');
				$type_site = $this->getRequest()->getParam('site');
				
				if(empty($type_site))
				$type_site = "live_site";

				$type_comment = Mage::helper("mwcore")->getModuleTypeComment($module);
				
		    	if(!Mage::app()->getCookie()->get($module))
		    	{
		    		Mage::app()->getCookie()->set($module,'1',Mage::getStoreConfig('mwcore/timelock'));
		    	}   
		    	else 
		    	{
		    		if(Mage::app()->getCookie()->get($module)< Mage::getStoreConfig('mwcore/timestolock'))
		    		{
		    			Mage::app()->getCookie()->set($module,intval(Mage::app()->getCookie()->get($module))+1,Mage::getStoreConfig('mwcore/timelock'));
		    		}
		    	} 	
		    if(Mage::app()->getCookie()->get($module)==Mage::getStoreConfig('mwcore/timestolock'))
		    	{
		    		echo "You have tried to activate too many times. Please try again in next 60 minutes.";
			    	return;
		    	}
		    	else 
		    	{
    				$domain = Mage::getBaseUrl(); //Mage::helper('mcore')->getDomain();	
	    			if(Mage::helper('mwcore')->activeOnLocal($domain,$type_site))
	    			{    
    					echo "Can not activate on localhost.";
				    	return;	
	    			}
	    			else if(Mage::helper('mwcore')->activeOnDevelopSite($domain,$type_site))
	    			{
	    				echo "Can not activate the extension on the development site.";
					    return;
	    			}
	    			else  
	    			{
	    				if($module!="" && $orderid!="" )
					    	{
					    		 $extend_name = Mage::helper('mwcore')->getModuleEdition($module);
					    		 $newmodule = $module;
					    		 if(!empty($extend_name))
		 							$newmodule = $module.strtolower($extend_name);
		 							
					    		 if (class_exists('SoapClient'))
					    		 {
					    		 	$arr_info_api = array();
					    		 	$arr_info_api = array('module' =>$newmodule, 'orderid' =>$orderid,'domain'=>$domain,'type_site'=>$type_site,'module_system'=>$module,'comment'=>$type_comment);
					    		 	Mage::getModel('core/config')->saveConfig('mwcore/errorSoap',0);							    		 	
									Mage::getConfig()->reinit();
									
						    		$client = new SoapClient(Mage::getStoreConfig('mwcore/activelink'));								 	
						        	$session = $client->login(Mage::getStoreConfig('mwcore/userapi'),Mage::getStoreConfig('mwcore/codeapi'));
								    $result=$client->call($session,'managelicense.verifyPro',array($arr_info_api));	 
								    
								    Mage::helper('mwcore')->getCommentActive($arr_info_api,$result);			
									echo $result[1];
					    		 }
					    		 else 
					    		 {			    		 	
					    		 	Mage::getModel('core/config')->saveConfig('mwcore/errorSoap',1);
									Mage::getConfig()->reinit();
					    		 
					    		 	echo 'It requires to enable PHP SOAP extension to activate online. Or please <a href="http://www.mage-world.com/contacts/">contact us</a> get offline activation key.</div>';
					    		 }
					    	}
					    	else 
					    		echo "Can not connect to server because extension name or order number is null. Please try again later. ";
					    	return;
	    			}
		    	}
    	}
    	catch(Exception $e)
    	{
    		echo "Can not connect to server. Please try again later. Error message: ".$e;
    		return;
    	}
    }
    
     function hideAction()
    {
	    Mage::getSingleton('core/config')->saveConfig('mw/hidesoap',1);
		 Mage::getConfig()->reinit();	   
	    return true;
    }
    
    
   function remindAction()
    {
    	
    	$module =  $this->getRequest()->getParam('module'); 
    	$notification = Mage::getModel('mwcore/notification')->load($module,"extension_key");
    	if($notification)
    	{
    		$notification->setStatus(1);
    		$notification->save();
    	}
    	
    	if(!$this->showMcoreNotification() && !$this->showMessage())
    		echo "hide";
    	else 
    		echo "nohide";
    	return;
    	
    }
    
   function notdisplayAction()
    {
    	$module =  $this->getRequest()->getParam('module'); 
   		$notification = Mage::getModel('mwcore/notification')->load($module,"extension_key");
    	if($notification)
    	{
    		$notification->setStatus(2);
    		$notification->save();
    	}
    	if(!$this->showMcoreNotification() && !$this->showMessage())
    		echo "hide";
    	else 
    		echo "nohide";
    	return;
    	
    }
    
    function specnotdisplayAction()
    {
    	$id =  $this->getRequest()->getParam('id'); 
    	$spec_notice = Mage::getModel('mwcore/notification')->load($id);
    	    	    	
    	if($spec_notice)
    		$spec_notice->setStatus(2);    	
    		$spec_notice->save();
	  
	  $this->removeMessageSession($id);
	  if(!$this->showMcoreNotification() && !$this->showMessage())
    	echo "hide";
      else 
    	echo "nohide";
       return;
    	
	    
    }
    
   function specremindAction()
    {
    	$id =  $this->getRequest()->getParam('id'); 
    	$spec_notice = Mage::getModel('mwcore/notification')->load($id);    
    	if($spec_notice)
    	{	
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$resource = Mage::getSingleton('core/resource');
			$table_name = $resource->getTableName("mwcore/notification");
			$sql = "update ".$table_name." set time_apply = DATE_ADD('".now()."',INTERVAL 1 DAY) where notification_id = ".$id;
			$write->query($sql);
    	} 
    	$this->removeMessageSession($id);
    	if(!$this->showMcoreNotification() && !$this->showMessage())
    		echo "hide";
    	else 
    		echo "nohide";
    	return;
    	
    }
        
	function activemanualAction()
	{
		$module = $this->getRequest()->getParam('module'); 
		$newmodule = $module;
		$keygen = $this->getRequest()->getParam('keygen');
		$type_site = $this->getRequest()->getParam('site');
		$domain = Mage::getBaseUrl();//Mage::helper('mcore')->getDomain();
		
		if(empty($type_site))
			$type_site = "live_site";
		
		$extend_name = Mage::helper('mwcore')->getModuleEdition($module);
		if(!empty($extend_name))
		 $newmodule = $module.strtolower($extend_name);
		
		$arr_info_api = array('module' =>$newmodule, 'domain'=>$domain,'type_site'=>$type_site,'module_system'=>$module);
    		 		
		if(Mage::helper('mwcore')->activeOnLocal($domain,$type_site))
	    	{    
    			echo "Can not activate on local host.";
				return;	
	    	}
	    	else if(Mage::helper('mwcore')->activeOnDevelopSite($domain,$type_site))
	    	{
	    		echo "Can not activate the extension on the development site.";
				 return;
	    	}
	    	else  
	    	{					
				if($module!="" && $keygen !="")
				{
					Mage::helper('mwcore')->getCommentActive($arr_info_api,$keygen);
				}
				else 
				{		
					echo "Activate failed. Please enter a valid activation key.";
				}
	    	}
	}
	
	function showMessage()
	{	
	  $notification = Mage::getModel('mwcore/notification')->getCollection();
	  $notification->addFieldToFilter("current_display",1);
	  if($notification->getSize())		
	   return  true;
	  return  false;
	 	    
	}
	
	function removeMessageSession($id)
	{
		$notification = Mage::getModel('mwcore/notification')->load($id);
		$notification->setCurrentDisplay(0);
		$notification->save();
	}
	
	function showMcoreNotification()
	{		
		$modulesCompany = Mage::helper("mwcore")->getModuleCompany();
		$modulesCompany = array_map('strtolower', $modulesCompany);
	
		$mcore_notif = Mage::getModel('mwcore/notification')->getCollection();
		$mcore_notif->addFieldToFilter('type',array("neq"=>'message'))
					->addFieldToFilter('extension_key',array("in"=>$modulesCompany))
					->addFieldToFilter('status',0);
							
		if($mcore_notif->getSize())
		{		
			return true;
		}
		else
		{
			
			return false;
		}
	}
	
  
}