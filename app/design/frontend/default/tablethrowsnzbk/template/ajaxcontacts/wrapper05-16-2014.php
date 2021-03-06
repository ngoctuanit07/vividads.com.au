<?php

/**

 * Magpleasure Ltd.

 *

 * NOTICE OF LICENSE

 *

 * This source file is subject to the EULA

 * that is bundled with this package in the file LICENSE-CE.txt.

 * It is also available through the world-wide-web at this URL:

 * http://www.magpleasure.com/LICENSE-CE.txt

 *

 * =================================================================

 *                 MAGENTO EDITION USAGE NOTICE

 * =================================================================

 * This package designed for Magento COMMUNITY edition

 * Magpleasure does not guarantee correct work of this extension

 * on any other Magento edition except Magento COMMUNITY edition.

 * Magpleasure does not provide extension support in case of

 * incorrect edition usage.

 * =================================================================

 *

 * @category   Magpleasure

 * @package    Magpleasure_Ajaxcontacts

 * @version    1.0

 * @copyright  Copyright (c) 2011 Magpleasure Ltd. (http://www.magpleasure.com)

 * @license    http://www.magpleasure.com/LICENSE-CE.txt

 */

?>

<?php /** @var Magpleasure_Ajaxcontacts_Block_Wrapper $this */ ?>

<?php if ($this->getEnabled()): ?>

<div id="contacts_button" class="contacts_button"   onclick="showContactsForm({

        url: '<?php echo $this->getUrl('ajaxcontacts/index/form'); ?>',

        post_url: '<?php echo $this->getUrl('ajaxcontacts/index'); ?>',

        width: <?php echo $this->getWidth(); ?>,

        height: <?php echo $this->getHeight(); ?>,

        ok_label: '<?php echo $this->__('Submit'); ?>',

        cancel_label: '<?php echo $this->__('Cancel'); ?>'

    }); return false;" style="top: <?php echo $this->getTop();  ?>px;"></div>

  <script type="text/javascript" >

    document.observe('dom:loaded', function(event){

         Event.observe(window, 'scroll', function() {

             var button = document.getElementById('contacts_button');

             var scroll = getScroll();

            /*

			if (button){

                button.style.top = (<?php echo $this->getTop();  ?> + scroll) + 'px';

            }

			*/

        });

    });

</script>



<?php endif; ?>



<div class="uploader_artwork">

<a class="upload_icon artwork_upload" href="<?php echo $this->getUrl('upload')?>" title="Upload Artwork"><span></span></a>

<a class="upload_icon get_quote" href="javascript:void(0)" title="Quick Quote" id="getqute" onclick="getQuote(<?php echo $getItemCount; ?>)" >
<span></span></a>

<a class="upload_icon design-templates" href="<?php echo $this->getUrl('artwork')?>" title="Design Templates"><span></span></a>

</div>