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
class Aitoc_Aitemails_Model_Rewrite_CoreMysql4ConfigDataCollection extends Mage_Core_Model_Mysql4_Config_Data_Collection
{
    public function addScopeMultiPathFilter($scope, $scopeId, $pathes)
    {
        if (!is_array($pathes))
        {
            $pathes = array($pathes);
        }
        $this->_select
            ->where('scope=?', $scope)
            ->where('scope_id=?', $scopeId)
            ->where('REPLACE(path, "/", "_") IN ( ' . implode(',', $pathes) . ' )');
        return $this;
    }
    
    public function addScopePathFilter($scope, $scopeId, $path)
    {
        $this->_select
            ->where('scope=?', $scope)
            ->where('scope_id=?', $scopeId)
            ->where('path = ?', $path);
        return $this;
    }
}