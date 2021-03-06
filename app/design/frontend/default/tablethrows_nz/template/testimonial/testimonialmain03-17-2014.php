<div class="clinetTestim">
        <h2><span class="yell">Client</span> Testimonials</h2>
            <div class="textClient">
         
           <?php echo $this->getLayout()->createBlock('cms/block')->setBlockId('testimonial')->toHtml() ?>
           
           
            </div><!--textClient-->
   </div><!--clinetTestim-->

<?php $testimonials = $this->getRecents(); ?>
<?php $allow_media = (Mage::getStoreConfig('hm_testimonial/general/allow_media')) ? Mage::getStoreConfig('hm_testimonial/general/allow_media'):0; ?>
<?php $allow_media_popup = (Mage::getStoreConfig('hm_testimonial/general/allow_media_popup')) ? Mage::getStoreConfig('hm_testimonial/general/allow_media_popup'):0; ?>
<?php $height_slide = ((int)Mage::getStoreConfig('hm_testimonial/general/heightslide')) ? Mage::getStoreConfig('hm_testimonial/general/heightslide'):400; ?>

<script src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'testimonial/jquery.cycle.all.js'?>" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->getSkinUrl('css/testimonial/style.css')?>" />

<script type="text/javascript">
   jQuery(document).ready(function() {
	   jQuery('#slideshow_advance').cycle({
	      fx: '<?php echo (Mage::getStoreConfig('hm_testimonial/general/slider')) ? Mage::getStoreConfig('hm_testimonial/general/slider'):''; ?>',
	     timeout:  <?php echo (Mage::getStoreConfig('hm_testimonial/general/delay')) ? Mage::getStoreConfig('hm_testimonial/general/delay') : 1000; ?>
		});
		
	});
</script>

<?php if ($allow_media_popup==1) :?>
	<script type="text/javascript">
		var tb_pathToImage = "<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'testimonial/loading.gif';?>";
	</script>
	
</script>
<?php endif;?>

<style type="text/css">
.block{border:none 0px;}
.mw_testimonial_content{margin:0px;}
#slideshow_advance{height:215px !important;}

</style>
<div class="block">
	
	<div class="block-content" style="border: none 0px; margin-top:-40px;">
		<?php if($testimonials->count() < 1): ?>
			<div>
			<?php echo Mage::helper('testimonial')->__('No Testimonials'); ?>
			</div>
		<?php else: ?>
		<div id="slideshow_advance" style="height:<?php echo $height_slide; ?>px;"> 		
		<?php foreach ($testimonials as $testimonial): ?>
			<div> 
			<?php if ($allow_media) :?>						
				<div class="mw_testimonial_media gallery">
     			<?php $mediaUpload = $testimonial->getMedia();
     			$mediaURL = $testimonial->getMediaUrl();
     			if ($mediaUpload)
	     			$media = $this->getMediaUrl($mediaUpload);
	     		elseif ($mediaURL)
	     			$media = $mediaURL;
	     		else 
	     			$media = false;
     			
     			?>
     			<?php if ($media):?>
		     		<?php $file_ext = array(); ?>
					<?php $file_ext = explode('.',$media); ?>
					<?php $file_ext = $file_ext[sizeof($file_ext)-1];?>
					<?php $file_ext = strtolower($file_ext) ?>
					<?php if ($allow_media_popup==1) :?>
						<?php if (strpos($media, 'www.youtube.com')!==false || $file_ext=='flv'|| $file_ext=='avi'|| $file_ext=='mp3' || $file_ext=='mp4' ):?>
					<a rel="prettyPhoto[testimonial]" href="#mw_testimonial_popup_<?php echo $testimonial->getId(); ?>" >
	     				<img src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'testimonial/260.png';?>" width="<?php echo $this->getWidthMedia();?>" height="<?php echo $this->getHeightMedia();?>">
	     			</a>
					<div id="mw_testimonial_popup_<?php echo $testimonial->getId(); ?>" class="mw_et_content_video">							
						<div id="mw_testimonial_adv_<?php echo $testimonial->getId(); ?>"></div>						
					</div>						
					
	     			<script type='text/javascript'>
						  jwplayer('mw_testimonial_adv_<?php echo $testimonial->getId(); ?>').setup({
						    'flashplayer': '<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'testimonial/player.swf';?>',
							'skin': '<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'testimonial/glow.zip';?>',
						    'file': '<?php echo $media;?>',
							'stretching':'fill',
							'controlbar.position':'over',
						    'width': '520'
						  });
					</script>
						<?php elseif ($file_ext=='swf') :?>
					<a rel="prettyPhoto[testimonial]" href="#mw_testimonial_popup_<?php echo $testimonial->getId(); ?>" >						
						<img src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'testimonial/260.png';?>" width="<?php echo $this->getWidthMedia();?>" height="<?php echo $this->getHeightMedia();?>">
					</a>
					<div id="mw_testimonial_popup_<?php echo $testimonial->getId(); ?>" class='mw_et_content_video'>							
						<object classid="clsid:your-class-id" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0">
					 		<param name=movie value="">
					 		<param name="autoplay" value="false" />
					 		<param name="controller" value="true" />
					    	<param name=quality value=high>
					    	<param name="wmode" value="transparent">
					    	<embed src="<?php echo $media;?>" 
					    			quality="high" 
					    			wmode="transparent"
					    			width="520"
					    			pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"
					    			type="application/x-shockwave-flash">
					    	</embed> 
				    	</object>			
					</div>	
						<?php else :?>
					<a rel="prettyPhoto[testimonial]" href="#mw_testimonial_popup_<?php echo $testimonial->getId(); ?>">						
						<img src="<?php echo $media;?>" width="<?php echo $this->getWidthMedia();?>" height="<?php echo $this->getHeightMedia();?>">
					</a>
					<div id="mw_testimonial_popup_<?php echo $testimonial->getId(); ?>" class='mw_et_content_video'>							
						<img src="<?php echo $media;?>" width='520'>					
					</div>		
						<?php endif;?>
					<?php else :?>
						<?php if (strpos($media, 'www.youtube.com')!==false || $file_ext=='flv'|| $file_ext=='avi'|| $file_ext=='mp3' || $file_ext=='mp4' || $file_ext=='swf'):?>
							<img src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'testimonial/260.png';?>" width="<?php echo $this->getWidthMedia();?>" height="<?php echo $this->getHeightMedia();?>">
						<?php else: ?>
							<img src="<?php echo $media;?>" width="<?php echo $this->getWidthMedia();?>" height="<?php echo $this->getHeightMedia();?>">
			     		<?php endif;?>
					<?php endif;?>
     			<?php endif; ?>
     			</div>
     		<?php endif;?>
				<p class="mw_testimonial_content" style="line-height:25px; font-family:Arial, serif, sans-serif; font-size:12px;">
					<span><img src="<?php echo $this->getSkinUrl('images/testimonial/icon_top.gif')?>"></span>
					<?php  echo $testimonial->getPostContent(); ?>
					<span><img src="<?php echo $this->getSkinUrl('images/testimonial/icon_bt.gif')?>"></span>
				</p>
				<p style="float:right; margin-right:0.4em;">
					<?php echo $testimonial->getClientName();?> 
				</p>
			</div>
								  
		<?php endforeach; ?>
		</div>
		<div class="actions">
			<?php /*?><a href="<?php echo Mage::getUrl("testimonial"); ?>"></a><?php */?>
				<button type="button" class="button btn-cart" title="<?php echo $this->__('View All')?>" onClick="setLocation('<?php echo Mage::getUrl("testimonial"); ?>')"><span><span><?php echo Mage::helper('testimonial')->__('View All'); ?></span></span></button>
			
		</div>
		<?php endif; ?>
	</div>
</div>


 <div class="leftpanel_pic"><img src="<?php echo $this->getSkinUrl(); ?>images/guarantee.png" width="256" height="260" /></div>
    <div class="leftpanel_pic"><img src="<?php echo $this->getSkinUrl(); ?>images/fast_delivery.png" width="262" height="212" /></div>
