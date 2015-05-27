<?php
 class Artis_Vendor_Model_Product_Attribute_Unit extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
    {
      public function getAllOptions()
      {
        $model = Mage::getModel('admin/user');
        $collection = $model->getCollection();
        $customerArr = array();
        $customerArr[] = array('value' => '0','label' => 'Select Vendor');
        foreach($collection as $customer)
        {
            $roleId = implode('', $customer->getRoles());
            $roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
            if($roleName == 'Vendor')
            {
                $customer = $model->load($customer->getId());
                $customerArr[] = array('value' => $customer->getId(),'label' => $customer->getUsername());
            }
        }
        if (!$this->_options) {
            $this->_options = $customerArr;
        }
        return $this->_options;
      }
    }
?>