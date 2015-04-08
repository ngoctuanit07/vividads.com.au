<?php
class Vividads_Tnt_Model_Mysql4_Typed extends Mage_Core_Model_Mysql4_Abstract{
	public function _construct(){
		$this->_init('tnt/typed', 'ManifestIdentifier');			
	}
}
?>