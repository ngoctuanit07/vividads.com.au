<?php

class MDN_Quotation_Model_History extends Mage_Core_Model_Abstract {

    /** 
     * Constructor
     */
    public function _construct() {
        parent::_construct();
        $this->_init('Quotation/History');
    }
		
	public function isCustomerNotificationNotApplicable($_item = null){ 
			
			//Zend_debug::dump($_item);
			//if($_item[''])
			return true; 
		}	
	public function isVisibleOnfront($_item = null){
			
			// Zend_debug::dump($_item);
			if($_item['is_visible_on_front']==1){
				return true;
				}
			return false;  
		}
	public function getIsCustomerNotified($_item = null){
		
		  if($_item['is_customer_notified']==1){
				return true;
				}
			//if($_item[''])
			return false; 
		
		}			
}
