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

/**

 * @var Mage_Page_Block_Html_Header $this

 */



$getItemCount=Mage::helper("checkout/cart")->getItemsCount();



if(Mage::helper('checkout/cart')->getSummaryCount() == "")

{

    $cartCount = "0";

}else{

    $cartCount = Mage::helper('checkout/cart')->getSummaryCount();

}



$quote = Mage::getSingleton('checkout/cart')->getQuote();



if(Mage::helper('checkout/cart')->getSummaryCount() == "")

{

    $subTotal = Mage::helper('core')->currency(0.00);

}else{

    $subTotal = Mage::helper('core')->currency($quote->getSubtotal());

}





$customerData = Mage::getSingleton('customer/session')->getCustomer();





?>





<div class="header-container">

    <div class="header">

        <?php if ($this->getIsHomePage()):?>

        <h1 class="logo"><strong><?php echo $this->getLogoAlt() ?></strong><a href="<?php echo $this->getUrl('') ?>" title="<?php echo $this->getLogoAlt() ?>" class="logo"><img src="<?php echo $this->getLogoSrc() ?>" alt="<?php echo $this->getLogoAlt() ?>" /></a></h1>

        <?php else:?>

        <a href="<?php echo $this->getUrl('') ?>" title="<?php echo $this->getLogoAlt() ?>" class="logo"><strong><?php echo $this->getLogoAlt() ?></strong><img src="<?php echo $this->getLogoSrc() ?>" alt="<?php echo $this->getLogoAlt() ?>" /></a>

        <?php endif?>

            	



	<div class="excptlogowrap">

    <div class="promotional_banner"><img src="<?php echo $this->getSkinUrl();?>images/promotional_banner.png" />

		<!--<span class="customer-support"><span class="top-phone-number">888.777.0223</span></span>-->

		

             

    </div>

    

	 <div class="top-links">

		<ul>

			

    			

                <li class="fast"><a href="<?php echo $this->getUrl('upload')?>"><?php echo $this->__('Artwork Uploader')?></a></li>

                <li><a href="<?php echo $this->getUrl('artwork')?>"><?php echo $this->__('Artwork Guides')?></a></li>

                <li><a href="<?php echo $this->getUrl('gallery')?>"><?php echo $this->__('Gallery')?></a></li>

                <li><a href="<?php echo $this->getUrl().'news/news-1.html' ?>"><?php echo $this->__('Our Clients')?></a></li>

				<?php /*?><li><a href="<?php echo $this->getUrl('artwork') ?>">Upload Artwork</a></li>

				<li><a href="<?php echo $this->getUrl().'news/news-1.html' ?>">News</a></li><?php */?>

				<li ><div class="get-quote-link">

                <a id="getqute" onclick="getQuote(<?php echo $getItemCount; ?>)" href="javascript:void(0)"><?php echo $this->__('Get a Quote')?></a></div></li>

				<li ><a title="My Account" href="<?php echo $this->getUrl(); ?>customer/account/"><?php echo $this->__('My Account')?></a></li>

				 <?php if (! Mage::getSingleton('customer/session')->isLoggedIn()): ?>

                <!--<li class="first"><a href="<?php //echo Mage::helper('customer')->getLoginUrl(); ?>" class="sign-min"><?php //echo $this->__('SIGN IN') ?></a></li>-->

                <li><a href="Javascript:void(0);" class="sign-min"><?php echo $this->__('Sign In') ?></a></li>

                <?php else: ?>

                <li ><a href="<?php echo Mage::helper('customer')->getLogoutUrl(); ?>"><?php echo $this->__('Sign out') ?></a></li>

                <?php endif; ?>

                <li class="last"><a href="<?php echo $this->getUrl('contact-us') ?>"><?php echo $this->__('Contact Us')?></a></li>

		</ul>

	</div>

    

	 <div class="social" style="display:none;">

                   <a href="#"><img src="<?php echo $this->getSkinUrl();?>images/rss.png" alt=""></a>

               <a href="#"><img src="<?php echo $this->getSkinUrl();?>images/twitter.png" alt=""></a>

               <a href="#"><img src="<?php echo $this->getSkinUrl();?>images/facebook.png" alt=""></a>

           </div>

        <div class="quick-access">

	    	



            

			<?php echo $this->getChildHtml('topSearch') ?>

           

           <div class="phone">

        	<a href="tel:1300721614"><img src="<?php echo $this->getSkinUrl();?>images/header_phone.jpg" alt="" border="0"></a>

        <!--<img src="<?php echo $this->getSkinUrl();?>images/aus-flag.png" alt=""><span>24 Hour Customer Support<br><span class="number"><?php echo $this->__('1300 72 16 14')?></span></span>--></div>

            <!--    Code by ART      -->

                <div id="boxes">

                    <div style="display:none" id="dialog" class="window">

                        <div style="font:normal 14px/20px Arial, Helvetica, sans-serif; padding:8px;">

                        <?php echo $this->getLayout()->createBlock('cms/block')->setBlockId('getquote')->toHtml() ?>

                        </div> 

                    </div>

      <!-- Mask to cover the whole screen -->

         <div style="width: 1478px; height: 602px; display: none; opacity: 0.8;" id="mask"></div>

         </div>

                

                



           

            <!--   End Code by ART      -->

            <?php /* ?><p class="welcome-msg"><?php echo $this->getWelcome() ?> <?php echo $this->getAdditionalHtml() ?></p><?php */ ?>

            <?php //echo $this->getChildHtml('topLinks') ?>

            <div class="linkswrapper">

		<div class="cart_bag" style="display:none;">

                   <img src="<?php echo $this->getSkinUrl(); ?>images/cart.png" alt="">

               </div>

		<?php

		 if (Mage::getSingleton('customer/session')->isLoggedIn())

		 {

		

		?>

		<p class="welcome-msg"><?php /*?><img src="<?php echo $this->getSkinUrl(); ?>images/welcome.png" alt=""><?php */?><?php echo $this->__('Welcome');?><img src="<?php echo $this->getSkinUrl();?>images/top_link_bg.png"  style="margin-top:5px; border:none 0px;"/><?php echo Mage::getSingleton('customer/session')->getCustomer()->getFirstname();?></p>

		<?php

		 }

		 else

		 {

		?>

               <p class="welcome-msg"><?php /*?><img src="<?php echo $this->getSkinUrl(); ?>images/welcome.png" alt="">&nbsp;<?php */?><?php echo $this->__('Welcome <img src="http://13expo.com.au/skin/frontend/default/13expo/images/top_link_bg.png" style="margin-top:5px;"/> Guest')?></p>

	       <?php

		 }

	       ?>

            <ul class="links customerlinks">

<?php /*?>		<li class="first"><a title="My Account" href="<?php echo $this->getUrl(); ?>customer/account/"><?php echo $this->__('My Account')?></a></li>

<?php */?>

		

                <?php /*?><?php if (! Mage::getSingleton('customer/session')->isLoggedIn()): ?>

                <li class="first"><a href="<?php //echo Mage::helper('customer')->getLoginUrl(); ?>" class="sign-min"><?php //echo $this->__('SIGN IN') ?></a></li>

                <li class="last"><a href="Javascript:void(0);" class="sign-min"><?php echo $this->__('Sign In') ?></a></li>

                <?php else: ?>

                <li class="last"><a href="<?php echo Mage::helper('customer')->getLogoutUrl(); ?>"><?php echo $this->__('Sign out') ?></a></li>

                <?php endif; ?>

                <?php */?>

            </ul>

		<div class="quick_main">

	                    <div class="quick-link2">

                    <div class="block block-cart" id="topBlockCart" onclick="cartHide()">				

                        <div class="summary">

                            <p class="amount"><a href="Javascript:void(0);"><span><?php //echo $this->__('Shopping Cart') ?><span style="font-size:14px;"><?php echo $cartCount; ?></span>&nbsp;<?php echo $this->__('Item(s)')?>&nbsp;<span style="font-size:14px;"><?php echo $subTotal; ?></span></span></a></p>

                            <!--<p class="subtotal">

                            <span class="price"><?php //echo $subTotal; ?></span>	</p>	-->					

                        </div>

                    </div>

                    

                    <!-- Mini cart page   -->

                    <div class="block-content" style="display:none;" id="topCartContent">

                        <?php echo $this->getChildHtml('topcart'); ?>

                    </div>

                    <!--   End Mini Cart  -->

                    

                    <div class="checkout-link">

                        <ul class="links">

                           <!-- <li><a href="<?php echo $this->getUrl(); ?>checkout/">Checkout</a></li>-->

			    <li style="background:none; margin-top:-2px; margin-left:36px; width:114px;">

                	<a href="<?php echo $this->getUrl(); ?>onepagecheckout/"><img src="<?php echo $this->getSkinUrl("images/check_out1.jpg")?>" style="border:none 0px;"></a>

               

                        </ul>

                    </div>

		</div>

                </div>

			    </div>



            <?php //echo $this->getChildHtml('store_language') ?>

            

            

            <?php if (! Mage::getSingleton('customer/session')->isLoggedIn()): ?>

            <div id="mini-login-top" style="display: none;">

		<div id="mini-login-content" style="top: 0px;">

                    <?php echo $this->getChildHtml('mini_login') ?>

                </div>

            </div>

            <?php else: ?>

            <?php /***********/ ?>

            <?php endif; ?>

            

            

            

        </div>

	</div>

	

        <?php echo $this->getChildHtml('topContainer'); ?>

	

    </div>

</div>

    



 <?php

  /*top menu*/

   //echo $this->getLayout()->createBlock('cms/block')->setBlockId('topmenu')->toHtml() ;

   

   

  ?>

  

  

  

  

  <!--Top Menu-->

  

  <input type="hidden" id="catimage"/>

<input type="hidden" id="topcat" />

<?php $_helper       = Mage::helper('catalog/category') ?>



<?php
 $_categories   = $_helper->getStoreCategories() ?>

<?php if (count($_categories) > 0): ?>

<div class="nav-container" style="margin-top:0px;">

    <ul id="nav">

	

        <?php 

		

		foreach($_categories as $_category): ?>

       

            <li class="level0 level-top parent">

                <a href="<?php echo $_helper->getCategoryUrl($_category) ?>" class="level-top level_0">

                    <span><span><?php echo $_category->getName() ?></span></span>

                </a>

                

            </li>



        <?php endforeach; ?>

        

    </ul>

<?php endif; ?></div>

  

   <!-- end of Top Menu-->

  

  

  

  

  

  

  

  

  

  <div class="factory-container">

  <div class="factory"><img src="<?php echo $this->getSkinUrl();?>images/factory_direct.jpg" /></div>

  </div>

<script type="text/javascript"> 

//function cartHide(){

//    jQuery("#topCartContent").css("display","block");

//

//}

</script>





<script type="text/javascript">

  var __lc_buttons = __lc_buttons || [];

  __lc_buttons.push({

    elementId: 'LiveChat_1392881178',

    language: 'en',

    skill: '0'

  });

</script>