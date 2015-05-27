<?php
class IWD_OrderManager_Model_Archive_Order extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'iwd_order_manager_archive_order';

    const MAX_ORDERS_IN_ONE_TRANSACTION = 10000;

    const XML_PATH_ARCHIVE_ORDER_ENABLE = 'iwd_ordermanager/archive/enable';
    const XML_PATH_ARCHIVE_ORDER_STATUSES = 'iwd_ordermanager/archive/order_statuses';
    const XML_PATH_AUTO_ARCHIVE_AFTER_DAYS = 'iwd_ordermanager/archive/auto_archive_after_days';

    protected $table_sales_flat_order;
    protected $table_archive_order_grid;
    protected $table_sales_flat_invoice;
    protected $table_archive_invoice_grid;
    protected $table_sales_flat_shipment;
    protected $table_archive_shipment_grid;
    protected $table_sales_flat_creditmemo;
    protected $table_archive_creditmemo_grid;

    protected $table_archive_order_grid_columns;
    protected $table_archive_invoice_grid_columns;
    protected $table_archive_shipment_grid_columns;
    protected $table_archive_creditmemo_grid_columns;

    protected function _construct()
    {
        $this->_init('iwd_ordermanager/archive_order');

        $core_resource = Mage::getSingleton('core/resource');

        $this->table_sales_flat_order = $core_resource->getTableName('sales_flat_order_grid');
        $this->table_archive_order_grid = $core_resource->getTableName('iwd_sales_archive_order_grid');
        $this->table_sales_flat_invoice = $core_resource->getTableName('sales_flat_invoice_grid');
        $this->table_archive_invoice_grid = $core_resource->getTableName('iwd_sales_archive_invoice_grid');
        $this->table_sales_flat_shipment = $core_resource->getTableName('sales_flat_shipment_grid');
        $this->table_archive_shipment_grid = $core_resource->getTableName('iwd_sales_archive_shipment_grid');
        $this->table_sales_flat_creditmemo = $core_resource->getTableName('sales_flat_creditmemo_grid');
        $this->table_archive_creditmemo_grid = $core_resource->getTableName('iwd_sales_archive_creditmemo_grid');

        $this->table_archive_order_grid_columns = array('entity_id', 'increment_id', 'status', 'store_id', 'store_name',
            'customer_id', 'base_grand_total', 'base_total_paid', 'grand_total', 'total_paid', 'base_currency_code',
            'order_currency_code', 'shipping_name', 'billing_name', 'created_at', 'updated_at');

        $this->table_archive_invoice_grid_columns = array('entity_id', 'increment_id', 'store_id', 'base_grand_total',
            'grand_total', 'order_id', 'state', 'store_currency_code', 'order_currency_code', 'base_currency_code',
            'global_currency_code', 'order_increment_id', 'created_at', 'order_created_at', 'billing_name');

        $this->table_archive_shipment_grid_columns = array('entity_id', 'increment_id', 'store_id', 'total_qty',
            'order_id', 'shipment_status', 'order_increment_id', 'created_at', 'order_created_at', 'shipping_name');

        $this->table_archive_creditmemo_grid_columns = array('entity_id', 'increment_id', 'store_id', 'store_to_order_rate',
            'base_to_order_rate', 'grand_total', 'store_to_base_rate', 'base_to_global_rate', 'base_grand_total', 'order_id',
            'creditmemo_status', 'state', 'invoice_id', 'store_currency_code', 'order_currency_code', 'base_currency_code',
            'global_currency_code', 'order_increment_id', 'created_at', 'order_created_at', 'billing_name',);
    }

    /***************************** SETTINGS **********************************/
    public function isAllowArchiveOrders()
    {
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/archive');
        $enable = $this->isArchiveEnable();
        return ($permission_allow && $enable);
    }

    public function isAllowRestoreOrders()
    {
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/archive_restore');
        $enable = $this->isArchiveEnable();
        return ($permission_allow && $enable);
    }

    public function isArchiveEnable()
    {
        return Mage::getStoreConfig(self::XML_PATH_ARCHIVE_ORDER_ENABLE) ? 1 : 0;
    }

    public function getArchiveOrderStatuses()
    {
        $statuses = Mage::getStoreConfig(self::XML_PATH_ARCHIVE_ORDER_STATUSES);
        return explode(",", $statuses);
    }

    public function isAutoArchiveAfterSeconds()
    {
        $days = Mage::getStoreConfig(self::XML_PATH_AUTO_ARCHIVE_AFTER_DAYS);

        if(empty($days) || $days <= 0){
            return null;
        }

        return $days * 60 * 60 * 24;
    }
    /********************************************************** end SETTINGS */


    /************************* GET ARCHIVE COLLECTIONS ***********************/
    public function getArchiveOrdersCollection()
    {
        $order_model = Mage::getModel('iwd_ordermanager/order_grid');
        $collection = Mage::getResourceModel('iwd_ordermanager/archive_order');
        $selected_columns = $order_model->getSelectedColumnsArray();

        return Mage::getModel('iwd_ordermanager/order_grid')->getOrdersCollection($selected_columns, $collection);
    }

    public function getArchiveInvoicesCollection()
    {
        return Mage::getResourceModel('iwd_ordermanager/archive_invoice');
    }

    public function getArchiveCreditmemosCollection()
    {
        return Mage::getModel('iwd_ordermanager/archive_creditmemo')->getCollection();
    }

    public function getArchiveShipmentsCollection()
    {
        return Mage::getModel('iwd_ordermanager/archive_shipment')->getCollection();
    }
    /******************************************* end GET ARCHIVE COLLECTIONS */

    /******************************* LOG RESULT ******************************/
    protected  $_result_error = array();
    protected  $_result_not_allow = array();
    protected  $_result_archived = array();

    public function resultError()
    {
        return $this->_result_error;
    }

    public function resultNotAllowedOrders()
    {
        return array(
            'count'=> count($this->_result_not_allow),
            'orders' => $this->_result_not_allow,
        );
    }

    public function resultArchivedOrders()
    {
        return array(
            'count'=> count($this->_result_archived),
            'orders' => $this->_result_archived,
        );
    }

    /******************************************************** end LOG RESULT */
    public function getOldOrdersForArchive(){
        $time = $this->isAutoArchiveAfterSeconds();

        if(empty($time)) {
            return array();
        }


        $time = Mage::getModel('core/date')->gmtTimestamp() - $time;
        $time = date('Y-m-d', $time);

        $order_ids = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToFilter('status', array('in' => $this->getArchiveOrderStatuses()))
            ->addFieldToFilter('created_at', array('to' => $time))
            ->getColumnValues('entity_id');

        return $order_ids;
    }

    public function addFilterToOrders($order_ids)
    {
        if (!is_array($order_ids))
            $order_ids = array($order_ids);

        $filtered_order_ids = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToFilter('status', array('in' => $this->getArchiveOrderStatuses()))
            ->addFieldToFilter('entity_id', array('in' => $order_ids))
            ->getColumnValues('entity_id');

        $this->_result_not_allow = array_diff($order_ids, $filtered_order_ids);
        $this->_result_archived = $filtered_order_ids;

        return $filtered_order_ids;
    }

    public function addOrdersToArchive($order_ids, $filter = true)
    {
        try {
            Mage::dispatchEvent('iwd_ordermanager_archive_orders_before', $order_ids);

            if($filter)
                $order_ids = $this->addFilterToOrders($order_ids);

            if (empty($order_ids)){
                return true;
            }

            $count = count($order_ids);

            for($i=0; $i < $count; $i+=50)
            {
                $ids = array_slice($order_ids, $i, $i + 50);

                $this->transaction = "";
                $filtered_order_ids = "'" . implode("','", $ids) . "'";

                $this->deleteFromArchiveTables($filtered_order_ids);
                $this->insertIntoArchiveTables($filtered_order_ids);
                $this->deleteFromOriginTables($filtered_order_ids);

                $this->sqlRunQuery();
            }

            Mage::dispatchEvent('iwd_ordermanager_archive_orders_after', $order_ids);
        } catch (Exception $e) {
            Mage::dispatchEvent('iwd_ordermanager_archive_orders_fail', array('order_ids' => $order_ids, 'exception' => $e));
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $this->_result_error = $e;
            return false;
        }

        return true;
    }

    public function restoreOrdersFromArchive($order_ids)
    {
        try {
            Mage::dispatchEvent('iwd_ordermanager_restore_archive_orders_before', $order_ids);

            if (!is_array($order_ids))
                $order_ids = array($order_ids);

            if (empty($order_ids)){
                return true;
            }

            $this->_result_not_allow = array();
            $this->_result_archived = $order_ids;


            $count = count($order_ids);

            for($i=0; $i < $count; $i+=50)
            {
                $ids = array_slice($order_ids, $i, $i + 50);

                $this->transaction = "";
                $_order_ids = "'" . implode("','", $ids) . "'";

                $this->deleteFromOriginTables($_order_ids);
                $this->insertIntoOriginTables($_order_ids);
                $this->deleteFromArchiveTables($_order_ids);

                $this->sqlRunQuery();
            }


            Mage::dispatchEvent('iwd_ordermanager_restore_archive_orders_after', $order_ids);
        } catch (Exception $e) {
            Mage::dispatchEvent('iwd_ordermanager_restore_archive_orders_fail', array('order_ids' => $order_ids, 'exception' => $e));
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $this->_result_error = $e;
            return false;
        }

        return true;
    }

    protected function deleteFromOriginTables($order_ids)
    {
        $this->sqlDeleteFromTable($this->table_sales_flat_order)
            ->sqlWhere("`{$this->table_sales_flat_order}`.`entity_id` IN ({$order_ids});")

            ->sqlDeleteFromTable($this->table_sales_flat_invoice)
            ->sqlWhere("`{$this->table_sales_flat_invoice}`.`order_id` IN ({$order_ids});")

            ->sqlDeleteFromTable($this->table_sales_flat_shipment)
            ->sqlWhere("`{$this->table_sales_flat_shipment}`.`order_id` IN ({$order_ids});")

            ->sqlDeleteFromTable($this->table_sales_flat_creditmemo)
            ->sqlWhere("`{$this->table_sales_flat_creditmemo}`.`order_id` IN ({$order_ids});");
    }

    protected function deleteFromArchiveTables($order_ids)
    {
        $this->sqlDeleteFromTable($this->table_archive_order_grid)
            ->sqlWhere("`{$this->table_archive_order_grid}`.`entity_id` IN ({$order_ids});")

            ->sqlDeleteFromTable($this->table_archive_invoice_grid)
            ->sqlWhere("`{$this->table_archive_invoice_grid}`.`order_id` IN ({$order_ids});")

            ->sqlDeleteFromTable($this->table_archive_shipment_grid)
            ->sqlWhere("`{$this->table_archive_shipment_grid}`.`order_id` IN ({$order_ids});")

            ->sqlDeleteFromTable($this->table_archive_creditmemo_grid)
            ->sqlWhere("`{$this->table_archive_creditmemo_grid}`.`order_id` IN ({$order_ids});");
    }

    protected function insertIntoOriginTables($order_ids)
    {
        $this->sqlCopyFromTableToTable($this->table_archive_order_grid, $this->table_sales_flat_order, $this->table_archive_order_grid_columns)
            ->sqlWhere("`{$this->table_archive_order_grid}`.`entity_id` IN ({$order_ids});")

            ->sqlCopyFromTableToTable($this->table_archive_invoice_grid, $this->table_sales_flat_invoice, $this->table_archive_invoice_grid_columns)
            ->sqlWhere("`{$this->table_archive_invoice_grid}`.`order_id` IN ({$order_ids});")

            ->sqlCopyFromTableToTable($this->table_archive_shipment_grid, $this->table_sales_flat_shipment, $this->table_archive_shipment_grid_columns)
            ->sqlWhere("`{$this->table_archive_shipment_grid}`.`order_id` IN ({$order_ids});")

            ->sqlCopyFromTableToTable($this->table_archive_creditmemo_grid, $this->table_sales_flat_creditmemo, $this->table_archive_creditmemo_grid_columns)
            ->sqlWhere("`{$this->table_archive_creditmemo_grid}`.`order_id` IN ({$order_ids});");
    }

    protected function insertIntoArchiveTables($order_ids)
    {
        $this->sqlCopyFromTableToTable($this->table_sales_flat_order, $this->table_archive_order_grid, $this->table_archive_order_grid_columns)
            ->sqlWhere("`{$this->table_sales_flat_order}`.`entity_id` IN ({$order_ids});")

            ->sqlCopyFromTableToTable($this->table_sales_flat_invoice, $this->table_archive_invoice_grid, $this->table_archive_invoice_grid_columns)
            ->sqlWhere("`{$this->table_sales_flat_invoice}`.`order_id` IN ({$order_ids});")

            ->sqlCopyFromTableToTable($this->table_sales_flat_shipment, $this->table_archive_shipment_grid, $this->table_archive_shipment_grid_columns)
            ->sqlWhere("`{$this->table_sales_flat_shipment}`.`order_id` IN ({$order_ids});")

            ->sqlCopyFromTableToTable($this->table_sales_flat_creditmemo, $this->table_archive_creditmemo_grid, $this->table_archive_creditmemo_grid_columns)
            ->sqlWhere("`{$this->table_sales_flat_creditmemo}`.`order_id` IN ({$order_ids});");
    }


    /*************************************************************************/
    protected $transaction = "";

    protected function sqlDeleteFromTable($source_table)
    {
        $this->transaction .= "DELETE FROM `{$source_table}`";
        return $this;
    }

    protected function sqlCopyFromTableToTable($source_table, $target_table, $columns_array)
    {
        $columns = "`" . implode("`,`", $columns_array) . "`";
        $this->transaction .= "INSERT INTO `{$target_table}` ({$columns}) SELECT {$columns} FROM `{$source_table}` ";
        return $this;
    }

    protected function sqlWhere($where_string)
    {
        if (!empty($where_string))
            $this->transaction .= " WHERE " . $where_string;
        return $this;
    }

    protected function sqlRunQuery()
    {
        //echo  $this->transaction; die;
        Mage::getSingleton('core/resource')
            ->getConnection('core_write')
            ->query($this->transaction);
        return $this;
    }
    /*************************************************************************/
}