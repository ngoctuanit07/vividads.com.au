<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid extends MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid_Abstract
//class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_Grid extends AdjustWare_Deliverydate_Block_Rewrite_AdminhtmlSalesOrderGrid
{   
    //tertet
    public function __construct() {
        parent::__construct();
        $this->setDefaultFilter(array('order_group'=>0)); // Actual
    }
    
    protected function _prepareColumns() {
        $helper = Mage::helper('orderspro');
        
	 //  var_dump(Mage::getModel('sales/order')->getCollection()->getSelect()->__toString());
	  // exit;
	   if (!$helper->isEnabled()) return parent::_prepareColumns();        
        
        $listColumns = $helper->getGridColumns();        
        //$currencyCode = $helper->getCurrentCurrencyCode();
	
	//Start 03_03_2014
	$designer_column = array('coupon_code','shipping_telephone','billing_telephone','shipping_postcode','billing_postcode','shipping_country','billing_country','shipping_region','billing_region','shipping_city','billing_city','shipping_street','billing_street','billing_company','shipping_company','payment_method','base_grand_total','order_group','status');
	//End 03_03_2014
	
	
       
        foreach ($listColumns as $column) {
	  
	/********************** Start check the user 03_03_2014 *********************************/
	$user_role = Mage::getSingleton('admin/session')->getUser();
	//Get the role id of the user
	$roleId = implode('', $user_role->getRoles());
	
	//Get the role name
	$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
	
	  if($roleName == 'Designer')
	  {
	    
	    if(in_array($column,$designer_column))
	    {
	      continue;
	    }
	    
	    $this->removeColumn('net_terms');
	    $this->removeColumn('payment_method');
	    $this->removeColumn('po_number');
	  }
	  
	 
	  /********************** End check the user 03_03_2014 *********************************/
	  
           
		    switch ($column) {
                
                // standard fields
		
			
	      //End 03_03_2014
	      
	     
		 case 'real_order_id':
                    $this->addColumn('real_order_id', array(
                        'header'=> Mage::helper('sales')->__('Order #'),
                        'width' => '80px',
                        'type'  => 'text',
                        'index' => 'increment_id',
                    ));
                break;  
		 
		 
		 /*
		 case 'proof_date':
                    $this->addColumn('proof_date', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_proof',                
                        'filter'    => false,
                        'sortable'  => false,                
                        'header' => $helper->__('Proof Approve Date'),
                        'index' => 'proof_date',
			'type' => 'datetime',
                        'width' => '100px',
                        ));
                break;
		
		 
		/*
		case 'delivery_date':
                    $this->addColumn('delivery_date', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_delivery',                
                        'filter'    => false,
                        'sortable'  => false,                
                        'header' => $helper->__('Delivery Date '),
                        'index' => 'delivery_date',
			'type' => 'date',
                        'width' => '100px',
                        ));
                break;
		
		*/
		
		 case 'base_grand_total':    
                    $this->addColumn('base_grand_total', array(
                        'header' => Mage::helper('sales')->__('G.T. (Base)'),
                        'index' => 'base_grand_total',
                        'type'  => 'currency',
                        'currency' => 'base_currency_code',
                    ));
                break;    

                    
                case 'grand_total':
                    $this->addColumn('grand_total', array(
                        'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
                        'index' => 'grand_total',
                        'type'  => 'currency',
                        'currency' => 'order_currency_code',
                    ));
                break;    
                    
        // additional fields
                
                case 'product_names':                
                    $this->addColumn('product_names', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_products',
                        'header' => $helper->__('Product Name(s)').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                        'index' => 'product_names'                        
                        ));
                break;
            
                case 'product_skus':                
                    $this->addColumn('product_skus', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_products',
                        'header' => $helper->__('SKU(s)'),
                        'index' => 'skus'                        
                        ));
                break;
                
                case 'product_options':                
                    $this->addColumn('product_options', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_products',
                        'header' => $helper->__('Product Option(s)'),
                        'index' => 'product_options',
                        'filter'    => false,
                        'sortable'  => false
                        ));
                break;

                case 'customer_email':
                    $this->addColumn('customer_email', array(
                        'type'  => 'text',
                        'header' => $helper->__('Customer Email'),
                        'index' => 'customer_email'
                        ));
                break;
            

                case 'customer_group':
                    $this->addColumn('customer_group', array(
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',
                        'type'  => 'options',
                        'options' => $helper->getCustomerGroups(),
                        'header' => $helper->__('Customer Group'),
                        'index' => 'customer_group_id',
                        'align' => 'center'
                        ));
                break;    
                    

                case 'payment_method':
                    $this->addColumn('payment_method', array(            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',
                        'type'  => 'options',
                        'options' => $helper->getAllPaymentMethods(),
                        'header' => $helper->__('Payment Method'),
                        'index' => 'method',
                        'align' => 'center'
                        ));
                break;
                    

                case 'base_total_refunded':
                    $this->addColumn('base_total_refunded', array(                            
                        'type'  => 'currency',
                        'currency' => 'base_currency_code',                        
                        'header' => $helper->__('Total Refunded (Base)'),
                        'index' => 'base_total_refunded',
                        'total' => 'sum'
                        ));
                break;
                case 'total_refunded':
                    $this->addColumn('total_refunded', array(                            
                        'type'  => 'currency',
                        'currency' => 'order_currency_code',                        
                        'header' => $helper->__('Total Refunded (Purchased)'),
                        'index' => 'total_refunded',
                        'total' => 'sum'
                        ));
                break;
				
				
				
                case 'shipped':
                    $this->addColumn('shipped', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getShippedStatuses(),
                        'header' => $helper->__('Shipped'),
                        'index' => 'shipped',
                        'align' => 'center'
                        ));
                break;                    
               
			   case 'base_tax_amount':
                    $this->addColumn('base_tax_amount', array(                            
                        'type'  => 'currency',
                        'currency' => 'base_currency_code',
                        'header' => $helper->__('Tax Amount (Base)'),
                        'index' => 'base_tax_amount'
                        ));
                break;
                case 'tax_amount':
                    $this->addColumn('tax_amount', array(                            
                        'type'  => 'currency',
                        'currency' => 'order_currency_code',
                        'header' => $helper->__('Tax Amount (Purchased)'),
                        'index' => 'tax_amount'
                        ));
                break; 
                    
                case 'base_discount_amount':
                    $this->addColumn('base_discount_amount', array(                            
                        'type'  => 'currency',
                        'currency' => 'base_currency_code',
                        'header' => $helper->__('Discount (Base)'),
                        'index' => 'base_discount_amount'                
                        ));
                break;    
                case 'discount_amount':
                    $this->addColumn('discount_amount', array(                            
                        'type'  => 'currency',
                        'currency' => 'order_currency_code',
                        'header' => $helper->__('Discount (Purchased)'),
                        'index' => 'discount_amount'                
                        ));
                break;
            
                case 'base_internal_credit':
                    if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                        $this->addColumn('base_internal_credit', array(
                            'type'  => 'currency',
                            'currency' => 'base_currency_code',
                            'header' => $helper->__('Internal Credit (Base)'),
                            'index' => 'base_customer_credit_amount'
                            ));
                    }
                break;
                case 'internal_credit':
                    if (Mage::getConfig()->getModuleConfig('MageWorx_CustomerCredit')->is('active', true)) {
                        $this->addColumn('internal_credit', array(
                            'type'  => 'currency',
                            'currency' => 'order_currency_code',
                            'header' => $helper->__('Internal Credit (Purchased)'),
                            'index' => 'customer_credit_amount'
                            ));
                    }
                break;
                
			        
                /*case 'order_group':
                    $this->addColumn('order_group', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getOrderGroups(),
                        'header' => $helper->__('Group'),
                        'index' => 'order_group_id',
                        'align' => 'center',                        
                        ));
                break;*/
        
		
		 /*case 'qnty':
                    $this->addColumn('qnty', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_qnty',                
                        'filter'    => false,
                        'sortable'  => false,                
                        'header' => $helper->__('Qnty'),
                        'index' => 'total_qty',                
                        ));
                break;*/
                
                case 'weight':
                    $this->addColumn('weight', array(
                        'type'    => 'number',
                        'header' => $helper->__('Weight'),
                        'index' => 'weight',                
                        ));
                break;
		                     
					
		case 'billing_company':
                    $this->addColumn('billing_company', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Bill to Company'),
                        'index' => 'billing_company',
                        'align' => 'center'
                        ));
                break;
		
				
		
		case 'billing_street':
                    $this->addColumn('billing_street', array(                            
                        'type'  => 'text',
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_street',
                        'header' => $helper->__('Bill to Street'),
                        'index' => 'billing_street',
                        'align' => 'center'
                        ));
                break;            
                case 'shipping_street':
                    $this->addColumn('shipping_street', array(
                        'type'  => 'text',
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_street',
                        'header' => $helper->__('Ship to Street'),
                        'index' => 'shipping_street',
                        'align' => 'center'
                        ));
                break;
            
            
                case 'billing_city':
                    $this->addColumn('billing_city', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Bill to City'),
                        'index' => 'billing_city',
                        'align' => 'center'
                        ));
                break;            
                case 'shipping_city':
                    $this->addColumn('shipping_city', array(
                        'type'  => 'text',
                        'header' => $helper->__('Ship to City'),
                        'index' => 'shipping_city',
                        'align' => 'center'
                        ));
                break;
            
                case 'billing_region':
                    $this->addColumn('billing_region', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Bill to State'),
                        'index' => 'billing_region',
                        'align' => 'center'
                        ));
                break;            
                case 'shipping_region':
                    $this->addColumn('shipping_region', array(
                        'type'  => 'text',
                        'header' => $helper->__('Ship to State'),
                        'index' => 'shipping_region',
                        'align' => 'center'
                        ));
                break;
            
                case 'billing_country':
                    $this->addColumn('billing_country', array(                            
                        'type'  => 'options',
                        'options' => $this->getCountryNames(),
                        'header' => $helper->__('Bill to Country'),
                        'index' => 'billing_country_id',
                        'align' => 'center'
                        ));
                break;
                case 'shipping_country':
                    $this->addColumn('shipping_country', array(
                        'type'  => 'options',
                        'header' => $helper->__('Ship to Country'),
                        'options' => $this->getCountryNames(),
                        'index' => 'shipping_country_id',
                        'align' => 'center'
                        ));
                break;
            
            
                case 'billing_postcode':
                    $this->addColumn('billing_postcode', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Billing Postcode'),
                        'index' => 'billing_postcode',
                        'align' => 'center'
                        ));
                break;
            
                case 'shipping_postcode':
                    $this->addColumn('shipping_postcode', array(
                        'type'  => 'text',
                        'header' => $helper->__('Shipping Postcode'),
                        'index' => 'shipping_postcode',
                        'align' => 'center'
                        ));
                break;
            
                case 'billing_telephone':
                    $this->addColumn('billing_telephone', array(                            
                        'type'  => 'text',
                        'header' => $helper->__('Billing Telephone'),
                        'index' => 'billing_telephone',
                        'align' => 'center'
                        ));
                break;
            
                case 'shipping_telephone':
                    $this->addColumn('shipping_telephone', array(
                        'type'  => 'text',
                        'header' => $helper->__('Shipping Telephone'),
                        'index' => 'shipping_telephone',
                        'align' => 'center'
                        ));
                break;
           case 'created_at':
                    $this->addColumn('created_at', array(
                        'header' => Mage::helper('sales')->__('Purchased On'),
                        'index' => 'created_at',
                        'type' => 'datetime',
                        'width' => '100px',
                    )); 
		 
		 case 'coupon_code':
                    $this->addColumn('coupon_code', array(
                        'type'  => 'text',
                        'header' => $helper->__('Coupon Code'),
                        'align' => 'center',
                        'index' => 'coupon_code'       
                        ));
                break;
            
                case 'is_edited':
                    $this->addColumn('is_edited', array(                            
                        'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_registry',                
                        'type'  => 'options',
                        'options' => $helper->getEditedStatuses(),
                        'header' => $helper->__('Edited'),
                        'index' => 'is_edited',
                        'align' => 'center'
                        ));
                break;
				
				case 'status':
                    $this->addColumn('status', array(
                        'header' => Mage::helper('sales')->__('Status'),
                        'index' => 'status',
                        'type'  => 'options',
                        'width' => '70px',
                        'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
                    ));
                break;  
				
				case 'action':    
                   
				    $this->addColumn('order_messages', array(
                        'header'=> Mage::helper('sales')->__('Messages'),
                        'width' => '80px',
                        'type'  => 'options',
						'options'=> array( 1=>'New Messages'),
                     //   'index' => 'readstatus',
					 'index'=>'entity_id',
						'width'=>'200',
						'align' => 'center',
						'renderer'  => 'mageworx/orderspro_sales_order_grid_renderer_Messages',
                    ));
				   
				   
				   
				    if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
                        $this->addColumn('action',
                            array(
                                'header'    => Mage::helper('sales')->__('Action'),
                                'width'     => '50px',
                                'type'      => 'action',
                                'getter'     => 'getId',
                                'actions'   => array(
                                    array(
                                        'caption' => Mage::helper('sales')->__('View'),
                                        'url'     => array('base'=>'*/sales_order/view'),
                                        'field'   => 'order_id'
                                    )
                                ),
                                'filter'    => false,
                                'sortable'  => false,
                                'index'     => 'stores',
                                'is_system' => true,
                        ));
                    }
                break;
            
               
            }    
			//Zend_debug::dump($column);        
        }
	   
	   //exit;
        
        if (method_exists($this, '_prepareAmastyFlagsColumns') 
        	|| method_exists($this, '_prepareAmastyOrderattachColumns') 
        	|| method_exists($this, '_prepareFraudDetectionColumns')
                || method_exists($this, '_prepareInnoextsWarehouseColumns')
        	|| method_exists($this, '_prepareSalesRepColumns')
        	|| method_exists($this, '_prepareAWOrdertagsColumns')
        	|| method_exists($this, '_prepareAmastyOrderattrColumns')
        	) {
            $actionsColumn = null;
            if (isset($this->_columns['action'])) {
                $actionsColumn = $this->_columns['action'];
                unset($this->_columns['action']);
            }        
            if (method_exists($this, '_prepareAmastyFlagsColumns')) $this->_prepareAmastyFlagsColumns();
            if (method_exists($this, '_prepareAmastyOrderattachColumns')) $this->_prepareAmastyOrderattachColumns();
            if (method_exists($this, '_prepareFraudDetectionColumns')) $this->_prepareFraudDetectionColumns();
            if (method_exists($this, '_prepareInnoextsWarehouseColumns')) $this->_prepareInnoextsWarehouseColumns();
            if (method_exists($this, '_prepareSalesRepColumns')) $this->_prepareSalesRepColumns();
            if (method_exists($this,'_prepareAWOrdertagsColumns')) $this->_prepareAWOrdertagsColumns();
            if (method_exists($this,'_prepareAmastyOrderattrColumns')) $this->_prepareAmastyOrderattrColumns();
            
            if ($actionsColumn) $this->_columns['action'] = $actionsColumn;
        }
        
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
        
        $this->sortColumnsByOrder();
                
        return $this;
    }
    
    public function getCountryNames() {
        if (Mage::registry('country_names')) return Mage::registry('country_names');
        $countryNames = array();
        $collection = Mage::getResourceModel('directory/country_collection')->load();
        foreach ($collection as $item) {
            if ($item->getCountryId()) $countryNames[$item->getCountryId()] = $item->getName();
        }
        asort($countryNames);
        Mage::register('country_names', $countryNames);
        return $countryNames;
    }
    
    protected function _prepareMassaction() {
        parent::_prepareMassaction();
        $block = $this->getMassactionBlock();
        
        if (Mage::helper('orderspro')->isEnabled()) {                
            
            if (Mage::helper('orderspro')->isEnableInvoiceOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/invoice')) {            
                $block->addItem('invoice_order', array(
                     'label'=> Mage::helper('orderspro')->__('Invoice'),
                     'url'  => $this->getUrl('*/sales_order/massInvoice'),
                ));
            }
            
            if (Mage::helper('orderspro')->isEnableShipOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/ship')) {            
                $block->addItem('ship_order', array(
                     'label'=> Mage::helper('orderspro')->__('Ship'),
                     'url'  => $this->getUrl('*/sales_order/massShip'),
                ));
            }
            
            if (Mage::helper('orderspro')->isEnableInvoiceOrders() && Mage::helper('orderspro')->isEnableShipOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/invoice_and_ship')) {            
                $block->addItem('invoice_and_ship_order', array(
                     'label'=> Mage::helper('orderspro')->__('Invoice+Ship'),
                     'url'  => $this->getUrl('*/sales_order/massInvoiceAndShip'),
                ));
            }
            
            if (Mage::helper('orderspro')->isEnableArchiveOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/archive')) {            
                $this->getMassactionBlock()->addItem('archive_order', array(
                     'label'=> Mage::helper('orderspro')->__('Archive'),
                     'url'  => $this->getUrl('*/sales_order/massArchive'),
                ));
            }
        

            if (Mage::helper('orderspro')->isEnableDeleteOrders() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/delete')) {
                $block->addItem('delete_order', array(
                     'label'=> Mage::helper('orderspro')->__('Delete'),
                     'url'  => $this->getUrl('*/sales_order/massDelete'),
                ));
            }
            
            if (Mage::helper('orderspro')->isEnableDeleteOrdersCompletely() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/delete_completely')) {
                $block->addItem('delete_order_completely', array(
                     'label'=> Mage::helper('orderspro')->__('Delete Completely'),
                     'url'  => $this->getUrl('*/sales_order/massDeleteCompletely'),
                ));
            }
            
            
            if ((Mage::helper('orderspro')->isEnableArchiveOrders() || Mage::helper('orderspro')->isEnableDeleteOrders()) && (Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/archive') || Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/delete'))) {
                $block->addItem('restore_order', array(
                     'label'=> Mage::helper('orderspro')->__('Restore'),
                     'url'  => $this->getUrl('*/sales_order/massRestore'),
                ));
            }
            
        }
        
        
        if (method_exists($this, '_prepareAmastyFlagsColumns')) {
            $block->addItem('amflags_separator_pre' . $i, array(
                'label'=> '---------------------',
                'url'  => '' 
            )); 

            $collection = Mage::getModel('amflags/flag')->getCollection();
            foreach ($collection as $flag) {
                $block->addItem('amflags_apply_' . $flag->getEntityId(), array(
                    'label'      => 'Apply "' . $flag->getAlias() . '" Flag',
                    'url'        => Mage::helper('adminhtml')->getUrl('amflags/adminhtml_flag/massApply', array('flag' => $flag->getEntityId())), 
                ));
            }

            $block->addItem('amflags_remove', array(
                'label'      => 'Remove Flag',
                'url'        => Mage::helper('adminhtml')->getUrl('amflags/adminhtml_flag/massApply', array('flag' => 0)), 
            ));

            $block->addItem('amflags_separator_post' . $i, array(
                'label'=> '---------------------',
                'url'  => '' 
            )); 
        }
        
        return $this;
    }
}