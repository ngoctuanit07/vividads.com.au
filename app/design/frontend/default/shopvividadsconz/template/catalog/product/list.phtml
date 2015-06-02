<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
?>

<?php
/**
 * Product list template
 *
 * @see Mage_Catalog_Block_Product_List
 */


if(is_object(Mage::registry('current_category')))
{
    $_categoryId = Mage::registry('current_category')->getId();
}
else{
    $_categoryId = "";
}

//echo "cat Id: ".$_categoryId;

$collection = Mage::getModel('catalog/category')->getCategories($_categoryId);

//echo "<br>C Count : ".$collection->count();

?>
<?php
    $_productCollection=$this->getLoadedProductCollection();
    $_helper = $this->helper('catalog/output');
?>
<?php if(!$_productCollection->count() && !$collection->count()): ?>
    <p class="note-msg">
	<?php echo $this->__('There are no products matching the selection.') ?>
    </p>
<?php else: ?>
<div class="category-products">
    <?php echo $this->getToolbarHtml() ?>
    <?php // List mode ?>
    <?php if($this->getMode()!='grid'): ?>
    <?php $_iterator = 0; ?>
    <ol class="products-list" id="products-list">
    <?php foreach ($_productCollection as $_product): ?>
        <li class="item<?php if( ++$_iterator == sizeof($_productCollection) ): ?> last<?php endif; ?>">
            <?php // Product Image ?>
            <a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" class="product-image"><img src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize(135); ?>" width="135" height="135" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" /></a>
            <?php // Product description ?>
            <div class="product-shop">
                <div class="f-fix">
                    <?php $_productNameStripped = $this->stripTags($_product->getName(), null, true); ?>
                    <h2 class="product-name"><a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $_productNameStripped; ?>"><?php echo $_helper->productAttribute($_product, $_product->getName() , 'name'); ?></a></h2>
                    <?php if($_product->getRatingSummary()): ?>
                    <?php echo $this->getReviewsSummaryHtml($_product) ?>
                    <?php endif; ?>
                    <?php echo $this->getPriceHtml($_product, true) ?>
                    <?php if($_product->isSaleable()): ?>
                        <p><button type="button" title="<?php echo $this->__('Add to Cart') ?>" class="button btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($_product) ?>')"><span><span><?php echo $this->__('Add to Cart') ?></span></span></button></p>
                    <?php else: ?>
                        <p class="availability out-of-stock"><span><?php echo $this->__('Out of stock') ?></span></p>
                    <?php endif; ?>
                    <div class="desc std">
                        <?php echo $_helper->productAttribute($_product, $_product->getShortDescription(), 'short_description') ?>
                        <a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $_productNameStripped ?>" class="link-learn"><?php echo $this->__('Learn More') ?></a>
                    </div>
                    <ul class="add-to-links">
                        <?php if ($this->helper('wishlist')->isAllow()) : ?>
                            <li><a href="<?php echo $this->helper('wishlist')->getAddUrl($_product) ?>" class="link-wishlist"><?php echo $this->__('Add to Wishlist') ?></a></li>
                        <?php endif; ?>
                        <?php if($_compareUrl=$this->getAddToCompareUrl($_product)): ?>
                            <li><span class="separator">|</span> <a href="<?php echo $_compareUrl ?>" class="link-compare"><?php echo $this->__('Add to Compare') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
    </ol>
    <script type="text/javascript">decorateList('products-list', 'none-recursive')</script>

    <?php else: ?>

    <?php // Grid Mode ?>

    <?php /* ?>
    <?php $_collectionSize = $_productCollection->count() ?>
    <?php $_columnCount = $this->getColumnCount(); ?>
    <?php $i=0; foreach ($_productCollection as $_product): ?>
        <?php if ($i++%$_columnCount==0): ?>
        <ul class="products-grid">
        <?php endif ?>
            <li class="item<?php if(($i-1)%$_columnCount==0): ?> first<?php elseif($i%$_columnCount==0): ?> last<?php endif; ?>">
                <a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" class="product-image"><img src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize(135); ?>" width="135" height="135" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" /></a>
                <h2 class="product-name"><a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($_product->getName(), null, true) ?>"><?php echo $_helper->productAttribute($_product, $_product->getName(), 'name') ?></a></h2>
                <?php if($_product->getRatingSummary()): ?>
                <?php echo $this->getReviewsSummaryHtml($_product, 'short') ?>
                <?php endif; ?>
                <?php echo $this->getPriceHtml($_product, true) ?>
                <div class="actions">
                    <?php if($_product->isSaleable()): ?>
                        <button type="button" title="<?php echo $this->__('Add to Cart') ?>" class="button btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($_product) ?>')"><span><span><?php echo $this->__('Add to Cart') ?></span></span></button>
                    <?php else: ?>
                        <p class="availability out-of-stock"><span><?php echo $this->__('Out of stock') ?></span></p>
                    <?php endif; ?>
                    <ul class="add-to-links">
                        <?php if ($this->helper('wishlist')->isAllow()) : ?>
                            <li><a href="<?php echo $this->helper('wishlist')->getAddUrl($_product) ?>" class="link-wishlist"><?php echo $this->__('Add to Wishlist') ?></a></li>
                        <?php endif; ?>
                        <?php if($_compareUrl=$this->getAddToCompareUrl($_product)): ?>
                            <li><span class="separator">|</span> <a href="<?php echo $_compareUrl ?>" class="link-compare"><?php echo $this->__('Add to Compare') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
        <?php if ($i%$_columnCount==0 || $i==$_collectionSize): ?>
        </ul>
        <?php endif ?>
        <?php endforeach ?>
        <script type="text/javascript">decorateGeneric($$('ul.products-grid'), ['odd','even','first','last'])</script>
    <?php endif; ?>
    <?php */ ?>
    
    <?php /**************Start code here********************/ ?>
    
    <!--  For category only  -->
    
    <?php $_collectionSizeCat = $collection->count() ?>
    <?php $_columnCountCat = 3;
    
    $currentUrl = Mage::helper('core/url')->getCurrentUrl();
    
    
     /****************** Start check for catalogsearch 18_02_2014 ***************************/
    if(!strpos($currentUrl,'catalogsearch'))
    {
    
    ?>
    <?php $i=-1; 
			foreach ($collection as $_childCategory):
	 ?>
    
    <?php
    //echo "kkkkkkk";
        $cur_category=Mage::getModel('catalog/category')->load($_childCategory->getId());
        $catName = $cur_category->getName();
        
        if($cur_category->getListthumbnail() == "")
        {
            $catThumbImageUrl=Mage::getBaseUrl('media').'catalog/category/'.$cur_category->getThumbnail();
        }else{
            $catThumbImageUrl=Mage::getBaseUrl('media').'catalog/category/'.$cur_category->getListthumbnail();
        }
        
        
        
        
        //$_category2 = Mage::getModel('catalog/category')->load($_childCategory->getId());
        //$collection2 = Mage::getModel('catalog/product')->getCollection();
        //$collection2->addCategoryFilter($_category2);
        //$collection2->getSelect()->joinLeft(
        //array('_inventory_table'=>$collection2->getResource()->getTable('cataloginventory/stock_item')),
        //"_inventory_table.product_id = e.entity_id",
        //array('is_in_stock', 'manage_stock')
        //);
        //$collection2->addExpressionAttributeToSelect('on_top',
        //'(CASE WHEN (((_inventory_table.use_config_manage_stock = 1) AND (_inventory_table.is_in_stock = 1)) OR ((_inventory_table.use_config_manage_stock = 0) AND (1 - _inventory_table.manage_stock + _inventory_table.is_in_stock >= 1))) THEN 1 ELSE 0 END)',
        //array());
        //$collection2->getSelect()->order('on_top DESC');
        //$collection2->getSelect()->order('entity_id ASC');
        //$collection2->getSelect()->limit('1');
        //if ($collection2->count() > 0){
        //    $_product2 = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($collection2->getFirstItem()->getId());
        //    
        //    //echo "First Product name :".$_product2->getName();
        //    //echo "<br>";
        //}
        //else{
        //    //$_product = Mage::getModel('catalog/product');
        //}
        
        
    ?>
        <?php 
				$i++; 
				
				if ($i%$_columnCountCat==0): 
				
				$flagUl=0;
				 ?>
        
        <ul class="products-grid">
        <?php endif ?>
        
            <li class="item<?php if($i%$_columnCountCat==0): ?> first<?php elseif($i%$_columnCountCat==2): ?> last<?php endif; ?>">
           	
            <div class="cat_browse">
     
                <a href="<?php echo $cur_category->getUrl() ?>" title="" class="product-image">
                <img src="<?php echo $catThumbImageUrl; ?>" width="220" height="220" alt="" />
                </a>
                <h2 class="product-name">
                    <a href="<?php echo $cur_category->getUrl() ?>" title="">
                        <?php echo $catName; ?>
                    </a>
                </h2>
            </div>
            <div class="btn-bg1">
            
                <div class="browse-btn" style="margin:0px 110px 0px 110px;">
                    <a class="category-browse" title="" href="<?php echo $cur_category->getUrl() ?>">
                        <span>Browse</span>
                    </a>  
                </div></div>
                <div class="price-box"> </div>
            </li>
               
        <?php if ($i%$_columnCountCat==2 || $i==$_collectionSizeCat): $flagUl=1; ?>
        </ul>
        <?php endif ?>
        <?php endforeach ?>
        <?php if($flagUl==0) { ?>
        </ul>
        <?php }
        
    }
    /****************** End check for catalogsearch 18_02_2014 ***************************/
        
        ?>
        
        <!--  End category code  -->
    
    <!--  For product only  -->
    <?php
        $_collectionSize = $_productCollection->count();
        //echo "<br>P Count : ".$_collectionSize;
    ?>
    <?php $_columnCount = 3; ?>
    <?php $i=-1; foreach ($_productCollection as $_product): ?>
    <?php
    $obj = Mage::getModel('catalog/product');
    $_productnew = $obj->load($_product->getId());
    $productDescription=$_productnew->getShortDescription();
    ?>
        <?php $i++; if ($i%$_columnCount==0): ?>
        <ul class="products-grid">
        <?php endif ?>
            <li class="hover_pro item<?php if($i%$_columnCount==0): ?> first<?php elseif($i%$_columnCount==2): ?> last<?php endif; ?>">
                   <div class="sale-label sale-top-left"></div>
                <div class="mask-product"> 
		<div class="pro-det-con">
                <a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" class="product-image"><img src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize(220); ?>" width="220" height="220" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" /></a>

                <?php
                /**************For assign to promotion product************************/
                if($_product->getPromotionicon() !='')
                {
                    $filePath=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."blfa_files/".$_product->getPromotionicon()
                ?>
                    <div class="promoicon-image">
                        <img width="75" height="75.5" style="position:absolute;  right:0;  top:0; " src="<?php echo $filePath; ?>" />
                    </div>
                    <?php
                }
                /**************End assign to promotion product************************/
                ?>
                    <?php if($_product->isSaleable()): ?> 
                    <?php else: ?>
                        <div class="out-of-stock"><img width="106" height="20" style="position:absolute;  left:0;  bottom:0; " src="<?php echo $this->getSkinUrl(); ?>images/ace-outofstock2png.png" class="promoicon-image"></div>
                    <?php endif; ?>
                        <div style="top: 227px;" class="product-detail-content">
                            <div class="product-detail-description">
                                <?php echo  substr($productDescription,0,150)."..."; ?>
                            </div>
                            <p class="product-detail-read-more">
                                <a href="<?php echo $_product->getProductUrl() ?>">Read More</a>
                            </p>
                        </div>
		    </div>
                    <h2 class="product-name"><a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($_product->getName(), null, true) ?>"><?php echo $_helper->productAttribute($_product, $_product->getName(), 'name') ?></a></h2>
                
                </div>
                      <div class="btn-bg">
                  <div class="pric-btn" style=" text-align: center; color: #fff; font: 12px 'gotham_mediumregular'; text-decoration: none; height:26px; width:134px; float:left; line-height:26px;">
                      <?php //echo $this->getPriceHtml($_product, true);
					  
					  echo Mage::helper('core')->currency($_product->getPrice());
					  
					  ?>
                    </a>  
                </div>
                <div class="browse-btn">
                    <a class="category-browse" title="<?php echo $this->stripTags($_product->getName(), null, true) ?>" href="<?php echo $_product->getProductUrl() ?>">
                        <span>Browse</span>
                    </a>  
                </div></div>
                <?php //echo $this->getPriceHtml($_product, true) ?>
            </li>
        <?php if ($i%$_columnCount==2 || $i==$_collectionSize): ?>
        </ul>
        <?php endif ?>
        <?php endforeach ?>
        <script type="text/javascript">decorateGeneric($$('ul.products-grid'), ['odd','even','first','last'])</script>
        <!--  End product code  -->
        
        
        
        
    <?php endif; ?>
    <?php /****************End Code******************/ ?>

    <div class="toolbar-bottom">
        <?php //echo $this->getToolbarHtml() ?>
    </div>
</div>
<?php endif; ?>

