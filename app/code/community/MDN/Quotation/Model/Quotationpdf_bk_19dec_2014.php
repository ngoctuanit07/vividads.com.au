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

        //if has business proposal, add the first page
        if ($quote->hasBusinessProposal()) {

            $page = $this->NewPage($settings);

            // main page
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 32);
            $this->drawTextInBlock($page, $title, 0, $this->_PAGE_WIDTH / 2 + 50, $this->_PAGE_WIDTH - 80, 50, 'c');
            $this->drawTextInBlock($page, $quote->getcaption(), 0, $this->_PAGE_WIDTH / 2, $this->_PAGE_WIDTH - 80, 50, 'c');
            $this->drawFooter($page, $storeId);

            // add business proposal
            $this->drawBusinessProposal($page, $quote, $settings);

            // new page
            $this->drawFooter($page, $storeId);
        }


        //$settings['title'] = $title;
        $this->_settings = $settings;
        $page = $this->NewPage($settings);

        //Header
	
        $txt_date = Mage::helper('quotation')->__('Quote Date : %s', Mage::helper('core')->formatDate($quote->getcreated_time(), 'long'));
       // $txt_quote = Mage::helper('quotation')->__('Quotation valid until %s', Mage::helper('core')->formatDate($quote->getvalid_end_time(), 'long'));
        $adresse_fournisseur = Mage::getStoreConfig('sales/identity/address', $storeId);
        if ($quote->GetCustomerAddress() != null)
            $adresse_client = $this->FormatAddress($quote->GetCustomerAddress(), '', false);
        else {
            $adresse_client = $quote->GetCustomer()->getName();
        }
        /********************** Start by dev **********************************/
        $this->AddAddressesBlock($page, $adresse_fournisseur, $txt_date."\n".$title,  '');
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
	
        
        
        
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
       // $this->drawTextInBlock($page, $billing, 30, 660, 200,150,'l');
        
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
	
        
	
	if($fetchSystem['telephone'] != '')
        {
            $page->setFillColor(Zend_Pdf_Color_Html::color('#A9A7A7'));
            $this->drawTextInBlock($page, $fetchSystem['telephone'], 310, 600, 200,150,'l');
        }
	
        
        //$page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        //$this->drawTextInBlock($page, $shipping, 310, 660, 200,150,'l');
        
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        
        /********************** End by dev **********************************/

        // add listing products
        $this->drawListingProducts($page, $quote, $style, $settings);

        //new page if required
        if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 100)) {
            $this->drawFooter($page, $settings['store_id']);
            $page = $this->NewPage($settings);
            $this->drawTableHeader($page);
        }
	
	$this->y -= 15;
	
		$page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
        $page->drawRectangle(21, $this->y+25, 270, $this->y, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),8);
        $this->drawTextInBlock($page, 'Shipping Method:', 30, $this->y+10, 200,$this->y+20,'l');
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#FFFFFF'));
        $page->setLineColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
       // $page->drawRectangle(21, $this->y-25, 269, $this->y);
        
        $carrierTitle =  end(explode('_',$quote->getShippingMethod()));
        $ship = Mage::getStoreConfig('carriers/'.$carrierTitle.'/title');
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $this->drawTextInBlock($page, $ship, 25, $this->y-20, 269,$this->y,'l');

        $this->drawTotals($page, $quote);
        //$this->drawAgreement($page, $settings);
        
        $this->y -= 15;
        
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
        
       
      
        
        
        $this->y -= 30;
	
	//$page1 = $this->NewPage($settings);

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
        $this->AddPagination($this->pdf);

        $this->_afterGetPdf();

        return $this->pdf;
    }

    /**
     * Draw products table header
     *
     * @param unknown_type $page
     */
    public function drawTableHeader(&$page) {

        //$this->y -= 15;
        //$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
        //$page->drawText(Mage::helper('quotation')->__('Reference'), 15, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Description'), 90, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Qty'), 285, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Discount'), 310, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Unit Price'), 370, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Subtotal'), 450, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Total'), 530, $this->y, 'UTF-8');
        
        $this->y -= 70;
        
        $page->setFillColor(Zend_Pdf_Color_Html::color('#DCDCDC'));
	    $page->drawRectangle(10, 550, 590, $this->y+20, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),8);
	    $page->drawText(Mage::helper('quotation')->__('Product'), 15, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('quotation')->__('Sku'), 285, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('quotation')->__('Unit Price'), 370, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('quotation')->__('Qty'), 450, $this->y, 'UTF-8');
       // $page->drawText(Mage::helper('quotation')->__('Discount'), 310, $this->y, 'UTF-8');
        
        $page->drawText(Mage::helper('quotation')->__('Subtotal'), 530, $this->y, 'UTF-8');
        //$page->drawText(Mage::helper('quotation')->__('Total'), 530, $this->y, 'UTF-8');

        $this->y -= 8;
        //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        $this->y -= 15;
    }

    /**
     * Add listing products part
     *
     * @param Zend_Pdf_Page $page
     * @param MDN_Quotation_Model_Quotation $quote
     * @param Zend_Pdf_Style $style
     * @return int
     */
    protected function drawListingProducts(&$page, $quote, $style, $settings) {

        $this->drawTableHeader($page);

        $collection = $quote->getItems();
        $needBundle = Mage::getModel('Quotation/Quotation_Bundle')->needBundleProduct($quote);
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
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
                $page->drawText($this->TruncateTextToWidth($page, $item->getreference(), 60), 15, $this->y, 'UTF-8');
                $caption = $this->WrapTextToWidth($page, $item->getcaption(), 200);
                $this->drawTextInBlock($page, $this->TruncateTextToWidth($page, $item->getsku(), 70), 285, $this->y, 40, 20, 'l');
                $offset = $this->DrawMultilineText($page, $caption, 10, $this->y, 10, 0.2, 11);
		
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
		    
		    
		    foreach($chkBundle as $bundle)
		    {
			$this->y += 10;
			$offset = $this->DrawMultilineText($page, $bundle['caption'], 20, $this->y , 7, 0.2, 11);
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
                if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
                    $this->drawFooter($page, $settings['store_id']);
                    $page = $this->NewPage($settings);
                    $this->drawTableHeader($page);
                }
            }
        }

        //Add shipping fees
        if ($quote->getfree_shipping() == 1) {
            $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
            $this->DrawMultilineText($page, Mage::helper('quotation')->__('Free Shipping'), 90, $this->y, 10, 0.2, 11);
        } else {
            if ($quote->getshipping_method()) {
                $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
                $this->DrawMultilineText($page, $quote->getshipping_description(), 90, $this->y, 10, 0.2, 11);
                //$this->drawTextInBlock($page, $quote->FormatPrice($quote->getShippingCostWithoutTax()), 560, $this->y, 60, 20, 'r');
                //$this->drawTextInBlock($page, $quote->FormatPrice($quote->getShippingCostWithTax()), 640, $this->y, 60, 20, 'r');
            }
        }

        return true;
    }

    /**
     * Return bundle in string
     */
    public function getConfigContentAsText($quote) {

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
    protected function drawBusinessProposal(&$page, $quote, $settings) {

        $proposal = $quote->getbusiness_proposal();

        $xml = new DomDocument();
        $xml->loadXML($proposal);

        if ($proposal != null && $proposal != '' && $xml->getElementsByTagName(MDN_Quotation_Helper_Proposal::kSectionNode)->item(0)) {

            $this->drawFooter($page, $quote->getStoreId());
            $page = $this->NewPage($settings);
            $this->drawFooter($page, $storeId);

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
                            $this->drawFooter($page, $settings['store_id']);
                            $page = $this->NewPage($settings);
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
                            $this->drawFooter($page, $settings['store_id']);
                            $page = $this->NewPage($settings);
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
    protected function drawTotals($page, $quote) {

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

        $page->drawText(Mage::helper('quotation')->__('Grand Total:'), $this->_PAGE_WIDTH / 2, $this->y, 'UTF-8');
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithOutTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
        
        //$this->drawTextInBlock($page, $quote->FormatPrice($quote->getPriceHt()+$quote->getShippingRate()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');
	$this->drawTextInBlock($page, $quote->FormatPrice($quote->GetFinalPriceWithTaxes()), $this->_PAGE_WIDTH / 2 + 40, $this->y, $this->_PAGE_WIDTH / 2, 40, 'r');//11_02_2014

        $this->y -= 20;

        return true;
    }

    /**
     * Add agreement part
     *
     * @param Zend_Pdf_Page $page
     * @return int
     */
    protected function drawAgreement(&$page, $settings) {

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
                $this->drawFooter($page);
                $page = $this->NewPage($settings);
                $this->y -= 25;
                $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                $this->y -= 11;
            } else {
                $height_text = $this->getMultilineTextHeight($page, $value . "\n", 10, 11);

                if ($height_text + 50 < $this->y) {
                    $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                    $this->y -= 11;
                } else {
                    $this->drawFooter($page);
                    $page = $this->NewPage($settings);
                    $this->y -= 25;
                    $offset = $this->DrawMultilineText($page, $value . "\n", 25, $this->y, 10, 0.2, 11);
                    $this->y -= 11;
                }
            }
        }

        return true;
    }

}
