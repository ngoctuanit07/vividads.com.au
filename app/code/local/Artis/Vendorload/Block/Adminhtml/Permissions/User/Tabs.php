<?php

/*
 * overload of app\code\core\Mage\Adminhtml\Block\Permissions\User\Edit\Tabs.php
 * to add a tab
 */
class Artis_Vendorload_Block_Adminhtml_Permissions_User_Tabs extends Mage_Adminhtml_Block_Permissions_User_Edit_Tabs {

    /**
     * Constructor
     */
    protected $isVendor=false;
    public function __construct() {
        parent::__construct();
        $user_id=$this->getRequest()->getParam('user_id');
        $usersModel=Mage::getModel('admin/user')->load($user_id)->getRole();
          if($usersModel->getRoleName() == 'Vendor' )
          {
            $this->setId('vendorload_edit_tabs');
            $this->setDestElementId('edit_form');
            $this->isVendor=true;
          }

    }

    /**
     * Set tabs
     */
    protected function _beforeToHtml() {
        if($this->isVendor)
        $this->addTabAfter('vendor_load', array(
            'label' => Mage::helper('vendorload')->__('Vendor Load'),
            'title' => Mage::helper('vendorload')->__('Vendor Load'),
            'content' => $this->getLayout()->createBlock('vendorload/Adminhtml_Permissions_User_Tabs_VendorLoad')->toHtml(),
        ),"roles_section");
        
        return parent::_beforeToHtml();;
        
    }
    
}
