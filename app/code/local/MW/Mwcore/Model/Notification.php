<?php

class MW_Mwcore_Model_Notification extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mwcore/notification');
    }
    
    public function _beforeSave()
    {
   		 if (!$this->getId()) {
   			 if($this->getType()=="")   			 
   			 	$this->setType("message");
   			 
	   			 if($this->getTimeApply()==Null)
	   			 {  	   			 	 
	   			 	$this->setTimeApply(now());
	   			 }
   			  }
    }
}