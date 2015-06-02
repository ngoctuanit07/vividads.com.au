<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     5aaeipF7ooGEnIcLG9rWordPqCAsU9nCJvGBjy56tC
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Deliverydate_Model_Rewrite_AdminhtmlSalesOrderCreate extends Mage_Adminhtml_Model_Sales_Order_Create
{

    /**
     * Create new order
     *
     * @return Mage_Sales_Model_Order
     */
    public function createOrder()
    {
        if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
        {
            // START AITOC DELIVERY DATE

            $errors = Mage::getModel('adjdeliverydate/step')->process();

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->getSession()->addError($error);
                }
                Mage::throwException('');
            }

            $quoteAddress = $this->getQuote()->getShippingAddress();
            Mage::getModel('adjdeliverydate/quote')->saveDelivery($quoteAddress);

            // FINISH AITOC DELIVERY DATE

            $this->_validate();

            if (!$this->getQuote()->getCustomerIsGuest()) {
                $this->_putCustomerIntoQuote();
            }

            $quoteConvert = Mage::getModel('sales/convert_quote');

            /* @var $quoteConvert Mage_Sales_Model_Convert_Quote */

            $quote = $this->getQuote();
            if (!$this->getSession()->getOrder()->getId()) {
                $quote->reserveOrderId();
            }

            if ($this->getQuote()->getIsVirtual()) {
                $order = $quoteConvert->addressToOrder($quote->getBillingAddress());
            }
            else {
                $order = $quoteConvert->addressToOrder($quote->getShippingAddress());
            }
            $order->setBillingAddress($quoteConvert->addressToOrderAddress($quote->getBillingAddress()))
                ->setPayment($quoteConvert->paymentToOrderPayment($quote->getPayment()));
            if (!$this->getQuote()->getIsVirtual()) {
                $order->setShippingAddress($quoteConvert->addressToOrderAddress($quote->getShippingAddress()));
            }

            if (!$this->getQuote()->getIsVirtual()) {
                foreach ($quote->getShippingAddress()->getAllItems() as $item) {
                    /* @var $item Mage_Sales_Model_Quote_Item */
                    $orderItem = $quoteConvert->itemToOrderItem($item);
                    $options = array();
                    if ($productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct())) {
                        $productOptions['info_buyRequest']['options'] = $this->_prepareOptionsForRequest($item);
                        $options = $productOptions;
                    }
                    if ($addOptions = $item->getOptionByCode('additional_options')) {
                        $options['additional_options'] = unserialize($addOptions->getValue());
                    }
                    if ($options) {
                        $orderItem->setProductOptions($options);
                    }

                    if ($item->getParentItem()) {
                        $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
                    }

                    $order->addItem($orderItem);
                }
            }
            if ($this->getQuote()->hasVirtualItems()) {
                foreach ($quote->getBillingAddress()->getAllItems() as $item) {
                    /* @var $item Mage_Sales_Model_Quote_Item */
                    $orderItem = $quoteConvert->itemToOrderItem($item);
                    $options = array();
                    if ($productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct())) {
                        $productOptions['info_buyRequest']['options'] = $this->_prepareOptionsForRequest($item);
                        $options = $productOptions;
                    }
                    if ($addOptions = $item->getOptionByCode('additional_options')) {
                        $options['additional_options'] = unserialize($addOptions->getValue());
                    }
                    if ($options) {
                        $orderItem->setProductOptions($options);
                    }

                    if ($item->getParentItem()) {
                        $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
                    }

                    $order->addItem($orderItem);
                }
            }

            if ($this->getSendConfirmation()) {
                $order->setEmailSent(true);
            }

            if ($this->getSession()->getOrder()->getId()) {
                $oldOrder = $this->getSession()->getOrder();

                $originalId = $oldOrder->getOriginalIncrementId() ? $oldOrder->getOriginalIncrementId() : $oldOrder->getIncrementId();
                $order->setOriginalIncrementId($originalId);
                $order->setRelationParentId($oldOrder->getId());
                $order->setRelationParentRealId($oldOrder->getIncrementId());
                $order->setEditIncrement($oldOrder->getEditIncrement()+1);
                $order->setIncrementId($originalId.'-'.$order->getEditIncrement());
            }

            $order->place();
            $this->_saveCustomerAfterOrder($order);
            $order->save();

            if ($this->getSession()->getOrder()->getId()) {
                $oldOrder = $this->getSession()->getOrder();

                $this->getSession()->getOrder()->setRelationChildId($order->getId());
                $this->getSession()->getOrder()->setRelationChildRealId($order->getIncrementId());
                $this->getSession()->getOrder()->cancel()
                    ->save();
                $order->save();
            }

            if ($this->getSendConfirmation()) {
                $order->sendNewOrderEmail();
            }

            return $order;
        }
        elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
        {
            // START AITOC DELIVERY DATE
            $errors = Mage::getModel('adjdeliverydate/step')->process();

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->getSession()->addError($error);
                }
                Mage::throwException('');
            }
            // FINISH AITOC DELIVERY DATE

            $quoteAddress = $this->getQuote()->getShippingAddress();
            Mage::getModel('adjdeliverydate/quote')->saveDelivery($quoteAddress);

            $order = parent::createOrder();

            return $order;
        }
    }

}