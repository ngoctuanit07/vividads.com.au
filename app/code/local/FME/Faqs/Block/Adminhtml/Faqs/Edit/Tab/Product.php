<?php
/**
 * Faqs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 
 * @category   FME
 * @package    Faqs
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Faqs_Block_Adminhtml_Faqs_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{
  
	public function __construct()
    {
        parent::__construct();
		echo '<script type="text/javascript" src="' . $this->getJsUrl() . 'faqs/jquery-1.4.2.min.js"></script>';
        $this->setId('unitedsol_faqs_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        
        $this->setRowClickCallback('FaqsRowClick');
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }
    
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
  	protected function _addColumnFilterToCollection($column)
    {

    if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
 			 
            	$this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
 

      if($column->getId()=="product_faqs")
      {
	
		$productIds = $this->_getSelectedProducts();
		$collection2 = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
			->addAttributeToFilter('status', 1)//enabled
			->addAttributeToFilter('visibility', 4);//catalog, search
		$this->setCollection($collection2);
	
      	if (empty($productIds)) {
               $productIds = 0;
         }
		 
		if ($column->getFilter()->getValue()) {
			
			$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
		
		}
		elseif(!empty($productIds)) {
		
			$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
			
		}            
      	     	
      }else{
      
            parent::_addColumnFilterToCollection($column);
      }
       
        return $this;
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)//enabled
			->addAttributeToFilter('visibility', 4);//catalog, search
            
    	if ($store->getId()) {
			
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
	    
            $collection->addAttributeToSelect('*');
        }
            
              
        $this->setCollection($collection);

        
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }    

    protected function _prepareColumns()
    {
       
       
		$this->addColumn('product_faqs', array(
			'header_css_class' => 'a-center',
			'type'      => 'checkbox',
			'name'      => 'product_faqs',
			'values'    => $this->_getSelectedProducts(),
			'align'     => 'center',
			'index'     => 'entity_id'
		));
                     
     
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
		
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
	    'sortable'  => true,
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
	    'sortable'  => true,
            'width'     => '80',
            'index'     => 'sku'
        ));
        
        
        
    	if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
        
        
        
        
        
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
	    'sortable'  => true,
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));
    	
        
    
		
        return parent::_prepareColumns();
    }

	
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    
    
    protected function _getSelectedProducts($json=false)
    {	
    	$temp = $this->getRequest()->getPost('product_id2s');
    	$store = $this->_getStore();
    	
    	if($temp)
    	{
    		parse_str($temp, $product_ids);
    		    		
    	}       
		
        $faqid=$this->getParam('id');	
		$faqsTable = Mage::getSingleton('core/resource')->getTableName('faqs');
		$faqsTopicTable = Mage::getSingleton('core/resource')->getTableName('faqs_topics');
			
			
		if(isset($faqid)){
		    
		
			
		    $sqry = "select f.product_ids from ".$faqsTable." f where f.faqs_id=$faqid";
		    $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		    $select = $connection->query($sqry);
		    $prds = $select->fetchAll();
		    
		    		    
		    $prds_ids=$prds['0']['product_ids'];
		    
		    if($prds_ids){
			
			$joinMethod = 'join';  //join with faqs table
			$_prod=Mage::getModel('catalog/product')->getCollection();
			$_prod->getSelect()->$joinMethod(array('f'=>$faqsTable), 'e.entity_id in ('.$prds_ids.') and f.faqs_id='.$faqid, array('f.*'));
			
			$this->setCollection($_prod);
		    }
		    else {
			
			$joinMethod = 'join';  //join with faqs table
			$_prod=Mage::getModel('catalog/product')->getCollection();
			$_prod->getSelect()->$joinMethod(array('f'=>$faqsTable), 'e.entity_id like  concat("%"+f.product_ids+"%") and f.faqs_id=-5', array('f.*'));
			$this->setCollection($_prod);
		    }
		}
		else {
		    $joinMethod = 'join';  //join with faqs table
		    $_prod=Mage::getModel('catalog/product')->getCollection();
		    $_prod->getSelect()->$joinMethod(array('f'=>$faqsTable), 'e.entity_id like  concat("%"+f.product_ids+"%") and f.faqs_id=-5', array('f.*'));
		    $this->setCollection($_prod);
		}
	    
            $products=$_prod->getColumnValues('entity_id'); 
            $selected_products=array();
            
            
            if($json==true)
        		{        	
        		foreach($products as $key => $value)
        			{	 
        			$selected_products[$value]='1';
        			}
        		return Zend_Json::encode($selected_products); 
        	}        	
        	else
        		{
        			
        			foreach($products as $key => $value)
        			{	 
        				if((isset($product_ids[$value]))&&($product_ids[$value]==0))        				
        				{
        					
        				}else	          				
        				$selected_products[$value]='0';			
        			}
        			
				if(isset($product_ids))        			
        			foreach($product_ids as $key => $value)
        			{
        				if($value==1)
        				$selected_products[$key]='0';
        			}
        			
        			
        			
        			
        			
        		return array_keys($selected_products);
        	}
        	
        return $products;
    }
     
    //add javascript before/after grid html
    protected function _afterToHtml($html){
    	return $this->_prependHtml() . parent::_afterToHtml($html) . $this->_appendHtml();
    }

    
    private function _prependHtml(){
    		$gridName = $this->getJsObjectName();
	    	$test='php in javascript';
    	        	$html=
<<<EndHTML

	<script type="text/javascript">
	//<![CDATA[
	
	jQuery( function() {
					 
	    var pid;
	    var prid = jQuery( "#prid" );
	    var resetBtn;
	    
	    jQuery( 'button.scalable' ).each( function() {
		if( jQuery( 'span', this ).html() == "Reset Filter" )
		    jQuery( this ).click( function() {
			prid.val( '' );
		    })
		    .ajaxComplete( function() {
			console.log( 'request completed' );
		    });
	    });
	    
	    if( prid.val() === '' ) {
		pid = new Array();
		
		jQuery( 'input.checkbox' ).each( function() {
		    if( this.checked && this.value != "on" )
			pid.push( this.value );
			
		});
		console.log( pid );
		pid.sort();
	    }
	    else
		pid = prid.val().split( ',' );
	   
	    jQuery( 'input.checkbox' ).click( function() {
		//console.log( this.checked );
		var val = this.value;
		
		if( this.value === "on" && this.checked === true){
		    //alert('select ALL ');
		    pid = new Array();
		    jQuery( 'input.checkbox' ).each( function() {
		    if( this.checked && this.value != "on")
			pid.push( this.value );
		    });
		}
		else if( this.value === "on" && this.checked === false){
		    //alert('unselect ALL ');
		    pid = new Array();
		}
		else {
		
		    if( this.checked === true && jQuery.inArray( this.value, pid ) == -1 ) {
			pid.push( this.value );
			pid.sort();
		    }
		    else {
			pid = jQuery.grep( pid, function( value ) {
			    return value !== val;
			});
		    }
		}
		
		prid.val( pid.join( ',' ) );

	    });
	    
	    prid.val( pid.join( ',' ) );
	    console.log( prid.val() );
	});


    function FaqsRowClick(grid, event)
    {
    }
        
//]]>
        </script>
	
EndHTML;

    		return $html;
    }
    
    
    private function _appendHtml(){
    	$html= '';
    	return $html;
    }

}

