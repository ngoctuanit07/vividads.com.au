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
 * @package    MageWorx_CustomOptions
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Advanced Product Options extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomOptions
 * @author     MageWorx Dev Team
 */
class MageWorx_CustomOptions_Model_Observer {

    // replace main product image in checkout/cart
    public function toHtmlBlockFrontBefore($observer) {
        $helper = Mage::helper('customoptions');
        if (!$helper->isEnabled() || !$helper->isImageModeEnabled()) return $this;        
        $block = $observer->getEvent()->getBlock();        
        if ($block instanceof Mage_Checkout_Block_Cart_Item_Renderer) {
            $product = $block->getProduct();
            if ($product) {
                if (is_null($product->getHasOptions())) $product->load($product->getId());
                if (($product->getTypeId()=='simple' || $product->getTypeId()=='virtual' || $product->getTypeId()=='downloadable') && $product->getHasOptions()) {
                    $post = $product->getCustomOption('info_buyRequest')->getValue();
                    if ($post) $post = unserialize ($post); else $post = array();
                    if (isset($post['options'])) $options = $post['options']; else $options = array();
                    if ($options) {
                        foreach ($options as $optionId => $value) {
                            $productOption = $product->getOptionById($optionId);
                            if (!$productOption) continue;
                            // if replace image mode setting
                            if ($productOption->getImageMode()==2) {
                                switch ($productOption->getType()) {
                                    case 'drop_down':
                                    case 'radio':
                                    case 'checkbox':                        
                                    case 'multiple':
                                    case 'swatch':
                                    case 'multiswatch':
                                        if (is_array($value)) {
                                            $optionTypeIds = $value;
                                        } else {
                                            $optionTypeIds = explode(',', $value);
                                        }
                                        foreach ($optionTypeIds as $optionTypeId) {
                                            if (!$optionTypeId) continue;
                                            $images = $productOption->getOptionValueImages($optionTypeId);
                                            if ($images) {
                                                foreach ($images as $index=>$image) {
                                                    // file
                                                    if ($image['source']==1 && (!$productOption->getExcludeFirstImage() || ($productOption->getExcludeFirstImage() && $index > 0))) {
                                                        // replace main image
                                                        $block->overrideProductThumbnail(Mage::getModel('customoptions/catalog_product_option_image')->init($image['image_file']));
                                                        return $this;
                                                    }
                                                }
                                            }
                                        }
                                }
                            }
                        }
                    }
                }
            }
            $block->overrideProductThumbnail(null);
        }
        return $this;
    }
    
    // add "Starting at" Price Prefix (front)
    public function toHtmlBlockFrontAfter($observer) {
        $helper = Mage::helper('customoptions');
        if (!$helper->isEnabled() || !$helper->isPricePrefixEnabled()) return $this;        
        $block = $observer->getEvent()->getBlock();        
        if ($block instanceof Mage_Catalog_Block_Product_Price) {
            $transport = $observer->getEvent()->getTransport();            
            if (Mage::app()->getRequest()->getControllerName()!='product' && Mage::app()->getRequest()->getActionName()!='configure') {
                $product = $block->getProduct();
                if ($product) {
                    if (is_null($product->getHasOptions())) $product->load($product->getId());
                    if (($product->getHasOptions() && $product->getFinalPrice()!=$product->getMaxPrice()) || ($product->getRequiredOptions() && $product->getAbsolutePrice())) {
                        $html = trim($transport->getHtml());
                        $htmlArr = explode('<', $html);
                        if (count($htmlArr)==7) {
                            $htmlArr[2] .= '<span class="price-label">'. $helper->__('Starting at') . '</span> ';
                            $html = implode('<', $htmlArr);
                            $transport->setHtml($html);
                        }                    
                    }
                }
            }
        }        
        return $this;
    }

    // ckeckout/cart
    public function checkQuoteItemQtyAndCustomerGroup($observer) {
        if (!Mage::helper('customoptions')->isEnabled()) return $this;
        $quoteItem = $observer->getEvent()->getItem();
        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote() || $quoteItem->getQuote()->getIsSuperMode()) {
            return $this;
        }

        $helper = Mage::helper('customoptions');      
        if (!$helper->isInventoryEnabled() && !$helper->isCustomerGroupsEnabled()) return $this;
        
        // product Qty
        $qty = 0;        
        // if update cart -> cart[182][qty]
        $quoteItemId = $quoteItem->getId();        
        if ($quoteItemId>0) {            
            $cartPost = Mage::app()->getRequest()->getParam('cart', false);
            if ($cartPost && isset($cartPost[$quoteItemId]['qty'])) $qty = $cartPost[$quoteItemId]['qty'];
        }                
        
        // standart add to cart
        if (!$qty) $qty = $quoteItem->getQty();
        
        if (!$qty) $qty = Mage::app()->getRequest()->getParam('qty', false);
        
        // get correctly options
        $options = false;        
        $post = Mage::app()->getRequest()->getParams();        
        
        if (isset($post['id'])) {
            // if update quote item 
            if ($post['id']==$quoteItemId) {
                // if quote item edited:
                if (isset($post['options'])) $options = $post['options'];
                $qty = Mage::app()->getRequest()->getParam('qty', false);                
            } else {
                return $this;
            }
        } else {
            $product = $quoteItem->getProduct();
            if (is_null($product->getHasOptions())) $product->load($product->getId());
            if (!$product->getHasOptions() || !$product->getCustomOption('info_buyRequest')) return $this;
            $post = $product->getCustomOption('info_buyRequest')->getValue();
            if ($post) $post = unserialize ($post); else $post = array();
            if (isset($post['options'])) $options = $post['options'];
        }        
        
        if ($options) {
            if (Mage::app()->getStore()->isAdmin()) {
                $sessionQuote = Mage::getSingleton('adminhtml/session_quote');
                if ($sessionQuote) $customerGroupId = $sessionQuote->getCustomer()->getGroupId(); else $customerGroupId = 0;        
            } else {
                $customerGroupId = Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getCustomer()->getGroupId() : 0;            
            }


            foreach ($options as $optionId => $option) {
                $productOption = Mage::getModel('catalog/product_option')->load($optionId);

                // check Options Customer Group
                if ($helper->isCustomerGroupsEnabled()) {
                    $groups = $productOption->getCustomerGroups();
                    if ($groups!=='' && !in_array($customerGroupId, explode(',', $groups))) {
                        $fullMessage = $helper->__('Some options are not available for your customer group. Please, edit product "%s"', $quoteItem->getProduct()->getName());
                        $message = $helper->__('Some options are not available for your customer group');
                        
                        $quoteItem->setHasError(true)->setMessage($message);
                        if ($quoteItem->getParentItem()) {
                            $quoteItem->getParentItem()->setMessage($message);
                        }
                        $quoteItem->getQuote()->setHasError(true)->addMessage($fullMessage, 'options');
                        return $this;
                        break;
                    }
                }
                
                // check Options Inventory
                if ($helper->isInventoryEnabled()) {
                    
                    $optionType = $productOption->getType();
                    if ($productOption->getGroupByType($optionType)!=Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) continue;                                        
                    if (!is_array($option)) $option = array($option);
                    
                    
                    foreach ($option as $optionTypeId) {
                        if (!$optionTypeId) continue;
                        
                        $row = $productOption->getOptionValue($optionTypeId);
                        list($customoptionsQty, $backorders) = $helper->getCustomoptionsQty(isset($row['customoptions_qty'])?$row['customoptions_qty']:'', isset($row['sku'])?$row['sku']:'', $optionId, $optionTypeId, $quoteItem->getId(), $quoteItem->getQuote(), true);
                        if ($backorders) continue;
                        if (substr($customoptionsQty, 0, 1)!='x' && $customoptionsQty!=='') {
                            
                            switch ($optionType) {
                                case 'checkbox':
                                case 'multiswatch':
                                    if (isset($post['options_'.$optionId.'_'.$optionTypeId.'_qty'])) $optionQty = intval($post['options_'.$optionId.'_'.$optionTypeId.'_qty']); else $optionQty = 1;
                                    break;
                                case 'drop_down':
                                case 'radio':
                                case 'swatch':    
                                    if (isset($post['options_'.$optionId.'_qty'])) $optionQty = intval($post['options_'.$optionId.'_qty']); else $optionQty = 1;
                                    break;
                                case 'multiple':
                                    $optionQty = 1;                            
                                    break;                       
                            }                            
                            $optionTotalQty = ($productOption->getCustomoptionsIsOnetime()?$optionQty:$optionQty*$qty);
                            
                            // is null if add new product (edit) (admin) -> correction inventory
                            if (is_null($quoteItem->getId()) && Mage::app()->getStore()->isAdmin()) $customoptionsQty += $optionTotalQty;
                            
                            if (intval($customoptionsQty)<$optionTotalQty) {
                                $productOptionResource = $productOption->getResource();
                                $message = Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', trim($quoteItem->getProduct()->getName() . ' / ' 
                                		. $productOptionResource->getTitle($optionId, $quoteItem->getStoreId()) . ' - '
                                		. $productOptionResource->getValueTitle($optionTypeId, $quoteItem->getStoreId())));
                                
                                $quoteItem->setHasError(true)->setMessage($message);
                                if ($quoteItem->getParentItem()) {
                                    $quoteItem->getParentItem()->setMessage($message);
                                }
                                $quoteItem->getQuote()->setHasError(true)->addMessage($message, 'qty');
                                return $this;
                                break; break;
                            }                            
                        }
                    }
                }    
            }
        }        
        return $this;
    }
        
    
    
    // before create order -> setCustomOptionsDetails
    public function convertQuoteItemToOrderItem($observer) {
        if (!Mage::helper('customoptions')->isEnabled()) return $this;
        $orderItem = $observer->getEvent()->getOrderItem();                
        $item = $observer->getEvent()->getItem();
        $product = $item->getProduct();
        // if bad magento))
        if (is_null($product->getHasOptions())) $product->load($product->getId());
        if (!$product->getHasOptions()) return $this;
        
        // multiplier - to order: 3 x Red
        Mage::helper('customoptions/product_configuration')->setCustomOptionsDetails($item);
        $quoteOptions = $product->getTypeInstance(true)->getOrderOptions($product);
        $orderOptions = $orderItem->getProductOptions();        
        if (!is_array($orderOptions)) return $this;
        
        
        // htmlspecialchars_decode titles
        if (isset($quoteOptions['options']) && is_array($quoteOptions['options'])) {
            foreach ($quoteOptions['options'] as $key=>$op) {
                if (isset($op['label'])) $quoteOptions['options'][$key]['label'] = htmlspecialchars_decode($op['label']);                
                if (isset($op['value'])) $quoteOptions['options'][$key]['value'] = htmlspecialchars_decode($op['value']);
                if (isset($op['print_value'])) unset($quoteOptions['options'][$key]['print_value']);
            }
            $orderOptions['options'] = $quoteOptions['options'];
        }        
        $orderItem->setProductOptions($orderOptions);
        return $this;
    }
    
    public function orderSaveAfter($observer) {
        if (Mage::app()->getRequest()->getControllerName()=='multishipping') $this->quoteSubmitSuccess($observer);
    }
    
    // after create order - reduce inventory + sku policy
    public function quoteSubmitSuccess($observer) {
        $helper = Mage::helper('customoptions');
        
        if (!$helper->isEnabled()) return $this;
        // inventory
        if ($helper->isInventoryEnabled()) {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $tablePrefix = (string) Mage::getConfig()->getTablePrefix();            
            $orderItems = $observer->getEvent()->getOrder()->getAllItems();
            
            foreach ($orderItems as $orderItem) {
               
                // product sku -> reduce option with sku inventory
//                $select = $connection->select()->from($tablePrefix . 'catalog_product_option_type_value', array('option_type_id', 'customoptions_qty'))->where('sku = ?', $orderItem->getSku());
//                $allOptionValues = $connection->fetchAll($select);
//                if ($allOptionValues && count($allOptionValues)>0) {
//                    foreach ($allOptionValues as $opValue) {
//                        if (isset($opValue['option_type_id']) && isset($opValue['customoptions_qty']) && intval($opValue['customoptions_qty'])>0) {
//                            $customoptionsQty = intval($opValue['customoptions_qty'])-intval($orderItem->getQtyOrdered());
//                            if ($customoptionsQty<0) $customoptionsQty = 0;
//                            // model 'catalog/product_option_value' - do not use!
//                            $connection->update($tablePrefix . 'catalog_product_option_type_value', array('customoptions_qty'=>$customoptionsQty), 'option_type_id = '.intval($opValue['option_type_id']));
//                        }    
//                    }
//                }                
                
                // options sku -> reduce product inventory or options inventory
                $productOptions = $orderItem->getProductOptions();            
                if (!isset($productOptions['options'])) continue;

                $qty = $orderItem->getQtyOrdered();
                foreach ($productOptions['options'] as $option) {                
                    switch ($option['option_type']) {
                        case 'drop_down':
                        case 'radio':
                        case 'checkbox':                        
                        case 'multiple':
                        case 'swatch':
                        case 'multiswatch':
                            $optionId = $option['option_id'];
                            $customoptionsIsOnetime = Mage::getModel('catalog/product_option')->load($optionId)->getCustomoptionsIsOnetime();                                                
                            $optionTypeIds = explode(',', $option['option_value']);
                            foreach ($optionTypeIds as $optionTypeId) {                        
                                $productOptionValueModel = Mage::getModel('catalog/product_option_value')->load($optionTypeId);
                                $customoptionsQty = $productOptionValueModel->getCustomoptionsQty();
                                $sku = $productOptionValueModel->getSku();
                                if ($customoptionsQty!=='' || $sku!='') {
                                    if (isset($productOptions['info_buyRequest']['options_'.$optionId.'_qty'])) {
                                        $optionQty = intval($productOptions['info_buyRequest']['options_'.$optionId.'_qty']);
                                    } elseif (isset($productOptions['info_buyRequest']['options_'.$optionId.'_'.$optionTypeId.'_qty'])) {
                                        $optionQty = intval($productOptions['info_buyRequest']['options_'.$optionId.'_'.$optionTypeId.'_qty']);
                                    } else {
                                        $optionQty = 1;
                                    }                            
                                    $optionTotalQty = ($customoptionsIsOnetime?$optionQty:$optionQty*$qty);

                                    if ($customoptionsQty!=='' && substr($customoptionsQty, 0, 1)!='x' && $customoptionsQty>0) {
                                        $customoptionsQty = $customoptionsQty - $optionTotalQty;
                                        if ($customoptionsQty<0) $customoptionsQty = 0;
                                        // model 'catalog/product_option_value' - do not use!
                                        $connection->update($tablePrefix . 'catalog_product_option_type_value', array('customoptions_qty'=>$customoptionsQty), 'option_type_id = '.$optionTypeId);
                                    }    

                                    if ($sku!=='') {
                                        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
                                        if (isset($product) && $product && $product->getId() > 0) {
                                            $item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);                                        
                                            if ($item->getManageStock()) {
                                            //if ($item->getQty() > 0) {
                                                //if ($item->getQty() < $optionTotalQty) $optionTotalQty = intval($item->getQty());
                                                $item->subtractQty($optionTotalQty);
                                                $item->save();
                                            //}
                                            }
                                        }
                                    }    

                                }    
                            }     
                    }    
                }
            }
        }
        
        
        // OptionSkuPolicy
        if ($helper->isOptionSkuPolicyEnabled()) {
            
            $configSkuPolicy = $helper->getOptionSkuPolicyDefault();
            
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
            $order = $observer->getEvent()->getOrder();
            $orderTotalQtyOrdered = $order->getTotalQtyOrdered();
            $orderChangesFlag = false;
            $invoiceChangesFlag = false;
            $orderItems = $order->getAllItems();            
            
            foreach ($orderItems as $orderItem) {
                $orderItemChangesFlag = false;
                $orderItemRemoveFlag = false;                
                $productOptions = $orderItem->getProductOptions();
                if (!isset($productOptions['options'])) continue;
                                                
                $product = Mage::getModel('catalog/product')->setStoreId($orderItem->getStoreId())->load($orderItem->getProductId());
                                
                $productSkuPolicy = $helper->getProductSkuPolicy($product);
                if ($productSkuPolicy==0) $productSkuPolicy = $configSkuPolicy;
                
                $finalProductPrice = $product->getFinalPrice();
                $productTaxClassId = $product->getTaxClassId();
                
                $store = Mage::app()->getStore($orderItem->getStoreId());
                $updateProductOptions = $productOptions;
                
                
                $reduce = array(
                    'weight' => 0,
                    'price' => 0,
                    'base_price' => 0,
                    'total_price' => 0,
                    'base_total_price' => 0,
                    'tax_amount' => 0,
                    'base_tax_amount' => 0
                );
             
                $skuReplacement = array();                
                
                $isInvoiced = false;
                foreach ($productOptions['options'] as $index=>$option) {                    
                    $optionId = $option['option_id'];
                    $productOptionModel = $product->getOptionById($optionId);
                    $customoptionsIsOnetime = $productOptionModel->getCustomoptionsIsOnetime();
                    $skuPolicy = $productOptionModel->getSkuPolicy();
                    // or $productSkuPolicy = Grouped
                    if ($skuPolicy==0 || $productSkuPolicy==3) $skuPolicy = $productSkuPolicy;                    
                    if ($skuPolicy==1) continue;
                    
                    switch ($option['option_type']) {
                        case 'drop_down':
                        case 'radio':
                        case 'checkbox':                        
                        case 'multiple':
                        case 'swatch':
                        case 'multiswatch':
                            $optionTypeIds = explode(',', $option['option_value']);
                            foreach ($optionTypeIds as $optionTypeId) {
                                if (!$optionTypeId) continue;
                                $productOptionValueModel = $productOptionModel->getValueById($optionTypeId);
                                $sku = $productOptionValueModel->getSku();
                                if (!$sku) continue;
                                
                                if ($skuPolicy==2 || $skuPolicy==3) { // Independent, Grouped
                                    list($reduce, $orderTotalQtyOrdered, $isInvoiced) = $this->insertNewOrderItem($sku, $option['option_type'], $orderItem, $productOptionValueModel, $productOptions, $store, $optionId, $optionTypeId, $connection, $tablePrefix, $reduce, $orderTotalQtyOrdered, $customoptionsIsOnetime, $finalProductPrice, $productTaxClassId);
                                    if (isset($updateProductOptions['options'][$index])) unset($updateProductOptions['options'][$index]);
                                    if (isset($updateProductOptions['info_buyRequest']['options'][$optionId])) unset($updateProductOptions['info_buyRequest']['options'][$optionId]);
                                    if ($skuPolicy==3) $orderItemRemoveFlag = true;
                                    $orderChangesFlag = true;                                    
                                } elseif ($skuPolicy==4) { // Replacement
                                    $skuReplacement[] = $sku;
                                }
                                $orderItemChangesFlag = true;
                            }
                            break;
                        default:
                            $sku = $productOptionModel->getSku();
                            if ($sku) {
                                if ($skuPolicy==2 || $skuPolicy==3) { // Independent, Grouped
                                    list($reduce, $orderTotalQtyOrdered, $isInvoiced) = $this->insertNewOrderItem($sku, $option['option_type'], $orderItem, $productOptionModel, $productOptions, $store, $optionId, 0, $connection, $tablePrefix, $reduce, $orderTotalQtyOrdered, $customoptionsIsOnetime, $finalProductPrice, $productTaxClassId);
                                    if (isset($updateProductOptions['options'][$index])) unset($updateProductOptions['options'][$index]);
                                    if (isset($updateProductOptions['info_buyRequest']['options'][$optionId])) unset($updateProductOptions['info_buyRequest']['options'][$optionId]);
                                    if ($skuPolicy==3) $orderItemRemoveFlag = true;
                                    $orderChangesFlag = true;                                    
                                } elseif ($skuPolicy==4) { // Replacement
                                    $skuReplacement[] = $sku;
                                }
                                $orderItemChangesFlag = true;
                            }
                            break;
                    }
                }
                
                if ($isInvoiced) $invoiceChangesFlag = true;
                
                if ($orderItemRemoveFlag) {
                    // remove order_item
                    $connection->delete($tablePrefix . 'sales_flat_order_item', 'item_id = ' . $orderItem->getId());                    
                    if ($isInvoiced) $connection->delete($tablePrefix . 'sales_flat_invoice_item', 'order_item_id = ' . $orderItem->getId());
                    
                    $orderTotalQtyOrdered -= $orderItem->getQtyOrdered();
                } else {
                    // update original order_item
                    
                    if ($orderItemChangesFlag) {
                        $updateItemData = array();
                        
                        // get simple $productSku
                        $productSku = $product->getSku();
                        // get correct configurable sku
                        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                            $childrenItems = $orderItem->getChildrenItems();
                            if ($childrenItems) {
                                foreach ($childrenItems as $childrenItem) {
                                    $productSku = $childrenItem->getSku();
                                }
                            }
                        }
                        // add to $productSku - options sku
                        if (isset($updateProductOptions['info_buyRequest'])) {
                            $_buyRequest = new Varien_Object($updateProductOptions['info_buyRequest']);
                            $product->getTypeInstance(true)->processConfiguration($_buyRequest, $product);
                            $productSku = $product->getTypeInstance(true)->getOptionSku($product, $productSku);
                        }
                        
                        
                        $skuReplacement = implode('-', $skuReplacement);
                        
                        $updateItemData['sku'] = ($skuReplacement ? $skuReplacement : $productSku);
                        $updateItemData['product_options'] = serialize($updateProductOptions);

                        if ($reduce['weight']>0) $updateItemData['weight'] = $orderItem->getWeight() - $reduce['weight'];

                        if ($reduce['price']>0) $updateItemData['price'] = $orderItem->getPrice() - ($reduce['price'] / $orderItem->getQtyOrdered());
                        if ($reduce['base_price']>0) $updateItemData['base_price'] = $orderItem->getBasePrice() - ($reduce['base_price'] / $orderItem->getQtyOrdered());

                        if ($reduce['price']>0) $updateItemData['original_price'] = $orderItem->getOriginalPrice() - ($reduce['price'] / $orderItem->getQtyOrdered());
                        if ($reduce['base_price']>0) $updateItemData['base_original_price'] = $orderItem->getBaseOriginalPrice() - ($reduce['base_price'] / $orderItem->getQtyOrdered());

                        if ($reduce['total_price']>0) $updateItemData['row_total'] = $orderItem->getRowTotal() - $reduce['total_price'];
                        if ($reduce['base_total_price']>0) $updateItemData['base_row_total'] = $orderItem->getBaseRowTotal() - $reduce['base_total_price'];

                        if ($reduce['price']>0) $updateItemData['price_incl_tax'] = $orderItem->getPriceInclTax() - ($reduce['price'] / $orderItem->getQtyOrdered()) - ($reduce['tax_amount']>0 ? $reduce['tax_amount'] / $orderItem->getQtyOrdered():0);
                        if ($reduce['base_price']>0) $updateItemData['base_price_incl_tax'] = $orderItem->getBasePriceInclTax() - ($reduce['base_price'] / $orderItem->getQtyOrdered()) - ($reduce['base_tax_amount']>0 ? $reduce['base_tax_amount'] / $orderItem->getQtyOrdered():0);

                        if ($reduce['total_price']>0) $updateItemData['row_total_incl_tax'] = $orderItem->getRowTotalInclTax() - $reduce['total_price'] - $reduce['tax_amount'];
                        if ($reduce['base_total_price']>0) $updateItemData['base_row_total_incl_tax'] = $orderItem->getBaseRowTotalInclTax() - $reduce['base_total_price'] - $reduce['base_tax_amount'];
                        
                        if ($reduce['tax_amount']>0) $updateItemData['tax_amount'] = $orderItem->getTaxAmount() - $reduce['tax_amount'];
                        if ($reduce['base_tax_amount']>0) $updateItemData['base_tax_amount'] = $orderItem->getBaseTaxAmount() - $reduce['base_tax_amount'];
                                                
                        $connection->update($tablePrefix . 'sales_flat_order_item', $updateItemData, 'item_id = ' . $orderItem->getId());
                        
                        if ($isInvoiced) {
                            $updateItemData = array();
                            $updateItemData['sku'] = ($skuReplacement? $skuReplacement : $productSku);                                                        
                            if ($reduce['price']>0) $updateItemData['price'] = $orderItem->getPrice() - ($reduce['price'] / $orderItem->getQtyOrdered());
                            if ($reduce['base_price']>0) $updateItemData['base_price'] = $orderItem->getBasePrice() - ($reduce['base_price'] / $orderItem->getQtyOrdered());                            
                            if ($reduce['total_price']>0) $updateItemData['row_total'] = $orderItem->getRowTotal() - $reduce['total_price'];
                            if ($reduce['base_total_price']>0) $updateItemData['base_row_total'] = $orderItem->getBaseRowTotal() - $reduce['base_total_price'];
                            if ($reduce['price']>0) $updateItemData['price_incl_tax'] = $orderItem->getPriceInclTax() - ($reduce['price'] / $orderItem->getQtyOrdered()) - ($reduce['tax_amount']>0 ? $reduce['tax_amount'] / $orderItem->getQtyOrdered():0);
                            if ($reduce['base_price']>0) $updateItemData['base_price_incl_tax'] = $orderItem->getBasePriceInclTax() - ($reduce['base_price'] / $orderItem->getQtyOrdered()) - ($reduce['base_tax_amount']>0 ? $reduce['base_tax_amount'] / $orderItem->getQtyOrdered():0);
                            if ($reduce['total_price']>0) $updateItemData['row_total_incl_tax'] = $orderItem->getRowTotalInclTax() - $reduce['total_price'] - $reduce['tax_amount'];
                            if ($reduce['base_total_price']>0) $updateItemData['base_row_total_incl_tax'] = $orderItem->getBaseRowTotalInclTax() - $reduce['base_total_price'] - $reduce['base_tax_amount'];
                            $connection->update($tablePrefix . 'sales_flat_invoice_item', $updateItemData, 'order_item_id = ' . $orderItem->getId());
                        }
                    }
                }
            } 
            //update  sales_flat_order total_qty_ordered
            $orderId = $order->getId();
            if ($invoiceChangesFlag) $connection->update($tablePrefix . 'sales_flat_invoice', array('total_qty'=>$orderTotalQtyOrdered), 'order_id = ' . $orderId);
            if ($orderChangesFlag) {
                $connection->update($tablePrefix . 'sales_flat_order', array('total_qty_ordered'=>$orderTotalQtyOrdered), 'entity_id = ' . $orderId);
                // reload order to correct e-mail send
                $order->unsetData()->load($orderId);
            }
            
        }
        
        return $this;
    }
    
    public function insertNewOrderItem($sku, $optionType, $orderItem, $optionModel, $productOptions, $store, $optionId, $optionTypeId, $connection, $tablePrefix, $reduce, $orderTotalQtyOrdered, $customoptionsIsOnetime, $finalProductPrice, $productTaxClassId) { 
        $helper = Mage::helper('customoptions');
        
        $isInvoiced = false;
        $product = Mage::getModel('catalog/product')->setStoreId($store->getId())->loadByAttribute('sku', $sku);
        // insert new order item
        $itemData = array();
        if ($product && $product->getId() > 0) {
            $productId = $product->getId();
            $productName = $product->getName();
        } else {                                    
            $productId = 0; //$orderItem->getProductId();
            $productName = $optionModel->getTitle();
        }
        //$productName .= ' ' . $orderItem->getName();
        
        $itemData['product_id'] = $productId;
        $itemData['name'] = $productName;
        
        $itemData['order_id'] = $orderItem->getOrderId();
        $itemData['quote_item_id'] = $orderItem->getQuoteItemId();
        $itemData['store_id'] = $orderItem->getStoreId();
        $itemData['created_at'] = $orderItem->getCreatedAt();
        $itemData['updated_at'] = $orderItem->getUpdatedAt();

        $itemData['product_type'] = 'simple';
        $itemData['product_options'] = '';

        $itemData['weight'] = $optionModel->getWeight();
        $reduce['weight'] += floatval($itemData['weight']);

        $itemData['sku'] = $sku;
                
        if (($optionType=='field' || $optionType=='area') && isset($productOptions['info_buyRequest']['options'][$optionId])) {
            $itemData['description'] = $productOptions['info_buyRequest']['options'][$optionId];
        } else {
            $itemData['description'] = $optionModel->getDescription();
        }
        
        $qty = $orderItem->getQtyOrdered();
        if ($qty==$orderItem->getQtyInvoiced()) $isInvoiced = true;
        
        if (isset($productOptions['info_buyRequest']['options_'.$optionId.'_qty'])) {
            $optionQty = intval($productOptions['info_buyRequest']['options_'.$optionId.'_qty']);
        } elseif (isset($productOptions['info_buyRequest']['options_'.$optionId.'_'.$optionTypeId.'_qty'])) {
            $optionQty = intval($productOptions['info_buyRequest']['options_'.$optionId.'_'.$optionTypeId.'_qty']);
        } else {
            $optionQty = 1;
        }
        $optionTotalQty = ($customoptionsIsOnetime?$optionQty:$optionQty*$qty);
        $orderTotalQtyOrdered += $optionTotalQty;
        $itemData['qty_ordered'] = $optionTotalQty;
        if ($isInvoiced) $itemData['qty_invoiced'] = $optionTotalQty;
        $itemData['base_cost'] = 0;
        
        // get price data
        if ($optionModel instanceof Mage_Catalog_Model_Product_Option) {
            list($price, $priceType, $isSkuPrice, $taxClassId, $oldPrice) = $helper->getOptionPriceAndPriceType($optionModel->getPrice(), $optionModel->getPriceType(), $sku, $store);
        } else {
            list($price, $priceType, $isSkuPrice, $taxClassId, $oldPrice) = $helper->getOptionTierPriceAndType($optionModel, $optionTotalQty, $store);
        }
        if (!$isSkuPrice) $taxClassId = $productTaxClassId;
        
        // get basePrice
        $basePrice = (($priceType=='percent' && $price) ? $finalProductPrice * $price / 100 : $price);         
        if ($isSkuPrice && $oldPrice) {
            $basePrice = $oldPrice;
        }
        
        // calculate tax
        if ($basePrice!=0) {
            //$quote = Mage::getSingleton('checkout/cart')->getQuote();
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            
            if (Mage::helper('tax')->priceIncludesTax($store)) {
                $basePriceInclTax = $basePrice;
                $basePrice = $helper->getPriceExcludeTax($basePrice, $quote, $taxClassId);
            } else {    
                $basePriceInclTax = $basePrice + $helper->getTaxPrice($basePrice, $quote, $taxClassId);
            }
        } else {
            $basePriceInclTax = 0;
        }
        
        // convert basePrice - to price
        $price = $store->convertPrice($basePrice, false, false);
        $priceInclTax = $store->convertPrice($basePriceInclTax, false, false);
        
        //$basePrice = (($optionModel->getPriceType()=='percent') ? $finalProductPrice * $optionModel->getPrice() / 100 : $optionModel->getPrice());        
        $itemData['base_price'] = $basePrice;
        $reduce['base_price'] += floatval($itemData['base_price']) * $optionTotalQty;
        
        
        $itemData['price'] = $price;
        $reduce['price'] += floatval($itemData['price']) * $optionTotalQty;

        $itemData['original_price'] = $itemData['price'];
        $itemData['base_original_price'] = $itemData['base_price'];                                                                        

        $itemData['row_total'] = $itemData['price'] * $optionTotalQty;
        $reduce['total_price'] += floatval($itemData['row_total']);
        $itemData['base_row_total'] = $itemData['base_price'] * $optionTotalQty;
        $reduce['base_total_price'] += floatval($itemData['base_row_total']);

        
        $itemData['price_incl_tax'] = $priceInclTax;
        $itemData['base_price_incl_tax'] = $basePriceInclTax;

        $itemData['row_total_incl_tax'] = $itemData['price_incl_tax'] * $optionTotalQty;
        $itemData['base_row_total_incl_tax'] = $itemData['base_price_incl_tax'] * $optionTotalQty;
        
        
        $itemData['tax_percent'] = $helper->getTaxRate($quote, $taxClassId);
        
        $itemData['tax_amount'] = ($priceInclTax - $price) * $optionTotalQty;
        $reduce['tax_amount'] += floatval($itemData['tax_amount']);
        
        $itemData['base_tax_amount'] = ($basePriceInclTax  - $basePrice) * $optionTotalQty;
        $reduce['base_tax_amount'] += floatval($itemData['base_tax_amount']);
        

        //print_r($itemData); exit;
        $connection->insert($tablePrefix . 'sales_flat_order_item', $itemData);
        $orderItemId = $connection->lastInsertId($tablePrefix . 'sales_flat_order_item');
        
        // insert invoice item
        if ($isInvoiced && $orderItemId) {
            $invoice = $orderItem->getOrder()->getInvoiceCollection()->getFirstItem();
            if ($invoice && $invoice->getId()) {            
                $itemInvoiceData = array();            
                $itemInvoiceData['parent_id'] = $invoice->getId();
                
                $itemInvoiceData['price'] = $itemData['price'];
                $itemInvoiceData['base_price'] = $itemData['base_price'];
                
                $itemInvoiceData['row_total'] = $itemData['row_total'];
                $itemInvoiceData['base_row_total'] = $itemData['base_row_total'];
                
                $itemInvoiceData['tax_amount'] = $itemData['tax_amount'];
                $itemInvoiceData['base_tax_amount'] = $itemData['base_tax_amount'];                
                
                $itemInvoiceData['price_incl_tax'] = $itemData['price_incl_tax'];
                $itemInvoiceData['base_price_incl_tax'] = $itemData['base_price_incl_tax'];
                
                $itemInvoiceData['row_total_incl_tax'] = $itemData['row_total_incl_tax'];
                $itemInvoiceData['base_row_total_incl_tax'] = $itemData['base_row_total_incl_tax'];                
                
                $itemInvoiceData['qty'] = $optionTotalQty;
                $itemInvoiceData['product_id'] = $productId;
                $itemInvoiceData['order_item_id'] = $orderItemId;
                $itemInvoiceData['sku'] = $sku;                
                $itemInvoiceData['name'] = $productName;
                $connection->insert($tablePrefix . 'sales_flat_invoice_item', $itemInvoiceData);
            }            
        }
        
        return array($reduce, $orderTotalQtyOrdered, $isInvoiced);
    }
    
    public function cancelOrderItem($observer) {        
        if (!Mage::helper('customoptions')->isInventoryEnabled()) return $this;
        
        $orderItem = $observer->getEvent()->getItem();
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        
        // qty cancel now
        $qty = intval($orderItem->getQtyToCancel());

        // options sku -> increase product inventory or options inventory
        $productOptions = $orderItem->getProductOptions();
        if (!isset($productOptions['options'])) return $this;
        
        
        foreach ($productOptions['options'] as $option) {                
            switch ($option['option_type']) {
                case 'drop_down':
                case 'radio':
                case 'checkbox':                        
                case 'multiple':
                case 'swatch':
                case 'multiswatch':
                    $optionId = $option['option_id'];
                    $customoptionsIsOnetime = Mage::getModel('catalog/product_option')->load($optionId)->getCustomoptionsIsOnetime();
                    $optionTypeIds = explode(',', $option['option_value']);
                    foreach ($optionTypeIds as $optionTypeId) {                    
                        $productOptionValueModel = Mage::getModel('catalog/product_option_value')->load($optionTypeId);
                        $customoptionsQty = $productOptionValueModel->getCustomoptionsQty();
                        $sku = $productOptionValueModel->getSku();
                        if ($customoptionsQty!=='' || $sku!='') {
                            if (isset($productOptions['info_buyRequest']['options_'.$optionId.'_qty'])) {
                                $optionQty = intval($productOptions['info_buyRequest']['options_'.$optionId.'_qty']);
                            } elseif (isset($productOptions['info_buyRequest']['options_'.$optionId.'_'.$optionTypeId.'_qty'])) {
                                $optionQty = intval($productOptions['info_buyRequest']['options_'.$optionId.'_'.$optionTypeId.'_qty']);                            
                            } else {
                                $optionQty = 1;
                            }                                                                        
                            $optionTotalQty = ($customoptionsIsOnetime?$optionQty:$optionQty*$qty);                        
                            
                            if ($customoptionsQty!=='' && substr($customoptionsQty, 0, 1)!='x') {
                                $customoptionsQty = $customoptionsQty + $optionTotalQty;                                
                                // model 'catalog/product_option_value' - do not use!
                                $connection->update($tablePrefix . 'catalog_product_option_type_value', array('customoptions_qty'=>$customoptionsQty), 'option_type_id = '.$optionTypeId);                                
                            }
                            
                            if ($sku!=='') {
                                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
                                if (isset($product) && $product && $product->getId() > 0) {
                                    $item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);                                    
                                    $item->addQty($optionTotalQty);
                                    $item->save();
                                }
                            }
                        }    
                    }    
            }            

        }        
        
        return $this;
        
    }

    public function creditMemoRefund($observer) {
        if (!Mage::helper('customoptions')->isInventoryEnabled()) return $this;

        $orderItems = $observer->getEvent()->getCreditmemo()->getOrder()->getItemsCollection();                
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();             
        $creditmemoData = Mage::app()->getRequest()->getParam('creditmemo');
        
        foreach ($orderItems as $orderItem) {
            // if not ckecked "Return to Stock"
            if (!isset($creditmemoData['items'][$orderItem->getId()]['back_to_stock'])) continue;            
            
            // qty refund now            
            $qty = intval($orderItem->getQtyRefunded()) - intval($orderItem->getOrigData('qty_refunded'));
            
            // product sku -> increase option with sku inventory
//            $select = $connection->select()->from($tablePrefix . 'catalog_product_option_type_value', array('option_type_id', 'customoptions_qty'))->where('sku = ?', $orderItem->getSku());
//            $allOptionValues = $connection->fetchAll($select);
//            if ($allOptionValues && count($allOptionValues)>0) {
//                foreach ($allOptionValues as $opValue) {
//                    if (isset($opValue['option_type_id']) && isset($opValue['customoptions_qty'])) {
//                        $customoptionsQty = intval($opValue['customoptions_qty']) + $qty;
//                        // model 'catalog/product_option_value' - do not use!
//                        $connection->update($tablePrefix . 'catalog_product_option_type_value', array('customoptions_qty'=>$customoptionsQty), 'option_type_id = '.intval($opValue['option_type_id']));
//                    }    
//                }
//            }
            
            // options sku -> increase product inventory and options inventory
            $productOptions = $orderItem->getProductOptions();
            if (!isset($productOptions['options'])) continue;
            
            foreach ($productOptions['options'] as $option) {                
                switch ($option['option_type']) {
                    case 'drop_down':
                    case 'radio':
                    case 'checkbox':                        
                    case 'multiple':
                    case 'swatch':
                    case 'multiswatch':
                        $optionId = $option['option_id'];
                        $customoptionsIsOnetime = Mage::getModel('catalog/product_option')->load($optionId)->getCustomoptionsIsOnetime();                        
                        $optionTypeIds = explode(',', $option['option_value']);
                        foreach ($optionTypeIds as $optionTypeId) {                        
                            $productOptionValueModel = Mage::getModel('catalog/product_option_value')->load($optionTypeId);
                            $customoptionsQty = $productOptionValueModel->getCustomoptionsQty();
                            $sku = $productOptionValueModel->getSku();
                            if ($customoptionsQty!=='' || $sku!='') {
                                if (isset($productOptions['info_buyRequest']['options_'.$optionId.'_qty'])) {
                                    $optionQty = intval($productOptions['info_buyRequest']['options_'.$optionId.'_qty']);
                                } elseif (isset($productOptions['info_buyRequest']['options_'.$optionId.'_'.$optionTypeId.'_qty'])) {
                                    $optionQty = intval($productOptions['info_buyRequest']['options_'.$optionId.'_'.$optionTypeId.'_qty']);    
                                } else {
                                    $optionQty = 1;
                                }                            
                                $optionTotalQty = ($customoptionsIsOnetime?$optionQty:$optionQty*$qty);
                                
                                if ($customoptionsQty!=='' && substr($customoptionsQty, 0, 1)!='x') {
                                    $customoptionsQty = $customoptionsQty + $optionTotalQty;
                                    // model 'catalog/product_option_value' - do not use!
                                    $connection->update($tablePrefix . 'catalog_product_option_type_value', array('customoptions_qty'=>$customoptionsQty), 'option_type_id = '.$optionTypeId);
                                }
                                
                                if ($sku!=='') {
                                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
                                    if (isset($product) && $product && $product->getId() > 0) {
                                        $item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);                                    
                                        $item->addQty($optionTotalQty);
                                        $item->save();
                                    }
                                }                                
                            }    
                        }     
                }    
                    
            }
        }            
        return $this;                                              
    }

    // set weight and sku_police apply to cart
    public function quoteItemSetProduct($observer) {
        $helper = Mage::helper('customoptions');        
        if (!$helper->isEnabled() || (!$helper->isWeightEnabled() && !$helper->isOptionSkuPolicyApplyToCart())) return $this;
        
        $quoteItem = $observer->getEvent()->getQuoteItem();
        
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote()) return $this;
        
        // prepare post data
        $infoBuyRequestValue= $quoteItem->getProduct()->getCustomOption('info_buyRequest')->getValue();
        if ($infoBuyRequestValue) $post = unserialize($infoBuyRequestValue); else $post = array();
        
        if ($helper->isOptionSkuPolicyApplyToCart()) {
            //if (isset($post['sku_policy_name'])) $quoteItem->setName($post['sku_policy_name']);
            if (isset($post['sku_policy_weight'])) $quoteItem->setWeight($post['sku_policy_weight']);
            if (isset($post['sku_policy_sku'])) $quoteItem->setSku($post['sku_policy_sku']);
        }
        
        if (!$helper->isWeightEnabled()) return $this;
        
        if (isset($post['options'])) $options = $post['options']; else $options = false;      
            
        if ($options) {            
            if (Mage::app()->getStore()->isAdmin()) {
                $sessionQuote = Mage::getSingleton('adminhtml/session_quote');
                if ($sessionQuote) $customerGroupId = $sessionQuote->getCustomer()->getGroupId(); else $customerGroupId = 0;        
            } else {
                $customerGroupId = Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getCustomer()->getGroupId() : 0;            
            }            

            $optionsWeight = 0;
            foreach ($options as $optionId => $option) {                     
                $productOption = Mage::getModel('catalog/product_option')->load($optionId);
                
                // check Options Customer Group
                if ($helper->isCustomerGroupsEnabled() && $productOption->getCustomerGroups()!=='' && !in_array($customerGroupId, explode(',', $productOption->getCustomerGroups()))) continue;
                
                // set options weight
                $optionType = $productOption->getType();                    
                if ($productOption->getGroupByType($optionType)!=Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) continue;
                if (!is_array($option)) $option = array($option);
                //product Qty
                $qty = intval($quoteItem->getQty());
                
                
                foreach ($option as $optionTypeId) {
                    if (!$optionTypeId) continue;
                    $row = $productOption->getOptionValue($optionTypeId);
                    if (isset($row['weight']) && $row['weight']>0) {

                        switch ($optionType) {
                            case 'checkbox':
                            case 'multiswatch':
                                if (isset($post['options_'.$optionId.'_'.$optionTypeId.'_qty'])) $optionQty = intval($post['options_'.$optionId.'_'.$optionTypeId.'_qty']); else $optionQty = 1;
                                break;
                            case 'drop_down':
                            case 'radio':
                            case 'swatch':    
                                if (isset($post['options_'.$optionId.'_qty'])) $optionQty = intval($post['options_'.$optionId.'_qty']); else $optionQty = 1;
                                break;
                            case 'multiple':
                                $optionQty = 1;                            
                                break;                       
                        } 
                        
                        // get option weight
                        $weight = floatval($row['weight']);
                        if ($productOption->getCustomoptionsIsOnetime()) $weight = $weight / $qty;
                        $optionsWeight += $weight * $optionQty;                        
                    }
                }
            }
            
            if ($optionsWeight>0) {
                // check absolute weight
                $product = $observer->getEvent()->getProduct();
                if (!$helper->getProductAbsoluteWeight($product)) $optionsWeight += $quoteItem->getWeight();
                // set weight for qty=1
                $quoteItem->setWeight($optionsWeight);
            }
        }
        return $this;
    }
    
    
    
    // isOptionSkuPolicyApplyToCart
    public function quoteProductAddAfter($observer) {
        $helper = Mage::helper('customoptions');
        if (!$helper->isEnabled() || !$helper->isOptionSkuPolicyEnabled() || !$helper->isOptionSkuPolicyApplyToCart()) return $this;
        $configSkuPolicy = $helper->getOptionSkuPolicyDefault();
        
        $items = $observer->getEvent()->getItems();
        foreach ($items as $item) {
            $itemChangesFlag = false;
            $itemRemoveFlag = false;
            
            $product = $item->getProduct();
            
            // if bad magento))
            if (is_null($product->getHasOptions())) $product->load($product->getId());
            if (!$product->getHasOptions()) continue;
            
            $productSkuPolicy = $helper->getProductSkuPolicy($product);
            if ($productSkuPolicy==0) $productSkuPolicy = $configSkuPolicy;
            
            $infoBuyRequestValue= $product->getCustomOption('info_buyRequest')->getValue();
            if ($infoBuyRequestValue) $infoBuyRequestValue = unserialize($infoBuyRequestValue); else $infoBuyRequestValue = array();
            
            if (isset($infoBuyRequestValue['options'])) $options = $infoBuyRequestValue['options']; else $options = false;
            
            if ($options) {
                foreach ($options as $optionId => $value) {                     
                    $productOption = $product->getOptionById($optionId);
                    if (!$productOption) continue;
                    
                    $customoptionsIsOnetime = $productOption->getCustomoptionsIsOnetime();
                    $skuPolicy = $productOption->getSkuPolicy();

                    // or $productSkuPolicy = Grouped
                    if ($skuPolicy==0 || $productSkuPolicy==3) $skuPolicy = $productSkuPolicy; 
                    if ($skuPolicy==1) continue;
                    
                    switch ($productOption->getType()) {
                        case 'drop_down':
                        case 'radio':
                        case 'checkbox':                        
                        case 'multiple':
                        case 'swatch':
                        case 'multiswatch':
                            if (is_array($value)) {
                                $optionTypeIds = $value;
                            } else {
                                $optionTypeIds = explode(',', $value);
                            }
                            
                            foreach ($optionTypeIds as $index=>$optionTypeId) {
                                if (!$optionTypeId) continue;
                                $productOptionValue = $productOption->getValueById($optionTypeId);
                                $sku = $productOptionValue->getSku();
                                if (!$sku) continue;
                                
                                $productIdBySku = $helper->getProductIdBySku($sku);
                                if (!$productIdBySku) continue;
                                
                                
                                if ($skuPolicy==2 || $skuPolicy==3) { // Independent, Grouped
                                    // add new product by $productIdBySku
                                    if (isset($infoBuyRequestValue['options_'.$optionId.'_qty'])) {
                                        $optionQty = intval($infoBuyRequestValue['options_'.$optionId.'_qty']);
                                    } elseif (isset($infoBuyRequestValue['options_'.$optionId.'_'.$optionTypeId.'_qty'])) {
                                        $optionQty = intval($infoBuyRequestValue['options_'.$optionId.'_'.$optionTypeId.'_qty']);
                                    } else {
                                        $optionQty = 1;
                                    }
                                    
                                    
                                    $optionTotalQty = ($customoptionsIsOnetime?$optionQty:$optionQty*$item->getQty());
                                    $request = new Varien_Object();
                                    $request->setQty($optionTotalQty);
                                    
                                    $productOptionResource = $productOption->getResource();
                                    $request->setSkuPolicyName($productOptionResource->getValueTitle($optionTypeId, $item->getStoreId()));
                                    if ($helper->isWeightEnabled()) $request->setSkuPolicyWeight($productOptionValue->getWeight());
                                    
                                    //$item->getQuote() or Mage::getSingleton('checkout/cart')
                                    $result = $item->getQuote()->addProduct(Mage::getModel('catalog/product')->setStoreId($item->getStoreId())->load($productIdBySku), $request);
                                    if (!is_object($result)) continue;
                                    
                                    // remove option or optionValue from item
                                    if (is_array($value)) {
                                        unset($value[$index]);
                                    } else {
                                        $value = '';
                                    }
                                    if ($value) {
                                        // if remove optionValue
                                        $infoBuyRequestValue['options'][$optionId] = $value;
                                        $itemOption = $item->getOptionByCode('option_'.$optionId);
                                        $itemOption->setValue((is_array($value)?implode(',', $value):$value));
                                        $item->addOption($itemOption);
                                    } else {
                                        // if remove option
                                        unset($infoBuyRequestValue['options'][$optionId]);
                                        $item->removeOption('option_'.$optionId);
                                        
                                        $itemOptionIds = $item->getOptionByCode('option_ids');
                                        $optionIds = $itemOptionIds->getValue();
                                        if ($optionIds) {
                                            $optionIds = explode(',', $optionIds);
                                            $i = array_search($optionId, $optionIds);
                                            if ($i!==false) unset($optionIds[$i]);
                                            if ($optionIds) {
                                                $optionIds = implode(',', $optionIds);
                                            }
                                            
                                        }
                                        if ($optionIds) {
                                            $itemOptionIds->setValue($optionIds);
                                            $item->addOption($itemOptionIds);
                                        } else {
                                            $item->removeOption('option_ids');
                                        }
                                    }
                                    $infoBuyRequest = $item->getOptionByCode('info_buyRequest');
                                    $infoBuyRequest->setValue(serialize($infoBuyRequestValue));
                                    $item->addOption($infoBuyRequest);
                                    // end remove option from item
                                    
                                    $itemChangesFlag = true;
                                    if ($skuPolicy==3) $itemRemoveFlag = true;
                                } elseif ($skuPolicy==4) {
                                    $infoBuyRequest = $item->getOptionByCode('info_buyRequest');
                                    $infoBuyRequestValue['sku_policy_sku'] = $sku;
                                    $infoBuyRequest->setValue(serialize($infoBuyRequestValue));
                                    $item->addOption($infoBuyRequest);
                                }
                            }
                            break;
                        default:
                            if (!$value) continue;
                            
                            $sku = $productOption->getSku();
                            if (!$sku) continue;
                            $productIdBySku = $helper->getProductIdBySku($sku);
                            if (!$productIdBySku) continue;
                                
                            if ($skuPolicy==2 || $skuPolicy==3) { // Independent, Grouped
                                // add new product by $productIdBySku
                                $optionTotalQty = ($customoptionsIsOnetime?1:$item->getQty());
                                $request = new Varien_Object();
                                $request->setQty($optionTotalQty);
                                $productOptionResource = $productOption->getResource();
                                $request->setSkuPolicyName($productOptionResource->getTitle($optionId, $item->getStoreId()));
                                
                                //$item->getQuote() or Mage::getSingleton('checkout/cart')
                                $result = $item->getQuote()->addProduct(Mage::getModel('catalog/product')->setStoreId($item->getStoreId())->load($productIdBySku));
                                if (!is_object($result)) continue;
                                
                                // remove option from item
                                unset($infoBuyRequestValue['options'][$optionId]);
                                $item->removeOption('option_'.$optionId);

                                $itemOptionIds = $item->getOptionByCode('option_ids');
                                $optionIds = $itemOptionIds->getValue();
                                if ($optionIds) {
                                    $optionIds = explode(',', $optionIds);
                                    $i = array_search($optionId, $optionIds);
                                    if ($i!==false) unset($optionIds[$i]);
                                    if ($optionIds) {
                                        $optionIds = implode(',', $optionIds);
                                    }

                                }
                                if ($optionIds) {
                                    $itemOptionIds->setValue($optionIds);
                                    $item->addOption($itemOptionIds);
                                } else {
                                    $item->removeOption('option_ids');
                                }
                                $infoBuyRequest = $item->getOptionByCode('info_buyRequest');
                                $infoBuyRequest->setValue(serialize($infoBuyRequestValue));
                                $item->addOption($infoBuyRequest);
                                // end remove option from item
                                
                                $itemChangesFlag = true;
                                if ($skuPolicy==3) $itemRemoveFlag = true;
                            } elseif ($skuPolicy==4) {
                                $infoBuyRequest = $item->getOptionByCode('info_buyRequest');
                                $infoBuyRequestValue['sku_policy_sku'] = $sku;
                                $infoBuyRequest->setValue(serialize($infoBuyRequestValue));
                                $item->addOption($infoBuyRequest);
                            }
                                                       
                            break;
                    }
                }
            }
            if ($itemRemoveFlag) {
                $itemsCollection = $item->getQuote()->getItemsCollection();
                foreach($itemsCollection as $key=>$itm) {
                    if ($itm===$item) $itemsCollection->removeItemByKey($key);
                }
            } elseif ($itemChangesFlag) {
                // update item
                $quote = $item->getQuote();
                $itemsCollection = $quote->getItemsCollection();
                $itemRemoveFlag = false;
                foreach($itemsCollection as $key=>$itm) {
                    if ($itm->getProductId()==$item->getProductId() && $itm!==$item) {
                        
                        // get current $item - $options
                        if (isset($infoBuyRequestValue['options'])) $options = $infoBuyRequestValue['options']; else $options = false;
                        
                        // get other $itm - $optns
                        $prdct = $itm->getProduct();
                        // if bad magento))
                        if (is_null($prdct->getHasOptions())) $prdct->load($prdct->getId());
                        $optns = false;
                        if ($prdct->getHasOptions()) {
                            $infoBuyRequestValue= $prdct->getCustomOption('info_buyRequest')->getValue();
                            if ($infoBuyRequestValue) $infoBuyRequestValue = unserialize($infoBuyRequestValue); else $infoBuyRequestValue = array();
                            if (isset($infoBuyRequestValue['options'])) $optns = $infoBuyRequestValue['options'];
                        }
                        
                        // compare options
                        if ($optns===$options) {
                            $itm->setQty($itm->getQty() + $item->getQty());
                            $itemRemoveFlag = true;
                        }
                        
                    }
                    if ($itemRemoveFlag && $itm===$item) $itemsCollection->removeItemByKey($key);
                }
            }
        }
    }
    
    public function orderItemsLoadBefore($observer) {
        $helper = Mage::helper('customoptions');
        if ($helper->isEnabled() && $helper->isOptionSkuPolicyEnabled()) {
            $observer->getEvent()->getOrderItemCollection()->setOrder('quote_item_id', 'ASC');
        }
    }

}