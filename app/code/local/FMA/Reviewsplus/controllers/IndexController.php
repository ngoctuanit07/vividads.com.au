<?php

class FMA_Reviewsplus_IndexController extends Mage_Core_Controller_Front_Action{


    public function preDispatch() 
    {
        parent::preDispatch();
        if(!Mage::getStoreConfig('reviewsplus_sec/plus_config/status'))
        {
          $message = $this->__('Sorry Module Temporarily Disabled');           
          Mage::getSingleton('core/session')->addError($message);
          $this->norouteAction();
        }
    }
    public function saveAction()
    {
        $customer_id=$this->getRequest()->getParam('customer_id');
        $review_id= $this->getRequest()->getParam('review_id');
        $option=$this->getRequest()->getParam('option');
          if($option==1)
          {
            try
            {
               $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                $table_name = Mage::getSingleton('core/resource')->getTableName('reviewsplus');
                $connection->beginTransaction();                
                $fields = array();
                $fields['customer_id'] = $customer_id;
                $fields['review_id'] = $review_id; 
                $fields['votes'] = $option;
                $connection->insert($table_name, $fields);
                $lastInsertId = $connection->lastInsertId();
                $connection->commit();
               
               Mage::dispatchEvent('reviewsplus_controller_review_vote_after', array('last_insert_id'=>&$lastInsertId)); 
                    echo "Thanks for rating it up" ;
                  
                       
            } catch (Exception $e) {
                echo "Can't save your vote";
                Mage::logException($e);
             
            }
          }
        elseif ($option==0) 
            {
            try
            {
                 $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                 $table_name = Mage::getSingleton('core/resource')->getTableName('reviewsplus');
                 $connection->beginTransaction();                
                 $fields = array();
                 $fields['customer_id'] = $customer_id;
                 $fields['review_id'] = $review_id; 
                 $fields['votes'] = $option;
                 $connection->insert($table_name, $fields);
                 $lastInsertId = $connection->lastInsertId('vote_id');
                 $connection->commit();
                 Mage::dispatchEvent('reviewsplus_controller_review_vote_after', array('last_insert_id'=>&$lastInsertId));
                 echo "Thanks for your vote";
            }
              catch (Exception $e) {
                  echo "Can't Save your vote";
                  Mage::logException($e);
            }
            
            }
            
    }
    
  }
  


