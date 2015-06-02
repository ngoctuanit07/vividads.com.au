<?php
 
class MDN_Quotation_Block_Adminhtml_Edit_Tabs_Tickets extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {

        parent::__construct();
        $this->setHtmlId('tickets');
        $this->setTemplate('Quotation/Edit/Tab/Tickets.phtml');

        $helper = Mage::helper('quotation/Tickets');
        $helper->initCache();
        $this->tickets_form = $helper->getTicketsForm($this->getRequest()->getParam('quote_id'));	
		//var_dump($this->tickets_form); exit;
        $this->tickets_tab = $helper->asArray($this->getRequest()->getParam('quote_id'));
		 
    }

}
