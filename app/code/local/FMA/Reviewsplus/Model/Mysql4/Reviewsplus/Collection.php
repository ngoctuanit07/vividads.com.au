<?php

/**
* 
*/
class FMA_Reviewsplus_Model_Mysql4_Reviewsplus_Collection extends Mage_Review_Model_Resource_Review_Collection
{
   
    public  function _construct()
    {
        parent::_construct();
        //$this->_init('reviewsplus/reviewsplus');
        
    }

	 protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(array('rev_details' => $this->_reviewDetailTable),
                'main_table.review_id = rev_details.review_id',
                array('review_reply'));
        return $this;
    }
}
?>