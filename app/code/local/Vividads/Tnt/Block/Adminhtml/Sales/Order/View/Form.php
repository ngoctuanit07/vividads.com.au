<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order view plane
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Vividads_Tnt_Block_Adminhtml_Sales_Order_View_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Form 
{	
	protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('tnt/floating-1.12.js');

        return parent::_prepareLayout();
    }
	 protected function _beforeToHtml()
    {  
	
        
		$this->setTemplate("tnt/sales/order/view/form.phtml");
        return parent::_beforeToHtml();
    }
	
	 public function getOrder()
    {
        return Mage::registry('current_order');
    }
	
	public function getShipment(){
        return Mage::registry('current_shipment');
    }
	protected function _getCollectionClass()
    {
        return 'sales/order_shipment_grid_collection';
    }
	public function setCollection($collection)
    {
        $this->_collection = $collection;
    }
	public function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('total_qty')
            ->addFieldToSelect('shipping_name')
            ->setOrderFilter($this->getOrder())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    public function getSaveUrl()
    {
 		$params = $this->getRequest()->getParams();	
		$orderId = $params['order_id'];
		return $this->getUrl('*/sales_order_shipment/save', array('order_id' => $orderId));
    }

	public function getSavedShipments(){		
		$params = $this->getRequest()->getParams();	
		$orderId = $params['order_id'];
		//$test="orderid=".$orderId = $params['order_id'];						
		$order = Mage::getModel('sales/order')->load($orderId);			
		//$test.="size=".sizeof($order->getShipmentsCollection() );		
		$count=0;
		foreach($order->getShipmentsCollection() as $shipment){
			$shipmentId=$shipment->getId();
			$shipment= Mage::getModel('sales/order_shipment')->load($shipmentId);
			$addressid=$shipment['shipping_address_id'];
			$incrementid=$shipment['increment_id'];
			$address=Mage::getModel('sales/order_address')->load($addressid);
			$shipmentitems= Mage::getModel('sales/order_shipment_item')->getCollection()
                ->addFieldToFilter(
                        array(
                            'parent_id',//attribute_1 with key 0
                        ),
                        array(
                            array('eq'=>$shipmentId),//condition for attribute_1 with key 0
                                )
                            );
			$return[$count]=array("incrementid"=>$incrementid,"items"=>$shipmentitems,"address"=>$address);
			$count++;
		}
		return $return;			
										
	}
		
	public function getShipments(){		
		$params = $this->getRequest()->getParams();	
		$orderId = $params['order_id'];
		$order = Mage::getModel('sales/order')->load($orderId);	
		$count=0;
		$shipments=Mage::getModel('multishipping/shipment')->getCollection()
		 			->addFieldToFilter(
                        array(
                            'order_id',//attribute_1 with key 0
                        ),
                        array(
                            array('eq'=>$orderId),//condition for attribute_1 with key 0
                       )
                     );		
					 $return=array();
		foreach($shipments as $shipment){
			$shipmentId=$shipment->getId();
			$multiShipment = Mage::getModel('multishipping/shipment')->load($shipmentId);			
			$addressid=$multiShipment['shipping_address_id'];			
			$address=Mage::getModel('sales/order_address')->load($addressid);
			$shipmentitems = Mage::getModel('multishipping/shipmentitem')->getCollection()
                ->addFieldToFilter(
                        array(
                            'shipment_id',//attribute_1 with key 0
                        ),
                        array(
                            array('eq'=>$shipmentId),//condition for attribute_1 with key 0
                                )
                            );
			$return[$count]=array("items"=>$shipmentitems,"address"=>$address,"shipment"=>$shipment);
			$count++;
		}
		return $return;			
										
	}
	
	public function productName($prodId){
		$product = Mage::getModel('catalog/product')->load($prodId);		
		$name = $product->getName();		
		return $name;
	}
	public function productSku($prodId){
		$product = Mage::getModel('catalog/product')->load($prodId);
		$sku = $product->getSku();		
		return $sku;
			
	}
	public function getTntServices(){
		
				return Mage::getModel('tnt/tntservices')->getCollection();																			
		}
	
	
	
	function requestShippingRates($shipmentid){
		$shipmentModel= Mage::getModel('multishipping/shipment')->load($shipmentid);
		$addressid=$shipmentModel['shipping_address_id'];
		$address=Mage::getModel('sales/order_address')->load($addressid);
		$address->setOrderId($shipmentModel['order_id']);
		$address->setShipmentId($shipmentid);
				/** @var $request Mage_Shipping_Model_Rate_Request */
				$request = Mage::getModel('shipping/rate_request');
				
		// load shipment items
				$shipmentItemsModel=Mage::getModel('multishipping/shipmentitem')->getCollection()
						->addFieldToFilter(
								array(
									'shipment_id',//attribute_1 with key 0
								),
								array(
									array('eq'=>$address['shipment_id']),//condition for attribute_1 with key 0
										)
									);
				$items=array();
				foreach($shipmentItemsModel as $shipItems){
						$productid=$shipItems['product_id'];	
						$qty=$shipItems['qty'];	
						$order_item_id=$shipItems['order_item_id'];	
						$item=Mage::getModel('sales/order_item')->load($order_item_id);
						$item->setQty($qty);
						$items[]=$item;
					}	
				$request->setAllItems($items);
			
				$request->setDestCountryId($address->getCountryId());
				$request->setDestRegionId($address->getRegionId());
				$request->setDestRegionCode($address->getRegionCode());
				/**
				 * need to call getStreet with -1
				 * to get data in string instead of array
				 */
				$request->setDestStreet($address->getStreet(-1));
				$request->setDestCity($address->getCity());
				$request->setDestPostcode($address->getPostcode());
				$request->setPackageValue($item ? $item->getBaseRowTotal() : $address->getBaseSubtotal());
				$packageValueWithDiscount = $item
					? $item->getBaseRowTotal() - $item->getBaseDiscountAmount()
					: $address->getBaseSubtotalWithDiscount();
				$request->setPackageValueWithDiscount($packageValueWithDiscount);
				$request->setPackageWeight($item ? $item->getRowWeight() : $address->getWeight());
				$request->setPackageQty($item ? $item->getQty() : $address->getItemQty());
		
				/**
				 * Need for shipping methods that use insurance based on price of physical products
				 */
				$packagePhysicalValue = $item
					? $item->getBaseRowTotal()
					: $address->getBaseSubtotal() - $address->getBaseVirtualAmount();
				$request->setPackagePhysicalValue($packagePhysicalValue);
		
				$request->setFreeMethodWeight($item ? 0 : $address->getFreeMethodWeight());
		
				/**
				 * Store and website identifiers need specify from quote
				 */
				/*$request->setStoreId(Mage::app()->getStore()->getId());
				$request->setWebsiteId(Mage::app()->getStore()->getWebsiteId());*/
				$orderModel=Mage::getModel('sales/order')->load($address['order_id']);
		
		
				$request->setStoreId($orderModel->getStore()->getId());
				$request->setWebsiteId($orderModel->getStore()->getWebsiteId());
				$request->setFreeShipping($address->getFreeShipping());
				/**
				 * Currencies need to convert in free shipping
				 */
				$request->setBaseCurrency($orderModel->getStore()->getBaseCurrency());
				$request->setPackageCurrency($orderModel->getStore()->getCurrentCurrency());
				$request->setLimitCarrier($address->getLimitCarrier());
		
				$request->setBaseSubtotalInclTax($address->getBaseSubtotalInclTax());
		
				$result = Mage::getModel('shipping/shipping')->collectRates($request)->getResult();
		
				$found = false;
				if ($result) {
					$shippingRates = $result->getAllRates();
		/* quote rates */
					return $shippingRates;
				}
		}

}
	
			
?>	