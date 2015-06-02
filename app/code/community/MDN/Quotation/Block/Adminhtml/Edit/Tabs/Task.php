<?php

class MDN_Quotation_Block_Adminhtml_Edit_Tabs_Task extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {

        parent::__construct();
        $this->setHtmlId('task');
        $this->setTemplate('Quotation/Edit/Tab/Task.phtml');

        $helper = Mage::helper('quotation/Proposal');
        $helper->initCache();
        $this->business_proposal_form = $helper->getBusinessProposalForm($this->getRequest()->getParam('quote_id'));
        $this->business_proposal_tab = $helper->asArray($this->getRequest()->getParam('quote_id'));
    }

}
