<?php
class Sag_Gallery_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();   
                $head = $this->getLayout()->getBlock('head');
                $head->setTitle("Table throws | Gallery");
                $head->setDescription("Custom printed table throws / branded table throws have been used in the marketing industry for ages . These table throws are also widely used at marketing events , trade shows , retails stores , retail outsets , libraries , out door events , schools , universities and product launch events .");
                $head->setKeywords("marketing events gallery,trade shows gallery,retails stores gallery,retail outsets gallery,libraries gallery,out door events gallery,schools gallery,universities gallery,product launch events gallery");
		$this->renderLayout();
    }
}