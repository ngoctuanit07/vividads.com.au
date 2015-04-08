<?php

/* 
 * Product.php
 * 
 * Copyright (c) 2012 Aftab Naveed <aftabnaveed@gmail.com>. 
 * 
 * This file is part of Displaze Web Services Inc..
 * 
 * Displaze Web Services Inc. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Displaze Web Services Inc. is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Displaze Web Services Inc..  If not, see <http ://www.gnu.org/licenses/>.
 */

class Displaze_MyProductType_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        //Change Product Type
        $this->getMassactionBlock()->addItem('type_id', array(
             'label'=> Mage::helper('catalog')->__('Change Product Type'),
             'url'  => $this->getUrl('*/*/massChangeType', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'type_id',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Type'),
                         'values' => array('bundle' => 'Bundle Product', 
                                'grouped' => 'Grouped Product', 
                                'configurable' => 'Configurable Product',
                                'virtual'   => 'Virtual Product',
                                'downloadable' => 'Downloadable Product')
                     )
             )
        ));
    }
}