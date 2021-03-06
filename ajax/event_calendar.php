<?php   
// Now login on MAGENTO
include('../app/Mage.php');
Mage::app();

Mage::getSingleton('core/session', array('name' => 'adminhtml'));

function build_calendar($month,$year,$dateArray,$country) {
    
    

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
         //print_r($holyday);
         /******************* End Holiday parse ***********************/
       
            
            
            
            // Create the table tag opener and day headers
       
            $calendar = "<table width='100%' border='0' cellspacing='0'  cellspadding='0' class='event_calendar'>";
            $calendar .= "<caption><span style='float: left; margin-left: 100px;'>".$select."</span><b>$monthName $year</b></caption>";     
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
                                   <a href='javascript:void(0);' class='right_arr' onclick='next_month($month,($next_year+1));'>></a>  
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
                 
                 //if($tot_invoice_placed>0 || $tot_quote_placed>0 || $item_shipped>0 || $pending_payments>0 || $total_paid>0  || $holyday[$year.'-'.$month.'-'.$currentDay1] != '')
                 if($holyday[$year.'-'.$month.'-'.$currentDay1] != ''  or $daysOfWeek[$dayOfWeek] == 'Sun' or $daysOfWeek[$dayOfWeek] == 'Sat')
                 {
                   $cl_nm="activate";
                 }
                 else
                 {
                   $cl_nm="";
                 }
                 
       
                 if($todays_date==$currentDay && $current_month==$month)
                 {
                     $slectedDate = $year . '-' . $current_month . '-' . $currentDay;
                     $calendar .= '<td class="event_day '.$cl_nm.'" align="right" style="background-color:#EF781E !important; color:#fff;" rel="$date"><div id="dt__'.$currentDay.'" onclick="open_pop(this.id);">'.$currentDay.'</div>
                   
                                          <div class="overlay" id="ov__'.$currentDay.'"></div>
                                           <div class="outer" id="ou__'.$currentDay.'">
                                               <div class="cross" id="cr__'.$currentDay.'" onclick="close_pop(this.id);">X</div>
                                               <div class="inner">
                                               <h6>Summary & Item Sold('.$slectedDate.')</h6>
                                                <div class="all_data">
                                                        <div class="tot_quote data-row odd" style="height:135px;">
                                                            <div class="lab_txt">Orders :</div>
                                                            <div class="lab_val" >
                                                                <ul>
                                                                    <li>Canceled(<strong>0</strong>)</li>
                                                                    <li>Closed(<strong>1</strong>)</li>
                                                                    <li>Complete(<strong>5</strong>)</li>
                                                                    <li>Suspected Fraud(<strong>0</strong>)</li>
                                                                    <li>On Hold(<strong>0</strong>)</li>
                                                                    <li>Payment Review(<strong>1</strong>)</li>
                                                                    <li>Processing(<strong>2</strong>)</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="tot_quote data-row even">
                                                            <div class="lab_txt">Refunds :</div>
                                                            <div class="lab_val">0</div>
                                                        </div>
                                                        <div class="tot_quote data-row odd">
                                                            <div class="lab_txt">Proof Disapproved :</div>
                                                            <div class="lab_val">0</div>
                                                        </div>                                                        
                                                        <div class="tot_quote data-row even">
                                                            <div class="lab_txt">Quote Sent :</div>
                                                            <div class="lab_val">' . $tot_quote_placed . '</div>
                                                        </div>
                                                        <div class="tot_invoice data-row odd">
                                                            <div class="lab_txt">Invoice Sent :</div>
                                                            <div class="lab_val">' . $tot_invoice_placed . '</div>
                                                        </div>
                                                        <div class="tot_ord data-row even" style=display:none;>
                                                            <div class="lab_txt">Total Money Sent : </div>
                                                            <div class="lab_val">' . $total_paid . '</div>
                                                        </div>
                                                        <div class="pen_pay data-row even">
                                                            <div class="lab_txt">Pending Payments : </div>
                                                            <div class="lab_val">' . $pending_payments . '</div>
                                                        </div>
                                                        <div class="pen_pay data-row odd">
                                                            <div class="lab_txt">Items Shipped : </div>
                                                            <div class="lab_val">' . number_format($item_shipped, 0) . '</div>
                                                        </div>
                                                        <div class="data-row"><hr style="" /></div>
                                                        <div class="pen_pay data-row total-sale">
                                                            <div class="lab_txt">Total Sale : </div>
                                                            <div class="lab_val">0</div>
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
                     $slectedDate = $year . '-' . $month . '-' . $currentDay;
                   $calendar .= '<td class="event_day '.$cl_nm.'" align="right"  rel="$date"><div id="dt__'.$currentDay.'" onclick="open_pop(this.id);">'.$currentDay.'</div>
                   
                                           <div class="overlay" id="ov__'.$currentDay.'"></div>
                                           <div class="outer" id="ou__'.$currentDay.'">
                                               <div class="cross" id="cr__'.$currentDay.'" onclick="close_pop(this.id);">X</div>
                                               <div class="inner">
                                               <h6>Summary & Item Sold('.$slectedDate.')</h6>
                                                <div class="all_data">
                                                        <div class="tot_quote data-row odd" style="height:135px;">
                                                            <div class="lab_txt">Orders :</div>
                                                            <div class="lab_val" >
                                                                <ul>
                                                                    <li>Canceled(<strong>0</strong>)</li>
                                                                    <li>Closed(<strong>1</strong>)</li>
                                                                    <li>Complete(<strong>5</strong>)</li>
                                                                    <li>Suspected Fraud(<strong>0</strong>)</li>
                                                                    <li>On Hold(<strong>0</strong>)</li>
                                                                    <li>Payment Review(<strong>1</strong>)</li>
                                                                    <li>Processing(<strong>2</strong>)</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="tot_quote data-row even">
                                                            <div class="lab_txt">Refunds :</div>
                                                            <div class="lab_val">0</div>
                                                        </div>
                                                        <div class="tot_quote data-row odd">
                                                            <div class="lab_txt">Proof Disapproved :</div>
                                                            <div class="lab_val">0</div>
                                                        </div>                                                        
                                                        <div class="tot_quote data-row even">
                                                            <div class="lab_txt">Quote Sent :</div>
                                                            <div class="lab_val">' . $tot_quote_placed . '</div>
                                                        </div>
                                                        <div class="tot_invoice data-row odd">
                                                            <div class="lab_txt">Invoice Sent :</div>
                                                            <div class="lab_val">' . $tot_invoice_placed . '</div>
                                                        </div>
                                                        <div class="tot_ord data-row even" style=display:none;>
                                                            <div class="lab_txt">Total Money Sent : </div>
                                                            <div class="lab_val">' . $total_paid . '</div>
                                                        </div>
                                                        <div class="pen_pay data-row even">
                                                            <div class="lab_txt">Pending Payments : </div>
                                                            <div class="lab_val">' . $pending_payments . '</div>
                                                        </div>
                                                        <div class="pen_pay data-row odd">
                                                            <div class="lab_txt">Items Shipped : </div>
                                                            <div class="lab_val">' . number_format($item_shipped, 0) . '</div>
                                                        </div>
                                                        <div class="data-row"><hr style="" /></div>
                                                        <div class="pen_pay data-row total-sale">
                                                            <div class="lab_txt">Total Sale : </div>
                                                            <div class="lab_val">0</div>
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
       
            return $calendar;
       
       }

 $dateArray=Array();

 echo build_calendar($_GET["m"],$_GET["y"],$dateArray,$_GET['country']);

?>