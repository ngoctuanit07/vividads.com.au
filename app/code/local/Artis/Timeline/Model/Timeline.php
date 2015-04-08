<?php

class Artis_Timeline_Model_Timeline extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('timeline/timeline');
    }
    
    public function UpdateTimeline($planing_type, $order_id, $item, $order_type)
    {
        $ProductId = $item->getProductId();
        
         $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
        // $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
         $sqlTimeline = $connectionRead->select()
                         ->from($temptableTimeline, array('*'))
                         ->where('product_id=?', $ProductId);
         $chkTimeline = $connectionRead->fetchRow($sqlTimeline);
         
         
        
        if($planing_type == 'order_create')
        $order_placed_date =  NOW();
        
        
      
        
        if($planing_type == 'order_create' || $planing_type == 'artwork_upload')
        {
            if($order_placed_date != '')
            $currnt = $order_placed_date;
            else
            {
                $currnt = date('Y-m-d', strtotime('-'.$chkTimeline['artwork_day'].' day', strtotime(NOW())));
                $virtual_order_date = date('Y-m-d', strtotime('-'.$chkTimeline['artwork_day'].' day', strtotime(NOW())));
                
            }
            
            
            $artwork_date = $this->gettimelinedate($chkTimeline['artwork_day'],$currnt,$chkTimeline['sunday_artwork'],$chkTimeline['holiday_artwork']);
            //$artwork_date = $this->gettimelinedate($chkTimeline['artwork_day'],$currnt,$chkTimeline['sunday_artwork'],$chkTimeline['holiday_artwork']);
        }
        
        
        if($planing_type == 'order_create' || $planing_type == 'artwork_upload' || $planing_type == 'proof_approve')
        {
            if($currnt == '')
            {
                if($order_placed_date != '')
                $currnt = $order_placed_date;
                else
                {
                    $currnt = date('Y-m-d', strtotime('-'.$chkTimeline['proof_day'].' day', strtotime(NOW())));
                    $virtual_order_date = date('Y-m-d', strtotime('-'.$chkTimeline['proof_day'].' day', strtotime(NOW())));
                    
                }
            }
            else
            $currnt = $virtual_order_date;
            
            $proof_date = $this->gettimelinedate($chkTimeline['proof_day'],$currnt,$chkTimeline['sunday_proof'],$chkTimeline['holiday_proof']);
            //$proof_date = $this->gettimelinedate($chkTimeline['proof_day'],$virtual_order_date,$chkTimeline['sunday_proof'],$chkTimeline['holiday_proof']);
        }
        
        
        if($planing_type == 'order_create' || $planing_type == 'artwork_upload' || $planing_type == 'proof_approve' || $planing_type == 'production_start')
        {
            if($currnt == '')
            {
                if($order_placed_date != '')
                $currnt = $order_placed_date;
                else
                {
                    $currnt = date('Y-m-d', strtotime('-'.$chkTimeline['production_day'].' day', strtotime(NOW())));
                    $virtual_order_date = date('Y-m-d', strtotime('-'.$chkTimeline['production_day'].' day', strtotime(NOW())));
                    
                }
             }
            else
            $currnt = $virtual_order_date;
            
            $production_start_date = $this->gettimelinedate($chkTimeline['production_day'],$currnt,$chkTimeline['sunday_production'],$chkTimeline['holiday_production']);
            //$production_start_date = $this->gettimelinedate($chkTimeline['production_day'],$virtual_order_date,$chkTimeline['sunday_production'],$chkTimeline['holiday_production']);
        }
        
        
        if($planing_type == 'order_create' || $planing_type == 'artwork_upload' || $planing_type == 'proof_approve' || $planing_type == 'production_start' || $planing_type == 'shipment')
        {
            if($currnt == '')
            {
                if($order_placed_date != '')
                $currnt = $order_placed_date;
                else
                {
                    $currnt = date('Y-m-d', strtotime('-'.$chkTimeline['shipping_day'].' day', strtotime(NOW())));
                    $virtual_order_date = date('Y-m-d', strtotime('-'.$chkTimeline['shipping_day'].' day', strtotime(NOW())));
                    
                }
            }
            else
            $currnt = $virtual_order_date;
            
            $shipping_date = $this->gettimelinedate($chkTimeline['shipping_day'],$currnt,$chkTimeline['sunday_shipping'],$chkTimeline['holiday_shipping']);
            //$shipping_date = $this->gettimelinedate($chkTimeline['shipping_day'],$virtual_order_date,$chkTimeline['sunday_shipping'],$chkTimeline['holiday_shipping']);
        }
        
        
        if($planing_type == 'order_create' || $planing_type == 'artwork_upload' || $planing_type == 'proof_approve' || $planing_type == 'production_start' || $planing_type == 'shipment' || $planing_type == 'delivery')
        {
            if($currnt == '')
            {
                if($order_placed_date != '')
                $currnt = $order_placed_date;
                else
                {
                    $currnt = date('Y-m-d', strtotime('-'.$chkTimeline['delivary_day'].' day', strtotime(NOW())));
                    $virtual_order_date = date('Y-m-d', strtotime('-'.$chkTimeline['delivary_day'].' day', strtotime(NOW())));
                    
                }
            }
            else
            $currnt = $virtual_order_date;
            
            //$delivery_date = $this->gettimelinedate($chkTimeline['delivary_day'],$currnt,$chkTimeline['sunday_delivary'],$chkTimeline['holiday_delivary']);
            $delivery_date = $this->gettimelinedate($chkTimeline['delivary_day'],$virtual_order_date,$chkTimeline['sunday_delivary'],$chkTimeline['holiday_delivary']);
        }
        
        $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
        
        if($connectionWrite->isTableExists($temptableShipping))
        {
            //$sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$order->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'order', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
            //$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
            
            $connectionWrite->beginTransaction();
            $data = array();
            //$data['quote_id']= $order_id;
            //$data['item_id'] = $item->getId();
            //$data['product_id'] = $ProductId; 
            //$data['planning_type'] = $order_type;
            if($order_placed_date != '')
            $data['order_placed_date'] = $order_placed_date;
            
            if($artwork_date != '')
            $data['artwork_date'] = $artwork_date;
            
            if($proof_date != '')
            $data['proof_date'] = $proof_date;
            
            if($production_start_date != '')
            $data['start_date'] = $production_start_date;
            
            if($shipping_date != '')
            $data['shipping_date'] = $shipping_date;
            
            if($delivery_date != '')
            $data['delivery_date']= $delivery_date;
            //print_r($data);
            //$connectionWrite->insert($temptableShipping, $data);
            //$connectionWrite->commit();
            $where1 = $connectionWrite->quoteInto('quote_id = "'.$order_id.'" AND item_id ="'.$item->getId().'" AND planning_type ="'.$order_type.'"');
            $connectionWrite->update($temptableShipping, $data, $where1);
            $connectionWrite->commit();
        }
                    
    }
    
     /***************************** Add custom function ***********************************/
     public function isweekend($date){
     $date = strtotime($date);
     $date = date("l", $date);
     $date = strtolower($date);
     if($date == "sunday"){
      return 1;
     } else {
      return 0;
     }
    }
    
    public function gettimelinedate($day_delay,$created_date,$sunday,$holiday)
    {
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        if($sunday == 0 and $holiday == 0)
        {
            $artwork_date = date ( 'Y-m-j', strtotime ( '+'.$day_delay.' day' . $created_date ) );
        }
        else
        {
            if($sunday == 1)
            {
                $flag = 0;
                $artwork_date = date ( 'Y-m-j', strtotime ( '+'.$day_delay.' day' . $created_date ) );
                
                $d = $this->isweekend($artwork_date);
                if($holiday == 1)
                {
                    while($flag == 0)
                    {
                        $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                        
                        $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
                        //$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                        $sqlHoliday= $connectionRead->select()
                                        ->from($temptableHoliday,array('*'))
                                        ->where('h_date=?',$artwork_date);
                        $chkHoliday = $connectionWrite->fetchAll($sqlHoliday);
                        
                        if(count($chkHoliday) > 0)
                        {
                            $d++;
                        }
                        else
                        {
                           $flag = 1; 
                        }
                    }
                    
                }
                else
                {
                    $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                }
                
            }
            else if($holiday == 1)
            {
                $flag = 0;
                $d = 0;
                while($flag == 0)
                {
                    $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                    
                    $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
                   // $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                    $sqlHoliday= $connectionRead->select()
                                        ->from($temptableHoliday,array('*'))
                                        ->where('h_date=?',$artwork_date);
                    $chkHoliday = $connectionWrite->fetchAll($sqlHoliday);
                    
                    if(count($chkHoliday) > 0 or ($sunday == 1 and $this->isweekend($artwork_date) == 1))
                    {
                        $d++;
                    }
                    else
                    {
                       $flag = 1; 
                    }
                }
            }
            
        }
        
        return $artwork_date;
    }
    /***************************** Add custom function ***********************************/
}