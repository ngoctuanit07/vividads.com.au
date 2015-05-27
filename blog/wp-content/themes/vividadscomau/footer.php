<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>
		</div><!-- #main -->

<style type="text/css">
.footer .links {
    float: left;
    margin-bottom: 8px;
    padding: 10px 25px;
    width: 24.05%;
}

.contact-container {
    border-bottom: 1px solid #687a85;
    border-top: 1px solid #687a85;
    float: left;
    margin: 15px 0 25px;
    padding: 30px 7%;
    text-align: center;
    width: 100%;
}
</style>
		<div class="footer-container">
<div class="footer clearfix">
   	        <div class="links">
<div class="block-title"><strong><span>Company</span></strong></div>
<ul>
<li><a href="http://vividads.com.au/about_us/">About Us</a></li>
<li><a href="http://vividads.com.au/catalog/seo_sitemap/category">Sitemap</a></li>
<li><a href="http://vividads.com.au/terms/">Terms &amp; Conditions</a></li>
<li><a href="http://vividads.com.au/contact-us/">Contact Us</a></li>
</ul>
</div><div class="links">
<div class="block-title"><strong><span>Customer Service</span></strong></div>
<ul>
<li><a href="http://vividads.com.au/customer/account/">My Account</a></li>
<li><a href="http://vividads.com.au/sales/order/history/">Ordering</a></li>
<li><a href="http://vividads.com.au/ProductReturn/Front/List/">Shipping &amp; Returns</a></li>
<li><a href="http://vividads.com.au/sales/order/history/">Order Status</a></li>
<li><a href="http://vividads.com.au/faqs/">FAQs</a></li>
</ul>
</div><div class="links">
<div class="block-title"><strong><span>Services</span></strong></div>
<ul>
<li><a href="http://vividads.com.au/artwork/">Artwork Guidelines</a></li>
<li><a href="http://vividads.com.au/artwork/">Graphic Templates</a></li>
<li><a href="http://vividads.com.au/upload/">Upload Artwork</a></li>
<li><a href="#">Compress Artwork</a></li>
</ul>
</div>
<div class="block block-subscribe">
    <div class="block-title">
        <strong><span>Newsletter</span></strong>
    </div>

<form id="newsletter-validate-detail" style="background:none !important;" onSubmit="return validateIt()" method="post" action="http://vividads.com.au/newsletter/subscriber/new/">

    <div class="block-content" style="padding:0; width:290px !important; height:38px; ">

    <div class="form-subscribe-header">Join our newsletter for lots of great offers and information about our products and services</div>

        <div class="input-box">

           <input  type="text" placeholder="Email Address" name="email" id="newsletter" title="Sign up for our newsletter" style="width:180px;line-height:25px; margin-bottom:0" class="input-text required-entry validate-email">

        </div>

        <button  type="submit" title="Submit" class="button" style="margin-top:6px;"><span><span>Submit</span></span></button>

    </div>

</form>
 
<script type="text/javascript">

function validateIt(){
	
	email = document.getElementById('newsletter');
	
	if(email.value == ''){
		alert('Oops! Error, please enter proper email address... ');
		email.select();
		email.focus();
		
		return false;
		}
		return true;
	}


//&lt;![CDATA[

 //   var newsletterSubscriberFormDetail = new VarienForm('newsletter-validate-detail');

//    new Varien.searchForm('newsletter-validate-detail', 'newsletter', 'Enter your email address');

//]]&gt;

</script>

</div>

<div class="footer_social">
<div class="block-title"><strong><span>Connect</span></strong></div>
<!--<a href="#" class="facebook">&nbsp;</a>
<a href="#" class="twitter">&nbsp;</a>
<a href="#" class="youtube">&nbsp;</a>
<a href="#" class="vimeo">&nbsp;</a>
<a href="#" class="google">&nbsp;</a>-->
</div>	    
<div class="socialaddthis" style="float: left; margin-right: 70px;">
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
<a class="addthis_button_print addthis_button_preferred_1 at300b" title="Print" href="#"><span class="at4-icon aticon-print" style="background-color: rgb(115, 138, 141);"><span class="at_a11y">Share on print</span></span></a>
<a class="addthis_button_facebook addthis_button_preferred_2 at300b" title="Facebook" href="#"><span class="at4-icon aticon-facebook" style="background-color: rgb(48, 88, 145);"><span class="at_a11y">Share on facebook</span></span></a>
<a class="addthis_button_twitter addthis_button_preferred_3 at300b" title="Tweet" href="#"><span class="at4-icon aticon-twitter" style="background-color: rgb(44, 168, 210);"><span class="at_a11y">Share on twitter</span></span></a>
<a class="addthis_button_email addthis_button_preferred_4 at300b" target="_blank" title="Email" href="#"><span class="at4-icon aticon-email" style="background-color: rgb(115, 138, 141);"><span class="at_a11y">Share on email</span></span></a>
<a class="addthis_button_compact at300m" href="#"><span class="at4-icon aticon-compact" style="background-color: rgb(252, 109, 76);"><span class="at_a11y">More Sharing Services</span></span></a>
<!--<a class="addthis_counter addthis_bubble_style"></a>-->
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
 </div>
<div class="contact-container">
                
<div class="textwidget"><h3>Free Call (Australia Only): <strong style="color:#000;">1300 721 614</strong></h3>
<div class="footer_item"><strong style="color: rgb(0, 0, 0); float: left; font-size: 12px; letter-spacing: normal; width: 100%;">Head Office</strong>
<p style="font-size: 12px; float: left; margin: 0px; line-height: normal;">1/2 Phillip Court, Port Melbourne<br>
Victoria 3207 Australia </p>
</div>
<div style="font-size: 12px; float: left; margin: 0px;" class="footer_item"> Ph: 1300 72 16 14<br>
  Fax: +613 8456 6234 </div>
</div>
</div>
<div class="item" style="float: left; width: 100%; text-align: center; padding: 0px 0px 20px; font-size:12px;">
&copy; VividAds Pty Limited  Australia. All rights reserved.</div>


</div>
</div>
</div><!-- #colophon -->
	</div><!-- #page -->
	<?php wp_footer(); ?>
</body>
</html>
<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Raleway:300,400,500,700,600" />
<script type="text/javascript">
var __lc = {};
__lc.license = 3920361;

(function() {
	var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
	lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
})();
</script>