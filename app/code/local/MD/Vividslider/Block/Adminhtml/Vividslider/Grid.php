<?php
/**
 * MD_Vividslider.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Vividslider
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php

class MD_Vividslider_Block_Adminhtml_Vividslider_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      
	  parent::__construct();
	  $this->setId('vividsliderGrid');
      $this->setDefaultSort('vividslider_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $_store_id = $this->getRequest()->getParam('store');
	  if(!isset($_store_id)){
	  $collection = Mage::getModel('vividslider/vividslider')->getCollection();
	 
	//  var_dump($collection->getSelect()->__toString());
	  }else{
		 $collection = Mage::getModel('vividslider/vividslider')
		 							->getCollection()
									->addFieldToFilter('store_id',$_store_id)
									; 
		 
		// var_dump($collection->getSelect()->__toString());							
		  }
      $this->setCollection($collection);
	  
	  return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('vividslider_id', array(
          'header'    => Mage::helper('vividslider')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'vividslider_id',
      ));

      
	  $this->addColumn('title', array(
          'header'    => Mage::helper('vividslider')->__('Slider Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  
	 $this->addColumn('store', array(
          'header'    => Mage::helper('vividslider')->__('Store Name'),
          'align'     =>'left',
          'index'     => 'store_name',
      ));
	  
 	$this->addColumn('category', array(
          'header'    => Mage::helper('vividslider')->__('Category'),
          'align'     =>'left',
          'index'     => 'category_name',
      ));
 
  $this->addColumn('Width', array(
          'header'    => Mage::helper('vividslider')->__('Weight PX '),
          'align'     =>'left',
          'index'     => 'width',
      )); 
  $this->addColumn('height', array(
          'header'    => Mage::helper('vividslider')->__('Height PX'),
          'align'     =>'left',
          'index'     => 'height',
      ));	  
	
		  	  
	  
	/*$this->addColumn('filename', array(
            'header'    => Mage::helper('vividslider')->__('Logo'),
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'filename',
            'type'      => 'image',
            'escape'    => true,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => new MD_Vividslider_Block_Adminhtml_Grid_Renderer_Image,
        )); */

     
	  
	   $this->addColumn('status', array(
          'header'    => Mage::helper('vividslider')->__('Status'),
          'align'     => 'left',
          'width'     => '100px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
	  
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('vividslider')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('vividslider')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
					 
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('vividslider')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('vividslider')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('vividslider_id');
        $this->getMassactionBlock()->setFormFieldName('vividslider');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vividslider')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vividslider')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('vividslider/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('vividslider')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('vividslider')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}