<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
//require_once 'Mage/Adminhtml/Block/Sales/Shipment/Grid.php';

class Partialshipping_Partialshipping_Block_Adminhtml_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

     /**
     * Initialization
     */
    //public function __construct()
    //{
    //    parent::__construct();
    //    $this->setId('sales_shipment_grid');
    //    $this->setDefaultSort('created_at');
    //    $this->setDefaultDir('DESC');
    //}

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_shipment_grid_collection';
    
    }

    /**
     * Prepare and set collection of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
       
        
        //$collection = Mage::getResourceModel($this->_getCollectionClass())->addAttributeToSort('created_at', 'DESC');
        //$collection = $this->_getCollectionClass();
        
	//20-2-2014 S
	$roleId = implode('', Mage::getSingleton('admin/session')->getUser()->getRoles());
	$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName(); 
	if($roleName == 'Warehouse'){
	  
	 $collection = Mage::getResourceModel($this->_getCollectionClass())->addAttributeToSort('created_at', 'DESC')->addAttributeToFilter('label_created', array('eq' => 1));
        
	 }else{
	   $collection = Mage::getResourceModel($this->_getCollectionClass())->addAttributeToSort('created_at', 'DESC');
	 }
	 
	  $this->setCollection($collection);
	
	
	
        ///15-11-2013 SOC
        //$dataID=array();
        //foreach($collection as $col){
        //    
        //    $data=$col->getData();
        //    $dataID=$data['order_id']; 
        //}
        ///15-11-2013 EOC 
        
        
        
        
        return parent::_prepareCollection();
    }

    /**
     * Prepare and add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Shipment #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Shipping Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        ///14-11-2013 SOC
        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'      => 'text',
        ));
        ///14-11-2013 EOC
        
        ///16-01-2014 SOC
        $this->addColumn('payment_status', array(
            'header' => Mage::helper('sales')->__('Payment Status'),
            'index' => 'payment_status',
            'type'      => 'text',
        ));
        
        $this->addColumn('track_number', array(
            'header' => Mage::helper('sales')->__('Traking Number'),
            'index' => 'track_number',
            'type'      => 'text',
        ));
        ///16-01-2014 EOC
        
        ///16-11-2013 SOC
        
        ///20-01-2014 SOC
        //$roleId = implode('', Mage::getSingleton('admin/session')->getUser()->getRoles());
        //$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
        //
        //if($roleName == 'Administrators'){
        /////20-01-2014 EOC
        //$this->addColumn('split_option', array(
        //    'header' => Mage::helper('sales')->__('Split Option'),
        //    'index' => 'split_option',
        //    'type'      => 'action',
        //    'getter'     => 'getId',
        //    'actions'   => array(
        //        array(
        //            'caption' => Mage::helper('sales')->__('Click here To Split the Shipment'),
        //            'url'     => array('base'=>'*/sales_shipment/view'),
        //            'field'   => 'shipment_id'
        //        )
        //    ),
        //    'filter'    => false,
        //    'sortable'  => false,
        //    'is_system' => true
        //    //'actions'   => array(
        //    //    
        //    //    array(
        //    //        'caption' => Mage::helper('sales')->__('Click here To Split the Shipment'),
        //    //        'url'     => array('base'=>'*/sales_shipment/view'),
        //    //        'field'   => 'shipment_id'
        //    //        //'onclick' => 'window.location = "' . Mage::getUrl('*/*/*', array('_current' => true)) . '"'
        //    //        //'onclick' => 'javascript:openMyPopup()'
        //    //    ),
        //    //    
        //    //),
        //        
        //));
        //
        //}
        ///16-11-2013 EOC
        
        
        
        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'*/sales_shipment/view'),
                        'field'   => 'shipment_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/shipment')) {
            return false;
        }

        return $this->getUrl('*/sales_shipment/view',
            array(
                'shipment_id'=> $row->getId(),
            )
        );
    }

    /**
     * Prepare and set options for massaction
     *
     * @return Mage_Adminhtml_Block_Sales_Shipment_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('shipment_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('PDF Packingslips'),
             'url'  => $this->getUrl('*/sales_shipment/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    /**
     * Get url of grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => true));
    }
}
?>

<?php /*//echo $this->getFormInitScripts() ?>
<script type="text/javascript">
    //function openMyPopup() {
    //    var url = '<?php echo $this->getUrl('adminhtml/dashboard/index') ?>?';
    //    if ($('browser_window') &amp;&amp; typeof(Windows) != 'undefined') {
    //        Windows.focus('browser_window');
    //        return;
    //    }
    //    var dialogWindow = Dialog.info(null, {
    //        closable:true,
    //        resizable:false,
    //        draggable:true,
    //        className:'magento',
    //        windowClassName:'popup-window',
    //        title:'Test popup Dialog',
    //        top:50,
    //        width:300,
    //        height:150,
    //        zIndex:1000,
    //        recenterAuto:false,
    //        hideEffect:Element.hide,
    //        showEffect:Element.show,
    //        id:'browser_window',
    //        url:url,
    //        onClose:function (param, el) {
    //            alert('onClose');
    //        }
    //    });
    //}
    //function closePopup() {
    //    Windows.close('browser_window');
    //}
</script>
*/ ?>