<?php
class Artis_Calendar_Block_Calendar extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCalendar()     
     { 
        if (!$this->hasData('calendar')) {
            $this->setData('calendar', Mage::registry('calendar'));
        }
        return $this->getData('calendar');
        
    }
}