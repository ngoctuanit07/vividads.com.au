<?php
/**
* 
*/
class FMA_Reviewsplus_Model_Mysql4_Reviewsplus extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('reviewsplus/reviewsplus','vote_id');
	}

    public function customerreviews($customer_id)
    {
         $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('reviewsplus'))
            ->where('customer_id = (?)', $customer_id);
        if($data = $this->_getReadAdapter()->fetchAll($select)){
            $reviewArray = array();            
            foreach($data as $str_info){                
                $reviewArray[] = $str_info['review_id'];
            }            
           

        }
            
      return $reviewArray;  
    }

    public function ratings($productid, $storeId)
        {
           
            $summaryData = Mage::getModel('review/review_summary')
                        ->setStoreId($storeId)
                        ->load($productid);
            if ($summaryData['rating_summary']){
                 $votesCollection = Mage::getModel('rating/rating_option_vote')
                    ->getResourceCollection();
                 $votesCollection->getSelect()
                    ->join(array('rev'=>Mage::helper('reviewsplus')->_getTableName('review')),
                        'rev.review_id=main_table.review_id')
                    ->join(array('rat'=>Mage::helper('reviewsplus')->_getTableName('review_store')),
                        'rat.review_id=main_table.review_id')
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns(array('value','entity_pk_value'))
                    ->columns(array('status_id'),'rev')
                    ->columns(array('store_id'),'rat');
                 $votesCollection->addFieldToFilter('rev.status_id', array('eq' => '1'))
                    ->addFieldToFilter('rev.entity_pk_value', array('eq' => $productid))
                    ->addFieldToFilter('rat.store_id', array('eq' =>Mage::app()->getStore()->getId()))
                    ->load();

                foreach ($votesCollection as $key) 
                {
                    # code...
                    $array[] =  $key->getData('value');
                    $ratingPercent =$summaryData['rating_summary']; 
                    $product = Mage::getModel('catalog/product')->load($productid);
                    $reviewData = Mage::getModel('review/review/summary');  
                    $storeId = Mage::app()->getStore()->getId();
                    Mage::getModel('review/review')
                      ->getEntitySummary($product, $storeId);
                    $ratingSummary = $product->getRatingSummary($storeId);
                    $string = ($ratingPercent * 5)/100 .' based on ' . count($array) . ' ratings';
                 }
            return array($summaryData['rating_summary'], $array,$string);
        }
}
}
?>