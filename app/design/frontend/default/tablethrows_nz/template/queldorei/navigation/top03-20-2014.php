<?php
/**
 * Top menu for store
 *
 * @see Queldorei_Navigation_Block_Navigation
 */
?>
<?php
/**
 * $this->renderCategoriesMenuHtml() supports optional arguments:
 * int Level number for list item class to start from
 * string Extra class of outermost list items
 * string If specified wraps children list in div with this class
 */
?>
<!-- navigation BOF -->
<?php $_menu = $this->renderCategoriesMenuHtml(0, 'level-top', 'sub-wrapper' ) ?>
<?php if($_menu): ?>
<nav class="queldorei">
    <ul id="queldoreiNav">
	    <?php if (Mage::getStoreConfig('shoppersettings/navigation/home')): ?>
         <li class="level0 level-top">
         	<a href="<?php echo $this->getBaseUrl(); ?>"><span><?php echo $this->__('Home'); ?></span></a>
         </li>
     <?php endif; ?>
        <?php
        echo $_menu;
        $custom_tab = Mage::getModel('cms/block')->load('shopper_navigation_block');
        if($custom_tab->getIsActive()) {
            echo '
            <li class="level0 level-top parent custom-block">
                <a href="#" class="level-top">
                    <span>'.$custom_tab->getTitle().'</span>
                </a>
                <div class="sub-wrapper" style="width:'.Mage::getStoreConfig('shoppersettings/navigation/custom_block_width').'px">'.$this->getLayout()->createBlock('cms/block')->setBlockId('shopper_navigation_block')->toHtml().'</div>
            </li>';
        }
        ?>
    </ul>
    <div class="nav-top-title"><?php echo $this->__('Navigation'); ?></div>
    <select id="navigation_select" name="navigation_select" onchange="if(this.value!='')window.location=this.value">
        <?php $m = new Queldorei_ShopperSettings_Block_Navigation(); echo $m->renderCategoriesSelectOptions(); ?>
    </select>
</nav>
<?php endif ?>
<!-- navigation EOF -->