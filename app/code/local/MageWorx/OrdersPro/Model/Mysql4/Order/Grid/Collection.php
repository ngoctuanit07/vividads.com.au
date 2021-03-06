<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_OrdersPro_Model_Mysql4_Order_Grid_Collection extends MageWorx_OrdersPro_Model_Mysql4_Order_Grid_Collection_Abstract
{
    protected $_setFields = array();    
    
    public function __construct($resource=null) {
        parent::__construct();        
        if (Mage::helper('orderspro')->isEnabled() && $this->getSelect()!==null) {
            
            // aitoc   
            if ((string)Mage::getConfig()->getModuleConfig('Aitoc_Aitpermissions')->active=='true') $this->aitocAitpermissionsLimitCollectionByStore();
            if ((string)Mage::getConfig()->getModuleConfig('AW_Deliverydate')->active=='true') $this->awDeliverydate();
            
            if (Mage::app()->getRequest()->getControllerName()!='customer') {
                // orders grid
                $listColumns = Mage::helper('orderspro')->getGridColumns();
                $shellRequestFlag = false;            

                foreach ($listColumns as $column) {

                    switch ($column) {
                        case 'product_names':
                        case 'product_skus':
                        case 'product_options':
                            $this->setOrderItemTbl();
                            $shellRequestFlag = true;
                        break;

                        case 'payment_method':
                            $this->setFieldPaymentMethod();
                        break;            

                        case 'order_group':
                            $this->setFieldOrderGroup();
                            $shellRequestFlag = true;
                        break;

                        case 'qnty':
                            //$this->setOrderTbl();
                            //$this->setInvoiceTbl();                                                            
                            $this->setOrderItemTbl();                    
                        case 'shipped':    
                            $this->setShipmentTbl();
                            $shellRequestFlag = true;
                        break;
                        case 'billing_company':
                        case 'billing_street':
                        case 'billing_city':
                        case 'billing_region':
                        case 'billing_country':
                        case 'billing_postcode':
                        case 'billing_telephone':
                            $this->setOrderAddressTbl('billing');
                            $shellRequestFlag = true;
                        break;
                        case 'shipping_company':
                        case 'shipping_street':    
                        case 'shipping_city':
                        case 'shipping_region':
                        case 'shipping_country':
                        case 'shipping_postcode':
                        case 'shipping_telephone':
                            $this->setOrderAddressTbl('shipping');
                            $shellRequestFlag = true;
                        break;                        
                    }
                }

                
                // amasty
                if ((string)Mage::getConfig()->getModuleConfig('Amasty_Orderattach')->active=='true') $this->setAmastyOrderattachTbl();
                if ((string)Mage::getConfig()->getModuleConfig('Amasty_Orderattr')->active=='true') $this->setAmastyOrderattrTbl();
                
                
                if ($shellRequestFlag) $this->setShellRequest();
            } else {
                // customers grid
                if (Mage::app()->getRequest()->getActionName()!='orders') return $this;                
                $listColumns = Mage::helper('orderspro')->getCustomerGridColumns();
                $shellRequestFlag = false;                                
                
                
                // for enterprise add salesarchive orders
                if (version_compare(Mage::getVersion(), '1.9.0', '>=')) {
                    $cloneSelect = clone $this->getSelect();
                    $union = Mage::getResourceModel('enterprise_salesarchive/order_collection')
                        ->getOrderGridArchiveSelect($cloneSelect);
                    $unionParts = array($cloneSelect, $union);
                    $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
                    $this->setShellRequest();
                }
                
                foreach ($listColumns as $column) {

                    switch ($column) {
                        case 'product_names':
                        case 'product_skus':
                        case 'product_options':    
                            $this->setOrderItemTbl();
                            $shellRequestFlag = true;
                        break;
                        
                        case 'payment_method':
                            $this->setFieldPaymentMethod();
                        break;            

                        case 'order_group':
                            $this->setFieldOrderGroup();
                            $shellRequestFlag = true;
                        break;

                        case 'qnty':                        
                            $this->setOrderItemTbl();                    
                        case 'shipped':    
                            $this->setShipmentTbl();
                            $shellRequestFlag = true;
                        break;            
                    }
                }
                
                if ($shellRequestFlag) $this->setShellRequest();
                
                
                foreach ($listColumns as $column) {                      
                    switch ($column) {                        
                        case 'status': $this->addFieldToSelect('status'); break;                        
                        case 'product_names': 
                            $this->addFieldToSelect('product_names');
                            if (Mage::helper('orderspro')->isShowThumbnails()) $this->addFieldToSelect('product_ids');
                        break;        
                        case 'product_skus': $this->addFieldToSelect('skus'); break;
                        case 'product_options': $this->addFieldToSelect('product_options'); break;
                        case 'total_refunded': $this->addFieldToSelect('total_refunded'); break;
                        case 'base_total_refunded': $this->addFieldToSelect('base_total_refunded'); break;
                        case 'customer_email': $this->addFieldToSelect('customer_email'); break;
                        case 'customer_group': $this->addFieldToSelect('customer_group_id'); break;
                        case 'tax_amount': $this->addFieldToSelect('tax_amount'); break;
                        case 'base_tax_amount': $this->addFieldToSelect('base_tax_amount'); break;
                        case 'discount_amount': $this->addFieldToSelect('discount_amount'); break;
                        case 'base_discount_amount': $this->addFieldToSelect('base_discount_amount'); break;
                        case 'shipping_method': $this->addFieldToSelect('shipping_method'); $this->addFieldToSelect('shipping_description'); break;                          
                        case 'payment_method': $this->addFieldToSelect('method'); break;
                        case 'internal_credit': 
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) $this->addFieldToSelect('customer_credit_amount');                            
                            break;
                        case 'base_internal_credit': 
                            if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) $this->addFieldToSelect('base_customer_credit_amount');                            
                            break;
                        case 'order_group': $this->addFieldToSelect('order_group_id'); break;
                        case 'qnty':
                            $this->addFieldToSelect('total_qty_shipped');
                            $this->addFieldToSelect('total_qty_invoiced');
                            $this->addFieldToSelect('total_qty_ordered');
                            $this->addFieldToSelect('total_qty_refunded');
                            break;
                        case 'weight': $this->addFieldToSelect('weight'); break;
                        case 'shipped': $this->addFieldToSelect('shipped'); break;
                        case 'coupon_code': $this->addFieldToSelect('coupon_code'); break;
                        case 'billing_company': $this->addFieldToSelect('billing_company'); break;
                        case 'billing_city': $this->addFieldToSelect('billing_city'); break;
                        case 'billing_postcode': $this->addFieldToSelect('billing_postcode'); break;
                        case 'shipping_company': $this->addFieldToSelect('shipping_company'); break;
                        case 'shipping_city': $this->addFieldToSelect('shipping_city'); break;
                        case 'shipping_postcode': $this->addFieldToSelect('shipping_postcode'); break;
                        case 'is_edited': $this->addFieldToSelect('is_edited'); break;
                    }
                }
                $this->addFieldToSelect('base_currency_code');
            }                
        }    
        
    }    

    public function setOrderItemTbl() {                      
        if ($this->getSelect()!==null && !isset($this->_setFields['setOrderItemTbl'])) {
            //$this->getSelect()->columns(array('product_names' =>"(SELECT GROUP_CONCAT(name SEPARATOR '\n') FROM ".$this->getTable('sales/order_item')." WHERE parent_item_id IS NULL AND order_id=main_table.entity_id)"));
            $this->getSelect()->joinLeft(array('order_item_tbl'=>$this->getTable('sales/order_item')),
                    'order_item_tbl.order_id = main_table.entity_id',
                    array(
                        'product_names' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`name` SEPARATOR \'\n\')'),
                        'skus' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`sku` SEPARATOR \'\n\')'),
                        'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`product_id` SEPARATOR \'\n\')'),
                        'product_options' => new Zend_Db_Expr('GROUP_CONCAT(order_item_tbl.`product_options` SEPARATOR \'\n\')'),
                        'total_qty_refunded' => new Zend_Db_Expr('SUM(order_item_tbl.`qty_refunded`)'),
                        'total_qty_invoiced' => new Zend_Db_Expr('SUM(order_item_tbl.`qty_invoiced`)')
                    ))
                    ->where('order_item_tbl.`parent_item_id` IS NULL')
                    ->group('main_table.entity_id');
            
            $this->_setFields['setOrderItemTbl'] = true;
        }      
        return $this;
    }
    
//    public function joinProductThumbnail() {        
//        $connection = $this->getConnection('core_read');
//        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();                
//
//        $attributeId = $connection->fetchOne("SELECT `attribute_id` FROM `".$tablePrefix."eav_attribute` WHERE `attribute_code` = 'thumbnail' AND `frontend_input` = 'media_image'");
//        if (!$attributeId) return $this;
//        $this->getSelect()->joinLeft(array('catalog_product_entity_tbl'=>$tablePrefix.'catalog_product_entity_varchar'),
//                'catalog_product_entity_tbl.entity_id = order_item_tbl.`product_id` AND catalog_product_entity_tbl.`attribute_id` = '.$attributeId. ' AND catalog_product_entity_tbl.`store_id`=0',
//                array('thumbnail' => new Zend_Db_Expr('GROUP_CONCAT(catalog_product_entity_tbl.`value` SEPARATOR \'\n\')')));
//        return $this;
//    }
    
    public function setOrderAddressTbl($addressType='billing') {                      
        if ($this->getSelect()!==null  && !isset($this->_setFields['setOrderAddressTbl'.$addressType])) {            
            $this->getSelect()->joinLeft(array('order_address_'.$addressType.'_tbl'=>$this->getTable('sales/order_address')),
                        'order_address_'.$addressType.'_tbl.parent_id = main_table.entity_id AND order_address_'.$addressType.'_tbl.`address_type` = "'.$addressType.'"',
                        array($addressType.'_company' => 'company', $addressType.'_street' => 'street', $addressType.'_city' => 'city', $addressType.'_region' => 'region', $addressType.'_country_id' => 'country_id', $addressType.'_postcode' => 'postcode', $addressType.'_telephone' => 'telephone')
                    )
                    ->group('main_table.entity_id');
            $this->_setFields['setOrderAddressTbl'.$addressType] = true;
        }     
        return $this;
    }
    
    public function setFieldPaymentMethod() {              
        if ($this->getSelect()!==null) {
            $this->setShellRequest();        
            $this->getSelect()->joinLeft(array('order_payment_tbl'=>$this->getTable('sales/order_payment')),
                    'order_payment_tbl.parent_id = main_table.entity_id', 'method'                    
                    //array('method' => new Zend_Db_Expr('GROUP_CONCAT(`method` SEPARATOR \'\n\')'))
            )->group('main_table.entity_id');
        }
        return $this;
    }        
    
    public function setFieldOrderGroup() {              
        if ($this->getSelect()!==null) {
            $this->getSelect()->joinLeft(array('order_item_group_tbl'=>$this->getTable('orderspro/order_item_group')),
                    'order_item_group_tbl.order_id = main_table.entity_id',                    
                    array('order_group_id' => new Zend_Db_Expr('IFNULL(order_item_group_tbl.`order_group_id`, 0)'))
            );            
        }
        return $this;
    }
    
//    public function setInvoiceTbl() {              
//        if ($this->getSelect()!==null) {
//            $this->getSelect()->joinLeft(array('invoice_tbl'=>$this->getTable('sales/invoice')),
//                    'invoice_tbl.order_id = main_table.entity_id',                    
//                    array('total_qty_invoiced' => new Zend_Db_Expr('IFNULL(invoice_tbl.`total_qty`, 0)'))
//            );
//        }
//        return $this;
//    }
    
    public function setShipmentTbl() {              
        if ($this->getSelect()!==null && !isset($this->_setFields['setShipmentTbl'])) {
            $this->getSelect()->joinLeft(array('shipment_tbl'=>$this->getTable('sales/shipment')),
                    'shipment_tbl.order_id = main_table.entity_id',                    
                    array (
                        'shipped' => new Zend_Db_Expr('(IF(IFNULL(shipment_tbl.`entity_id`, 0)>0, 1, 0))'),
                        'total_qty_shipped' => new Zend_Db_Expr('IFNULL(shipment_tbl.`total_qty`, 0)')
                    )                    
            )->group('main_table.entity_id');
            $this->_setFields['setShipmentTbl'] = true;
        }                        
        return $this;
    }        
    
    public function setAmastyOrderattachTbl() {
        $attachments = Mage::getModel('amorderattach/field')->getCollection();
        $attachments->addFieldToFilter('show_on_grid', 1);
        $attachments->load();

        if ($attachments->getSize()) {
            $fields = array();
            foreach ($attachments as $attachment) {
                $fields[] = $attachment->getFieldname();
            }
            $this->getSelect()->joinLeft(
                array('attachments' => Mage::getModel('amorderattach/order_field')->getResource()->getTable('amorderattach/order_field')), "main_table.entity_id = attachments.order_id", $fields
            );
        }
    }
    public function setAmastyOrderattrTbl() {
        $attributes = Mage::getModel('eav/entity_attribute')->getCollection();
        $attributes->addFieldToFilter('entity_type_id', Mage::getModel('eav/entity')->setType('order')->getTypeId());
        $attributes->addFieldToFilter('show_on_grid', 1);
        $attributes->load();
        if ($attributes->getSize()){
            $fields = array();
            foreach ($attributes as $attribute) {
                $fields[] = $attribute->getAttributeCode();
            }
            
            $alias = 'main_table';
            $this->getSelect()
                    ->joinLeft(
                         array('custom_attributes' => Mage::getModel('amorderattr/attribute')->getResource()->getTable('amorderattr/order_attribute')),
                         "$alias.entity_id = custom_attributes.order_id",
                         $fields
                    );
        }        
    }
    
    
    
    protected function aitocAitpermissionsLimitCollectionByStore() {
        if (!$this->getFlag('permissions_processed')) {
            if (Mage::helper('aitpermissions')->isPermissionsEnabled()) {
                $AllowedStoreviews = Mage::helper('aitpermissions')->getAllowedStoreviews();
                if (version_compare(Mage::getVersion(), '1.4.1.0', '>')) {
                    $this->addAttributeToFilter('main_table.store_id', array('in' => $AllowedStoreviews));
                } else {
                    $this->addAttributeToFilter('store_id', array('in' => $AllowedStoreviews));
                }
            }
            $this->setFlag('permissions_processed', true);
        }
    }
    protected function awDeliverydate() {
        $tableName = Mage::getModel('deliverydate/delivery')->getCollection()->getTable('deliverydate/delivery');
        $this->getSelect()->joinLeft(array('del' => $tableName), '`main_table`.`entity_id`=`del`.`order_id`', array('aw_deliverydate_date' => 'del.delivery_date'));
    }
    public function setShellRequest() {              
        if ($this->getSelect()!==null) {            
            $sql = $this->getSelect()->assemble();
            $this->getSelect()->reset()->from(array('main_table' => new Zend_Db_Expr('('.$sql.')')), '*');
            //echo $this->getSelect()->assemble(); exit;
        }                        
        return $this;
    }
    
}