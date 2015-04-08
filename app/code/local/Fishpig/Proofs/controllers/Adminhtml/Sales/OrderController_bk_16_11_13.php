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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
/**
 * Adminhtml sales orders controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Fishpig_Proofs_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
   
    
        /************************************ Start by dev ***********************************************/
    public function paymentAction()
    {
        extract($_REQUEST);
        
        
        $order = Mage::getModel('sales/order')->load($orderid);
        
        
                
        
        $main_price = $order->getGrandTotal();
        $due_price = $order->getTotalDue();
        if($main_price >= $amount and $due_price >= $amount)
        {
            if ($paymentData = $_REQUEST['payment']) {
            
            $order->setGrandTotal($amount);
            
            $order->setPaymentData($paymentData);
            $order->getPayment()->addData($paymentData);
            $tableItem = Mage::getSingleton('core/resource')->getTableName('quotation_items');
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            }
            
            $order->setGrandTotal($main_price);
            $order->save();
            
             
                    
                $transactionTable=Mage::getSingleton('core/resource')->getTableName('partial_payment');
                $orderTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
                
                
                $order_id = $order->getId();
                $payemnt_type = $payment['method'];
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($transactionTable))
                {
                    $sqlPaymentSystem="INSERT INTO ".$transactionTable." SET orderid = '$order_id', amount = '$amount', payment_type = '$payemnt_type', received_date = '$date', postdate = NOW()";
                    try {
                            $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
                    } catch (Exception $e){
                    //echo $e->getMessage();
                    }
                }
                
                $sqlPaymentSystem="SELECT * FROM ".$orderTable." WHERE  entity_id = '".$order_id."' ";
                try {
                        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
                        $resultsSystem = $chkSystem->fetch();
                } catch (Exception $e){
                //echo $e->getMessage();
                }
                
                if($resultsSystem['total_paid'] == 0)
                $paid = $amount;
                else
                $paid = $resultsSystem['total_paid']+$amount;
                
                if($resultsSystem['total_due'] == '')
                $due = $resultsSystem['base_grand_total']-$amount;
                else
                $due = $resultsSystem['total_due']-$amount;
                
                $sqlPaymentSystem="UPDATE ".$orderTable." SET base_total_due = '$due', total_due = '$due', base_total_paid = '$paid', total_paid = '$paid' WHERE  entity_id = '".$order_id."' ";
                try {
                        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
                        $resultsSystem = $chkSystem->fetch();
                } catch (Exception $e){
                //echo $e->getMessage();
                }
                $order = Mage::getModel('sales/order')->load($orderid);
                
                $due_price = $order->getTotalDue();
                if($due_price == 0)
                {
                    $invoice = $order->getInvoiceCollection()->getLastItem();
                    $invoice->setState(2);
                    $invoice->save();
                    
                }
                $order->sendNewOrderEmail();
                
                $this->_getSession()->clear();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been saved.'));
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('The partial payment amount are greater than order due amount.'));
        }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        

            
    }
    
    //Proof create update for order
    public function proofsAction()
    {
        extract($_REQUEST);
        
        $order = Mage::getModel('sales/order')->load($orderid);
        $storId =  $order->getStoreId();
        $customer_id =  $order->getCustomerId();
      
        
            foreach($_FILES['item_file']['name'] as $key=>$value)
            {
                
                if(isset($_FILES['item_file']['name'][$key]) and (file_exists($_FILES['item_file']['tmp_name'][$key]))) {
                   
                    $file_name=$_FILES['item_file']['name'][$key];
                    
                    $expFilename=explode(".",$file_name);
                    $fileNameVal=time().".".end($expFilename);
                    
                    
                    $mediaPath=Mage::getBaseDir('media') . DS ;
                    //$path2 = $mediaPath.'upload_image/'.$fileNameVal;
                    $path2 = $mediaPath.'proofs/'.$fileNameVal;
                    chmod($path2,0777);
                    $filepath = $fileNameVal;
                    
                    //file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
                    if(move_uploaded_file($_FILES['item_file']['tmp_name'][$key],$path2))
                    {
                        //$tableName = Mage::getSingleton('core/resource')->getTableName('upload_image');
                        $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                        
                        $sqlProofsSystem="INSERT INTO ".$tableName."  SET store_id = '".$storId."', order_id = '".$order->getId()."', customer_id = '".$customer_id."' , item_id = '".$item[$key]."', file = '".$filepath."', status = 'Awaiting Proof Approval',   postdate = NOW(), proof_type = 'order'";
                        
                        //$sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$file_name."',status = 2, created_time = NOW()";
                       // $sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$fileNameVal."',status = 2, created_time = NOW()";
                        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                      
                    }
                }
            }
            
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }
    
    //Proof create update for quote
    public function proofsquoteAction()
    {
        extract($_REQUEST);
        
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteid);
        $storId =  $quote->getStoreId();
        $customer_id =  $quote->getCustomerId();
      
        
            foreach($_FILES['item_file']['name'] as $key=>$value)
            {
                
                if(isset($_FILES['item_file']['name'][$key]) and (file_exists($_FILES['item_file']['tmp_name'][$key]))) {
                   
                    $file_name=$_FILES['item_file']['name'][$key];
                    
                    $expFilename=explode(".",$file_name);
                    $fileNameVal=time().".".end($expFilename);
                    
                    
                    $mediaPath=Mage::getBaseDir('media') . DS ;
                    //$path2 = $mediaPath.'upload_image/'.$fileNameVal;
                    $path2 = $mediaPath.'proofs/'.$fileNameVal;
                    chmod($path2,0777);
                    $filepath = $fileNameVal;
                    
                    //file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
                    if(move_uploaded_file($_FILES['item_file']['tmp_name'][$key],$path2))
                    {
                        //$tableName = Mage::getSingleton('core/resource')->getTableName('upload_image');
                        $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                        
                        $sqlProofsSystem="INSERT INTO ".$tableName."  SET store_id = '".$storId."', order_id = '".$quoteid."', customer_id = '".$customer_id."' , item_id = '".$item[$key]."', file = '".$filepath."', status = 'Awaiting Proof Approval',   postdate = NOW(), proof_type = 'quote'";
                        
                        //$sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$file_name."',status = 2, created_time = NOW()";
                       // $sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$fileNameVal."',status = 2, created_time = NOW()";
                        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                      
                    }
                }
            }
            
        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteid));
    }
    
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
    
    //Proof quantity update for quote
    public function quantityupdateAction()
    {
        extract($_REQUEST);
        //print_r($_REQUEST);
        $flag = 0;
        foreach($quantity as $key=>$qty)
        {
            $tableItemName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
            $sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE item_id = '".$item[$key]."'";
            $chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlItemSystem);
            $fetchItem = $chkItem->fetch();
            
            if($fetchItem['qty_ordered'] >= $qty)
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                $sqlProofsSystem="UPDATE ".$tableName."  SET quantity = '".$qty."' WHERE entity_id = '".$key."'";
                $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
            }
            else
            {
                $flag =1;
            }
        }
        if($flag == 1)
        Mage::getSingleton('adminhtml/session')->addError($this->__('Proofs quantity are not more than item quantity.'));
            
        $this->_redirect('*/sales_order/view', array('order_id' => $orderid));
    }
    
    //Proof quantity update for order
     public function quantityupdatequoteAction()
    {
        extract($_REQUEST);
        //print_r($_REQUEST);
        $flag = 0;
        foreach($quantity as $key=>$qty)
        {
            $tableItemName = Mage::getSingleton('core/resource')->getTableName('quotation_items');
            $sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE quotation_item_id = '".$item[$key]."'";
            $chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlItemSystem);
            $fetchItem = $chkItem->fetch();
            
            if($fetchItem['qty'] >= $qty)
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                $sqlProofsSystem="UPDATE ".$tableName."  SET quantity = '".$qty."' WHERE entity_id = '".$key."'";
                $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
            }
            else
            {
                $flag =1;
            }
        }
        if($flag == 1)
        Mage::getSingleton('adminhtml/session')->addError($this->__('Proofs quantity are not more than item quantity.'));
            
       $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteid));
    }
    
     public function UpdateAction()
	{
		//create planning
                
                extract($_REQUEST);
                
		$orderId = $_REQUEST['order_id'];
		
		try 
		{
			$order = Mage::getModel('sales/order')->load($orderId);
			$created_date = $order->getCreatedAt();
			foreach($order_date as $key=>$value)
                        {
			
                            $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                            $sqlShipping="UPDATE  ".$temptableShipping." SET order_placed_date = '$value', artwork_date = '$artwork[$key]', proof_date = '$proof[$key]', start_date ='$start[$key]', shipping_date = '$shipping_date[$key]', delivery_date = '$delivery_date[$key]' WHERE entity_id = '".$key."'";
                            $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                        }
			

			
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Planning created'));
		}
		catch (Exception $ex)
		{
			Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
		}
		
		//redirect
        $url = Mage::helper("adminhtml")->getUrl("admin/sales_order/view/order_id/".$orderId);
        $url = str_replace('p//s','p/admin/s',$url);
        
        Mage::log($url);
        Mage::app()->getResponse()->setRedirect($url);
        
    	//$this->_redirect($url, array('order_id' => $orderId));
    	
	}
    /************************************ End by dev ***********************************************/
}
