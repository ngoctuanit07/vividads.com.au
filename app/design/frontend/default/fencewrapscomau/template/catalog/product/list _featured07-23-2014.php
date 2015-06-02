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



    <?php if($this->getMode()!='grid'): ?>

    <?php $_iterator = 0; ?>

    

    <script type="text/javascript">decorateList('products-list', 'none-recursive')</script>



    <?php else: ?>





   

    

   

        

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

        	

        <?php endif ?>

            <li class="hover_pro item">

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

            	

                    <h2 class="product-name"><a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($_product->getName(), null, true) ?>">

					 <?php echo substr(($_helper->productAttribute($_product, $_product->getName(), 'name')),0,35)."...";?>



</a></h2>

                

                </div>

				<?php 

						$_p_model = $_product->getSku();

						$_store_id = Mage::app()->getStore()->getStoreId();

				?>

				

				<div style="background-color: #818181;

                    color: #F6F6F6;

                    font-size: 12px;

                    padding-left: 10px;

                    position: relative;

                    text-align: center;

                    text-shadow: 0px 1px 1px #4F4F4F;

                    top: 0;

    font-weight: bold;">Model # <?php echo $_store_id.'-'.$_p_model; ?></div>

				

                      <div class="btn-bg">

                  <div class="pric-btn" style=" text-align: center; color: #fff; font: 12px 'gotham_mediumregular'; text-decoration: none; height:26px; width:134px; float:left; line-height:26px;">

                     

					  <?php //echo $this->getPriceHtml($_product, true);

					  

					  echo Mage::helper('core')->currency($_product->getPrice());

					  

					  ?>

					  

                      

                </div>

				

				 

				

				

                <div class="browse-btn">

                    <a class="category-browse" title="<?php echo $this->stripTags($_product->getName(), null, true) ?>" href="<?php echo $_product->getProductUrl() ?>">

                        <span>Browse</span>

                    </a>  

                </div></div>

                <?php //echo $this->getPriceHtml($_product, true) ?>

            </li>

        <?php if ($i%$_columnCount==2 || $i==$_collectionSize): ?>

    

        <?php endif ?>

        <?php endforeach ?>

        

        

    <?php endif; ?>

    <?php /****************End Code******************/ ?>



   



<?php endif; ?>



