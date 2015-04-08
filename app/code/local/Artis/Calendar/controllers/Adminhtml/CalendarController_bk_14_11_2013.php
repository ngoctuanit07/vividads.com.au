<?php

class Artis_Calendar_Adminhtml_CalendarController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('calendar/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('calendar/calendar')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('calendar_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('calendar/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('calendar/adminhtml_calendar_edit'))
				->_addLeft($this->getLayout()->createBlock('calendar/adminhtml_calendar_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('calendar')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		//print_r($_REQUEST);exit;
		
		$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
		$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE country_name = '".$_REQUEST['title']."'";
		$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlHoliday);
		
		if(!$chkHoliday[0]['entity_id'])
		{
		
		if ($data = $this->getRequest()->getPost()) {
			
			/**************************************************************/
			if($_REQUEST['link'] != '')
			{
				$contents = file_get_contents($_REQUEST['link']);
							
				//file_put_contents(Mage::getBaseDir(). DS.'calender_xml' . DS.$_REQUEST['title'].'.xml', $contents);
			}
			
			$holidays = simplexml_load_string($contents);
			
			for($i=0;$i<=count($holidays->entry);$i++)
			{
			    
				$event = $holidays->entry[$i]->title;
				$summary = $holidays->entry[$i]->summary;
				$summery_all = explode(' ',$summary);
				//print_r($summery_all);
				
				//$first_str = explode(' ',$summery_all[0]);
				if($summery_all[2] < 10 )
				$date = '0'.$summery_all[2];
				else
				$date = $summery_all[2];
				
				//$key = array_search($date, $first_str);
				
				//$month1 = $first_str[$key-1];
				
				$month1 = date('m', strtotime($summery_all[3]));
				
				//$year1 = explode(' ',$summery_all[1]);
				$year1 = explode('<br',$summery_all[4]);
				$year = $year1[0];
				
				//print_r($year1[0]);
				
				$holi[$date][$month][$year] =   $event;
				$date1 = $year.'-'.$month1.'-'.$date;
			    
				if($event != '')
				{
					$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
					$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$date1."' AND event ='".$event."'";
					$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlHoliday);
					//print_r($chkHoliday[0]['date']);
					if($chkHoliday[0]['h_date'] == '')
					{
						$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
						$sqlHoliday="INSERT INTO ".$temptableHoliday." SET country_name = '".$_REQUEST['title']."', h_date = '".$date1."' , event ='".$event."', color = '".$_REQUEST['color']."'";
						$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlHoliday);
					}
				}
			}
		
			
			
			/*************************************************************/
	//		
	//		if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
	//			try {	
	//				/* Starting upload */	
	//				$uploader = new Varien_File_Uploader('filename');
	//				
	//				// Any extention would work
	//           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	//				$uploader->setAllowRenameFiles(false);
	//				
	//				// Set the file upload mode 
	//				// false -> get the file directly in the specified folder
	//				// true -> get the file in the product like folders 
	//				//	(file.jpg will go in something like /media/f/i/file.jpg)
	//				$uploader->setFilesDispersion(false);
	//						
	//				// We set media as the upload dir
	//				$path = Mage::getBaseDir('media') . DS ;
	//				$uploader->save($path, $_FILES['filename']['name'] );
	//				
	//			} catch (Exception $e) {
	//	      
	//	        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			//}
	  			
	  			
			$model = Mage::getModel('calendar/calendar');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('calendar')->__('Item was successfully saved'));
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
	}
	else
	{
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('calendar')->__('THis Country name allready exit.'));
		Mage::getSingleton('adminhtml/session')->setFormData($data);
		$this->_redirect('*/*/');
	}
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('calendar')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('calendar/calendar');
				 
			
					
				/*****************************************************************/
				$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('calendar');
				$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE calendar_id ='".$this->getRequest()->getParam('id')."'";
				$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetch($sqlHoliday);
				
				$temptableHoliday1=Mage::getSingleton('core/resource')->getTableName('holiday');
				$sqlHoliday1="DELETE FROM ".$temptableHoliday1." SET country_name = '".$chkHoliday['title']."'";
				$chkHoliday1 = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlHoliday1);
				/****************************************************************/
				//exit;
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
        $calendarIds = $this->getRequest()->getParam('calendar');
        if(!is_array($calendarIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($calendarIds as $calendarId) {
			
			/*****************************************************************/
			$temptableHoliday=Mage::getSingleton('core/resource')->getTableName('calendar');
			$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE calendar_id ='".$calendarId."'";
			$chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlHoliday);
			
			$temptableHoliday1=Mage::getSingleton('core/resource')->getTableName('holiday');
			$sqlHoliday1="DELETE FROM ".$temptableHoliday1." WHERE country_name = '".$chkHoliday[0]['title']."'";
			$chkHoliday1 = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlHoliday1);
			/****************************************************************/
			
                    $calendar = Mage::getModel('calendar/calendar')->load($calendarId);
                    $calendar->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($calendarIds)
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
        $calendarIds = $this->getRequest()->getParam('calendar');
        if(!is_array($calendarIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($calendarIds as $calendarId) {
                    $calendar = Mage::getSingleton('calendar/calendar')
                        ->load($calendarId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($calendarIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'calendar.csv';
        $content    = $this->getLayout()->createBlock('calendar/adminhtml_calendar_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'calendar.xml';
        $content    = $this->getLayout()->createBlock('calendar/adminhtml_calendar_grid')
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
    
     
    public function buildcalendarAction() {
    
    
	extract($_REQUEST);
            // Create array containing abbreviations of days of week.
            $daysOfWeek = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
       
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
            $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
            if(Mage::getSingleton('core/resource')
->getConnection('core_write')
->isTableExists(trim($temptableHoliday,'`')) !== false)
            {
            
            $sqlHoliday="SELECT * FROM ".$temptableHoliday;
            $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlHoliday);
            
            foreach($chkHoliday as $holidays)
            {
                if($holyday[$holidays['h_date']] != '')
                $holyday[$holidays['h_date']] .= '<br/> '.$holidays['country_name'].' :: '.$holidays['event'];
                else
                $holyday[$holidays['h_date']] = $holidays['country_name'].' :: '.$holidays['event'];
            }
            }
            
            /******************* End Holiday parse ***********************/
           
            
            
            // Create the table tag opener and day headers
       
            $calendar = "<table width='100%' border='0' cellspacing='0'  cellspadding='0' class='event_calendar'>";
            $calendar .= "<caption><b>$monthName $year</b></caption>";     
            $calendar .= "<tr>
                           <td colspan='".$numberDays."'>
                               <div style='float:left;'>        
                                   <a href='javascript:void(0);' class='lft_arr' onclick='prev_month($month,($prev_year-1));'><</a>
                               </div>
                               <div style='float:left; margin-left:50px;'>        
                                   <a href='javascript:void(0);' class='lft_arr_year' onclick='prev_month($prev_month,$prev_year);'><<</a>
                               </div>
                               <div style='float:left; margin-left:50px;'>        
                                   <a href='javascript:void(0);' class='right_arr_year' onclick='next_month($next_month,$next_year);'>>></a>
                               </div>
                               <div style='float:right;'>        
                                   <a href='javascript:void(0);' class='right_arr' onclick='prev_month($month,($next_year+1));'>></a>  
                               </div>    
                           </td></tr>";
            $calendar .= "<tr>";
       
            // Create the calendar headers
           $num=$dayOfWeek; 
           for($d=0;$d<$numberDays;$d++) {
               
               if($d%7==0)
               {
                   $calendar .= "<td class='week_header' colspan='7'>Week".date("W",mktime(0,0,0,$month,($d+1),$year))."</td>";
               }
            
              $num++;
               if($num>6)
               {
                 $num=0;
               }
            }
            
            
            $calendar .= "</tr><tr>";
            
            $num=$dayOfWeek;
            for($d=0;$d<$numberDays;$d++) {
               
                   
                       
               
               
                 $calendar .= '<td align="right" class="day_header">'.
                 
                 
                                   $daysOfWeek[$num].'
                                   
                                  
                                       
                 
                 
                 
                 
                 
                 
                 </td>';
                 $num++;
                 
                 if($num>6)
                 {
                   $num=0;
                 }
            } 
       
            // Create the rest of the calendar
       
            // Initiate the day counter, starting with the 1st.
       
            $currentDay = 1;
       
            $calendar .= "</tr><tr>";
       
            // The variable $dayOfWeek is used to
            // ensure that the calendar
            // display consists of exactly 7 columns.
       
            if ($dayOfWeek > 0) { 
                 //$calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>"; 
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
                   
                   
                   
                    
                   $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('quotation');
                   $sqlSaleOrder="SELECT count(increment_id) as ctct FROM ".$temptableSaleOrder." WHERE created_time between '".$start_time."' and '".$end_time."'";
                   try {
                       $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
                       
                   
                   
                   
                   } catch (Exception $e){
                   //echo $e>getMessage();
                   }
                   $tot_quote_placed=0;
                   foreach($chkSaleOrder as $res_chkSaleOrder) 
                   {
                      $tot_quote_placed= $res_chkSaleOrder["ctct"];
                      
                      
                   }
                   
                   $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice');
                   $sqlSaleOrder="SELECT count(entity_id) as ctct FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
                   try {
                       $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
                       
                   
                   
                   
                   } catch (Exception $e){
                   //echo $e>getMessage();
                   }
                   $tot_invoice_placed=0;
                   foreach($chkSaleOrder as $res_chkSaleOrder) 
                   {
                      $tot_invoice_placed= $res_chkSaleOrder["ctct"];
                      
                      
                   }
                   
                   
                   $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
                   $sqlSaleOrder="SELECT sum(total_paid) as ctct FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
                   try {
                       $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
                       
                   
                   
                   
                   } catch (Exception $e){
                   //echo $e>getMessage();
                   }
                   $total_paid=0;
                   foreach($chkSaleOrder as $res_chkSaleOrder) 
                   {
                      if($res_chkSaleOrder["ctct"]>0)
                      {
                        $total_paid=number_format($res_chkSaleOrder["ctct"],2);
                      }
                      else
                      {
                        $total_paid=0;
                      }
                      
                      
                   }
                   
                   
                   $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
                   $sqlSaleOrder="SELECT count(entity_id) as ctct FROM ".$temptableSaleOrder." WHERE status='pending' and created_at between '".$start_time."' and '".$end_time."'";
                   try {
                       $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
                       
                   
                   
                   
                   } catch (Exception $e){
                   //echo $e>getMessage();
                   }
                   $pending_payments=0;
                   foreach($chkSaleOrder as $res_chkSaleOrder) 
                   {
                     if($res_chkSaleOrder["ctct"]>0)
                     {
                      $pending_payments=$res_chkSaleOrder["ctct"];
                     }
                     else
                     {
                        $pending_payments=0;
                        
                     }
                      
                      
                   }
                   
                   
                   
                   $temptableSaleOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment');
                   $sqlSaleOrder="SELECT sum(total_qty) as ctct FROM ".$temptableSaleOrder." WHERE created_at between '".$start_time."' and '".$end_time."'";
                   try {
                       $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlSaleOrder);
                       
                   
                   
                   
                   } catch (Exception $e){
                   //echo $e>getMessage();
                   }
                   $item_shipped=0;
                   foreach($chkSaleOrder as $res_chkSaleOrder) 
                   {
                        
                        if($res_chkSaleOrder["ctct"]>0)
                        {
                            $item_shipped=number_format($res_chkSaleOrder["ctct"],0);      
                        }
                        else
                        {
                            $item_shipped=0;
                        }
                      
                      
                      
                   }
                   
                   
                   //All query end
               
               
       
                 // Seventh column (Saturday) reached. Start a new row.
       
                 if ($dayOfWeek == 7) {
       
                      $dayOfWeek = 0;
                     // $calendar .= "</tr><tr>";
       
                 }
                 //Mage::getBaseUrl()."/admin/sales_order/view/order_id/";
                 
                 $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
                 
                 $date = "$year-$month-$currentDayRel";
                 
                 //if($tot_invoice_placed>0 || $tot_quote_placed>0 || $item_shipped>0 || $pending_payments>0 || $total_paid>0 || $holyday[$year.'-'.$month.'-'.$currentDay1] != '')
		if( $holyday[$year.'-'.$month.'-'.$currentDay1] != '')
                 {
                   $cl_nm="activate";
                 }
                 else
                 {
                   $cl_nm="";
                 }
                 
       
                 if($todays_date==$currentDay && $current_month==$month)
                 {
                   $calendar .= '<td class="event_day '.$cl_nm.'" align="right" style="color:#cc0000;" rel="$date"><div id="dt__'.$currentDay.'" onclick="open_pop(this.id);">'.$currentDay.'</div>
                   
                                          <div class="overlay" id="ov__'.$currentDay.'"></div>
                                           <div class="outer" id="ou__'.$currentDay.'">
                                               <div class="cross" id="cr__'.$currentDay.'" onclick="close_pop(this.id);">X</div>
                                               <div class="inner">
                                               <div class="all_data">
                                                   <div class="tot_quote">
                                                       <span class="lab_txt">
                                                           Quote Sent : 
                                                       </span>
                                                       <span class="lab_val">
                                                           '.$tot_quote_placed.'
                                                       </span>
                                                   </div>
                                                   <div class="tot_invoice">
                                                       <span class="lab_txt">
                                                           Invoice Sent : 
                                                       </span>
                                                       <span class="lab_val">
                                                           '.$tot_invoice_placed.'
                                                       </span>
                                                   </div>
                                                   <div class="tot_ord">
                                                    <span class="lab_txt">
                                                        Total Money Sent : 
                                                    </span>
                                                    <span class="lab_val">
                                                        '.$total_paid.'
                                                    </span>
                                                   </div>
                                                   <div class="pen_pay">
                                                    <span class="lab_txt">
                                                        Pending Payments : 
                                                    </span>
                                                    <span class="lab_val">
                                                        '.$pending_payments.'
                                                    </span>
                                                   </div>
                                                   <div class="pen_pay">
                                                    <span class="lab_txt">
                                                        Items Shipped : 
                                                    </span>
                                                    <span class="lab_val">
                                                        '.number_format($item_shipped,0).'
                                                    </span>
                                                   </div>
                                                  </div>
                                                  
                                                  ';
                                               
                                              /* $calendar .='<div class="tot_ord_det">';
                                               $ctctct=0;
                                              foreach($show_id as $vvl)
                                              {
                                                $calendar .='<div class="ord_lnk"><a target="_blank" href="'.Mage::getBaseUrl()."/admin/sales_order/view/order_id/".$ord_id[$ctctct].'">Order : '.$vvl.'</a></div>';
                                                $ctctct++;
                                              }
                                               $calendar .='</div>';*/
                                              
                                              if($holyday[$year.'-'.$month.'-'.$currentDay1] != '')
                                                $calendar .='<div class="holi_lnk">Holiday : 
                                                  
                                                  
                                                         '.$holyday[$year.'-'.$month.'-'.$currentDay1].'
                                                         
                                                  </div>';
                                              
                                              $calendar .='</div>
                                              
                                               
                                           </div>
                   
                   
                   
                   
                   
                   
                   </td>';  
                 }
                 else
                 {
                   $calendar .= '<td class="event_day '.$cl_nm.'" align="right"  rel="$date"><div id="dt__'.$currentDay.'" onclick="open_pop(this.id);">'.$currentDay.'</div>
                   
                                           <div class="overlay" id="ov__'.$currentDay.'"></div>
                                           <div class="outer" id="ou__'.$currentDay.'">
                                               <div class="cross" id="cr__'.$currentDay.'" onclick="close_pop(this.id);">X</div>
                                               <div class="inner">
                                                <div class="all_data">
                                                   <div class="tot_quote">
                                                       <span class="lab_txt">
                                                           Quote Sent : 
                                                       </span>
                                                       <span class="lab_val">
                                                           '.$tot_quote_placed.'
                                                       </span>
                                                   </div>
                                                   <div class="tot_invoice">
                                                       <span class="lab_txt">
                                                           Invoice Sent : 
                                                       </span>
                                                       <span class="lab_val">
                                                           '.$tot_invoice_placed.'
                                                       </span>
                                                   </div>
                                                   <div class="tot_ord">
                                                    <span class="lab_txt">
                                                        Total Money Sent : 
                                                    </span>
                                                    <span class="lab_val">
                                                        '.$total_paid.'
                                                    </span>
                                                   </div>
                                                   <div class="pen_pay">
                                                    <span class="lab_txt">
                                                        Pending Payments : 
                                                    </span>
                                                    <span class="lab_val">
                                                        '.$pending_payments.'
                                                    </span>
                                                   </div>
                                                   <div class="pen_pay">
                                                    <span class="lab_txt">
                                                        Items Shipped : 
                                                    </span>
                                                    <span class="lab_val">
                                                        '.$item_shipped.'
                                                    </span>
                                                   </div>
                                                   </div>
                                                  ';
                                               
                                              /* $calendar .='<div class="tot_ord_det">';
                                               $ctctct=0;
                                              foreach($show_id as $vvl)
                                              {
                                                $calendar .='<div class="ord_lnk"><a target="_blank" href="'.Mage::getBaseUrl()."/admin/sales_order/view/order_id/".$ord_id[$ctctct].'">Order : '.$vvl.'</a></div>';
                                                $ctctct++;
                                              }
                                               $calendar .='</div>';*/
                                              
                                               if($holyday[$year.'-'.$month.'-'.$currentDay1] != '')
                                                $calendar .='<div class="holi_lnk">Holiday : 
                                                  
                                                  
                                                         '.$holyday[$year.'-'.$month.'-'.$currentDay1].'
                                                         
                                                  </div>';
                                              
                                              
                                              $calendar .='</div>
                                              
                                               
                                           </div>
                   
                   
                   
                   
                   
                   </td>'; 
                 }
                 
       
                 // Increment counters
        
                 $currentDay++;
                 $dayOfWeek++;
       
            }
            
            
       
            // Complete the row of the last week in month, if necessary
       
           /* if ($dayOfWeek != 7) { 
            
                 $remainingDays = 7 - $dayOfWeek;
                 $calendar .= "<td colspan='$remainingDays'>&nbsp;</td>"; 
       
            }*/
            
            $calendar .= "</tr>";
       
            $calendar .= "</table>";
       
            echo $calendar;
       
       }
}