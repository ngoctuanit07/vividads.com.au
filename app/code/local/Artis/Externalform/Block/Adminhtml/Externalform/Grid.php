<?php

class Artis_Externalform_Block_Adminhtml_Externalform_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('externalformGrid');
      $this->setDefaultSort('externalform_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
    /*  $collection = Mage::getModel('externalform/externalform')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();*/
  }

  protected function _prepareColumns()
  {
     /* $this->addColumn('externalform_id', array(
          'header'    => Mage::helper('externalform')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'externalform_id',
      ));*/

     /* $this->addColumn('title', array(
          'header'    => Mage::helper('externalform')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));*/

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('externalform')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

    /*  $this->addColumn('status', array(
          'header'    => Mage::helper('externalform')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));*/
	  
        /*$this->addColumn('action',
            array(
                'header'    =>  Mage::helper('externalform')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('externalform')->__('Edit'),
                        'url'       => array('base'=> '*///*/edit'),
                      /*  'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));*/
		
		//$this->addExportType('*/*/exportCsv', Mage::helper('externalform')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('externalform')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('externalform_id');
        $this->getMassactionBlock()->setFormFieldName('externalform');

       /* $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('externalform')->__('Delete'),
             'url'      => $this->getUrl('*//*/massDelete'),
             'confirm'  => Mage::helper('externalform')->__('Are you sure?')
        ));*/

        $statuses = Mage::getSingleton('externalform/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('externalform')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('externalform')->__('Status'),
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