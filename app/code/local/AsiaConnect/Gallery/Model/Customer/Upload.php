<?php

class AsiaConnect_Gallery_Model_Customer_Upload
{
    const DISABLE	 		= 0;
    const ALL_CUSTOMER		= 1;
    const LOGGIN_CUSTOMER	= 2;
    
	protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options =  array(
            array('value'=>self::ALL_CUSTOMER,'label'=> Mage::helper('gallery')->__('All Customer')),
            array('value'=>self::LOGGIN_CUSTOMER,'label'=> Mage::helper('gallery')->__('Only Logged-in Customer')),
            array('value'=>self::DISABLE,'label'=> Mage::helper('gallery')->__('Disable')),
        );
        }
        return $this->_options;
    }
}
