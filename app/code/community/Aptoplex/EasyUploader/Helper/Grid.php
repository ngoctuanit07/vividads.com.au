<?php
/**
 * Class Aptoplex_EasyUploader_Block_Adminhtml_Upload_Grid
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Block_Adminhtml_Upload_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Internal constructor
     */
    public function _construct() {
        parent::_construct();

        $this->setId('aptoplex_easyuploader');
        $this->setDefaultSort('uploaded_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('aptoplex_easyuploader/upload_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($row) {
//        if (Mage::getSingleton('admin/session')->isAllowed('aptoplex_easyuploader/upload/actions/view')) {
//            return $this->getUrl('*/*/view', array('order_id' => $row->getId()));
//        }
        return false;
    }


    protected function _prepareColumns() {

//        $this->addColumn('entity_id', array(
//            'header'    => $this->_getHelper()->__('ID'),
////            'width'     => '50px',
//            'sortable' => true,
//            'type'      => 'number',
//            'index'     => 'entity_id'
//        ));

        $this->addColumn('order_id', array(
            'header'    => $this->_getHelper()->__('Order #'),
            'width'     => '80px',
//            'sortable' => true,
            'type'      => 'text',
            'index'     => 'order_id',
//            'filter' => false
        ));

        $this->addColumn('original_filename', array(
            'header'    => $this->_getHelper()->__('Original Filename'),
            'width'     => '300px',
            'type'      => 'text',
            'index'     => 'original_filename'
        ));

        $this->addColumn('new_filename', array(
            'header'    => $this->_getHelper()->__('New Filename'),
            'width'     => '300px',
            'type'      => 'text',
            'index'     => 'new_filename'
        ));

        $this->addColumn('additional_comments', array(
            'header'    => $this->_getHelper()->__('Additional Comments'),
//            'width'     => '50px',
            'type'      => 'longtext',
//            'sortable'  => false,
            'index'     => 'additional_comments'
        ));

        $this->addColumn('email_address', array(
            'header'    => $this->_getHelper()->__('Customer E-Mail'),
//            'width'     => '50px',
            'type'      => 'text',
            'index'     => 'email_address'
        ));

        // Only show the IP Address column if we're NOT in demo mode.
        if (!Aptoplex_EasyUploader_Helper_Data::RUN_IN_DEMO_MODE) {
            $this->addColumn('ip_address', array(
                'header' => $this->_getHelper()->__('IP Address'),
                'width' => '100px',
                'type' => 'ip',
                'index' => 'ip_address'
            ));
        }

        $this->addColumn('uploaded_at', array(
            'header'    => $this->_getHelper()->__('Uploaded at'),
            'width'     => '150px',
            'sortable' => true,
            'type'      => 'datetime',
            'index'     => 'uploaded_at'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('aptoplex_easyuploader/upload/download')) {
            $this->addColumn('action',
                array(
                    'header'    => $this->_getHelper()->__('Action'),
                    'width'     => '70px',
                    'type'      => 'action',
                    'getter'    => 'getEntityId',
                    'actions'   => array(array(
                        'caption' => $this->_getHelper()->__('Download'),
                        'url'     => array('base' => '*/*/download'),
                        'field'   => 'entity_id'
                    )),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'news',  
					 
                )
            );
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        if (Mage::getSingleton('admin/session')->isAllowed('aptoplex_easyuploader/upload/delete')) {
            $this->setMassactionIdField('entity_id');
            $this->getMassactionBlock()->setFormFieldName('entity_ids');
            $this->getMassactionBlock()->setUseSelectAll(false);

            // Disallow deleting of uploads if we're in demo mode.
            $url = '';
            $alertText = $this->_getHelper()->__('Are you sure you want to delete the selected items?');
            if (Aptoplex_EasyUploader_Helper_Data::RUN_IN_DEMO_MODE) {
                $url = $this->getUrl('*/easyuploader_upload');
                $alertText = $this->_getHelper()->__('Sorry, deleting uploads is not allowed in demo mode!');
            }
            else {
                $url = $this->getUrl('*/easyuploader_upload/massDelete');
            }
            $this->getMassactionBlock()->addItem('delete_upload', array(
                'label' => Mage::helper('aptoplex_easyuploader')->__('Delete'),
                'url' => $url,
                'confirm' => $alertText
            ));
        }

        return $this;
    }

    /**
     * Returns the main data helper class
     *
     * @return Aptoplex_EasyUploader_Helper_Data
     */
    protected function _getHelper() {
        return Mage::helper('aptoplex_easyuploader');
    }


    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}