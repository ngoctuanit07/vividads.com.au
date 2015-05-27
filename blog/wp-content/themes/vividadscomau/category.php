<?php
/**
 * The template for displaying Category pages
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>
<div class="wrapper pagephp" style="width: 1200px; margin: 0px auto; position: relative; top: -9px;">
<div style="float:left; width:100%;text-align:center;" class="counters">

  <div style="float:left;width:20%;" class="project">

    <h3 id="counter" ></h3>

    <h4>Happy Customers</h4>

  </div>

  <div style="float:left;width:20%;" class="project">

    <h3 id="counter1"  ></h3>

    <h4>Awesome Projects</h4>

  </div>

  <div style="float:left;width:20%;" class="project"> 

    <h3 id="counter2" ></h3>

    <h4>Items Sold</h4>

  </div>

  <div style="float:left;width:20%;" class="project">

    <h3 id="counter3" ></h3>

    <h4>Facebook Likes</h4>

  </div>

  <div style="float:left;width:20%;" class="project">

    <h3 id="counter5" ></h3>

    <h4>Twitter Tweets</h4>

  </div>

  <div style="float:left;width:16.66%; display:none;" class="project">

    <h3 id="counter4" ><?php //echo rand(1,20);

	    /*$visitor_collection = Mage::getResourceModel('log/visitor_collection')->useOnlineFilter();

        $visitor_count = count($visitor_collection);    

        if(!empty($visitor_count) && $visitor_count > 0)

        {

            $cnt =  $visitor_count;             

                echo 'Visitors online :'.$cnt;

        }*/  

	/*	$visitor_count = Mage::getModel('log/visitor_online')

            ->prepare()

            ->getCollection()->count();

        if(!empty($visitor_count) && $visitor_count > 0)

        {

            $cnt =  $visitor_count;             

                echo $cnt;

        }  */

	?></h3>

    <h4>Visitors Today</h4>

  </div>

</div>
	<div class="wrapper_contrainer category" style="float:left;width:1200px; background:#fff;" >
    <div style="float: left; width: 260px; height: 100%; display:block;" class="myNavigation">
       <div class="col-left sidebar">

<link media="screen" type="text/css" href="http://vividexhibits.com.au/skin/frontend/base/default/css/phoneorder.css" rel="stylesheet">
<!--<div class="clearer"></div>-->
    <div style="margin-bottom:0px; border:none 0px;" class="block block-list block-compare">
    <div class="cate-heading">
    SHOP BY PRODUCT 
    </div>
    </div>
    <div class="block-content">
<ul class="left_nav">
    <li><a style="padding-right:20px;" href="http://vividads.com.au/portable-exhibition-display-walls.html">&nbsp;<span>1&nbsp;-</span>Exhibit Display Walls</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/event-media-backdrops.html">&nbsp;<span>2&nbsp;-</span>Media Backdrops</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/tradeshow-multimedia-displays.html">&nbsp;<span>3&nbsp;-</span>Multimedia Displays</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/shopping-center-display-kiosks.html">&nbsp;<span>4&nbsp;-</span>Shopping Center Kiosks</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/expo-shell-graphics.html">&nbsp;<span>5&nbsp;-</span>Expo Shell Graphics</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/pull-up-banner-stands.html">&nbsp;<span>6&nbsp;-</span>Banner Stands</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/branded-table-throws-2177.html">&nbsp;<span>7&nbsp;-</span>Table Throws</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/trade-show-counter-displays.html">&nbsp;<span>8&nbsp;-</span>Counter Displays</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/exhibition-trade-show-display-boards.html">&nbsp;<span>9&nbsp;-</span>Display Boards</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/outdoor-teardrop-flying-flags.html">&nbsp;<span>10&nbsp;-</span>Outdoor Flags</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/outdoor-banners.html">&nbsp;<span>11&nbsp;-</span>Outdoor Banners</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/branded-outdoor-marquees.html">&nbsp;<span>12&nbsp;-</span>Outdoor Marquees</a> </li>
                    <li><a style="padding-right:20px;" href="http://vividads.com.au/expo-accessories.html">&nbsp;<span>13&nbsp;-</span>Accessories</a> </li>
                  </ul>
   </div> 

<div style="float: left; width: 99%; margin: 10px 0px; display: block;" class="phone_left" id="phone_left">


    
    <div class="phone-order-kit-pink-right">
        <div class="po-title">
            Request Callback        </div>
        <div class="po-content">
            <div class="po-info">
                If you would like to get more information by phone, leave your number. We will contact you as soon as possible.            </div>
            <div class="po-form">
            <div class="error"></div>
                <form action="" id="addphone">
                    <input type="hidden" value="http://vividexhibits.com.au/shopping-center-displays/table-throw-tear-drop-flags-combo.html" name="url" id="url">             
                    <label>Your phone number</label>
                <input type="text" class="input-text" placeholder="e.g. 0403184210" name="phone" id="phone" kl_virtual_keyboard_secure_input="on">
                    <div class="po-btn-contener">
                        <button class="po-btn" type="submit" id="submit">
                            <span>
                                Request a call                            </span>
                        </button>      
                    </div>                                 
                </form>                
            </div>
            <div id="formsuccess"></div>
        </div>
         
    </div>
</div>

<!--<div class="clients"></div>-->
		<div class="clients-banner" id="clients_banner" style="display: block;">
        	<a href="http://vividexhibits.com.au/our-clients/"><img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/client_banner.png"></a>
        </div>
        
        <div class="testimonial" id="testimonial" style="display: block;">
<p><script type="text/javascript" src="http://vividexhibits.com.au/js/testimonial/jquery.cycle.all.js"></script>
<link href="http://vividexhibits.com.au/skin/frontend/default/default/css/testimonial/style.css" type="text/css" rel="stylesheet">
<script type="text/javascript">
   jQuery(document).ready(function() {
	   jQuery('#slideshow_advance').cycle({
	      fx: 'fadeZoom',
	     timeout:  4000		});
	});
</script>
	<script type="text/javascript">
		var tb_pathToImage = "http://vividexhibits.com.au/media/testimonial/loading.gif";
	</script>

<style type="text/css">
.block{border:none 0px;}
.mw_testimonial_content{margin:0px;}
#slideshow_advance{height:185px !important;}
.mw_testimonial_heading{  color:#fff;  margin:0px auto; font-size:14px; text-align:center;
}
</style>
</p><div class="block">
	<div style="border: none 0px; margin:70px auto 0px auto; width:185px; background:none; color:#fff; " class="block-content">
    		<div class="mw_testimonial_heading">Client Testimonials</div>
				<div style="height: 420px; position: relative;" id="slideshow_advance"> 		
					<div style="position: absolute; top: 0px; left: 0px; display: none; z-index: 5; opacity: 0; width: 185px; height: 226px;"> 
									
				<div class="mw_testimonial_media gallery">
     			     			     			</div>
     						<p style="line-height:25px;   font-size:12px; padding:5px; color:#fff !important;" class="mw_testimonial_content">
				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/top_icon.png">-->
					Thank you for the great work for Westside Toyota . We have finally installed our fence mesh banners and just wanted you to know the directors are extremely satisfied with the project . Cheers 				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/btm_icon.png">-->
				</p>
				<p style="float:right; margin-right:1.4em; color:#fff;">
					Zainab Ali 
				</p>
			</div>
					<div style="position: absolute; top: 0px; left: 0px; display: block; z-index: 6; width: 185px; height: 301px; opacity: 1;"> 
									
				<div class="mw_testimonial_media gallery">
     			     			     			</div>
     						<p style="line-height:25px;   font-size:12px; padding:5px; color:#fff !important;" class="mw_testimonial_content">
				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/top_icon.png">-->
					Thanks for the amazing exhibition design you came up with. Your work and quality of the finished product was truly amazing . We had many people asking your company detail's . A big thanks from Newtech Systems for such amazing work for a such a competitive price . 
				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/btm_icon.png">-->
				</p>
				<p style="float:right; margin-right:1.4em; color:#fff;">
					Jane Matthews  
				</p>
			</div>
					<div style="position: absolute; top: 0px; left: 0px; display: none; z-index: 5; width: 185px; height: 276px; opacity: 0;"> 
									
				<div class="mw_testimonial_media gallery">
     			     			     			</div>
     						<p style="line-height:25px;   font-size:12px; padding:5px; color:#fff !important;" class="mw_testimonial_content">
				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/top_icon.png">-->
					A very Good morning . Just wanted to let you know that our wood show in NY was a big success. 
We really admire Rachel's work for coming up with such a brilliant concept . We will be in touch soon for a bigger system for our next show .				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/btm_icon.png">-->
				</p>
				<p style="float:right; margin-right:1.4em; color:#fff;">
					Alicia 
				</p>
			</div>
					<div style="position: absolute; top: 0px; left: 0px; display: none; z-index: 5; width: 185px; height: 301px; opacity: 0;"> 
									
				<div class="mw_testimonial_media gallery">
     			     			     			</div>
     						<p style="line-height:25px;   font-size:12px; padding:5px; color:#fff !important;" class="mw_testimonial_content">
				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/top_icon.png">-->
					Hello to all at Banner Shop !I just wanted to thank you so much for the wonderful banner and display that you got to me on such short notice! The mesh banner's looked really great, and it was a very effective part of our construction site . I was asked a few times where I had the banner created, so…				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/btm_icon.png">-->
				</p>
				<p style="float:right; margin-right:1.4em; color:#fff;">
					Stockland  
				</p>
			</div>
					<div style="position: absolute; top: 0px; left: 0px; display: none; z-index: 5; width: 185px; height: 301px; opacity: 0;"> 
									
				<div class="mw_testimonial_media gallery">
     			     			     			</div>
     						<p style="line-height:25px;   font-size:12px; padding:5px; color:#fff !important;" class="mw_testimonial_content">
				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/top_icon.png">-->
					Just wanted to thank you on the amazing work you did for Porter Davis Homes last week . 
The banners are of amazingly high quality and look great . I am pleased to say that the
product is of much higher quality and you guys are amazingly price competitive . 
Regards 				<!--<img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/btm_icon.png">-->
				</p>
				<p style="float:right; margin-right:1.4em; color:#fff;">
					Mark Collins 
				</p>
			</div>
				</div>
			</div>
</div><p></p>            	</div>
        
        <div class="artwork-banner" id="artwork_banner" style="display: none;">
        	<a href="http://vividexhibits.com.au/upload/"><img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/upload_banner.png"></a>
        </div>
        
        <div class="price-guarante">
			<ul>
            <li id="save_img" style="display: none;"><img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/buy_factory.png "></li>
            <li id="tnt_img" style="display: none;"><img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/fast_delivery.png "></li>
            <li id="quality_img" style="display: none;"><img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/quality_assurance_banner.png "></li>
            <li id="customer_services_img" style="display: none;"><img src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/customer_care.png "></li>
            </ul>
        </div>
        <div class="best_offer" id="best_offer" style="display: none;">
        <img alt="Best Offer" src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/best_offer_banner.png">
        </div>
        <div class="secure" id="secure" style="display: none;">
          <img alt="Securely Accept" src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/securely_accept.png"></div>
          <div class="likeus" id="likeus" style="display: none;">
          <img alt="Like Us on Facebook" src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/likeus.png"></div>
          <div id="stand_design" class="stand_design" style="display: none;">
          <img alt="Banner Stand" src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/stand_design.png">
          
          </div>
          <div id="sale_banner" class="sale_banner" style="display: none;">
          <img alt="Sales" src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/sale_banner.png">
          </div>
          <div id="educational_banner" class="educational_banner" style="display: none;">
          <img alt="Educational Banner" src="http://vividexhibits.com.au/skin/frontend/default/vividadscomau/images/educational_banner.png">
          </div>


<script type="text/javascript">
//	var $ = jQuery.noConflict();
jQuery(function() {

    jQuery("#submit").click(function() {
      // validate and process form here
  	  var phone = jQuery("#phone").val();
	  var url = jQuery("#url").val();
  		if (phone == "") {
        jQuery("label#name_error").show();
        jQuery("input#name").focus();
        return false;
      }
		var dataString = 'phone='+ phone + '&amp;url=' + url;
		var getUrl = 'http://vividexhibits.com.au/phoneorder/index/addPhoneOrder';
		console.log(dataString+getUrl);
  //alert (dataString);return false;
  jQuery.ajax({
    type: "get",
    url: getUrl,
    data: dataString,
    success: function() {
	  jQuery('#formsuccess').html("&lt;div id='message'&gt;&lt;/div&gt;");
      jQuery('#message').html("&lt;h2 style='color:#f1396d;font-size:18px;'&gt;Phone number has Submitted!&lt;/h2&gt;")
      .append("&lt;p&gt;We will be in touch soon.&lt;/p&gt;")
    }
  });
	  
	return false;  
	  
    });
  });
		
//  return false;		
	</script>
    </div>
  	   <script>
			var $ = jQuery.noConflict();
			jQuery(document).ready(function(){
				function sdf_FTS(_number,_decimal,_separator)
				{
				var decimal=(typeof(_decimal)!='undefined')?_decimal:2;
				var separator=(typeof(_separator)!='undefined')?_separator:'';
				var r=parseFloat(_number)
				var exp10=Math.pow(10,decimal);
				r=Math.round(r*exp10)/exp10;
				rr=Number(r).toFixed(decimal).toString().split('.');
				b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
				r=(rr[1]?b+'.'+rr[1]:b);
				return r;
}
			setTimeout(function(){
					jQuery('#counter').text('0');
					jQuery('#counter1').text('0');
					jQuery('#counter2').text('0');
					jQuery('#counter3').text('0');					
					jQuery('#counter5').text('0');					
//					$('#counter4').text('0');										
					setInterval(function(){
						var curval=parseInt(jQuery('#counter').text().replace(' ',''));
						var curval1=parseInt(jQuery('#counter1').text().replace(' ',''));
						var curval2=parseInt(jQuery('#counter2').text().replace(' ',''));
						var curval3=parseInt(jQuery('#counter3').text().replace(' ',''));
						var curval5=parseInt(jQuery('#counter5').text().replace(' ',''));
					//	var curval4=parseInt($('#counter4').text().replace(' ',''));
						if(curval<=26000){
							jQuery('#counter').text(sdf_FTS((curval+500),0,' '));
						//	$('#counter4').text(Math.floor((Math.random() * (20 - 5)) + 5));							
						}
					if(curval1<=66500){
							jQuery('#counter1').text(sdf_FTS((curval1+500),0,' '));
						}

						if(curval2<=924024){

							jQuery('#counter2').text(sdf_FTS((curval2+2500),0,' '));

						}

						if(curval2>=924024){

							jQuery('#counter2').text('Over 1 Million +');

						}

						if(curval3<=10000){

							jQuery('#counter3').text(sdf_FTS((curval3+500),0,' '));

						}

						if(curval5<=50000){

							jQuery('#counter5').text(sdf_FTS((curval5+500),0,' '));

						}

						/*if(curval3>=954024){

							$('#counter3').text('Over 1 Million +');

						}*/

					}, 2);

				}, 500);

			});

	</script>
	</div>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">


        <?php if ( have_posts() ) { ?>
        <header class="archive-header">
          <h1 class="archive-title"><?php printf( __( 'Category Archives: %s', 'twentythirteen' ), single_cat_title( '', false ) ); ?></h1>
          <?php if ( category_description() ) : // Show an optional category description ?>
          <div class="archive-meta"><?php echo category_description(); ?></div>
          <?php endif; ?>
        </header>
        <!-- .archive-header -->
        <?php $i =1;?>
        <?php /* The loop */ ?>
        <?php while ( have_posts() ) : the_post(); ?>
        <?php if($i<= 8){ ?>
        <div class="mycategoryPost" style="float:left;clear:both;">
        <?php get_template_part( 'content', get_post_format() ); ?>
        </div>
		<?php } $i++;?>        
        <?php endwhile; ?>
        <?php twentythirteen_paging_nav(); ?>
        <?php }else{ ?>
        <?php get_template_part( 'content', 'none' ); ?>
        <?php } ?>

		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_sidebar(); ?>
  </div>
</div>

<?php get_footer(); ?>



