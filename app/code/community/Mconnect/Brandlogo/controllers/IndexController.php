<?php

class Mconnect_Brandlogo_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function listAction() {
        $this->loadLayout();
        
        $this->renderLayout();
    }
	
	/*getStoreLogos*/
	
	public function getStoreLogosAction(){
		
		/*load all logos from model*/
		$_logos = Mage::getModel('brandlogo/brandlogo');
		$_logos = $_logos->getAllStoreLogos();		
		
		//var_dump($_logos);		
		
		return $_logos;
	}

}