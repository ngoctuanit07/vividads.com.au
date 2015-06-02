<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Block_OrdersPro_Sales_Order_Grid_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row) {
        // poducts or SKUs $prefix
        if ($this->getColumn()->getIndex()=='product_names') {
            $prefix = 'p';
        } elseif ($this->getColumn()->getIndex()=='skus') {
            $prefix = 's';
        } elseif ($this->getColumn()->getIndex()=='product_options') {
            $prefix = 'o';
        } else {
            $prefix = 'a';
        }
        
        if ($prefix=='o') {
            // parse product option(s)
            $products = explode("\n", $row->getData($this->getColumn()->getIndex()));
            foreach ($products as $key=>$product) {
                if (strlen($product)>10) $product = @unserialize($product);
                if (isset($product['options']) && is_array($product['options'])) {
                    // custom options
                    $options = array();
                    foreach ($product['options'] as $option) {
                        $options[] = $option['label'] . ': ' . $option['value'];
                    }
                    $products[$key] = implode(', ', $options);
                } elseif (isset($product['bundle_options']) && is_array($product['bundle_options'])) {
                    // bundle
                    $options = array();
                    foreach ($product['bundle_options'] as $option) {
                        if (is_array($option['value'])) {
                            $values = array();
                            foreach ($option['value'] as $value) {
                                $values[] = $value['title'];
                            }
                            $options[] = $option['label'] . ': ' . implode(', ', $values);
                        } else {                        
                            $options[] = $option['label'] . ': ' . $option['value'];
                        }
                    }
                    $products[$key] = implode(', ', $options);
                } elseif (isset($product['simple_name']) && $product['simple_name']) {
                    // configurable
                    $products[$key] = $product['simple_name'];
                } elseif ($product===false) {
                    $products[$key] = 'Required value of MySQL setting: group_concat_max_len = 32768';
                } else {
                    //print_r($product);
                    $products[$key] = '';                    
                }               
            }            
        } else {
            $products = explode("\n", $this->htmlEscape($row->getData($this->getColumn()->getIndex())));
        }
        
        if (strpos(Mage::app()->getRequest()->getRequestString(), '/exportCsv/')) {            
            return implode('|', $products);
        }
                
        // add Thumbnails
        $divFlag = false;
        if (Mage::helper('orderspro')->isShowThumbnails() && $prefix=='p') {
            $productIds = explode("\n", $row->getData('product_ids'));
            $imgSize = intval(Mage::helper('orderspro')->getThumbnailHeight());
            foreach ($products as $key=>$value) {
                if (isset($productIds[$key]) && $productIds[$key]) {                    
                    $product = Mage::getModel('catalog/product')->setStoreId($row->getStoreId())->load($productIds[$key]);
                    if ($product && $product->getThumbnail() && $product->getThumbnail()!='no_selection') {
                        $imgUrl = $this->helper('catalog/image')->init($product, 'thumbnail')->resize($imgSize, $imgSize);                        
                    } else {
                        $imgUrl = Mage::getDesign()->getSkinUrl('images/placeholder/thumbnail.jpg');
                    }                                        
                    $products[$key] = '<div style="height:'.Mage::helper('orderspro')->getThumbnailHeight().'px;"><img src="'.$imgUrl.'" height="'.$imgSize.'" width="'.$imgSize.'" alt="" align="left" style="padding-right:2px;" />'.$products[$key].'</div>';
                }
            }
            $divFlag = true;
        }
        
        $prCount=count($products);
        if ($prCount>3) {
            $products[$prCount-1].='<a href="" onclick="$(\'hdiv_'.$row->getData('increment_id').'_'.$prefix.'\').style.display=\'none\'; $(\'a_'.$row->getData('increment_id').'_'.$prefix.'\').style.display=\'block\'; return false;" style="float:right; font-weight:bold; text-decoration: none;" title="'.Mage::helper('orderspro')->__('Less..').'">↑</a>'
                .'</div>'
                .'<a href="" id="a_'.$row->getData('increment_id').'_'.$prefix.'" onclick="$(\'hdiv_'.$row->getData('increment_id').'_'.$prefix.'\').style.display=\'block\'; this.style.display=\'none\'; return false;" style="float:right; font-weight:bold; text-decoration: none;" title="'.Mage::helper('orderspro')->__('More..').'">↓</a>';
            $products[2].='<div id="hdiv_'.$row->getData('increment_id').'_'.$prefix.'" style="display:none">'.$products[3];
            unset($products[3]);            
        }        
        return '<div style="cursor: text">'.implode(($divFlag?"\n":'<br/>'), $products).'</div>';
    }
}
