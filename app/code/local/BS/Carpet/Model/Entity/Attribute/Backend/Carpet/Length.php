<?php

class BS_Carpet_Model_Entity_Attribute_Backend_Carpet_Length extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	/*
	public function afterLoad($object)
    {
        if (!$object->hasData($this->getAttribute()->getAttributeCode())){
            $object->setData($this->getAttribute()->getAttributeCode(), $this->getDefaultValue());
        }
        $object->setData($this->getAttribute()->getAttributeCode(), number_format($object->getData($this->getAttribute()->getAttributeCode()), 2));
    }
	*/
    public function beforeSave($object)
    {
        if ($object->hasData($this->getAttribute()->getAttributeCode())
            && $object->getData($this->getAttribute()->getAttributeCode()) == $this->getDefaultValue()){
            $object->unsData($this->getAttribute()->getAttributeCode());
        }
    }
}