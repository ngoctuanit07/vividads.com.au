<?php
/**
* 
*/
class FMA_Reviewsplus_Block_Product_View_List extends Mage_Review_Block_Product_View_List
{
	

 		 public function reviewscollection()
 		 {
    		if(Mage::getSingleton('customer/session')->isLoggedIn())
		    {

		        $customer_id= Mage::getSingleTon('customer/session')->getId();
		        $customer_reviews=Mage::getResourceModel('reviewsplus/reviewsplus')
		        ->customerreviews($customer_id);
            	return $customer_reviews;
		    }  
		}
		
		public function ratingSummary()
 		{
 			$productid= $this->getProduct()->getId(); 
			$storeId = Mage::app()->getStore()->getId();
           	list($summaryData['rating_summary'], $array,$string)=Mage::getResourceModel('reviewsplus/reviewsplus')
		        ->ratings($productid, $storeId);
            return array($summaryData['rating_summary'], $array,$string);
		}
 	}

?>
				 


				  
 						

 




	            

            	
	            

