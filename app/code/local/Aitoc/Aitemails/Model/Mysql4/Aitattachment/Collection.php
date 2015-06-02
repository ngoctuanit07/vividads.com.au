<?php
/**
 * Email Template Manager
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitemails
 * @version      2.0.13
 * @license:     POwWoRanFs2vVHb6wy050abrHO3ftlyCeMSf01dXU3
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitemails_Model_Mysql4_Aitattachment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('aitemails/aitattachment');
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product|array|integer|null $product
     * @return Mage_Downloadable_Model_Mysql4_Aitfile_Collection
     */
    public function addTemplateToFilter($template)
    {
        if (empty($template)) {
            $this->addFieldToFilter('template_id', '');
        } elseif (is_array($template)) {
            $this->addFieldToFilter('template_id', array('in' => $template));
        } elseif ($template instanceof Mage_Core_Model_Email_Template) {
            $this->addFieldToFilter('template_id', $template->getId());
        } else {
            $this->addFieldToFilter('template_id', $template);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param integer $storeId
     * @return Mage_Downloadable_Model_Mysql4_Aitfile_Collection
     */
    public function addTitleToResult($storeId=0)
    {
        $this->getSelect()
            //->joinLeft(array('default_title_table' => $this->getTable('aitemails/aitattachment_title')),
            //    '`default_title_table`.attachment_id=`main_table`.attachment_id AND `default_title_table`.store_id = 0',
            //    array('default_title' => 'title'))
            ->joinLeft(array('store_title_table' => $this->getTable('aitemails/aitattachment_title')),
                '`store_title_table`.attachment_id=`main_table`.attachment_id AND `store_title_table`.store_id = ' . intval($storeId),
                //array('store_title' => 'title','title' => new Zend_Db_Expr('IFNULL(`store_title_table`.title, `default_title_table`.title)')))
                array('store_title' => 'title'))
            ->order('main_table.sort_order ASC')
            ->order('title ASC');

        return $this;
    }

}