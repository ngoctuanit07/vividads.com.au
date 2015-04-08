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
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

//class MageWorx_OrdersPro_Block_Sales_Order_History extends MageWorx_OrdersPro_Block_Sales_Order_History_Abstract
class MageWorx_OrdersPro_Block_Sales_Order_History extends Artis_Partialpayment_Block_Sales_Order_History
{

    public function __construct()
    {        
        parent::__construct();
        if (Mage::helper('orderspro')->isHideDeletedOrdersForCustomers()) {           
            $orders = Mage::getResourceModel('orderspro/order_collection')
                ->addFieldToSelect('*')                
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))                
                ->hideDeletedGroup()
                ->setOrder('created_at', 'desc');            
            $this->setOrders($orders);
            Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
        }
        if ((string)Mage::getConfig()->getModuleConfig('Innoexts_Warehouse')->active=='true') $this->getOrders()->setFlag('appendStockIds');
    }
	
	/*pubilc function get both quotes and Orders*/
	
	public function getQuotesAndOrders($customer_id = ''){
		$_quotes = Mage::getModel('Quotation/Quotation')
						->getCollection()						
						 ->addFieldToFilter('customer_id',$customer_id)
						 ->addFieldToFilter('status','active')
						 ;		
		//var_dump($_quotes->getSelect()->__toString());				
		return $_quotes;				
		 
		} 
	
	/*public function getViewQuoteUrl()*/
	
	public function getViewQuoteUrl($order=null){
		
		//$url = $order->getUrl();
		$url = 'Quotation/Quote/View/quote_id/'.$order->getQuotation_id();
		return Mage::getUrl($url);
		
		}
	
	/**
     * Render pagination HTML
     *
     * @return string
     */
    public function getNewPagerHtml()
    {
        
		$resource = Mage::getSingleton('core/resource');     
		/**
		 * Retrieve the read connection
		 */
		 $readConnection = $resource->getConnection('core_read');
     
		
		$_customer_id = Mage::getSingleton('customer/session')->getId();		 
		 	
		$_colls1 = Mage::getModel('Quotation/Quotation')
						->getCollection()
						->addFieldToFilter('customer_id',$_customer_id)
						->addFieldToFilter('status','active')
						; 		 
		$_colls2 = Mage::getModel('sales/order')
						->getCollection()
						->addAttributeToFilter('customer_id',$_customer_id)
						; 		 
	 	$join_sql = $_colls1->getSelect()
   	 					 ->join( array('options'=>'sales_flat_order'),'`main_table`.`customer_id` = `options`.`customer_id`','`options`.*');
		
		
		 var_dump($join_sql->__toString());
		  
		$pager = new Mage_Page_Block_Html_Pager();
		$pager->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
				->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
		        ->setCollection($_orders)
			  ;
			  
			  
		$this->setChild('pager', $pager);
		
		//$pagerBlock = $this->getChild('MageWorx_SeoSuite_Block_Page_Html_Pager');
		 return $pager->toHtml();
       
    }	 	       
    
}
