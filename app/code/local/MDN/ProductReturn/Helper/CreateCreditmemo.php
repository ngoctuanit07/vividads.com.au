<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_ProductReturn_Helper_CreateCreditmemo extends MDN_ProductReturn_Helper_CreateAbstract {

    /**
     * Main function, create credit memo using data
     *
     * @param unknown_type $object
     */
    public function CreateCreditmemo($object) {

        //load sales order and check for errors
        $order = mage::getModel('sales/order')->load($object['order_id']);
        $rma = mage::getModel('ProductReturn/Rma')->load($object['rma_id']);
        if (!$order->getId())
            throw new Exception(mage::helper('ProductReturn')->__('Unable to load sales order'));
        if (!$order->canCreditmemo())
            throw new Exception(mage::helper('ProductReturn')->__('Can not do credit memo for order'));
        if (!$this->hasProducts($object))
            throw new Exception(mage::helper('ProductReturn')->__('Can not do credit memo without product'));

        //init credit memo
        $invoice = null;
        $convertor = Mage::getModel('sales/convert_order');
        $creditmemo = $convertor->toCreditmemo($order)->setInvoice($invoice);
        $debug = '';

        //add items
        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->isDummy() && !$orderItem->getQtyToRefund()) {
                continue;
            }

            $debug .= '<br>Processing ' . $orderItem->getName();

            $item = $convertor->itemToCreditmemoItem($orderItem);
            $qty = $this->getQtyForProductId($object, $orderItem->getproduct_id());
            if ($qty == 0) {
                if ($orderItem->isDummy()) {
                    if ($orderItem->getParentItem() && ($qty > 0)) {
                        $parentItemNewQty = $this->getQtyForProductId($object, $orderItem->getParentItem()->getproduct_id());
                        $parentItemOrigQty = $orderItem->getParentItem()->getQtyOrdered();
                        $itemOrigQty = $orderItem->getQtyOrdered() / $parentItemOrigQty;
                        $qty = $itemOrigQty * $parentItemNewQty;
                    }
                }
            }

            $debug .= '<br>' . $qty . 'x' . $orderItem->getname();

            $item->setQty($qty);
            $creditmemo->addItem($item);
        }

        //refund shipping fees
        if ($object['refund_shipping_fees']) {
            $creditmemo->setShippingAmount((float) ($order->getBaseShippingAmount() - $order->getBaseShippingRefunded()));
            $creditmemo->setBaseShippingAmount((float) ($order->getBaseShippingAmount() - $order->getBaseShippingRefunded()));
        }
        else
            $creditmemo->setBaseShippingAmount(0.00);

        $creditmemo->collectTotals();
        $creditmemo->register();
        $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($creditmemo)
                        ->addObject($creditmemo->getOrder());
        $transactionSave->save();

        //notify customer
        $creditmemo->sendEmail(true, '');

        //store creditmemo creation in history
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/sales_creditmemo/view', array('creditmemo_id' => $creditmemo->getId()));
        $rma->addHistoryRma('<a href="' . $url . '">' . mage::helper('ProductReturn')->__('Credit memo #%s created', $creditmemo->getincrement_id()) . '</a>');

        //store credit memo in rma/products
        foreach ($object['products'] as $product) {
            if ((isset($product['rp_id'])) && ($product['rp_id'] != null))
            {
                //update information
                $rmaProduct = mage::getModel('ProductReturn/RmaProducts')->load($product['rp_id']);
                $rmaProduct->setrp_action_processed(1)
                        ->setrp_rp_destination_processed(1)
                        ->setrp_associated_object(mage::helper('ProductReturn')->__('Credit memo #%s', $creditmemo->getincrement_id()))
                        ->setrp_object_type('creditmemo')
                        ->setrp_object_id($creditmemo->getId())
                        ->setrp_action('refund')
                        ->setrp_destination($product['destination'])
                        ->save();
            }

            //manage destination
            $description = mage::helper('ProductReturn')->__('Product return #%s', $rma->getrma_ref());
            $this->manageProductDestination($product, $order->getStore()->getwebsite_id(), $description);
            
        }

        $comment = mage::helper('ProductReturn')->__('Created for Product return #%s', $rma->getrma_ref());
        $creditmemo->addComment($comment, false);
        $creditmemo->save();

        return $creditmemo;
    }

    /**
     * Check if credit memo request contains products
     *
     * @param unknown_type $object
     * @return unknown
     */
    protected function hasProducts($object) {
        $retour = false;
        foreach ($object['products'] as $item) {
            if ($item['qty'] > 0)
                $retour = true;
        }
        return $retour;
    }

    /**
     * return invoice
     *
     * @param unknown_type $order
     */
    protected function getInvoice($order) {
        $collection = $order->getInvoiceCollection();
        foreach ($collection as $item) {
            return $item;
        }

        return null;
    }

    /**
     * Return qty to refund for productid
     *
     * @param unknown_type $object
     */
    protected function getQtyForProductId($object, $productId) {
        $retour = 0;
        $products = $object['products'];
        foreach ($products as $key => $value) {
            if ($value['product_id'] == $productId)
                $retour = $value['qty'];
        }
        return $retour;
    }

}