<?php
class Artis_Designer_Block_Adminhtml_Sales_Order_View_Tab
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    /**
     * Set the template for the block
     *
     */
    public function _construct()
    {
        parent::_construct();
        
       $this->setTemplate('designer/sales/order/view/tab.phtml');
    }
    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Designer');
    }
    
    public function getOrder()
    {
        $_order = Mage::getModel('sales/order');
        $_order->load($this->getRequest()->getParam('order_id'));
        return $_order;
    }
    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Click here to view Proofs History');
    }
    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
	
/**
     * get already updated proof files from 
     *
     * @return resultset for proof files
     */
    public function getProof_files($_order_id=0, $_item_id=0 )
    {
        $_resource = Mage::getSingleton('core/resource');
			$_read= $_resource->getConnection('core_read');
			$_designerTable = $_resource->getTableName('task_designer');
			
			$_select = $_read->select()
			   ->from($_designerTable,array('entity_id','file','heigh_res_file','file_type','quantity','file_size','postdate','status','approved_status'))
			   ->where('proof_type=?','proof')
			   ->where('order_quote_id=?',$_order_id)
			   ->where('item_id=?',$_item_id)
			   ->order('entity_id ASC');			
			$_proof_files = $_read->fetchAll($_select);
			
		/*printing the query */			
			//echo $_select->__toString();
		
		/*registering the */
		
		//Mage::register('proof_files', $_proof_files);
		
		return $_proof_files;
    }
	
/**
     * get already updated client files from 
     *
     * @return resultset for proof files
     */
    public function getCustomer_files($_order_id=0, $_item_id=0)
    {
      //  var_dump($_order_id);
		$_resource = Mage::getSingleton('core/resource');
			$_read= $_resource->getConnection('core_read');
			$_designerTable = $_resource->getTableName('task_designer');
			
			$_select = $_read->select()
			   ->from($_designerTable,array('entity_id','file','file_type','file_size','postdate','status','approve_date'))
			   ->where('proof_type=?','customer')
			   ->where('order_quote_id=?',$_order_id)
			   // ->where('increment_id=?',$_order_id)
			   ->where('item_id=?',$_item_id)			   
			   ->order('entity_id ASC');			
			$_proof_files = $_read->fetchAll($_select);
			
		/*printing the query */			
			//echo $_select->__toString();
		
		/*registering the */
		
		//Mage::register('proof_files', $_proof_files);
		
		return $_proof_files;
    }	
		
  /*
  		function getRemainingQty();
		@return: remaining approved quantity
  */
	  public function getRemainingQty($_order_id=0, $_item_id=0){
		  
		  	$_connection = Mage::getSingleton('core/resource');
			$_read = $_connection->getConnection('core_read');
			$_proofsTable = $_read->getTableName('proofs');
			
		    //fetching the total prooved quantity
		 	$_pr_sql = $_read->select()
						  ->from($_proofsTable,array('sum(quantity) as remainingQty'))
						  ->where('order_id=?',$_order_id)
						  ->Where('status=?','Approved')
						  ->where('item_id=?',$_item_id);								
			//var_dump($_pr_sql->__toString());
			$_rem_qty = $_read->fetchOne($_pr_sql);
			
			return $_rem_qty;	
		  
		  }
	  
  
    
    /**
     * AJAX TAB's
     * If you want to use an AJAX tab, uncomment the following functions
     * Please note that you will need to setup a controller to recieve
     * the tab content request
     *
     */
    /**
     * Retrieve the class name of the tab
     * Return 'ajax' here if you want the tab to be loaded via Ajax
     *
     * return string
     */
#   public function getTabClass()
#   {
#       return 'my-custom-tab';
#   }
    /**
     * Determine whether to generate content on load or via AJAX
     * If true, the tab's content won't be loaded until the tab is clicked
     * You will need to setup a controller to handle the tab request
     *
     * @return bool
     */
#   public function getSkipGenerateContent()
#   {
#       return false;
#   }
    /**
     * Retrieve the URL used to load the tab content
     * Return the URL here used to load the content by Ajax
     * see self::getSkipGenerateContent & self::getTabClass
     *
     * @return string
     */
#   public function getTabUrl()
#   {
#       return null;
#   }
}