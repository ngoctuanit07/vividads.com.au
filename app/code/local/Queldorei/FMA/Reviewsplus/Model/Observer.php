<?php
class FMA_Reviewsplus_Model_Observer
{
	public function reviewreply(Varien_Event_Observer $observer)
	{
		$object = $observer->getEvent()->getObject();
		$reviewId = $object->getReviewId();
		$review=$object->getData('review_reply');
			
		if(!empty($review))
		 {
		  	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	        $table_name = Mage::getSingleton('core/resource')->getTableName('review_detail');
	        $connection->beginTransaction();                
	        $condition = array($connection->quoteInto('review_id=?', $reviewId));
	        $field['review_reply'] = $review;
	    	$connection->update($table_name, $field, $condition);
	        $connection->commit();
		}
	}

	public function countVotes(Varien_Event_Observer $observer)
	{
		if (isset($observer['last_insert_id'])) 
		{
        	$id = 	$observer['last_insert_id'];}
        	$write= Mage::getSingleton('core/resource')
           		->getConnection('core_write');
			$readresult=$write->query(
			"SELECT review_id,  votes
			 FROM reviewsplus 
	 		 where review_id=(SELECT review_id FROM reviewsplus WHERE vote_id=$id) 
			"
			);
			while ($row = $readresult->fetch() ) 
			{
				$review_id= $row['review_id'];
				if ($row['votes']==1) {
					# code...
					$yes[]=$row['votes'];
				}elseif ($row['votes']==0) 
				{
					# code...
					$no[]=$row['votes'];
				}
			}
			$votes= count($yes)-count($no);
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $table_name = Mage::getSingleton('core/resource')->getTableName('review');
            $connection->beginTransaction();                
            $condition = array($connection->quoteInto('review_id=?', $review_id));
            $field['votes'] = $votes;
            $connection->update($table_name, $field, $condition);
            $connection->commit();
	}

	public function getfeedback(Varien_Event_Observer $observer)
	{
		if (Mage::getStoreConfig('reviewsplus_sec/feedback_config/status') && Mage::getStoreConfig('reviewsplus_sec/plus_config/status')) 
		{
			$orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
			$order = Mage::getModel('sales/order')->load($orderId);
			Mage::getModel('reviewsplus/log')
	           ->reviewsplusOrderLog($order);
	    }
	 }
    
    public function customerfeedbackCron()
    {
    	if (Mage::getStoreConfig('reviewsplus_sec/feedback_config/status')) 
		{
	    	return Mage::getModel('reviewsplus/feedback')
	    	 ->getFeedbackEmail();
    	}
    }
}
