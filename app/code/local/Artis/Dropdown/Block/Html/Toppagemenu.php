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
 * @package     Mage_Page
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Top menu block
 *
 * @category    Mage
 * @package     Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Artis_Dropdown_Block_Html_Toppagemenu extends Mage_Page_Block_Html_Topmenu
{
   
    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Varien_Data_Tree_Node $menuTree
     * @param string $childrenWrapClass
     * @return string
     */
    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

		// $html .= $children;
		
		//exit;        
		foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount-1);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }
            
            $function = '';
            
            if($childLevel == 0)
            {
                
                $child->setIsPromotions($counter == $childrenCount);
            }
            
             if($childLevel == 1)
            {
                
                $child->setIsSelect($counter == 1);
                $function = 'onmouseover="settop(this);"';
            }
            

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            
            if($childLevel == 1)
            {
                $catid = end(explode('-',$child->getId()));
                $cur_category=Mage::getModel('catalog/category')->load($catid);
                if($cur_category->getThumbnail() != '')
                $image_cat = '<img   width="120px" height="120px" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/category/'.$cur_category->getThumbnail().'"/>';
            }
            
            if($childLevel == 2)
            {
                $catid = end(explode('-',$child->getId()));
                $cur_category=Mage::getModel('catalog/category')->load($catid);
                $childrenIds = $cur_category->getChildren();
                 
                $childrenIds = explode(',', $childrenIds);
                $childname = '';
                $childImage = '';
                foreach($childrenIds as $childrenId)
                {
                    $sub_category=Mage::getModel('catalog/category')->load($childrenId);
                     
                    $collection = $sub_category->getProductCollection();
                    //$collection->addAttributeToSelect('*')->setOrder('product_id','asc');
                    $collection->addAttributeToSelect('product_id')->setOrder('product_id','asc');
                    $collection->setPage(1, 1);
                    $k=1;
                    $link = '';
                    foreach ($collection as $cat_product) {
                        if($k==1)
                        {
                            //$link_product = Mage::getModel('catalog/product')->load($cat_product->getId());
                            //$link = $link_product->getProductUrl();
                            $link = $cat_product->getProductUrl();
                        }
                    }
                    
                    if($link == '')
                    {
                        $link = $sub_category->getUrl();
                    }
                     
                     if($sub_category->getName() != '')
                     $childname .= '<span class="last_cat"  onmouseover="changeHover(this,\''.$childrenId.'\');"><a href="'.$link.'">'.$sub_category->getName().'</a></span>';
                        if($sub_category->getThumbnail() != '')
                      $childImage .= '<span style="display:none;" class="last_cat"  id="cat_image_'.$childrenId.'"><a href="'.$link.'"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/category/'.$sub_category->getThumbnail().'"/></a></span>';
                    
                }
                $class = 'class="bold"';
            }
            
            $html .= '<a '.$function.' href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span '.$class.'>'.$image_cat
                .'<span>'. $this->escapeHtml($child->getName()) . '</span></span></a>'.$childname.'';
            

            if ($child->hasChildren() and $childLevel < 2) {
                if (!empty($childrenWrapClass)) {
                    $html .= '<div class="' . $childrenWrapClass . '">';
                }
                $html .= '<ul class="level' . $childLevel . '">';
                $html .= $this->_getHtml($child, $childrenWrapClass);
                $html .= '</ul>';

                if (!empty($childrenWrapClass)) {
                    $html .= '</div>';
                }
            }
            
            $html .= '</li>';
            
            if($childLevel > 1)
            {
                $html .= '<span class="current_image">'.$childImage.'</span>';
            }
            
            $counter++;
        }

        return $html;
    }

   

   
    /**
     * Returns array of menu item's classes
     *
     * @param Varien_Data_Tree_Node $item
     * @return array
     */
    protected function _getMenuItemClasses(Varien_Data_Tree_Node $item)
    {
        $classes = array();

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }
        
        if ($item->getIsSelect()) {
            $classes[] = 'select';
        }
        
        if ($item->getIsPromotions()) {
            $classes[] = 'promotions';
        }

        return $classes;
    }
}
