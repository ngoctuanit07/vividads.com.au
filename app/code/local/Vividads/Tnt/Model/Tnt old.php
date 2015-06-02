<?php

class Vividads_Tnt_Model_Tntservices extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('tnt/tnt');
    }
	public function insertData(){
		
		 $model  = Mage::getModel("tnt/tnt")->load($id); //i dont think load is required, but it s no problem.
        $model->setData(array('TransmissionIdentifier'=>45,'SenderInterchangeAddress'=>'naveed	','ReceiverInterchangeAddress'=>$xxxcode['value']))->save(); 
		
		}
}																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																						