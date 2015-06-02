<?php
class Artis_Eventcalendar_Block_Eventcalendar extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getEventcalendar()     
     { 
        if (!$this->hasData('eventcalendar')) {
            $this->setData('eventcalendar', Mage::registry('eventcalendar'));
        }
        return $this->getData('eventcalendar');
        
    }
}