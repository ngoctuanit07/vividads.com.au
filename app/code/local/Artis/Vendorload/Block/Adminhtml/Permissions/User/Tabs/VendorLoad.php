<?php

class Artis_Vendorload_Block_Adminhtml_Permissions_User_Tabs_VendorLoad 
extends Mage_Adminhtml_Block_Widget_Grid
implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{

     /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();
        $this->setId('user_vendorload');
        $this->setTemplate('vendorload/grid.phtml');   
        
         $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText($this->__('No items'));
        $this->setDefaultSort('al_date');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    public function getAttributesBox($attributeCode, $label = '', $defaultSelect = null, $extraParams = null){
      $entityType = Mage::getModel('catalog/product')->getResource()->getTypeId();
      $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityType);
      $allSet = array();
      foreach($collection as $coll){
                $options[]=array('value'=>$coll->getAttributeSetId(),'label'=>$coll->getAttributeSetName());
        }
            array_unshift($options, array('label' => $label, 'value' => ''));

        $select = Mage::app()->getLayout()->createBlock('core/html_select')
                ->setName($attributeCode)
                ->setId($attributeCode)
                ->setTitle($label)
                ->setValue($defaultSelect)
                ->setExtraParams($extraParams)
                ->setOptions($options);
        return $select->getHtml();
    }

    public function getSelectBox($attributeCode, $label = '', $defaultSelect = null, $extraParams = null){
	$attributeCode='manufacturer';
        $options            = array();
        $collection            = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('sku');
	foreach($collection as $col){
		$options[]=array('value'=>$col->getId(),'label'=>$col->getSku());
	}
            array_unshift($options, array('label' => $label, 'value' => ''));

        $select = Mage::app()->getLayout()->createBlock('core/html_select')
                ->setName($attributeCode)
                ->setId($attributeCode)
                ->setTitle($label)
                ->setValue($defaultSelect)
                ->setExtraParams($extraParams)
                ->setOptions($options);
        return $select->getHtml();
    }
    /**
     * Charge collection with filter for user infos
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {		            

        $collection = Mage::getModel('vendorload/vendorload')
        	->getCollection()
                ->addFieldToFilter('admin_id', array('eq' => $this->getRequest()->getParam('user_id') ) );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
   /**
     * D�fini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
                               
        $this->addColumn('attribute_id', array(
            'header'=> Mage::helper('vendorload')->__('Attribute'),
            'type' => 'options',
            'index' => 'attribute_id',
            'options' => Mage::helper('vendorload')->ListAttributes()
        ));

        $this->addColumn('load', array(
            'header'=> Mage::helper('vendorload')->__('Load'),
            'index' => 'load'
        ));
                                    //$url=Mage::helper("adminhtml")->getUrl("vendorload/adminhtml_vendorload/edit/");

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('vendorload')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('vendorload')->__('Edit'),
                        'url'     => array(
                            'base'=>"vendorload/adminhtml_vendorload/edit/"
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('VendorLoad/Admin/UserAjaxGrid', array('_current' => true));
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        //return $this->fetchView($templateName);
    }
    


/**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel(){
        return $this->__('Admin Logger');
    }
 
    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle(){
        return $this->__('Click here to view the log for this product');
    }
 
    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab(){
        return true;
    }
 
    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden(){
        return false;
    }

}
