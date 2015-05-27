<?php

class Artis_Vendor_Model_Vendorname extends Varien_Object
{
    //const STATUS_ENABLED	= 1;
    //const STATUS_DISABLED	= 2;
    

    static public function getOptionArray()
    {
        $adminUserModel = Mage::getModel('admin/user');
        $userCollection = $adminUserModel->getCollection()->load();
        foreach($userCollection as $user)
        {
             $roleId = implode('', $user->getRoles());
             $roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
             if($roleName == 'Vendor')
             {
                 $str_array[$user->getId()] = Mage::helper('vendor')->__($user->getUsername());
             }
        }
       
        return $str_array;
    }
    
    static public function getOptionList()
    {
        $adminUserModel = Mage::getModel('admin/user');
        $userCollection = $adminUserModel->getCollection()->load();
        foreach($userCollection as $user)
        {
             $roleId = implode('', $user->getRoles());
             $roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
             if($roleName == 'Vendor')
             {
                 $str_array[$user->getUsername()] = Mage::helper('vendor')->__($user->getUsername());
             }
        }
       
        return $str_array;
    }
}