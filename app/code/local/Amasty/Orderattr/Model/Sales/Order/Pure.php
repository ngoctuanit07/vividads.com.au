<?php
/**
* @author Amasty Team
* @copyright Copyright (c) Amasty (http://www.amasty.com)
* @package Amasty_Orderattr
*/
if (Mage::helper('core')->isModuleEnabled('Amasty_Deliverydate')) {
    class Amasty_Orderattr_Model_Sales_Order_Pure extends Amasty_Deliverydate_Model_Sales_Order {}
} else {
    class Amasty_Orderattr_Model_Sales_Order_Pure extends Mage_Sales_Model_Order {}
}
