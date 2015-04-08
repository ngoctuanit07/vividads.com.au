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
function getQutefunc()
{

                var id = '#dialog';
	
		//Get the screen height and width
		var maskHeight = jQuery(document).height();
		var maskWidth = jQuery(window).width();
	
		//Set heigth and width to mask to fill up the whole screen
		jQuery('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		jQuery('#mask').fadeIn(1000);	
		jQuery('#mask').fadeTo("slow",0.8);	
	
		//Get the window height and width
		var winH = jQuery(window).height();
		var winW = jQuery(window).width();
              
		//Set the popup window to center
		jQuery(id).css('top',  winH/2-jQuery(id).height()/2);
		jQuery(id).css('left', winW/2-jQuery(id).width()/2);
	
		//transition effect
		jQuery(id).fadeIn(2000);
        
	
			
	
	//if mask is clicked
	jQuery('#mask').click(function () {
		//$(this).hide();
		//$('.window').hide();
	});		

}
jQuery(document).ready(function() {
//if close button is clicked
	jQuery('.window .magento_close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		//jQuery('#mask').hide();
		//jQuery('.window').hide(); 
                jQuery('#mask').fadeOut(1000);
		jQuery('.window').fadeOut(1000);
                
	});
});

////Top Menu Code/////



//jQuery("#topBlockCart").hover(function () {
//    //alert("ok");
//    jQuery("#topCartContent").slideDown(1000);
//}, function() {
//    jQuery("#topCartContent").slideUp(1000);
//});

//jQuery('#topBlockCart').click(function() {
//  jQuery('#topCartContent').slideDown('slow', function() {
//    // Animation complete.
//  });
//});
//
//jQuery('#closeCart').click(function() {
//  jQuery('#topCartContent').slideUp('slow', function() {
//    // Animation complete.
//  });
//});


function cartHide()
{
    //alert("ok");
    //alert(jQuery('#topCartContent').css("top"));
    if(document.getElementById('topCartContent').style.display=="none")
    {
        jQuery('#topCartContent').slideDown('200', function() {
        // Animation complete.
        });
    }else{
        jQuery('#topCartContent').slideUp('200', function() {
        // Animation complete.
        });
    }
}

function cartClose()
{
    jQuery('#topCartContent').slideUp('slow', function() {
    // Animation complete.
    });
}


/****************************/
jQuery(document).ready(function(){
    jQuery(".sign-min").click(function () {
      //jQuery("#mini-login-top").show("slow").siblings().hide("slow");
      jQuery("#mini-login-top").slideToggle("slow");
    });
});
/***************************/

