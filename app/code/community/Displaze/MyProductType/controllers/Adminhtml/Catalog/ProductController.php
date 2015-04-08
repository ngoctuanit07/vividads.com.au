<?php

/* 
 * ProductController.php
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

include_once "Mage".DS."Adminhtml".DS."controllers".DS."Catalog".DS."ProductController.php";

class Displaze_MyProductType_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Displaze_MyProductType');
    }
    
    public function massChangeTypeAction()
    {
        $productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $type     = (string)$this->getRequest()->getParam('type_id');

        try {
            /*Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, array('type_id' => $type), $storeId);*/
            foreach($productIds as $productId) {
                $product = Mage::getModel('catalog/product')->load($productId);
                $product->setTypeId($type);
                $product->setPriceType(Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED);
                $product->setWeightType(1); //WEIGHT_TYPE_FIXED
                $product->setWeight($product->getWeight());
                $product->setPrice($product->getPrice());
                $product->save();
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated, product type has been changed to %s .', count($productIds), $type)
            );
            
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred while updating the product(s) status.'));
        }

        $this->_redirect('/*/', array('store'=> $storeId));

    }
}