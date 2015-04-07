<?php   
$i=time();
// Now login on MAGENTO
include('app/Mage.php');
Mage::app();

/******************************** Start System alert send ****************************************/
            
    $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    $temptableAlert=Mage::getSingleton('core/resource')->getTableName('system_alert');
    $temptableAlertTask=Mage::getSingleton('core/resource')->getTableName('system_alert_task');
    
    $select = $connectionRead->select()
    ->from($temptableAlert, array('*'));
    
    /* Sender Name */
    $supportName = Mage::getStoreConfig('trans_email/ident_support/name'); 
    /* Sender Email */
    $supportEmail = Mage::getStoreConfig('trans_email/ident_support/email');
    
    $result = $connectionRead->fetchAll($select);
    
       
        $salesModel=Mage::getModel("sales/order");
        $salesCollection = $salesModel->getCollection();
        foreach($salesCollection as $order)
        {
            $orderId= $order->getId();
            $items_obj = Mage::getModel("sales/order")->loadByIncrementId($order->getIncrementId()); // get all items
            $items = $items_obj->getAllItems();
            
            foreach($items as $itemId => $item)
            {
                $itemId = $item->getId();
                $productId = $item->getProductId();
                $_Product = Mage::getModel('catalog/product')->load($productId);
                
                //planning date
                $temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                
                $sqlPlannerSystem = $connectionRead->select()
                ->from($temptablePlanning, array('*'))
                ->where('quote_id=?',$orderId)
                ->where('item_id=?',$itemId)
                ->where('planning_type=?','order');
                
                $planninglist = $connectionRead->fetchRow($sqlPlannerSystem);
                
                // Assign design id
                $temptableService=Mage::getSingleton('core/resource')->getTableName('design_service');
                
                $sqlService = $connectionRead->select()
                ->from($temptableService, array('*'))
                ->where('order_id=?',$orderId)
                ->where('item_id=?',$itemId)
                ->where('type=?','order');
                
                $servicelist = $connectionRead->fetchRow($sqlService);
                
                $temptableDesign=Mage::getSingleton('core/resource')->getTableName('task_designer');
                $temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
                
                $sqlDesign = $connectionRead->select()
                ->from($temptableDesign, array('*'))
                ->join( array('commenttab'=>$temptableComment), 'commenttab.parent_id = '.$temptableDesign.'.entity_id', array('commenttab.*'))
                ->where($temptableDesign.'.order_quote_id=?',$orderId)
                ->where('commenttab.user_type=?','admin')
                ->where($temptableDesign.'.item_id=?',$itemId)
                ->where($temptableDesign.'.proof_type=?','order');
                 
                
                $chkDesign = $connectionRead->fetchAll($sqlDesign);
                
                $today = date('Y-m-d');
                
                if($today > $planninglist['artwork_date'] and count($chkDesign) == 0)
                {
                    foreach($result as $list)
                    {
                        if($list['task_id'] == 1)
                        {
                           $temptableAdminUser=Mage::getSingleton('core/resource')->getTableName('admin_role');
                                   
                           $select1 = $connectionRead->select()
                           ->from($temptableAdminUser, array('*'))
                           ->where('parent_id=?',$list['user_id']);
                           
                           $Userlist = $connectionRead->fetchAll($select1);
                           
                           foreach($Userlist as $User)
                           {
                                
                                
                               
                                if($User['role_name'] == 'Designer')
                                {
                                    if($servicelist['assign_to'] != '' and $User['user_id'] == $servicelist['assign_to'])
                                    {
                                        echo $orderId.' - '.$itemId.' - '.$servicelist['assign_to'].'<br/>';
                                        
                                        $userdata = Mage::getModel('admin/user')->load($servicelist['assign_to']);
                                        $mail = Mage::getModel('core/email');
                                        $mail->setToName($userdata->getName());
                                        $mail->setToEmail($userdata->getEmail());
                                        $mail->setBody('Please Upload the artwork  for the item '.$_Product->getName().' in order '.$order->getIncrementId().' . '.$userdata->getName().' was not upload design till now. The date has been over.');
                                        $mail->setSubject('Artwork upload delay');
                                        $mail->setFromEmail($supportEmail);
                                        $mail->setFromName($supportName);
                                        $mail->setType('html');// YOu can use Html or text as Mail format
                                        $mail->send();
                                        
                                        $connectionWrite->beginTransaction();
                                        $data = array();
                                        $data['subject']= 'Artwork upload delay';
                                        $data['description']='Please Upload the artwork  for the item '.$_Product->getName().' in order '.$order->getIncrementId().' . '.$userdata->getName().' was not upload design till now.The date has been over.';
                                        $data['user_id']= $servicelist['assign_to'];
                                        $data['postdate']= date('Y-m-d');
                                        $connectionWrite->insert($temptableAlertTask, $data);
                                        $connectionWrite->commit();
                                    }
                                }
                                else
                                {
                                    if($User['user_id'] != 0)
                                    echo $orderId.' - '.$itemId.' - '.$User['user_id'].'<br/>';
                                
                                    $userdata = Mage::getModel('admin/user')->load($User['user_id']);
                                    $mail = Mage::getModel('core/email');
                                    $mail->setToName($userdata->getName());
                                    $mail->setToEmail($userdata->getEmail());
                                    $mail->setBody('Please Upload the artwork  for the item '.$_Product->getName().' in order '.$order->getIncrementId().' . '.$userdata->getName().' was not upload design till now.The date has been over.');
                                    $mail->setSubject('Artwork upload delay');
                                    $mail->setFromEmail($supportEmail);
                                    $mail->setFromName($supportName);
                                    $mail->setType('html');// YOu can use Html or text as Mail format
                                    $mail->send();
                                    
                                    $connectionWrite->beginTransaction();
                                    $data = array();
                                    $data['subject']= 'Artwork upload delay';
                                    $data['description']='Please Upload the artwork  for the item '.$_Product->getName().' in order '.$order->getIncrementId().' . '.$userdata->getName().' was not upload design till now.The date has been over.';
                                    $data['user_id']= $User['user_id'];
                                    $data['postdate']= date('Y-m-d');
                                    $connectionWrite->insert($temptableAlertTask, $data);
                                    $connectionWrite->commit();
                                }
                           }
                        }
                    }
                }
            }
        }
            
            
            

        
        //$mail = Mage::getModel('core/email');
        //            $mail->setToName($customername);
        //            $mail->setToEmail($customeremail);
        //            $mail->setBody('Please enter the fax number.');
        //            $mail->setSubject('Shipping fax was not enter');
        //            $mail->setFromEmail($supportEmail);
        //            $mail->setFromName($supportName);
        //            $mail->setType('html');// YOu can use Html or text as Mail format
        //            $mail->send();

                
            /******************************** End System alert send ****************************************/

//echo 'time consumed='.(time()-$i);
?>
