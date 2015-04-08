<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
 
 
 //var_dump($app);
 
?>
<style>
.uploader_artwork {
    background-repeat: no-repeat;
    left: 0;
    position: fixed;
    top: 326px;
    width: 42px;
    z-index: 10;
}
div.contacts_button {
    background: url("../images/contact_us.png") no-repeat scroll 0 0 transparent;
    height: 135px;
    left: 0;
    position: fixed;
    width: 42px;
}
</style>

<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
    <link rel="stylesheet" type="text/css" href="http://vividads.com.au/skin/frontend/default/vividadscomau/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="http://vividads.com.au/skin/frontend/default/vividadscomau/css/nav.css" />
    
</head>



<div id="contacts_button" class="contacts_button" onclick="document.location.href='http://vividads.com.au/get_a_quote/';" style="top: 180px;display:block; z-index:1000;"></div>

<div style="display:block;" class="uploader_artwork">
    <a class="upload_icon artwork_upload" href="http://vividads.com.au/upload/" title="Upload Artwork"><span></span></a> <a class="upload_icon get_quote" href="javascript:void(0)" title="Quick Quote" id="getqute"><span></span></a> <a class="upload_icon design-templates" href="http://vividads.com.au/artwork/" title="Design Templates"><span></span></a> </div>

<body <?php body_class(); ?>>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-54d04a886a6b8c0c" async></script>
	<div id="page" class="hfeed site">
		<header id="masthead" class="site-header" role="banner">
			<div id="navbar" class="navbar">
				<nav id="site-navigation" class="navigation main-navigation" role="navigation">
<div style="float: left; width: 100%; background: none repeat scroll 0% 0% rgb(229, 229, 229); height: 45px; position: relative;  " class="header_top">
  <div style="width:33%; float:left;padding-top: 5px; text-align:left;" class="top_left">
   <h1 style="margin-top: 0px ! important; margin-right: 0px ! important; margin-bottom: 0px ! important; padding: 0px ! important; color:#565656; float: left; margin-left: 130px;">
Vivid Exhibition Displays
</h1>
   </div>
  <div style="width:35%; float:left;padding-top: 5px;" class="top_center">
  		<img style="float:left;" alt="Mon To Fri (9:30-4:30) Our Working Hours" src="http://vividads.com.au/skin/frontend/default/vividadscomau/images/phoneInfo.png">
<div class="socialaddthis" style="float: left; width:58%; ">
 <!-- Check whether the plugin is enabled -->

<!-- AddThis Button BEGIN -->

<!-- AddThis API Config -->
<script type="text/javascript">
var addthis_product = 'mag-sp-2.0.0';
var addthis_config 	= {
pubid : 'xa-525fbbd6215b4f1a'
}
</script>
<!-- AddThis API Config END -->
<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
<a class="addthis_button_twitter addthis_button_preferred_1 at300b" title="Tweet" href="#"><span class="at4-icon aticon-twitter" style="background-color: rgb(44, 168, 210);"><span class="at_a11y">Share on twitter</span></span></a>
<a class="addthis_button_facebook addthis_button_preferred_2 at300b" title="Facebook" href="#"><span class="at4-icon aticon-facebook" style="background-color: rgb(48, 88, 145);"><span class="at_a11y">Share on facebook</span></span></a>
<a class="addthis_button_email addthis_button_preferred_3 at300b" target="_blank" title="Email" href="#"><span class="at4-icon aticon-email" style="background-color: rgb(115, 138, 141);"><span class="at_a11y">Share on email</span></span></a>
<a class="addthis_button_print addthis_button_preferred_4 at300b" title="Print" href="#"><span class="at4-icon aticon-print" style="background-color: rgb(115, 138, 141);"><span class="at_a11y">Share on print</span></span></a>
<a class="addthis_button_compact at300m" href="#"><span class="at4-icon aticon-compact" style="background-color: rgb(252, 109, 76);"><span class="at_a11y">More Sharing Services</span></span></a>
<!--<a style="margin-left:-1px;" class="addthis_counter addthis_bubble_style"></a>-->
<div class="atclear"></div></div>

<script>
	var ats_widget =  function(){
	    if(typeof addthis_conf == 'undefined'){
	        var at_script = document.createElement('script');
	        at_script.src = '//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-525fbbd6215b4f1a';
	        document.getElementsByTagName('head')[0].appendChild(at_script);
	        var addthis_product = 'mag-sp-2.1.0';
	    }
	};
	if(window.addEventListener)
		window.addEventListener('load',ats_widget);        
	else                
		window.attachEvent('onload',ats_widget);           	    
</script>	    
<!-- AddThis Button END -->
<style>
#at3win #at3winheader h3 {
	text-align:left !important;
}
</style>
 </div>   </div>
  <div style="width:32%; float:left; text-align:right;" class="top_right">
	         <div style="margin:0px;" class="account_links">

        <ul>

          <li><a href="http://vividads.com.au/customer/account/"> <img alt="My Account" title="My Account" src="http://vividads.com.au/skin/frontend/default/vividadscomau/images/my_accont.jpg"> </a> </li>

          
          <li> <a href="http://vividads.com.au/customer/account/login/"> <img alt="Login" title="Login" src="http://vividads.com.au/skin/frontend/default/vividadscomau/images/login.jpg"> </a> </li>

          
        </ul>

      </div>
  </div>
  </div>
                  <div class="top_menu" style="margin-top: -30px;">
  <div class="menuBall" id="menuBall1" style="height:121px;"> <a class="nav home_btn" href="http://vividads.com.au/" style="background:none;"> <img alt="" src="http://vividads.com.au/skin/frontend/default/vividadscomau/images/logo.png"> </a> </div>
  <div class="menuBall" id="menuBall2"> <a class="nav contact_btn" href="http://vividads.com.au/about_us/">
    <div class="menuText"> About Us </div>
    <br>
    <span class="navi-description"> Who we are </span> </a> </div>
  <div class="menuBall" id="menuBall3"> 
      <a class="nav artwork_btn" target="_blank" href="https://plus.google.com/photos/+VividAdsAutradeshowsaustralia/albums/6124406101444221089?banner=pwa">
   <!-- <a href="http://vividads.com.au/gallery/" class="nav artwork_btn" >-->
    <div class="menuText"> Gallery </div>
    <br>
    <span class="navi-description"> Work we pride </span> </a> </div>
  <div class="menuBall" id="menuBall4"> <a class="nav gallery_btn" href="http://vividads.com.au/our-clients/">
    <div class="menuText"> Clients </div>
    <br>
    <span class="navi-description"> Who we pride </span> </a> </div>
    <div class="menuBall" id="menuBall5"> <a class="nav clients_btn" href="http://vividads.com.au/artwork-design/">
    <div class="menuText"> Artwork Design </div>
    <br>
    <span class="navi-description"> The Creative Guys </span> </a> </div>
  <div class="menuBall" id="menuBall6"> <a class="nav testimonial_btn" href="http://vividads.com.au/blog/?cat=3">
    <div class="menuText"> Video </div>
    <br>
    <span class="navi-description"> How it Works </span> </a> </div>
  <div class="menuBall" id="menuBall7"> <a class="nav portable_btn" href="http://vividads.com.au/warranties/">
    <div class="menuText"> Warranties </div>
    <br>
    <span class="navi-description"> Our Confidence </span> </a> </div>
  <div class="menuBall" id="menuBall8"> <a class="nav who_we_are_btn" href="http://vividads.com.au/blog/?cat=1">
    <div class="menuText"> Education </div>
    <br>
    <span class="navi-description"> We Guide </span> </a> </div>
  <div class="menuBall" id="menuBall9"> <a class="nav customer_btn" href="http://vividads.com.au/get_a_quote/">
    <div class="menuText"> Free Quote </div>
    <br>
    <span class="navi-description"> Its Free </span> </a> </div>
  <div class="menuBall" id="menuBall10"> <a class="nav upload_btn" href="http://vividads.com.au/contact-us/">
    <div class="menuText"> Contact Us </div>
    <br>
    <span class="navi-description"> Get In Touch </span> </a> </div>
</div>

				</nav><!-- #site-navigation -->
			</div><!-- #navbar -->
<?php        echo do_shortcode( '[ngg-nivoslider html_id="about-slider"]' ); ?>
            
            
		</header><!-- #masthead -->
		<div id="main" class="site-main">
