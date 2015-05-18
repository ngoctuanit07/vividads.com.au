<?php
class IWD_OrderManager_Adminhtml_Sales_AddressController extends Mage_Adminhtml_Controller_Action
{
    protected $reload = true;

    public function editAddressAction()
    {
        $result = array('status' => 1);

        try {
            $address = $this->prepareDataBeforeSave();
            $this->isNeedReloadPage($address);

            Mage::getModel('iwd_ordermanager/address')->updateOrderAddress($address);

            $result['address'] = $this->getAddressTextAfterSave($address);

        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function getAddressTextAfterSave($address)
    {
        if(!$this->reload){
            return $this->getLayout()
                ->createBlock('iwd_ordermanager/adminhtml_sales_order_address_text')
                ->setData('address', $address)
                ->toHtml();
        }

        return false;
    }

    protected function isNeedReloadPage($address)
    {
        $order_address = Mage::getModel('sales/order_address')->load($address['address_id']);
        $recalculate = Mage::getModel('iwd_ordermanager/address')->isNeedRecalculateOrderTotalAmount($address, $order_address);
        return $this->reload = ($recalculate || isset($address["confirm_edit"]));
    }

    protected function prepareDataBeforeSave(){
        $address = $this->getRequest()->getPost();

        $id = $address["address_id"];

        if(isset($address["country_id_".$id])){
            $address["country_id"] = $address["country_id_".$id];
            unset($address["country_id_".$id]);
        }
        if(isset($address["region_".$id])){
            $address["region"] = $address["region_".$id];
            unset($address["region_".$id]);
        }
        if(isset($address["region_id_".$id])){
            $address["region_id"] = $address["region_id_".$id];
            unset($address["region_id_".$id]);
        }
        if(isset($address["vat_id_".$id])){
            $address["vat_id"] = $address["vat_id_".$id];
            unset($address["vat_id_".$id]);
        }

        return $address;
    }

    public function getEditAddressFormAction()
    {
        $result = array('status' => 1);

        try {
            $address_id = $this->getRequest()->getPost('address_id');
            $address = Mage::getModel('sales/order_address')->load($address_id);
            $is_allow = Mage::getModel('iwd_ordermanager/address')->isAllowEditAddress();

            if ($address && $is_allow) {
                $result['form'] = $this->getLayout()
                    ->createBlock('iwd_ordermanager/adminhtml_sales_order_address_form')
                    ->setData('address_id', $address_id)
                    ->setData('address', $address)
                    ->toHtml();
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}