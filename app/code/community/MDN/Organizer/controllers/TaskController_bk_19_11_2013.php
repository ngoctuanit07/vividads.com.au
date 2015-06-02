<?php

class MDN_Organizer_TaskController extends Mage_Adminhtml_Controller_Action {

    /**
     * Affiche la liste des taches
     *
     */
    public function ListAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function DashboardAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Nouveau
     *
     */
    public function NewAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edition
     *
     */
    public function EditAction() {
        //recupere les infos
        $ot_id = Mage::app()->getRequest()->getParam('ot_id');

        //cree le block et le retourne
        $block = $this->getLayout()->createBlock('Organizer/Task_Edit', 'taskedit');
        $block->setotId($ot_id);
        $block->setGuid(Mage::app()->getRequest()->getParam('guid'));
        $block->setTemplate('Organizer/Task/Edit.phtml');

        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Met a jour (en ajax)
     *
     */
    
    /*************************** Add custom function ***************************************/
    public function IntervalDays($CheckIn,$CheckOut)
    { 
    $CheckInX = explode("-", $CheckIn); 
    $CheckOutX =  explode("-", $CheckOut); 
    $date1 =  mktime(0, 0, 0, $CheckInX[1],$CheckInX[2],$CheckInX[0]); 
    $date2 =  mktime(0, 0, 0, $CheckOutX[1],$CheckOutX[2],$CheckOutX[0]); 
    $interval =($date2 - $date1)/(3600*24); 
    
    // returns numberofdays 
    return  $interval ; 
    
    } 
    /*************************** Add custom function ***************************************/
    public function SaveAction() {
        $ok = true;
        $msg = 'Task saved';

        try {
            //save
            $Task = Mage::getModel('Organizer/Task')
                            ->load($this->getRequest()->getPost('ot_id'))
                            ->setot_author_user($this->getRequest()->getPost('ot_author_user'))
                            ->setot_caption($this->getRequest()->getPost('ot_caption'))
                            ->setot_description($this->getRequest()->getPost('ot_description'))
                            ->setot_entity_type($this->getRequest()->getPost('ot_entity_type'))
                            ->setot_entity_id($this->getRequest()->getPost('ot_entity_id'))
                            ->setot_entity_description($this->getRequest()->getPost('ot_entity_description'))
                            ->setot_finished($this->getRequest()->getPost('ot_finished'))
                            ->setot_task_type($this->getRequest()->getPost('ot_type'));

            $target = $this->getRequest()->getPost('ot_target_user');
            if ($target > 0)
                $Task->setot_target_user($target);
            else
                $Task->setot_target_user('');

            if ($this->getRequest()->getPost('ot_deadline') != '')
                $Task->setot_deadline($this->getRequest()->getPost('ot_deadline'));
            if ($this->getRequest()->getPost('ot_notify_date') != '')
                $Task->setot_notify_date($this->getRequest()->getPost('ot_notify_date'));
            //if ($this->getRequest()->getPost('ot_id') == '')
            //    $Task->setot_created_at(date('Y-m-d H:i'));
                
                /*********************** Start by dev *******************************/
                if ($this->getRequest()->getPost('ot_id') == '')
                {
                    if($this->getRequest()->getPost('ot_created_date') != '')
                    $Task->setot_created_at($this->getRequest()->getPost('ot_created_date'));
                    else
                    $Task->setot_created_at(date('Y-m-d H:i'));
                }
                
                if ($this->getRequest()->getPost('ot_time') != '')
                {

                    $Task->setot_create_time($this->getRequest()->getPost('ot_time'));
                }
                
                if ($this->getRequest()->getPost('ot_day') != '')
                {

                    $Task->setot_day($this->getRequest()->getPost('ot_day'));
                }
                
                /************************** Auto Change the deadline of all chain task ******************************/
                
                if($this->getRequest()->getPost('ot_id') != '' and $this->getRequest()->getPost('ot_entity_type') != 'product')
                {
                    $Task1 = Mage::getModel('Organizer/Task')
                            ->load($this->getRequest()->getPost('ot_id'));
                          
                    if($Task1->getot_deadline() != $this->getRequest()->getPost('ot_deadline'))
                    {
                        $day_diff = $this->IntervalDays($Task1->getot_deadline(),$this->getRequest()->getPost('ot_deadline'));
                        
                        $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                        $temptableTask = Mage::getSingleton('core/resource')->getTableName('organizer_task');
                        $sqlChain2="SELECT * FROM ".$temptableChain." AS chain LEFT JOIN ".$temptableTask." AS task ON chain.task_id = task.ot_id  WHERE chain.order_quote_id = '".$this->getRequest()->getPost('ot_entity_id')."' AND chain.task_type = 'Chain' AND chain.product_id = '".$this->getRequest()->getPost('ot_item')."' AND task.ot_entity_type = '".$this->getRequest()->getPost('ot_entity_type')."' ORDER BY chain.entity_id ASC  ";
                        $chkChain2 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain2);
                        
                        
                        $flag = 0;
                        foreach($chkChain2 as $chain)
                        {
                            
                            
                            if($flag == 1)
                            {
                                $Task2 = Mage::getModel('Organizer/Task')->load($chain['ot_id']);
                                $dead_date = date ( 'Y-m-j', strtotime ( '+'.$day_diff.' day' . $chain['ot_deadline'] ) );
                                $Task2->setot_deadline($dead_date);
                                $Task2->save();
                            }
                            
                            if($chain['ot_id'] == $this->getRequest()->getPost('ot_id'))
                            $flag = 1;
                        }
                        // exit;
                    }
                }
               
                /************************** Auto Change the deadline of all chain task ******************************/
              
                
                /*********************** End by dev *******************************/

            $Task->save();
            
            /********************* Start by dev *****************************/
            if($this->getRequest()->getPost('ot_todo') == 1 and $this->getRequest()->getPost('ot_id') == '')
            {
                $user = Mage::getSingleton('admin/session');
                $userId = $user->getUser()->getUserId();
                $list_id = Mage::getSingleton('core/session')->getListid();
                
                $temptableTodo=Mage::getSingleton('core/resource')->getTableName('todolist');
                $sqlSaleOrder="INSERT INTO ".$temptableTodo." SET ot_id = '".$Task->getot_id()."', user_id = '".$userId."', list_id = '".$list_id."'";
                $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlSaleOrder);
                   
            }
            $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
            $last_id = $Task->getId();
            
            $sqlChain="SELECT * FROM ".$temptableChain." WHERE task_id = '$last_id'  ";
                                
            $chkChain1 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlChain);
            
            if($this->getRequest()->getPost('ot_entity_type') != 'product' and count($chkChain1) == 0)
            {
                 //For chain task
                
                $sqlChain="INSERT INTO ".$temptableChain." SET task_id = '$last_id', 
                                order_quote_id = '".$this->getRequest()->getPost('ot_entity_id')."' ,
                                product_id ='".$this->getRequest()->getPost('ot_item')."', 
                                task_type = '".$this->getRequest()->getPost('ot_type')."'";
                                
                if($this->getRequest()->getPost('ot_finished') == 1)
                $sqlChain .= ', task_status = "complete"';
                                
                $chkChain = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChain);
            }
            else if($this->getRequest()->getPost('ot_entity_type') != 'product')
            {
                if($this->getRequest()->getPost('ot_finished') == 1)
                $status = ', task_status = "complete"';
                
                $sqlChain="UPDATE ".$temptableChain." SET task_type = '".$this->getRequest()->getPost('ot_type')."' ".$status." WHERE task_id = '".$Task->getot_id()."' ";
                              
                $chkChain = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChain);
            }
            
            //else if($this->getRequest()->getPost('ot_todo') == 1 and $this->getRequest()->getPost('ot_id') != '')
            //{
            //    $user = Mage::getSingleton('admin/session');
            //    $userId = $user->getUser()->getUserId();
            //    
            //    $temptableTodo=Mage::getSingleton('core/resource')->getTableName('todolist');
            //    $sqlSaleOrder="INSERT INTO ".$temptableTodo." SET ot_id = '".$Task->getot_id()."', user_id = '".$userId."'";
            //    $chkSaleOrder = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlSaleOrder);
            //       
            //}
            /********************* End by dev *****************************/

            //Test if we have to notify target
            if ($this->getRequest()->getPost('notify_target') == 1) {
                if ($target > 0) {
                    $Task->notifyTarget();
                }
            }

            $ok = true;
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $ok = false;
        }

        //Retourne
        $response = array(
            'error' => (!$ok),
            'message' => $this->__($msg)
        );
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    /**
     * Retourne le block avec la liste des taches pour une entit� donn�e
     *
     */
    public function EntityListAction() {
        //recupere les infos
        $entity_type = Mage::app()->getRequest()->getParam('entity_type');
        $entity_id = Mage::app()->getRequest()->getParam('entity_id');

        //cree le block et le retourne
        $block = $this->getLayout()->createBlock('Organizer/Task_Grid', 'tasklist');
        $block->setEntityId($entity_id);
        $block->setEntityType($entity_type);
        $block->setShowEntity(Mage::app()->getRequest()->getParam('show_entity'));
        $block->setMode(Mage::app()->getRequest()->getParam('mode'));
        $block->setShowTarget(Mage::app()->getRequest()->getParam('show_target'));
        $block->setEnableSortFilter(Mage::app()->getRequest()->getParam('enable_sort_filter'));

        //$block->setTemplate('Organizer/Task/List.phtml');

        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Supprime une tache
     *
     */
    public function DeleteAction() {
        $ok = true;
        $msg = 'Task deleted';

        try {
            //recupere lkes infos
            $otId = Mage::app()->getRequest()->getParam('ot_id');
            $Task = mage::getModel('Organizer/Task')->load($otId);
            $url = $Task->getEntityLink();

            //supprime
            $Task->delete();
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $ok = false;
        }

        //Retourne
        $response = array(
            'error' => (!$ok),
            'message' => $this->__($msg)
        );
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    /**
     * Notify target
     *
     */
    public function NotifyAction() {
        $otId = Mage::app()->getRequest()->getParam('ot_id');
        $Task = mage::getModel('Organizer/Task')->load($otId);
        $Task->notifyTarget();
    }

}
