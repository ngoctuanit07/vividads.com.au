<?php

class MDN_Quotation_Model_QuotationPdf extends MDN_Quotation_Model_Pdfhelper {

    protected $_settings;

    /**
     * Create PDF
     */
    public function getPdf($invoices = array()) {

         $this->_beforeGetPdf();
         $this->_initRenderer('invoice');

        //load Quote
        if (count($invoices) == 0)
            throw new Exception('No quote to print !');

        $quote = $invoices[0];
        $storeId = $quote->getStoreId();

        $this->pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);

        //create new page
        $settings = array();
        $settings['title'] = '';
        $settings['store_id'] = $storeId;
        $title = Mage::helper('quotation')->__('Quotation #%s', $quote->getincrement_id());
		
         
		// $txt_quote = Mage::helper('quotation')->__('Quotation valid until %s', Mage::helper('core')->formatDate($quote->getvalid_end_time(), 'long'));
      //  $adresse_fournisseur = Mage::getStoreConfig('sales/identity/address', $storeId);
        
		if ($quote->GetCustomerAddress() != null){
            $adresse_client = $this->FormatAddress($quote->GetCustomerAddress(), '', false);
	   }else {
            $adresse_client = $quote->GetCustomer()->getName();
        }
		 
        /*fetching quote detail*/
		
		$store_id = $quote->getCustomer()->getStore_id();
		$quoteInfo['quote_type'] = 'Quote';
		$quoteInfo['data'] = $quote->getData();
		$quoteInfo['customer'] = $quote->getCustomer();
		$quoteInfo['customer_address'] = $adresse_client;
		$quoteInfo['id'] = $quote->getId();	
		$quoteInfo['store_id']	 = $store_id;
		$quoteInfo['store_phone']= Mage::getStoreConfig('globalpdf/general/company_tel',$store_id);
		$quoteInfo['store_logo'] = Mage::getStoreConfig('globalpdf/general/logo',$store_id);
		$quoteInfo['store_name'] = Mage::getStoreConfig('globalpdf/general/company_name',$store_id);
		
		$quoteInfo['store_url'] = Mage::app()->getWebsite($quote->getCustomer()->getWebsite_id())->getName();
		$quoteInfo['store_email'] = Mage::getStoreConfig('globalpdf/general/company_email',$store_id);
		$quoteInfo['ABN'] = Mage::getStoreConfig('globalpdf/general/company_abn',$store_id);;
		$quoteInfo['store_address'] = Mage::getStoreConfig('globalpdf/general/store_address',$store_id);
		$quoteInfo['grand_total'] = $quote->GetFinalPriceWithTaxes();
		$quoteInfo['grand_total_f'] = $quote->FormatPrice($quote->GetFinalPriceWithTaxes());
		$quoteInfo['grand_total_format']= $quote->FormatPrice($quote->GetFinalPriceWithTaxes());
		$quoteInfo['total_saved'] = $quote->FormatPrice($quote->getGrand_total()*.22);
		$quoteInfo['total_amount'] = str_replace('$','',$quote->FormatPrice($quote->GetFinalPriceWithTaxes()));
		$quoteInfo['sub_total'] = $quote->FormatPrice($quote->GetFinalPriceWithoutTaxes());
		$quoteInfo['shipping_total'] = $quote->FormatPrice($quote->GetTaxAmount());
		$quoteInfo['tax_total'] = $quote->FormatPrice($quote->getShippingRate());
		
		
		
		
		//if has business proposal, add the first page
        if ($quote->hasBusinessProposal()) {

            $page = $this->NewPage($settings, $quoteInfo);

            // main page
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 32);
            $this->drawTextInBlock($page, $title, 0, $this->_PAGE_WIDTH / 2 + 50, $this->_PAGE_WIDTH - 80, 50, 'c');
            $this->drawTextInBlock($page, $quote->getcaption(), 0, $this->_PAGE_WIDTH / 2, $this->_PAGE_WIDTH - 80, 50, 'c');
          //  $this->drawFooter($page, $storeId);

            // add business proposal
            $this->drawBusinessProposal($page, $quote, $settings);
            // new page
           // $this->drawFooter($page, $storeId);
        }
		
		$settings['title'] = $title;
        $this->_settings = $settings;
        $page = $this->NewPage($settings, $quoteInfo);		
		
		$this->drawHeader($page, $settings['title'], $settings['store_id'], $quoteInfo);
		$this->companyAddress($page, $settings['title'], $settings['store_id'], $quoteInfo);
		$this->billBlock($page, $settings['title'], $settings['store_id'], $quoteInfo);
		$this->shipAddress($page, $settings['title'], $settings['store_id'], $quoteInfo); 
		  
        //Header
	
        $txt_date = Mage::helper('quotation')->__('Quote Date : %s', Mage::helper('core')->formatDate($quote->getcreated_time(), 'long'));
       
		
		/********************** Start by dev **********************************/
        /* 
		$this->AddAddressesBlock($page, $adresse_fournisseur, $txt_date."\n".$title,  '');
        
		/*
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(20, 680, 250, 700, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
        $this->drawTextInBlock($page, 'Bill To', 30, 685, 200,150,'l');
         
        $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(300, 680, 580, 700, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $this->drawTextInBlock($page, 'Ship To', 310, 685, 200,150,'l');        
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(21, 590, 249, 680);
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(301, 590, 579, 680);
        
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
		
		$sqlSystem="SELECT * FROM ".$tableBilling." WHERE quotation_id = '".$quote->getId()."' ";
         
         try {
                 $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlSystem);
		 $fetchSystem = $chkSystem->fetch();
         } catch (Exception $e){
         echo $e->getMessage();
         }
         $billing = '';
         
        $billing .= wordwrap($fetchSystem['firstname'].' '.$fetchSystem['lastname'],20,"\n");
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, $billing, 25, 660, 500,150,'l');
        
        if($fetchSystem['street1'] != '')
         {
             $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['street1'].', '.$fetchSystem['street2'], 25, 640, 200,150,'l');
        }
        
	
	if($fetchSystem['city'] != '')
	$billing1 .= $fetchSystem['city'].",";
	
	if($fetchSystem['region'] != '')
	$billing1 .= $fetchSystem['region'].",";
	//
	//
	//if($fetchSystem['region_id'] != '')
	//$billing1 .= $fetchSystem['region_id'].",";
	
	if($fetchSystem['postcode'] != '')
	$billing1 .= $fetchSystem['postcode'].", ";
	
	if($fetchSystem['country_id'] != '')
	$billing1 .= $fetchSystem['country_id'];
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, $billing1, 25, 620, 200,150,'l');
	
	if($fetchSystem['telephone'] != '')
         {
            $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['telephone'], 25, 600, 200,150,'l');
        }
	
        
         
        
         $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $this->drawTextInBlock($page, $billing, 30, 660, 200,150,'l');
        
        $tableShipping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($tableShipping))
        {
	    $sqlSystem="SELECT * FROM ".$tableShipping." WHERE quotation_id = '".$quote->getId()."' ";
	     
	     try {
		     $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlSystem);
		     $fetchSystem = $chkSystem->fetch();
	     } catch (Exception $e){
	     echo $e->getMessage();
	     }
	}
         $shipping = '';
         
        $shipping = wordwrap($fetchSystem['firstname'].' '.$fetchSystem['lastname'],20,"\n");
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, $shipping, 310, 660, 200,150,'l');
        
        if($fetchSystem['street1'] != '')
        {
             $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['street1'].', '.$fetchSystem['street2'], 310, 640, 200,150,'l');
        }
	
	
	$shipping1 = '';
	if($fetchSystem['city'] != '')
	$shipping1 .= $fetchSystem['city'].",";
	
	if($fetchSystem['region'] != '')
	$shipping1 .= $fetchSystem['region'].",";
	
	//
	//if($fetchSystem['region_id'] != '')
	//$shipping1 .= $fetchSystem['region_id'].",";
        
        if($fetchSystem['postcode'] != '')
	$shipping1 .= $fetchSystem['postcode'].',';
        
        if($fetchSystem['country_id'] != '')
        {
            $shipping1 .= $fetchSystem['postcode'];
        }
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, $shipping1, 310, 620, 200,150,'l');
	
     */   
	
	if($fetchSystem['telephone'] != '')
        {
            $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['telephone'], 310, 600, 200,150,'l');
        }
	
        
         $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
         $this->drawTextInBlock($page, $shipping, 310, 660, 200,150,'l');
        
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        
        /********************** End by dev **********************************/

        // add listing products
        $this->y -= 235;
	   
	     $this->drawListingProducts($page, $quote, $style, $settings, $quoteInfo);
		 
        //new page if required
       // if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 50)) {
            //  $this->drawFooter($page, $settings['store_id']);
            //  $page = $this->NewPage($settings, $quoteInfo);			 
           //  $this->drawProductTableHeader($page, $quoteInfo);
       // }
		
		 $this->y -= 15;
	
		$page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
        $page->drawRectangle(10, $this->y-25, 270, $this->y, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),11);
        $this->drawTextInBlock($page, 'Shipping Method:', 15, $this->y-15, 200,$this->y+20,'l');
        
        // $page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
       // $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        // $page->drawRectangle(21, $this->y-25, 269, $this->y);
        
        if($quote->getFree_shipping() !=1){
	    $carrierTitle =  end(explode('_',$quote->getShippingMethod()));
        $ship = Mage::getStoreConfig('carriers/'.$carrierTitle.'/title');
		}else{
			$ship = 'Free Shipping';
		}
        $page->setFillColor(Zend_Pdf_Color_Html::color('#666666'));
      //  $this->drawTextInBlock($page, $ship, 15, $this->y-40, 269,$this->y,'l');
		 
		
		
		if($quote->getMessage() !=''){
			$ycolumn = $this->y;		
		$customer_comments =$quote->getMessage();		
		$ycolumn -= 60 ;
		$_block_text = array('text'=>$customer_comments,'bgcolor'=>'#dceff6', 'fcolor'=>'#000000','type'>='single');	 
	       $this->addBlockTextComments($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-6, 300, 90, 10, 14, $_block_text['type'] );  
		}
	 
        // $this->drawTotals($page, $quote, $quoteInfo);		
         $this->drawAgreement($page, $settings, $quoteInfo);
        
      //  $this->y -= 15;
        
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        //$page->drawRectangle(20, $this->y+25, 270, $this->y, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        //$this->drawTextInBlock($page, 'Requested Delivery Date', 30, $this->y+10, 200,$this->y+20,'l');
        //
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        //$page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        //$page->drawRectangle(21, $this->y-25, 269, $this->y);
        //
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        //$this->drawTextInBlock($page, $fetchSystem['inhand'], 24, $this->y-20, 269,$this->y,'l');
        
       
      //  $this->y -= 30;
	
		//$page1 = $this->NewPage($settings, $quoteInfo);

        //on place Y tout en haut
       // $pdf = new Zend_Pdf();
	//$page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        
	 /*
        $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(20, $this->y-25, 270, $this->y, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $this->drawTextInBlock($page, 'Project Timeline', 30, $this->y-15, 200,$this->y+20,'l');
        
	//Adding the planninmg time line
	$temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptablePlanning))
        {
	$sqlPlanning="SELECT * FROM ".$temptablePlanning." WHERE quote_id = '".$quote->getId()."' AND planning_type = 'quote' ";
	$chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchALL($sqlPlanning);
	}
	
	$page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(21, $this->y-(count($chkPlanning)*9*20), 269, $this->y-20);
  
        //$this->y = $this->y-500;
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, 'Requested Delivery Date: '.date('d/m/Y',strtotime($fetchSystem['inhand'])), 24, $this->y-35, 269,$this->y-20,'l');
	
	$this->y = $this->y-35;
	foreach($chkPlanning as $planning)
	{
	    $_Product = Mage::getModel('catalog/product')->load($planning['product_id']);
	    
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
	    $page->drawRectangle(20, $this->y-3, 270, $this->y+13, Zend_Pdf_Page::SHAPE_DRAW_FILL);
	    
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, $_Product->getName(), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Order Placed By: '.date('d/m/Y',strtotime($chkPlanning[0]['order_placed_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Artwork Submitted By: '.date('d/m/Y',strtotime($chkPlanning[0]['artwork_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Proof Approval Date: '.date('d/m/Y',strtotime($chkPlanning[0]['proof_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Production Start: '.date('d/m/Y',strtotime($chkPlanning[0]['start_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Shipping On: '.date('d/m/Y',strtotime($chkPlanning[0]['shipping_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Delivery On: '.date('d/m/Y',strtotime($chkPlanning[0]['delivery_date'])), 24, $this->y, 269,$this->y-20,'l');
	}
	
        */ 
	  //$this->drawFooter($page, $storeId);
        //display page number
         $this->AddPagination($this->pdf, $quoteInfo);
         $this->_afterGetPdf();
        return $this->pdf;
    }
	
	 /**
     * Create PDF from order
     */
    public function getOrderPdf($invoices = array()) {
		
		 $this->_beforeGetPdf();
         $this->_initRenderer('invoice');
		 
		
		  //load Quote
        if (count($invoices) == 0)
            throw new Exception('No quote to print !');

        $quote = $invoices[0];
        $storeId = $quote->getStoreId();

        $this->pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
		
        //create new page
        $settings = array();
        $settings['title'] = '';
        $settings['store_id'] = $storeId;
        $title = Mage::helper('quotation')->__('Quotation #%s', $quote->getincrement_id());
		
		$customerid = $quote->getCustomerId();
		$customer_data = Mage::getModel('customer/customer')->load($customerid);
		$billingaddress = Mage::getModel('customer/address')->load($customer_data->default_billing);
		
		// $txt_quote = Mage::helper('quotation')->__('Quotation valid until %s', Mage::helper('core')->formatDate($quote->getvalid_end_time(), 'long'));
      //  $adresse_fournisseur = Mage::getStoreConfig('sales/identity/address', $storeId);
        
		if ($billingaddress != null){
            $adresse_client = $this->FormatAddress($billingaddress, '', false);
	   }else {
            $adresse_client = $billingaddress->getData()->getName();
        }
		 
      /*fetching quote detail*/
		
		$store_id = $storeId;
		$quoteInfo['quote_type'] = 'Tax Invoice';
		$quoteInfo['data'] = $quote->getData();
		$quoteInfo['customer'] = $customer_data;
		$quoteInfo['customer_address'] = $adresse_client;
		$quoteInfo['id'] = $quote->getId();	
		$quoteInfo['store_id']	 = $store_id;
		$quoteInfo['store_phone']= Mage::getStoreConfig('globalpdf/general/company_tel',$store_id);
		$quoteInfo['store_logo'] = Mage::getStoreConfig('globalpdf/general/logo',$store_id);
		$quoteInfo['store_name'] = Mage::getStoreConfig('globalpdf/general/company_name',$store_id);
		
	//	$quoteInfo['store_url'] = Mage::app()->getWebsite($quote->getCustomer()->getWebsite_id())->getName();
	   $_website = Mage::app()->getWebsite( Mage::getModel('core/store')->load($store_id)->getWebsiteId());
		$quoteInfo['store_url']=$_website->getName();	
		$quoteInfo['store_email'] = Mage::getStoreConfig('globalpdf/general/company_email',$store_id);
		$quoteInfo['ABN'] = Mage::getStoreConfig('globalpdf/general/company_abn',$store_id);;
		$quoteInfo['store_address'] = Mage::getStoreConfig('globalpdf/general/store_address',$store_id);
		$grand_total = $quote->FormatPrice($quote->getGrand_total());
		$grand_total = substr($grand_total, strpos($grand_total,'$')+1,strpos($grand_total,'</span>'));
		$quoteInfo['grand_total'] = $quote->getGrand_total();		
		$quoteInfo['grand_total_f'] = $quote->FormatPrice($quote->getGrand_total());
		$quoteInfo['grand_total_format'] = $grand_total;
		$quoteInfo['total_saved'] = $quote->FormatPrice($quote->getGrand_total()*.22);
		$quoteInfo['total_amount'] = str_replace('$','',$quote->FormatPrice($quote->getGrand_total())); 
		$quoteInfo['sub_total'] = $quote->FormatPrice($quote->getSubtotal());
		$quoteInfo['shipping_total'] = $quote->FormatPrice($quote->getShippingRate());
		$quoteInfo['tax_total'] = $quote->FormatPrice($quote->getTax_amount());
		
		
		
		//if has business proposal, add the first page
        if ($quote->hasBusinessProposal()) {

            $page = $this->NewPage($settings, $quoteInfo);

            // main page
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 32);
            $this->drawTextInBlock($page, $title, 0, $this->_PAGE_WIDTH / 2 + 50, $this->_PAGE_WIDTH - 80, 50, 'c');
            $this->drawTextInBlock($page, $quote->getcaption(), 0, $this->_PAGE_WIDTH / 2, $this->_PAGE_WIDTH - 80, 50, 'c');
          //  $this->drawFooter($page, $storeId);
            // add business proposal
            $this->drawBusinessProposal($page, $quote, $settings);
            // new page
           // $this->drawFooter($page, $storeId);
        }
		
				 
		$settings['title'] = $title;
        $this->_settings = $settings;
        $page = $this->NewPage($settings, $quoteInfo);	
		
		$this->drawHeader($page, $settings['title'], $settings['store_id'], $quoteInfo);		
		$this->companyAddress($page, $settings['title'], $settings['store_id'], $quoteInfo);		
		$this->billBlock($page, $settings['title'], $settings['store_id'], $quoteInfo);		
		$this->shipAddress($page, $settings['title'], $settings['store_id'], $quoteInfo); 
		   
        //Header
	
        $txt_date = Mage::helper('quotation')->__('Quote Date : %s', Mage::helper('core')->formatDate($quote->getcreated_time(), 'long'));
       
		/********************** Start by dev **********************************/
        /* 
		$this->AddAddressesBlock($page, $adresse_fournisseur, $txt_date."\n".$title,  '');
        
		/*
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(20, 680, 250, 700, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
        $this->drawTextInBlock($page, 'Bill To', 30, 685, 200,150,'l');
         
        $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(300, 680, 580, 700, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $this->drawTextInBlock($page, 'Ship To', 310, 685, 200,150,'l');        
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(21, 590, 249, 680);
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(301, 590, 579, 680);
        
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
		
		$sqlSystem="SELECT * FROM ".$tableBilling." WHERE quotation_id = '".$quote->getId()."' ";
         
         try {
                 $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlSystem);
		 $fetchSystem = $chkSystem->fetch();
         } catch (Exception $e){
         echo $e->getMessage();
         }
         $billing = '';
         
        $billing .= wordwrap($fetchSystem['firstname'].' '.$fetchSystem['lastname'],20,"\n");
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, $billing, 25, 660, 500,150,'l');
        
        if($fetchSystem['street1'] != '')
         {
             $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['street1'].', '.$fetchSystem['street2'], 25, 640, 200,150,'l');
        }
        
	
	if($fetchSystem['city'] != '')
	$billing1 .= $fetchSystem['city'].",";
	
	if($fetchSystem['region'] != '')
	$billing1 .= $fetchSystem['region'].",";
	//
	//
	//if($fetchSystem['region_id'] != '')
	//$billing1 .= $fetchSystem['region_id'].",";
	
	if($fetchSystem['postcode'] != '')
	$billing1 .= $fetchSystem['postcode'].", ";
	
	if($fetchSystem['country_id'] != '')
	$billing1 .= $fetchSystem['country_id'];
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, $billing1, 25, 620, 200,150,'l');
	
	if($fetchSystem['telephone'] != '')
         {
            $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['telephone'], 25, 600, 200,150,'l');
        }
	
        
         
        
         $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $this->drawTextInBlock($page, $billing, 30, 660, 200,150,'l');
        
        $tableShipping = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($tableShipping))
        {
	    $sqlSystem="SELECT * FROM ".$tableShipping." WHERE quotation_id = '".$quote->getId()."' ";
	     
	     try {
		     $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlSystem);
		     $fetchSystem = $chkSystem->fetch();
	     } catch (Exception $e){
	     echo $e->getMessage();
	     }
	}
         $shipping = '';
         
        $shipping = wordwrap($fetchSystem['firstname'].' '.$fetchSystem['lastname'],20,"\n");
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, $shipping, 310, 660, 200,150,'l');
        
        if($fetchSystem['street1'] != '')
        {
             $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['street1'].', '.$fetchSystem['street2'], 310, 640, 200,150,'l');
        }
	
	
	$shipping1 = '';
	if($fetchSystem['city'] != '')
	$shipping1 .= $fetchSystem['city'].",";
	
	if($fetchSystem['region'] != '')
	$shipping1 .= $fetchSystem['region'].",";
	
	//
	//if($fetchSystem['region_id'] != '')
	//$shipping1 .= $fetchSystem['region_id'].",";
        
        if($fetchSystem['postcode'] != '')
	$shipping1 .= $fetchSystem['postcode'].',';
        
        if($fetchSystem['country_id'] != '')
        {
            $shipping1 .= $fetchSystem['postcode'];
        }
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, $shipping1, 310, 620, 200,150,'l');
	
     */   
	
	if($fetchSystem['telephone'] != '')
        {
            $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['telephone'], 310, 600, 200,150,'l');
        }
	
         $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
         $this->drawTextInBlock($page, $shipping, 310, 660, 200,150,'l'); 
         $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        
        /********************** End by dev **********************************/

         // add listing products
        $this->y -= 235;
	   
	    if($quoteInfo['quote_type']=='quote'){
		 	 	$this->drawListingProducts($page, $quote, $style, $settings, $quoteInfo);
			}else{
		 	 	$this->drawListingOrderProducts($page, $quote, $style, $settings, $quoteInfo);
			}
        
		
		
		// exit;
		
		//new page if required
       // if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 50)) {
            //  $this->drawFooter($page, $settings['store_id']);
            //  $page = $this->NewPage($settings, $quoteInfo);			 
           //  $this->drawProductTableHeader($page, $quoteInfo);
       // }
		
		 $this->y -= 15;
	
		$page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
        $page->drawRectangle(10, $this->y-25, 270, $this->y, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),11);
        $this->drawTextInBlock($page, 'Shipping Method:', 15, $this->y-15, 200,$this->y+20,'l');
        
        // $page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
       // $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        // $page->drawRectangle(21, $this->y-25, 269, $this->y);
        
        if($quote->getFree_shipping() !=1){
			$carrierTitle =  end(explode('_',$quote->getShippingMethod()));
			$ship = Mage::getStoreConfig('carriers/'.$carrierTitle.'/title');
		}else{
			$ship = 'Free Shipping';
		}
			$page->setFillColor(Zend_Pdf_Color_Html::color('#666666'));
			$this->drawTextInBlock($page, $ship, 15, $this->y-40, 269,$this->y,'l');
		 
		 
		
		if($quote->getMessage() !=''){
			$ycolumn = $this->y;		
		$customer_comments =$quote->getMessage();		
		$ycolumn -= 60 ;
		$_block_text = array('text'=>$customer_comments,'bgcolor'=>'#dceff6', 'fcolor'=>'#000000','type'>='single');	 
	 	$this->addBlockTextComments($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-6, 300, 90, 10, 14, $_block_text['type'] );  
		}
	 
	
	 if($quoteInfo['quote_type']=='quote'){
         
		 	$this->drawTotals($page, $quote, $quoteInfo);		
	 }else{
		 	$this->drawOrderTotals($page, $quote, $quoteInfo);
		 }
		 
		$this->drawAgreement($page, $settings, $quoteInfo);
        
       
	  
	  //  $this->y -= 15;
        
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        //$page->drawRectangle(20, $this->y+25, 270, $this->y, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        //$this->drawTextInBlock($page, 'Requested Delivery Date', 30, $this->y+10, 200,$this->y+20,'l');
        //
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        //$page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        //$page->drawRectangle(21, $this->y-25, 269, $this->y);
        //
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        //$this->drawTextInBlock($page, $fetchSystem['inhand'], 24, $this->y-20, 269,$this->y,'l');
        
       
      //  $this->y -= 30;
	
		//$page1 = $this->NewPage($settings, $quoteInfo);

        //on place Y tout en haut
       // $pdf = new Zend_Pdf();
	//$page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        
	 /*
        $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(20, $this->y-25, 270, $this->y, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $this->drawTextInBlock($page, 'Project Timeline', 30, $this->y-15, 200,$this->y+20,'l');
        
	//Adding the planninmg time line
	$temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptablePlanning))
        {
	$sqlPlanning="SELECT * FROM ".$temptablePlanning." WHERE quote_id = '".$quote->getId()."' AND planning_type = 'quote' ";
	$chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchALL($sqlPlanning);
	}
	
	$page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(21, $this->y-(count($chkPlanning)*9*20), 269, $this->y-20);
  
        //$this->y = $this->y-500;
        $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
        $this->drawTextInBlock($page, 'Requested Delivery Date: '.date('d/m/Y',strtotime($fetchSystem['inhand'])), 24, $this->y-35, 269,$this->y-20,'l');
	
	$this->y = $this->y-35;
	foreach($chkPlanning as $planning)
	{
	    $_Product = Mage::getModel('catalog/product')->load($planning['product_id']);
	    
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
	    $page->drawRectangle(20, $this->y-3, 270, $this->y+13, Zend_Pdf_Page::SHAPE_DRAW_FILL);
	    
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, $_Product->getName(), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Order Placed By: '.date('d/m/Y',strtotime($chkPlanning[0]['order_placed_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Artwork Submitted By: '.date('d/m/Y',strtotime($chkPlanning[0]['artwork_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Proof Approval Date: '.date('d/m/Y',strtotime($chkPlanning[0]['proof_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Production Start: '.date('d/m/Y',strtotime($chkPlanning[0]['start_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Shipping On: '.date('d/m/Y',strtotime($chkPlanning[0]['shipping_date'])), 24, $this->y, 269,$this->y-20,'l');
	    $this->y = $this->y-20;
	    $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
	    $this->drawTextInBlock($page, 'Delivery On: '.date('d/m/Y',strtotime($chkPlanning[0]['delivery_date'])), 24, $this->y, 269,$this->y-20,'l');
	}
	
        */ 
	 
	 
	  //$this->drawFooter($page, $storeId);
        //display page number
         $this->AddPagination($this->pdf, $quoteInfo);
         $this->_afterGetPdf();
		 
		
         return $this->pdf;
		
    }

    /**
     * Draw products table header
     *
     * @param unknown_type $page
     */
    public function drawTableHeader(&$page, $quoteInfo='') {

        //$this->y -= 15;
        //$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
        //$page->drawText(Mage::helper('quotation')->__('Reference'), 15, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Description'), 90, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Qty'), 285, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Discount'), 310, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Unit Price'), 370, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Subtotal'), 450, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Total'), 530, $this->y, 'UTF-8');
        
        /*
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#4d7272'));
	    $page->drawRectangle(10, $this->y, 590, $this->y+10, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),16);
	    $page->drawText(Mage::helper('quotation')->__('Popup Marquee / Canopy / Tent / Gazebo (3.0m x 3.0m) (VDK M1HG)'), 18, $this->y+23, 'UTF-8');
		
        $this->y -=10;
		*/
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ec4d53'));
	    $page->drawRectangle(10, $this->y-10, 590, $this->y+16, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));        
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD),12);
	    $page->drawText(Mage::helper('quotation')->__('Item Description'), 15, $this->y, 'UTF-8');
		$page->drawText(Mage::helper('quotation')->__('Qty'), 450, $this->y, 'UTF-8');        
        $page->drawText(Mage::helper('quotation')->__('Unit Price'), 370, $this->y, 'UTF-8');
		$page->drawText(Mage::helper('quotation')->__('Sku'), 285, $this->y, 'UTF-8');       
       // $page->drawText(Mage::helper('quotation')->__('Discount'), 310, $this->y, 'UTF-8');        
        $page->drawText(Mage::helper('quotation')->__('Subtotal'), 530, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Total'), 530, $this->y, 'UTF-8');
		
       // $this->y -= 8;
        //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
       // $this->y -= 20;
		
		
    }
	
	/**
     * Draw products table header
     *
     * @param unknown_type $page
     */
    public function drawProductTableHeader(&$page, $caption='', $ycolumn=0, $quoteInfo='') {

      //  $this->y -= 5;  	
	     
        $page->setFillColor(Zend_Pdf_Color_Html::color('#4d7272'));
	    $page->drawRectangle(10, $ycolumn+5, 590, $ycolumn-30, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));
		
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),15);
	    $page->drawText(Mage::helper('quotation')->__($caption), 18, $ycolumn-20, 'UTF-8');
		//$ycolumn -=30;
       // $this->y -=30;	     
		$ycolumn -=48;		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ec4d53'));
	    $page->drawRectangle(10, $ycolumn-10, 590, $ycolumn+16, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));        
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD),11);
	    $page->drawText(Mage::helper('quotation')->__('Item Description'), 15, $ycolumn, 'UTF-8');		       
        $page->drawText(Mage::helper('quotation')->__('Price'), 370, $ycolumn, 'UTF-8');
		$page->drawText(Mage::helper('quotation')->__('Quantity'), 450, $ycolumn, 'UTF-8'); 
		$page->drawText(Mage::helper('quotation')->__('Sku'), 285, $ycolumn, 'UTF-8');       
       // $page->drawText(Mage::helper('quotation')->__('Discount'), 310, $ycolumn, 'UTF-8');        
        $page->drawText(Mage::helper('quotation')->__('Subtotal'), 530, $ycolumn, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Total'), 530, $ycolumn, 'UTF-8');
		
      //  $this->y -= 8;
        //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $ycolumn);
      //  $this->y -= 20;
	   
		
		
    }
	
	 /**
     * Add listing products part
     *
     * @param Zend_Pdf_Page $page
     * @param MDN_Quotation_Model_Quotation $quote
     * @param Zend_Pdf_Style $style
     * @return int
     */
    protected function drawListingProducts(&$page, $quote, $style, $settings, $quoteInfo='') 
	{

       // $this->drawTableHeader($page);
	   
	   
		
		$type = $quoteInfo['quote_type'];
		if($type=='Quote'){
				$collection = $quote->getItems();
				
				}else{
					
				$collection = $quote->getAllItems();	
			}
		
        $needBundle = Mage::getModel('Quotation/Quotation_Bundle')->needBundleProduct($quote);
	 	$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
		
		/* draw quote*/
		 
	 	if ($needBundle && ($quote->getshow_detail_price() == 0)) {
            
			 
			$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
            $page->drawText($this->TruncateTextToWidth($page, '', 60), 15, $this->y, 'UTF-8');
           
		    $caption = $this->WrapTextToWidth($page, $quote->getcaption(), 200);
            $caption .= $this->getConfigContentAsText($quote);
            $caption = $this->WrapTextToWidth($page, $caption, 450);
            
			$this->drawTextInBlock($page, $this->TruncateTextToWidth($page, $quote->GetLinkedProduct()->getsku(), 70), 10, $this->y, 40, 20, 'l');
            $offset = $this->DrawMultilineText($page, $caption, 90, $this->y, 10, 0.2, 11);
            $this->drawTextInBlock($page, 1, 275, $this->y, 40, 20, 'c'); //qty
            $this->drawTextInBlock($page, $quote->GetConfigFormatedPriceWithoutTaxes(), 490, $this->y, 60, 20, 'r');
            $this->drawTextInBlock($page, $quote->GetConfigFormatedPriceWithoutTaxes(), 560, $this->y, 60, 20, 'r');
            $this->drawTextInBlock($page, $quote->GetConfigFormatedPriceWithTaxes(), 640, $this->y, 60, 20, 'r');
            $this->y -= $this->_ITEM_HEIGHT + $offset;
       
	    }  
		 
        foreach ($collection as $item) {
          
		     
			if (($item->getexclude() == 1) || ($quote->getshow_detail_price() == 1)) {
                
				$y = $this->y;
				$caption = $this->WrapTextToWidth($page, $item->getcaption(), 200);
				
				///inserting table headers
			 	$this->drawProductTableHeader($page, $caption, $y);
				 
				$this->y -= 80;
        		 
				$page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
				$page->drawRectangle(10, $this->y-12, 590, $this->y+18, Zend_Pdf_Page::SHAPE_DRAW_FILL);
				$this->y -= 3;				
				$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
                $page->drawText($this->TruncateTextToWidth($page, $item->getreference(), 60), 15, $this->y, 'UTF-8');
                $caption = $this->WrapTextToWidth($page, $item->getcaption(), 200);
                $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
			    $this->drawTextInBlock($page, $this->TruncateTextToWidth($page, $item->getsku(), 70), 285, $this->y, 40, 20, 'l');
                $ycolmn = $this->y;
				//$offset = $this->DrawMultilineText($page, $ycolmn, 15, $this->y, 10, 0.2, 11);
				
			  	$offset = $this->DrawMultilineText($page, $caption, 15, $this->y, 10, 0.2, 11);
		
                $this->drawTextInBlock($page, $quote->FormatPrice($item->GetUnitPriceWithoutTaxes($quote)), 480, $this->y, 60, 20, 'r');
                //$this->drawTextInBlock($page, $item->getqty(), 310, $this->y, 40, 20, 'c');
                				 
				
			    $this->drawTextInBlock($page, $item->getqty(), 470, $this->y, 40, 20, 'l');
                
				if ($quote->getshow_detail_price() || ($item->getexclude() == 1)) {
                    //if ($item->getdiscount_purcent() > 0)
                       // $this->drawTextInBlock($page, $item->getdiscount_purcent() . '%', 330, $this->y, 20, 20, 'r');
                     //$this->drawTextInBlock($page, $quote->FormatPrice($item->GetUnitPriceWithoutTaxes($quote)), 370, $this->y, 60, 20, 'r');
                    $this->drawTextInBlock($page, $quote->FormatPrice($item->GetTotalPriceWithoutTaxes($quote)), 640, $this->y, 60, 20, 'r');
                    //$this->drawTextInBlock($page, $quote->FormatPrice($item->GetTotalPriceWithTaxes($quote)), 640, $this->y, 60, 20, 'r');
                }
                $this->y -= $this->_ITEM_HEIGHT + $offset;
		
		/***************** Start For bundle product 11_02_2014  *********************/
		
	 	if($item->getProductType() == 'bundle')
		{
		    $temptableBundleItem=Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
		    $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		    
		    $sqlBundle = $connectionRead->select()
                                ->from($temptableBundleItem, array('*'))
                                ->where('parent_item_id=?', $item->getId());
		    $chkBundle = $connectionRead->fetchAll($sqlBundle);
		    
			///fetching bundle items
			$bundled_product = Mage::getModel('catalog/product')->load($item->getProduct_id());			
			$chkBundle = Mage::getModel('Quotation/Quotationitem')->getBundledChildrenProduct($bundled_product);			
		    
					
		    foreach($chkBundle as $bundle)
		    {
				  
				
				$this->y += 10;
				
				$order_qty = $item->getQty_ordered();
				$bundle_qty = $bundle->getSelection_qty();
				$f_bundled_qty = $order_qty * $bundle_qty;
				
				$offset = $this->DrawMultilineText($page, '('.round($f_bundled_qty).')   '.$bundle->getName(), 20, $this->y , 7, 0.2, 11);
				$this->y -= $this->_ITEM_HEIGHT + $offset;
		    }
			 
		}  
		 
		/***************** End For bundle product 11_02_2014  *********************/
		   
                //display options
                $optionsText = $item->getOptionsValuesAsText();
                if ($optionsText != '') {
                    $this->y += 10;
                    $offset = $this->DrawMultilineText($page, $optionsText, 105, $this->y, 10, 0.2, 11);
                    $this->y -= $this->_ITEM_HEIGHT + $offset;
                }

                //custom description
                if ($item->getdescription() != '') {
                    $this->y += 10;
                    $description = $item->getdescription();
                    $description = $this->WrapTextToWidth($page, $description, 450);
                    $offset = $this->DrawMultilineText($page, $description, 105, $this->y, 10, 0.2, 11);
                    $this->y -= $this->_ITEM_HEIGHT + $offset;
                }
				
                //new page if required
              //  if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 120)) {
					 if ($ycolmn < ($this->_BLOC_FOOTER_HAUTEUR + 180)) {
                   // $this->drawFooter($page, $settings['store_id']);
                     $page = $this->NewPage($settings, $quoteInfo);
					 $this->y -=60;					
					//  $this->drawProductTableHeader($page, $caption, $y, $quoteInfo);
                   // $this->drawProductTableHeader($page);
                }
				
				 
             }
			
			 
        }
		
		  //Add shipping fees
		  /*
        if ($quote->getfree_shipping() == 1) {
            $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
            $this->DrawMultilineText($page, Mage::helper('quotation')->__('Free Shipping'), 10, $this->y, 10, 0.2, 11);
        } else {
            if ($quote->getshipping_method()) {
                $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
                $this->DrawMultilineText($page, $quote->getshipping_description(), 10, $this->y, 10, 0.2, 11);
                $this->drawTextInBlock($page, $quote->FormatPrice($quote->getShippingCostWithoutTax()), 560, $this->y, 60, 20, 'r');
               $this->drawTextInBlock($page, $quote->FormatPrice($quote->getShippingCostWithTax()), 640, $this->y, 60, 20, 'r');
            }
        }
		*/
		 

        return true;
    }
	
	
	/**
     * Add listing products part
     *
     * @param Zend_Pdf_Page $page
     * @param MDN_Quotation_Model_Quotation $quote
     * @param Zend_Pdf_Style $style
     * @return int
     */
    protected function drawListingOrderProducts(&$page, $quote, $style, $settings, $quoteInfo='') 
	{

       // $this->drawTableHeader($page);		
		$type = $quoteInfo['quote_type'];
		if($type=='Quote'){
		$collection = $quote->getItems();
		}else{
		$collection = $quote->getAllItems();	
			}
		
        $needBundle = Mage::getModel('Quotation/Quotation_Bundle')->needBundleProduct($quote,$quoteInfo);
	 	$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
		
		/* draw quote*/		 
	 
	 if ($needBundle && ($quote->getPrice())) {           
			 
			$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
            $page->drawText($this->TruncateTextToWidth($page, '', 60), 15, $this->y, 'UTF-8');
           
		    $caption = $this->WrapTextToWidth($page, $quote->getName(), 200);
            $caption .= $this->getConfigContentAsText($quote);
            $caption = $this->WrapTextToWidth($page, $caption, 450);
            
			$this->drawTextInBlock($page, $this->TruncateTextToWidth($page, $quote->GetLinkedProduct()->getsku(), 70), 10, $this->y, 40, 20, 'l');
            $offset = $this->DrawMultilineText($page, $caption, 90, $this->y, 10, 0.2, 11);
            $this->drawTextInBlock($page, 1, 275, $this->y, 40, 20, 'c'); //qty
            $this->drawTextInBlock($page, $quote->GetConfigFormatedPriceWithoutTaxes(), 490, $this->y, 60, 20, 'r');
            $this->drawTextInBlock($page, $quote->GetConfigFormatedPriceWithoutTaxes(), 560, $this->y, 60, 20, 'r');
            $this->drawTextInBlock($page, $quote->GetConfigFormatedPriceWithTaxes(), 640, $this->y, 60, 20, 'r');
            $this->y -= $this->_ITEM_HEIGHT + $offset;
       
	    }   
		 
		 
        
		foreach ($collection as $item) {
         $showProduct =0;
		 $parentItems = $this->getParentId($item->getProduct_id());
		 if(count($parentItems)>0){
			 
			 foreach($parentItems as $parentItem){
				 if($parentItem==$item->getProduct_id()){
					 $parent_id = $item->getProduct_id();
					 }
				 }		 
			 }
		   
		  if($parent_id == $item->getProduct_id() && $item->getProductType()=='bundle'){		 
			$showProduct=1;				 		 
		  }
		  if($parent_id == false && $item->getProductType()!='bundle'){		 
			$showProduct=1;	
			 		 
		  }
		  
		//var_dump($showProduct);
		if($showProduct==1){			
		 		
		if ($item->getPrice() ) {	
				$y = $this->y;
				$caption = $this->WrapTextToWidth($page, $item->getName(), 200);				
				///inserting table headers
			 	$this->drawProductTableHeader($page, $caption, $y);				 
				$this->y -= 80;        		 
				$page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
				$page->drawRectangle(10, $this->y-12, 590, $this->y+25, Zend_Pdf_Page::SHAPE_DRAW_FILL);
				$this->y += 7;				
				$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
                $page->drawText($this->TruncateTextToWidth($page, $item->getreference(), 60), 15, $this->y, 'UTF-8');
                $caption = $this->WrapTextToWidth($page, $item->getName(), 200);
                $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
			    $this->drawTextInBlock($page, $this->TruncateTextToWidth($page, $item->getSku(), 70), 285, $this->y, 40, 20, 'l');
                $ycolmn = $this->y;
				//$offset = $this->DrawMultilineText($page, $ycolmn, 15, $this->y, 10, 0.2, 11);
				
			   $offset = $this->DrawMultilineText($page, $caption, 15, $this->y, 10, 0.2, 11);		
               $this->drawTextInBlock($page, $quote->FormatPrice($item->getPrice()), 480, $this->y, 60, 20, 'r');
               // $this->drawTextInBlock($page, $item->getQty(), 310, $this->y, 40, 20, 'c');
               
			    $this->drawTextInBlock($page, round($item->getQty_ordered()), 470, $this->y, 40, 20, 'l');
                 
				 
				if ($item->getPrice() ) {
                    //if ($item->getdiscount_purcent() > 0)
                       // $this->drawTextInBlock($page, $item->getdiscount_purcent() . '%', 330, $this->y, 20, 20, 'r');
                     //$this->drawTextInBlock($page, $quote->FormatPrice($item->GetUnitPriceWithoutTaxes($quote)), 370, $this->y, 60, 20, 'r');
                    $this->drawTextInBlock($page, $quote->FormatPrice($item->getRow_total()), 640, $this->y, 60, 20, 'r');
                    //$this->drawTextInBlock($page, $quote->FormatPrice($item->GetTotalPriceWithTaxes($quote)), 640, $this->y, 60, 20, 'r');
                }
                $this->y -= $this->_ITEM_HEIGHT + $offset;
				$this->y -=5;
		
		/***************** Start For bundle product 11_02_2014  *********************/
	 
	
	 	if($item->getProductType() == 'bundle')
		{
		
		$_product = Mage::getModel('catalog/product')->load($item->getProduct_id());  			
		$bundleCollections = $_product->getTypeInstance($_product)
									  ->getChildrenIds($_product->getId(),false); 		
		
		$bundle_products = array();
		foreach($bundleCollections as $_bundle_products){
				
				foreach($_bundle_products as $_bundle_product){
					  $bundle_products[] = Mage::getModel('catalog/product')->load($_bundle_product);
					}
			
			}		
		$selections = $_product->getTypeInstance(true)
                          ->getSelectionsCollection($_product->getTypeInstance(true)
                          ->getOptionsIds($_product), $_product);
			
		    foreach($selections as $bundle)
		    {
				
				$this->y += 10;				
				
				$order_qty = $item->getQty_ordered();
				$bundle_qty = $bundle->getSelection_qty();
				$f_bundled_qty = $order_qty * $bundle_qty;
				
				//$bundleCaption = round($item->getQty_ordered()).'  x  '.$bundle->getName();
				$bundleCaption = round($f_bundled_qty).'  x  '.$bundle->getName();
				//var_dump($bundle->getData());
				//var_dump('<br/>'.$bundleCaption);
				
				$offset = $this->DrawMultilineText($page, $bundleCaption, 20, $this->y , 7, 0.2, 11);
				$this->y -= $this->_ITEM_HEIGHT + $offset;
		    }
		}  
		
		/***************** End For bundle product 11_02_2014  *********************/
		   
        //display options
        $optionsText = $item->getOptionsValuesAsText();
           if ($optionsText != '') {
                    $this->y += 10;
                    $offset = $this->DrawMultilineText($page, $optionsText, 105, $this->y, 10, 0.2, 11);
                    $this->y -= $this->_ITEM_HEIGHT + $offset;
           }
           //custom description
           if ($item->getDescription() != '') {
                    $this->y += 10;
                    $description = $item->getDescription();
                    $description = $this->WrapTextToWidth($page, $description, 450);
                    $offset = $this->DrawMultilineText($page, $description, 105, $this->y, 10, 0.2, 11);
                    $this->y -= $this->_ITEM_HEIGHT + $offset;
                }				
           //new page if required
           //  if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 120)) {
		   if ($ycolmn < ($this->_BLOC_FOOTER_HAUTEUR + 180)) {
                   // $this->drawFooter($page, $settings['store_id']);
                     $page = $this->NewPage($settings, $quoteInfo);
					 $this->y -=60;					
					//  $this->drawProductTableHeader($page, $caption, $y, $quoteInfo);
                   // $this->drawProductTableHeader($page);
                }
				
				
    	 }
		}
		 
	}
	
		
		  //Add shipping fees
        
		/*
		if ($quote->getfree_shipping() == 1) {
            $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
            $this->DrawMultilineText($page, Mage::helper('quotation')->__('Free Shipping'), 10, $this->y, 10, 0.2, 11);
        } else {
            if ($quote->getshipping_method()) {
                $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
                $this->DrawMultilineText($page, $quote->getshipping_description(), 10, $this->y, 10, 0.2, 11);
                $this->drawTextInBlock($page, $quote->FormatPrice($quote->getShippingCostWithoutTax()), 560, $this->y, 60, 20, 'r');
               $this->drawTextInBlock($page, $quote->FormatPrice($quote->getShippingCostWithTax()), 640, $this->y, 60, 20, 'r');
            }
        }
		 */
   
        return true;
    }


    /**
     * Return bundle in string
     */
    public function getConfigContentAsText($quote, $quoteInfo='') {

        $retour = '';
        foreach ($quote->getItems() as $item) {
            if ($item->getexclude() == 0) {
                $retour .= "\n" . $item->getqty() . 'x ' . $item->getcaption();

                //add product options
                $product = $item->getProduct();
                if ($product->gethas_options() == 1) {
                    foreach ($item->getOptionsCollection() as $option) {
                        $optionValue = $item->getOptionValueAsText($option->getId());
                        if ($optionValue != '')
                            $retour .= "\n......... " . $option->gettitle() . ' : ' . $optionValue;
                    }
                }

                //add custom description
                if ($item->getdescription() != '') {
                    $description = "\n" . $item->getdescription();
                    $retour .= $description;
                }
            }
        }
        return $retour;
    }

    /**
     * Add business proposal part
     *
     * @param Zend_Pdf_Page $page
     * @param MDN_Quotation_Model_Quotation $quote
     * @return int
     */
    protected function drawBusinessProposal(&$page, $quote, $settings, $quoteInfo) {

        $proposal = $quote->getbusiness_proposal();

        $xml = new DomDocument();
        $xml->loadXML($proposal);

        if ($proposal != null && $proposal != '' && $xml->getElementsByTagName(MDN_Quotation_Helper_Proposal::kSectionNode)->item(0)) {

          //  $this->drawFooter($page, $quote->getStoreId());
            $page = $this->NewPage($settings);
          //  $this->drawFooter($page, $storeId);

            $this->y -= 30;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));

            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 24);
            $this->drawTextInBlock($page, Mage::Helper('quotation')->__('Business Proposal'), 0, $this->y, $this->_PAGE_WIDTH - 80, 50, 'c');

            $this->y -= 30;

            foreach ($xml->getElementsBytagName(MDN_Quotation_Helper_Proposal::kSectionNode) as $section) {

                // add title
                $this->y -= 10;
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 20);
                $page->drawText($section->getElementsByTagName(MDN_Quotation_Helper_Proposal::kTitleNode)->item(0)->nodeValue, 15, $this->y, 'UTF-8');
                $this->y -= 30;
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);

                // add content
                if ($section->getElementsByTagName(MDN_Quotation_Helper_Proposal::kModeNode)->item(0)->nodeValue == MDN_Quotation_Helper_Proposal::kModeList) {

                    $lines = explode("\n", $section->getElementsByTagName(MDN_Quotation_Helper_Proposal::kContentNode)->item(0)->nodeValue);

                    foreach ($lines as $line) {

                        if (trim($line) == '')
                            continue;

                        $line = $this->WrapTextToWidth($page, $line, 520);
                        $page->drawCircle(30, $this->y + 3, 2);
                        $t_line = explode("\n", $line);

                        foreach ($t_line as $elt) {
                            $page->drawText($elt, 55, $this->y, 'UTF-8');
                            $this->y -= 15;
                        }

                        $this->y -= 7;

                        //if we reach page footer, new page
                        if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 150)) {
                          //  $this->drawFooter($page, $settings['store_id']);
                            $page = $this->NewPage($settings, $quoteInfo);
                            $this->y -= 30;
                            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
                            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
                        }
                    }
                } else {
                    $content = $this->WrapTextToWidth($page, $section->getElementsByTagName(MDN_Quotation_Helper_Proposal::kContentNode)->item(0)->nodeValue, 520);
                    $t_content = explode("\n", $content);
                    for ($i = 0; $i < count($t_content); $i++) {

                        $line = $t_content[$i];

                        $page->drawText($line, 30, $this->y, 'UTF-8');

                        $this->y -= 15;

                        //if we reach page footer, new page
                        if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 50)) {
                           // $this->drawFooter($page, $settings['store_id']);
                            $page = $this->NewPage($settings, $quoteInfo);
                            $this->y -= 30;
                            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
                            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
                        }
                    }
                }

                $this->y -= 30;
            }
        }

        return true;
    }

    /**
     * Add Draw totals
     *
     * @param Zend_Pdf_Page $page
     * @param MDN_Quotation_Model_Quotation $quote
     * @return int
     */
    protected function drawTotals($page, $quote, $quoteInfo='') {

        $ycolumn = $this->y;
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
	    $page->drawRectangle((($this->_PAGE_WIDTH / 2)-10), $ycolumn, 590, $ycolumn-115, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		$this->y -= 20;
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);

       // $this->y -= 20;
        //$page->drawText(Mage::helper('quotation')->__('Totals'), 15, $this->y, 'UTF-8');
        //$this->y -= 5;
        //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
       // $this->y -= 20;
        //$page->drawText(Mage::helper('quotation')->__('Subtotal:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithoutTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        
        $page->drawText(Mage::helper('quotation')->__('Subtotal:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithoutTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        
        $this->y -= 25;

       // $page->drawText(Mage::helper('quotation')->__('Tax'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
       // $this->drawTextInBlock($page, $quote->FormatPrice($quote->GetTaxAmount()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
       // $this->y -= 25;
       
        $page->drawText(Mage::helper('quotation')->__('Shipping & Handling: '), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $quote->FormatPrice($quote->getShippingRate()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        $this->y -= 25;
		
	//start 11_02_2014
	$page->drawText(Mage::helper('quotation')->__('Tax: '), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $quote->FormatPrice($quote->GetTaxAmount()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        $this->y -= 25;
	//End 11_02_2014

       // $page->drawText(Mage::helper('quotation')->__('Total (incl tax)'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 16);
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ec4d53'));
        $page->drawText(Mage::helper('quotation')->__('Grand Total:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithOutTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->getPriceHt()+$quote->getShippingRate()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
	
	
	$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithTaxes()), $this->_PAGE_WIDTH / 2 + 85, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');//11_02_2014

        $this->y -= 20;	
		

        return true;
    }
	
	/**
     * Add Draw totals
     *
     * @param Zend_Pdf_Page $page
     * @param MDN_Quotation_Model_Quotation $quote
     * @return int
     */
    protected function drawOrderTotals($page, $quote, $quoteInfo='') {

         
		$ycolumn = $this->y+25;
		
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
	    $page->drawRectangle((($this->_PAGE_WIDTH / 2)-10), $ycolumn, 590, $ycolumn-90, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		$this->y += 5;
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);

       // $this->y -= 20;
        //$page->drawText(Mage::helper('quotation')->__('Totals'), 15, $this->y, 'UTF-8');
        //$this->y -= 5;
        //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
       // $this->y -= 20;
        //$page->drawText(Mage::helper('quotation')->__('Subtotal:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithoutTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        
        $page->drawText(Mage::helper('quotation')->__('Subtotal:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $quote->FormatPrice($quote->getSubtotal()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        
        $this->y -= 20;

       // $page->drawText(Mage::helper('quotation')->__('Tax'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
       // $this->drawTextInBlock($page, $quote->FormatPrice($quote->GetTaxAmount()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
       // $this->y -= 25;
       
        $page->drawText(Mage::helper('quotation')->__('Shipping & Handling: '), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $quote->FormatPrice($quote->getShipping_amount()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        $this->y -= 20;
		
	//start 11_02_2014
	$page->drawText(Mage::helper('quotation')->__('Tax: '), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $quote->FormatPrice($quote->getTax_amount()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        $this->y -= 20;
	//End 11_02_2014

       // $page->drawText(Mage::helper('quotation')->__('Total (incl tax)'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 16);
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ec4d53'));
        $page->drawText(Mage::helper('quotation')->__('Grand Total:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $quote->FormatPrice($quote->getGrand_total()), $this->_PAGE_WIDTH / 2 + 85, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');//11_02_2014	
       
	   
	   /////drawing paid stamp 
	   
	  	
		
	   
	   ///adding if paid or partial paid ////
	    $this->y -= 20;
	    
		$page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
	    $page->drawRectangle((($this->_PAGE_WIDTH / 2)-10), $this->y, 590, $this->y-70, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		
		$grandTotal = $quote->FormatPrice($quote->getGrand_total());
		$totalDue = $quote->FormatPrice($quote->getTotal_due());
		$totalPaid = $quote->FormatPrice($quote->getTotal_paid());
		$totalBalance = $quote->getGrand_total()-$quote->getTotal_paid();
		$totalBalance = $quote->FormatPrice($totalBalance);
		
		if( $quote->getTotal_due() <= 0){
		 $_image = Mage::getBaseDir('media').'/stamped_invoice.png';
		 $_image = Zend_Pdf_Image::imageWithPath($_image);
         $page->drawImage($_image, 100, $this->y-80, 500, $this->y+171 );
		}
		$this->y -= 20;
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 12);
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ec4d53'));
        $page->drawText(Mage::helper('quotation')->__('Total Due:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $totalDue, $this->_PAGE_WIDTH / 2 + 25, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');//11_02_2014	
	    $this->y -= 18;
		$page->setFillColor(Zend_Pdf_Color_Html::color('#1a910e'));
		$page->drawText(Mage::helper('quotation')->__('Total Paid:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $totalPaid, $this->_PAGE_WIDTH / 2 + 25, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');//11_02_2014	
	    $this->y -= 18;		
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#666666'));
		$page->drawText(Mage::helper('quotation')->__('Balance:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $totalBalance, $this->_PAGE_WIDTH / 2 + 25, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');//11_02_2014	
	    
		
		//$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithOutTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->getPriceHt()+$quote->getShippingRate()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
	
	//$target = Zend_Pdf_Action_URI :: create( 'http://example.com' );	
		
		/*
		$target = Zend_Pdf_Action_URI :: create( 'http://example.com' );
		$annotation = Zend_Pdf_Annotation_Link :: create( 0, 0, 100, 100, $target );
		$pdf->pages[0]->attachAnnotation( $annotation );
		$pdf->save( 'test.pdf' );
		*/
	
		 $this->y -= 20;
		
		///adding payment info 
		
		

        return true;
    }

    /**
     * Add agreement part
     *
     * @param Zend_Pdf_Page $page
     * @return int
     */
    protected function drawAgreement_old(&$page, $settings, $quoteInfo='') {

        $this->y -= 40;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
        $page->drawText(Mage::helper('quotation')->__('Agreement'), 15, $this->y, 'UTF-8');
        $this->y -= 5;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        $this->y -= 15;
        $agreement = $this->WrapTextToWidth($page, Mage::getStoreConfig('quotation/pdf/agreement', $settings['store_id']), 600);

        //cutting $agreement
        $all_agreement = explode("\n", $agreement);

        foreach ($all_agreement as $value) {

            if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
               // $this->drawFooter($page);
                $page = $this->NewPage($settings, $quoteInfo);
                $this->y -= 25;
                $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                $this->y -= 11;
            } else {
                $height_text = $this->getMultilineTextHeight($page, $value . "\n", 10, 11);

                if ($height_text + 50 < $this->y) {
                    $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                    $this->y -= 11;
                } else {
                  //  $this->drawFooter($page);
                    $page = $this->NewPage($settings, $quoteInfo);
                    $this->y -= 25;
                    $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                    $this->y -= 11;
                }
            }
        }

        return true;
    }
	
	/**
     * Add agreement part
     *
     * @param Zend_Pdf_Page $page
     * @return int
     */
    protected function drawAgreement(&$page, $settings, $quoteInfo='') {

        $page = $this->NewPage($settings, $quoteInfo);
		$total_amount = $quoteInfo['total_amount'];
	    $quote_id = $quoteInfo['data']['increment_id'];
		
		
		//  $link = $quoteInfo['store_url'].'myauth/order_id/'.$quoteInfo['data']['increment_id'];
		  $incrementId  = $quoteInfo['data']['increment_id'];
		  $order_id 	= $quoteInfo['data']['entity_id'];
		  $customerid 	= $quoteInfo['data']['customer_id'];
		  $storeId 		= $quoteInfo['data']['store_id'];
		 
		 $hash  =md5('vividexhibits'.$incrementId.$customerid);
		   $order_type = $quoteInfo['quote_type'];
		   
		   if($order_type=='Quote'){
			   $order_type = 'quote_id';
			   }else{
				 $order_type = 'order_id'; 
			}
		 		  
		 // $hash = md5('vividads Melbourne Australia'.$incrementId.$customerid.$storeId.$order_id);
		 $link = $quoteInfo['store_url'].'autologin/directauth/order/'.$order_type.'/'.$quoteInfo['data']['increment_id'].'/SID/'.$hash;
		 // var_dump($quoteInfo['data']['increment_id']);exit;
		
	
		/*
		// $hash = md5('vividads Melbourne Australia'.$incrementId.$customerid.$storeId.$order_id);
		   $hash  =md5('vividexhibits'.$incrementId.$customerid);
		   $order_type = $quoteInfo['quote_type'];
		   
		   if($order_type=='Quote'){
			   $order_type = 'quote_id';
			   }else{
				 $order_type = 'order_id'; 
			}
		 
		  $link = $quoteInfo['store_url'].'autologin/directauth/order/'.$order_type.'/'.$quoteInfo['data']['increment_id'].'/SID/'.$hash;
		
		*/
		
		
		$this->y -=70;
		
		//$this->y -= 40;
        
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.1));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 26);
        $page->drawText(Mage::helper('quotation')->__('How To Upload Artwork'), (($this->_PAGE_WIDTH / 2)-200), $this->y, 'UTF-8');
        
		
		$this->y -= 10;
		$ycolumn=$this->y;
       // $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        
		//drawing text Headings of Art Ready Artwork below
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ec4d53'));
	    $page->drawRectangle(10, $ycolumn-10, (($this->_PAGE_WIDTH / 2)-150), $ycolumn-45, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 16);
        $page->drawText(Mage::helper('quotation')->__('Artwork ready to go?'), 20, $this->y-32, 'UTF-8');
       
	    ///drawing the text Artwork Ready below
		$ycolumn -= 40;
		$page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
	    $page->drawRectangle(10, $ycolumn, $this->_BLOC_ENTETE_LARGEUR, $ycolumn-65, Zend_Pdf_Page::SHAPE_DRAW_FILL);
	    
		$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 11);
		
		//var_dump($quoteInfo); exit;
		
		$url = $quoteInfo['store_url'].'upload';	
		$_text = 'Please go to \''.$url.'\' and enter your quote or invoice number \''.$quote_id.'\' to upload your artwork against relevant item. Once your arwork has been uploaded please ensure you have paid for the project for us to start printing job. You would receive proofs to approve before we print any job.';
		
		$_n_text = $this->WrapTextToWidth($page, $_text, $this->_BLOC_ENTETE_LARGEUR-20);		
		$this->DrawMultilineText($page, $_n_text, 18, $ycolumn-18, 11, 0.2, 14);
		//$this->DrawMultilineText($_n_text, 15, $ycolumn-15, 'UTF-8');
	   
	   
	    ///hyper link
	   // $url_link = $quoteInfo['store_url'].'upload/index/fetchOrder/?'.$order_type.'='.$quote_id;
		$url_link = $quoteInfo['store_url'].'upload';
		$target = Zend_Pdf_Action_URI :: create( $url_link );		
		$ycolumn -= -10;
		$annot = Zend_Pdf_Annotation_Link :: create( $ycolumn-610, $ycolumn-15, $ycolumn-460, $ycolumn-35, $target );
		//var_dump($ycolumn);
		 $annot->getResource()->Border = new Zend_Pdf_Element_Array() ;
		  //  var_dump($annot->getResource()->toString());exit;
		//  var_dump($annotation);exit;
		$page->attachAnnotation( $annot );
	  
	    $ycolumn -= 95;
	   //drawing text How to place your order headings
	   
	   $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.1));
       $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 26);
       $page->drawText(Mage::helper('quotation')->__('How to place your order'), (($this->_PAGE_WIDTH / 2)-200), $ycolumn-10, 'UTF-8');
       $ycolumn -= 10; 
	
	  $page->setFillColor(Zend_Pdf_Color_Html::color('#e2ecf0'));
	  $page->drawRectangle(10, $ycolumn-10, $this->_BLOC_ENTETE_LARGEUR, $ycolumn-80, Zend_Pdf_Page::SHAPE_DRAW_FILL);
	  $_pl_text = 'Once you are ready to place your order Click here to access your account and click on the convert to invoice button to convert your quote to an invoice automatically. Click on the invoice tab to view and make a payment. Alternatively you can use the following options to make your payment. We start to process your order as soon as payment has been confirmed.';
	   
	  $_n_pl_text = $this->WrapTextToWidth($page, $_pl_text, 1450);		
	  $this->DrawMultilineText($page, $_n_pl_text, 18, $ycolumn-25, 11, 0.2, 14);
	  $ycolumn -= 110; 
	  
	  
	   ///hyper link
	   
		$target = Zend_Pdf_Action_URI :: create( $url);		
		$ycolumn -= -10;
		$annot1 = Zend_Pdf_Annotation_Link :: create( $ycolumn-275, $ycolumn+70, $ycolumn-215, $ycolumn+85, $target );
		//var_dump($ycolumn);
		  $annot1->getResource()->Border = new Zend_Pdf_Element_Array() ;
		  //  var_dump($annot->getResource()->toString());exit;
		//  var_dump($annotation);exit;
		$page->attachAnnotation( $annot1 );
	  
	  
	  	
		//drawing text Payment Methods order headings
	   
	   $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.1));
       $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 26);
       $page->drawText(Mage::helper('quotation')->__('Payment Methods'), (($this->_PAGE_WIDTH / 2)-200), $ycolumn-10, 'UTF-8');
     
	 $ycolumn -= 20;
	 
	 ////adding Direct deposit block headings
	 
	 $_block_text = array('text'=>'Direct Deposit','bgcolor'=>'#ec4d53', 'fcolor'=>'#ffffff','type'>='single');	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-6, 180, 30, 16, 14,$_block_text['type'] );
	 
	 $ycolumn -= 30;
	
	
	 ////adding Direct deposit text body block
	 $_text = 'Please direct deposit your payment to our account in the amount of \'AUD '.$total_amount.'\' with your reference ID /              Job ID:  \' '.$quote_id.' \'. 
	 
	 Beneficiary:      Vivid Ads Pty Ltd    
	 Bank Name:     Westpac     
	 BSB:                 033018     
	 Accountt:          301881 ';
	 $_block_text = array('text'=>$_text,'bgcolor'=>'#e2ecf0', 'fcolor'=>'#000000','type'>='multiple');	 	 
	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-2, $this->_BLOC_ENTETE_LARGEUR, 120, 11, 14, $_block_text['type'] );
	   
	   
	   ////adding Cheque Payment / Cash Payment block headings
	 $ycolumn -= 130;
	 $_block_text = array('text'=>'Cheque Payment / Cash Payment','bgcolor'=>'#ec4d53', 'fcolor'=>'#ffffff','type'>='single');	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-6, 300, 30, 16, 14,$_block_text['type'] );
	   
	   $store_name = $quoteInfo['store_name'];
	    $ycolumn -= 35;
	   ////adding Cheque Payment / Cash Payment text body block
	 $_text = 'The cheque would be made payable to \''.$store_name.'\' and mailed to "Vivid Ads 1/2 Phillip Court ,Port Melbourne, Victoria, 3207 Australia.
Cash payment can be made at the facility for any purchased goods/services';
	 $_block_text = array('text'=>$_text,'bgcolor'=>'#e2ecf0', 'fcolor'=>'#000000','type'>='multiple');	 	 
	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-2, $this->_BLOC_ENTETE_LARGEUR, 60, 11, 14, $_block_text['type'] );
	  
	  
	 
	/*creating new page*/
	 $page = $this->NewPage($this->_settings, $quoteInfo);
	
	
	 ////adding Purchase Order block headings
	 $ycolumn =$this->y;
	 $ycolumn -= 55;
	 
	 
	  ////adding Purchase Order block headings
	 $ycolumn -= 0;
	 $_block_text = array('text'=>'International Wire Transfer','bgcolor'=>'#ec4d53', 'fcolor'=>'#ffffff','type'>='single');	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-6, 270, 30, 16, 14,$_block_text['type'] ); 
	 
	 $ycolumn -= 30;
	   ////adding Cheque Payment / Cash Payment text body block
	 $_text = 'Please direct deposit your payment to our account in the amount of \'AUD '.$total_amount.'\'  with your Reference ID / Job ID:\' '.$quote_id.'\'.

    Beneficiary:        Vivid Ads Pty Ltd    
    Bank Name:       Westpac    
    BSB:                   033018    
    Account:             301881    
    Swift Code:         WPACAU2S
Bank Address :  156 Bay Street,Port Melbourne,Victoria Australia.
';
	 $_block_text = array('text'=>$_text,'bgcolor'=>'#e2ecf0', 'fcolor'=>'#000000','type'>='multiple');	 	 
	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-2, $this->_BLOC_ENTETE_LARGEUR, 145, 11, 14, $_block_text['type'] );
	   
	 $ycolumn -= 150;
	 
	 
	 $_block_text = array('text'=>'Purchase Order','bgcolor'=>'#ec4d53', 'fcolor'=>'#ffffff','type'>='single');	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-6, 180, 30, 16, 14,$_block_text['type'] );  
	   
	 $ycolumn -= 45;
	   ////adding Cheque Payment / Cash Payment text body block
	 $_text = 'Please fax your purchase orders to 03 8456 6234.Please ensure to include your Job ID:\' '.$quote_id.'\'.';
	 $_block_text = array('text'=>$_text,'bgcolor'=>'#e2ecf0', 'fcolor'=>'#000000','type'>='multiple');	 	 
	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-2, $this->_BLOC_ENTETE_LARGEUR, 35, 11, 14, $_block_text['type'] );
	    
	////adding Online Payment block headings	
	 $ycolumn -= 55;
	 
	 $_block_text = array('text'=>'Online Payment','bgcolor'=>'#ec4d53', 'fcolor'=>'#ffffff','type'>='single');	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-6, 180, 30, 16, 14,$_block_text['type'] );  
	   
	 $ycolumn -= 35;
	   ////adding Cheque Payment / Cash Payment text body block
	 $_text = 'You can directly make your payment online.Click on the link at the end of this form to make your payment. Job ID \''.$quote_id.'\' Total Due \'AUD '.$total_amount.'\'';
	 $_block_text = array('text'=>$_text,'bgcolor'=>'#e2ecf0', 'fcolor'=>'#000000','type'>='multiple');	 	 
	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-2, $this->_BLOC_ENTETE_LARGEUR, 45, 11, 14, $_block_text['type'] );   
	   
	   
	   ////adding Online Payment block headings
	
	 $ycolumn -= 65;
	 
	 $_block_text = array('text'=>'Payment Over Phone (Master/Visa/Amex)','bgcolor'=>'#ec4d53', 'fcolor'=>'#ffffff','type'>='single');	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-6, 400, 30, 16, 14,$_block_text['type'] );  
	   
	 $ycolumn -= 35;
	   ////adding Cheque Payment / Cash Payment text body block
	 $_text = 'Payment can be made over the phone using your Master / Visa / Amex cards (3% surcharge on Amex only). Call \''.$quoteInfo['store_phone'].'\' (choose option Accounts). 
	 	 
	 Invoice ID \''.$quote_id.'\' Total Due AUD \''.$total_amount.'\' .';
	 $_block_text = array('text'=>$_text,'bgcolor'=>'#e2ecf0', 'fcolor'=>'#000000','type'>='multiple');	 	 
	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 10, $ycolumn-2, $this->_BLOC_ENTETE_LARGEUR, 75, 11, 14, $_block_text['type'] );  
	   
	 $ycolumn -= 105;
	 
	 $quote_type = $quoteInfo['quote_type'];
	 if($quote_type=='Quote'){
		 $type = 'Quote';
		 
		 }else{
		$type='Invoice';	 
			}
	 
	 ////adding Cheque Payment / Cash Payment text body block
	 $_text = 'To place your order online or request any changes to this '.$type.' please click here';
	 $_block_text = array('text'=>$_text,'bgcolor'=>'', 'fcolor'=>'#255fdc','type'>='single'); 
	 
	 $this->addBlockText($page, $_block_text['text'], $_block_text['bgcolor'], $_block_text['fcolor'], 0, $ycolumn-2, $this->_BLOC_ENTETE_LARGEUR, 60, 14, 14, $_block_text['type'] );  
	  ///hyper link
	  
		$target = Zend_Pdf_Action_URI :: create( $link );		
		$ycolumn -= +90;
		$annotation = Zend_Pdf_Annotation_Link :: create( $ycolumn+350, $ycolumn+55, $ycolumn+440, $ycolumn+85, $target );
		$annotation->getResource()->Border = new Zend_Pdf_Element_Array() ;
		  // var_dump($annotation->getResource()->toString());exit;
		// var_dump($annotation);exit;
		$page->attachAnnotation( $annotation );
	  
	  // $cord= '('.($ycolumn+170).','.($ycolumn+55).')'.'('.($ycolumn+240).','.($ycolumn+85).')';
	    //   var_dump($cord);exit;
	   ////agreeement dynamic 
	   
	    $agreement = $this->WrapTextToWidth($page, Mage::getStoreConfig('quotation/pdf/agreement', $settings['store_id']), 600);
		$this->y -= 15;

        //cutting $agreement
        $all_agreement = explode("\n", $agreement);

        foreach ($all_agreement as $value) {

            if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
              //  $this->drawFooter($page);
                $page = $this->NewPage($settings, $quoteInfo);
                $this->y -= 25;
                $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                $this->y -= 11;
            } else {
                $height_text = $this->getMultilineTextHeight($page, $value . "\n", 10, 11);

                if ($height_text + 50 < $this->y) {
                    $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                    $this->y -= 11;
                } else {
                  //  $this->drawFooter($page);
                    $page = $this->NewPage($settings, $quoteInfo);
                    $this->y -= 25;
                    $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                    $this->y -= 11;
                }
            }
        }

        return true;
    }
	
	/*get if product is simple and has no children*/
	public function hasChildren($product=0){
		
		$_product = Mage::getModel('catalog/product')->load($product->getProduct_id());  			
		$bundleCollections = $_product->getTypeInstance($_product)
										    ->getChildrenIds($_product->getId(),false); 		
		
			
		if(count($bundleCollections)>0){
			return true;
			}					
		
		return false;
		}
		
		/*get if product is return parent*/
	public function getParentId($product_id=0){
		
		$_product = Mage::getModel('catalog/product')->load($product_id);  			
		$bundleCollections = $_product->getTypeInstance($_product)
										    ->getChildrenIds($_product->getId(),false); 		
		if(count($bundleCollections)>0){			 
			foreach($bundleCollections as $_bundle_products){
					  
					  foreach($_bundle_products as $_bundle_product){
					    $bundle_products[Mage::getModel('catalog/product')->load($_bundle_product)
					   	->getId()]=$product_id;	
					
					  }
				}
				return $bundle_products;
			}
			
								
		
		return false;
		}
	
	
	}//end of class
