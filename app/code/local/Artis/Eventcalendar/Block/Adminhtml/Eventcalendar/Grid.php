<?php

class Artis_Eventcalendar_Block_Adminhtml_Eventcalendar_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('eventcalendarGrid');
      $this->setDefaultSort('eventcalendar_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      
      $this->setTemplate('eventcalendar/eventcalendar.phtml');
      return;
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('eventcalendar/eventcalendar')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('eventcalendar_id', array(
          'header'    => Mage::helper('eventcalendar')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'eventcalendar_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('eventcalendar')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('eventcalendar')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('eventcalendar')->__('Status'),
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
                'header'    =>  Mage::helper('eventcalendar')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('eventcalendar')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('eventcalendar')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('eventcalendar')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('eventcalendar_id');
        $this->getMassactionBlock()->setFormFieldName('eventcalendar');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('eventcalendar')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('eventcalendar')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('eventcalendar/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('eventcalendar')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('eventcalendar')->__('Status'),
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