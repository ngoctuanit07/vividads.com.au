<?php

class Artis_Vendorload_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function ListSku() {
        $retour = array();
	$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('sku')->setOrder('sku');
        foreach ($collection as $item) {
            $retour[$item->getId()] = $item->getSku();
        }
        return $retour;
    }
    public function ListAttributes() {
      $entityType = Mage::getModel('catalog/product')->getResource()->getTypeId();
      $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityType);
      $allSet = array();
      foreach($collection as $coll){
         $allSet[$coll->getAttributeSetId()] =  $coll->getAttributeSetName();
      }
return $allSet;	
	}
    public function ListVendors(){
        $users=Mage::getModel('admin/user')->getCollection();
        foreach($users as $user){
            $userModel=Mage::getModel('admin/user')->load($user->getId())->getRole();
            if($userModel->getRoleName() == 'Vendor' ){

            $userid=$user->getId();
            $name=$user->getName();
            $options[$user->getId()]=$name;
        }
        }
            return $options;
    }

}
