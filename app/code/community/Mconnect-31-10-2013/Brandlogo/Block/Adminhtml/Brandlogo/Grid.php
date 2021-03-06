<?php
/**
 * M-Connect Solutions.
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */
?>
<?php

class Mconnect_Brandlogo_Block_Adminhtml_Brandlogo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('brandlogoGrid');
      $this->setDefaultSort('brandlogo_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('brandlogo/brandlogo')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('brandlogo_id', array(
          'header'    => Mage::helper('brandlogo')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'brandlogo_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('brandlogo')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  
      $this->addColumn('url', array(
			'header'    => Mage::helper('brandlogo')->__('Url'),
			'width'     => '150px',
			'index'     => 'url',
      ));
	  
      
       $this->addColumn('filename', array(
            'header'    => Mage::helper('brandlogo')->__('Logo'),
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'filename',
            'type'      => 'image',
            'escape'    => true,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => new Mconnect_Brandlogo_Block_Adminhtml_Grid_Renderer_Image,
        )); 

      $this->addColumn('status', array(
          'header'    => Mage::helper('brandlogo')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('brandlogo')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('brandlogo')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('brandlogo')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('brandlogo')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('brandlogo_id');
        $this->getMassactionBlock()->setFormFieldName('brandlogo');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('brandlogo')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('brandlogo')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('brandlogo/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('brandlogo')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('brandlogo')->__('Status'),
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