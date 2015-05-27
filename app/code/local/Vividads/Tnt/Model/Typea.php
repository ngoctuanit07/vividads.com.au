<?php
class Vividads_Tnt_Model_Typea extends Mage_Core_Model_Abstract{
    public function _construct()
    {        
        $this->_init('tnt/typea');
		parent::_construct();
    }
	public function getTntServices(){		
		return Mage::getModel('tnt/typea')->getCollection();				
	}
		
 }
 ?>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																						