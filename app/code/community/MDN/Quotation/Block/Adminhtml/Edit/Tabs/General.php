<?php

class MDN_Quotation_Block_Adminhtml_Edit_Tabs_General extends Mage_Adminhtml_Block_Widget_Form {

    protected $_product;

    /**
     * Return current quote
     */
    public function getQuote()
    {
        return Mage::registry('current_quote');
    }

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();
        $this->setHtmlId('general');
        $this->setTemplate('Quotation/Edit/Tab/General.phtml');
    }

    /**
     * Title
     *
     * @return unknown
     */
    public function getTitle() {
        return $this->__('Quotation #%s', $this->getQuote()->getincrement_id());
    }

    /**
     * Return textual scope information (website + store)
     *
     */
    public function getScopeInformation() {
        $website = Mage::getModel('core/website')->load($this->getQuote()->getWebsiteId());
        $store = Mage::getModel('core/store')->load($this->getQuote()->getStoreId());

        $value = $website->getname() . ' / ' . $store->getname();
        return $value;
    }

    /**
     * Return customer information + link
     *
     */
    public function GetCustomerInfo() {
        $customer = $this->getQuote()->getCustomer();
        return "<a href=\"" . $this->GetBackUrl() . "\">" . $customer->getname() . "</a>";
    }

    /**
     * Url to go back to customer account
     */
    public function GetBackUrl() {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $this->getQuote()->getcustomer_id()));
    }

    /**
     * Return delete url
     */
    public function getDeleteUrl() {
        return $this->getUrl('Quotation/Admin/delete', array('quote_id' => $this->getQuote()->getId()));
    }

    /**
     * return associated product
     *
     */
    public function GetProduct() {
        if (($this->_product == null) && ($this->getQuote()->getproduct_id() != null) && ($this->getQuote()->getproduct_id() != 0)) {
            $productId = $this->getQuote()->getproduct_id();
            $this->_product = Mage::getModel('catalog/product')->load($productId);
        }
        return $this->_product;
    }

    /**
     * Return url to view PDF
     */
    public function getViewAttachmentUrl() {
        return $this->getUrl('Quotation/Admin/DownloadAdditionalPdf/', array('quote_id' => $this->getQuote()->getquotation_id()));
    }

    /**
     * Return true if PDF attachment exists
     */
    public function hasAttachment()
    {
        return Mage::helper('quotation/Attachment')->attachmentExists($this->getQuote());
    }

    /**
     * Return carriers
     *
     */
    public function getCarriersAsCombo($name, $value) {
        $retour = '<select name="' . $name . '">';
        $retour .= '<option value=""></option>';
        $config = Mage::getStoreConfig('carriers');
        foreach ($config as $code => $methodConfig) {
            if (Mage::getStoreConfigFlag('carriers/' . $code . '/active')) {
                $selected = '';
                if ($code == $value)
                    $selected = ' selected ';
                $retour .= '<option value="' . $code . '" ' . $selected . '>' . $code . '</option>';
            }
        }

        $retour .= '</select>';
        return $retour;
    }

    /**
     * Return a combo box with yes / no
     */
    public function getYesNoCombo($name, $value, $onChange=null)
    {
        $retour = '<select id="' . $name . '" name="' . $name . '" onchange="'.$onChange.'">';
        $retour .= '<option value="0" '.($value?'':'selected').'>'.$this->__('No').'</option>';
        $retour .= '<option value="1"'.($value?'selected':'').'>'.$this->__('Yes').'</option>';
        $retour .= '</select>';
        return $retour;
    }

    /**
     * Return a dropdown menu with statuses
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    public function getStatusesAsCombo($name, $value) {
        $retour = '<select id="' . $name . '" name="' . $name . '">';
        $config = Mage::helper('quotation')->getStatusesAsArray();
        foreach ($config as $code => $caption) {
            $selected = '';
            if ($code == $value)
                $selected = ' selected ';
            $retour .= '<option value="' . $code . '" ' . $selected . '>' . $caption . '</option>';
        }

        $retour .= '</select>';
        return $retour;
    }

    /**
     * return a combobox with users (manager)
     *
     * @param unknown_type $name
     * @param unknown_type $value
     * @return unknown
     */
    public function getUsersAsCombo($name, $value) {
        $retour = '<select id="' . $name . '" name="' . $name . '">';
        $retour .= '<option value="" ></option>';
        $config = Mage::helper('quotation')->getUsers();
        foreach ($config as $code => $caption) {
            $selected = '';
            if ($code == $value)
                $selected = ' selected ';
            $retour .= '<option value="' . $code . '" ' . $selected . '>' . $caption . '</option>';
        }

        $retour .= '</select>';
        return $retour;
    }



	/**
     * return a combobox with pre defined quote emails templates (manager)
     *
     * @Function: getQuoteEmailTemplatesAsCombo()
	 * @param unknown_type $name
     * @param unknown_type $value
     * @return quoteEmail Templates list in cumbo box
     */
    public function getQuoteEmailTemplatesAsCombo($name, $value='') {
        $emailoutput = '<select style="width:300px;" id="' . $name . '" name="' . $name . '" onchange="updateQuoteTemplate(this)">';
         $emailoutput .= '<option value="0" >---Please select email template--- </option>';
		
		$quotemail = Mage::getModel('quotemail/quotemail')->getAllQuoteMails();
		//$config = Mage::helper('quotation')->getUsers();		 		
	 foreach ($quotemail as $code => $caption) {
            	$selected = '';            	
				if ($caption['quotemail_id'] == $value){
                	$selected = ' selected ';
				}
            $emailoutput .= '<option  value="' . $caption['quotemail_id'] . '" ' . $selected . '>' . $caption['title'] . '</option>';
        }

        $emailoutput .= '</select>';
        return $emailoutput;
    }






    /**
     * return url to print quote
     */
    public function getPrintUrl() {
        return $this->getUrl('Quotation/Admin/print', array('quote_id' => $this->getQuote()->getId()));
    }

    /**
     * Return url to notify customer
     */
    public function getNotifyUrl() {
        return $this->getUrl('Quotation/Admin/notify', array('quote_id' => $this->getQuote()->getId()));
    }

    /**
     * Return url to remind customer
     */
    public function getRemindUrl() {
        return $this->getUrl('Quotation/Admin/RemindCustomer', array('quote_id' => $this->getQuote()->getId()));
    }

    /**
     * Return quote statuses as combobox
     */
    public function getQuoteStatusAsCombo() {

        $html = '';

        $html .= '<select name="myform[bought]" id="bought"/>';
        $html .= '<option value=""></option>';

        foreach (Mage::getModel('Quotation/Quotation')->getBoughtStatusValues() as $k => $v) {

            $selected = ($this->getQuote()->getbought() == $k) ? 'selected' : '';
            $html .= '<option ' . $selected . ' value="' . $k . '">' . Mage::Helper('quotation')->__($v) . '</option>';
        }

        $html .= '</select>';
        return $html;
    }
	


  /**
     * Function: getStoreInfoCombo();
	 * Return storeinfo
     */
    public function getStoreInfoCombo() {

        $html = '';

        $html .= '<select name="myform[bought]" id="bought"/>';
        $html .= '<option value="">Select Store</option>';

        foreach (Mage::getModel('Quotation/Quotation')->getBoughtStatusValues() as $k => $v) {

            $selected = ($this->getQuote()->getbought() == $k) ? 'selected' : '';
            $html .= '<option ' . $selected . ' value="' . $k . '">' . Mage::Helper('quotation')->__($v) . '</option>';
        }

        $html .= '</select>';
        return $html;
    }
	
/**
     * Function: getCustomerStore();
	 * Return storeinfo
     */
    public function getCustomerStore() {

        $_customer_store = array();
		$_quote_id = $this->getQuote()->getId();
		$_quote = Mage::getModel('Quotation/Quotation')->load($_quote_id);
		/*loading customer id*/		
		$_customer_id = $_quote->getCustomer_id();
		
		/*loading customer store */
		$_customer = Mage::getModel('customer/customer')->load($_customer_id);		
		$_customer_store['store_name'] = $_customer->getCreated_in();
		$_customer_store['store_id'] = $_customer->getStore_id();		
        
        return $_customer_store;
    }

	/**
     * Function: showEmailTemplate();
	 * Return Textarea with loaded template
     */
    public function showEmailTemplate($id='', $name='', $value='') {
		
			$_html = '';
			
			$_html .='<textarea id="'.$id.'" name="'.$name.'" cols="80" rows="10" style="width: 1524px; height: 360px;">'.$value.'</textarea>';
        
      			   
	  return $_html;
    }
	

/*function showEmailAttachements()*/
	
	public function showEmailFiles($quote_id=0){
		
		
		$_files = Mage::getModel('Quotation/Quotation')->showEmailFiles($quote_id);
		
		
		return $_files;
		
		
		}
		
	/*function getChildrenBundleItems*/
	
	public function getChildrenBundleItems($item_id=null){
		
		 ///bundle items
		$bundle_items = Mage::getModel('Quotation/Quotationitem')->getItemBundleItems( $item_id);
		return $bundle_items;
		
		}	
		
	///function getBundleItems()
	
	public function getBundleItems($_item_id=null, $quotation_id=null){
		
		$bundleItemTable = Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sqlItem = $connectionRead->select()
                                    ->from($bundleItemTable, array('*'))
                                    ->where("parent_item_id = '".$_item_id."' AND quotation_id='".$quotation_id."'")
									;
        $bundle_items = $connectionRead->fetchAll($sqlItem);	
		return $bundle_items;
	}
	
}
