<?php

abstract class MDN_Quotation_Model_Pdfhelper extends Mage_Sales_Model_Order_Pdf_Abstract {

    protected $_BLOC_ENTETE_HAUTEUR = 50;
    protected $_BLOC_ENTETE_LARGEUR = 585;
    protected $_BLOC_FOOTER_HAUTEUR = 40;
    protected $_BLOC_FOOTER_LARGEUR = 585;	
	protected $_BLOC_ENTETE_HAUTEUR_TOP = 92;
    //protected $_LOGO_HAUTEUR = 50;
    protected $_LOGO_HAUTEUR = 150;//11_02_2014
    protected $_LOGO_LARGEUR = 220;
    protected $_PAGE_HEIGHT = 820;
    protected $_PAGE_WIDTH = 700;
    protected $_ITEM_HEIGHT = 25;
    public $pdf;
    protected $firstPageIndex = 0;

    /**
     * Draw logo
     *
     * @param unknown_type $page
     */
    protected function insertLogo(&$page, $StoreId = null) {
        $image = Mage::getStoreConfig('quotation/pdf/logo', $StoreId);
        if ($image) {
            $image = Mage::getBaseDir('media') . DS . 'upload' . DS . 'image' . DS . $image;
            if (is_file($image)) {
                try {
                    $image = Zend_Pdf_Image::imageWithPath($image);
                } catch (Exception $ex) {
                    throw new Exception('Logo file for PDF is not supported, please use jpeg ou png file');
                }
                //$page->drawImage($image, 10, 780, $this->_LOGO_LARGEUR, 780 + $this->_LOGO_HAUTEUR);
                $page->drawImage($image, 10, 700, $this->_LOGO_LARGEUR-130, 700 + $this->_LOGO_HAUTEUR);//11_02_2014
            }
        }

        return $this;
    }



    /**
     * Calculate multiline text height
     *
     */
    public function getMultilineTextHeight($page, $Text, $Size, $LineHeight) {
        $retour = -$LineHeight;
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $Size);
        foreach (explode("\n", $Text) as $value) {
            if ($value !== '') {
                $retour += $LineHeight;
            }
        }
        return $retour;
    }

    /**
     * Draw multiline text and return total height
     */
    protected function DrawMultilineText($page, $Text, $x, $y, $Size, $GrayScale, $LineHeight) {
        $retour = -$LineHeight;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale($GrayScale));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $Size);
        foreach (explode("\n", $Text) as $value) {
            if ($value !== '') {
                $page->drawText(trim(strip_tags($value)), $x, $y, 'UTF-8');
                $y -=$LineHeight;
                $retour += $LineHeight;

                if (($y < $this->_BLOC_FOOTER_HAUTEUR)) {
                    $savedFont = $page->getFont();
                    $savedFontSize = $page->getFontSize();
                    $this->drawFooter($page, $this->_settings['store_id']);
                    $page = $this->NewPage($this->_settings);
                    $this->drawTableHeader($page);
                    $y = $this->y;
                    $retour = 0;

                    //re apply font (because new page can change font settings
                    $page->setFont($savedFont, $savedFontSize);
                }
            }
        }
        return $retour;
    }

    /**
     * Return text width (considering size & font)
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize) {
        try {
            $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
            $characters = array();
            for ($i = 0; $i < strlen($drawingString); $i++) {
                $characters[] = (ord($drawingString[$i++]) << 8 ) | ord($drawingString[$i]);
            }
            $glyphs = $font->glyphNumbersForCharacters($characters);
            $widths = $font->widthsForGlyphs($glyphs);
            $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
            return $stringWidth;
        } catch (Exception $ex) {
            throw new Exception('Unable to calculate widyj for string ' . $string);
        }
    }

    /**
     * Draw text in a specific box
     *
     */
    public function drawTextInBlock($page, $text, $x, $y, $width, $height, $alignment = 'c', $encoding = 'UTF-8') {
        $text_width = $this->widthForStringUsingFontSize($text, $page->getFont(), $page->getFontSize());
        switch ($alignment) {
            case 'c': //center text
                $x = $x + ($width / 2) - $text_width / 2;
                break;
            case 'r': //right align
                $x = $x + $width - $text_width;
        }

        $page->drawText(trim(strip_tags($text)), $x, $y, $encoding);
    }

    /**
     * Draw footer
     *
     * @param unknown_type $page
     */
    public function drawFooter($page, $StoreId = null, $quote='') {
        //Background
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.7));
        $page->drawRectangle(10, $this->_BLOC_FOOTER_HAUTEUR + 15, $this->_BLOC_FOOTER_LARGEUR, 15, Zend_Pdf_Page::SHAPE_DRAW_FILL);

        //text
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
        $this->DrawFooterMultilineText($page, Mage::getStoreConfig('quotation/pdf/pdf_footer', $StoreId), 20, $this->_BLOC_FOOTER_HAUTEUR, 10, 0, 15);
    }

    /**
     * Draw footer text
     */
    public function DrawFooterMultilineText($page, $Text, $x, $y, $Size, $GrayScale, $LineHeight) {

        $retour = -$LineHeight;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale($GrayScale));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $Size);
        foreach (explode("\n", $Text) as $value) {
            if ($value !== '') {
                $page->drawText(trim(strip_tags($value)), $x, $y, 'UTF-8');
                $y -=$LineHeight;
                $retour += $LineHeight;
            }
        }
        return $retour;
    }

    /**
     * Draw header
     */
    public function drawHeader($page, $title, $StoreId = null, $quote='') {

        //background
       // $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.7));
      //  $page->setFillColor(Zend_Pdf_Color_Html::color('#f9f9f9'));
      //  $page->drawRectangle(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y - $this->_BLOC_ENTETE_HAUTEUR, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		//$this->topHeader($page, $StoreId);
        // insert le logo
       // $this->insertLogo($page, $StoreId);

        $this->y -= $this->_BLOC_ENTETE_HAUTEUR + 5;
        $page->setLineWidth(1.5);
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.1));
        //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        //Title
		
        $this->y -= 25;
        $name =  $quote['quote_type'];
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
        if ($title != '') {
			$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 45);
            $this->drawTextInBlock($page, $name, 0, $this->y, $this->_PAGE_WIDTH - 80, 50, 'c');
            $this->y -= 10;
            //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        }
		$_text = $quote['store_name'];
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 17);
        $this->drawTextInBlock($page, $_text, 0, $this->y-15, $this->_PAGE_WIDTH - 80, 50, 'c');
        $this->y -= 10;
		
		$_text = $quote['ABN'];
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 13);
        $this->drawTextInBlock($page, $_text, 0, ($this->y-20), $this->_PAGE_WIDTH - 80, 50, 'c');
        $this->y -= 10;
		
		 
    }

    /**
     * Draw Top Header
     *
     * @param page, store id $page
     */
    protected function topHeader(&$page, $settings=null, $StoreId = null, $quote='') {
        
         
		//background
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.7));
		$page->setFillColor(Zend_Pdf_Color_Html::color('#f9f9f9'));
		$page->drawRectangle(0,0,$this->_PAGE_WIDTH,($this->y), Zend_Pdf_Page::SHAPE_DRAW_FILL);
		 
		
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.9));
        $page->setFillColor(Zend_Pdf_Color_Html::color('#ec4d53'));
   //   $page->drawRectangle(0, 0, $this->_BLOC_ENTETE_LARGEUR, $this->y - $this->_BLOC_ENTETE_HAUTEUR_TOP, Zend_Pdf_Page::SHAPE_DRAW_FILL);
	  	$page->drawRectangle(0,($this->y+12),$this->_PAGE_WIDTH,($this->y-60), Zend_Pdf_Page::SHAPE_DRAW_FILL);
		  
		//$logo = Mage::getStoreConfig('quotation/pdf/logo', $StoreId);
		
		
		$crruent_logo = 'logo.png';
		
		$_images = array('tel'=>'telephone.png', 'logo'=>$crruent_logo,
							  'envelop'=>'envelop.png','globe'=>'globe.png',
							);							
		$_image= $_images['tel'];       
	    $_image = Mage::getBaseDir('media') . DS . 'pdfemails' . DS . 'images' . DS . $_image;	   
		$_image = Zend_Pdf_Image::imageWithPath($_image);
        
		//$page->drawImage($image, 10, 780, $this->_LOGO_LARGEUR, 780 + $this->_LOGO_HAUTEUR);
        $page->drawImage($_image, 20, 795, 42, 817 );
        
		$_text = 'Ph: '.$quote['store_phone'];
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 11);
        $this->drawTextInBlock($page, $_text, -25, 800, $this->_PAGE_WIDTH - 450, 50, 'c');
        $this->y -= 10;
		
		
		$_image= $_images['globe'];
		$_image = Mage::getBaseDir('media') . DS . 'pdfemails' . DS . 'images' . DS . $_image;	   
		$_image = Zend_Pdf_Image::imageWithPath($_image);
        //$page->drawImage($image, 10, 780, $this->_LOGO_LARGEUR, 780 + $this->_LOGO_HAUTEUR);
        $page->drawImage($_image, 200, 795, 222, 817 );
        
		$website = $quote['store_url'];
		
		$_text = $website;	
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 11);
        $this->drawTextInBlock($page, $_text, 43, 801, $this->_PAGE_WIDTH - 190, 60, 'c');
        $this->y -= 10;
		
		$_image= $_images['envelop'];
        $_image = Mage::getBaseDir('media') . DS . 'pdfemails' . DS . 'images' . DS . $_image;	   
		$_image = Zend_Pdf_Image::imageWithPath($_image);
        //$page->drawImage($image, 10, 780, $this->_LOGO_LARGEUR, 780 + $this->_LOGO_HAUTEUR);
        $page->drawImage($_image, 405, 797, 427, 814 );
        $store_email = $quote['store_email'];
		$_text = $store_email;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));			
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 11);
        $this->drawTextInBlock($page, $_text, 200, 801, $this->_PAGE_WIDTH - 90, 60, 'c');
        $this->y -= 10;
		
		// insert le logo
        // $this->insertLogo($page, $StoreId);

         /*
		 $this->y -= $this->_BLOC_ENTETE_HAUTEUR + 5;
         $page->setLineWidth(1.5);
         $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.1));		
		 $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
	/*
        //Title
        $this->y -= 25;
        $name = $title;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
        if ($title != '') {
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 24);
            $this->drawTextInBlock($page, $name, 0, $this->y, $this->_PAGE_WIDTH - 80, 50, 'c');
            $this->y -= 10;
            //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        }
        */
		return $this;
		
    }
	
	
	 /**
     * Draw Company Address
     *
     * @param page, store id $page
     */
    protected function companyAddress(&$page, $StoreId = null, $setting=null, $quote='') {
        
       
		//$logo = Mage::getStoreConfig('quotation/pdf/logo', $StoreId);
		//var_dump($logo);
		$_images = array('tel'=>'telephone.png', 'logo'=>'logo.png',
							  'envelop'=>'envelop.png','globe'=>'globe.png',
							);							
		$store_logo=$quote['store_logo'];
		
		if($store_logo !=''){
		$_image = Mage::getBaseDir('media').'/upload/image/'.$store_logo;
		
		 
		}else{
		
		 $_image= $_images['logo'];
		 $_image = Mage::getBaseDir('media') . DS . 'pdfemails' . DS . 'images' . DS . $_image;
		}
       	  
		$_image = Zend_Pdf_Image::imageWithPath($_image);
        //$page->drawImage($image, 10, 780, $this->_LOGO_LARGEUR, 780 + $this->_LOGO_HAUTEUR);
        $page->drawImage($_image, 20, 570, 80, 640 );
		
		
		$LeftAddress = $quote['store_name'].'
						'.$quote['store_address'];
		
		$LeftAddress = $this->WrapTextToWidth($page, $LeftAddress, 250);
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 11);
		$this->DrawMultilineText($page, $LeftAddress, 90, $this->y-60, 12, 0.2, 16);
        
		return $this;
		
    }
	
	 /**
     * Draw billAddress
     *
     * @param page, store id $page
     */
    protected function billBlock(&$page, $StoreId = null, $setting=null, $quote) {
        
       
		//$logo = Mage::getStoreConfig('quotation/pdf/logo', $StoreId);
		//var_dump($logo);
		$_images = array('tel'=>'telephone.png', 'logo'=>'logo.png',
							  'envelop'=>'envelop.png','globe'=>'globe.png',
							);							
		
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#dceff6'));
        $page->drawRectangle(320, 550, 570, 650, Zend_Pdf_Page::SHAPE_DRAW_FILL);
        $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 18);
        $this->drawTextInBlock($page, 'Your Bill', 200, 630, 480,150,'c');		
		
		$_created_date = new DateTime($quote['data']['created_time']);		
		$_created_date =$_created_date->format('Y-m-d');
		
		$_valid_date = new DateTime($quote['data']['valid_end_time']);		
		$_valid_date =$_valid_date->format('Y-m-d');
		
		////right address		
		$rightAddress='Quote Number:  	'.$quote['data']['increment_id'].'
						Generate Date:  '.$_created_date.'
						Valid Till:            '.$_valid_date.'';		
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 11);
		$this->DrawMultilineText($page, $rightAddress, 335, $this->y-85, 12, 0.0, 18);
        
		///drawing total
		$grand_total = $quote['grand_total_format'];
		
		
		$grand_total = '       AUD '.$grand_total;		
			
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ec4d53'));
        $page->drawRectangle(320, 540, 570, 560, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		$page->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 12);
        $this->drawTextInBlock($page, 'Total:', 220, 545, 250,150,'c');
		$this->drawTextInBlock($page, $grand_total, 330, 545, 410,150,'c');
		
		//total saved
		$saved_total = '       AUD '.round($quote['grand_total']*.22,2);
		
		$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 12);
		$this->drawTextInBlock($page, 'Total Saved Today:', 227, 525, 303,150,'c');
		$this->drawTextInBlock($page, $saved_total, 300, 525, 380,150,'c');
		
		return $this;
		
    }
	
	 /**
     * Draw billAddress
     *
     * @param page, store id $page
     */
    protected function shipAddress(&$page, $StoreId = null, $setting=null, $quote='') {
        
       	$page->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 16);
        $this->drawTextInBlock($page, 'Billing Address', 36, 540, 80,50,'c');		
		////right address		
		
		 
		$customer_data = $quote['customer']->getData();		
		$customer_address = $quote['customer_address'];
		 
	//	$customer_phone = $customer_data[''];
		
		
		$customer = Mage::getModel('customer/customer')->load($customer_data['entity_id']);
		$customer_addresses = $customer->getAddresses();
		
		$current_address = array();		
		foreach($customer_addresses as $address){
			$current_address[]=$address->toArray();
			} 
		 
		$address_obj = $current_address[0];		
		$countryName = Mage::getModel('directory/country')->load($address_obj['country_id'])->getName();
		
		$complete_address = $address_obj['company'].' 
						'.$address_obj['firstname'].' '.$address_obj['lastname'].'
						'.$address_obj['street'].'
						'.$address_obj['city'].', '.$address_obj['region'].' '.$address_obj['postcode'].' '.$countryName;
		
		
		$leftShippingAddress = $customer_address;		
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
		$this->DrawMultilineText($page, $leftShippingAddress, 18, $this->y-165, 12, 0.0, 14);
        
		///drawing total
		
		
		return $this;
		
    }
	
	/**
     * Add new page (draw header / footer)
     *
     */
    public function NewPage(array $settings = array(), $quote='') {
        $page = $this->pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->pdf->pages[] = $page;

        //on place Y tout en haut
        $this->y = 830;
		 
        //dessine l'entete
        $this->topHeader($page, $settings['title'], $settings['store_id'],$quote);		
		
        //retourne la page
        return $page;
    }

    /**
     * Truncate text to fit width
     * 
     */
    public function TruncateTextToWidth($page, $text, $width) {
        $current_width = $this->widthForStringUsingFontSize($text, $page->getFont(), $page->getFontSize());
        while ($current_width > $width) {
            $text = substr($text, 0, strlen($text) - 1);
            $current_width = $this->widthForStringUsingFontSize($text, $page->getFont(), $page->getFontSize());
        }
        return $text;
    }

    /**
     * Add line return to fit multiline text to width
     *
     * @param unknown_type $text
     * @param unknown_type $width
     */
    public function WrapTextToWidth($page, $text, $width) {
        $t_words = explode(' ', $text);
        $retour = "";
        $current_line = "";
        for ($i = 0; $i < count($t_words); $i++) {
            if ($this->widthForStringUsingFontSize($current_line . ' ' . $t_words[$i], $page->getFont(), $page->getFontSize()) < $width) {
                $current_line .= ' ' . $t_words[$i];
            } else {
                if (($current_line != '') && (strlen($current_line) > 2))
                    $retour .= $current_line . "\n";
                $current_line = $t_words[$i];
            }

            if (strpos($t_words[$i], "\n") === false) {
                
            } else {
                if (($current_line != '') && (strlen($current_line) > 2))
                    $retour .= $current_line;
                $current_line = '';
            }
        }
        $retour .= $current_line;

        return $retour;
    }

    /**
     * Draw page number
     *
     */
    public function AddPagination($pdf, $quoteInfo='') {
        $page_count = count($pdf->pages);
        for ($i = 0; $i < $page_count; $i++) {
            if ($i >= $this->firstPageIndex) {
                $page = $pdf->pages[$i];
                
				$pagination = ($i + 1 - $this->firstPageIndex) . ' / ' . ($page_count - $this->firstPageIndex);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 15);
           //     $this->drawTextInBlock($page, 'text'.($this->y)-100, 200, $this->_PAGE_WIDTH - 20, 40, 'r');
		$page->setFillColor(Zend_Pdf_Color_Html::color('#888888'));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA),8);
        $this->drawTextInBlock($page, $quoteInfo['store_name'].' '.$quoteInfo['ABN'].' 1/2 Phillip Court, Port Melbourne, Victoria 3207 Australia Tel: '.$quoteInfo['store_phone'].';   Fax: 03 8456 6234   (Page: '.$pagination.')', 15, 15, 200,$this->y+20,'l');
				//var_dump($this->y); 
            }
        }
    }

    /**
     * Draw addresses & main text
     *
     */
    public function AddAddressesBlock($page, $LeftAddress, $RightAddress, $TxtDate, $TxtInfo) {
        //$page->drawLine($this->_PAGE_WIDTH / 2 - 50, $this->y, $this->_PAGE_WIDTH / 2 - 50, $this->y - 160);

        $this->y -= 1;
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
        $page->drawText($TxtDate, 25, $this->y, 'UTF-8');
        $page->drawText($TxtInfo, $this->_PAGE_WIDTH / 2 - 30, $this->y, 'UTF-8');

        $this->y -= 1;
       // $page->drawLine(10, 710, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        $this->y -= 1;
        $this->DrawMultilineText($page, $LeftAddress, 25, $this->y, 14, 0.4, 16);
        $this->DrawMultilineText($page, $RightAddress, $this->_PAGE_WIDTH / 2 - 30, $this->y, 14, 0.4, 16);

        $this->y -= 120;
        $page->setLineWidth(1.5);
        //$page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
    }

    /**
     * Format address
     */
    public function FormatAddress($adress, $caption = '', $show_details = false, $NoTvaIntraco = '') {
        if ($NoTvaIntraco == 'taxvat')
            $NoTvaIntraco = '';
        $FormatedAddress = "";
        if ($caption != '')
            $FormatedAddress = $caption . "\n ";
        if ($adress != null) {
            if ($adress->getcompany() != '')
                $FormatedAddress .= $adress->getcompany() . "\n ";
            if ($adress->getPrefix() == '')
                $FormatedAddress .= 'M. ';
            $FormatedAddress .= $adress->getName() . "\n ";
            $FormatedAddress .= $adress->getStreet(1) . "\n ";
            if ($adress->getStreet(2) != '')
                $FormatedAddress .= $adress->getStreet(2) . "\n ";
            if ($show_details) {
                if ($adress->getbuilding() != '')
                    $FormatedAddress .= ' Bat ' . $adress->getbuilding();
                if ($adress->getfloor() != '')
                    $FormatedAddress .= ' Etage ' . $adress->getfloor();
                if ($adress->getdoor_code() != '')
                    $FormatedAddress .= ' Code ' . $adress->getdoor_code();
                if ($adress->getappartment() != '')
                    $FormatedAddress .= ' Appt ' . $adress->getappartment();
                $FormatedAddress .= "\n ";
            }
            $FormatedAddress .= $adress->getPostcode() . ' ' . $adress->getCity() . "\n ";
            $FormatedAddress .= strtoupper(Mage::getModel('directory/country')->load($adress->getCountry())->getName()) . "\n ";
            if ($show_details)
                $FormatedAddress .= $adress->getcomments() . "\n ";
            if ($NoTvaIntraco != '')
                $FormatedAddress .= 'No TVA : ' . $NoTvaIntraco;
        }
        return $FormatedAddress;
    }
	
	
	/*function adding text block*/
	
	public function addBlockText($page=null, $text='', $bgcolor='', $fcolor, $x=0, $y=0, $width=0, $height=0, $fontsize=11, $lineheight=14 ){
		 
		if($bgcolor !=''){
		$page->setFillColor(Zend_Pdf_Color_Html::color($bgcolor));
	    $page->drawRectangle($x, $y, $width, $y-$height, Zend_Pdf_Page::SHAPE_DRAW_FILL);	    
		 }
	if(strlen($text) < 100){
		$page->setFillColor(Zend_Pdf_Color_Html::color($fcolor));
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), $fontsize);				
		$page->drawText(Mage::helper('quotation')->__($text), $x+10, $y-20, 'UTF-8');
		
	}else{
		
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $fontsize);	
		$_n_text = $this->WrapTextToWidth($page, $text, $width-15);		
		$this->DrawMultilineText($page, $_n_text, $x+10, $y-18, $fontsize, 0.2, $lineheight);
		}
		return true;
	 }
	 
	 /*function adding text block*/
	
	public function addBlockTextComments($page=null, $text='', $bgcolor='', $fcolor, $x=0, $y=0, $width=0, $height=0, $fontsize=11, $lineheight=14 ){
		 
		if($bgcolor !=''){
		$page->setFillColor(Zend_Pdf_Color_Html::color($bgcolor));
	    $page->drawRectangle($x, $y, $width, $y-$height, Zend_Pdf_Page::SHAPE_DRAW_FILL);	    
		 }
			
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $fontsize);	
		$_n_text = $this->WrapTextToWidth($page, $text, $width-15);		
		$this->DrawMultilineText($page, $_n_text, $x+10, $y-18, $fontsize, 0.1, $lineheight);
		 
		return true;
	 }
	 
	
	

}
