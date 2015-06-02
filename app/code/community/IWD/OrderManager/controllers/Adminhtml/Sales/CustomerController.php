<?php
class IWD_OrderManager_Adminhtml_Sales_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function getEditAccountFormAction()
    {
        $result['status'] = 0;
        $order_id = $this->getRequest()->getPost('order_id');
        $order = Mage::getModel('sales/order')->load($order_id);

        if ($order) {
            $fields = Mage::getModel('iwd_ordermanager/order_customer')->CustomerInfoOrderField($order);

            $result['form'] = $this->getLayout()
                ->createBlock('iwd_ordermanager/adminhtml_sales_order_account_form')
                ->setData('order_id', $order_id)
                ->setData('order', $fields)
                ->toHtml();

            $result['status'] = 1;
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function editAccountAction()
    {
        try {
            $params = $this->getRequest()->getParams();

            Mage::getModel('iwd_ordermanager/order_customer')->updateOrderCustomer($params);

            /*if ($order) {
                $result['status'] =
                $order = Mage::getModel('sales/order')->load($order_id);
                $groupName = Mage::getModel('customer/group')->load((int)$order->getCustomerGroupId())->getCode();
                $getCustomerAccountData = $this->getCustomerAccountData($order);
                $getCustomerViewUrl = $this->getCustomerViewUrl($order);
                $result['text'] = $this->getLayout()
                    ->createBlock('iwd_ordermanager/adminhtml_sales_order_account_text')
                    ->setData('order', $order)
                    ->setData('group_name', $groupName)
                    ->setData('customer_account_data', $getCustomerAccountData)
                    ->setData('customer_view_url', $getCustomerViewUrl)
                    ->toHtml();
            }*/

            $result = array('status' => 1, 'text' => 'test');
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }







    /**
     * Return array of additional account data
     * Value is option style array
     *
     * @return array
     */
    public function getCustomerAccountData($order)
    {
        $accountData = array();

        /* @var $config Mage_Eav_Model_Config */
        $config     = Mage::getSingleton('eav/config');
        $entityType = 'customer';
        $customer   = Mage::getModel('customer/customer');
        foreach ($config->getEntityAttributeCodes($entityType) as $attributeCode) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            $attribute = $config->getAttribute($entityType, $attributeCode);
            if (!$attribute->getIsVisible() || $attribute->getIsSystem()) {
                continue;
            }
            $orderKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $order->getData($orderKey);
            if ($orderValue != '') {
                $customer->setData($attribute->getAttributeCode(), $orderValue);
                $dataModel  = Mage_Customer_Model_Attribute_Data::factory($attribute, $customer);
                $value      = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML);
                $sortOrder  = $attribute->getSortOrder() + $attribute->getIsUserDefined() ? 200 : 0;
                $sortOrder  = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = array(
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, array('br'))
                );
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * Find sort order for account data
     * Sort Order used as array key
     *
     * @param array $data
     * @param int $sortOrder
     * @return int
     */
    protected function _prepareAccountDataSortOrder(array $data, $sortOrder)
    {
        if (isset($data[$sortOrder])) {
            return $this->_prepareAccountDataSortOrder($data, $sortOrder + 1);
        }
        return $sortOrder;
    }

    public function getCustomerViewUrl($order)
    {
        if ($order->getCustomerIsGuest() || !$order->getCustomerId())
            return false;

        return $this->getUrl('*/customer/edit', array('id' => $order->getCustomerId()));
    }
}
