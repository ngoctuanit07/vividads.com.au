<?php

class Artis_Designer_Model_Designer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('designer/designer');
    }
	
	public function heigh_res_file_uploaded($file_id=0){
		
		$_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$_vendor_item_table = Mage::getSingleton('core/resource')->getTableName('vendor_item');
		
		$_sql = $_connectionRead->select()
								->from($_vendor_item_table,'heigh_res_file')
		                        ->where('task_designer_id=?',$file_id);
		// var_dump($_sql->__toString());						
		
		try{
			$_result = $_connectionRead->fetchOne($_sql);
			
			if($_result){
				$_is_file_uploaded = true;
				}else{
				$_is_file_uploaded = false;	
					}
			
			}catch(Exception $e){
				print_r($e);
			}
		
		return $_is_file_uploaded;
		}
}