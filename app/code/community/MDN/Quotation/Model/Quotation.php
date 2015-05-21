<?php

class MDN_Quotation_Model_Quotation extends Mage_Core_Model_Abstract {

    protected $_CUSTOMER_ID_FIELD_NAME = 'customer_id';
    protected $_customer = false;
    protected $_items = false;

    //Quotation statuses
    const STATUS_NEW = 'new';
    const STATUS_CUSTOMER_REQUEST = 'customer_request';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';

    // constants for commercial status
    const QUOTE_STATUS_NEW = 'New';
    const QUOTE_STATUS_SENT = 'Sent';
    const QUOTE_STATUS_REMINDED = 'Reminded';
    const QUOTE_STATUS_BOUGHT = 'Bought';
    const QUOTE_STATUS_CANCELED = 'Canceled';



    /**
     * XML configuration paths
     */
    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/order/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/order/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/order/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/order/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/order/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/order/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/order_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/order_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/order_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/order_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/order_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/order_comment/enabled';

    /**
     * Return commerciale statuses
     * @return <type>
     */
    public function getBoughtStatusValues() {

        $retour = array(
            '0' => Mage::Helper('quotation')->__(self::QUOTE_STATUS_NEW),
            '1' => Mage::Helper('quotation')->__(self::QUOTE_STATUS_BOUGHT),
            '2' => Mage::helper('quotation')->__(self::QUOTE_STATUS_SENT),
            '3' => Mage::Helper('quotation')->__(self::QUOTE_STATUS_REMINDED),
            '4' => Mage::Helper('quotation')->__(self::QUOTE_STATUS_CANCELED)
        );

        return $retour;
    }

    /**
     * Constructor
     */
    public function _construct() {
        parent::_construct();
        $this->_init('Quotation/Quotation');
    }

    /**
     * Load quote for customer
     */
    public function loadByCustomer($customerId, $showInvisible = false) {
        if ($showInvisible)
            $collection = $this->getCollection()->addFilter('customer_id', $customerId);
        else
            $collection = $this->getCollection()
                            ->addFilter('customer_id', $customerId)
                            ->addFieldToFilter('status', array('neq' => MDN_Quotation_Model_Quotation::STATUS_NEW));
        return $collection;
    }

    /**
     * Return current customer
     */
    public function getCustomer() {
        if (!$this->_customer) {
            $CustomerId = $this->getcustomer_id();
            $this->_customer = Mage::getModel('customer/customer')->load($CustomerId);
        }
        return $this->_customer;
    }

    /**
     * Return customer website
     */
    public function getWebsiteId() {
        $value = 0;
        if ($this->getCustomer())
            $value = $this->getCustomer()->getWebsiteId();

        if ($value == 0)
            $value = 1;

        return $value;
    }

    /**
     * Return store id
     */
    public function getStoreId() {
        $value = 0;

        //get store id from customer
        if ($this->getCustomer())
            $value = $this->getCustomer()->getStoreId();

        //if store id not set, get the first store for website
        if ($value == 0) {
            $websiteId = $this->getWebsiteId();
            $website = Mage::getModel('core/website')->load($websiteId);
            if ($website->getStoresCount() > 0) {
                foreach ($website->getStoreIds() as $storeId) {
                    $value = $storeId;
                    break;
                }
            }
        }

        //prevent crash with admin website
        if ($value == 0)
            $value = 1;

        return $value;
    }

    /**
     * calculate and save price
     */
    public function CalculateWeight() {
        $weight = 0;
        $collection = $this->getItems();
        foreach ($collection as $item) {
            if ($item->getexclude() == 0)
                $weight += $item->getweight() * $item->getqty();
        }

        //save
        $this->setweight($weight);
    }

    /**
     * Return true if current quote is valid
     */
    public function IsValid() {
        if ($this->getStatus() == self::STATUS_EXPIRED)
                return false;

        $date_limite = date_create($this->getvalid_end_time());
        $now = date_create();
        return ($date_limite->format('Ymd') >= $now->format('Ymd'));
    }

    /**
     * Create bundle for quote
     */
    public function commit() {

        $model = Mage::getModel('Quotation/Quotation_Bundle');
        $bundle = $model->createBundle($this);

        $model = Mage::getModel('Quotation/Quotation_Promotion');
        $promotion = $model->createPromotion($this);

        return $bundle;
    }

    /**
     * Send email to cuystomer
     */
    public function NotifyCustomer() {
        return Mage::getModel('Quotation/Quotation_Notification')->NotifyCustomer($this);
    }
	
	/**
     * email preview 
     */
    public function previewEmail() {
		return Mage::getModel('Quotation/Quotation_Notification')->cPreviewEmail($this);
    }

    /**
     * Return custom address
     */
    public function GetCustomerAddress() {
        $address = $this->getCustomer()->getPrimaryBillingAddress();
        return $address;
    }

    /**
     * Return bundle product id
     */
    public function GetLinkedProduct() {
        if ($this->getproduct_id()) {
            $product = Mage::getModel('catalog/product')->load($this->getproduct_id());
            if ($product->getId() != $this->getproduct_id())
                $product = null;
        }
        else
            $product = null;
        return $product;
    }

    /**
     * Return true if customer has quote
     */
    public function CustomerHasQuotation($CustomerId) {
        if ($this->loadByCustomer($CustomerId)->getSize() > 0)
            return true;
        else
            return false;
    }

    /**
     * Return quote promotion
     */
    public function GetPromo() {
        $promo = null;
        if ($this->getpromo_id() > 0) {
            $promo = Mage::getModel('quotationrule/rule')->load($this->getpromo_id());
        }
        return $promo;
    }

    /**
     * Get quote weight including excluded products
     *
     */
    public function getTotalWeightIncludingExcludedProducts() {
        $retour = $this->getweight();
        foreach ($this->getItems() as $item) {
            if ($item->getexclude() == 1) {
                $retour += $item->getweight();
            }
        }

        return $retour;
    }

    /**
     * Generate increment id for quote
     *
     */
    //public function generateIncrementId() {
    //    $prefix = date('Y') . date('m');
    //
    //    //get last quote with same prefix
    //    $previous = $this->getCollection()
    //                    ->addFieldToFilter('increment_id', array('like' => $prefix . '%'))
    //                    ->setOrder('increment_id', 'desc')
    //                    ->getFirstItem()
    //    ;
    //
    //    $sequence = 1;
    //    if ($previous->getincrement_id()) {
    //        $sequence = (int) str_replace($prefix, '', $previous->getincrement_id());
    //        $sequence++;
    //    }
    //
    //    $prefix .= sprintf('%04d', $sequence);
    //
    //    return $prefix;
    //}
    
     public function generateIncrementId() {


         $prefix = date('Y') . date('m');

        //get last quote with same prefix
        $previous = $this->getCollection()
                        ->addFieldToFilter('increment_id', array('like' => $prefix . '%'))
                        ->setOrder('increment_id', 'desc')
                        ->getFirstItem()
        			;

        $sequence = 1;
        if ($previous->getincrement_id()) {
            $sequence = (int) str_replace($prefix, '', $previous->getincrement_id());
            $sequence++;
        }

        $prefix .= sprintf('%04d', $sequence);
        
		$ref = Mage::helper('core/http')->getHttpReferer();
		$url_params = explode('/',$ref);
		
		foreach($url_params as $key=>$param){
			  if($param == 'customer_id'){
				  $url_key=$key+1;				  
				  $customer['customer_id'] = $url_params[$url_key];
				  }
			}
		$customer_id = $customer['customer_id'];		
		$cr_customer = Mage::getModel('customer/customer')->load($customer_id);			
		$store_id = $cr_customer->getStore_id();
		//$store_id = Mage::App()->getStore()->getStore_id();		
		
	    $strlen = strlen($store_id);		
		$prefix = mt_rand(200000000,100000000000);// 06_01_2014
		$prefix = substr($prefix, $strlen);		
		$prefix = $store_id.$prefix;
        $prefix = $this->checkid($prefix);  
       	
        return $prefix;
    }

    public function checkid($incrementid)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($incrementid);
        if($order->getId() != '')
        {
            $incrementid++;
            $incrementid = $this->checkid($incrementid);
            
            return $incrementid;
        }
        else
        {
            return $incrementid;
        }
        
        
    }

 /**
     *
     */
    public function hasBusinessProposal() {

        $quote = $this->getCollection()
                        ->addFieldToFilter('quotation_id', $this->getId())
                        ->getFirstItem();
        try
        {
            $xml = '';
            $proposal = $this->getbusiness_proposal();
            $xml = new DomDocument();
            $xml->loadXML($proposal);

            return ($proposal != null && $proposal != '' && $xml->getElementsByTagName(MDN_Quotation_Helper_Proposal::kSectionNode)->item(0));
        }
        catch(Exception $ex)
        {
            //should not happen as xml is generated from code
            Mage::logException($ex);
            return false;
        }

    }

    /**
     * Return quote history
     */
    public function getHistory() {
        return Mage::getResourceModel('Quotation/History_collection')
                ->addFieldToFilter('qh_quotation_id', $this->getId());
    }

    /**
     * Add history
     */
    public function addHistory($message) {

        //set username
        $username = 'customer';
        if (Mage::getSingleton('admin/session')->getUser())
            $username = Mage::getSingleton('admin/session')->getUser()->getusername();

        $model = Mage::getModel('Quotation/History');
        $model->setqh_quotation_id($this->getId())
                ->setqh_message($message)
                ->setqh_date(date('Y-m-d', Mage::getModel('core/date')->timestamp()))
                ->setqh_user($username)
                ->save();
    }

    /**
     * Delete quote (and associate objects
     */
    public function delete() {
        //delete products
        foreach ($this->getItems() as $item) {
            $item->delete();
        }

        //delete history
        foreach ($this->getHistory() as $history) {
            $history->delete();
        }

        //delete associated objects
        Mage::getModel('Quotation/Quotation_Bundle')->deleteBundle($this);
        Mage::getModel('Quotation/Quotation_Promotion')->deletePromotion($this);

        //delete History
        foreach($this->getHistory() as $history)
        {
            $history->delete();
        }

        parent::delete();
        return $this;
    }


    /**
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * *************************************** PRODUCTS *************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     */

    /**
     * Add fake product to quote
     */
    public function addFakeProduct($name, $qty, $price, $weight) {
        $item = Mage::getModel('Quotation/Quotationitem')
                        ->setquotation_id($this->getid())
                        ->setorder($this->getNextProductPosition())
                        ->setproduct_id(null)
                        ->setqty($qty)
                        ->setprice_ht($price)
                        ->setcaption($name)
                        ->setweight($weight)
                        ->setexclude(0)
                        ->setsku('')
                        ->setcost(0)
                        ->save();

        $this->resetItems();

        return $item;
    }

    /**
     * Check special price
     *
     * @param <type> $product
     * @return float
     */
    public function checkSpecialPrice($product){

        $price = $product->getprice();
        if ($product->getspecial_price() != '') {
            $applySpecialPrice = true;
            if ($product->getspecial_from_date() != '') {
                $fromTimeStamp = strtotime($product->getspecial_from_date());
                if ($fromTimeStamp > time())
                    $applySpecialPrice = false;
            }
            if ($product->getspecial_to_date() != '') {
                $toTimeStamp = strtotime($product->getspecial_to_date());
                if ($toTimeStamp < time())
                    $applySpecialPrice = false;
            }
            if ($applySpecialPrice)
                $price = $product->getspecial_price();
        }

        return $price;

    }

    /**
     * Add a product to quote
     */
    public function addProduct($productId, $qty) {

        $product = Mage::getModel('catalog/product')->setStoreId($this->getStoreId())->load($productId);
        
        /******************* Start for bundle product **************************/
        $type = '';
        if($product->getTypeId() == 'bundle')
        {
            $type = $product->getTypeId();
        }
        /******************* End for bundle product **************************/
        
        $price = $this->checkSpecialPrice($product);
        $item = Mage::getModel('Quotation/Quotationitem')
                        ->setquotation_id($this->getid())
                        ->setorder($this->getNextProductPosition())
                        ->setproduct_id($productId)
                        ->setproduct_type($type)// 22-01-2014 for bundle product
                        ->setqty($qty)
                        ->setcaption($product->getName())
                        ->setweight($product->getWeight())
                        ->setexclude(0)
                        ->setsku($product->getSku())
                        ->setcost($product->getCost())
                        ->setprice_ht($this->GetTierPriceProduct($product, $price))
                        ->save();

        $this->resetItems();

        return $item;
    }

    /**
     * Return products
     */
    public function getItems() {
        if (!$this->_items) {
            $this->_items = Mage::getModel('Quotation/Quotationitem')
                            ->getCollection()
                            ->addFilter('quotation_id', $this->getquotation_id())
                            ->setOrder('`order`', 'ASC');

            //affect quote (to avoid lazy loading)
            foreach ($this->_items as $item) {
                $item->setQuote($this);
            }
        }

        return $this->_items;
    }

    /**
     * Return products are array
     */
    public function getItemsArray() {

        $items = $this->getItems();
        $products_array = array();
        foreach ($items as $item) {
            $products_array[] = $item;
        }

        return $products_array;
    }

    /**
     * Reset lazy loaded items collection
     */
    public function resetItems() {
        $this->_items = null;
    }

    /**
     * Get products ids
     *
     */
    public function GetItemsIds() {
        $retour = array();
        $retour[] = -1;
        foreach ($this->getItems() as $item) {
            $retour[] = (int) $item->getproduct_id();
        }
        return $retour;
    }

    /**
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * *************************************** PRICE METHODS *************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     */

    /**
     * Get total cost
     *
     */
    public function getTotalCost($quote='') {
        $collection = $this->getItems();
        $cost_sum = 0;
        foreach ($collection as $item) {
            if ($item->getexclude() != "1") {
                $product = Mage::getModel('catalog/product')->load($item->getproduct_id());
                $cost_sum += ( $product->getcost() * $item->getqty());
            }
        }
        return $cost_sum;
    }

    /**
     * Return margin
     *
     */
    public function getMargin() {
        return ($this->getprice_ht() - $this->getTotalCost());
    }

    /**
     * Return margin (percent)
     *
     */
    public function getMarginPercent() {
        if ($this->getprice_ht() > 0)
            return ($this->getprice_ht() - $this->getTotalCost()) / $this->getprice_ht() * 100;
        else
            return 0;
    }

    /**
     * Format price
     */
    public function FormatPrice($price) {
        return Mage::app()->getStore()->convertPrice($price, true);
    }

    /**
     * Format discount
     */
    public function FormatDiscount($discount) {
        return $discount . ' %';
    }

    /**
     * Calculate and store quote price
     */
    public function CalculateQuotationPriceHt($quote='') {
        $price = 0;
        $collection = $this->getItems();
        foreach ($collection as $item) {
            if ($item->getexclude() == 0)
                $price += $item->getPriceIncludingDiscount() * $item->getqty();
        }

        //discount
        $price = $price * (1 - $this->getreduction() / 100);

        //save
        $this->setprice_ht($price);
    }

    /**
     * Retourne le prix final (prix config + produits exclus)
     *
     */
    public function GetFinalPriceWithTaxes($quote='') {
        //get price if qoute type is invoice / order then return its prices
		 
		
		//get config price (non exclude products)
        	$value_with_taxes = 0;
        if ($this->GetLinkedProduct() != null)
            $value_with_taxes = $this->GetProductPriceWithTaxes($this->GetLinkedProduct());
        else {
            //if bundle product not created
            $storeId = $this->getCustomer()->getStoreId();
            $TaxId = Mage::getStoreConfig('quotation/general/quotation_tax_class');
            $customPriceForBundle = $this->getprice_ht();
            $fakeProductForTaxCalculation = Mage::getModel('catalog/product')
                            ->setPrice($customPriceForBundle)
                            ->setattribute_set_id(Mage::getStoreConfig('quotation/general/attribute_set_id', $storeId))
                            ->settype_id('simple')
                            ->setPrice($this->getprice_ht())
                            ->setWebsiteIds(array($this->getCustomer()->getWebsiteId()))   //Main web site
                            ->settax_class_id($TaxId);
            $value_with_taxes = $this->GetProductPriceWithTaxes($fakeProductForTaxCalculation);
        }

        //add exclude products
        foreach ($this->getItems() as $item) {
            if ($item->getexclude() == 1) {
                $excludeProductTotalPriceWithTaxes = $item->GetTotalPriceWithTaxes($this);
                $value_with_taxes += $excludeProductTotalPriceWithTaxes;
            }
        }

        //add shipping fees
        $value_with_taxes += $this->getShippingCostWithTax();

        return number_format($value_with_taxes, 2, '.', '');
    }

    /**
     * Return final price (bundle + excluded products
     *
     */
    public function GetFinalPriceWithoutTaxes($quote='') {
        $value = $this->getprice_ht();
        if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1) {
            $ShippingAddress = $this->getCustomer()->getPrimaryShippingAddress();
            $BillingAddress = $this->getCustomer()->getPrimaryBillingAddress();
            $productTaxId = Mage::getStoreConfig('quotation/general/quotation_tax_class');
            $storeId = $this->getCustomer()->getStoreId();
            $percent = $this->getTaxRateForProduct($productTaxId, $ShippingAddress, $BillingAddress, $storeId);
            $value = $value / (1 + ($percent / 100));
        }

        //add excluded products
        foreach ($this->getItems() as $item) {
            if ($item->getexclude() == 1) {
                $value += $item->GetUnitPriceWithoutTaxes($this) * $item->getqty();
            }
        }

        //Shipping fees
        $value += $this->getShippingCostWithoutTax();

        return number_format($value, 2, '.', '');
    }

    /**
     *  Return wee (formated)
     */
    public function GetFormatedEcoTax($quote='') {
        //recupere le prix brut
        $value = $this->geteco_tax();
        //formate l'affichage
        $value = Mage::app()->getStore()->convertPrice($value, true);
        //retourne
        return $value;
    }

    /**
     * Return bundle price (formated)
     */
    public function GetConfigFormatedPriceWithoutTaxes($quote='') {
        if ($this->GetLinkedProduct())
            $priceWithoutTaxes = $this->GetProductPriceWithoutTaxes($this->GetLinkedProduct(), $this->GetLinkedProduct()->getprice());
        else
            $priceWithoutTaxes = 0;
        return $this->FormatPrice($priceWithoutTaxes);
    }

    /**
     * Return bundle price (formated)
     *
     * @return unknown
     */
    public function GetConfigFormatedPriceWithTaxes($quote='') {
        $priceWithTaxes = $this->GetProductPriceWithTaxes($this->GetLinkedProduct());
        return $this->FormatPrice($priceWithTaxes);
    }

    /**
     * Return tax amount
     *
     */
    public function GetConfigFormatedTaxAmount($quote='') {
        if ($this->GetLinkedProduct())
            $priceWithoutTaxes = $this->GetProductPriceWithoutTaxes($this->GetLinkedProduct(), $this->GetLinkedProduct()->getprice());
        else
            $priceWithoutTaxes = 0;
        $priceWithTaxes = $this->GetProductPriceWithTaxes($this->GetLinkedProduct());
        return $this->FormatPrice($priceWithTaxes - $priceWithoutTaxes);
    }

    /**
     * Return tax amount
     *
     */
    public function GetTaxAmount($quote='') {
        $ttc = number_format($this->GetFinalPriceWithTaxes(), 2, '.', '');
        $ht = number_format($this->GetFinalPriceWithoutTaxes(), 2, '.', '');
        $value = $ttc - $ht;
        return $value;
    }

    /**
     * Return product price (without tax)
     *
     * @param unknown_type $product
     * @param unknown_type $price
     */
    public function GetProductPriceWithoutTaxes($product, $price = null) {
        if ($product == null)
            $product = $this->GetLinkedProduct();
        if ($product == null) {
            if ($price != null)
                return $price;
            else
                return 0;
        }

        if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1) {


            $ShippingAddress = $this->getCustomer()->getPrimaryShippingAddress();
            $BillingAddress = $this->getCustomer()->getPrimaryBillingAddress();
            $productTaxId = Mage::getStoreConfig('quotation/general/quotation_tax_class');
            $storeId = $this->getCustomer()->getStoreId();
            $percent = $this->getTaxRateForProduct($productTaxId, $ShippingAddress, $BillingAddress, $storeId);

            $return = $price / (1 + ($percent / 100));
        } else {
            if ($price == null)
                $price = $product->getPrice();
            $return = $price;
        }

        return $return;
    }

    /**
     * Return product price (with taxes)
     */
    public function GetProductPriceWithTaxes($product, $price = null) {
        if ($product == null)
            $product = $this->GetLinkedProduct();
        if ($product == null)
            return 0;

        if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1) {
            if ($price == null)
                $price = $product->getPrice();
            $return = $price;
        }
        else {
            $helper = Mage::helper('tax');
            $ShippingAddress = $this->getCustomer()->getPrimaryShippingAddress();
            $BillingAddress = $this->getCustomer()->getPrimaryBillingAddress();
            $CustomerTaxClass = $this->getCustomer()->getTaxClassId();

            $store = $this->getCustomer()->getStoreId();
            if ($price == null) {
                $price = $product->getPrice();
            }

            $productTaxId = Mage::getStoreConfig('quotation/general/quotation_tax_class');
            $return = $this->GetTaxForProductFake($price, $productTaxId, $ShippingAddress, $BillingAddress, $CustomerTaxClass, $store);
        }

        return $return;
    }

    public function GetTaxForProductFake($price, $productTaxId, $ShippingAddress, $BillingAddress, $CustomerTaxClass, $storeId) {
        $percent = $this->getTaxRateForProduct($productTaxId, $ShippingAddress, $BillingAddress, $storeId);

        if ($percent > 0)
            $return = $price + ($price * $percent) / 100;
        else
            $return = $price;

        return $return;
    }

    /**
     * Return tax percent for a product
     */
    protected function getTaxRateForProduct($productTaxId, $ShippingAddress, $BillingAddress, $storeId) {
        $store = Mage::app()->getStore($storeId);
        $CustomerTaxClass = $this->getCustomer()->getTaxClassId();

        $request = Mage::getSingleton('tax/calculation')->getRateRequest($ShippingAddress, $BillingAddress, $CustomerTaxClass, $store);
        $request->setProductClassId($productTaxId);
        $percent = Mage::getSingleton('tax/calculation')->getRate($request);

        return $percent;
    }

    /**
     * return tier price
     *
     */
    public function GetTierPriceProduct($product, $price_product_ht, $qty = 1) {

        $CustomerGroupId = $this->getCustomer()->getgroup_id();

        foreach ($product->gettier_price() as $row) {

            if ($row['all_groups'] == 1 || $row['cust_group'] == $CustomerGroupId) {
                if ($row['price_qty'] <= $qty)
                    return $row['price'];
            }
        }

        return $price_product_ht;
    }

    /**
     * return taxes for shipping
     *
     */
    public function getShippingTax($quote='') {
        return $this->getShippingCostWithTax() - $this->getShippingCostWithoutTax();
    }

    /**
     * Return shipping cost without tax
     *
     */
    public function getShippingCostWithoutTax($quote='') {
        if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1) {
            $shippingCostWithTax = $this->getshipping_rate();
            $percent = $this->getShippingTaxRate();
            $value = $shippingCostWithTax / (1 + ($percent / 100));
            return $value;
        } else {
            return $this->getshipping_rate();
        }
    }

    /**
     * Return shipping cost with tax
     *
     * @return unknown
     */
    public function getShippingCostWithTax($quote='') {
        if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1) {
            return $this->getshipping_rate();
        } else {
            $shippingCostWithoutTax = $this->getshipping_rate();
            $percent = $this->getShippingTaxRate();
            $value = $shippingCostWithoutTax * (1 + ($percent / 100));
            return $value;
        }
    }

    /**
     * Return shipping tax rate
     *
     */
    public function getShippingTaxRate($quote='') {
        $helper = Mage::helper('tax');
        $storeId = $this->getStoreId();
        $store = $this->getCustomer()->getStoreId();
        $shippingClassId = $helper->getShippingTaxClass($store);

        $ShippingAddress = $this->getCustomer()->getPrimaryShippingAddress();
        $BillingAddress = $this->getCustomer()->getPrimaryBillingAddress();

        $percent = $this->getTaxRateForProduct($shippingClassId, $ShippingAddress, $BillingAddress, $storeId);
        return $percent;
    }

    /**
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * *************************************** MISCELANEOUS *************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     * **************************************************************************************************************************
     */

   

    /**
     * Get position for next item
     *
     */
    public function getNextProductPosition() {
        $value = 0;
        foreach ($this->getItems() as $item) {
            if ($item->getorder() > $value) {
                $value = $item->getorder();
            }
        }
        return $value + 1;
    }

    /**
     * Check if quote contains at least one real product
     *
     */
    public function hasRealProduct() {
        $retour = false;
        foreach ($this->getItems() as $item) {
            if ($item->getproduct_id() != '')
                return true;
        }
        return $retour;
    }

    /**
     * Check if quote is visible for customer
     */
    public function isViewableByCustomer() {
        return ($this->getstatus() == MDN_Quotation_Model_Quotation::STATUS_ACTIVE);
    }

    /**
     * Check if quote is valid
     */
    public function checkExpirationDateAndApply() {
        if (!$this->IsValid())
            $this->setstatus(MDN_Quotation_Model_Quotation::STATUS_EXPIRED)->save();
    }

    /**
     * Return total products qty
     */
    public function getItemsQty($quote='') {
        $retour = 0;

        foreach ($this->getItems() as $item)
            $retour += $item->getqty();

        return $retour;
    }

    /**
     * Check if quote has been purchased (and update flag)
     */
    public function checkForQuoteIsBought($quote='') {
        $collection = Mage::getModel('sales/order_item')
                        ->getCollection()
                        ->addFieldToFilter('product_id', $this->getproduct_id());
        if ($collection->getSize() > 0) {
            $this->setbought(1)->save();
        }
    }

    //**************************************************************************************************************
    //**************************************************************************************************************
    // EVENT
    //**************************************************************************************************************
    //**************************************************************************************************************

    protected function _beforeSave() {
        parent::_beforeSave();

        //set security key if not set
        if ($this->getsecurity_key() == '') {
            $this->setsecurity_key(md5(date('Y-m-d h:i:s')));
        }

        //if creation, init fields
        if (!$this->getId()) {
            $customerId = $this->getcustomer_id();
            $storeId = Mage::getModel('customer/customer')->load($customerId)->getStoreId();
            $defaultValidityDuration = Mage::getStoreConfig('quotation/general/default_validity_duration', $storeId);
            $defaultExpirationDate = time() + $defaultValidityDuration * 24 * 3600;
            $this->setcreated_time(date("Y/m/d"))
                    ->setvalid_end_time(date('Y/m/d', $defaultExpirationDate));

            $this->setincrement_id($this->generateIncrementId());

            $this->setCustomerName($this->getCustomer()->getName());

            //default status
            if (!$this->getstatus())
                $this->setStatus(self::QUOTE_STATUS_NEW);
        }

        $this->setupdate_time(date("Y/m/d H:i:s"), Mage::getModel('core/date')->timestamp());
    }

    /**
     * After save
     */
    protected function _afterSave() {
        parent::_afterSave();

        //add creation history
        if (!$this->getOrigData('quotation_id'))
            $this->addHistory(Mage::helper('quotation')->__('Created'));
        else {
            if ($this->getStatus() != $this->getOrigData('status'))
                $this->addHistory(Mage::helper('quotation')->__('Status changed to %s', $this->getStatus()));
        }
    }


    /**
     * Duplicate quote
     * @param <type> $newCustomerId
     */
    public function duplicate($customerId)
    {
        //duplicate
        $newQuotation = Mage::getModel('Quotation/Quotation');
        $newQuotation->setData($this->getData());
        $newQuotation->setcustomer_id($customerId);
        $newQuotation->setId(null);
        $newQuotation->setStatus(MDN_Quotation_Model_Quotation::STATUS_NEW);
        $newQuotation->setbought(MDN_Quotation_Model_Quotation::QUOTE_STATUS_NEW);
        $newQuotation->setproduct_id(new Zend_Db_Expr('null'));
        $newQuotation->setnotification_date(new Zend_Db_Expr('null'));
        $newQuotation->setpromo_id(new Zend_Db_Expr('null'));
        $newQuotation->setreminded(new Zend_Db_Expr('null'));
        $newQuotation->setadditional_pdf(new Zend_Db_Expr('null'));
        $newQuotation->setsecurity_key(new Zend_Db_Expr('null'));
        $newQuotation->save();

        //add quote items
        foreach ($this->getItems() as $item) {
            $newItem = Mage::getModel('Quotation/Quotationitem');
            $newItem->setData($item->getData());
            $newItem->setquotation_id($newQuotation->getquotation_id());
            $newItem->setId(null);
            $newItem->save();
        }

        // add history
        $newQuotation->addHistory(Mage::helper('quotation')->__('Duplicated from quote #%s', $this->getincrement_id()));

        return $newQuotation;
    }

    public function checkProducts(){

        $retour = array(
            'error' => false,
            'message' => Mage::helper('quotation')->__('Warning').' : <br/>'
        );

        foreach($this->getItems() as $item){

            $product = Mage::getModel('catalog/product')->load($item->getproduct_id());

            if(!$product->getentity_id()){
                $retour['error'] = true;
                $retour['message'] .= Mage::helper('quotation')->__('%s doesn\'t exists anymore in Magento', $item->getsku()).'<br/>';
            }else{

                if($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED){
                    $retour['error'] = true;
                    $retour['message'] .= Mage::helper('quotation')->__('%s isn\'t enable', $item->getsku()).'<br/>';
                }

                //if(!$product->getis_in_stock()){
                //    $retour['error'] = true;
                //    $retour['message'] .= Mage::helper('quotation')->__('%s isn\'t in stock', $item->getsku()).'<br/>';
                //}
                
                 /************************ Start for bundle product *******************************/
                if($product->getTypeId() == 'bundle')
                {
                    $temptableItem=Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
                    $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                    
                    $sqlItem = $connectionRead->select()
                                    ->from($temptableItem, array('*'))
                                    ->where("parent_item_id = '".$item->getId()."' ");
                    $chkItem = $connectionRead->fetchAll($sqlItem);
                    if(count($chkItem) == 0)
                    {
                        $retour['error'] = true;
                        $retour['message'] .= Mage::helper('quotation')->__('%s has no any bundle item', $item->getsku()).'<br/>';
                    }
                }
                else
                {
                    if(!$product->getis_in_stock()){
                        $retour['error'] = true;
                        $retour['message'] .= Mage::helper('quotation')->__('%s isn\'t in stock', $item->getsku()).'<br/>';
                    }
                }
                /************************ End for bundle product *******************************/

            }

        }

        return $retour;

    }
    public function getStatusHistoryCollection($reload=false)
    {
        if (is_null($this->_statusHistory) || $reload) {
            $this->_statusHistory = Mage::getResourceModel('Quotation/statushistory_collection')
                ->addFieldToFilter('parent_id', $this->getId())
                ->setOrder('created_at', 'desc')
                ->setOrder('entity_id', 'desc');
/*
            if ($this->getId()) {
                foreach ($this->_statusHistory as $status) {
                    $status->setOrder($this);
                }
            }
*/
        }
        return $this->_statusHistory;
    }
    /*
     * Add a comment to order
     * Different or default status may be specified
     *
     * @param string $comment
     * @param string $status
     * @return Mage_Sales_Order_Statushistory
     */
    public function addStatusHistoryComment($comment, $status = false,$notify,$visible)
    {
        if (false === $status) {
            $status = $this->getStatus();
        } elseif (true === $status) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        } else {
            $this->setStatus($status);
        }
        $history = Mage::getModel('Quotation/statushistory')
            ->setStatus($status)
            ->setComment($comment)
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify)
            ->setEntityName($this->_historyEntityName);
        $this->addStatusHistory($history);
        return $history;
    }
    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            return Mage::app()->getStore($storeId);
        }
        return Mage::app()->getStore();
    }
    protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    public function sendOrderUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $storeId = $this->getStore()->getId();

/*        if (!Mage::helper('quotation')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }
*/
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
 	$customer=$this->_customer;
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $customer->getCustomerName();

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($customer->getEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is
        // 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'quote'   => $this,
                'comment' => $comment,
                'billing' => $customer->getBillingAddress()
            )
        );
        $mailer->send();

        return $this;
    }
    /**
     * Set the order status history object and the order object to each other
     * Adds the object to the status history collection, which is automatically saved when the order is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     *
     * @param Mage_Sales_Model_Order_Statushistory $status
     * @return Mage_Sales_Model_Order
     */
    public function addStatusHistory(MDN_Quotation_Model_Statushistory $history)
    {
        $history->setQuote($this);
	$history->setParentId($this->getId());
        $this->setStatus($history->getStatus());
        if (!$history->getId()) {
		$history->save();
//            $this->getStatusHistoryCollection()->addItem($history);
        }
        return $this;
    }

    
    /**
     * Overrides entity id, which will be saved to comments history status
     *
     * @param string $status
     * @return Mage_Sales_Model_Order
     */
    public function setHistoryEntityName( $entityName )
    {
        $this->_historyEntityName = $entityName;
        return $this;
    }
	
	/*function showEmailFiles()*/
	
	public function showEmailFiles($quote_id=0){
		
		/*getting db resources*/
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$quote_attachement_table=Mage::getSingleton('core/resource')->getTableName('quotation_attachements');
    	
		/*show all attachements*/
		$connectionRead->beginTransaction();
		$_attachment_obj = $connectionRead->select()
		                   ->from($quote_attachement_table, array('*'))
						   ->where('quotemail_id=?',$quote_id)
						   ;
		$_attachment_list = $connectionRead->fetchAll($_attachment_obj);
		$connectionRead->commit();
		
		$attachment_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'attachedfiles/';
		$icons_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'upload/fileicons/icons/';
		
		$html='';
		
		
		$_file_icons = array('png'=>'png_icon.png',
							 'doc'=>'docx_icon.png',
							 'docx'=>'docx_icon.png', 
							 'fla'=>'fla_icon.png',
							 'psd'=>'psd_icon.png',
							 'pdf'=>'pdf_icon.png',
							 'rar'=>'rar_icon.png',
							 'txt'=>'txt_icon.png',
							 'ai'=>'ai_icon.png',
							 'xlx'=>'xlxs_icon.png',
							 'xlxs'=>'xlxs_icon.png',
							 'ttf'=>'ttf_icon.png',
							 'gif'=>'gif_icon.png',
							 'jpg'=>'jpg_icon.png',
							 'jpeg'=>'jpg_icon.png',
							 'sql'=>'other_icon.png',
							 'eps'=>'other_icon.png'
							 );
		
		if(count($_attachment_list)>0){
			$html .='<br/><h3>Email Attachement(s)</h3>: <ul>';
			foreach($_attachment_list as $_image){			
			
			$_exten = substr(basename($_image['email_attachment']),strpos(basename($_image['email_attachment']),'.')+1);
			
			
			$html .='<li id="email_attachement_'.$_image['quotation_attachment_id'].'" style="float:left; text-align:center; width:100px;"> <a href="'.$attachment_dir.$_image['email_attachment'].'" target="_blank"><img src="'.$icons_dir.$_file_icons[$_exten].'" /><br/>'.$_image['email_attachment'].'</a><br/><!--div style="cursor:pointer;" onclick="deleteAttachement('.$quote_id.','.$_image['email_attachment_id'].',\''.$_image['email_attachment'].'\')" title="Click to remove '.$_image['email_attachment'].'">X Remove</div-->';
			$html .='<!--input type="hidden" id="email_files_'.$_image['quotation_attachment_id'].'" name="email_attachements[]" value="'.$_image['email_attachment'].'" /-->';
			
			$html .='</li>';
			}
		$html .='</ul>';	
		}
		
		
		
		return $html;
		
	}
	
	/*function showAttachedFiles()*/
	
	public function showAttachedFiles($quote_id=0){
		
		/*getting db resources*/
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');    	
    	$quote_attachement_table=Mage::getSingleton('core/resource')->getTableName('quotation_attachements');
    	
		/*show all attachements*/
		$connectionRead->beginTransaction();
		$_attachment_obj = $connectionRead->select()
		                   ->from($quote_attachement_table, array('*'))
						   ->where('quotemail_id=?',$quote_id)
						   ;
		$_attachment_list = $connectionRead->fetchAll($_attachment_obj);
		$connectionRead->commit();
		
		$attachment_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'attachedfiles/';
		$icons_dir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'upload/fileicons/icons/';
		
		$html='';
		
		if(count($_attachment_list)>0){
			$html .='<span> ';
			foreach($_attachment_list as $_image){			
			
			$_exten = substr(basename($_image['email_attachment']),strpos(basename($_image['email_attachment']),'.')+1);
			
			
			$html .=' 
			&nbsp;|&nbsp; <a href="'.$attachment_dir.$_image['email_attachment'].'" target="_blank">'.$_image['email_attachment'].'</a>';
			
			
			$html .='';
			}
		$html .='<div style="clear:both;"></div>';	
		}
		
		
		
		return $html;
		
	}
	
	
	 ///show_detail_price
	 public function getshow_detail_price($quoted=''){
		 
		 return 1;
		 }
		 
	public function getStatusLabel(){
		 return $this->getStatus();
		}	 
	
    		
	
}
