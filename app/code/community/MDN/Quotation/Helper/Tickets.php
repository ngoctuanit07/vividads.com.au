<?php

class MDN_Quotation_Helper_Tickets extends Mage_Core_Helper_Abstract {

    //constants
    const kTicketNode = 'tickets';
    const kSectionNode = 'section';
    const kTitleNode = 'title';
    const kContentNode = 'content';
    const kModeNode = 'mode';
    const kModeList = "list";
    const kModeText = "text";

    private $_cache = '';

    /**
     * Get business proposal from quote
     * @param <type> $quote_id
     * @return <type>
     */
    public function getCache($quote_id) {

        if ($this->_cache == '') {
            $quote = Mage::getModel('Quotation/Quotation')->load($quote_id);
           // $this->_cache = $quote->getbusiness_proposal();
        }

        return $this->_cache;
    }

    /**
     * Initialisation
     */
    public function initCache() {
        $this->_cache = '';
    }

    /**
     * Save proposal to quote
     */
    public function save($proposal, $quote) {

        $key_title = 'title_section_';
        $key_content = 'content_section_';
        $key_mode = 'mode_section_';
        $j = 0;

        $xml = new DomDocument();
        $root = $xml->createElement(self::kTicketNode, '');

        while (array_key_exists($key_title . $j, $proposal)) {

            if ($proposal[$key_title . $j] != '' && $proposal[$key_content . $j] != '') {

                $section = $xml->createElement(self::kSectionNode, '');

                $mode = $proposal[$key_mode . $j];
                $title = $proposal[$key_title . $j];
                $content = $proposal[$key_content . $j];

                $title = $xml->createElement(self::kTitleNode, '');
                $title->appendChild($xml->createTextNode(strip_tags($proposal[$key_title . $j])));
                $section->appendChild($title);

                $content = $xml->createElement(self::kContentNode, '');
                $content->appendChild($xml->createTextNode(strip_tags($proposal[$key_content . $j])));
                $section->appendChild($content);

                $mode = $xml->createElement(self::kModeNode, '');
                $mode->appendChild($xml->createTextNode($proposal[$key_mode . $j]));
                $section->appendChild($mode);

                $root->appendChild($section);
            }

            $j++;
        }

        $xml->appendChild($root);

        $quote->setbusiness_proposal($xml->saveXML());

        return $quote;
    }

    /**
     *
     * @param <type> $quoteId 
     */
    protected function loadXml($quoteId)
    {
        try
        {
            $xmlDoc = new DomDocument();
            $xml = $this->getCache($quoteId);
            $xmlDoc->loadXML($xml);

            return $xmlDoc;
        }
        catch(Exception $ex)
        {
            return null;
        }
    }

    /**
     * return tickets form
     */
    public function getTicketsForm($quote_id) {

        $i = 0;
        $retour = '';
        $retour .= '<ul>';

        $tickets = Mage::getModel('CrmTicket/Ticket')->getCollection();
				   
		
		if (count($tickets) ==0)
            return '';

         if (count($tickets) > 0) {

            foreach ($tickets as $ticket) {
				$retour .='<li id='.$ticket->getCt_id().'>'.$ticket->getCt_customer_id().'</li>';
				
				}
        }

        $retour .= '</ul>';
		 
        return $retour;
    }

    /**
     * Return sections as array
     */
    public function asArray($quote_id) {

        $retour = array();

        $xml = $this->loadXml($quote_id);
        if ($xml == null)
            return '';

        $root = $xml->getElementsByTagName(self::kTicketNode)->item(0);

        if ($root->nodeType == XML_ELEMENT_NODE) {
            foreach ($root->getElementsBytagName(self::kSectionNode) as $section) {

                $retour[] = array(
                    'title' => $section->getElementsByTagName(self::kTitleNode)->item(0)->nodeValue,
                    'content' => $section->getElementsByTagName(self::kContentNode)->item(0)->nodeValue,
                    'mode' => $section->getElementsByTagName(self::kModeNode)->item(0)->nodeValue
                );
            }
        }


        return json_encode($retour);
    }

    /**
     * Rturn as html
     */
    public function asHtml($quote_id) {

        $html = '';

        $xml = $this->loadXml($quote_id);
        if ($xml == null)
            return '';

        $root = $xml->getElementsByTagName(self::kTicketNode)->item(0);

        if ($root->nodeType == XML_ELEMENT_NODE) {

            foreach ($root->getElementsByTagName(self::kSectionNode) as $section) {

                $html .= '<h3>' . $section->getElementsByTagName(self::kTitleNode)->item(0)->nodeValue . '</h3>';

                $content = $section->getElementsByTagName(self::kContentNode)->item(0)->nodeValue;

                switch ($section->getElementsByTagName(self::kModeNode)->item(0)->nodeValue) {

                    case self::kModeList:
                        $tmp = explode("\n", $content);
                        $html .= '<ul>';
                        foreach ($tmp as $k => $v) {
                            $html .= '<li>' . $v . '</li>';
                        }
                        $html .= '</ul>';
                        break;

                    case self::kModeText:
                        $html .= '<p>' . str_replace("\n", '<br/>', $content) . '</p>';
                        break;
                }
            }
        }

        return $html;
    }

}
