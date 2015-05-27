<?php
class AsiaConnect_Gallery_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('gallery');
    }
    
    public function getSuccess($clear=false)
    {
        $messages = $this->getData('success');
    	if ($clear) {
            $this->unsetData('success');
            return $messages;
        }
        return $messages;
    }
}
