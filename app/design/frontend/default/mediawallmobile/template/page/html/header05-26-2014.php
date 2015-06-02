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

 * @package     default_iphone

 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)

 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)

 */

/**

 * @var Mage_Page_Block_Html_Header $this

 */

?>

<?php $_cartItems = $this->helper('checkout/cart')->getSummaryCount(); ?>

<div class="header-bg">

    <!--<a class="header-logo" href="<?php echo $this->getUrl('') ?>"></a>-->

	<h1 class="logo" style="float:left;margin-left:5px;margin-top:4px;">

	<a href="<?php echo $this->getUrl('') ?>" title="<?php echo $this->getLogoAlt() ?>" class="logo">

	<img src="<?php echo $this->getSkinUrl() ?>images/custom/bg_logo.png" alt="<?php echo $this->getLogoAlt() ?>" /></a>

	</h1>

	

	<div class="promotional_banner" style="cursor:pointer;text-align:center;width:80%; position:relative; top:5px;">

	<h1 style="font-size:10px;"><?php echo __('Red Carpet Backdrop And Event Backdrop');?></h1>

	

	<!--<img src="<?php echo $this->getSkinUrl();?>images/promotional_banner.png" />-->

		<span class="customer-support"><span class="top-phone-number">Sales & Support: 1300 72 16 14</span></span>  

    </div>

	

    <!--<div class="ph-number">1300 72 16 14</div>-->

</div>

<header>

    <div class="menu-wrapper">

        <dl id="menu">

            <dt class="menu dropdown"><a href="#" style="width: 35px !important;"></a></dt>

            <dd class="menu-box">

                <?php echo $this->getChildHtml('topLinks') ?>

                <?php //echo $this->getChildHtml('checkoutLinks') ?>

                <?php //echo $this->getChildHtml('accountLinks') ?>

            </dd>

            <dt class="cart-icon <?php echo $this->getInCart() ? 'active' : '' ?>">

                <a href="<?php echo $this->getUrl('checkout/cart'); ?>"><?php echo $_cartItems > 0 ? '<span>'.$_cartItems.'</span>' : ''; ?> Items(s) 

                <?php echo $this->helper('checkout')->formatPrice(Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal()); ?>



                </a>

                

            </dt>

            <dd></dd>

        </dl>

        <div class="search"><?php echo $this->getChildHtml('topSearch') ?></div>

    </div>

</header>

<?php echo $this->getChildHtml('topCart') ?>

