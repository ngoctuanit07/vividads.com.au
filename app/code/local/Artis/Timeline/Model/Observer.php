<?php
class Artis_Timeline_Model_Observer extends Mage_Core_Model_Abstract
{
	public function onBeforeSave($obj)
	{
		$object = $obj->getEvent()->getObject();
 
		if ($object->getId() && $object->getId() == $object->load($object->getId())->getId()) {
			$object->setIsNewlyCreated(false);
		} else {
			$object->setIsNewlyCreated(true);
		}
	}
 
	public function onAfterSave($obj)
	{
		$object = $obj->getEvent()->getObject();
		if ($object->getIsNewlyCreated()) {
			Mage::dispatchEvent('on_create', array('object' => $object));
		} else {
			Mage::dispatchEvent('on_update', array('object' => $object));
		}
	}
 
	public function onCreate($obj)
	{
		$object = $obj->getEvent()->getObject();
		Mage::log('Just created new object of class ' . get_class($object) . '. Object id: ' . $object->getId());
	}
 
	public function onUpdate($obj)
	{
		$object = $obj->getEvent()->getObject();
		Mage::log('Just updated object of class ' . get_class($object) . '. Object id: ' . $object->getId());
	}
}