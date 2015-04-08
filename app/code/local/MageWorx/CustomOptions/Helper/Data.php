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

class MageWorx_CustomOptions_Helper_Data extends Mage_Core_Helper_Abstract {
    const STATUS_VISIBLE = 1;
    const STATUS_HIDDEN = 2;

    const XML_PATH_CUSTOMOPTIONS_ENABLE_QNTY_INPUT = 'mageworx_catalog/customoptions/enable_qnty_input';
    const XML_PATH_CUSTOMOPTIONS_DISPLAY_QTY_FOR_OPTIONS = 'mageworx_catalog/customoptions/display_qty_for_options';
    
    public function isEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/enabled');
    }
    
    public function isDependentEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/dependent_enabled');
    }
    
    public function hideDependentOption() {
        if (!$this->isDependentEnabled()) return false;
        return Mage::getStoreConfig('mageworx_catalog/customoptions/hide_dependent_option');
    }
       
    public function isWeightEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/weight_enabled');
    }    
    
    public function isPricePrefixEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/price_prefix_enabled');
    }
    
    public function isSpecialPriceEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/special_price_enabled');
    }
    
    public function isTierPriceEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/tier_price_enabled');
    }
    
    public function isSkuPriceLinkingEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/option_sku_price_linking_enabled');
    }
        
    public function isOptionSkuPolicyEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/option_sku_policy_enabled');
    }
    
    public function getOptionSkuPolicyDefault() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/option_sku_policy_default');
    }
    
    public function isOptionSkuPolicyApplyToCart() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/option_sku_policy_apply')==2;
    }
    
    public function isInventoryEnabled() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/inventory_enabled');
    }
    
    public function isQntyInputEnabled() {        
        return Mage::getStoreConfig(self::XML_PATH_CUSTOMOPTIONS_ENABLE_QNTY_INPUT);
    }
    
    public function getDefaultOptionQtyLabel() {        
        return Mage::getStoreConfig('mageworx_catalog/customoptions/default_option_qty_label');
    }

    public function canDisplayQtyForOptions() {
        return Mage::getStoreConfig(self::XML_PATH_CUSTOMOPTIONS_DISPLAY_QTY_FOR_OPTIONS);
    }
    
    public function canShowQtyPerOptionInCart() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/show_qty_per_option_in_cart');
    }
    

    public function canHideOutOfStockOptions() {
        return Mage::getStoreConfig('mageworx_catalog/customoptions/hide_out_of_stock_options');
    }
    
    public function getImagesThumbnailsSize() {        
        return intval(Mage::getStoreConfig('mageworx_catalog/customoptions/images_thumbnails_size'));
    }    
    public function isImageModeEnabled() {
        return Mage::getStoreConfigFlag('mageworx_catalog/customoptions/enable_image_mode');  
    }
    public function isImagesAboveOptions() {        
        return Mage::getStoreConfigFlag('mageworx_catalog/customoptions/images_above_options');
    }
    
    public function isDefaultTextEnabled() {
        return Mage::getStoreConfigFlag('mageworx_catalog/customoptions/enable_default_text');  
    }
    
    public function isSpecifyingCssClassEnabled() {
        return Mage::getStoreConfigFlag('mageworx_catalog/customoptions/enable_specifying_css_class');  
    }
    
    public function isCustomerGroupsEnabled() {
        return Mage::getStoreConfigFlag('mageworx_catalog/customoptions/enable_customer_groups');  
    }

    public function getOptionStatusArray() {
        return array(
            self::STATUS_VISIBLE => $this->__('Active'),
            self::STATUS_HIDDEN => $this->__('Disabled'),
        );
    }

    public function getFilter($data) {
        $result = array();
        $filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_StringTrim());

        if ($data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = $this->getFilter($value);
                } else {
                    $result[$key] = $filter->filter($value);
                }
            }
        }
        return $result;
    }

    public function getFiles($path) {
        return @glob($path . "*.*");
    }

    public function isCustomOptionsFile($groupId, $optionId, $valueId = false) {
        return $this->getFiles($this->getCustomOptionsPath($groupId, $optionId, $valueId));
    }

    public function getCustomOptionsPath($groupId, $optionId = false, $valueId = false) {
        return Mage::getBaseDir('media') . DS . 'customoptions' . DS . ($groupId ? $groupId : 'options') . DS . ($optionId ? $optionId . DS : '') . ($valueId ? $valueId . DS : '');
    }    

    public function deleteOptionFile($groupId, $optionId, $valueId = false, $fileName = '') {
        $dir = $this->getCustomOptionsPath($groupId, $optionId, $valueId);
        if ($fileName) {
            if (file_exists($dir . $fileName)) {
                unlink($dir . $fileName);
                $isEmpty = true;
                if (is_dir($dir)) {
                    $objects = scandir($dir);
                    foreach ($objects as $object) {
                        if ($object=='.' || $object == '..') continue;
                        if (filetype($dir . DS . $object) == "dir") {
                            if (file_exists($dir . $object . DS . $fileName)) unlink($dir . $object . DS . $fileName);
                            continue;
                        }
                        $isEmpty = false;
                    }
                }
                // if empty - remove folder
                if ($isEmpty) $this->deleteFolder($dir);
                return true;
            } else {
                return false;
            }
        } else {
            $this->deleteFolder($dir);
        }
    }

    public function getFileName($filePath) {
        $name = '';
        $name = substr(strrchr($filePath, '/'), 1);
        if (!$name) {
            $name = substr(strrchr($filePath, '\\'), 1);
        }
        return $name;
    }

    public function getCheckImgPath($imagePath, $thumbnailsSize = 0) {
        $ext = strtolower(substr($imagePath, -4));
        if ($ext=='.jpg' || $ext=='.gif' || $ext=='.png' || $ext=='jpeg') {
            $path = explode(DS, $imagePath);
            $fileName = array_pop($path);
            $imagePath = implode(DS, $path);
            $path = Mage::getBaseDir('media') . DS . 'customoptions' . DS . $imagePath . DS;
            $filePath = $path . $fileName;
            if (!file_exists($filePath)) return '';            
        } else {        
            if (substr($imagePath, -1, 1)!=DS) $imagePath .= DS;
            $path = Mage::getBaseDir('media') . DS . 'customoptions' . DS . $imagePath;

            $file = @glob($path . "*.*");
            if (!$file) return '';
            $filePath = $file[0];
            $fileName = str_replace($path, '', $filePath);
        }
        
        if ($thumbnailsSize>0) {
            $smallPath = $path . $thumbnailsSize . 'x' . DS;
            $smallFilePath = $smallPath . $fileName;
            if (!file_exists($smallFilePath)) $this->getSmallImageFile($filePath, $smallPath, $fileName);
        }        
        return array($imagePath, $fileName);
    }
    
    public function getImgHtml($imagePath, $optionId = false, $optionTypeId = false, $isArr = false, $thumbnailsSize = 0) {
        if (!$imagePath) return '';
        
        if (!$thumbnailsSize) $thumbnailsSize = $this->getImagesThumbnailsSize();
        $result = $this->getCheckImgPath($imagePath, $thumbnailsSize);
        if (!$result) return '';
        list($imagePath, $fileName) = $result;
        
        $urlImagePath = trim(str_replace(DS, '/', $imagePath), ' /');
        
        $imgUrl = Mage::getBaseUrl('media') . 'customoptions/'. $urlImagePath. '/' . $thumbnailsSize . 'x/' . $fileName;
        $bigImgUrl = Mage::getBaseUrl('media') . 'customoptions/'. $urlImagePath. '/' . $fileName;
        
        if (Mage::app()->getStore()->isAdmin() && Mage::app()->getRequest()->getControllerName()!='sales_order_create') {
            $imgData = array(
                'label' => $this->__('Delete Image'),
                'big_img_url' => $bigImgUrl,
                'url' => $imgUrl,
                'id' => $optionId,
                'select_id' => $optionTypeId,
                'file_name' => $fileName
            ); 
        } else {
            $imgData = array(
                'big_img_url' => $bigImgUrl,                
                'url' => $imgUrl
            );
        }    
        
        if ($isArr) return $imgData;
        
        $template = 'customoptions/option_image.phtml';
        if (Mage::app()->getRequest()->getControllerName()=='sales_order_create') $template = 'customoptions/composite/option_image.phtml';        
                
        return Mage::app()->getLayout()
                ->createBlock('core/template')
                ->setTemplate($template)
                ->addData(array('item' => new Varien_Object($imgData)))
                ->toHtml();
        
    }
    
    public function getSmallImageFile($fileOrig, $smallPath, $newFileName) {
        try {
            $image = new Varien_Image($fileOrig);
            $origHeight = $image->getOriginalHeight();
            $origWidth = $image->getOriginalWidth();

            // settings
            $image->keepAspectRatio(true);
            $image->keepFrame(true);
            $image->keepTransparency(true);
            $image->constrainOnly(false);
            $image->backgroundColor(array(255, 255, 255));
            $image->quality(90);


            $width = null;
            $height = null;
            if (Mage::app()->getStore()->isAdmin()) {
                if ($origHeight > $origWidth) {
                    $height = $this->getImagesThumbnailsSize();
                } else {
                    $width = $this->getImagesThumbnailsSize();
                }
            } else {
                $configWidth = $this->getImagesThumbnailsSize();
                $configHeight = $this->getImagesThumbnailsSize();

                if ($origHeight > $origWidth) {
                    $height = $configHeight;
                } else {
                    $width = $configWidth;
                }
            }


            $image->resize($width, $height);

            $image->constrainOnly(true);
            $image->keepAspectRatio(true);
            $image->keepFrame(false);
            //$image->display();
            $image->save($smallPath, $newFileName);
        } catch (Exception $e) {
        }
    }

    public function getOptionImgView($option) {
        $block = Mage::app()->getLayout()
                ->createBlock('core/template')
                ->setTemplate('customoptions/option_image.phtml')
                ->addData(array('items' => $option))
                ->toHtml();

        return $block;
    }

    public function copyFolder($path, $dest) {
        if (is_dir($path)) {
            @mkdir($dest);
            $objects = scandir($path);
            if (sizeof($objects) > 0) {
                foreach ($objects as $file) {
                    if ($file == "." || $file == "..")
                        continue;
                    // go on
                    if (is_dir($path . DS . $file)) {
                        $this->copyFolder($path . DS . $file, $dest . DS . $file);
                    } else {
                        copy($path . DS . $file, $dest . DS . $file);
                    }
                }
            }
            return true;
        } elseif (is_file($path)) {
            return copy($path, $dest);
        } else {
            return false;
        }
    }

    public function deleteFolder($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . DS . $object) == "dir") {
                        $this->deleteFolder($dir . DS . $object);
                    } else {
                        unlink($dir . DS . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
    
    public function getMaxOptionId() {
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $select = $connection->select()->from($tablePrefix . 'catalog_product_option', 'MAX(`option_id`)');        
        return intval($connection->fetchOne($select));
    }
    
    public function currencyByStore($price, $store, $format=true, $includeContainer=true) {
        if (version_compare(Mage::getVersion(), '1.5.0', '>=')) {
            return Mage::helper('core')->currencyByStore($price, $store, $format, $includeContainer);
        } else {
            return Mage::helper('core')->currency($price, $format, $includeContainer);
        }
    }
    
    public function _formatPrice($product, $store, $price, $flag = true) {
        if ($price==0) return '';
        $taxHelper = Mage::helper('tax');

        $sign = '+';
        if ($price < 0) {
            $sign = '-';
            $price = 0 - $price;
        }

        $priceStr = $sign;
        $_priceInclTax = Mage::helper('tax')->getPrice($product, $price, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($product, $price);
        
        
        if ($taxHelper->displayPriceIncludingTax()) {
            $priceStr .= Mage::helper('core')->currencyByStore($_priceInclTax, $store, true, $flag);
        } elseif ($taxHelper->displayPriceExcludingTax()) {
            $priceStr .= Mage::helper('core')->currencyByStore($_priceExclTax, $store, true, $flag);
        } elseif ($taxHelper->displayBothPrices()) {
            $priceStr .= Mage::helper('core')->currencyByStore($_priceExclTax, $store, true, $flag);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' ('.$sign.Mage::helper('core')
                    ->currencyByStore($_priceInclTax, $store, true, $flag).' '.$this->__('Incl. Tax').')';
            }
        }
        
        if ($flag) {
            $priceStr = '<span class="price-notice">'.$priceStr.'</span>';
        }
        return $priceStr;
    }
    
    public function getFormatedOptionPrice($product, $option, $value = null) {
        
        if ($value) $model = $value; else $model = $option;
        
        if (!Mage::app()->getStore()->isAdmin() && $model->getIsMsrp()) return '';
        
        $store = $product->getStore();
        $isAbsolutePrice = $this->getProductAbsolutePrice($product);
        
        // for apply other TaxClassId
        if ($this->isSkuPriceLinkingEnabled() && $model->getSku() && $model->getTaxClassId()>0) {
            $product = Mage::getModel('catalog/product')->setStoreId($store->getId())->loadByAttribute('sku', $model->getSku());
        }
        
        if ($option->getType()==Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN || $option->getType()==Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) $isFormat = false; else $isFormat = true;
        
        if ($this->isSpecialPriceEnabled() && $model->getSpecialPrice() > 0) {
            $special = true;
            $priceStr = $this->_formatPrice($product, $store, $model->getPrice(true), $isFormat); // old price
            
            // add striked to old price
            if ($isFormat) {
                $priceStr =  '<s>' . $priceStr .  '</s> ';
            } else {
                $priceStr =  '(' . $priceStr .  ') ';
            }
            
            $priceStr .= $this->_formatPrice($product, $store, $model->getSpecialPrice(), $isFormat); // special price            
            
        } else {
            $special = false;
            $price = ($model->getIsSkuPrice() && $model->getSpecialPrice()>0 ? $model->getSpecialPrice() : $model->getPrice(true));
            $priceStr = $this->_formatPrice($product, $store, $price, $isFormat); // standart price
        }
        // remove first plus
        if ($isAbsolutePrice) $priceStr = str_replace('+', '', $priceStr);
        
        if ($special && $model->getSpecialComment()) $priceStr .= ' '. $model->getSpecialComment();
        
        return $priceStr;
    }
    
    // translate and QuoteEscape
    public function __js($str) {
        return $this->jsQuoteEscape(str_replace("\'", "'", $this->__($str)));
    }    
    
    protected $_products = array();    
    public function getProductIdBySku($sku) {
        if ($sku=='') return 0;
        if (isset($this->_products[$sku]['id'])) return $this->_products[$sku]['id'];
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        if ($product) $productId = $product->getId(); else $productId = 0;        
        $this->_products[$sku]['id'] = $productId;
        return $productId;
    }
    
    public function getTaxPrice($price, $quote, $taxClassId, $address=null) {
        if (!$quote) return 0;
        
        // prepare tax calculator
        if (!$address) {
            $address = $quote->getShippingAddress();
            if (!$address) $address = $quote->getBillingAddress();
        }        
        
        $calc = Mage::getSingleton('tax/calculation');
        $store = $quote->getStore();
        $addressTaxRequest = $calc->getRateRequest(
            $address,
            $quote->getBillingAddress(),
            $quote->getCustomerTaxClassId(),
            $store
        );                
        $addressTaxRequest->setProductClassId($taxClassId);                
        $rate = $calc->getRate($addressTaxRequest);
        
        return $calc->calcTaxAmount($price, $rate, false, true);
    }
    
    public function getPriceExcludeTax($price, $quote, $taxClassId, $address=null) {
        if (!$quote || !$taxClassId || !$price) return $price;
        $rate = $this->getTaxRate($quote, $taxClassId, $address);
        return $quote->getStore()->roundPrice($price / ((100 + $rate)/100));
    }
    
    public function getTaxRate($quote, $taxClassId, $address=null) {
        if (!$quote || !$taxClassId) return 0;
        // prepare tax calculator
        if (!$address) {
            $address = $quote->getShippingAddress();
            if (!$address) $address = $quote->getBillingAddress();
        }
        
        $calc = Mage::getSingleton('tax/calculation');
        $store = $quote->getStore();
        $addressTaxRequest = $calc->getRateRequest(
            $address,
            $quote->getBillingAddress(),
            $quote->getCustomerTaxClassId(),
            $store
        );                
        $addressTaxRequest->setProductClassId($taxClassId);                
        $rate = $calc->getRate($addressTaxRequest);
        return $rate;
    }

    public function getActualProductPrice($product, $store) {
        $price = $product->getPrice();
        $specialPrice = $product->getSpecialPrice();        
        if (!is_null($specialPrice) && $specialPrice != false) {
            if (Mage::app()->getLocale()->isStoreDateInInterval($store, $product->getSpecialFromDate(), $product->getSpecialToDate())) {
                $price = min($price, $specialPrice);
            }
        }
        return $price;
    }
    
    // return $price, $priceType, $flag => true - use product price, false = no; $taxClassId, $oldPrice, $isMsrp = true/false
    public function getOptionPriceAndPriceType($price, $priceType, $sku, $store) {
        if ($sku!='' && $this->isSkuPriceLinkingEnabled()) {
            if (isset($this->_products[$sku]['price'])) {
                return array($this->_products[$sku]['price'], 'fixed', true, $this->_products[$sku]['tax_class_id'], $this->_products[$sku]['old_price'], $this->_products[$sku]['msrp']);                
            } else {
                $product = Mage::getModel('catalog/product')->setStoreId($store->getId())->loadByAttribute('sku', $sku);
                if (isset($product) && $product && $product->getId() > 0) {                    
                    $this->_products[$sku]['id'] = $product->getId();
                    $this->_products[$sku]['price'] = $this->getActualProductPrice($product, $store);
                    $this->_products[$sku]['old_price'] = (($this->_products[$sku]['price']!=$product->getPrice()) ? $product->getPrice() : 0);
                    $this->_products[$sku]['tax_class_id'] = $product->getTaxClassId();
                    $catalogHelper = Mage::helper('catalog');
                    $this->_products[$sku]['msrp'] = ((method_exists($catalogHelper, 'canApplyMsrp')) ? $catalogHelper->canApplyMsrp($product) : false);
                    return array($this->_products[$sku]['price'], 'fixed', true, $this->_products[$sku]['tax_class_id'], $this->_products[$sku]['old_price'], $this->_products[$sku]['msrp']);  
                } else {
                    $this->_products[$sku]['id'] = 0;
                }
            }
        }        
        return array($price, $priceType, false, 0, 0, false);
    }
    
    public function getCustomoptionsQty($customoptionsQty, $sku, $optionId=0, $optionTypeId=0, $quoteItemId=0, $quote=null, $returnWithBackorders=false) {        
        $backorders = false;
        if (substr($customoptionsQty, 0, 1)!='x') {
            if ($sku!='') {
                if (isset($this->_products[$sku]['qty'])) {
                    $customoptionsQty = $this->_products[$sku]['qty'];
                    $backorders = $this->_products[$sku]['backorders'];
                } elseif (!isset($this->_products[$sku]['id']) || $this->_products[$sku]['id']>0) { 
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);                
                    if (isset($product) && $product && $product->getId() > 0) {                    
                        $this->_products[$sku]['id'] = $product->getId();
                        $customoptionsQty = '0';
                        // check product status!='disabled'
                        if ($product->getStatus()!=2) {
                            $item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);                            
                            if ($item) {
                                if ($item->getManageStock()) {
                                    if ($item->getIsInStock()) {
                                        $customoptionsQty = strval(intval($item->getQty()));
                                        $backorders = $item->getBackorders();
                                    }
                                } else {
                                    $customoptionsQty = '';
                                }
                            }
                        }
                        $this->_products[$sku]['qty'] = $customoptionsQty;
                        $this->_products[$sku]['backorders'] = $backorders;
                    } else {
                        $this->_products[$sku]['id'] = 0;
                    }
                }
            }
            // check already added options in cart
            if ($optionId>0 && $optionTypeId>0 && ($customoptionsQty!=='' && $customoptionsQty!=='0')) {
                $opTotalQty = 0;
                if (is_null($quote)) {
                    if (Mage::app()->getStore()->isAdmin()) {
                        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
                    } else {
                        //$quote = Mage::getSingleton('checkout/cart')->getQuote();
                        $quote = Mage::getSingleton('checkout/session')->getQuote();
                    }
                }
                $allItems = $quote->getAllItems();
                foreach ($allItems as $item) {                                        
                    if (is_null($item->getId())) continue;
                    if ($quoteItemId>0 && $item->getId()==$quoteItemId) continue;
                    
                    // get correct $item qty
                    $qty = 0;
                    $cartPost = Mage::app()->getRequest()->getParam('cart', false);
                    if ($cartPost && isset($cartPost[$item->getId()]['qty'])) $qty = $cartPost[$item->getId()]['qty'];
                    if (!$qty) $qty = $item->getQty();
                    
                    // if is options sku
                    if ($item->getSku()==$sku) $opTotalQty += $qty;
                    
                    $options = false;                   
                    $post = $item->getProduct()->getCustomOption('info_buyRequest')->getValue();
                    if ($post) $post = unserialize($post); else $post = array();
                    if (isset($post['options'])) $options = $post['options'];
                    if ($options) {
                        foreach ($options as $opId => $option) {
                            if (!$opId || ($opId!=$optionId && (!$sku || $this->_products[$sku]['id']==0))) continue;                            
                            $productOption = Mage::getSingleton('catalog/product_option')->load($opId);
                            // check Options Inventory
                            $opType = $productOption->getType();                    
                            if ($opType!='drop_down' && $opType!='multiple' && $opType!='radio' && $opType!='checkbox') continue;
                            if (!is_array($option)) $option = array($option);
                            foreach ($option as $opTypeId) {
                                if (!$opTypeId) continue;
                                if ($sku && $this->_products[$sku]['id']>0) {
                                    $row = $productOption->getOptionValue($opTypeId);
                                    if (!isset($row['sku']) || !$row['sku'] || $row['sku']!=$sku) continue;
                                } elseif ($opTypeId!=$optionTypeId) {
                                    continue;
                                }
                                switch ($opType) {
                                    case 'checkbox':
                                        if (isset($post['options_'.$opId.'_'.$opTypeId.'_qty'])) $opQty = intval($post['options_'.$opId.'_'.$opTypeId.'_qty']); else $opQty = 1;
                                        break;
                                    case 'drop_down':
                                    case 'radio':
                                        if (isset($post['options_'.$opId.'_qty'])) $opQty = intval($post['options_'.$opId.'_qty']); else $opQty = 1;
                                        break;
                                    case 'multiple':
                                        $opQty = 1;
                                        break;
                                }
                                $opTotalQty += ($productOption->getCustomoptionsIsOnetime()?$opQty:$opQty*$qty);
                            }
                        }
                    }                    
                }
                
                // correction option inventory
                $customoptionsQty -= $opTotalQty;                
            }
            
            if (!Mage::app()->getStore()->isAdmin() || (Mage::app()->getRequest()->getControllerName()!='catalog_product' && Mage::app()->getRequest()->getControllerName()!='customoptions_options')) {
                if ($customoptionsQty==='0' || $customoptionsQty<0) $customoptionsQty = 0;
                if ($backorders && $customoptionsQty===0) $customoptionsQty = '';
            }
            
        }
        if ($returnWithBackorders) return array($customoptionsQty, $backorders);
        return $customoptionsQty;
    }
    
    public function getProductAbsolutePrice($product) {
        $flag = $product->getAbsolutePrice();
        if (!is_null($flag)) return $flag;
        $productId = $product->getId();
        if (!$productId) return false;
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $select = $connection->select()->from($tablePrefix . 'catalog_product_entity', array('absolute_price'))->where('entity_id = ' . $productId);
        return $connection->fetchOne($select);
    }
    
    public function getProductAbsoluteWeight($product) {
        $flag = $product->getAbsoluteWeight();
        if (!is_null($flag)) return $flag;
        $productId = $product->getId();
        if (!$productId) return false;
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $select = $connection->select()->from($tablePrefix . 'catalog_product_entity', array('absolute_weight'))->where('entity_id = ' . $productId);
        return $connection->fetchOne($select);
    }
    
    public function getProductSkuPolicy($product) {
        $flag = $product->getSkuPolicy();
        if (!is_null($flag)) return $flag;
        $productId = $product->getId();
        if (!$productId) return false;
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $select = $connection->select()->from($tablePrefix . 'catalog_product_entity', array('sku_policy'))->where('entity_id = ' . $productId);
        return $connection->fetchOne($select);
    }
        
    public function getOptionTierPriceAndType($optionValue, $optionQty, $store) { 
        $isSkuPrice = false;
        $taxClassId = 0;
        $isMsrp = false;
        
        if ($this->isSpecialPriceEnabled() && $optionValue->getSpecialPrice()>0) {
            $optionPrice = $optionValue->getSpecialPrice();
            $optionPriceType = $optionValue->getPriceType();
        } else {
            list($optionPrice, $optionPriceType, $isSkuPrice, $taxClassId, $oldPrice, $isMsrp) = $this->getOptionPriceAndPriceType($optionValue->getPrice(), $optionValue->getPriceType(), $optionValue->getSku(), $store);
        }
        if ($this->isTierPriceEnabled()) {
            $tiers = $optionValue->getTiers();
            if ($tiers && is_array($tiers)) {
                foreach ($tiers as $tierQty=>$tierValue) {
                    if ($optionQty>=$tierQty) {
                        $optionPrice = $tierValue['price'];
                        $optionPriceType = $tierValue['price_type'];
                        if ($optionValue->getPriceType()=='fixed' && $optionPriceType=='percent' && $optionPrice>0) {
                            $optionPrice = $optionValue->getPrice(true)*($optionPrice/100);
                            $optionPriceType = 'fixed';
                        }
                    }
                }
            }
        }
        return array($optionPrice, $optionPriceType, $isSkuPrice, $taxClassId, $oldPrice, $isMsrp);
    }
    
    public function getOptionsJsonConfig($options) {
        $config = array();
        foreach ($options as $option) {
            /* @var $option Mage_Catalog_Model_Product_Option */
            if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                foreach ($option->getValues() as $value) {
                    $config[$option->getId()][$value->getId()] = $this->_getOptionConfiguration($option, $value);                    
                }
            } else {
                $config[$option->getId()] = $this->_getOptionConfiguration($option);
            }
            
            $config[$option->getId()]['is_onetime'] = $option->getCustomoptionsIsOnetime();
            $config[$option->getId()]['image_mode'] = ($this->isImageModeEnabled()?$option->getImageMode():1);
            $config[$option->getId()]['exclude_first_image'] = $option->getExcludeFirstImage();
        }
        return Mage::helper('core')->jsonEncode($config);
    }
    public function _getOptionConfiguration($option, $value = null) {
        $data = array();        
        // price
        if ($value) {
            if ($value->getIsMsrp()) {
                $data['price'] = 0;
            } elseif ($value->getSpecialPrice()>0) {
                $data['price'] = Mage::helper('core')->currency($value->getSpecialPrice(), false, false);
                $data['old_price'] = Mage::helper('core')->currency($value->getPrice(), false, false);
            } else {
                $data['price'] = Mage::helper('core')->currency($value->getPrice(), false, false);
            }
            $data['price_type'] = $value->getPriceType();
            if ($this->isTierPriceEnabled()) {
                $tierPrices = $this->_getTierPrices($value);
                if (count($tierPrices)>0) $data['tier_prices'] = $tierPrices;
            }
            if ($value->getTaxClassId()>0) $data['tax'] = $this->_getTaxPercentForCatalog($value->getTaxClassId());
        } else {
            if ($option->getIsMsrp()) {
                $data['price'] = 0;
            } elseif ($option->getSpecialPrice()>0) {
                $data['price'] = Mage::helper('core')->currency($option->getSpecialPrice(), false, false);
                $data['old_price'] = Mage::helper('core')->currency($option->getPrice(), false, false);
            } else {
                $data['price'] = Mage::helper('core')->currency($option->getPrice(), false, false);
            }
            $data['price_type'] = $option->getPriceType();
            if ($option->getTaxClassId()>0) $data['tax'] = $this->_getTaxPercentForCatalog($option->getTaxClassId());
        }
        
        // images
        if ($value && (($option->getType()==Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN || $option->getType()==Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) || $option->getImageMode()>1)) {
            $images = $value->getImages();
            if ($images) {
                foreach($images as $image) {
                    if ($image['source']==1) { // file
                        $arr = $this->getImgHtml($image['image_file'], false, false, true);
                        if (isset($arr['big_img_url'])) $data['images'][] = array($arr['url'], $arr['big_img_url']);
                    } elseif ($image['source']==2) { // color
                        $data['images'][] = array($image['image_file'], false);
                    }
                }
            }
        }
        
        // value title
        if ($value) $data['title'] = $value->getTitle();
        
        return $data;
    }
    
    public function _getTierPrices($value) {
        $tiers = $value->getTiers();
        $tiersArr = array();
        if ($tiers && count($tiers)>0) {
            if ($value->getPriceType()=='fixed') {
                $basePrice = $value->getPrice(true);
            } else {
                $basePrice = $value->getProduct()->getFinalPrice();
            }
            
            $tiersJsArr = array();
            foreach ($tiers as $qty=>$val) {
                if ($val['price_type']== 'percent') {
                    $price = $basePrice*($val['price']/100);
                } else {
                    $price = $val['price'];
                }
                $price = number_format($price, 2, null, '');
                $tiersArr[$qty] = $price;
            }
        }
        return $tiersArr;
    }
    
    public function _getTaxPercentForCatalog($taxClassId) {
        $request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
        return $percent;
    }
    
    
    
    private $_categories = array();
    public function getCategories() {
        if (count($this->_categories)) {
            return $this->_categories;
        }
        foreach (Mage::app()->getWebsites() as $website) {
            $defaultStore = $website->getDefaultStore();
        	if ($defaultStore) {
            	$rootId = $defaultStore->getRootCategoryId();
            	$rootCat = Mage::getModel('catalog/category')->load($rootId);
            	$this->_categories[$rootId] = $rootCat->getName();
            	$this->getChildCats($rootCat, 0);
            }
        }
        return $this->_categories;
    }
    
    public function getChildCats($cat, $level) {
        if ($children = $cat->getChildren()) {
            $level++;
            $children = explode(',', $children);
            foreach ($children as $childId) {
                $childCat = Mage::getModel('catalog/category')->load($childId);
                $this->_categories[$childId] = str_repeat('&nbsp;&nbsp;&nbsp;', $level) . $childCat->getName();
                if ($childCat->getChildren()) {
                    $this->getChildCats($childCat, $level);
                }
            }
        }
    }
    
}