<?php

class Artis_Calendar_Block_Adminhtml_Calendar_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('calendarGrid');
      $this->setDefaultSort('calendar_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      //$this->setTemplate('eventcalendar/eventcalendarlist.phtml');
      //return;
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('calendar/calendar')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('calendar_id', array(
          'header'    => Mage::helper('calendar')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'calendar_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('calendar')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('calendar')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('calendar')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        //$this->addColumn('action',
        //    array(
        //        'header'    =>  Mage::helper('calendar')->__('Action'),
        //        'width'     => '100',
        //        'type'      => 'action',
        //        'getter'    => 'getId',
        //        'actions'   => array(
        //            array(
        //                'caption'   => Mage::helper('calendar')->__('Edit'),
        //                'url'       => array('base'=> '*/*/edit'),
        //                'field'     => 'id'
        //            )
        //        ),
        //        'filter'    => false,
        //        'sortable'  => false,
        //        'index'     => 'stores',
        //        'is_system' => true,
        //));
		
		//$this->addExportType('*/*/exportCsv', Mage::helper('calendar')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('calendar')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('calendar_id');
        $this->getMassactionBlock()->setFormFieldName('calendar');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('calendar')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('calendar')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('calendar/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('calendar')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('calendar')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}