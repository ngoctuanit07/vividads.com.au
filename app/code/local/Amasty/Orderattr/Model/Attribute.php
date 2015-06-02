<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattr
*/
class Amasty_Orderattr_Model_Attribute extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('amorderattr/attribute');
    }
    
    public function dropAttributeField($code)
    {
        $connection = Mage::getSingleton('core/resource') ->getConnection('core_write');
        $sql = 'ALTER TABLE `' . $this->getResource()->getTable('amorderattr/order_attribute') . '` DROP `' . $code . '`';
        $connection->query($sql);
    }
    
    public function addAttributeField($code, $type)
    {
        $connection = Mage::getSingleton('core/resource') ->getConnection('core_write');
        $sql = 'ALTER TABLE `' . $this->getResource()->getTable('amorderattr/order_attribute') . '` ADD `' . $code . '` ' . $this->_getSqlType($type);
        $connection->query($sql);
    }
    
    protected function _getSqlType($fieldType)
    {
        $type = '';
        switch ($fieldType)
        {
            case 'textarea':
                $type = 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
            break;
            case 'text':
                $type = 'VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
            break;
            case 'date':
                $type = 'DATE NULL';
            break;
            case 'datetime':
                $type = 'DATETIME NULL';
            break;
            case 'boolean':
                $type = 'TINYINT(1) UNSIGNED NOT NULL';
            break;
            case 'select':
                $type = 'INT(11) UNSIGNED NOT NULL' ;
            break;
            default: // all others: select, checkboxes etc. for future use
                $type = 'VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
            break;
        }
        return $type;
    }
    
    protected function _beforeSave()
    {
        $connection = Mage::getSingleton('core/resource') ->getConnection('core_read');
        $sql = 'DESCRIBE `' . $this->getResource()->getTable('amorderattr/order_attribute') . '`';
        $tableInfo = $connection->fetchAssoc($sql);
        foreach ($tableInfo as $column)
        {
            if ('date' == $column['Type']) {
                if ($this->getData($column['Field'])) {
                    $this->setData($column['Field'], $this->_prepareDate($this->getData($column['Field']), 'Y-m-d'));
                } else {
                    $this->setData($column['Field'], '0000-00-00');
                }
            }
            if ('datetime' == $column['Type']) {
                if ($this->getData($column['Field'])) {
                    $this->setData($column['Field'], $this->_prepareDate($this->getData($column['Field']), 'Y-m-d H:i:s'));
                } else {
                    $this->setData($column['Field'], '0000-00-00 00:00:00');
                }
            }
        }
        return parent::_beforeSave();
    }
    
    protected function _prepareDate($value, $format)
    {
        $temp = Mage::app()->getLocale()->date($value);
        return Mage::getModel('core/date')->date($format, $temp->getTimestamp());
    }
}
