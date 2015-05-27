<?php

class Magestore_Imageoption_Model_Usedqty 
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('imageoption')->__('Enabled')),
            array('value'=>0, 'label'=>Mage::helper('imageoption')->__('Disabled')),
        );
    }
}