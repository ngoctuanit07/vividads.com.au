<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @package MDN_CrmTicket
 * @version 1.2
 */
class MDN_CrmTicket_Block_Admin_Ticket_Affect_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();

        $this->setId('CrmTicketsAffectsGrid');
        $this->_parentTemplate = $this->getTemplate();

        $this->setEmptyText('No Items');
      //  $this->setDefaultSort('created_at');

        $this->setDefaultDir('DESC');
        //$this->setRowClickCallback(false);
        //$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * load order collection
     *
     * @return unknown
     */    

    protected function _prepareCollection()
    {
       
	    $ticket_id = $this->getRequest()->getParam('ticket_id');	
		$_assigned_ticket = Mage::getModel('CrmTicket/Ticket')->load($ticket_id)->getCt_object_id();
		$order_type = null;
		$order_id  = null;
		if($_assigned_ticket !=null){
			$assigned_ids = explode('_',$_assigned_ticket);		
			$order_type = $assigned_ids[0];
			$order_id = $assigned_ids[1];
		}
		  
		$collection1 = Mage::getModel('Quotation/Quotation')
							->getCollection()
							->addFieldToSelect(array('increment_id'=>'increment_id',
													 'created_at'=>'created_time',
													 'customer_id'=>'customer_id',
													 'status'=>'updated_status'									 
							))
							;
		$collection = $collection1;		
		 
	 	//  Zend_debug::dump($collection1->getSelect()->__toString());					
							
		$collection2 = Mage::getResourceModel('sales/order_collection')->addAttributeToSelect('*');		
		$collection = $collection2;	
		
		//merging two collections
		if($order_id==null){ 
		   if(count($collection1)>0){
				foreach($collection1 as $col){
					$collection = $collection2->addItem($col);
					}	
			}
			
		}
		
		if($order_type !=null && $order_type =='quote'){
			$collection = $collection1->addFieldToFilter('quotation_id',$order_id);					
		}
		
		if($order_type !=null && $order_type =='order'){
		 $collection = $collection2->addFieldToFilter('entity_id',$order_id);					
		}
		//echo '<br/>';
		// Zend_debug::dump($collection2->getSelect()->__toString());
		
		
		
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {

        $helper = Mage::helper('CrmTicket');

        //cet onglet affiche toutes les commandes (reference, date, nom prénom client, email client + colonne "affecter")

        $this->addColumn('real_order_id', array(
            'header'=> $helper->__('Order #'),
            'width' => '100px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        $this->addColumn('marketplace_order_id', array(
            'header'=> $helper->__('MarketPlace Order #'),
            'width' => '100px',
            'type'  => 'text',
            'index' => 'marketplace_order_id',
        ));
       
        $this->addColumn('created_at', array(
            'header' => $helper->__('Created at'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '150px',
        ));
      

       $this->addColumn('order_type', array(
            'header'=> $helper->__('Order Type'),
            'type'  => 'text',
            'width' => '100px',
            'index' => 'increment_id',
			'renderer' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_OrderType',
            'align' => 'center',
        ));
 
       $this->addColumn('customer_firstname', array(
            'header'=> $helper->__('Customer first name'),
            'type'  => 'text',
            'width' => '200px',
            'index' => 'customer_id',
			'renderer' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_CustomerFirstname',
            'align' => 'center',
        ));

       $this->addColumn('customer_lastname', array(
            'header'=> $helper->__('Customer last name'),
            'type'  => 'text',
            'width' => '200px',
            'index' => 'customer_id',
			'renderer' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_CustomerLastname',
            'align' => 'center',
        ));

       $this->addColumn('customer_email', array(
            'header'=> $helper->__('Customer email #'),
			'width' => '300px',
            'type'  => 'text',
            'index' => 'customer_id',
			'renderer' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_CustomerEmail',
            'align' => 'center',
        ));
              

       $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));


        $this->addColumn('affect', array(
              'header' => $helper->__('Assign'),
              'index' => 'increment_id',
              'width' => '50px',
              'renderer' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_AffectToCustomer',
              'align' => 'center',
         ));        
         


        return parent::_prepareColumns();
    }

    /**
     *
     * @return type
     */
    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }


    /**
     * Ajax callback
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/AffectGridAjax', array('_current' => true));
    }


    public function getUnaffectObjectUrl() {
        $ticketId = Mage::registry('ct_id');
        if(!$ticketId){
          $ticketId = $this->getRequest()->getParam('ticket_id');
        }
        return $this->getUrl('CrmTicket/Admin_Ticket/Unaffect', array('ticket_id' => $ticketId, 'mode' => 'object'));
    }

    public function getUnaffectCustomerUrl() {
        $ticketId = Mage::registry('ct_id');
        if(!$ticketId){
          $ticketId = $this->getRequest()->getParam('ticket_id');
        }
        return $this->getUrl('CrmTicket/Admin_Ticket/Unaffect', array('ticket_id' => $ticketId, 'mode' => 'customer'));
    }

    


}

