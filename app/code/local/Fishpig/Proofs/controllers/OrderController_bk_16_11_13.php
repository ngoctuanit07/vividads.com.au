<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales orders controller
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once Mage::getModuleDir('controllers', 'Mage_Sales').DS.'OrderController.php';

class Fishpig_Proofs_OrderController extends Mage_Sales_OrderController
{

    public function downloadAction()
    {
        $file_path=Mage::getBaseDir('media').'/proofs/'.$this->getRequest()->getParam('file');
    
    
    //Call the download function with file path,file name and file type
    //download_file($file_path, ''.$_REQUEST['file'].'', 'text/plain');
    $this->download_file($file_path, ''.$this->getRequest()->getParam('file').'', 'text/plain');
    
    
    }
    
    public function download_file($file, $name, $mime_type='')
    {
       
        $size = filesize($file);
        $name = rawurldecode($name);
       
        $known_mime_types=array(
               "pdf" => "application/pdf",
               "txt" => "text/plain",
               "html" => "text/html",
               "htm" => "text/html",
               "exe" => "application/octet-stream",
               "zip" => "application/zip",
               "doc" => "application/msword",
               "xls" => "application/vnd.ms-excel",
               "ppt" => "application/vnd.ms-powerpoint",
               "gif" => "image/gif",
               "png" => "image/png",
               "jpeg"=> "image/jpg",
               "jpg" =>  "image/jpg",
               "php" => "text/plain"
        );
       
        if($mime_type==''){
                $file_extension = strtolower(substr(strrchr($file,"."),1));
                if(array_key_exists($file_extension, $known_mime_types)){
                       $mime_type=$known_mime_types[$file_extension];
                } else {
                       $mime_type="application/force-download";
                };
        };
       
        @ob_end_clean(); 
       
        // required for IE, otherwise Content-Disposition may be ignored
        if(ini_get('zlib.output_compression'))
         ini_set('zlib.output_compression', 'Off');
       
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; file="'.$name.'"');
        header("Content-Transfer-Encoding: binary");
        header('Accept-Ranges: bytes');
        header("Cache-control: private");
        header('Pragma: private');
        readfile($file); 
    }
    
    public function proofcommentAction()
    {
        extract($_REQUEST);
 
        $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
        
        $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');              
        $sqlProofsSystem="SELECT * FROM ".$tableName." WHERE entity_id = '".$proof_id."'";
        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlProofsSystem);
        
        if($proof_type == 'quote')
        {
        $tableItemName = Mage::getSingleton('core/resource')->getTableName('quotation_items');
        $sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE quotation_item_id = '".$chkSystem[0]['item_id']."'";
        $chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlItemSystem);
        }
        else if($proof_type == 'order'){
             $tableItemName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
            $sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE item_id = '".$chkSystem[0]['item_id']."'";
            $chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlItemSystem);
        }
        $fetchItem = $chkItem->fetch();
        
        if($qty != '')
        {
            if(($fetchItem['qty'] >= $qty or $fetchItem['qty_ordered'] >= $qty) and $qty != 0)
            {
                $sqlProofsSystem="UPDATE ".$tableName."  SET  quantity = '".$qty."' WHERE entity_id = '".$proof_id."'";
                $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                
                $sqlProofsSystem="UPDATE ".$tableName."  SET  status = '".$status."', comment = '".$comment."', approve_date=NOW() WHERE entity_id = '".$proof_id."' AND quantity != 0";
                $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
            }
            else
            {
                echo 'Quantity will not be greater than order item quantity or not 0.';
            }
        }
        
       
        
        $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');              
        $sqlProofsSystem="SELECT * FROM ".$tableName." WHERE entity_id = '".$proof_id."'";
        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlProofsSystem);
        
        if($proof_type == 'quote')
        {
            $tableNamePlanner = Mage::getSingleton('core/resource')->getTableName('quote_planning');
            if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($tableNamePlanner))
            {
            $sqlPlannerSystem="SELECT * FROM ".$tableNamePlanner."   WHERE quote_id = '".$chkSystem[0]['order_id']."' AND item_id = '".$chkSystem[0]['item_id']."' AND planning_type = 'quote' ";
            $chkSystemPlanner = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlPlannerSystem);
            }
           
             $_product = Mage::getModel('catalog/product')->load($chkSystemPlanner[0]['product_id']);
             $today = date('Y-m-d');
             $approval_date = $chkSystemPlanner[0]['proof_date'];
             
             if($approval_date<=$today and $status == 'Disapproved')
             {
                 $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableTimeline))
                {
                $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$chkSystemPlanner[0]['product_id']."' ";
                $chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTimeline);
                }
                        
                //$artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$today,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$today,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$today,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$today,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                
             
                $temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptablePlanning))
                {
                $sqlPlanning="UPDATE  ".$temptablePlanning." SET  artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' WHERE quote_id = '".$chkSystem[0]['order_id']."' AND item_id = '".$chkSystem[0]['item_id']."' AND planning_type = 'quote'";
                $chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlPlanning);
                }
            }
        }
        else if($proof_type == 'order')
        {
            $tableNamePlanner = Mage::getSingleton('core/resource')->getTableName('quote_planning');
            if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($tableNamePlanner))
            {
            $sqlPlannerSystem="SELECT * FROM ".$tableNamePlanner."   WHERE quote_id = '".$chkSystem[0]['order_id']."' AND item_id = '".$chkSystem[0]['item_id']."' AND planning_type = 'order' ";
            $chkSystemPlanner = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlPlannerSystem);
            }
            
             $_product = Mage::getModel('catalog/product')->load($chkSystemPlanner[0]['product_id']);
             $today = date('Y-m-d');
             $approval_date = $chkSystemPlanner[0]['proof_date'];
             
             if($approval_date>=$today and $status == 'Disapproved')
             {
                
                 $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableTimeline))
                {
                $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$chkSystemPlanner[0]['product_id']."' ";
                $chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTimeline);
                }
                        
                //$artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$today,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$today,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$today,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$today,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                
             
                $temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptablePlanning))
                {
                $sqlPlanning="UPDATE  ".$temptablePlanning." SET   proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' WHERE quote_id = '".$chkSystem[0]['order_id']."' AND item_id = '".$chkSystem[0]['item_id']."' AND planning_type = 'order'";
                $chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlPlanning);
                }
            }
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
                        $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                        $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
                        
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
                    $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                    $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
                    
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
