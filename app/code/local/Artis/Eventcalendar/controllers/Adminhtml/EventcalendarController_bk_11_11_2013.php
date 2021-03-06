<?php

class Artis_Eventcalendar_Adminhtml_EventcalendarController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('eventcalendar/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('eventcalendar/eventcalendar')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('eventcalendar_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('eventcalendar/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('eventcalendar/adminhtml_eventcalendar_edit'))
				->_addLeft($this->getLayout()->createBlock('eventcalendar/adminhtml_eventcalendar_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('eventcalendar')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('eventcalendar/eventcalendar');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('eventcalendar')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('eventcalendar')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('eventcalendar/eventcalendar');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $eventcalendarIds = $this->getRequest()->getParam('eventcalendar');
        if(!is_array($eventcalendarIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($eventcalendarIds as $eventcalendarId) {
                    $eventcalendar = Mage::getModel('eventcalendar/eventcalendar')->load($eventcalendarId);
                    $eventcalendar->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($eventcalendarIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $eventcalendarIds = $this->getRequest()->getParam('eventcalendar');
        if(!is_array($eventcalendarIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($eventcalendarIds as $eventcalendarId) {
                    $eventcalendar = Mage::getSingleton('eventcalendar/eventcalendar')
                        ->load($eventcalendarId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($eventcalendarIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'eventcalendar.csv';
        $content    = $this->getLayout()->createBlock('eventcalendar/adminhtml_eventcalendar_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'eventcalendar.xml';
        $content    = $this->getLayout()->createBlock('eventcalendar/adminhtml_eventcalendar_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
    public function getcountryAction()
    {
	extract($_REQUEST);
	
	$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
        {
		$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE entity_id = '".$holiday_country."'";
		$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlHoliday);
		Mage::getSingleton('admin/session', array('name'=>'adminhtml'));	
		
		$path = str_replace('/index.php/','/',Mage::getBaseUrl()).'calender_xml/'.$chkHoliday[0]['file'];
		if($holiday_country != '')
		Mage::getSingleton('admin/session')->setCountryholiday($holiday_country);
		else
		Mage::getSingleton('admin/session')->setCountryholiday(0);
		//Mage::getSingleton('admin/session')->getCountryholiday();
	}
	
	//exit();
	$this->_redirect('*/*/index');
    }
    
    public function countryAction()
    {
	extract($_REQUEST);
	if($country != '')
	Mage::getSingleton('admin/session')->setCountryholi($country);
	else
	Mage::getSingleton('admin/session')->setCountryholi(0);
	//Mage::getSingleton('admin/session')->getCountryholiday();
	
	//exit();
	
    }
    
     /********************************** Custom controller action for ajax *****************************************/
    
	public function GreenYellowRed($number)
	{
		$number--; // working with 0-99 will be easier
		
		if ($number < 50) {
		  // green to yellow
		  $r = floor(255 * ($number / 50));
		  $g = 255;
		
		} else {
		  // yellow to red
		  $r = 255;
		  $g = floor(255 * ((50-$number%50) / 50));
		}
		$b = 0;
		
		return "$r,$g,$b";
	}
	
	public function GreenYellowblue($number)
	{
		$number--; // working with 0-99 will be easier
		
		if ($number < 50) {
		  // green to yellow
		  $r = floor(200 * ($number / 50));
		  $g = 50;
		
		} else {
		  // yellow to red
		  $r = 255;
		  $g = floor(200 * ((50-$number%50) / 50));
		}
		$b = 320;
		
		return "$r,$g,$b";
	}
	
	public function getcalendarformat($year,$month,$day)
	{
		
		if(Mage::getSingleton('core/session')->getFormattype() == 'day')
		$fday_active = 'active';
		if(Mage::getSingleton('core/session')->getFormattype() == '4day')
		$f4day_active = 'active';
		else if(Mage::getSingleton('core/session')->getFormattype() == 'week')
		$fweek_active = 'active';
		else if(Mage::getSingleton('core/session')->getFormattype() == 'year')
		$fyear_active = 'active';
		else 
		$fmonth_active = 'active';
		
		$calendar .= " <div class='calfomat1'>";
		//Add Permistion 'Can view Day wish'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(22,$all_permission))
		{
		
			$calendar .= " <div  class='fday $fday_active' onclick='sc_loadXMLDoc($month,$year,\"".$path."\",".$day.",\"day\");'>        
				<a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($month,$year,\"".$path."\",".$day.",\"day\");'>Day</a>  
			    </div>
			      
			";
		}
		
		//Add Permistion 'Can view 4 Days wish'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(24,$all_permission))
		{
			
			$calendar .= " <div  class='fday $f4day_active' onclick='sc_loadXMLDoc($month,$year,\"".$path."\",".$day.",\"4day\");'>        
				<a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($month,$year,\"".$path."\",".$day.",\"4day\");'>4 Days</a>  
			    </div>
			      
			";
		}
		
		//Add Permistion 'Can view Month wish'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(25,$all_permission))
		{
			$calendar .= "<div  class='fday $fmonth_active' onclick='sc_loadXMLDoc($month,$year,\"".$path."\");'>        
				<a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($month,$year,\"".$path."\");'>Month</a>  
			    </div>
			      
			";
		}
		
		//Add Permistion 'Can view Year wish'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(26,$all_permission))
		{
			$calendar .= "
			<div  class='fday $fyear_active' onclick='sc_loadXMLDoc($month,$year,\"".$path."\",".$day.",\"year\");'>        
			     <a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($month,$year,\"".$path."\",".$day.",\"year\");'>Year</a>  
			 </div>
			   
			";
		}
		
		//Add Permistion 'Can view Month wish'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(23,$all_permission))
		{
			$calendar .= "
			 <div  class='fday $fweek_active' onclick='sc_loadXMLDoc($month,$year,\"".$path."\",".$day.",\"week\");' styele>        
				<a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($month,$year,\"".$path."\",".$day.",\"week\");'>Week</a>  
			    </div>
			      
			";
		}
		
		$calendar .= '</div>';
		
		return $calendar;
	}
	
	
	public function getMycalendar()
	{
		/********************** For My Calender ******************************/
	
		//Add Permistion 'Able to see tasks for following user '
		    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(1,$all_permission) and in_array(27,$all_permission))
		{
			$user_role = Mage::getSingleton('admin/session')->getUser();
			//Get the role id of the user
			$roleId = implode('', $user_role->getRoles());
			
			//Get the role name
			$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
			    
			if($roleName == 'Administrators')
			{
			    
			 
			    $my_cal = '<div class="othercal">My Calendar</div>';
			    $adminUserModel = Mage::getModel('admin/user');
			    $userCollection = $adminUserModel->getCollection()->load();
			    foreach($userCollection as $user)
			    {
				$output = "";
				$usercol = explode(',',Mage::getSingleton('core/session')->getRemoveuser());
				if(in_array($user->getId(),$usercol))
				 $rgb = 'f,f,f';
				 else
				 $rgb = $this->GreenYellowRed($user->getId()*20);
				
			       
				$output .= "<div style='background-color: rgb($rgb);width:12px;height:12px;border:1px;'></div>";
				$my_cal .= '<div class="my_cal" onclick="chkuser('.$user->getId().',\''.Mage::getSingleton('core/session')->getFormattype().'\');">'.$output.$user->getName().'</div>';
			    }
			}
		}
		
		/********************** For My Calender ******************************/
		
		return $my_cal;
	}
	
	public function getUnshipproduct()
	{
		/********************** For Unshipp Product ******************************/
		$my_cal = '<div class="othercal">Products Sku</div>';
	        $temptableSaleitem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		$temptableShipitem=Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_item');
		
		$sqlItem="SELECT * FROM ".$temptableSaleitem." WHERE item_id NOT IN (SELECT order_item_id FROM ".$temptableShipitem.") GROUP BY product_id";
		$chkItems = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlItem);
		
		$my_cal .= '<div class="prosub">';
		
		foreach($chkItems as $chkItem)
		{
			$Product = Mage::getModel('catalog/product')->load($chkItem['product_id']);
			$img = '';
			$pro = explode(',',Mage::getSingleton('core/session')->getPro());
			if(in_array($Product->getId(),$pro))
			$img = '<span style="float: right;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/tick_icon.gif"/></span>';
			
			
			if(in_array($Product->getId(),$pro))
			 $rgb = 'f,f,f';
			 else
			 $rgb = $this->GreenYellowblue($Product->getId()*(5));
			
			$output = "<div style='background-color: rgb($rgb);width:12px;height:12px;border:1px;'></div>";
			if($Product->getSku() != '')
			$my_cal .= '<div class="my_cal" onclick="chksku('.$Product->getId().',\''.Mage::getSingleton('core/session')->getFormattype().'\');">'.$output.$Product->getSku().' '.$img.'</div>'; 
		}
		$my_cal .= '</div>';
		/********************** For Unshipp Product ******************************/
		
		return $my_cal;
	}
	
	public function chkshipcolor($date)
	{
		if(trim(Mage::getSingleton('core/session')->getPro(),' ') != '')
		{
			$chkItems = explode(',',Mage::getSingleton('core/session')->getPro());
			foreach($chkItems as $chkItem)
			{
				$temptableSaleDate=Mage::getSingleton('core/resource')->getTableName('quote_planning');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableSaleDate))
				{
					$sqlDate="SELECT * FROM ".$temptableSaleDate." WHERE product_id = '".$chkItem."' AND shipping_date = '".$date."'";
					$chkDate = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlDate);
					
					if(count($chkDate))
					{
						$Product = Mage::getModel('catalog/product')->load($chkItem);
						$rgb = $this->GreenYellowblue($Product->getId()*(5));
						$color .= '<span style="background-color: rgb('.$rgb.');width:5px;height:12px;border:1px;float: right;"></span>';
					}
				}
			}
		}
		
		return $color;
	}
	
	public function getOthercalendar($year,$month)
	{
		//Add Permistion 'Able to see tasks for following user '
		    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(28,$all_permission))
		{
			$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
			{
				$sqlHoliday="SELECT * FROM ".$temptableHoliday." GROUP BY country_name";
				$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlHoliday);
				$countryh = '<div class="holical"><div class="othercal">Other Calendar</div>';
				foreach($chkHoliday as $holicountry)
				{
				$img = '';
				if(!strpos(Mage::getSingleton('core/session')->getRemoveholi(),$holicountry['country_name']))
				$img = '<span style="float: right;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/tick_icon.gif"/></span>';
				
				$countryh .= '<div class="countryh"  onclick="chkholi(\''.$holicountry['country_name'].'\',\''.$month.'\',\''.$year.'\',\''.Mage::getSingleton('core/session')->getFormattype().'\')" style="cursor:pointer;"><div style="background-color: '.$holicountry['color'].';width: 11px;height: 12px;" class="colorbox"></div><div class="countryN" style="float: left;width: 122px;">'.$holicountry['country_name'].'</div>'.$img.'</div>';
				}
				
				$countryh .= '</div>';
				//Add Permistion 'Able to force mark a day a holiday'
				    
				$all_permission = Mage::getSingleton('core/session')->getAllpermission();
				
				if(in_array(3,$all_permission))
				{
				
				$countryh .= '<div onclick="holiadd();" class="newholi">Add Holiday</div>';
				
				}
			}
		}
		
		return $countryh;
	}
	
	//This ajax function for the 4 day event calendar
	
	public function event4dayAction() {
		
	$day = $_REQUEST["d"];
	$month = $_REQUEST["m"];
	$year = $_REQUEST["y"];
	
	$timezone = Mage::app()->getStore($store)->getConfig('general/locale/timezone');
	//$currentTimezone = @date_default_timezone_get();
	@date_default_timezone_set($timezone);
	
	$currenthour = date('H');
	$currentmint = date('i');
	
	
	Mage::getSingleton('core/session')->setFormattype('4day');
	
	// Create array containing abbreviations of days of week.
	$daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$daysOfWeek1 = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	
	// What is the first day of the month in question?
	$firstDayOfMonth = mktime(0,0,0,$month,$day,$year);
	
	// How many days does this month contain?
	$numberDays = date('t',$firstDayOfMonth);
	
	// Retrieve some information about the first day of the
	// month in question.
	$dateComponents = getdate($firstDayOfMonth);
	
	// What is the name of the month in question?
	$monthName = $dateComponents['month'];
	
	// What is the index value (0-6) of the first day of the
	// month in question.
	$dayOfWeek = $dateComponents['wday'];
	
	//if($daysOfWeek[$dayOfWeek] != 'Sunday')
	//$sundate = date('Y-m-d', strtotime('last Sunday', strtotime($year.'-'.$month.'-'.$day)));
	//else
	$sundate = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));
	
	// Create the table tag opener and day headers
	
	
	       $format = 'Y-m-j'; 
	       $prev_date = date ( $format, strtotime ( '-4 day' . $sundate ) );
	       $prev = explode('-',$prev_date);
	       $prev_date = $prev[2];
	       $prev_month = $prev[1];
	       $prev_year= $prev[0];
	       
	       $next_date = date ( $format, strtotime ( '+4 day' . $sundate ) );
	       $next = explode('-',$next_date);
	       $next_date = $next[2];
	       $next_month = $next[1];
	       $next_year=$next[0];
	       
	       for($m=0;$m<4;$m++)
	       {
			      $alldate[$m] = date ( $format, strtotime ( '+'.$m.' day' . $sundate ) );
			      
			      if($m == 0 or $m == 3)
			      {
					     $current = explode('-',$alldate[$m]);
					     $current_date = $current[2];
					     $current_month = $current[1];
					     $current_year=$current[0];
					     
					     $monthName1[$m] = date("M", mktime(0, 0, 0, $current_month, $current_date, $current_year));
					     $dateName[$m] = $current_date;
					     $yearName[$m] = $current_year;
			      }
			      
			      
	       }
	       
	
	
	/******************* Start Holiday parse ***********************/
	$short = rtrim(Mage::getSingleton('core/session')->getRemoveholi(),',');
	
	if($short != '')
	$sql = " WHERE country_name NOT IN(".$short.")";
	
	$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
        {
		$sqlHoliday="SELECT * FROM ".$temptableHoliday.$sql;
		$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlHoliday);
	}
	
	//$path = str_replace('/index.php/','/',Mage::getBaseUrl()).'calender_xml/'.$chkHoliday[0]['file'];
	
	//$holidays = simplexml_load_file($path);
	
	
	
	foreach($chkHoliday as $holidays)
	{
	 $dateHoli = getdate(strtotime($holidays['h_date']));
	 
	 $day1 = $daysOfWeek1[$dateHoli['wday']];
	 $monthHoli = $dateHoli['month'];
	 $dateHoli = $dateHoli['mday'];
	 
	 //Add Permistion 'Able to force unmark a day a not a holiday'
	    
	$all_permission = Mage::getSingleton('core/session')->getAllpermission();
	
	if(in_array(4,$all_permission))
	{
	 
		if($holidays['addby'] == 'manually')
		$holiremove = '<div onclick="holidelete('.$holidays['entity_id'].');">Remove</div>';
		else
		$holiremove = '';
	}
	 
	   if($holyday[$holidays['h_date']] != '')
	$holyday[$holidays['h_date']] .= '<div style="background-color: '.$holidays['color'].';cursor:pointer;" class="holilink" onclick="holipop(\''.$holidays['h_date'].'\')"> '.$holidays['event'].'</div><div style="display:none;" id="details_'.$holidays['h_date'].'" class="holipop"><div  style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day1.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
	else
	$holyday[$holidays['h_date']] = '<div style="background-color: '.$holidays['color'].';cursor:pointer;" class="holilink" onclick="holipop(\''.$holidays['h_date'].'\')"> '.$holidays['event'].'</div><div style="display:none;" id="details_'.$holidays['h_date'].'" class="holipop"><div style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day1.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
	}
	/******************* End Holiday parse ***********************/
	
	       $datediff = $monthName1[0].' '.$dateName[0].','.$yearName[0].' - '.$monthName1[3].' '.$dateName[3].','.$yearName[3];
	$calendar = "<table width='100%' style='margin-top: -232px;' border='0' cellspacing='0' cellpadding='0' class='sc_calendar_day week_day'>";
	$calendar .= "<caption>".$datediff." </caption><div class='prev-next1'><div style='float:left;' class='prevdiv' onclick='sc_loadXMLDoc($prev_month,$prev_year,\"".$path."\",$prev_date,\"4day\");'>        
				   <a href='javascript:void(0);' class='arr_l' onclick='sc_loadXMLDoc($prev_month,$prev_year,\"".$path."\",$prev_date,\"4day\");'><< Previous Month</a>
			       </div>
			       <div style='float:right;' class='nextdiv' onclick='sc_loadXMLDoc($next_month,$next_year,\"".$path."\",$next_date,\"4day\");'>        
				   <a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($next_month,$next_year,\"".$path."\",$next_date,\"4day\");'>Next Month >></a>  
			       </div></div>  ";
			       
	      
	$calendar .= $this->getcalendarformat($year,$month,$day);
	      
	$calendar .= "<tr>
			   <td colspan='7'>
				  
			   </td></tr>";
	$calendar .= "<tr>";
	
	
	
	// Create the rest of the calendar
	
	// Initiate the day counter, starting with the 1st.
	
	$currentDay = 1;
	
	$calendar .= "</tr><tr>";
	
	
	
	$month = str_pad($month, 2, "0", STR_PAD_LEFT);
	
	
	//$calendar .= '<tr style="border:1px solid #ccc;"><td style="border:1px solid #ccc;"></td>';
	$calendar .= '<tr style="border:1px solid #ccc;">';
	foreach($alldate as $alldateview)
	{
	
	       $current = explode('-',$alldateview);
	       $current_date = $current[2];
	       $current_month = $current[1];
	       $current_year=$current[0];
	       
	       if($current_date < 10)
	       $current_date1 = '0'.$current_date;
	       else
	       $current_date1 = $current_date;
	       
	       $firstDayOfMonth = mktime(0,0,0,$current_month,$current_date,$current_year);
	       $dateComponents = getdate($firstDayOfMonth);
	       
	       $dayOfWeek = $dateComponents['wday'];
	
	       
	       $calendar .= '<td style="border:1px solid #ccc;text-align:center;cursor: pointer;" onclick="daydetails(\''.$current_year.'-'.$current_month.'-'.$current_date1.'\');">'.$daysOfWeek1[$dayOfWeek].' '.$current_month.'/'.$current_date;
	       
		$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
		{
			$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$current_year."-".$current_month."-".$current_date1."%'";
			$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
		}
		if($chkProofs)
		{
				  $calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
		}
	       
	       $calendar .= $this->chkshipcolor($current_year."-".$current_month."-".$current_date1);
	       $calendar .='</td>';        
	}
	
	$calendar .= '</tr>';
	//$calendar .= '<tr style="border:1px solid #ccc;"><td style="border:1px solid #ccc;"></td>';
	$calendar .= '<tr style="border:1px solid #ccc;">';
	
	
	foreach($alldate as $alldateview)
	{
	       $current = explode('-',$alldateview);
	       $current_date = $current[2];
	       $current_month = $current[1];
	       $current_year=$current[0];
	       
	       $firstDayOfMonth = mktime(0,0,0,$current_month,$current_date,$current_year);
	       $dateComponents = getdate($firstDayOfMonth);
	       
	       $dayOfWeek = $dateComponents['wday'];
	
	       
	       /********************************* Start by dev ************************************/
	       
	       //Add Permistion 'Able to see tasks for following user '
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(1,$all_permission))
		{
	     
			    $user = Mage::getSingleton('admin/session');
			    $userId = $user->getUser()->getUserId();
			    
			    $user_role = Mage::getSingleton('admin/session')->getUser();
			     //Get the role id of the user
			    $roleId = implode('', $user_role->getRoles());
			    
			    //Get the role name
			    $roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
			    
			    $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
			    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
				{
			    
					$user = Mage::getSingleton('core/session')->getRemoveuser();
					$user = rtrim($user,',');
					$user = ltrim($user,' ');
					
					    if(trim(Mage::getSingleton('core/session')->getpro(),' ') != '')
					    {
						    $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
						    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
							{
								$sql_pro = ' AND ot_id IN(SELECT task_id FROM '.$temptableChain.' WHERE product_id IN ('.rtrim(Mage::getSingleton('core/session')->getpro(),',').'))';
							}
					    }
					    else
					    {
						    $sql_pro = '';
					    }
					
				       if($roleName == 'Administrators' and $user != '')
					$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user NOT IN(".$user.") AND (ot_deadline = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_created_at = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_notify_date = '".$current_year.'-'.$current_month.'-'.$current_date."')".$sql_pro;
					else if($roleName == 'Administrators')
					$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_deadline = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_created_at = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_notify_date = '".$current_year.'-'.$current_month.'-'.$current_date."')".$sql_pro;
					else
					$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_target_user = '".$userId."' AND ot_deadline = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_created_at = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_notify_date = '".$current_year.'-'.$current_month.'-'.$current_date."') ".$sql_pro;
					
					$chkOrganiger[$alldateview] = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
				}
		}
	       
	//   /********************************* End by dev ************************************/
	       
	       $calendar .='<td style="border:1px solid #ccc;">
							   
									   <div class="mainright1" style="border:1px solid #ccc;height: 20px;" >';
									   
	       foreach($chkOrganiger[$alldateview] as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
				
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				$current_pro = '';
				foreach($chkChain1 as $Chain)
				{
					if($current_pro != $Chain['product_id'])
					{
						$current_pro = $Chain['product_id'];
						$chkId[] = $Chain['task_id'];
					}
					
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
				{
					if($task['ot_create_time']=='00:00:00')
					{
						       if($task['ot_created_at'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Created ';
						       else if($task['ot_notify_date'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Notify  ';
						       else if($task['ot_deadline'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Complte  ';
						       else
						       $msg = '';
						       
							  if($task['ot_entity_type'] == 'product')
							  {
							  $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							  $task_caption = $_newProduct->getSku();
							  }
							  else if($task['ot_entity_type'] == 'order')
							  {
							  $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							  $task_caption = $order->getIncrementId();
							  }
							  else if($task['ot_entity_type'] == 'quote')
							  {
							  $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							  $task_caption = $quote->getIncrementId();
							  }
						       
						       //if($i%2 == 0)
						       //$css1 = 'class="even"';
						       //else
						       //$css1 = 'class="odd"';
						       $last = $i;
						       $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						       $calendar .= '<div class="odd" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
					}
				}
			}
			  
		}
		
		
		
		if($holyday[$current_year.'-'.$current_month.'-'.$current_date] != '')
	       $calendar .=$holyday[$current_year.'-'.$current_month.'-'.$current_date];
	       
	       //Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .='<div class="addtask4" style="width: 100%;height: 106px;background: #CCC;" id="sc_dt__'.$current_date.'" onclick="addtask('.$current_month.','.$current_year.','.$current_date.')"></div>';
		}
	       
	       $calendar .='
							    </div>
					     </td>';
	}
					     
			      $calendar .='</tr>';
	
	for($i=0;$i<=23;$i++)
	{
	       if($i == 0)
	       {
			      $date = 12;
			      $state = 'am';
	       }
	       else if($i > 12)
	       {
			      $date = $i-12;
			      $state = 'pm';
	       }
	       else if($i == 12)
	       {
			      $date = $i;
			      $state = 'pm';
	       }
	       else
	       {
			      $state = 'am';
			      $date = $i;
	       }
	       
	       if($i < 10)
		       $ii = '0'.$i;
	       else
		       $ii = $i;
	       
	       $ftime = $ii.":00:00";
	       $stime = $ii.":30:00";
	       
	       //$calendar .= '<tr style="border:1px solid #ccc;">
	       //<td ><div class="leftdate" style="border:1px solid #ccc;width: 7%; height: 40px;">'.$date.$state.'</div></td>';
	       
	       $calendar .= '<tr style="border:1px solid #ccc;">';
	       
	       foreach($alldate as $alldateview)
	       {
			      
	       $current = explode('-',$alldateview);
	       $current_date = $current[2];
	       $current_month = $current[1];
	       $current_year=$current[0];
	       
	       $calendar .= '<td style="border:1px solid #ccc;">
							    
							    <div class="rightdate1">
									   <div style="border:1px solid #ccc;height: 20px;border-bottom: 0;" >
											  <table class="eventtab" style="height:100%;">';
											  
		if($currenthour == $i and $currentmint < 30)
		$calendar .='<tr style="border:1px solid #ccc;background-color:red;height:2px;"><td></td></tr>';
													 $calendar .='<tr>';
	       foreach($chkOrganiger[$alldateview] as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				
				$current_pro = '';
				foreach($chkChain1 as $Chain)
				{
					if($current_pro != $Chain['product_id'])
					{
						$current_pro = $Chain['product_id'];
						$chkId[] = $Chain['task_id'];
					}
					
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
				{
					if($task['ot_create_time']==$ftime and $ftime != '00:00:00')
					{
					
						       if($task['ot_created_at'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Created ';
						       else if($task['ot_notify_date'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Notify  ';
						       else if($task['ot_deadline'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Complte  ';
						       else
						       $msg = '';
						       
						       if($task['ot_entity_type'] == 'product')
							  {
								  $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								  $task_caption = $_newProduct->getSku();
							  }
							  else if($task['ot_entity_type'] == 'order')
							  {
								  $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								  $task_caption = $order->getIncrementId();
							  }
							  else if($task['ot_entity_type'] == 'quote')
							  {
								  $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								  $task_caption = $quote->getIncrementId();
							  }
						       
						       //if($i%2 == 0)
						       //$css1 = 'class="even"';
						       //else
						       //$css1 = 'class="odd"';
						       $last = $i;
						       $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						       //$calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$date.':00 '.$state.' -- '.substr($task['ot_caption'],0, 9).'</td>';
						       $calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$task_caption.'...'.substr($task['ot_caption'],0, 9).'</td>';
					}
				}
			}
			 
		}
		
		 //Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<td class="tabnew" style="width:2%" onclick="addtask('.$current_month.','.$current_year.','.$current_date.',\''.$ftime.'\')"></td>';
		}
													 
	       $calendar .= '
													 </tr>
											  </table>
									   </div>
									   <div style="border:1px solid #ccc;height: 20px;border-bottom: 0;" >
											  <table class="eventtab" style="height:100%;">';
		if($currenthour == $i and $currentmint >= 30)
		$calendar .='<tr style="border:1px solid #ccc;background-color:red;height:2px;"><td></td></tr>';
													$calendar .='<tr>';
	       foreach($chkOrganiger[$alldateview] as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
					
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				
				$current_pro = '';
				foreach($chkChain1 as $Chain)
				{
					if($current_pro != $Chain['product_id'])
					{
						$current_pro = $Chain['product_id'];
						$chkId[] = $Chain['task_id'];
					}
					
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
				{
					if($task['ot_create_time']==$stime)
					{
					
						       if($task['ot_created_at'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Created ';
						       else if($task['ot_notify_date'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Notify  ';
						       else if($task['ot_deadline'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Complte  ';
						       else
						       $msg = '';
						       
						       if($task['ot_entity_type'] == 'product')
							  {
								  $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								  $task_caption = $_newProduct->getSku();
							  }
							  else if($task['ot_entity_type'] == 'order')
							  {
								  $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								  $task_caption = $order->getIncrementId();
							  }
							  else if($task['ot_entity_type'] == 'quote')
							  {
								  $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								  $task_caption = $quote->getIncrementId();
							  }
						       
						       //if($i%2 == 0)
						       //$css1 = 'class="even"';
						       //else
						       //$css1 = 'class="odd"';
						       $last = $i;
						       $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						       //$calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$date.':30 '.$state.' -- '.substr($task['ot_caption'],0, 9).'</td>';
							$calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$task_caption.'...'.substr($task['ot_caption'],0, 9).'</td>';
					}
				}
			}
			 
		}
		
		 //Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<td class="tabnew" style="width:2%"  onclick="addtask('.$current_month.','.$current_year.','.$current_date.',\''.$stime.'\')"></td>';
		}
													 
	       $calendar .= '
													 </tr>
											  </table>
									   </div>
							    </div>
					     </td>';
	       }
					     
			       $calendar .= '</tr>';
	       
	}
	
	
	
	$calendar .= "</tr>";
	
	$calendar .= "</table><input type='hidden' id='nowdate' value='".$year.'-'.$month.'-'.$day."'/><input type='hidden' id='catformat' value='".Mage::getSingleton('core/session')->getFormattype()."'/>";
	
	/********************** For Other Calender ******************************/
	$countryh = $this->getOthercalendar($year,$month);
	/********************** For Other Calender ******************************/
	
	/********************** For My Calender ******************************/
	$my_cal = $this->getMycalendar();
	/********************** For My Calender ******************************/
	
	/********************** For Product Sku ******************************/
	$unship_pro = $this->getUnshipproduct();
	/********************** For Product Sku ******************************/

	
	$calendar = '<div class="calt" style="margin-top: 29px;">'.$my_cal.'</div><div class="countryt" style="margin-top: 29px;">'.$countryh.'</div><div class="countryt" style="margin-top: 0px;">'.$unship_pro.'</div><div class="maincal">'.$calendar.'</div>';
	
	echo $calendar;
	
	}
	
	
	
	function eventweekAction() {
		
	$day = $_REQUEST["d"];
	$month = $_REQUEST["m"];
	$year = $_REQUEST["y"];
	
	$timezone = Mage::app()->getStore($store)->getConfig('general/locale/timezone');
	//$currentTimezone = @date_default_timezone_get();
	@date_default_timezone_set($timezone);
	
	$currenthour = date('H');
	$currentmint = date('i');
	
	Mage::getSingleton('core/session')->setFormattype('week');
	
	
	// Create array containing abbreviations of days of week.
	$daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$daysOfWeek1 = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	
	// What is the first day of the month in question?
	$firstDayOfMonth = mktime(0,0,0,$month,$day,$year);
	
	// How many days does this month contain?
	$numberDays = date('t',$firstDayOfMonth);
	
	// Retrieve some information about the first day of the
	// month in question.
	$dateComponents = getdate($firstDayOfMonth);
	
	// What is the name of the month in question?
	$monthName = $dateComponents['month'];
	
	// What is the index value (0-6) of the first day of the
	// month in question.
	$dayOfWeek = $dateComponents['wday'];
	
	if($daysOfWeek[$dayOfWeek] != 'Sunday')
	$sundate = date('Y-m-d', strtotime('last Sunday', strtotime($year.'-'.$month.'-'.$day)));
	else
	$sundate = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));
	
	// Create the table tag opener and day headers
	
	
	       $format = 'Y-m-j'; 
	       $prev_date = date ( $format, strtotime ( '-7 day' . $sundate ) );
	       $prev = explode('-',$prev_date);
	       $prev_date = $prev[2];
	       $prev_month = $prev[1];
	       $prev_year= $prev[0];
	       
	       $next_date = date ( $format, strtotime ( '+7 day' . $sundate ) );
	       $next = explode('-',$next_date);
	       $next_date = $next[2];
	       $next_month = $next[1];
	       $next_year=$next[0];
	       
	       for($m=0;$m<7;$m++)
	       {
			      $alldate[$m] = date ( $format, strtotime ( '+'.$m.' day' . $sundate ) );
			      
			      if($m == 0 or $m == 6)
			      {
					     $current = explode('-',$alldate[$m]);
					     $current_date = $current[2];
					     $current_month = $current[1];
					     $current_year=$current[0];
					     
					     $monthName1[$m] = date("M", mktime(0, 0, 0, $current_month, $current_date, $current_year));
					     $dateName[$m] = $current_date;
					     $yearName[$m] = $current_year;
			      }
			      
			      
	       }
	       
	
	
	/******************* Start Holiday parse ***********************/
	$short = rtrim(Mage::getSingleton('core/session')->getRemoveholi(),',');
	
	if($short != '')
	$sql = " WHERE country_name NOT IN(".$short.")";
	
	$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
	{
		$sqlHoliday="SELECT * FROM ".$temptableHoliday.$sql;
		$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlHoliday);
	}
	
	//$path = str_replace('/index.php/','/',Mage::getBaseUrl()).'calender_xml/'.$chkHoliday[0]['file'];
	
	//$holidays = simplexml_load_file($path);
	
	
	
	foreach($chkHoliday as $holidays)
	{
	 $dateHoli = getdate(strtotime($holidays['h_date']));
	 
	 $day1 = $daysOfWeek1[$dateHoli['wday']];
	 $monthHoli = $dateHoli['month'];
	 $dateHoli = $dateHoli['mday'];
	 
	 //Add Permistion 'Able to force unmark a day a not a holiday'
	    
	$all_permission = Mage::getSingleton('core/session')->getAllpermission();
	
	if(in_array(4,$all_permission))
	{
	 
		if($holidays['addby'] == 'manually')
		$holiremove = '<div onclick="holidelete('.$holidays['entity_id'].');">Remove</div>';
		else
		$holiremove = '';
	}
	 
	 
	   if($holyday[$holidays['h_date']] != '')
	$holyday[$holidays['h_date']] .= '<div style="background-color: '.$holidays['color'].';cursor:pointer;" class="holilink" onclick="holipop(\''.$holidays['h_date'].'\')"> '.$holidays['event'].'</div><div style="display:none;" id="details_'.$holidays['h_date'].'" class="holipop"><div  style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day1.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
	else
	$holyday[$holidays['h_date']] = '<div style="background-color: '.$holidays['color'].';cursor:pointer;" class="holilink" onclick="holipop(\''.$holidays['h_date'].'\')"> '.$holidays['event'].'</div><div style="display:none;" id="details_'.$holidays['h_date'].'" class="holipop"><div style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day1.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
	}
	/******************* End Holiday parse ***********************/
	
	       $datediff = $monthName1[0].' '.$dateName[0].','.$yearName[0].' - '.$monthName1[6].' '.$dateName[6].','.$yearName[6];
	$calendar = "<table width='100%' style='margin-top: -232px;' border='0' cellspacing='0' cellpadding='0' class='sc_calendar_day week_day'>";
	$calendar .= "<caption>".$datediff." </caption><div class='prev-next1'><div style='float:left;' class='prevdiv' onclick='sc_loadXMLDoc($prev_month,$prev_year,\"".$path."\",$prev_date,\"week\");'>        
				   <a href='javascript:void(0);' class='arr_l' onclick='sc_loadXMLDoc($prev_month,$prev_year,\"".$path."\",$prev_date,\"week\");'><< Previous Month</a>
			       </div>
			       <div style='float:right;' class='nextdiv' onclick='sc_loadXMLDoc($next_month,$next_year,\"".$path."\",$next_date,\"week\");'>        
				   <a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($next_month,$next_year,\"".$path."\",$next_date,\"week\");'>Next Month >></a>  
			       </div></div>  ";
			       
	  $calendar .= $this->getcalendarformat($year,$month,$day);
	  
	$calendar .= "<tr>
			   <td colspan='7'>
				  
			   </td></tr>";
	$calendar .= "<tr>";
	
	
	
	// Create the rest of the calendar
	
	// Initiate the day counter, starting with the 1st.
	
	$currentDay = 1;
	
	$calendar .= "</tr><tr>";
	
	
	
	$month = str_pad($month, 2, "0", STR_PAD_LEFT);
	
	
	//$calendar .= '<tr style="border:1px solid #ccc;"><td style="border:1px solid #ccc;"></td>';
	$calendar .= '<tr style="border:1px solid #ccc;">';
	foreach($alldate as $alldateview)
	{
	
	       $current = explode('-',$alldateview);
	       $current_date = $current[2];
	       $current_month = $current[1];
	       $current_year=$current[0];
	       
	       if($current_date < 10)
	       $current_date1 = '0'.$current_date;
	       else
	       $current_date1 = $current_date;
	       
	       $firstDayOfMonth = mktime(0,0,0,$current_month,$current_date,$current_year);
	       $dateComponents = getdate($firstDayOfMonth);
	       
	       $dayOfWeek = $dateComponents['wday'];
	
	       
	       $calendar .= '<td style="border:1px solid #ccc;text-align:center;cursor: pointer;"  onclick="daydetails(\''.$current_year.'-'.$current_month.'-'.$current_date1.'\');">'.$daysOfWeek1[$dayOfWeek].' '.$current_month.'/'.$current_date;
	       
	       $temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
	       if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
                {
		$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$current_year."-".$current_month."-".$current_date1."%'";
		$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
		}
		if($chkProofs)
		{
				$calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
		}
		$calendar .= $this->chkshipcolor($current_year."-".$current_month."-".$current_date1);
	       $calendar .='</td>';        
	}
	
	$calendar .= '</tr>';
	//$calendar .= '<tr style="border:1px solid #ccc;"><td style="border:1px solid #ccc;"></td>';
	$calendar .= '<tr style="border:1px solid #ccc;">';
	
	
	foreach($alldate as $alldateview)
	{
	       $current = explode('-',$alldateview);
	       $current_date = $current[2];
	       $current_month = $current[1];
	       $current_year=$current[0];
	       
	       $firstDayOfMonth = mktime(0,0,0,$current_month,$current_date,$current_year);
	       $dateComponents = getdate($firstDayOfMonth);
	       
	       $dayOfWeek = $dateComponents['wday'];
	
	       
	       /********************************* Start by dev ************************************/
		
		//Add Permistion 'Able to see tasks for following user '
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
	    
		if(in_array(1,$all_permission))
		{
			$user = Mage::getSingleton('admin/session');
			$userId = $user->getUser()->getUserId();
			
			$user_role = Mage::getSingleton('admin/session')->getUser();
			 //Get the role id of the user
			$roleId = implode('', $user_role->getRoles());
			
			//Get the role name
			$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
			
			$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
			
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
			{
			
			$user = Mage::getSingleton('core/session')->getRemoveuser();
			$user = rtrim($user,',');
			$user = ltrim($user,' ');
			
			if(trim(Mage::getSingleton('core/session')->getpro(),' ') != '')
			{
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
				$sql_pro = ' AND ot_id IN(SELECT task_id FROM '.$temptableChain.' WHERE product_id IN ('.rtrim(Mage::getSingleton('core/session')->getpro(),',').'))';
				}
			}
			else
			{
				$sql_pro = '';
			}
			
		       if($roleName == 'Administrators' and $user != '')
			$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user NOT IN(".$user.") AND (ot_deadline = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_created_at = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_notify_date = '".$current_year.'-'.$current_month.'-'.$current_date."')".$sql_pro;
			else if($roleName == 'Administrators')
			$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_deadline = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_created_at = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_notify_date = '".$current_year.'-'.$current_month.'-'.$current_date."')".$sql_pro;
			else
			$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_target_user = '".$userId."' AND ot_deadline = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_created_at = '".$current_year.'-'.$current_month.'-'.$current_date."' OR ot_notify_date = '".$current_year.'-'.$current_month.'-'.$current_date."') ".$sql_pro;
			
			$chkOrganiger[$alldateview] = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
			}
		    }
	       
	//   /********************************* End by dev ************************************/
	       
	       $calendar .='<td style="border:1px solid #ccc;">
							  
							    
									   <div class="mainright1" style="border:1px solid #ccc;height: 20px;" >';
									   
	       foreach($chkOrganiger[$alldateview] as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
				
				$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
						    
				$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				
				$current_pro = '';
				foreach($chkChain1 as $Chain)
				{
					if($current_pro != $Chain['product_id'])
					{
						$current_pro = $Chain['product_id'];
						$chkId[] = $Chain['task_id'];
					}
					
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
				{
					if($task['ot_create_time']=='00:00:00')
					{
						       if($task['ot_created_at'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Created ';
						       else if($task['ot_notify_date'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Notify  ';
						       else if($task['ot_deadline'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Complte  ';
						       else
						       $msg = '';
						       
							  if($task['ot_entity_type'] == 'product')
							  {
								  $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								  $task_caption = $_newProduct->getSku();
							  }
							  else if($task['ot_entity_type'] == 'order')
							  {
								  $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								  $task_caption = $order->getIncrementId();
							  }
							  else if($task['ot_entity_type'] == 'quote')
							  {
								  $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								  $task_caption = $quote->getIncrementId();
							  }
						       
						       //if($i%2 == 0)
						       //$css1 = 'class="even"';
						       //else
						       //$css1 = 'class="odd"';
						       $last = $i;
						       $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						       $calendar .= '<div class="odd" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
					}
				}
			}
			  
		}
		
		
		
		if($holyday[$current_year.'-'.$current_month.'-'.$current_date] != '')
	       $calendar .=$holyday[$current_year.'-'.$current_month.'-'.$current_date];
	       
	       //Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .='<div style="width: 100%;height: 11px;background: #CCC;" id="sc_dt__'.$current_date.'" onclick="addtask('.$current_month.','.$current_year.','.$current_date.')"></div>';
		}
	       
	       $calendar .='
							    </div>
					     </td>';
	}
					     
			      $calendar .='</tr>';
	
	for($i=0;$i<=23;$i++)
	{
	       if($i == 0)
	       {
			      $date = 12;
			      $state = 'am';
	       }
	       else if($i > 12)
	       {
			      $date = $i-12;
			      $state = 'pm';
	       }
	       else if($i == 12)
	       {
			      $date = $i;
			      $state = 'pm';
	       }
	       else
	       {
			      $state = 'am';
			      $date = $i;
	       }
	       
	       if($i < 10)
	       $ii = '0'.$i;
	       else
	       $ii = $i;
	       
	       $ftime = $ii.":00:00";
	       $stime = $ii.":30:00";
	       
	       //$calendar .= '<tr style="border:1px solid #ccc;">
	       //<td ><div class="leftdate" style="border:1px solid #ccc;width: 7%; height: 40px;">'.$date.$state.'</div></td>';
	       
	       $calendar .= '<tr style="border:1px solid #ccc;">';
	       
	       foreach($alldate as $alldateview)
	       {
			      
	       $current = explode('-',$alldateview);
	       $current_date = $current[2];
	       $current_month = $current[1];
	       $current_year=$current[0];
	       
	       $calendar .= '<td style="border:1px solid #ccc;">
							    
							    <div class="rightdate1">
									   <div style="border:1px solid #ccc;height: 20px;border-bottom: 0;" >
											  <table class="eventtab" style="height:100%;">';
		if($currenthour == $i and $currentmint < 30)
	       $calendar .='<tr style="border:1px solid #ccc;background-color:red;height:2px;"><td></td></tr>';
													 $calendar .='<tr>';
	       foreach($chkOrganiger[$alldateview] as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
				
				$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
						    
				$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				
				$current_pro = '';
				foreach($chkChain1 as $Chain)
				{
					if($current_pro != $Chain['product_id'])
					{
						$current_pro = $Chain['product_id'];
						$chkId[] = $Chain['task_id'];
					}
					
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
				{
					if($task['ot_create_time']==$ftime and $ftime != '00:00:00')
					{
					
						       if($task['ot_created_at'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Created ';
						       else if($task['ot_notify_date'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Notify  ';
						       else if($task['ot_deadline'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Complte  ';
						       else
						       $msg = '';
						       
						       if($task['ot_entity_type'] == 'product')
							  {
								  $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								  $task_caption = $_newProduct->getSku();
							  }
							  else if($task['ot_entity_type'] == 'order')
							  {
								  $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								  $task_caption = $order->getIncrementId();
							  }
							  else if($task['ot_entity_type'] == 'quote')
							  {
								  $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								  $task_caption = $quote->getIncrementId();
							  }
						       
						       //if($i%2 == 0)
						       //$css1 = 'class="even"';
						       //else
						       //$css1 = 'class="odd"';
						       $last = $i;
						       $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						       //$calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$date.':00 '.$state.' -- '.substr($task['ot_caption'],0, 7).'</td>';
						       $calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$task_caption.'...'.substr($task['ot_caption'],0, 7).'</td>';
					}
				}
			}
			 
		}
		
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<td class="tabnew" style="width:2%" onclick="addtask('.$current_month.','.$current_year.','.$current_date.',\''.$ftime.'\')"></td>';
		}
													 
	       $calendar .= '
													 </tr>
											  </table>
									   </div>
									   <div style="border:1px solid #ccc;height: 20px;border-bottom: 0;" >
											  <table class="eventtab" style="height:100%;">';
											  
		if($currenthour == $i and $currentmint >= 30)
	       $calendar .='<tr style="border:1px solid #ccc;background-color:red;height:2px;"><td></td></tr>';
													$calendar .='<tr>';
	       foreach($chkOrganiger[$alldateview] as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
				$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
						    
				$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				
				$current_pro = '';
				foreach($chkChain1 as $Chain)
				{
					if($current_pro != $Chain['product_id'])
					{
						$current_pro = $Chain['product_id'];
						$chkId[] = $Chain['task_id'];
					}
					
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
				{
					if($task['ot_create_time']==$stime)
					{
					
						       if($task['ot_created_at'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Created ';
						       else if($task['ot_notify_date'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Notify  ';
						       else if($task['ot_deadline'] == $current_year.'-'.$current_month.'-'.$current_date)
						       $msg = 'Complte  ';
						       else
						       $msg = '';
						       
						       if($task['ot_entity_type'] == 'product')
							  {
								  $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								  $task_caption = $_newProduct->getSku();
							  }
							  else if($task['ot_entity_type'] == 'order')
							  {
								  $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								  $task_caption = $order->getIncrementId();
							  }
							  else if($task['ot_entity_type'] == 'quote')
							  {
								  $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								  $task_caption = $quote->getIncrementId();
							  }
						       
						       //if($i%2 == 0)
						       //$css1 = 'class="even"';
						       //else
						       //$css1 = 'class="odd"';
						       $last = $i;
						       $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						       //$calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$date.':30 '.$state.' -- '.substr($task['ot_caption'],0, 6).'</td>';
						       $calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$task_caption.'...'.substr($task['ot_caption'],0, 6).'</td>';
					}
				}
			}
			 
		}
		
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<td class="tabnew" style="width:2%"  onclick="addtask('.$current_month.','.$current_year.','.$current_date.',\''.$stime.'\')"></td>';
		}
													 
	       $calendar .= '
													 </tr>
											  </table>
									   </div>
							    </div>
					     </td>';
	       }
					     
			       $calendar .= '</tr>';
	       
	}
	
	
	
	$calendar .= "</tr>";
	
	$calendar .= "</table><input type='hidden' id='nowdate' value='".$year.'-'.$month.'-'.$day."'/><input type='hidden' id='catformat' value='".Mage::getSingleton('core/session')->getFormattype()."'/>";
	
	
	
	/********************** For Other Calender ******************************/
	$countryh = $this->getOthercalendar($year,$month);
	/********************** For Other Calender ******************************/
	
	/********************** For My Calender ******************************/
	$my_cal = $this->getMycalendar();
	/********************** For My Calender ******************************/
	
	
	/********************** For Product Sku ******************************/
	$unship_pro = $this->getUnshipproduct();
	/********************** For Product Sku ******************************/
	
	$calendar = '<div class="calt" style="margin-top: 29px;">'.$my_cal.'</div><div class="countryt" style="margin-top: 29px;">'.$countryh.'</div><div class="countryt" style="margin-top: 0px;">'.$unship_pro.'</div><div class="maincal">'.$calendar.'</div>';
	
	echo $calendar;
	
	}
	
	
	//This ajax function for the one day event calendar
	
	public function eventdayAction() {
		
	$day = $_REQUEST["d"];
	$month = $_REQUEST["m"];
	$year = $_REQUEST["y"];
	
	if($day<10)
	$day2 = '0'.$day;
	else
	$day2 = $day;
	
	if($month<10)
	$month1 = '0'.$month;
	else
	$month1 = $month;
	
	
	$timezone = Mage::app()->getStore($store)->getConfig('general/locale/timezone');
	//$currentTimezone = @date_default_timezone_get();
	@date_default_timezone_set($timezone);
	
	Mage::getSingleton('core/session')->setFormattype('day');
	
	// Create array containing abbreviations of days of week.
	$daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$daysOfWeek1 = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	
	// What is the first day of the month in question?
	$firstDayOfMonth = mktime(0,0,0,$month,$day,$year);
	
	// How many days does this month contain?
	$numberDays = date('t',$firstDayOfMonth);
	
	// Retrieve some information about the first day of the
	// month in question.
	$dateComponents = getdate($firstDayOfMonth);
	
	// What is the name of the month in question?
	$monthName = $dateComponents['month'];
	
	// What is the index value (0-6) of the first day of the
	// month in question.
	$dayOfWeek = $dateComponents['wday'];
	
	// Create the table tag opener and day headers
	
	
	       $format = 'Y-m-j'; 
	       $prev_date = date ( $format, strtotime ( '-1 day' . $year.'-'.$month.'-'.$day ) );
	       $prev = explode('-',$prev_date);
	       $prev_date = $prev[2];
	       $prev_month = $prev[1];
	       $prev_year= $prev[0];
	       
	       $next_date = date ( $format, strtotime ( '+1 day' . $year.'-'.$month.'-'.$day ) );
	       $next = explode('-',$next_date);
	       $next_date = $next[2];
	       $next_month = $next[1];
	       $next_year=$next[0];
	
	
	/******************* Start Holiday parse ***********************/
	$short = rtrim(Mage::getSingleton('core/session')->getRemoveholi(),',');
	
	if($short != '')
	$sql = " WHERE country_name NOT IN(".$short.")";
	
	$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
	{
		$sqlHoliday="SELECT * FROM ".$temptableHoliday.$sql;
		$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlHoliday);
	}
	
	//$path = str_replace('/index.php/','/',Mage::getBaseUrl()).'calender_xml/'.$chkHoliday[0]['file'];
	
	//$holidays = simplexml_load_file($path);
	
	
	
	foreach($chkHoliday as $holidays)
	{
	 $dateHoli = getdate(strtotime($holidays['h_date']));
	 
	 $day1 = $daysOfWeek1[$dateHoli['wday']];
	 $monthHoli = $dateHoli['month'];
	 $dateHoli = $dateHoli['mday'];
	 
	 //Add Permistion 'Able to force unmark a day a not a holiday'
	    
	$all_permission = Mage::getSingleton('core/session')->getAllpermission();
	
	if(in_array(4,$all_permission))
	{
		if($holidays['addby'] == 'manually')
		$holiremove = '<div onclick="holidelete('.$holidays['entity_id'].');">Remove</div>';
		else
		$holiremove = '';
	}
	 
	   if($holyday[$holidays['h_date']] != '')
	$holyday[$holidays['h_date']] .= '<div style="background-color: '.$holidays['color'].';cursor:pointer;" class="holilink" onclick="holipop1(\''.$holidays['h_date'].'\',\''.$holidays['country_name'].'\')"> '.$holidays['event'].'</div><div style="display:none;" id="details_'.$holidays['h_date'].'_'.$holidays['country_name'].'" class="holipop"><div  style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day1.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
	else
	$holyday[$holidays['h_date']] = '<div style="background-color: '.$holidays['color'].';cursor:pointer;" class="holilink" onclick="holipop1(\''.$holidays['h_date'].'\',\''.$holidays['country_name'].'\')"> '.$holidays['event'].'</div><div style="display:none;" id="details_'.$holidays['h_date'].'_'.$holidays['country_name'].'" class="holipop"><div style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day1.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
	}
	/******************* End Holiday parse ***********************/
	
	$calendar = "<table width='100%' style='margin-top: -232px;' border='0' cellspacing='0' cellpadding='0' class='sc_calendar_day1'>";
	$calendar .= "<caption style='cursor: pointer;' onclick='daydetails(\"$year-$month1-$day2\");'>$daysOfWeek[$dayOfWeek] $monthName $day, $year ";
	
	$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
	{
	$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$year."-".$month1."-".$day2."%'";
	$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
	}
	if($chkProofs)
	{
				 $calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
	}
	
	$calendar .= $this->chkshipcolor($year.'-'.$month.'-'.$day);
	
	$calendar .= "</caption>";
	
	$calendar .="<div class='prev-next1'><div style='float:left;' class='prevdiv' onclick='sc_loadXMLDoc($prev_month,$prev_year,\"".$path."\",$prev_date,\"day\");'>        
				   <a href='javascript:void(0);' class='arr_l' onclick='sc_loadXMLDoc($prev_month,$prev_year,\"".$path."\",$prev_date,\"day\");'><< Previous Month</a>
			       </div>
			       <div style='float:right;' class='nextdiv' onclick='sc_loadXMLDoc($next_month,$next_year,\"".$path."\",$next_date,\"day\");'>        
				   <a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($next_month,$next_year,\"".$path."\",$next_date,\"day\");'>Next Month >></a>  
			       </div></div>  ";
			       
	$calendar .= $this->getcalendarformat($year,$month,$day);
			       
		
	$calendar .= "<tr>
			   <td colspan='7'>
				  
			   </td></tr>";
	$calendar .= "<tr>";
	
	
	
	// Create the rest of the calendar
	
	// Initiate the day counter, starting with the 1st.
	
	$currentDay = 1;
	
	$calendar .= "</tr><tr>";
	
	
	
	$month = str_pad($month, 2, "0", STR_PAD_LEFT);
	
	/********************************* Start by dev ************************************/
	 
	    $user = Mage::getSingleton('admin/session');
	    $userId = $user->getUser()->getUserId();
	    
	    $user_role = Mage::getSingleton('admin/session')->getUser();
	     //Get the role id of the user
	    $roleId = implode('', $user_role->getRoles());
	    
	    //Get the role name
	    $roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
	    
	    $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
	    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
		{
	    
			$user = Mage::getSingleton('core/session')->getRemoveuser();
			$user = rtrim($user,',');
			$user = ltrim($user,' ');
			
			//Add Permistion 'Able to see tasks for following user '
			
			$all_permission = Mage::getSingleton('core/session')->getAllpermission();
			
			if(in_array(1,$all_permission))
			{
			    if(trim(Mage::getSingleton('core/session')->getpro(),' ') != '')
			    {
				    $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				     if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
						$sql_pro = ' AND ot_id IN(SELECT task_id FROM '.$temptableChain.' WHERE product_id IN ('.rtrim(Mage::getSingleton('core/session')->getpro(),',').'))';
					}
			    }
			    else
			    {
				    $sql_pro = '';
			    }
							    
			    if($roleName == 'Administrators' and $user != '')
			     $sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user NOT IN(".$user.") AND (ot_deadline = '".$year.'-'.$month.'-'.$day."' OR ot_created_at = '".$year.'-'.$month.'-'.$day."' OR ot_notify_date = '".$year.'-'.$month.'-'.$day."')".$sql_pro;
			     else if($roleName == 'Administrators')
			     $sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_deadline = '".$year.'-'.$month.'-'.$day."' OR ot_created_at = '".$year.'-'.$month.'-'.$day."' OR ot_notify_date = '".$year.'-'.$month.'-'.$day."')".$sql_pro;
			     else
			     $sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_target_user = '".$userId."' AND ot_deadline = '".$year.'-'.$month.'-'.$day."' OR ot_created_at = '".$year.'-'.$month.'-'.$day."' OR ot_notify_date = '".$year.'-'.$month.'-'.$day."') ".$sql_pro;
			     
			     $chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
			}
		}
	    
	//   /********************************* End by dev ************************************/
	
	//$calendar .= '<tr style="border:1px solid #ccc;">
	//				     <td style="width:100%;border:1px solid #ccc;">
	//						    <div class="leftdate" style="border:1px solid #ccc;width: 7%; height: 20px;" onclick="addtask('.$next_month.','.$next_year.','.$day.')"></div>
	//						    
	//								   <div class="mainright" style="border:1px solid #ccc;height: 78px;" >';
	
	$calendar .= '<tr style="border:1px solid #ccc;">
					     <td style="width:100%;border:1px solid #ccc;">
							   
							    
									   <div class="mainright" style="border:1px solid #ccc;height: 78px;" >';
									   
	       foreach($chkOrganiger as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				$current_pro = '';
				foreach($chkChain1 as $Chain)
				{
					if($current_pro != $Chain['product_id'])
					{
						$current_pro = $Chain['product_id'];
						$chkId[] = $Chain['task_id'];
					}
					
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
				{
					if($task['ot_create_time']=='00:00:00')
					{
						     if($task['ot_created_at'] == $year.'-'.$month.'-'.$day)
						     $msg = 'Created ';
						     else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$day)
						     $msg = 'Notify  ';
						     else if($task['ot_deadline'] == $year.'-'.$month.'-'.$day)
						     $msg = 'Complte  ';
						     else
						     $msg = '';
						     
						     $last = $i;
						     $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						     
						     if($task['ot_entity_type'] == 'product')
						     {
							$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							$task_caption = $_newProduct->getSku();
						     }
						     else if($task['ot_entity_type'] == 'order')
						     {
							$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							$task_caption = $order->getIncrementId();
							
							
		
							
						     }
						     else if($task['ot_entity_type'] == 'quote')
						     {
							$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							$task_caption = $quote->getIncrementId();
						     }
						     
						     //if($i%2 == 0)
						     //$css1 = 'class="even"';
						     //else
						     //$css1 = 'class="odd"';
						     
						     $calendar .= '<div class="odd" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
						     if($proof != '')
						     $calendar .= $proof;
					}
				}
			}
			  
		}
		
		
		
		if($holyday[$year.'-'.$month.'-'.$day] != '')
	       $calendar .=$holyday[$year.'-'.$month.'-'.$day];
	       
	       
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .='<div style="width: 100%;height: 11px;background: #CCC;" id="sc_dt__'.$day.'" onclick="addtask('.$next_month.','.$next_year.','.$day.')"></div>';
		}
	       
	       $calendar .='
							    </div>
					     </td>
			      </tr>';
	
#disabled this loop temporary
//	if($enabled)
	for($i=0;$i<=23;$i++)
	{

	       if($i == 0)
	       {
			      $date = 12;
			      $state = 'am';
	       }
	       else if($i > 12)
	       {
			      $date = $i-12;
			      $state = 'pm';
	       }
	       else if($i == 12)
	       {
			      $date = $i;
			      $state = 'pm';
	       }
	       else
	       {
			      $state = 'am';
			      $date = $i;
	       }
	       
	       $currenthour = date('H');
	       $currentmint = date('i');
	       
	       if($i < 10)
	       $ii = '0'.$i;
	       else
	       $ii = $i;
	       
	       $ftime = $ii.":00:00";
	       $stime = $ii.":30:00";
	       
	       
	       
	//       $calendar .= '<tr style="border:1px solid #ccc;">
	//				     <td style="width:100%;border:1px solid #ccc;">
	//						    <div class="leftdate" style="border:1px solid #ccc;width: 7%; height: 40px;">'.$date.$state.'</div>
	//						    <div class="rightdate">
	//								   <div style="border:1px solid #ccc;height: 20px;border-bottom: 0;" >
	//										  <table class="eventtab" style="width:100%;height:100%;">';
	
		$calendar .= '<tr style="border:1px solid #ccc;">
					     <td style="width:100%;border:1px solid #ccc;">
							    <div class="rightdate">
									   <div style="border:1px solid #ccc;height: 20px;border-bottom: 0;" >
											  <table class="eventtab" style="width:100%;height:100%;">';
		if($currenthour == $i and $currentmint < 30)
	       $calendar .='<tr style="border:1px solid #ccc;background-color:red;height:2px;"><td></td></tr>';
	       
													  $calendar .='<tr>';
	       foreach($chkOrganiger as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' GROUP BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				foreach($chkChain1 as $Chain)
				{
					$chkId[] = $Chain['task_id'];
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1)
				{
				      if($task['ot_create_time']==$ftime and $ftime != '00:00:00')
				      {
				      
						     if($task['ot_created_at'] == $year.'-'.$month.'-'.$day)
						     $msg = 'Created ';
						     else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$day)
						     $msg = 'Notify  ';
						     else if($task['ot_deadline'] == $year.'-'.$month.'-'.$day)
						     $msg = 'Complte  ';
						     else
						     $msg = '';
						     
						     if($task['ot_entity_type'] == 'product')
						     {
							$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							$task_caption = $_newProduct->getSku();
						     }
						     else if($task['ot_entity_type'] == 'order')
						     {
							$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							$task_caption = $order->getIncrementId();
						     }
						     else if($task['ot_entity_type'] == 'quote')
						     {
							$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							$task_caption = $quote->getIncrementId();
						     }
						     
						     //if($i%2 == 0)
						     //$css1 = 'class="even"';
						     //else
						     //$css1 = 'class="odd"';
						     $last = $i;
						     $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						     //$calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$date.':00 '.$state.' -- '.substr($msg.' '.$task['ot_caption'],-12).'</td>';
						     $calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.substr($msg.' '.$task_caption.'...'.$task['ot_caption'],-12).'</td>';
				      }
				}
			}
		}
		
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<td class="tabnew" style="width:2%" onclick="addtask('.$month.','.$year.','.$day.',\''.$ftime.'\')">'.($KK).'</td>';
		}
													 
	       $calendar .= '
													 </tr>
											  </table>
									   </div>
									   <div style="border:1px solid #ccc;height: 20px;border-bottom: 0;" >
											  <table class="eventtab" style="width:100%;height:100%;">';
			if($currenthour == $i and $currentmint >= 30)
	       $calendar .='<tr style="border:1px solid #ccc;background-color:red;height:2px;"><td></td></tr>';
													$calendar .='<tr>';
	       foreach($chkOrganiger as $task)
		{
			if($task['ot_entity_type'] != 'product')
			{
				$chkId = array();
				$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
				if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
				{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
				}
				
				$current_pro = '';
				foreach($chkChain1 as $Chain)
				{
					if($current_pro != $Chain['product_id'])
					{
						$current_pro = $Chain['product_id'];
						$chkId[] = $Chain['task_id'];
					}
					
				}
				if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
				{
					if($task['ot_create_time']==$stime)
					{
					
						       if($task['ot_created_at'] == $year.'-'.$month.'-'.$day)
						       $msg = 'Created ';
						       else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$day)
						       $msg = 'Notify  ';
						       else if($task['ot_deadline'] == $year.'-'.$month.'-'.$day)
						       $msg = 'Complte  ';
						       else
						       $msg = '';
						       
							if($task['ot_entity_type'] == 'product')
						       {
							  $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							  $task_caption = $_newProduct->getSku();
						       }
						       else if($task['ot_entity_type'] == 'order')
						       {
							  $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							  $task_caption = $order->getIncrementId();
						       }
						       else if($task['ot_entity_type'] == 'quote')
						       {
							  $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							  $task_caption = $quote->getIncrementId();
						       }
						       
						       //if($i%2 == 0)
						       //$css1 = 'class="even"';
						       //else
						       //$css1 = 'class="odd"';
						       $last = $i;
						       $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						       //$calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$date.':30 '.$state.' -- '.substr($task['ot_caption'],0, 9).'</td>';
						       $calendar .= '<td class="tabold" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> '.$task_caption.'...'.substr($task['ot_caption'],0, 9).'</td>';
					}
				}
			}
			 
		}
		
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<td class="tabnew" style="width:2%"  onclick="addtask('.$month.','.$year.','.$day.',\''.$stime.'\')">'.($KK).'</td>';
		}
													 
	       $calendar .= '
													 </tr>
											  </table>
									   </div>
							    </div>
					     </td>
			      </tr>';
	       
	}
	
	
	
        $calendar .= '</tr><tr><td class="tabnew" style="width:2%">'.$this->fetchdaydetails()."</td></tr>";

	$calendar .= "</table><input type='hidden' id='nowdate' value='".$year.'-'.$month.'-'.$day."'/><input type='hidden' id='catformat' value='".Mage::getSingleton('core/session')->getFormattype()."'/>";
	
	
	/********************** For Other Calender ******************************/
	$countryh = $this->getOthercalendar($year,$month);
	/********************** For Other Calender ******************************/
	
	/********************** For My Calender ******************************/
	$my_cal = $this->getMycalendar();
	/********************** For My Calender ******************************/
	
	/********************** For Product Sku ******************************/
	$unship_pro = $this->getUnshipproduct();
	/********************** For Product Sku ******************************/
	
	$calendar = '<div class="calt" style="margin-top: 29px;">'.$my_cal.'</div><div class="countryt" style="margin-top: 29px;">'.$countryh.'</div><div class="countryt" style="margin-top: 0px;">'.$unship_pro.'</div><div class="maincal">'.$calendar.'</div>';
	
	echo $calendar;
	
	}
	
	//This ajax function for the year event calendar
	
	public function eventyearAction() {
		
		$day = $_REQUEST["d"];
		$month = $_REQUEST["m"];
		$year = $_REQUEST["y"];
		
		
		Mage::getSingleton('core/session')->setFormattype('year');
		
		// Create array containing abbreviations of days of week.
		$daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
		$daysOfWeek1 = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
		
		 $yaer_months = array(
			1   =>  'January',
			2   =>  'February',
			3   =>  'March',
			4   =>  'April',
			5   =>  'May',
			6   =>  'June',
			7   =>  'July',
			8   =>  'August',
			9   =>  'September',
			10  =>  'October',
			11  =>  'November',
			12  =>  'December'
		    );
		
		// What is the first day of the month in question?
		$firstDayOfMonth = mktime(0,0,0,$month,$day,$year);
		
		// How many days does this month contain?
		$numberDays = date('t',$firstDayOfMonth);
		
		// Retrieve some information about the first day of the
		// month in question.
		$dateComponents = getdate($firstDayOfMonth);
		
		// What is the name of the month in question?
		$monthName = $dateComponents['month'];
		
		// What is the index value (0-6) of the first day of the
		// month in question.
		$dayOfWeek = $dateComponents['wday'];
		
		// Create the table tag opener and day headers
		
		
		$format = 'Y-m-j'; 
		$prev_date = date ( $format, strtotime ( '-1 year' . $year.'-'.$month.'-'.$day ) );
		$prev = explode('-',$prev_date);
		$prev_date = $prev[2];
		$prev_month = $prev[1];
		$prev_year= $prev[0];
		
		$next_date = date ( $format, strtotime ( '+1 year' . $year.'-'.$month.'-'.$day ) );
		$next = explode('-',$next_date);
		$next_date = $next[2];
		$next_month = $next[1];
		$next_year=$next[0];
		
		
		/******************* Start Holiday parse ***********************/
		$short = rtrim(Mage::getSingleton('core/session')->getRemoveholi(),',');
		
		if($short != '')
		$sql = " WHERE country_name NOT IN(".$short.")";
		
		$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
		{
			$sqlHoliday="SELECT * FROM ".$temptableHoliday.$sql;
			$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlHoliday);
		}
		
		//$path = str_replace('/index.php/','/',Mage::getBaseUrl()).'calender_xml/'.$chkHoliday[0]['file'];
		
		//$holidays = simplexml_load_file($path);
		
		
		
		foreach($chkHoliday as $holidays)
		{
		$dateHoli = getdate(strtotime($holidays['h_date']));
		
		$day1 = $daysOfWeek1[$dateHoli['wday']];
		$monthHoli = $dateHoli['month'];
		$dateHoli = $dateHoli['mday'];
		
		//Add Permistion 'Able to force unmark a day a not a holiday'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(4,$all_permission))
		{
			if($holidays['addby'] == 'manually')
			$holiremove = '<div onclick="holidelete('.$holidays['entity_id'].');">Remove</div>';
			else
			$holiremove = '';
		}
		
		if($holyday[$holidays['h_date']] != '')
		$holyday[$holidays['h_date']] .= '<div style="background-color: '.$holidays['color'].';cursor:pointer;width:100%;" class="holilink1" onclick="holipop1(\''.$holidays['h_date'].'\',\''.$holidays['country_name'].'\')"> H </div><div style="display:none;" id="details_'.$holidays['h_date'].'_'.$holidays['country_name'].'" class="holipop1"><div  style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day1.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
		else
		$holyday[$holidays['h_date']] = '<div style="background-color: '.$holidays['color'].';cursor:pointer;width:100%;" class="holilink1" onclick="holipop1(\''.$holidays['h_date'].'\',\''.$holidays['country_name'].'\')"> H </div><div style="display:none;" id="details_'.$holidays['h_date'].'_'.$holidays['country_name'].'" class="holipop1"><div style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day1.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
		}
		
		//print_r($holyday); 
		/******************* End Holiday parse ***********************/
		
		
		$calendar = "<table width='100%' style='margin-top: -232px;' border='0' cellspacing='0' cellpadding='0' class='sc_calendar_day'>";
		$calendar .= "<caption> $year </caption><div class='prev-next1'><div style='float:left;' class='prevdiv' onclick='sc_loadXMLDoc($prev_month,$prev_year,\"".$path."\",$prev_date,\"year\");'>        
				<a href='javascript:void(0);' class='arr_l' onclick='sc_loadXMLDoc($prev_month,$prev_year,\"".$path."\",$prev_date,\"year\");'><< Previous Month</a>
			    </div>
			    <div style='float:right;' class='nextdiv' onclick='sc_loadXMLDoc($next_month,$next_year,\"".$path."\",$next_date,\"year\");'>        
				<a href='javascript:void(0);' class='arr_r' onclick='sc_loadXMLDoc($next_month,$next_year,\"".$path."\",$next_date,\"year\");'>Next Month >></a>  
			    </div></div>  ";
			    
		$calendar .= $this->getcalendarformat($year,$month,$day);
		
			
		$calendar .= "<tr>
			<td colspan='7'>
			       
			</td></tr>";
		$calendar .= "<tr>";
		
		
		
		// Create the rest of the calendar
		
		// Initiate the day counter, starting with the 1st.
		
		$currentDay = 1;
		
		$calendar .= "</tr><tr>";
		
		
		
		$month = str_pad($month, 2, "0", STR_PAD_LEFT);
		
		
		$calendar .= "<td><table  cellspacing='0' cellpadding='0' class='yeartab'>";
		
		$i_count = 1;
		for($m=1;$m<=4;$m++)
		{
			$calendar .= "<tr>";
			for($n=1;$n<=3;$n++)
			{
				if($i_count < 10)
				$i_m = '0'.$i_count;
				else
				$i_m = $i_count;
				
				
				if($i_count == 12)
				$css = 'last';
				
				
				$firstDayOfMonth = mktime(0,0,0,$i_count,1,$year);

				// How many days does this month contain?
				$numberDays = date('t',$firstDayOfMonth);
			   
				// Retrieve some information about the first day of the
				// month in question.
				$dateComponents = getdate($firstDayOfMonth);
			   
				// What is the name of the month in question?
				$monthName = $dateComponents['month'];
			   
				// What is the index value (0-6) of the first day of the
				// month in question.
				$dayOfWeek = $dateComponents['wday'];
				
				$month_end = strtotime('last day of this month', $year.'-'.$i_m.'-01');
				$last_date = date('d', $month_end);
				
				
				$calendar .= "<td><table  cellspacing='0' cellpadding='0' class='yearbox $css'>";
				
				$calendar .= '<th colspan="7" style="text-align:center;">'.$yaer_months[$i_count].'</th><tr>';
				
				foreach($daysOfWeek1 as $day) {
					if($day == 'Sat')
					$calendar .= "<td class='dayhead last'>$day</td>";
					else
					$calendar .= "<td class='dayhead'>$day</td>";
				   }
				   
				$calendar .= "</tr><tr>";

				// The variable $dayOfWeek is used to
				// ensure that the calendar
				// display consists of exactly 7 columns.
			   
				if ($dayOfWeek > 0) { 
				     $calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>";
				}
				
				if ($dayOfWeek == 7) {

					$dayOfWeek = 0;
					$calendar .= "</tr><tr>";
			 
				   }
				 $currentDay = 1;
				 
				while ($currentDay <= $numberDays) {
					if ($dayOfWeek == 7) {

						$dayOfWeek = 0;
						$calendar .= "</tr><tr >";
				 
					   }
					   
					if($currentDay < 10)
					$cdate = '0'.$currentDay;
					else
					$cdate = $currentDay;
					
					
					if($numberDays == $currentDay or $dayOfWeek == 6)
					$csstd = 'last';
					else if($currentDay == 1)
					$csstd = 'first';
					else
					$csstd = '';
					
					
					
						
				
					   
					/********************************* Start by dev ************************************/
					//Add Permistion 'Able to see tasks for following user '
	    
					$all_permission = Mage::getSingleton('core/session')->getAllpermission();
					
					if(in_array(1,$all_permission))
					{
						$user = Mage::getSingleton('admin/session');
						$userId = $user->getUser()->getUserId();
						
						$user_role = Mage::getSingleton('admin/session')->getUser();
						//Get the role id of the user
						$roleId = implode('', $user_role->getRoles());
						
						//Get the role name
						$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
						
						$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
						
						if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
						{
						
							$user = Mage::getSingleton('core/session')->getRemoveuser();
							$user = rtrim($user,',');
							$user = ltrim($user,' ');
							
							if(trim(Mage::getSingleton('core/session')->getpro(),' ') != '')
							{
								$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
								if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
								{
								$sql_pro = ' AND ot_id IN(SELECT task_id FROM '.$temptableChain.' WHERE product_id IN ('.rtrim(Mage::getSingleton('core/session')->getpro(),',').'))';
								}
							}
							else
							{
								$sql_pro = '';
							}
							
							if($roleName == 'Administrators' and $user != '')
							$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user NOT IN(".$user.") AND (ot_deadline = '".$year.'-'.$i_m.'-'.$cdate."' OR ot_created_at = '".$year.'-'.$i_m.'-'.$cdate."' OR ot_notify_date = '".$year.'-'.$i_m.'-'.$cdate."')".$sql_pro;
							else if($roleName == 'Administrators')
							$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_deadline = '".$year.'-'.$i_m.'-'.$cdate."' OR ot_created_at = '".$year.'-'.$i_m.'-'.$cdate."' OR ot_notify_date = '".$year.'-'.$i_m.'-'.$cdate."')".$sql_pro;
							else
							$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_target_user = '".$userId."' AND ot_deadline = '".$year.'-'.$i_m.'-'.$cdate."' OR ot_created_at = '".$year.'-'.$i_m.'-'.$cdate."' OR ot_notify_date = '".$year.'-'.$i_m.'-'.$cdate."') ".$sql_pro;
							
							$chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
						}
					}
					
					//   /********************************* End by dev ************************************/
					
					
		
					 $calendar .= "<td class='datebox $csstd'><div style='position: absolute;cursor:pointer;' onclick='daydetails(\"$year-$i_m-$cdate\");'>$currentDay</div>";
					 $calendar .= $this->chkshipcolor($year.'-'.$i_m.'-'.$cdate);
					$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
					{
						$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$year.'-'.$i_m.'-'.$cdate."%'";
						$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
					}
					if($chkProofs)
					{
								 $calendar .= "<div  class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></div>";
					}
					 
					if($holyday[$year.'-'.$i_m.'-'.$cdate] != '')
				       $calendar .=$holyday[$year.'-'.$i_m.'-'.$cdate];
				       $count = 0;
				       foreach($chkOrganiger as $task)
					{
						if($task['ot_entity_type'] != 'product')
						{
							$chkId = array();
							$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
							if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
							{
							
								$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
										    
								$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
							}
							$current_pro = '';
							foreach($chkChain1 as $Chain)
							{
								if($current_pro != $Chain['product_id'])
								{
									$current_pro = $Chain['product_id'];
									$chkId[] = $Chain['task_id'];
								}
								
							}
							if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
							{
							 	$count++;
							}
						}
					}
				       
				       if($count > 0)
				       {
				       
						$calendar .= '<div class="more" style="text-align: left;margin-top: 20px; width: 23px; font-size: 8px;color: green;font-weight: bold; cursor:pointer;" onclick="expand1(\''.$cdate.'\',\''.$i_m.'\');"> '.$count.' Task </div><div class="expand1" id="expand_'.$cdate.'_'.$i_m.'">
						
						<div class="stickhead" ><span style="float: left;"><strong>'.$daysOfWeek[$dayOfWeek].', '.$monthName.' '.$cdate.'</strong></span><span style="float: right;cursor : pointer;" onclick="close_expan();">X</span></div>';
						$i = 1;
						foreach($chkOrganiger as $task)
						{
							if($task['ot_entity_type'] != 'product')
							{
								$chkId = array();
								$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
								
								if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
								{
								
								$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
										    
								$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
								}
								$current_pro = '';
								foreach($chkChain1 as $Chain)
								{
									if($current_pro != $Chain['product_id'])
									{
										$current_pro = $Chain['product_id'];
										$chkId[] = $Chain['task_id'];
									}
									
								}
								if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
								{
						
									if($task['ot_created_at'] == $date)
									$msg = 'Created ';
									else if($task['ot_notify_date'] == $date)
									$msg = 'Notify  ';
									else if($task['ot_deadline'] == $date)
									$msg = 'Complte  ';
									else
									$msg = '';
									
									if($task['ot_entity_type'] == 'product')
									{
									$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
									$task_caption = $_newProduct->getSku();
									}
									else if($task['ot_entity_type'] == 'order')
									{
									$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
									$task_caption = $order->getIncrementId();
									}
									else if($task['ot_entity_type'] == 'quote')
									{
									$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
									$task_caption = $quote->getIncrementId();
									}
									
									$rgb = $this->GreenYellowRed($task['ot_target_user']*20);
									$calendar .= '<div class="even"  style="text-align: left;cursor:pointer;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div><div onclick="deletetask('.$task['ot_id'].');">Remove</div>';
								}
							}
						
						}
						$calendar .= '</div>';
		    
				       }
	       
	       //Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
	       
					 $calendar .= "<div style='width:100%;height:100%;' onclick='addtask(".$i_count.",".$year.",".$currentDay.")'></div></td>";
		}
					$currentDay++;
					$dayOfWeek++;
				}
				if ($dayOfWeek != 7) {

						$remainingDays = 7 - $dayOfWeek;
          $calendar .= "<td colspan='$remainingDays' class='last'>&nbsp;</td>";
				 
					   }
				
				
				$calendar .= "</tr>";
				   
				
				$calendar .= "</table></td>";
				$i_count++;
			}
			$calendar .= "</tr>";
		}
		
		$calendar .= "</table></td>";
		
		
		$calendar .= "</tr>";
		
		$calendar .= "</table><input type='hidden' id='nowdate' value='".$year."-".date('m')."-".date('d')."'/><input type='hidden' id='catformat' value='".Mage::getSingleton('core/session')->getFormattype()."'/>";
		
		
		/********************** For Other Calender ******************************/
		$countryh = $this->getOthercalendar($year,$month);
		/********************** For Other Calender ******************************/
		
		/********************** For My Calender ******************************/
		$my_cal = $this->getMycalendar();
		/********************** For My Calender ******************************/
		/********************** For Product Sku ******************************/
		$unship_pro = $this->getUnshipproduct();
		/********************** For Product Sku ******************************/
	
		$calendar = '<div class="calt" style="margin-top: 29px;">'.$my_cal.'</div><div class="countryt" style="margin-top: 29px;">'.$countryh.'</div><div class="countryt" style="margin-top: 0px;">'.$unship_pro.'</div><div class="maincal" id="maincal">'.$calendar.'</div>';
		
		echo $calendar;
		
	}
	
	//This ajax function for the month event calendar
	
	public function eventmonthAction() {
		
		$day = $_REQUEST["d"];
		$month = $_REQUEST["m"];
		$year = $_REQUEST["y"];
		
		
		Mage::getSingleton('core/session')->setFormattype('month');
	
	     // Create array containing abbreviations of days of week.
	     $daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	     $daysOfWeek1 = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
	
	     // What is the first day of the month in question?
	     $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
	
	     // How many days does this month contain?
	     $numberDays = date('t',$firstDayOfMonth);
	
	     // Retrieve some information about the first day of the
	     // month in question.
	     $dateComponents = getdate($firstDayOfMonth);
	
	     // What is the name of the month in question?
	     $monthName = $dateComponents['month'];
	
	     // What is the index value (0-6) of the first day of the
	     // month in question.
	     $dayOfWeek = $dateComponents['wday'];
	
	     // Create the table tag opener and day headers
	     
	     
	    $todays_date=date("d",time());
	    $current_month=date("m",time());
	    
	    $next_month=$month;
	    $next_year=$year;
	    
	    $prev_month=$month;
	    $prev_year=$year;
	    
	    
	    $next_month++;
	    if($next_month>12)
	    {
	       $next_month=1;
	       $next_year++;
	    }
	    
	    $prev_month--;
	    if($prev_month<1)
	    {
	       $prev_month=12;
	       $prev_year--;
	    }
	    
	    
	    /******************* Start Holiday parse ***********************/
	      $short = rtrim(Mage::getSingleton('core/session')->getRemoveholi(),',');
	      
	      if($short != '')
	      $sql = " WHERE country_name NOT IN(".$short.")";
	   
	      $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
	      if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoliday))
		{
	      $sqlHoliday="SELECT * FROM ".$temptableHoliday.$sql;
	      $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlHoliday);
		}
	      
	      //$path = str_replace('/index.php/','/',Mage::getBaseUrl()).'calender_xml/'.$chkHoliday[0]['file'];
	    
	      //$holidays = simplexml_load_file($path);
	      
	      
	     
	      foreach($chkHoliday as $holidays)
	      {
		 $dateHoli = getdate(strtotime($holidays['h_date']));
		 
		 $day = $daysOfWeek1[$dateHoli['wday']];
		 $monthHoli = $dateHoli['month'];
		 $dateHoli = $dateHoli['mday'];
		 
		 //Add Permistion 'Able to force unmark a day a not a holiday'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(4,$all_permission))
		{
			 
			 if($holidays['addby'] == 'manually')
			$holiremove = '<div onclick="holidelete('.$holidays['entity_id'].');">Remove</div>';
			else
			$holiremove = '';
		}
		 
		   if($holyday[$holidays['h_date']] != '')
		$holyday[$holidays['h_date']] .= '<div style="background-color: '.$holidays['color'].';cursor:pointer;" class="holilink" onclick="holipop(\''.$holidays['h_date'].'\')"> '.$holidays['event'].'</div><div style="display:none;" id="details_'.$holidays['h_date'].'" class="holipop"><div  style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div>'.$holiremove.'</div>';
		else
		$holyday[$holidays['h_date']] = '<div style="background-color: '.$holidays['color'].';cursor:pointer;" class="holilink" onclick="holipop(\''.$holidays['h_date'].'\')"> '.$holidays['event'].'</div><div style="display:none;" id="details_'.$holidays['h_date'].'" class="holipop"><div style="cursor:pointer" class="holiclose" onclick="holiclose()">X</div><div style="color:'.$holidays['color'].';font-weight:bold;">'.$holidays['event'].'</div><div>'.$day.', '.$monthHoli.' '.$dateHoli.'</div><div><strong>Calenar</strong> '.$holidays['country_name'].' Holiday</div> '.$holiremove.'</div>';
	      }
	      /******************* End Holiday parse ***********************/
	      
	     
	
	     $calendar = "<table width='100%' style='margin-top: -232px;' border='0' cellspacing='0' cellpadding='0' class='sc_calendar'>";
	     $calendar .= "<caption>$monthName $year </caption><div class='prev-next'><div style='float:left;' class='prevdiv' onclick='sc_prev_month($prev_month,$prev_year,\"".$path."\");'>        
					   <a href='javascript:void(0);' class='arr_l' onclick='sc_prev_month($prev_month,$prev_year,\"".$path."\");'><< Previous Month</a>
				       </div>
				       <div style='float:right;' class='nextdiv' onclick='sc_next_month($next_month,$next_year,\"".$path."\");'>        
					   <a href='javascript:void(0);' class='arr_r' onclick='sc_next_month($next_month,$next_year,\"".$path."\");'>Next Month >></a>  
				       </div></div>  ";
				       
		$calendar .= $this->getcalendarformat($year,$month,date('d'));		       
		
				   
				   
	     $calendar .= "<tr>
				   <td colspan='7'>
					  
				   </td></tr>";
	     $calendar .= "<tr>";
	
	     // Create the calendar headers
	
	     foreach($daysOfWeek1 as $day) {
		  $calendar .= "<th class='sc_header'>$day</th>";
	     } 
	
	     // Create the rest of the calendar
	
	     // Initiate the day counter, starting with the 1st.
	
	     $currentDay = 1;
	
	     $calendar .= "</tr><tr>";
	
	     // The variable $dayOfWeek is used to
	     // ensure that the calendar
	     // display consists of exactly 7 columns.
	
	     if ($dayOfWeek > 0) { 
		 // $calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>";
		 
		   $format = 'Y-m-j'; 
		    $firstdate = date ( $format, strtotime ( '-'.$dayOfWeek.' day' . $year.'-'.$month.'-01' ) );
		    $numberDaysoflast = date('t',strtotime ( '-'.$dayOfWeek.' day' . $year.'-'.$month.'-01' ));
		    $currentday2 = explode('-',$firstdate);
		    
		    
		  
		  $predate = 0;
		  for($predate = 0;$predate < $dayOfWeek;$predate++)
		  {
		   $currentday3 = $currentday2[2]+$predate;
		    $start_time=date("Y-m-d H:i:s",mktime(0,0,0,$prev_month,$currentday3,$prev_year));
		$end_time=date("Y-m-d H:i:s",mktime(23,59,59,$prev_month,$currentday3,$prev_year));
		
		$dateComponents1 = getdate(mktime(0,0,0,$prev_month,$currentday3,$prev_year));
		    $monthName1 = $dateComponents1['month'];
		    $dayOfWeek1 = $dateComponents1['wday'];
		
		//All query Start
	    
		$temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		$sqlSaleOrder="SELECT count(entity_id) as ctct FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
		try {
		    $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
		    
		
		
		
		} catch (Exception $e){
		//echo $e>getMessage();
		}
		
		$tot_order_placed=0;
		foreach($chkSaleOrder as $res_chkSaleOrder) 
		{
		   $tot_order_placed= $res_chkSaleOrder["ctct"];
		}
		
		$temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		$sqlSaleOrder="SELECT entity_id FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
		try {
		    $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
		    
		
		
		
		} catch (Exception $e){
		//echo $e>getMessage();
		}
		
		$ord_id=array();
		$show_id=array();
		
		foreach($chkSaleOrder as $res_chkSaleOrder) 
		{
		   $ord_id[]=$res_chkSaleOrder["entity_id"];
		   $show_id[]=str_pad($res_chkSaleOrder["entity_id"],9,"100000000",STR_PAD_LEFT);
		   
		   
		}
		
		if(count($ord_id)>0)
		{
		    $clclcl="active_view";
		}
		else
		{
		    $clclcl="";
		}
		
		/********************************* Start by dev ************************************/
		//Add Permistion 'Able to see tasks for following user '
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(1,$all_permission))
		{
			$user = Mage::getSingleton('admin/session');
			$userId = $user->getUser()->getUserId();
			
			$user_role = Mage::getSingleton('admin/session')->getUser();
			 //Get the role id of the user
			$roleId = implode('', $user_role->getRoles());
			
			//Get the role name
			$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
			
			$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
			{
			
				$user = Mage::getSingleton('core/session')->getRemoveuser();
				$user = rtrim($user,',');
				$user = ltrim($user,' ');
				
				if(trim(Mage::getSingleton('core/session')->getpro(),' ') != '')
				{
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sql_pro = ' AND ot_id IN(SELECT task_id FROM '.$temptableChain.' WHERE product_id IN ('.rtrim(Mage::getSingleton('core/session')->getpro(),',').'))';
					}
				}
				else
				{
					$sql_pro = '';
				}
				
			       if($roleName == 'Administrators' and $user != '')
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user NOT IN(".$user.") AND (ot_deadline = '".$prev_year.'-'.$prev_month.'-'.$currentday3."' OR ot_created_at = '".$prev_year.'-'.$prev_month.'-'.$currentday3."' OR ot_notify_date = '".$prev_year.'-'.$prev_month.'-'.$currentday3."')".$sql_pro;
				else if($roleName == 'Administrators')
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_deadline = '".$prev_year.'-'.$prev_month.'-'.$currentday3."' OR ot_created_at = '".$prev_year.'-'.$prev_month.'-'.$currentday3."' OR ot_notify_date = '".$prev_year.'-'.$prev_month.'-'.$currentday3."')".$sql_pro;
				else
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_target_user = '".$userId."' AND ot_deadline = '".$prev_year.'-'.$prev_month.'-'.$currentday3."' OR ot_created_at = '".$prev_year.'-'.$prev_month.'-'.$currentday3."' OR ot_notify_date = '".$prev_year.'-'.$prev_month.'-'.$currentday3."')".$sql_pro;
				
				$chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
			}
			foreach($chkOrganiger as $task)
			{
			    if($k < 5)
			    {
			      $calendar1[$currentDay] .= '<div style="text-align: left;"> -- '.$task['ot_caption'].'</div> ';
			    }
			    $k++;
			}
		}
		/********************************* End by dev ************************************/
		
		
	
		  // Seventh column (Saturday) reached. Start a new row.
	
		  if ($dayOfWeek == 7) {
	
		       $dayOfWeek = 0;
		       $calendar .= "</tr><tr>";
	
		  }
		  //print_r($show_id);
		  $currentDayRel = str_pad($currentday3, 2, "0", STR_PAD_LEFT);
	    
		  $currentMonthRel = str_pad($prev_month, 2, "0", STR_PAD_LEFT);
		  
		  $date = "$prev_year-$currentMonthRel-$currentDayRel";
		  
		  
		    if($holyday[$date] != '')
		    $css = 'holiday';
		    else
		    $css = '';
		    
		
		    
		  if($todays_date==$currentDay && $current_month==$prev_month)
		  {
		    
		    
		    
		    $calendar .= '<td class="sc_day '.$clclcl.'" id="sc_day_'.$currentday3.'" rel="'.$date.'"><div style="position:relative;  width:100%; height:100%;"><span id="dt__'.$currentday3.'"  class="ddc activated_day '.$css.'" onclick="daydetails(\''.$date.'\');">'.$currentday3;
		    
			$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
			{
				$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$date."%'";
				$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
			}
			if($chkProofs)
			{
					  $calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
			}
			$calendar .= $this->chkshipcolor($date);
		
		   $calendar .= '</span>';
			$i=1;
			$last = 0;
			foreach($chkOrganiger as $task)
			{
			    
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
						if($i < 3)
						{
							if($task['ot_created_at'] == $date)
							$msg = 'Created ';
							else if($task['ot_notify_date'] == $date)
							$msg = 'Notify  ';
							else if($task['ot_deadline'] == $date)
							$msg = 'Complte  ';
							else
							$msg = '';
							
							if($task['ot_entity_type'] == 'product')
							{
								$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								$task_caption = $_newProduct->getSku();
							}
							else if($task['ot_entity_type'] == 'order')
							{
								$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								$task_caption = $order->getIncrementId();
							}
							else if($task['ot_entity_type'] == 'quote')
							{
								$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								$task_caption = $quote->getIncrementId();
							}
							
							//if($i%2 == 0)
							//$css1 = 'class="even"';
							//else
							//$css1 = 'class="odd"';
							
							$last = $i;
							$rgb = $this->GreenYellowRed($task['ot_target_user']*20);
							$calendar .= '<div class="even" style="text-align: left;cursor:pointer;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
						}
						$i++;
					}
				}
			    
			    
			    
			    
			}
			
			if($i-$last-1 > 0)
			{
			    $calendar .= '<div class="more" style="text-align: left; cursor:pointer;" onclick="expand('.$currentday3.');"> + '.($i-$last-1).' more</div><div class="expand" id="expand_'.$currentday3.'">
			    
			    <div class="stickhead" ><span style="float: left;"><strong>'.$daysOfWeek[$dayOfWeek1].', '.$monthName1.' '.$currentday3.'</strong></span><span style="float: right;" onclick="close_expan()">X</span></div>';
			    
			    foreach($chkOrganiger as $task)
			    {
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
			      
						if($task['ot_created_at'] == $date)
						$msg = 'Created ';
						else if($task['ot_notify_date'] == $date)
						$msg = 'Notify  ';
						else if($task['ot_deadline'] == $date)
						$msg = 'Complte  ';
						else
						$msg = '';
						
						    if($task['ot_entity_type'] == 'product')
						    {
							    $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							    $task_caption = $_newProduct->getSku();
						    }
						    else if($task['ot_entity_type'] == 'order')
						    {
							    $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							    $task_caption = $order->getIncrementId();
						    }
						    else if($task['ot_entity_type'] == 'quote')
						    {
							    $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							    $task_caption = $quote->getIncrementId();
						    }
						
						$last = $i;
						$rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						$calendar .= '<div class="even"  style="text-align: left;cursor:pointer;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
					}
				}
			       
			    }
			    $calendar .= '</div>';
			}
			
			if($holyday[$date] != '')
		       $calendar .=$holyday[$date];
		       
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<div style="width:100%; height:100%;" id="sc_dt__'.$currentday3.'" onclick="addtask('.$prev_month.','.$prev_year.','.$currentday3.')">';
		}
		    
		    $calendar .= '</div>
		    
			<div class="overlay" id="sc_ov__'.$currentday3.'"></div>
			    <div class="outer taskcl" id="sc_ou__'.$currentday3.'">
				<div class="cross" id="sc_cr__'.$currentday3.'" onclick="sc_close_pop(this.id);">X</div>
				<div class="inner">
				    
				    ';
				       $calendar .= '<div>Organiger Task</div>';  
					foreach($chkOrganiger as $task)
					{
						if($task['ot_entity_type'] != 'product')
						{
							$chkId = array();
							$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
							if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
							{
							$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
									    
							$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
							}
							$current_pro = '';
							foreach($chkChain1 as $Chain)
							{
								if($current_pro != $Chain['product_id'])
								{
									$current_pro = $Chain['product_id'];
									$chkId[] = $Chain['task_id'];
								}
								
							}
							if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
							{
								if($task['ot_created_at'] == $date)
								$msg = 'Created ';
								else if($task['ot_notify_date'] == $date)
								$msg = 'Notify  ';
								else if($task['ot_deadline'] == $date)
								$msg = 'Complte  ';
								else
								$msg = '';
								
								    if($task['ot_entity_type'] == 'product')
								    {
									    $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
									    $task_caption = $_newProduct->getSku();
								    }
								    else if($task['ot_entity_type'] == 'order')
								    {
									    $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
									    $task_caption = $order->getIncrementId();
								    }
								    else if($task['ot_entity_type'] == 'quote')
								    {
									    $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
									    $task_caption = $quote->getIncrementId();
								    }
								
						
								$taskModule = Mage::getModel('Organizer/Task')->load($task['ot_id']);
								$calendar .= '<div style="text-align: left;"><a href="'.$taskModule->getEntityLink().'" target="_blank"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</a></div>
								
								<div class="desc">      '.$task['ot_description'].'</div>';
							}
						}
					    
					    
					}
				   
			
			$calendar .='<div class="tot_ord_det">';
					    $ctctct=0;
					   foreach($show_id as $vvl)
					   {
					    //print_r($show_id);
					    $Order = Mage::getModel('sales/order')->load($ord_id[$ctctct]);
					    // print_r($Order);
					     
					    $CustomerAdrId=$Order['shipping_address_id'];
					    $address = Mage::getModel('sales/order_address')->load($CustomerAdrId);
				 
					    $Street = $address['street'];
					    $city = $address['city'];
					    $region = $address['region'];
					    $PostCode = $address['postcode'];
					    $country_code = $address['country_id'];
					    $countryModel = Mage::getModel('directory/country')->load($country_code);
					    $country_name = $countryModel->getName();
					     
					    $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
					    $sqlSaleOrder="SELECT created_at FROM ".$temptableSaleOrder." WHERE entity_name='shipment' and parent_id='".$ord_id[$ctctct]."'";
					    try {
						$chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
						
					    
					    
					    
					    } catch (Exception $e){
					    //echo $e>getMessage();
					    }
					    
					    $ord_pay_dt="";
					    foreach($chkSaleOrder as $res_chkSaleOrder) 
					    {
					       $ord_pay_dt= $res_chkSaleOrder["created_at"];
					       
					       
					    }
					     
					     
					     
					    
					     
					    // exit;
					     
					     $calendar .='<div class="ord_lnk"><a target="_blank" href="'.Mage::getBaseUrl()."/admin/sales_order/view/order_id/".$ord_id[$ctctct].'">Order : '.$vvl.'</a>
					     
					     
						<div class="bil_ship_con"><span>Shipping Country : </span>
						    '.$country_name.'
						    
						</div>
						<div class="bil_ship_state"><span>Shipping State : </span>
						    '.$region.'
						    
						</div>
						<div class="bil_ship_state"><span>Order payment date : </span>
						    '.$ord_pay_dt.'
						    
						</div>
						<div class="bil_ship_state"><span>Delivery Deadline : </span>
						    
						    
						</div>
						
						
					     
					     
					     </div>';
					     
					     $ctctct++;
					   }
					   
					  
			$calendar .='</div></div></div>';
					  
		    $calendar .='
					   
					    
		    
		    
		    </div>
		    
		    </td>';  
		  }
		  else
		  {
		    $calendar .= '<td class="sc_day '.$clclcl.'" id="sc_day_'.$currentday3.'" rel="'.$date.'"><div style="position:relative; width:100%; height:100%;"><span class="ddc '.$css.'" onclick="daydetails(\''.$date.'\');">'.$currentday3;
		    
			$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
			{
				$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$date."%'";
				$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
			}
			if($chkProofs)
			{
					  $calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
			}
			
		   $calendar .= '</span>';
		    
			   
			    $i=1;
			    $last = 0;
			foreach($chkOrganiger as $task)
			{
			   
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
						if($i < 3)
						{
							if($task['ot_created_at'] == $date)
							$msg = 'Created ';
							else if($task['ot_notify_date'] == $date)
							$msg = 'Notify  ';
							else if($task['ot_deadline'] == $date)
							$msg = 'Complte  ';
							else
							$msg = '';
							
							if($task['ot_entity_type'] == 'product')
							{
								$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								$task_caption = $_newProduct->getSku();
							}
							else if($task['ot_entity_type'] == 'order')
							{
								$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								$task_caption = $order->getIncrementId();
							}
							else if($task['ot_entity_type'] == 'quote')
							{
								$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								$task_caption = $quote->getIncrementId();
							}
							
							//if($i%2 == 0)
							//$css1 = 'class="even"';
							//else
							//$css1 = 'class="odd"';
							$last = $i;
							$rgb = $this->GreenYellowRed($task['ot_target_user']*20);
							$calendar .= '<div class="even" style="text-align: left;cursor:pointer;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
						}
						$i++;
					}
				}
			    
			    
			    
			}
			
			if($i-$last-1 > 0)
			{
			    $calendar .= '<div class="more" style="text-align: left; cursor:pointer;" onclick="expand('.$currentday3.');"> + '.($i-$last-1).' more</div><div class="expand" id="expand_'.$currentday3.'">
			    
			    <div class="stickhead" ><span style="float: left;"><strong>'.$daysOfWeek[$dayOfWeek1].', '.$monthName1.' '.$currentday3.'</strong></span><span style="float: right;cursor : pointer;" onclick="close_expan();">X</span></div>';
			    $i = 1;
			    foreach($chkOrganiger as $task)
			    {
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
			      
						if($task['ot_created_at'] == $date)
						$msg = 'Created ';
						else if($task['ot_notify_date'] == $date)
						$msg = 'Notify  ';
						else if($task['ot_deadline'] == $date)
						$msg = 'Complte  ';
						else
						$msg = '';
						
						    if($task['ot_entity_type'] == 'product')
						    {
							    $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							    $task_caption = $_newProduct->getSku();
						    }
						    else if($task['ot_entity_type'] == 'order')
						    {
							    $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							    $task_caption = $order->getIncrementId();
						    }
						    else if($task['ot_entity_type'] == 'quote')
						    {
							    $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							    $task_caption = $quote->getIncrementId();
						    }
						
						$rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						$calendar .= '<div class="even"  style="text-align: left;cursor:pointer;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
					}
				}
	
			    }
			    $calendar .= '</div>';
			}
		    
		    $calendar .= '<div style="width:100%; height:100%;" id="sc_dt__'.$currentday3.'" onclick="addtask('.$prev_month.','.$prev_year.','.$currentday3.')"></div>
		    
			<div class="overlay" id="sc_ov__'.$currentday3.'"></div>
			    <div class="outer taskcl" id="sc_ou__'.$currentday3.'">
				<div class="cross" id="sc_cr__'.$currentday3.'" onclick="sc_close_pop(this.id);">X</div>
				<div class="inner">
				    
				    ';
			     $calendar .= '<div>Organiger Task</div>';
				       
								       
			foreach($chkOrganiger as $task)
			{
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
						if($task['ot_created_at'] == $date)
						$msg = 'Created ';
						else if($task['ot_notify_date'] == $date)
						$msg = 'Notify  ';
						else if($task['ot_deadline'] == $date)
						$msg = 'Complte  ';
						else
						$msg = '';
						
						    if($task['ot_entity_type'] == 'product')
						    {
							    $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							    $task_caption = $_newProduct->getSku();
						    }
						    else if($task['ot_entity_type'] == 'order')
						    {
							    $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							    $task_caption = $order->getIncrementId();
						    }
						    else if($task['ot_entity_type'] == 'quote')
						    {
							    $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							    $task_caption = $quote->getIncrementId();
						    }
						
						$taskModule = Mage::getModel('Organizer/Task')->load($task['ot_id']);
						$calendar .= '<div style="text-align: left;"><a href="'.$taskModule->getEntityLink().'" target="_blank"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</a></div>
									
									<div class="desc">      '.$task['ot_description'].'</div>';
					}
				}
			    
			}
			
			if($holyday[$date] != '')
		       $calendar .=$holyday[$date];
				   
			$calendar .='<div class="tot_ord_det">';
					    $ctctct=0;
					    
					    
					      
					   foreach($show_id as $vvl)
					   {
					    //print_r($show_id);
					    $Order = Mage::getModel('sales/order')->load($ord_id[$ctctct]);
					    // print_r($Order);
					     
					    $CustomerAdrId=$Order['shipping_address_id'];
					    $address = Mage::getModel('sales/order_address')->load($CustomerAdrId);
				 
					    $Street = $address['street'];
					    $city = $address['city'];
					    $region = $address['region'];
					    $PostCode = $address['postcode'];
					    $country_code = $address['country_id'];
					    $countryModel = Mage::getModel('directory/country')->load($country_code);
					    $country_name = $countryModel->getName();
					     
					    $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
					    $sqlSaleOrder="SELECT created_at FROM ".$temptableSaleOrder." WHERE entity_name='shipment' and parent_id='".$ord_id[$ctctct]."'";
					    try {
						$chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
						
					    
					    
					    
					    } catch (Exception $e){
					    //echo $e>getMessage();
					    }
					    
					    $ord_pay_dt="";
					    foreach($chkSaleOrder as $res_chkSaleOrder) 
					    {
					       $ord_pay_dt= $res_chkSaleOrder["created_at"];
					       
					       
					    }
					     
					     
					     
					    
					     
					    // exit;
					     
					     $calendar .='<div class="ord_lnk"><a target="_blank" href="'.Mage::getBaseUrl()."/admin/sales_order/view/order_id/".$ord_id[$ctctct].'">Order : '.$vvl.'</a>
					     
					     
						<div class="bil_ship_con"><span>Shipping Country : </span>
						    '.$country_name.'
						    
						</div>
						<div class="bil_ship_state"><span>Shipping State : </span>
						    '.$region.'
						    
						</div>
						<div class="bil_ship_state"><span>Order payment date : </span>
						    '.$ord_pay_dt.'
						    
						</div>
						<div class="bil_ship_state"><span>Delivery Deadline : </span>
						    
						    
						</div>
						
					     
					     
					     </div>';
					     $ctctct++;
					   }
					   
					
					
					    
			$calendar .='</div></div></div>';
		   
			
			
		    $calendar .='
					   
					    
		    
		    
		    
		    
		    </div>
		    
		    
		    
		    </td>';  
		  }
		  
		  // Increment counters
	 
		  }
	     }
	     
	     $month = str_pad($month, 2, "0", STR_PAD_LEFT);
	  
	     while ($currentDay <= $numberDays) {
		
		
		$start_time=date("Y-m-d H:i:s",mktime(0,0,0,$month,$currentDay,$year));
		$end_time=date("Y-m-d H:i:s",mktime(23,59,59,$month,$currentDay,$year));
		
		if($currentDay < 10)
		 $currentDay1 = '0'.$currentDay;
		 else
		 $currentDay1 = $currentDay;
		
		//All query Start
	    
		$temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		$sqlSaleOrder="SELECT count(entity_id) as ctct FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
		try {
		    $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
		    
		
		
		
		} catch (Exception $e){
		//echo $e>getMessage();
		}
		
		$tot_order_placed=0;
		foreach($chkSaleOrder as $res_chkSaleOrder) 
		{
		   $tot_order_placed= $res_chkSaleOrder["ctct"];
		   
		   
		}
		
		$temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		$sqlSaleOrder="SELECT entity_id FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
		try {
		    $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
		    
		
		
		
		} catch (Exception $e){
		//echo $e>getMessage();
		}
		
		$ord_id=array();
		$show_id=array();
		
		foreach($chkSaleOrder as $res_chkSaleOrder) 
		{
		   $ord_id[]=$res_chkSaleOrder["entity_id"];
		   $show_id[]=str_pad($res_chkSaleOrder["entity_id"],9,"100000000",STR_PAD_LEFT);
		   
		   
		}
		
		if(count($ord_id)>0)
		{
		    $clclcl="active_view";
		}
		else
		{
		    $clclcl="";
		}
		
		 /********************************* Start by dev ************************************/
		 //Add Permistion 'Able to see tasks for following user '
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(1,$all_permission))
		{
			$user = Mage::getSingleton('admin/session');
			$userId = $user->getUser()->getUserId();
			
			$user_role = Mage::getSingleton('admin/session')->getUser();
			 //Get the role id of the user
			$roleId = implode('', $user_role->getRoles());
			
			//Get the role name
			$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
			
			$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
			{
				$user = Mage::getSingleton('core/session')->getRemoveuser();
				$user = rtrim($user,',');
				$user = ltrim($user,' ');
				
				if(trim(Mage::getSingleton('core/session')->getpro(),' ') != '')
				{
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sql_pro = ' AND ot_id IN(SELECT task_id FROM '.$temptableChain.' WHERE product_id IN ('.rtrim(Mage::getSingleton('core/session')->getpro(),',').'))';
					}
				}
				else
				{
					$sql_pro = '';
				}
				
			       if($roleName == 'Administrators' and $user != '')
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user NOT IN(".$user.") AND (ot_deadline = '".$year.'-'.$month.'-'.$currentDay1."' OR ot_created_at = '".$year.'-'.$month.'-'.$currentDay1."' OR ot_notify_date = '".$year.'-'.$month.'-'.$currentDay1."')".$sql_pro;
				else if($roleName == 'Administrators')
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_deadline = '".$year.'-'.$month.'-'.$currentDay1."' OR ot_created_at = '".$year.'-'.$month.'-'.$currentDay1."' OR ot_notify_date = '".$year.'-'.$month.'-'.$currentDay1."')".$sql_pro;
				else
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_target_user = '".$userId."' AND ot_deadline = '".$year.'-'.$month.'-'.$currentDay1."' OR ot_created_at = '".$year.'-'.$month.'-'.$currentDay1."' OR ot_notify_date = '".$year.'-'.$month.'-'.$currentDay1."')".$sql_pro;
				
				$chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
			}
			foreach($chkOrganiger as $task)
			{
			    if($k < 5)
			    {
			      $calendar1[$currentDay] .= '<div style="text-align: left;"> -- '.$task['ot_caption'].'</div> ';
			    }
			    $k++;
			}
		}
		/********************************* End by dev ************************************/
		
		
		
	
		  // Seventh column (Saturday) reached. Start a new row.
	
		  if ($dayOfWeek == 7) {
	
		       $dayOfWeek = 0;
		       $calendar .= "</tr><tr>";
	
		  }
		  //print_r($show_id);
		  $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
		  
		  $date = "$year-$month-$currentDayRel";
		  
		  //if($holyday[$year.'-'.$month.'-'.$currentDay1] != '')
		  //  $css = 'holiday';
		  //  else
		  //  $css = '';
		  
		  $c_date = date('Y-m-d');
		  
		  if($c_date == $year.'-'.$month.'-'.$currentDay1)
		  $c_css = 'currentcss';
		  else
		  $c_css = '';
	
		  if($todays_date==$currentDay && $current_month==$month)
		  {
		    $calendar .= '<td class="sc_day '.$clclcl.' '.$c_css.'" rel="'.$date.'"><div style="position:relative;  width:100%; height:100%;"><span id="dt__'.$currentDay.'" onclick="daydetails(\''.$year.'-'.$month.'-'.$currentDay1.'\');" class="ddy activated_day '.$css.'">'.$currentDay;
		    
			$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
			{
			$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$year."-".$month."-".$currentDay1."%'";
			$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
			}
			if($chkProofs)
			{
					  $calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
			}
			
			$calendar .= $this->chkshipcolor($year."-".$month."-".$currentDay1);
			
		  $calendar .=   '</span>';
			     $i=1;
			     $last = 0;
			foreach($chkOrganiger as $task)
			{
			    
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
						if($i < 3)
						{
							if($task['ot_created_at'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Created ';
							else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Notify  ';
							else if($task['ot_deadline'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Complte  ';
							else
							$msg = '';
							
							if($task['ot_entity_type'] == 'product')
							{
								$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								$task_caption = $_newProduct->getSku();
							}
							else if($task['ot_entity_type'] == 'order')
							{
								$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								$task_caption = $order->getIncrementId();
							}
							else if($task['ot_entity_type'] == 'quote')
							{
								$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								$task_caption = $quote->getIncrementId();
							}
							
							//if($i%2 == 0)
							//$css1 = 'class="even"';
							//else
							//$css1 = 'class="odd"';
							$last = $i;
							$rgb = $this->GreenYellowRed($task['ot_target_user']*20);
							$calendar .= '<div class="odd" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
						}
						$i++;
					}
				}
			    
			    
			    
			}
			
			if($i-$last-1 > 0)
			{
			    $calendar .= '<div class="more" style="text-align: left; cursor:pointer;" onclick="expand('.$currentDay.');"> + '.($i-$last-1).' more</div><div class="expand" id="expand_'.$currentDay.'">
			    
			    <div class="stickhead" ><span style="float: left;"><strong>'.$daysOfWeek[$dayOfWeek].', '.$monthName.' '.$currentDay1.'</strong></span><span style="float: right;cursor : pointer;" onclick="close_expan();">X</span></div>';
			    $i = 1;
			    foreach($chkOrganiger as $task)
			    {
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
			      
						if($task['ot_created_at'] == $year.'-'.$month.'-'.$currentDay1)
						$msg = 'Created ';
						else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$currentDay1)
						$msg = 'Notify  ';
						else if($task['ot_deadline'] == $year.'-'.$month.'-'.$currentDay1)
						$msg = 'Complte  ';
						else
						$msg = '';
						
						    if($task['ot_entity_type'] == 'product')
						    {
							    $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							    $task_caption = $_newProduct->getSku();
						    }
						    else if($task['ot_entity_type'] == 'order')
						    {
							    $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							    $task_caption = $order->getIncrementId();
						    }
						    else if($task['ot_entity_type'] == 'quote')
						    {
							    $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							    $task_caption = $quote->getIncrementId();
						    }
						
						//if($i%2 == 0)
						//$css1 = 'class="even"';
						//else
						//$css1 = 'class="odd"';
						
						$rgb = $this->GreenYellowRed($task['ot_target_user']*20);
						$calendar .= '<div class="odd"  style="text-align: left;cursor:pointer;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
					}
				}
	
			    }
			    $calendar .= '</div>';
			}
			
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<div style="width:100%; height:100%;" id="sc_dt__'.$currentDay.'" onclick="addtask('.$month.','.$year.','.$currentDay.')"></div>';
		}
		    $calendar .= '
		    
			<div class="overlay" id="sc_ov__'.$currentDay.'"></div>
			    <div class="outer" id="sc_ou__'.$currentDay.'">
				<div class="cross" id="sc_cr__'.$currentDay.'" onclick="sc_close_pop(this.id);">X</div>
				<div class="inner">
				    
				    ';
				    
				    foreach($chkOrganiger as $task)
				      {
					if($task['ot_entity_type'] != 'product')
					{
						$chkId = array();
						$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
						if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
						{
						$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
								    
						$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
						}
						$current_pro = '';
						foreach($chkChain1 as $Chain)
						{
							if($current_pro != $Chain['product_id'])
							{
								$current_pro = $Chain['product_id'];
								$chkId[] = $Chain['task_id'];
							}
							
						}
						if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
						{
							if($task['ot_created_at'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Created ';
							else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Notify  ';
							else if($task['ot_deadline'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Complte  ';
							else
							$msg = '';
							
							      if($task['ot_entity_type'] == 'product')
							      {
								      $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								      $task_caption = $_newProduct->getSku();
							      }
							      else if($task['ot_entity_type'] == 'order')
							      {
								      $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								      $task_caption = $order->getIncrementId();
							      }
							      else if($task['ot_entity_type'] == 'quote')
							      {
								      $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								      $task_caption = $quote->getIncrementId();
							      }
							
							$taskModule = Mage::getModel('Organizer/Task')->load($task['ot_id']);
							$calendar .= '<div style="text-align: left;"><a href="'.$taskModule->getEntityLink().'" target="_blank"> -- '.$msg.' '.$task_caption.' '.$task['ot_caption'].'</a></div>
										
										<div class="desc">      '.$task['ot_description'].'</div>';
						}
					}
					  
				      }
				      
				      if($holyday[$year.'-'.$month.'-'.$currentDay1] != '')
		       $calendar .=$holyday[$year.'-'.$month.'-'.$currentDay1];
				    
				   
			$calendar .='<div class="tot_ord_det">';
					    $ctctct=0;
					   foreach($show_id as $vvl)
					   {
					    //print_r($show_id);
					    $Order = Mage::getModel('sales/order')->load($ord_id[$ctctct]);
					    // print_r($Order);
					     
					    $CustomerAdrId=$Order['shipping_address_id'];
					    $address = Mage::getModel('sales/order_address')->load($CustomerAdrId);
				 
					    $Street = $address['street'];
					    $city = $address['city'];
					    $region = $address['region'];
					    $PostCode = $address['postcode'];
					    $country_code = $address['country_id'];
					    $countryModel = Mage::getModel('directory/country')->load($country_code);
					    $country_name = $countryModel->getName();
					     
					    $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
					    $sqlSaleOrder="SELECT created_at FROM ".$temptableSaleOrder." WHERE entity_name='shipment' and parent_id='".$ord_id[$ctctct]."'";
					    try {
						$chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
						
					    
					    
					    
					    } catch (Exception $e){
					    //echo $e>getMessage();
					    }
					    
					    $ord_pay_dt="";
					    foreach($chkSaleOrder as $res_chkSaleOrder) 
					    {
					       $ord_pay_dt= $res_chkSaleOrder["created_at"];
					       
					       
					    }
					     
					     
					     
					    
					     
					    // exit;
					     
					     $calendar .='<div class="ord_lnk"><a target="_blank" href="'.Mage::getBaseUrl()."/admin/sales_order/view/order_id/".$ord_id[$ctctct].'">Order : '.$vvl.'</a>
					     
					     
						<div class="bil_ship_con"><span>Shipping Country : </span>
						    '.$country_name.'
						    
						</div>
						<div class="bil_ship_state"><span>Shipping State : </span>
						    '.$region.'
						    
						</div>
						<div class="bil_ship_state"><span>Order payment date : </span>
						    '.$ord_pay_dt.'
						    
						</div>
						<div class="bil_ship_state"><span>Delivery Deadline : </span>
						    
						    
						</div>
						
						
					     
					     
					     </div>';
					     $ctctct++;
					   }
					   
			$calendar .='</div>';
			
					   
		    $calendar .='</div>
					   
					    
		    </div>
		    
		    </div>
		    
		    </td>';  
		  }
		  else
		  {
		    $calendar .= '<td class="sc_day '.$clclcl.'" rel="'.$date.'"><div style="position:relative; width:100%; height:100%;"><span class="ddy '.$css.'" onclick="daydetails(\''.$year.'-'.$month.'-'.$currentDay1.'\');">'.$currentDay;
		    
			$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
			{
			$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$year."-".$month."-".$currentDay1."%'";
			$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
			}
			if($chkProofs)
			{
				$calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
			}
			$calendar .= $this->chkshipcolor($year."-".$month."-".$currentDay1);
			
		    $calendar .= '</span>';
			 $i=1;
			 $last = 0;
			foreach($chkOrganiger as $task)
			{
			    
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
						if($i < 3)
						{
							if($task['ot_created_at'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Created ';
							else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Notify  ';
							else if($task['ot_deadline'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Complte  ';
							else
							$msg = '';
							
							if($task['ot_entity_type'] == 'product')
							{
								$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								$task_caption = $_newProduct->getSku();
							}
							else if($task['ot_entity_type'] == 'order')
							{
								$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								$task_caption = $order->getIncrementId();
							}
							else if($task['ot_entity_type'] == 'quote')
							{
								$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								$task_caption = $quote->getIncrementId();
							}
							
							//if($i%2 == 0)
							//$css1 = 'class="even"';
							//else
							//$css1 = 'class="odd"';
							
							$last = $i;
							$rgb = $this->GreenYellowRed($task['ot_target_user']*20);
							$calendar .= '<div class="odd" style="text-align: left;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
						}
						$i++;
					}
				}
			    
			    
			    
			}
			
			if($i-$last-1 > 0)
			{
			    $calendar .= '<div class="more" style="text-align: left; cursor:pointer;" onclick="expand('.$currentDay.');"> + '.($i-$last-1).' more</div><div class="expand" id="expand_'.$currentDay.'">
			    
			    <div class="stickhead" ><span style="float: left;"><strong>'.$daysOfWeek[$dayOfWeek].', '.$monthName.' '.$currentDay1.'</strong></span><span style="float: right;cursor : pointer;" onclick="close_expan();">X</span></div>';
			    $i = 1;
			    foreach($chkOrganiger as $task)
			    {
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
			      
						if($task['ot_created_at'] == $year.'-'.$month.'-'.$currentDay1)
						$msg = 'Created ';
						else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$currentDay1)
						$msg = 'Notify  ';
						else if($task['ot_deadline'] == $year.'-'.$month.'-'.$currentDay1)
						$msg = 'Complte  ';
						else
						$msg = '';
					    
						if($task['ot_entity_type'] == 'product')
						{
							$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							$task_caption = $_newProduct->getSku();
						}
						else if($task['ot_entity_type'] == 'order')
						{
							$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							$task_caption = $order->getIncrementId();
						}
						else if($task['ot_entity_type'] == 'quote')
						{
							$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							$task_caption = $quote->getIncrementId();
						}
					    
					    //if($i%2 == 0)
					    //$css1 = 'class="even"';
					    //else
					    //$css1 = 'class="odd"';
					    //
					    $rgb = $this->GreenYellowRed($task['ot_target_user']*20);
					    $calendar .= '<div class="odd"  style="text-align: left;cursor:pointer;background-color: rgb('.$rgb.');" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
					}
				}
	
			    }
			    $calendar .= '</div>';
			}
			if($holyday[$year.'-'.$month.'-'.$currentDay1] != '')
		       $calendar .=$holyday[$year.'-'.$month.'-'.$currentDay1];
		       
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<div style="width:100%; height:100%;" id="sc_dt__'.$currentDay.'" onclick="addtask('.$month.','.$year.','.$currentDay.')"></div>';	
		}
		    $calendar .= '
		    
			<div class="overlay" id="sc_ov__'.$currentDay.'"></div>
			    <div class="outer" id="sc_ou__'.$currentDay.'">
				<div class="cross" id="sc_cr__'.$currentDay.'" onclick="sc_close_pop(this.id);">X</div>
				<div class="inner">
				    
				    ';
				    
				    foreach($chkOrganiger as $task)
				      {
					if($task['ot_entity_type'] != 'product')
					{
						$chkId = array();
						$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
						if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
						{
						$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
								    
						$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
						}
						$current_pro = '';
						foreach($chkChain1 as $Chain)
						{
							if($current_pro != $Chain['product_id'])
							{
								$current_pro = $Chain['product_id'];
								$chkId[] = $Chain['task_id'];
							}
							
						}
						if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
						{
							if($task['ot_created_at'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Created ';
							else if($task['ot_notify_date'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Notify  ';
							else if($task['ot_deadline'] == $year.'-'.$month.'-'.$currentDay1)
							$msg = 'Complte  ';
							else
							$msg = '';
							
						      if($task['ot_entity_type'] == 'product')
						      {
							      $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							      $task_caption = $_newProduct->getSku();
						      }
						      else if($task['ot_entity_type'] == 'order')
						      {
							      $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							      $task_caption = $order->getIncrementId();
						      }
						      else if($task['ot_entity_type'] == 'quote')
						      {
							      $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							      $task_caption = $quote->getIncrementId();
						      }
							
							$taskModule = Mage::getModel('Organizer/Task')->load($task['ot_id']);
							$calendar .= '<div style="text-align: left;"><a href="'.$taskModule->getEntityLink().'" target="_blank"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</a></div>
										
										<div class="desc">      '.$task['ot_description'].'</div>';
						}
					}
					  
				      }
				      
				      
			$calendar .='<div class="tot_ord_det">';
					    $ctctct=0;
					   foreach($show_id as $vvl)
					   {
					    //print_r($show_id);
					    $Order = Mage::getModel('sales/order')->load($ord_id[$ctctct]);
					    // print_r($Order);
					     
					    $CustomerAdrId=$Order['shipping_address_id'];
					    $address = Mage::getModel('sales/order_address')->load($CustomerAdrId);
				 
					    $Street = $address['street'];
					    $city = $address['city'];
					    $region = $address['region'];
					    $PostCode = $address['postcode'];
					    $country_code = $address['country_id'];
					    $countryModel = Mage::getModel('directory/country')->load($country_code);
					    $country_name = $countryModel->getName();
					     
					    $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
					    $sqlSaleOrder="SELECT created_at FROM ".$temptableSaleOrder." WHERE entity_name='shipment' and parent_id='".$ord_id[$ctctct]."'";
					    try {
						$chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
						
					    
					    
					    
					    } catch (Exception $e){
					    //echo $e>getMessage();
					    }
					    
					    $ord_pay_dt="";
					    foreach($chkSaleOrder as $res_chkSaleOrder) 
					    {
					       $ord_pay_dt= $res_chkSaleOrder["created_at"];
					       
					       
					    }
					     
					     
					     
					    
					     
					    // exit;
					     
					     $calendar .='<div class="ord_lnk"><a target="_blank" href="'.Mage::getBaseUrl()."/admin/sales_order/view/order_id/".$ord_id[$ctctct].'">Order : '.$vvl.'</a>
					     
					     
						<div class="bil_ship_con"><span>Shipping Country : </span>
						    '.$country_name.'
						    
						</div>
						<div class="bil_ship_state"><span>Shipping State : </span>
						    '.$region.'
						    
						</div>
						<div class="bil_ship_state"><span>Order payment date : </span>
						    '.$ord_pay_dt.'
						    
						</div>
						<div class="bil_ship_state"><span>Delivery Deadline : </span>
						    
						    
						</div>
						
					     
					     
					     </div>';
					     $ctctct++;
					   }
					   
					   
					   
			$calendar .='</div>';
			
					   
		    $calendar .='</div>
					   
					    
		    </div>
		    
		    </div>
		    
		    
		    
		    </td>';  
		  }
		  
	
		  // Increment counters
	 
		  $currentDay++;
		  $dayOfWeek++;
	
	     }
	     
	     
	
	     // Complete the row of the last week in month, if necessary
	
	     if ($dayOfWeek != 7) { 
	     
		  $remainingDays = 7 - $dayOfWeek;
		  //$calendar .= "<td colspan='$remainingDays'>&nbsp;</td>";
		  
		     
		  
		    
		  for($predate = 0;$predate < $remainingDays;$predate++)
		  {
		   $currentday3 = 01+$predate;
		    $start_time=date("Y-m-d H:i:s",mktime(0,0,0,$next_month,$currentday3,$next_year));
		$end_time=date("Y-m-d H:i:s",mktime(23,59,59,$next_month,$currentday3,$next_year));
		
		 $dateComponents1 = getdate(mktime(0,0,0,$next_month,$currentday3,$next_year));
		 $monthName1 = $dateComponents1['month'];
		 $dayOfWeek1 = $dateComponents1['wday'];
		
		
		//All query Start
	    
		$temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		$sqlSaleOrder="SELECT count(entity_id) as ctct FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
		try {
		    $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
		    
		
		
		
		} catch (Exception $e){
		//echo $e>getMessage();
		}
		
		$tot_order_placed=0;
		foreach($chkSaleOrder as $res_chkSaleOrder) 
		{
		   $tot_order_placed= $res_chkSaleOrder["ctct"];
		   
		   
		}
		
		$temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		$sqlSaleOrder="SELECT entity_id FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
		try {
		    $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
		    
		
		
		
		} catch (Exception $e){
		//echo $e>getMessage();
		}
		
		$ord_id=array();
		$show_id=array();
		
		foreach($chkSaleOrder as $res_chkSaleOrder) 
		{
		   $ord_id[]=$res_chkSaleOrder["entity_id"];
		   $show_id[]=str_pad($res_chkSaleOrder["entity_id"],9,"100000000",STR_PAD_LEFT);
		   
		   
		}
		
		if(count($ord_id)>0)
		{
		    $clclcl="active_view";
		}
		else
		{
		    $clclcl="";
		}
		
		/********************************* Start by dev ************************************/
		//Add Permistion 'Able to see tasks for following user '
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(1,$all_permission))
		{
			$user = Mage::getSingleton('admin/session');
			$userId = $user->getUser()->getUserId();
			
			$user_role = Mage::getSingleton('admin/session')->getUser();
			 //Get the role id of the user
			$roleId = implode('', $user_role->getRoles());
			
			//Get the role name
			$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
			
			$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
			
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
			{
			
				$user = Mage::getSingleton('core/session')->getRemoveuser();
				$user = rtrim($user,',');
				$user = ltrim($user,' ');
				
				if(trim(Mage::getSingleton('core/session')->getpro(),' ') != '')
				{
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					$sql_pro = ' AND ot_id IN(SELECT task_id FROM '.$temptableChain.' WHERE product_id IN ('.rtrim(Mage::getSingleton('core/session')->getpro(),',').'))';
				}
				else
				{
					$sql_pro = '';
				}
				
			       if($roleName == 'Administrators' and $user != '')
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user NOT IN(".$user.") AND (ot_deadline = '".$next_year.'-'.$next_month.'-'.$currentday3."' OR ot_created_at = '".$next_year.'-'.$next_month.'-'.$currentday3."' OR ot_notify_date = '".$next_year.'-'.$next_month.'-'.$currentday3."')".$sql_pro;
				else if($roleName == 'Administrators')
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_deadline = '".$next_year.'-'.$next_month.'-'.$currentday3."' OR ot_created_at = '".$next_year.'-'.$next_month.'-'.$currentday3."' OR ot_notify_date = '".$next_year.'-'.$next_month.'-'.$currentday3."')".$sql_pro;
				else
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE (ot_target_user = '".$userId."' AND ot_deadline = '".$next_year.'-'.$next_month.'-'.$currentday3."' OR ot_created_at = '".$next_year.'-'.$next_month.'-'.$currentday3."' OR ot_notify_date = '".$next_year.'-'.$next_month.'-'.$currentday3."')".$sql_pro;
				
				$chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
			}
			foreach($chkOrganiger as $task)
			{
			    if($k < 5)
			    {
			      $calendar1[$currentDay] .= '<div style="text-align: left;"> -- '.$task['ot_caption'].'</div> ';
			    }
			    $k++;
			}
		}
		/********************************* End by dev ************************************/
		
		
	
		  // Seventh column (Saturday) reached. Start a new row.
	
		  if ($dayOfWeek == 7) {
	
		       $dayOfWeek = 0;
		       $calendar .= "</tr><tr>";
	
		  }
		  //print_r($show_id);
		  $currentDayRel = str_pad($currentday3, 2, "0", STR_PAD_LEFT);
		  $currentMonthRel = str_pad($next_month, 2, "0", STR_PAD_LEFT);
		  
		  $date = "$next_year-$currentMonthRel-$currentDayRel";
		
		    if($holyday[$date] != '')
		    $css = 'holiday';
		    else
		    $css = '';
		    
		
		    
		  if($todays_date==$currentDay && $current_month==$next_month)
		  {
		    
		    
		    
		    $calendar .= '<td class="sc_day '.$clclcl.'" id="sc_day_'.$currentday3.'" rel="'.$date.'"><div style="position:relative;  width:100%; height:100%;"><span id="dt__'.$currentday3.'"  class="ddc activated_day '.$css.'" onclick="daydetails(\''.$date.'\');">'.$currentday3;
		    
			$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
			{
			$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$date."%'";
			$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
			}
			if($chkProofs)
			{
				$calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
			}
			
			$calendar .= $this->chkshipcolor($date);
			
		     $calendar .= '</span>';
			$i=1;
			$last = 0;
			foreach($chkOrganiger as $task)
			{
			   
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
						if($i < 3)
						{
							if($task['ot_created_at'] == $date)
							$msg = 'Created ';
							else if($task['ot_notify_date'] == $date)
							$msg = 'Notify  ';
							else if($task['ot_deadline'] == $date)
							$msg = 'Complte  ';
							else
							$msg = '';
							
							if($task['ot_entity_type'] == 'product')
							{
								$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								$task_caption = $_newProduct->getSku();
							}
							else if($task['ot_entity_type'] == 'order')
							{
								$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								$task_caption = $order->getIncrementId();
							}
							else if($task['ot_entity_type'] == 'quote')
							{
								$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								$task_caption = $quote->getIncrementId();
							}
							
							//if($i%2 == 0)
							//$css1 = 'class="even"';
							//else
							//$css1 = 'class="odd"';
							
							$last = $i;
							$calendar .= '<div class="even" style="text-align: left;cursor:pointer;" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
						}
						$i++;
					}
				}
			    
			    
			    
			    
			}
			
			if($i-$last-1 > 0)
			{
			    $calendar .= '<div class="more" style="text-align: left; cursor:pointer;" onclick="expand('.$currentday3.');"> + '.($i-$last-1).' more</div><div class="expand" id="expand_'.$currentday3.'">
			    
			    <div class="stickhead" ><span style="float: left;"><strong>'.$daysOfWeek[$dayOfWeek1].', '.$monthName1.' '.$currentday3.'</strong></span><span style="float: right;" onclick="close_expan()">X</span></div>';
			    
			    foreach($chkOrganiger as $task)
			    {
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
			      
						if($task['ot_created_at'] == $date)
						$msg = 'Created ';
						else if($task['ot_notify_date'] == $date)
						$msg = 'Notify  ';
						else if($task['ot_deadline'] == $date)
						$msg = 'Complte  ';
						else
						$msg = '';
						
						if($task['ot_entity_type'] == 'product')
						{
							$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							$task_caption = $_newProduct->getSku();
						}
						else if($task['ot_entity_type'] == 'order')
						{
							$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							$task_caption = $order->getIncrementId();
						}
						else if($task['ot_entity_type'] == 'quote')
						{
							$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							$task_caption = $quote->getIncrementId();
						}
					    
					    if($i%2 == 0)
					    $css1 = 'class="even"';
					    else
					    $css1 = 'class="odd"';
					    $last = $i;
					    $calendar .= '<div class="even"  style="text-align: left;cursor:pointer;" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
					}
				}
			       
			    }
			    $calendar .= '</div>';
			}
			if($holyday[$date] != '')
		       $calendar .=$holyday[$date];
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<div style="width:100%; height:100%;" id="sc_dt__'.$currentday3.'" onclick="addtask('.$next_month.','.$next_year.','.$currentday3.')"></div>';
		}
		    $calendar .= '
		    
			<div class="overlay" id="sc_ov__'.$currentday3.'"></div>
			    <div class="outer taskcl" id="sc_ou__'.$currentday3.'">
				<div class="cross" id="sc_cr__'.$currentday3.'" onclick="sc_close_pop(this.id);">X</div>
				<div class="inner">
				    
				    ';
				       $calendar .= '<div>Organiger Task</div>';  
					foreach($chkOrganiger as $task)
					{
						if($task['ot_entity_type'] != 'product')
						{
							$chkId = array();
							$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
							if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
							{
							$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
									    
							$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
							}
							$current_pro = '';
							foreach($chkChain1 as $Chain)
							{
								if($current_pro != $Chain['product_id'])
								{
									$current_pro = $Chain['product_id'];
									$chkId[] = $Chain['task_id'];
								}
								
							}
							if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
							{
								if($task['ot_created_at'] == $date)
								$msg = 'Created ';
								else if($task['ot_notify_date'] == $date)
								$msg = 'Notify  ';
								else if($task['ot_deadline'] == $date)
								$msg = 'Complte  ';
								else
								$msg = '';
							    
								if($task['ot_entity_type'] == 'product')
								{
									$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
									$task_caption = $_newProduct->getSku();
								}
								else if($task['ot_entity_type'] == 'order')
								{
									$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
									$task_caption = $order->getIncrementId();
								}
								else if($task['ot_entity_type'] == 'quote')
								{
									$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
									$task_caption = $quote->getIncrementId();
								}
					    
							    $taskModule = Mage::getModel('Organizer/Task')->load($task['ot_id']);
							    $calendar .= '<div style="text-align: left;"><a href="'.$taskModule->getEntityLink().'" target="_blank"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</a></div>
							    
							    <div class="desc">      '.$task['ot_description'].'</div>';
							}
						}
					    
					}
					
					
				   
			
			$calendar .='<div class="tot_ord_det">';
					    $ctctct=0;
					   foreach($show_id as $vvl)
					   {
					    //print_r($show_id);
					    $Order = Mage::getModel('sales/order')->load($ord_id[$ctctct]);
					    // print_r($Order);
					     
					    $CustomerAdrId=$Order['shipping_address_id'];
					    $address = Mage::getModel('sales/order_address')->load($CustomerAdrId);
				 
					    $Street = $address['street'];
					    $city = $address['city'];
					    $region = $address['region'];
					    $PostCode = $address['postcode'];
					    $country_code = $address['country_id'];
					    $countryModel = Mage::getModel('directory/country')->load($country_code);
					    $country_name = $countryModel->getName();
					     
					    $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
					    $sqlSaleOrder="SELECT created_at FROM ".$temptableSaleOrder." WHERE entity_name='shipment' and parent_id='".$ord_id[$ctctct]."'";
					    try {
						$chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
						
					    
					    
					    
					    } catch (Exception $e){
					    //echo $e>getMessage();
					    }
					    
					    $ord_pay_dt="";
					    foreach($chkSaleOrder as $res_chkSaleOrder) 
					    {
					       $ord_pay_dt= $res_chkSaleOrder["created_at"];
					       
					       
					    }
					     
					     
					     
					    
					     
					    // exit;
					     
					     $calendar .='<div class="ord_lnk"><a target="_blank" href="'.Mage::getBaseUrl()."/admin/sales_order/view/order_id/".$ord_id[$ctctct].'">Order : '.$vvl.'</a>
					     
					     
						<div class="bil_ship_con"><span>Shipping Country : </span>
						    '.$country_name.'
						    
						</div>
						<div class="bil_ship_state"><span>Shipping State : </span>
						    '.$region.'
						    
						</div>
						<div class="bil_ship_state"><span>Order payment date : </span>
						    '.$ord_pay_dt.'
						    
						</div>
						<div class="bil_ship_state"><span>Delivery Deadline : </span>
						    
						    
						</div>
						
						
					     
					     
					     </div>';
					     
					     $ctctct++;
					   }
					   
					  
			$calendar .='</div></div></div>';
					  
		    $calendar .='
					   
					    
		    
		    
		    </div>
		    
		    </td>';  
		  }
		  else
		  {
		    $calendar .= '<td class="sc_day '.$clclcl.'" id="sc_day_'.$currentday3.'" rel="'.$date.'"><div style="position:relative; width:100%; height:100%;"><span class="ddc '.$css.'" onclick="daydetails(\''.$date.'\');">'.$currentday3;
		    
			$temptableProof=Mage::getSingleton('core/resource')->getTableName('quote_planning');
			if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
			{
			$sqlProof="SELECT * FROM ".$temptableProof." WHERE  proof_date LIKE  '%".$date."%'";
			$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
			}
			if($chkProofs)
			{
			$calendar .= "<span class='blink'><img  src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."adminhtml/default/default/images/redstar.gif'/></span>";
			}
			$calendar .= $this->chkshipcolor($date);
			
		    $calendar .='</span>';
		    
			   
			    $i=1;
			    $last = 0;
			foreach($chkOrganiger as $task)
			{
			    
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
						if($i < 3)
						{
							if($task['ot_created_at'] == $date)
							$msg = 'Created ';
							else if($task['ot_notify_date'] == $date)
							$msg = 'Notify  ';
							else if($task['ot_deadline'] == $date)
							$msg = 'Complte  ';
							else
							$msg = '';
							
							if($task['ot_entity_type'] == 'product')
							{
								$_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
								$task_caption = $_newProduct->getSku();
							}
							else if($task['ot_entity_type'] == 'order')
							{
								$order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
								$task_caption = $order->getIncrementId();
							}
							else if($task['ot_entity_type'] == 'quote')
							{
								$quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
								$task_caption = $quote->getIncrementId();
							}
							
							//if($i%2 == 0)
							//$css1 = 'class="even"';
							//else
							//$css1 = 'class="odd"';
							$last = $i;
							$calendar .= '<div class="even" style="text-align: left;cursor:pointer;" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
						}
						$i++;
					}
				
			    }
			    
			   
			}
			
			if($i-$last-1 > 0)
			{
			    $calendar .= '<div class="more" style="text-align: left; cursor:pointer;" onclick="expand('.$currentday3.');"> + '.($i-$last-1).' more</div><div class="expand" id="expand_'.$currentday3.'">
			    
			    <div class="stickhead" ><span style="float: left;"><strong>'.$daysOfWeek[$dayOfWeek1].', '.$monthName1.' '.$currentday3.'</strong></span><span style="float: right;cursor : pointer;" onclick="close_expan();">X</span></div>';
			    $i = 1;
			    foreach($chkOrganiger as $task)
			    {
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
			      
						if($task['ot_created_at'] == $date)
						$msg = 'Created ';
						else if($task['ot_notify_date'] == $date)
						$msg = 'Notify  ';
						else if($task['ot_deadline'] == $date)
						$msg = 'Complte  ';
						else
						$msg = '';
						
						    if($task['ot_entity_type'] == 'product')
						    {
							    $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							    $task_caption = $_newProduct->getSku();
						    }
						    else if($task['ot_entity_type'] == 'order')
						    {
							    $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							    $task_caption = $order->getIncrementId();
						    }
						    else if($task['ot_entity_type'] == 'quote')
						    {
							    $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							    $task_caption = $quote->getIncrementId();
						    }
						
						if($i%2 == 0)
						$css1 = 'class="even"';
						else
						$css1 = 'class="odd"';
						
						$calendar .= '<div class="even"  style="text-align: left;cursor:pointer;" onclick="edittask('.$task['ot_id'].');"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</div>';
					}
				}
	
			    }
			    $calendar .= '</div>';
			}
			
			if($holyday[$date] != '')
		       $calendar .=$holyday[$date];
		       
		//Add Permistion 'Able to add tasks on calander of following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(5,$all_permission))
		{
			$calendar .= '<div style="width:100%; height:100%;" id="sc_dt__'.$currentday3.'" onclick="addtask('.$next_month.','.$next_year.','.$currentday3.')"></div>';
		}
		    
		    $calendar .= '
		    
			<div class="overlay" id="sc_ov__'.$currentday3.'"></div>
			    <div class="outer taskcl" id="sc_ou__'.$currentday3.'">
				<div class="cross" id="sc_cr__'.$currentday3.'" onclick="sc_close_pop(this.id);">X</div>
				<div class="inner">
				    
				    ';
			     $calendar .= '<div>Organiger Task</div>';
				       
								       
			foreach($chkOrganiger as $task)
			{
				if($task['ot_entity_type'] != 'product')
				{
					$chkId = array();
					$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
					if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
					{
					$sqlChain="SELECT * FROM ".$temptableChain." WHERE  order_quote_id = '".$task['ot_entity_id']."' AND task_status = '' AND product_id != '' AND task_type = 'Chain' ORDER BY product_id   ";
							    
					$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
					}
					$current_pro = '';
					foreach($chkChain1 as $Chain)
					{
						if($current_pro != $Chain['product_id'])
						{
							$current_pro = $Chain['product_id'];
							$chkId[] = $Chain['task_id'];
						}
						
					}
					if(in_array($task['ot_id'],$chkId) or $task['ot_finished'] == 1  or $task['ot_task_type'] == 'Independent')
					{
						if($task['ot_created_at'] == $date)
						$msg = 'Created ';
						else if($task['ot_notify_date'] == $date)
						$msg = 'Notify  ';
						else if($task['ot_deadline'] == $date)
						$msg = 'Complte  ';
						else
						$msg = '';
						
						    if($task['ot_entity_type'] == 'product')
						    {
							    $_newProduct = Mage::getModel('catalog/product')->load($task['ot_entity_id']);
							    $task_caption = $_newProduct->getSku();
						    }
						    else if($task['ot_entity_type'] == 'order')
						    {
							    $order = Mage::getModel('sales/order')->load($task['ot_entity_id']);
							    $task_caption = $order->getIncrementId();
						    }
						    else if($task['ot_entity_type'] == 'quote')
						    {
							    $quote = Mage::getModel("Quotation/Quotation")->load($task['ot_entity_id']);
							    $task_caption = $quote->getIncrementId();
						    }
						
						$taskModule = Mage::getModel('Organizer/Task')->load($task['ot_id']);
						$calendar .= '<div style="text-align: left;"><a href="'.$taskModule->getEntityLink().'" target="_blank"> -- '.$msg.' '.$task_caption.'...'.$task['ot_caption'].'</a></div>
									
									<div class="desc">      '.$task['ot_description'].'</div>';
					}
				}
			    
			}       
				   
			$calendar .='<div class="tot_ord_det">';
					    $ctctct=0;
					    
					    
					      
					   foreach($show_id as $vvl)
					   {
					    //print_r($show_id);
					    $Order = Mage::getModel('sales/order')->load($ord_id[$ctctct]);
					    // print_r($Order);
					     
					    $CustomerAdrId=$Order['shipping_address_id'];
					    $address = Mage::getModel('sales/order_address')->load($CustomerAdrId);
				 
					    $Street = $address['street'];
					    $city = $address['city'];
					    $region = $address['region'];
					    $PostCode = $address['postcode'];
					    $country_code = $address['country_id'];
					    $countryModel = Mage::getModel('directory/country')->load($country_code);
					    $country_name = $countryModel->getName();
					     
					    $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
					    $sqlSaleOrder="SELECT created_at FROM ".$temptableSaleOrder." WHERE entity_name='shipment' and parent_id='".$ord_id[$ctctct]."'";
					    try {
						$chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
						
					    
					    
					    
					    } catch (Exception $e){
					    //echo $e>getMessage();
					    }
					    
					    $ord_pay_dt="";
					    foreach($chkSaleOrder as $res_chkSaleOrder) 
					    {
					       $ord_pay_dt= $res_chkSaleOrder["created_at"];
					       
					       
					    }
					     
					     
					     
					    
					     
					    // exit;
					     
					     $calendar .='<div class="ord_lnk"><a target="_blank" href="'.Mage::getBaseUrl()."/admin/sales_order/view/order_id/".$ord_id[$ctctct].'">Order : '.$vvl.'</a>
					     
					     
						<div class="bil_ship_con"><span>Shipping Country : </span>
						    '.$country_name.'
						    
						</div>
						<div class="bil_ship_state"><span>Shipping State : </span>
						    '.$region.'
						    
						</div>
						<div class="bil_ship_state"><span>Order payment date : </span>
						    '.$ord_pay_dt.'
						    
						</div>
						<div class="bil_ship_state"><span>Delivery Deadline : </span>
						    
						    
						</div>
						
					     
					     
					     </div>';
					     $ctctct++;
					   }
					   
					
					
					    
			$calendar .='</div></div></div>';
		   
			
			
		    $calendar .='
					   
					    
		    
		    
		    
		    
		    </div>
		    
		    
		    
		    </td>';  
		  }
		  
	
		  // Increment counters
	 
		  }
	
	     }
	     
	     $calendar .= "</tr>";
	
	     $calendar .= "</table><input type='hidden' id='nowdate' value='".$year.'-'.$month.'-'.$currentDay1."'/><input type='hidden' id='catformat' value='".Mage::getSingleton('core/session')->getFormattype()."'/>";
	     
	     
		/********************** For Other Calender ******************************/
		$countryh = $this->getOthercalendar($year,$month);
		/********************** For Other Calender ******************************/
	
		/********************** For My Calender ******************************/
		$my_cal = $this->getMycalendar();
		/********************** For My Calender ******************************/
		
		
		/********************** For Product Sku ******************************/
		$unship_pro = $this->getUnshipproduct();
		/********************** For Product Sku ******************************/

	
	    $calendar = '<div class="calt" style="margin-top: 29px;">'.$my_cal.'</div><div class="countryt" style="margin-top: 29px;">'.$countryh.'</div><div class="countryt" style="margin-top: 0px;">'.$unship_pro.'</div><div class="maincal">'.$calendar.'</div>';
	
	     echo $calendar;
	
	}
	
	// Load todo list in event calendar
	
	public function loadtodoAction()
	{
		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$list_id = Mage::getSingleton('core/session')->getListid();    
		
		
		$temptableTodo=Mage::getSingleton('core/resource')->getTableName('todolist');
		$sqlTodo="SELECT * FROM ".$temptableTodo." WHERE user_id = '".$userId."' AND list_id = '".$list_id."'";
		$chkTodo = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo);
		$k=1;
		foreach($chkTodo as $todo)
		{
		    $Task = Mage::getModel('Organizer/Task')->load($todo['ot_id']);
		    
		$complete_date = date("jS F Y", strtotime($Task->getOtDeadline()));
		    
		    $checked = '';
		    if($todo['complete'] == 1)
		    $checked = 'checked';
		    
		       echo  '<div id="row_'.$k.'" style="margin-top:0px;" class="rowtodo">
			   <input onclick="complete('.$todo['ot_id'].');" id="todoid_'. $k.'"  value="'.$todo['ot_id'].'" '.$checked.' type="checkbox"/><input class="todoitem" onkeyup="onKeyPressed(event,this.id)" value="'.$Task->getOtCaption().'.......'.$complete_date.'" type="text" id="todo_'.$k.'" style="width: 145px;border:0"/>
			    <span style="cursor:pointer;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/greater.png'.'" id="img_'.$k.'" onclick="addtodo('.$todo['ot_id'].','.$k.')"/></span>
			</div>';
		    $k++;
		}
		
		
		echo '<div id="row_'.$k.'" style="margin-top:0px;" class="rowtodo">
		    <input type="checkbox"/><input class="todoitem" onkeyup="onKeyPressed(event,this.id)" type="text" id="todo_'.$k.'" style="width: 105px;border:0"/>
		    <span style="cursor:pointer;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/greater.png'.'" id="img_'.$k.'" onclick="addtodo(\' \','.$k.')"/></span>
		</div>';
	}
	
	// Remove ar add the holiday list in country wish
	
	public function chkholiAction()
	{
		$holiid = $_REQUEST['holiid'];
		if(strpos(Mage::getSingleton('core/session')->getRemoveholi(),$holiid))
		{
		    $nowholi = str_replace("'".$holiid."',",'',Mage::getSingleton('core/session')->getRemoveholi());
		}
		else{
		    $nowholi = Mage::getSingleton('core/session')->getRemoveholi()."'".$holiid."',";
		}
		Mage::getSingleton('core/session')->setRemoveholi($nowholi);
		echo Mage::getSingleton('core/session')->getRemoveholi();
	}
	
	// Select the user list 
	
	public function chkuserAction()
	{
		$userid = $_REQUEST['userid'];
		$user = explode(',',Mage::getSingleton('core/session')->getRemoveuser());
		if(in_array($userid,$user))
		{
		    $userid.",";
		    $nowuser = str_replace($userid.",",'',Mage::getSingleton('core/session')->getRemoveuser());
		    if(Mage::getSingleton('core/session')->getRemoveuser() == ' ')
		    Mage::getSingleton('core/session')->setRemoveuser();
		}
		else{
		    if(Mage::getSingleton('core/session')->getRemoveuser())
		    $nowuser = Mage::getSingleton('core/session')->getRemoveuser().$userid.",";
		    else
		    $nowuser = " ".$userid.",";
		}
		Mage::getSingleton('core/session')->setRemoveuser($nowuser);
		echo Mage::getSingleton('core/session')->getRemoveuser();
	}
	
	public function chkproductAction()
	{
		$productid = $_REQUEST['productid'];
		$pro = explode(',',Mage::getSingleton('core/session')->getPro());
		if(in_array($productid,$pro))
		{
		    $nowpro = str_replace($productid.",",'',Mage::getSingleton('core/session')->getPro());
		    if(Mage::getSingleton('core/session')->getPro() == ' ')
		    Mage::getSingleton('core/session')->setPro();
		}
		else{
		    if(Mage::getSingleton('core/session')->getPro())
		    $nowpro = Mage::getSingleton('core/session')->getPro().$productid.",";
		    else
		    $nowpro = " ".$productid.",";
		}
		Mage::getSingleton('core/session')->setPro($nowpro);
		echo Mage::getSingleton('core/session')->getPro();
	}
	
	// Check for todo task complete
	
	public function todocompleteAction()
	{
		$id = $_REQUEST['id'];
		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$list_id = Mage::getSingleton('core/session')->getListid();
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todolist');
		$sqlTodo1="SELECT * FROM ".$temptableTodo1." WHERE ot_id = '".$id."' ";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo1);
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todolist');
		if($chkTodo1[0]['complete'] == 1)
		$sqlTodo1="UPDATE ".$temptableTodo1." SET complete= 0 WHERE ot_id = '".$id."' ";
		else
		$sqlTodo1="UPDATE ".$temptableTodo1." SET complete= 1 WHERE ot_id = '".$id."' ";
		
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo1);


	}
	
	// Rename the todo list
	
	public function todorenameAction()
	{
		$listname = $_REQUEST['listname'];
		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$list_id = Mage::getSingleton('core/session')->getListid();
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="UPDATE ".$temptableTodo1." SET name= '".$listname."' WHERE user_id = '".$userId."' AND list_id = '".$list_id."' ";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo1);
		
		
		echo $listname;
		
		 echo '@';
		    
		 
		    $temptableTodo=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		    $sqlTodo="SELECT * FROM ".$temptableTodo." WHERE user_id = '".$userId."'";
		    $chkTodos = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo);
		    
		    foreach($chkTodos as $chkTodo)
		    {
		
			echo '<div onclick="listload('.$chkTodo['list_id'].');" style="cursor:pointer;" >'.$chkTodo['name'].'</div>';
		
		    }

	}
	
	// Delete the todo list
	
	public function todolistdeleteAction()
	{
		
		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$list_id = Mage::getSingleton('core/session')->getListid();
		
		$temptableTodo=Mage::getSingleton('core/resource')->getTableName('todolist');
		$sqlTodo="DELETE FROM ".$temptableTodo." WHERE user_id = '".$userId."' AND list_id = '".$list_id."'";
		$chkTodo = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo);
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="DELETE FROM ".$temptableTodo1." WHERE user_id = '".$userId."' AND list_id = '".$list_id."'";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo1);
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="SELECT * FROM ".$temptableTodo1." WHERE user_id = '".$userId."' ORDER BY list_id DESC";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo1);
		
		Mage::getSingleton('core/session')->setListid($chkTodo1[0]['list_id']);
		
		$temptableTodo2=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo2="UPDATE ".$temptableTodo2." SET active = 1 WHERE user_id = '".$userId."' AND list_id = '".$chkTodo1[0]['list_id']."'";
		$chkTodo2 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo2);
		
		
		
		$temptableTodo=Mage::getSingleton('core/resource')->getTableName('todolist');
		$sqlTodo="SELECT * FROM ".$temptableTodo." WHERE list_id = '".$chkTodo1[0]['list_id']."' AND user_id = '".$userId."' ";
		$chkTodo = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo);
		
		$k=1;
		foreach($chkTodo as $todo)
		{
		    $Task = Mage::getModel('Organizer/Task')->load($todo['ot_id']);
		    $complete_date = date("jS F Y", strtotime($Task->getOtDeadline()));
		    
		    $checked = '';
		    if($todo['complete'] == 1)
		    $checked = 'checked';
		    
		   
			echo '<div id="row_'.$k.'" style="margin-top:0px;" class="rowtodo">
			   <input id="todoid_'.$k.'"  value="'.$todo['ot_id'].'" '.$checked.' type="checkbox"/><input class="todoitem" onkeyup="onKeyPressed(event,this.id)" value="'.$Task->getOtCaption().'.......'.$complete_date.'" type="text" id="todo_'.$k.'" style="width: 145px;border:0"/>
			    <span style="cursor:pointer;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/greater.png'.'" id="img_'.$k.'" onclick="addtodo('.$todo['ot_id'].','.$k.')"/></span>
			</div>';
		   
		    $k++;
		}
		
		
		echo '<div id="row_'.$k.'" style="margin-top:0px;" class="rowtodo">
		    <input onclick="complete('.$todo['ot_id'].');" type="checkbox"/><input class="todoitem" onkeyup="onKeyPressed(event,this.id)" type="text" id="todo_'.$k.'" style="width: 105px;border:0"/>
		    <span style="cursor:pointer;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/greater.png'.'" id="img_'.$k.'" onclick="addtodo(\' \','.$k.')"/></span>
		</div>';
		
		
		    echo '@'.$chkTodo1[0]['name'];
		
		    echo '@';
		    
		 
		    $temptableTodo=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		    $sqlTodo="SELECT * FROM ".$temptableTodo." WHERE user_id = '".$userId."'";
		    $chkTodos = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo);
		    
		    foreach($chkTodos as $chkTodo)
		    {
		
			echo '<div onclick="listload('.$chkTodo['list_id'].');" style="cursor:pointer;" >'.$chkTodo['name'].'</div>';
		
		    }
		
		
		
		
		}
		
		public function todolistAction()
		{
			$listname = $_REQUEST['listname'];
			$listid = $_REQUEST['listid'];
		
		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		
		if($listname != '')
		{
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="INSERT INTO ".$temptableTodo1." SET name = '".$listname."' , user_id = '".$userId."'";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo1);
		
		$lastInsertId = Mage::getSingleton('core/resource')->getConnection('core_write')->lastInsertId();
		
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="UPDATE ".$temptableTodo1." SET active=0 WHERE user_id = '".$userId."' ";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo1);
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="UPDATE ".$temptableTodo1." SET active=1 WHERE list_id = '".$lastInsertId."' ";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo1);
		}
		else{
		$lastInsertId = $listid;
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="UPDATE ".$temptableTodo1." SET active=0 WHERE user_id = '".$userId."' ";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo1);
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="UPDATE ".$temptableTodo1." SET active=1 WHERE list_id = '".$listid."' ";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlTodo1);
		
		$temptableTodo1=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo1="SELECT * FROM ".$temptableTodo1." WHERE list_id = '".$listid."' ";
		$chkTodo1 = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo1);
		
		$listname = $chkTodo1[0]['name'];
		}
		
		Mage::getSingleton('core/session')->setListid($lastInsertId);
		
		
		
		
		$temptableTodo=Mage::getSingleton('core/resource')->getTableName('todolist');
		$sqlTodo="SELECT * FROM ".$temptableTodo." WHERE list_id = '".$lastInsertId."' AND user_id = '".$userId."' ";
		$chkTodo = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo);
		
		$k=1;
		foreach($chkTodo as $todo)
		{
			$Task = Mage::getModel('Organizer/Task')->load($todo['ot_id']);
			$complete_date = date("jS F Y", strtotime($Task->getOtDeadline()));
		
			$checked = '';
			if($todo['complete'] == 1)
			$checked = 'checked';
			
			
			echo '<div id="row_'.$k.'" style="margin-top:0px;" class="rowtodo">
			<input onclick="complete('.$todo['ot_id'].');" id="todoid_'.$k.'"  value="'.$todo['ot_id'].'" '.$checked.' type="checkbox"/><input class="todoitem" onkeyup="onKeyPressed(event,this.id)" value="'.$Task->getOtCaption().'.......'.$complete_date.'" type="text" id="todo_'.$k.'" style="width: 145px;border:0"/>
			<span style="cursor:pointer;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/greater.png'.'" id="img_'.$k.'" onclick="addtodo('.$todo['ot_id'].','.$k.')"/></span>
			</div>';
			
			$k++;
		}
		
		
		echo '<div id="row_'.$k.'" style="margin-top:0px;" class="rowtodo">
		<input type="checkbox"/><input class="todoitem" onkeyup="onKeyPressed(event,this.id)" type="text" id="todo_'.$k.'" style="width: 105px;border:0"/>
		<span style="cursor:pointer;"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/greater.png'.'" id="img_'.$k.'" onclick="addtodo(\' \','.$k.')"/></span>
		</div>';
		
		echo '@'.$listname;
		echo '@';
		
		
		$temptableTodo=Mage::getSingleton('core/resource')->getTableName('todotasklist');
		$sqlTodo="SELECT * FROM ".$temptableTodo." WHERE user_id = '".$userId."'";
		$chkTodos = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTodo);
		
		foreach($chkTodos as $chkTodo)
		{
		
		echo '<div onclick="listload('.$chkTodo['list_id'].');" style="cursor:pointer;" >'.$chkTodo['name'].'</div>';
		
		}
		
		
	}
	
	//Delete the todo task
	
	public function deletetodoAction()
	{
		$todo = $_REQUEST['todo'];
		$todo_ids = explode('_',$todo);

		//print_r($todo_ids);
		$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
		$temptableTodo=Mage::getSingleton('core/resource')->getTableName('todolist');
		
		foreach($todo_ids as $todo_id)
		{
		    echo $todo_id;
		    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
			{
		    $sqlOrganiger="DELETE FROM ".$temptableOrganiger." WHERE ot_id = '".$todo_id."' ";
		    $chkOrganiger1 = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlOrganiger);
		    
		    $sqlTodo="DELETE FROM ".$temptableTodo." WHERE ot_id = '".$todo_id."'";
		    $chkTodo = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlTodo);
			}
		}

	}
	
	//Delete the todo task
	
	public function deletetaskAction()
	{
		$task_id = $_REQUEST['task_id'];

		//print_r($todo_ids);
		$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
		$temptableTodo=Mage::getSingleton('core/resource')->getTableName('todolist');
		
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
		{
		$sqlOrganiger="DELETE FROM ".$temptableOrganiger." WHERE ot_id = '".$task_id."' ";
		$chkOrganiger1 = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlOrganiger);
		
		$sqlTodo="DELETE FROM ".$temptableTodo." WHERE ot_id = '".$task_id."'";
		$chkTodo = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlTodo);
		}
		

	}
	
	
	// Edit the todo task
	
	public function edittodotaskAction()
	{
		$task_id = $_REQUEST['task_id'];
		$task_id = trim($task_id,' ');
		if($task_id != '')
		{
		    
		    $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
		
		    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
			{
				$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_id = '".$task_id."' ";
				
				$chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
		    
			}
		
		echo '<form id="edit_form_task_1" name="edit_form_task_">
		    <input type="hidden" id="ot_id" name="ot_id" value="'.$task_id.'">
		    <input type="hidden" id="ot_entity_type" name="ot_entity_type" value="'.$chkOrganiger[0]['ot_entity_type'].'">
		    <input type="hidden" id="ot_entity_id" name="ot_entity_id" value="'.$chkOrganiger[0]['ot_entity_id'].'">
		    <input type="hidden" id="ot_entity_description" name="ot_entity_description" value="'.$chkOrganiger[0]['ot_entity_description'].'">
		    <input type="hidden" id="ot_event" name="ot_event"  >
		    <input type="hidden" id="ot_todo" name="ot_todo" value="1"  >
		    <input id="form_key" name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'" />
		    <div style="margin-left: 30px; margin-top: 66px;" "="" id="div_edit_task_">
		
			<fieldset id="my-fieldset">
			    <table cellspacing="0" border="0" style="width: 550px;" class="edittodotab">
				<tbody><tr>
				    <td class="label" width="18%">Entity</td>            	
				    <td class="input-ele" colspan="2">
					'.$chkOrganiger[0]['ot_entity_description'].'
				    </td>
				    <td>Created at  </td>
				    <td class="input-ele" id="createto" width="16%" >
					    <input type="text" id="ot_created_date" name="ot_created_date" value="'.$chkOrganiger[0]['ot_created_at'].'" class="ot_created_date">
						<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif'.'" class="v-middle" id="img_ot_create">
						<script type="text/javascript">
						    Calendar.setup({
							inputField : document.getElementById(\'edit_form_task_\').elements[\'ot_created_date\'],
							//inputField : document.getElementById(\'edit_form_task_\').elements[\'ot_create\'],
							ifFormat : \'%Y-%m-%e\',
							button : document.getElementById(\'edit_form_task_\').elements[\'img_ot_create\'],
							align : \'Bl\',
							singleClick : true
						    });
						    </script>
					</td>
				    </tr>
				    <tr>';
		//Add Permistion 'Able to edit author'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(8,$all_permission))
		{
				    echo '
					<td class="label" width="16%">Author</td>
					<td class="input-ele" width="16%">
					<select name="ot_author_user" id="ot_author_user">';
					
						$adminUserModel = Mage::getModel('admin/user');
						$userCollection = $adminUserModel->getCollection()->load();
						foreach($userCollection as $user)
						{
						    $selected = '';
						    if($chkOrganiger[0]['ot_author_user'] == $user->getId())
						    $selected = 'selected';
						    echo '<option value="'.$user->getId().'" '.$selected.'>'.$user->getUsername().'</option>';
						}
					   echo '</select>                        </td>';
		}
		
		//Add Permistion 'Able to edit notification date'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(14,$all_permission))
		{
					echo '<td class="label" width="16%">Notification Date</td>
					<td class="input-ele" width="16%">
					    <input size="6" type="text" id="ot_notify_date" name="ot_notify_date" value="'.$chkOrganiger[0]['ot_notify_date'].'">
					    <img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif'.'" class="v-middle" id="img_ot_notify_date">
					    <script type="text/javascript">
						Calendar.setup({
						    inputField : document.getElementById(\'edit_form_task_\').elements[\'ot_notify_date\'],
						    ifFormat : \'%Y-%m-%e\',
						    button : document.getElementById(\'edit_form_task_\').elements[\'img_ot_notify_date\'],
						    align : \'Bl\',
						    singleClick : true
						});
					    </script>
					</td>';
		}
		
		//Add Permistion 'Able to view notification date'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(15,$all_permission))
		{
					echo '<td class="label" width="16%">Notification Date</td>
					<td class="input-ele" width="16%">'.$chkOrganiger[0]['ot_notify_date'].'</td>';
		}
		
		//Add Permistion 'Able to edit priority'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(19,$all_permission))
		{
		
					echo '<td class="label" width="16%">Priority</td>
					<td class="input-ele" width="16%">
					    <select name="ot_priority" id="ot_priority">';
					    for($l=1;$l<=5;$l++)
					    {
						$selected =  '';
						if($chkOrganiger[0]['ot_priority'] == 1)$selected =  'selected';
						echo '<option value="1" '.$selected.'>1</option>';
					    }
						
					    echo '</select>
					</td>';
		}
		
		//Add Permistion 'Able to view priority'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(18,$all_permission))
		{
		
					echo '<td class="label" width="16%">Priority</td>
					<td class="input-ele" width="16%">'.$chkOrganiger[0]['ot_priority'].'</td>';
		}
					echo '</tr>
				    <tr>';
		//Add Permistion 'Able to edit taget user'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(9,$all_permission))
		{
				    echo '
					<td class="label" width="16%">Target</td>
					<td class="input-ele" width="16%">
					<select name="ot_target_user" id="ot_target_user">
					    <option value=""></option>';
					    
						$adminUserModel = Mage::getModel('admin/user');
						$userCollection = $adminUserModel->getCollection()->load();
						foreach($userCollection as $user)
						{
						    $selected = '';
						    if($chkOrganiger[0]['ot_target_user'] == $user->getId())
						    $selected = 'selected';
						    echo '<option value="'.$user->getId().'" '.$selected.'>'.$user->getUsername().'</option>';
						}
					   
					echo '</select>                                                        <input type="checkbox" value="1" name="notify_target" id="notify_target"> Notify                            </td>';
		}
		
		//Add Permistion 'Able to edit deadline'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(17,$all_permission))
		{
					echo '
					    <td class="label" width="16%">Dead line</td>
					    <td class="input-ele" width="16%">
						<input size="6" type="text" id="ot_deadline" name="ot_deadline" value="'.$chkOrganiger[0]['ot_deadline'].'">
						<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif'.'" class="v-middle" id="img_ot_deadline">
						<script type="text/javascript">
						    Calendar.setup({
							inputField : document.getElementById(\'edit_form_task_\').elements[\'ot_deadline\'],
							ifFormat : \'%Y-%m-%e\',
							button : document.getElementById(\'edit_form_task_\').elements[\'img_ot_deadline\'],
							align : \'Bl\',
							singleClick : true
						    });
						</script>
					    </td>';
		}
		
		//Add Permistion 'Able to view deadline'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(16,$all_permission))
		{
					echo '
					    <td class="label" width="16%">Dead line</td>
					    <td class="input-ele" width="16%">'.$chkOrganiger[0]['ot_deadline'].'</td>';
		}
		
		//Add Permistion 'Able to mark task as complete for folloing members'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(7,$all_permission))
		{
				    echo '
					    <td class="label" width="16%">Complete</td>
					    <td class="input-ele" width="16%"><input type="checkbox" value="'.$chkOrganiger[0]['ot_finished'].'" id="ot_finished" name="ot_finished"></td>';
		}
		else{
			 echo '
					    <td class="label" width="16%"></td>
					    <td class="input-ele" width="16%"><input type="hidden" value="'.$chkOrganiger[0]['ot_finished'].'" id="ot_finished" name="ot_finished"></td>';
		}
		
		//Add Permistion 'Able to view mark as complete'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(20,$all_permission))
		{
			
			if($chkOrganiger[0]['ot_finished'] == 1)
			$complete = 'Completed';
			else
			$complete = 'Uncomplete';
			
			echo '<td class="label" width="16%"></td>
				<td class="input-ele" width="16%">'.$complete.'</td>';
		}
		
				echo '</tr>
				';
		// Add the task type option		
		$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
		{          
		$sqlChain="SELECT * FROM ".$temptableChain." WHERE task_id = '$task_id'  ";
                                
		$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
		}
		if($chkChain1[0]['task_type'] == 'Chain')
		$cselected = 'selected';
		else if($chkChain1[0]['task_type'] == 'Independent')
		$iselected = 'selected';
		
		
		echo '<tr>
                     <td class="label" width="16%">'.$this->__('Task Type').'</td>
                        <td class="label" width="16%">
                            <select name="ot_type" id="ot_type">
                                <option value="">Select Task Type</option>
                                <option value="Chain" '.$cselected.' >Chain</option>
                                <option value="Independent" '.$iselected.' >Independent</option>
                            </select>
                        </td>
                </tr>';
		
		// Add the task item option	
		if($chkOrganiger[0]['ot_entity_type'] != 'product')
		{
                
                
                $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
		{
                $sqlChain="SELECT * FROM ".$temptableChain." WHERE task_id = '".$chkOrganiger[0]['ot_id']."' AND 
                                    order_quote_id = '".$chkOrganiger[0]['ot_entity_id']."'  ";
                                    
                $chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
		}
                echo '<tr>
                    <td class="label" width="16%">'.$this->__('Product').'</td>
                    <td class="input-ele" colspan="7">';
                            
                                
                                    $_newProduct = Mage::getModel('catalog/product')->load($chkChain1[0]['product_id']);
                                   
                                    echo $_newProduct->getName();
                              
                          
                    echo     '
                    </td>
                </tr>';
            
            }
		if($chkOrganiger[0]['ot_entity_type'] == 'product')
		{
			echo ' <tr id="number"  class="addtasktr">
			    <td class="label" width="16%">'.$this->__('Complition Day From Order').'</td>
			    <td class="input-ele">
				<input name="ot_day" id="ot_day" value="'.$chkOrganiger[0]['ot_day'].'"  />
			    </td>
			</tr>';
		}
		
		//Add Permistion 'Able to edit caption'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(10,$all_permission))
		{
				    echo '<tr>
				    <td class="label" width="16%">Caption</td>
				    <td class="input-ele" colspan="7"><input type="text" name="ot_caption" id="ot_caption" value="'.$chkOrganiger[0]['ot_caption'].'" size="40"></td>
				</tr>';
		}
		
		//Add Permistion 'Able to view caption'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(11,$all_permission))
		{
				    echo '<tr>
				    <td class="label" width="16%">Caption</td>
				    <td class="input-ele" colspan="7">'.$chkOrganiger[0]['ot_caption'].'</td>
				</tr>';
		}
		
		//Add Permistion 'Able to view description'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(12,$all_permission))
		{
				    echo '<tr>
				    <td class="label" width="16%">Description</td>
				    <td class="input-ele" colspan="7">'.$chkOrganiger[0]['ot_description'].'</td>
				</tr>';
		}
		//Add Permistion 'Able to edit description'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(13,$all_permission))
		{
				    echo '<tr>
				    <td class="label" width="16%">Description</td>
				    <td class="input-ele" colspan="7"><textarea name="ot_description" id="ot_description" cols="60" rows="5">'.$chkOrganiger[0]['ot_description'].'</textarea></td>
				    
				</tr>';
		}
				echo '<tr class="editdel">
				    <td class="label" width="16%"></td>
				    <td class="input-ele" colspan="7">
					    <button onclick="SubmitForm(\'1\', \'1\');" class="scalable save" type="button"><span>Save</span></button>
					</td></tr>
			    </tbody></table>
			</fieldset>
		
		    </div>
		
		
		</form>';
		
		}
		else{
		
		
		
		echo '<form id="edit_form_task_1" name="edit_form_task_">
		    <input type="hidden" id="ot_id" name="ot_id" value="">
		    <input type="hidden" id="ot_entity_type" name="ot_entity_type" value="">
		    <!--<input type="hidden" id="ot_created_date" name="ot_created_date" value="">-->
		    <input type="hidden" id="ot_entity_id" name="ot_entity_id" value="">
		    <input type="hidden" id="ot_entity_description" name="ot_entity_description" value="">
		    <input type="hidden" id="ot_event" name="ot_event"  >
		    <input type="hidden" id="ot_todo" name="ot_todo" value="1"  >
		    <input id="form_key" name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'" />
		    <div style="margin-left: 30px; margin-top: 66px;" "="" id="div_edit_task_">
		
			<fieldset id="my-fieldset">
			    <table cellspacing="0" border="0" style="width: 550px;" class="addtodotab">
				<tbody><tr>
				    <td class="label" width="18%">Entity</td>            	
				    <td class="input-ele" colspan="2">
					<span>
					    <select name="entity_type" id="entity_type1" onchange="checktype1(this.value);">
						<option value="">Select Task Type</div>
						<option value="order">Order</div>
						<option value="quote">Quote</div>
						<option value="product">Product</div>
					    </select>
					</span>
					<span id="order_id1" style="display:none;">';
						
					
					    echo '
					     <input type="text" id="order1" name="order" onblur="set_order1();"/>
					</span>
					<span id="quote_id1" style="display:none;">';
						  
					    echo '
					    <input type="text" id="quote1" name="quote" onblur="set_quote1();"/>
					</span>
					<span id="pro_id1" style="display:none;">';
						    
					   echo  '
					   <input type="text" id="product1" name="product" onblur="set_product1();"/>
					</span>
				    </td>
				    <td>Created at  </td>
				    <td class="input-ele" id="createto" width="16%" >
					    <input type="text" id="ot_created_date" name="ot_created_date" value="" class="ot_created_date">
						<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif'.'" class="v-middle" id="img_ot_create">
						<script type="text/javascript">
						    Calendar.setup({
							inputField : document.getElementById(\'edit_form_task_\').elements[\'ot_created_date\'],
							//inputField : document.getElementById(\'edit_form_task_\').elements[\'ot_create\'],
							ifFormat : \'%Y-%m-%e\',
							button : document.getElementById(\'edit_form_task_\').elements[\'img_ot_create\'],
							align : \'Bl\',
							singleClick : true
						    });
						    </script>
					</td>
				   
				    </tr>
				    <tr>
					<td class="label" width="16%">Author</td>
					<td class="input-ele" width="16%">
					<select name="ot_author_user" id="ot_author_user">
					    ';
						$adminUserModel = Mage::getModel('admin/user');
						$userCollection = $adminUserModel->getCollection()->load();
						foreach($userCollection as $user)
						{
						    echo '<option value="'.$user->getId().'" >'.$user->getUsername().'</option>';
						}
					   echo '</select>                        </td>
					<td class="label" width="16%">Notification Date</td>
					<td class="input-ele" width="16%">
					    <input size="6" type="text" id="ot_notify_date" name="ot_notify_date" value="">
					    <img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif'.'" class="v-middle" id="img_ot_notify_date">
					    <script type="text/javascript">
						Calendar.setup({
						    inputField : document.getElementById(\'edit_form_task_\').elements[\'ot_notify_date\'],
						    ifFormat : \'%Y-%m-%e\',
						    button : document.getElementById(\'edit_form_task_\').elements[\'img_ot_notify_date\'],
						    align : \'Bl\',
						    singleClick : true
						});
					    </script>
					</td>
					<td class="label" width="16%">Priority</td>
					<td class="input-ele" width="16%"><select name="ot_priority" id="ot_priority"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></td> </tr>
				    <tr>
					<td class="label" width="16%">Target</td>
					<td class="input-ele" width="16%">
					<select name="ot_target_user" id="ot_target_user">
					    <option value=""></option>
					    ';
						$adminUserModel = Mage::getModel('admin/user');
						$userCollection = $adminUserModel->getCollection()->load();
						foreach($userCollection as $user)
						{
						    echo '<option value="'.$user->getId().'" >'.$user->getUsername().'</option>';
						}
					   
					echo '</select>                                                        <input type="checkbox" value="1" name="notify_target" id="notify_target"> Notify                            </td>
					    <td class="label" width="16%">Dead line</td>
					    <td class="input-ele" width="16%">
						<input size="6" type="text" id="ot_deadline" name="ot_deadline" value="">
						<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif'.'" class="v-middle" id="img_ot_deadline">
						<script type="text/javascript">
						    Calendar.setup({
							inputField : document.getElementById(\'edit_form_task_\').elements[\'ot_deadline\'],
							ifFormat : \'%Y-%m-%e\',
							button : document.getElementById(\'edit_form_task_\').elements[\'img_ot_deadline\'],
							align : \'Bl\',
							singleClick : true
						    });
						</script>
					    </td>';
		//Add Permistion 'Able to mark task as complete for folloing members'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(7,$all_permission))
		{
				    echo '
					
					    <td class="label" width="16%">Complete</td>
					    <td class="input-ele" width="16%"><input type="checkbox" value="1" id="ot_finished" name="ot_finished"></td>
					    ';
		}
		else{
			 echo '
					    <td class="label" width="16%"></td>
					    <td class="input-ele" width="16%"><input type="hidden" value="1" id="ot_finished" name="ot_finished"></td>';
		}
		
		echo '<tr>
				<td class="label" width="16%">'.$this->__('Task Type').'</td>
				   <td class="label" width="16%">
				       <select name="ot_type" id="ot_type">
					   <option value="">Select Task Type</option>
					   <option value="Chain" >Chain</option>
					   <option value="Independent" >Independent</option>
				       </select>
				   </td>
			   </tr>';
		
		echo '<tr>
                     <td class="label" width="16%">'.$this->__('Product').'</td>
                        <td class="label" width="16%" id="task_item1">
                            <select name="ot_item" id="ot_item">
                                <option value="">Select Item</option>
                            </select>
                        </td>
                </tr>';
		
		echo ' <tr id="number1"  class="addtasktr">
			    <td class="label" width="16%">'.$this->__('Complition Day From Order').'</td>
			    <td class="input-ele">
				<input name="ot_day" id="ot_day"   />
			    </td>
			</tr>';
				   
				echo '</tr>
				<tr>
				    <td class="label" width="16%">Caption</td>
				    <td class="input-ele" colspan="7"><input type="text" name="ot_caption" id="ot_caption" value="" size="40"></td>
				</tr>
				<tr>
				    <td class="label" width="16%">Description</td>
				    <td class="input-ele" colspan="7"><textarea name="ot_description" id="ot_description" cols="60" rows="5"></textarea></td>
				    
				</tr>
				<tr class="editdel">
				    <td class="input-ele" colspan="7">
					    <button onclick="SubmitForm(\'1\', \'1\');" class="scalable save" type="button"><span>Save</span></button>
					</td></tr>
			    </tbody></table>
			</fieldset>
		
		    </div>
		
		
		</form>';
		
		}
		
	}
	
	
	// Edit the task
	
	public function edittaskAction()
	{
		$task_id = $_REQUEST['task_id'];
		$temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
		{

		$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_id = '".$task_id."' ";
		
		$chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
		
		}
		echo '<form id="edit_form_task_1" name="edit_form_task_">
		    <input type="hidden" id="ot_id" name="ot_id" value="'.$task_id.'">
		    <input type="hidden" id="ot_entity_type" name="ot_entity_type" value="'.$chkOrganiger[0]['ot_entity_type'].'">
		    <input type="hidden" id="ot_created_date" name="ot_created_date" value="'.$chkOrganiger[0]['ot_created_at'].'">
		    <input type="hidden" id="ot_entity_id" name="ot_entity_id" value="'.$chkOrganiger[0]['ot_entity_id'].'">
		    <input type="hidden" id="ot_entity_description" name="ot_entity_description" value="'.$chkOrganiger[0]['ot_entity_description'].'">
		    <input type="hidden" id="ot_event" name="ot_event"  >
		    <input id="form_key" name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'" />
		    <div style="margin-left: 30px; margin-top: 66px;" "="" id="div_edit_task_1">
		
			<fieldset id="my-fieldset">
			    <table cellspacing="0" border="0" style="width: 570px;" class="edittasktab">
				<tbody><tr>
				    <td class="label" width="18%">Entity</td>            	
				    <td class="input-ele" colspan="3">
				       '.$chkOrganiger[0]['ot_entity_description'].'
				    </td>
				    <td></td>
				    </tr>
				    <tr>';
		//Add Permistion 'Able to edit author'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(8,$all_permission))
		{
				    echo '
					<td class="label" width="16%">Author</td>
					<td class="input-ele" width="16%">
					<select name="ot_author_user" id="ot_author_user">
					    <option value=""></option>';
					    
						$adminUserModel = Mage::getModel('admin/user');
						$userCollection = $adminUserModel->getCollection()->load();
						foreach($userCollection as $user)
						{
						    $selected = '';
						    if($chkOrganiger[0]['ot_author_user'] == $user->getId())
						    $selected = 'selected';
						    echo '<option value="'.$user->getId().'" '.$selected.'>'.$user->getUsername().'</option>';
						}
					   
					echo '</select>                        </td>';
		}
		
		
		//Add Permistion 'Able to edit notification date'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(14,$all_permission))
		{
					echo '<td class="label" width="16%">Notification Date</td>
					<td class="input-ele" width="16%">
					    <input size="6" type="text" id="ot_notify_date" name="ot_notify_date" value="'.$chkOrganiger[0]['ot_notify_date'].'">
					    <img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif'.'" class="v-middle" id="img_ot_notify_date1">
					    <script type="text/javascript">
						Calendar.setup({
						    inputField : document.getElementById(\'edit_form_task_1\').elements[\'ot_notify_date\'],
						    ifFormat : \'%Y-%m-%e\',
						    button : document.getElementById(\'edit_form_task_1\').elements[\'img_ot_notify_date1\'],
						    align : \'Bl\',
						    singleClick : true
						});
					    </script>
					</td>';
		}
		
		//Add Permistion 'Able to view notification date'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(15,$all_permission))
		{
					echo '<td class="label" width="16%">Notification Date</td>
					<td class="input-ele" width="16%">'.$chkOrganiger[0]['ot_notify_date'].'</td>';
		}
		
		//Add Permistion 'Able to edit priority'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(19,$all_permission))
		{
		
					echo '<td class="label" width="16%">Priority</td>
					<td class="input-ele" width="16%">
					    <select name="ot_priority" id="ot_priority">';
					    for($l=1;$l<=5;$l++)
					    {
						$selected = '';
						if($chkOrganiger[0]['ot_priority'] == 1)$selected = 'selected';
						echo '<option value="1" '.$selected.'>1</option>';
					    }
						
				echo '</select>
					</td>';
		}
		
		//Add Permistion 'Able to view priority'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(18,$all_permission))
		{
		
					echo '<td class="label" width="16%">Priority</td>
					<td class="input-ele" width="16%">'.$chkOrganiger[0]['ot_priority'].'</td>';
		}
					
				    echo '</tr>
				    <tr>';
		//Add Permistion 'Able to edit taget user'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(9,$all_permission))
		{
				    echo '
					<td class="label" width="16%">Target</td>
					<td class="input-ele" width="16%">
					    <select name="ot_target_user" id="ot_target_user">
						<option value=""></option>';
						
						$adminUserModel = Mage::getModel('admin/user');
						$userCollection = $adminUserModel->getCollection()->load();
						foreach($userCollection as $user)
						{
						    $selected = '';
						    if($chkOrganiger[0]['ot_target_user'] == $user->getId())
						    $selected = 'selected';
						    echo '<option value="'.$user->getId().'" '.$selected.'>'.$user->getUsername().'</option>';
						}
						
					    echo '</select>
					    <input type="checkbox" value="'.$chkOrganiger[0]['ot_notification_read'].'" name="notify_target" id="notify_target"> Notify                            </td>';
		}
		
		//Add Permistion 'Able to edit deadline'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(17,$all_permission))
		{
					echo '
					    <td class="label" width="16%">Dead line</td>
					    <td class="input-ele" width="16%">
						<input size="6" type="text" id="ot_deadline" name="ot_deadline" value="'.$chkOrganiger[0]['ot_deadline'].'">
						<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif'.'" class="v-middle" id="img_ot_deadline1">
						<script type="text/javascript">
						    Calendar.setup({
							inputField : document.getElementById(\'edit_form_task_1\').elements[\'ot_deadline\'],
							ifFormat : \'%Y-%m-%e\',
							button : document.getElementById(\'edit_form_task_1\').elements[\'img_ot_deadline1\'],
							align : \'Bl\',
							singleClick : true
						    });
						</script>
					    </td>';
		}
		
		//Add Permistion 'Able to view deadline'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(16,$all_permission))
		{
					echo '
					    <td class="label" width="16%">Dead line</td>
					    <td class="input-ele" width="16%">'.$chkOrganiger[0]['ot_deadline'].'</td>';
		}
		
		//Add Permistion 'Able to mark task as complete for folloing members'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(7,$all_permission))
		{
				    echo '
					    <td class="label" width="16%">Complete</td>
					    <td class="input-ele" width="16%"><input type="checkbox" value="1" id="ot_finished" name="ot_finished"></td>';
		}
		
		//Add Permistion 'Able to view mark as complete'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(20,$all_permission))
		{
			
			if($chkOrganiger[0]['ot_finished'] == 1)
			$complete = 'Completed';
			else
			$complete = 'Uncomplete';
			
			echo '<td class="label" width="16%"></td>
				<td class="input-ele" width="16%">'.$complete.'</td>';
		}
				echo '
				</tr>
				';
				
		$temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
		{        
		$sqlChain="SELECT * FROM ".$temptableChain." WHERE task_id = '$task_id'  ";
                                
		$chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
		}
		if($chkChain1[0]['task_type'] == 'Chain')
		$cselected = 'selected';
		else if($chkChain1[0]['task_type'] == 'Independent')
		$iselected = 'selected';
		
		
		echo '<tr>
                     <td class="label" width="16%">'.$this->__('Task Type').'</td>
                        <td class="label" width="16%">
                            <select name="ot_type" id="ot_type">
                                <option value="">Select Task Type</option>
                                <option value="Chain" '.$cselected.' >Chain</option>
                                <option value="Independent" '.$iselected.' >Independent</option>
                            </select>
                        </td>
                </tr>';
		
		// Add the task item option	
		if($chkOrganiger[0]['ot_entity_type'] != 'product')
		{
                
                $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
		{ 
                $sqlChain="SELECT * FROM ".$temptableChain." WHERE task_id = '".$chkOrganiger[0]['ot_id']."' AND 
                                    order_quote_id = '".$chkOrganiger[0]['ot_entity_id']."'  ";
                                    
                $chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
		}
                echo '<tr>
                    <td class="label" width="16%">'.$this->__('Product').'</td>
                    <td class="input-ele" colspan="7">';
                            
                                    $_newProduct = Mage::getModel('catalog/product')->load($chkChain1[0]['product_id']);
                                    
                                    
                                    echo $_newProduct->getName();
                               
                          
                    echo     '
                    </td>
                </tr>';
            
		}
		
		//Add Permistion 'Able to edit caption'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(10,$all_permission))
		{
				    echo '<tr>
					<td class="label" width="16%">Caption</td>
					<td class="input-ele" colspan="7"><input type="text" name="ot_caption" id="ot_caption" value="'.$chkOrganiger[0]['ot_caption'].'" size="40"></td>
				    </tr>';
		}
		
		//Add Permistion 'Able to view caption'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(11,$all_permission))
		{
				    echo '<tr>
				    <td class="label" width="16%">Caption</td>
				    <td class="input-ele" colspan="7">'.$chkOrganiger[0]['ot_caption'].'</td>
				</tr>';
		}
		
		//Add Permistion 'Able to view description'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
            
		if(in_array(12,$all_permission))
		{
				    echo '<tr>
				    <td class="label" width="16%">Description</td>
				    <td class="input-ele" colspan="7">'.$chkOrganiger[0]['ot_description'].'</td>
				</tr>';
		}
		//Add Permistion 'Able to edit description'
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if($chkOrganiger[0]['ot_entity_type'] == 'product')
		{
			echo ' <tr id="number"  class="addtasktr">
			    <td class="label" width="16%">'.$this->__('Complition Day From Order').'</td>
			    <td class="input-ele">
				<input name="ot_day" id="ot_day" value="'.$chkOrganiger[0]['ot_day'].'"  />
			    </td>
			</tr>';
		}
            
		if(in_array(13,$all_permission))
		{
				    echo '<tr>
				    <td class="label" width="16%">Description</td>
				    <td class="input-ele" colspan="7"><textarea name="ot_description" id="ot_description" cols="60" rows="5">'.$chkOrganiger[0]['ot_description'].'</textarea></td>
				    
				</tr>';
		}
				echo '
				<tr class="editdel">
				    <td class="label" width="16%"></td>
				    <td class="input-ele" colspan="7">
					    <button onclick="SubmitForm(\'1\', \'1\');" class="scalable save" type="button"><span>Save</span></button>';
					    
		//Add Permistion 'Able to delete task for following members'
	    
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		
		if(in_array(6,$all_permission))
		{
			echo '<button onclick="deletetask('.$task_id.');" class="scalable save" type="button"><span>Delete</span></button>';
		}
		
					echo'</td></tr>
			    </tbody></table>
			</fieldset>
		
		    </div>
		
		
		</form>';

	}

	
	// Select the country in world clock
	
	public function clockAction()
	{
		$clockid = $_REQUEST['clockid'];
		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		
		if(strpos(Mage::getSingleton('core/session')->getRemoveclock(),$clockid))
		{
		    $nowholi = str_replace("'".$clockid."',",'',Mage::getSingleton('core/session')->getRemoveclock());
		    
		    $temptableClock=Mage::getSingleton('core/resource')->getTableName('clock');
		    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableClock))
			{	 
		    $sqlClock="DELETE FROM ".$temptableClock." WHERE user_id = '".$userId."' AND clock_id = '".$clockid."' ";
		    $chkClock = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlClock);
			}
		    
		}
		else{
		    $nowholi = Mage::getSingleton('core/session')->getRemoveclock()."'".$clockid."',";
			
		    $temptableClock=Mage::getSingleton('core/resource')->getTableName('clock');
		     if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableClock))
			{
		    $sqlClock="INSERT INTO ".$temptableClock." SET user_id = '".$userId."', clock_id = '".$clockid."' ";
		    $chkClock = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlClock);
			}
		}
		Mage::getSingleton('core/session')->setRemoveclock($nowholi);
		echo Mage::getSingleton('core/session')->getRemoveclock()."@".$clockid;

	}
	
	public function holisubmitAction()
	{
		$country = $_REQUEST['country'];
		$from_date = $_REQUEST['from_date'];
		//$to_date = $_REQUEST['to_date'];
		$caption = $_REQUEST['caption'];
		
		if($country != '' and $from_date != '' and $caption != '')
		{
		    $temptableHoli=Mage::getSingleton('core/resource')->getTableName('holiday');
		     if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoli))
			{
		    $sqlHoli="SELECT * FROM ".$temptableHoli." WHERE country_name = '".$country."' LIMIT 1 ";
		    $chkselect = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlHoli);
			
		    
		    $sqlHoli1="INSERT INTO ".$temptableHoli." SET country_name = '".$country."', h_date = '".$from_date."', event = '".$caption."', color = '".$chkselect[0]['color']."' , addby = 'manually' ";
		    $chkHoli = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHoli1);
			}
		}
		else{
			echo 'All field are madetory.';
		}

	}
	
	public function holideleteAction()
	{
		$holi_id = $_REQUEST['holi_id'];
		
		if($holi_id != '')
		{
		    $temptableHoli=Mage::getSingleton('core/resource')->getTableName('holiday');
		    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableHoli))
			{
		    $sqlHoli1="DELETE FROM ".$temptableHoli." WHERE entity_id = '".$holi_id."' ";
		    $chkHoli = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlHoli1);
		    
		    echo 'Holiday successfully deleted.';
			}
		}

	}
	
	public function chkpermissionAction()
	{
		$all_permission = Mage::getSingleton('core/session')->getAllpermission();
		//print_r($all_permission);
		
		if(in_array(1,$all_permission))
		{
			echo 'odd-even-more-tabold';
		}

	}
	
	public function loadtaskitemAction()
	{
		$order_quote_id = $_REQUEST['order_quote_id'];
		$entity_type = $_REQUEST['entity_type'];
		
		if($entity_type == 'order')
                {
                   $order = Mage::getModel('sales/order')->load($order_quote_id);
                   $ItemCollection = $order->getAllItems();
                   
                }
                else if($entity_type == 'quote')
                {
                   $quote = Mage::getModel('Quotation/Quotation')->load($order_quote_id);
                   $ItemCollection = $quote->getItems();
                   
                }
		
		echo '<select name="ot_item" id="ot_item">
                                <option value="">Select Item</option>';
		
		foreach($ItemCollection as $items)
		{
		    $_newProduct = Mage::getModel('catalog/product')->load($items->getProductId());
		    
		    
		    echo '<option value="'.$items->getProductId().'" >'.$_newProduct->getName().'</option>';
		}
		
		echo '</select>';
                       
	}
        function daydetailsAction()
        {
		echo $this->fetchdaydetails();
	}

	protected function fetchdaydetails(){
		ob_start();	
		if(!isset($_GET['daydate'])){
	                extract($_REQUEST);
			$daydate=$_GET['daydate']=$y."-".$m."-".$d;
		}
		extract($_REQUEST);
		
		echo '<div class="detailsmain" style="margin-top: 10px;">
			<div class="headdate">'.date("jS F, Y", strtotime($daydate)).'</div>';
		
		// added the proofs module
		$temptableProof=Mage::getSingleton('core/resource')->getTableName('proofs');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
                {
			$sqlProof="SELECT * FROM ".$temptableProof." WHERE  status = 'Awaiting Proof Approval' AND postdate LIKE  '%".$daydate."%'";
			$chkProofs = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlProof);
			if($chkProofs)
			{
				echo '<div class="deatilshead">Proofs :</div>';
				$i++;
				foreach($chkProofs as $chkProof)
				{
					if($i%2 == 0)
					$css = 'class="even1"';
					else
					$css = 'class="odd1"';
					
					
					
					if($chkProof['proof_type'] == 'order')
					{
						$temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
						$sqlItem="SELECT * FROM ".$temptableItem." WHERE item_id = '".$chkProof['item_id']."'";
						$chkItem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);

						$url= $this->getUrl('adminhtml/sales_order/view/order_id/'.$chkProof['order_id']);
						$url = str_replace('p//s','p/admin/s',$url);

// fetch increment id
						$oderModel=Mage::getModel('sales/order')->load($chkProof['order_id']);
						$incrementId=$oderModel->getIncrementId();
					}
					else if($chkProof['proof_type'] == 'quote')
					{
						$temptableItem=Mage::getSingleton('core/resource')->getTableName('quotation_items');
						$sqlItem="SELECT * FROM ".$temptableItem." WHERE quotation_item_id = '".$chkProof['item_id']."'";
						$chkItem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);

						$url= $this->getUrl('Quotation/Admin/edit/quote_id/'.$chkProof['order_id']);
						$url = str_replace('p//s','p/admin/s',$url);

// fetch increment id
						$quoteModel=Mage::getModel('Quotation/Quotation')->load($chkProof['order_id']);
						$incrementId=$quoteModel->getIncrementId();
					}
					
					$_proofProduct = Mage::getModel('catalog/product')->load($chkItem[0]['product_id']);
					$proof_caption = $_proofProduct->getSku();
					
					$pro = explode(',',Mage::getSingleton('core/session')->getPro());
				
					if(in_array($chkItem[0]['product_id'],$pro) or (trim(Mage::getSingleton('core/session')->getPro()) == ''))
					echo  '<div '.$css.' style="text-align: left;" > <a href="'.$url.'" target="_blank">'.$chkProof['proof_type'].$chkProof['order_id']."---".$incrementId." ".$proof_caption.'...Proof Approval Waiting</a></div>';
					$i++;
				}
			}
		}
		
		
		// added the design module
		$temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableDesign))
                {
			$sqlDesign="SELECT * FROM ".$temptableDesign." WHERE  postdate LIKE  '%".$daydate."%'";
			$chkDesigns = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchall($sqlDesign);
			if($chkDesigns)
			{
				echo '<div class="deatilshead"> Design :</div>';
				$i++;
				foreach($chkDesigns as $chkDesign)
				{
					if($i%2 == 0)
					$css = 'class="even1"';
					else
					$css = 'class="odd1"';
					
					if($chkDesign['proof_type'] == 'order')
					{
						$temptableItem=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
						$sqlItem="SELECT * FROM ".$temptableItem." WHERE item_id = '".$chkDesign['item_id']."'";
						$chkItem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);

						$url= $this->getUrl('adminhtml/sales_order/view/order_id/'.$chkDesign['order_quote_id']);
						$url = str_replace('p//s','p/admin/s',$url);
// fetch increment id
						$oderModel=Mage::getModel('sales/order')->load($chkDesign['order_quote_id']);
						$incrementId=$oderModel->getIncrementId();

					}
					else if($chkDesign['proof_type'] == 'quote')
					{
						$temptableItem=Mage::getSingleton('core/resource')->getTableName('quotation_items');
						$sqlItem="SELECT * FROM ".$temptableItem." WHERE quotation_item_id = '".$chkDesign['item_id']."'";
						$chkItem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlItem);

						$url= $this->getUrl('Quotation/Admin/edit/quote_id/'.$chkDesign['order_quote_id']);
						$url = str_replace('p//s','p/admin/s',$url);

// fetch increment id
						$quoteModel=Mage::getModel('Quotation/Quotation')->load($chkDesign['order_quote_id']);
						$incrementId=$quoteModel->getIncrementId();

					}
					
					$_proofProduct = Mage::getModel('catalog/product')->load($chkItem[0]['product_id']);
					$proof_caption = $_proofProduct->getSku();
				
					$pro = explode(',',Mage::getSingleton('core/session')->getPro());
					if(in_array($chkItem[0]['product_id'],$pro) or (trim(Mage::getSingleton('core/session')->getPro()) == ''))
					echo  '<div '.$css.' style="text-align: left;" > <a href="'.$url.'" target="_blank">'.$incrementId." ".$proof_caption.'...'.$chkDesign['status'].'</a></div>';
					$i++;
				}
			}
		}
		
		// added the quote module
		$temptableQuote=Mage::getSingleton('core/resource')->getTableName('quotation');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableQuote))
                {
			$sqlQuote="SELECT * FROM ".$temptableQuote." WHERE   created_time LIKE  '%".$daydate."%'";
			$chkQuotes = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlQuote);
			if($chkQuotes)
			{
				echo '<div class="deatilshead">Generate Quotes :</div>';
				$i=1;
				foreach($chkQuotes as $chkQuote)
				{
					if($i%2 == 0)
					$css = 'class="even1"';
					else
					$css = 'class="odd1"';
					
					$url= $this->getUrl('Quotation/Admin/edit/quote_id/'.$chkQuote['quotation_id']);
					$url = str_replace('p//s','p/admin/s',$url);
					echo  '<div '.$css.' style="text-align: left;" > <a href="'.$url.'" target="_blank">'.$chkQuote['increment_id'].'</a></div>';
					$i++;
				}
			}
		}
		
		// added the Invoice
		$temptableInvoice=Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice');
		$sqlInvoice="SELECT * FROM ".$temptableInvoice." WHERE   created_at LIKE  '%".$daydate."%'";
		$chkInvoices = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlInvoice);
		if($chkInvoices)
		{
			echo '<div class="deatilshead">Generated Invoices :</div>';
			$i=1;
			foreach($chkInvoices as $chkInvoice)
			{
				if($i%2 == 0)
				$css = 'class="even1"';
				else
				$css = 'class="odd1"';
				
				$url= $this->getUrl('adminhtml/sales_invoice/view/invoice_id/'.$chkInvoice['entity_id']);
				$url = str_replace('p//s','p/admin/s',$url);
				echo  '<div '.$css.' style="text-align: left;" ><a target="_blank" href="'.$url.'"> '.$chkInvoice['increment_id'].'</a></div>';
				$i++;
			}
		}
		
		// added the Payment
		$temptablePayment=Mage::getSingleton('core/resource')->getTableName('sales_payment_transaction');
		$sqlPayment="SELECT * FROM ".$temptablePayment." WHERE   created_at LIKE  '%".$daydate."%'";
		$chkPayments = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlPayment);
		if($chkPayments)
		{
			echo '<div class="deatilshead">Payments Recieved :</div>';
			$i=1;
			foreach($chkPayments as $chkPayment)
			{
				if($i%2 == 0)
				$css = 'class="even1"';
				else
				$css = 'class="odd1"';
				$order = Mage::getModel('sales/order')->load($chkPayment['order_id']);
				
				echo  '<div '.$css.' style="text-align: left;" > '.$order->getIncrementId().'</div>';
				$i++;
			}
		}
		
		// added the Shipment
		$temptableShipment=Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment');
		$sqlShipment="SELECT * FROM ".$temptableShipment." WHERE   created_at LIKE  '%".$daydate."%'";
		$chkShipments = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlShipment);
		if($chkShipments)
		{
			echo '<div class="deatilshead">Deliveries :</div>';
			$i=1;
			foreach($chkShipments as $chkShipment)
			{
				//$order = Mage::getModel('sales/order')->load($chkShipment['order_id']);
				
				if($i%2 == 0)
				$css = 'class="even1"';
				else
				$css = 'class="odd1"';
				
				$url= $this->getUrl('adminhtml/sales_shipment/view/shipment_id/'.$chkShipment['entity_id']);
				$url = str_replace('p//s','p/admin/s',$url);
			
			
				echo  '<div '.$css.' style="text-align: left;" > <a href="'.$url.'" target="_blank">'.$chkShipment['planning_type']." ".$chkShipment['increment_id'].'</a></div>';
				$i++;
			}
		}
		
		//added the Planning
		$temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptablePlanning))
                {
			$sqlPlanning="SELECT * FROM ".$temptablePlanning." WHERE   order_placed_date LIKE  '%".$daydate."%' OR artwork_date LIKE  '%".$daydate."%' OR proof_date LIKE  '%".$daydate."%' OR start_date LIKE  '%".$daydate."%' OR shipping_date LIKE  '%".$daydate."%' OR delivery_date LIKE  '%".$daydate."%'";
			$chkPlannings = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlPlanning);
			if($chkPlannings)
			{
				echo '<div class="deatilshead">Order & Quote Planning :</div>';
				$i=1;
				foreach($chkPlannings as $chkPlanning)
				{
					//$order = Mage::getModel('sales/order')->load($chkShipment['order_id']);
					
					if($i%2 == 0)
					$css = 'class="even1"';
					else
					$css = 'class="odd1"';
					
					if($chkPlanning['planning_type'] == 'order')
					{
						$order = Mage::getModel('sales/order')->load($chkPlanning['quote_id']);
						$_Product = Mage::getModel('catalog/product')->load($chkPlanning['product_id']);
						$shu_caption = $_Product->getSku();
						
						$url= $this->getUrl('admin/sales_order/view/order_id/'.$chkPlanning['quote_id']);
						$url = str_replace('p//s','p/admin/s',$url);
						
						$maincaption = 'Order '.$order->getIncrementId().' - '.$shu_caption;
					}
					else if($chkPlanning['planning_type'] == 'quote')
					{
						$order = Mage::getModel('Quotation/Quotation')->load($chkPlanning['quote_id']);
						$_Product = Mage::getModel('catalog/product')->load($chkPlanning['product_id']);
						$shu_caption = $_Product->getSku();
						
						$url= $this->getUrl('Quotation/Admin/edit/quote_id/'.$chkPlanning['quote_id']);
						$url = str_replace('p//s','p/admin/s',$url);
						
						$maincaption = 'Quote '.$order->getIncrementId().' - '.$shu_caption;
					}
					
					if($chkPlanning['order_placed_date'] == $daydate)
					{
						$caption = 'Order has been placed';
					}
					else if($chkPlanning['artwork_date'] == $daydate)
					{
						$caption = 'Artwork approval date';
					}
					else if($chkPlanning['proof_date'] == $daydate)
					{
						$caption = 'Proofs approval date';
					}
					else if($chkPlanning['start_date'] == $daydate)
					{
						$caption = 'Production Start Date';
					}
					else if($chkPlanning['shipping_date'] == $daydate)
					{
						$caption = 'Product Shipping Date';
					}
					else if($chkPlanning['delivery_date'] == $daydate)
					{
						$caption = 'Product Delivery Date';
					}
					
					$pro = explode(',',Mage::getSingleton('core/session')->getPro());
						
					if(in_array($chkPlanning['product_id'],$pro) or (trim(Mage::getSingleton('core/session')->getPro()) == ''))
					echo  '<div '.$css.' style="text-align: left;" > <a href="'.$url.'" target="_blank">'.$maincaption.' --- '.$caption.'</a></div>';
					$i++;
				}
			}
		}
		
		echo '</div>';
		
		$contents=ob_get_contents();
		ob_end_clean();

		return $contents;	
	}

    /********************************** Custom controller action for ajax *****************************************/
}
