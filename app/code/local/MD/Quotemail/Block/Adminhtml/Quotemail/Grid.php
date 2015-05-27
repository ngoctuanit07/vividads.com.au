<?php
/**
 * MD_Quotemail.
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
 * @package    Quotemail
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php

class MD_Quotemail_Block_Adminhtml_Quotemail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      
	  parent::__construct();
	  $this->setId('quotemailGrid');
      $this->setDefaultSort('quotemail_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('quotemail/quotemail')->getCollection();
      $this->setCollection($collection);
	  
	  return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('quotemail_id', array(
          'header'    => Mage::helper('quotemail')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'quotemail_id',
      ));

       $this->addColumn('name', array(
          'header'    => Mage::helper('quotemail')->__('Template Model '),
          'align'     =>'left',
          'index'     => 'name',
      ));
	  $this->addColumn('title', array(
          'header'    => Mage::helper('quotemail')->__('Email Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  
	  
	  
 	$this->addColumn('subject', array(
          'header'    => Mage::helper('quotemail')->__('Subject'),
          'align'     =>'left',
          'index'     => 'subject',
      ));
	  
	  
     /* $this->addColumn('url', array(
			'header'    => Mage::helper('quotemail')->__('Url'),
			'width'     => '150px',
			'index'     => 'url',
      ));*/
	  
      
       /*$this->addColumn('filename', array(
            'header'    => Mage::helper('quotemail')->__('Logo'),
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'filename',
            'type'      => 'image',
            'escape'    => true,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => new MD_Quotemail_Block_Adminhtml_Grid_Renderer_Image,
        )); */

      $this->addColumn('templatetype', array(
          'header'    => Mage::helper('quotemail')->__('Template Type'),
          'align'     => 'center',
          'width'     => '100px',
          'index'     => 'templatetype',
          'type'      => 'options',
          'options'   => array(
              1 => 'HTML',
              2 => 'TEXT',
          ),
      ));
	  
	   $this->addColumn('status', array(
          'header'    => Mage::helper('quotemail')->__('Status'),
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
                'header'    =>  Mage::helper('quotemail')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('quotemail')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
					 
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('quotemail')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('quotemail')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('quotemail_id');
        $this->getMassactionBlock()->setFormFieldName('quotemail');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('quotemail')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('quotemail')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('quotemail/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('quotemail')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('quotemail')->__('Status'),
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