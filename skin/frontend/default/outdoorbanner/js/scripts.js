jQuery(document).ready(function(){
	
       jQuery("a[data-gal^='prettyPhoto']").prettyPhoto({
            animationSpeed: 'normal',
            padding: 40,
            opacity: 0.35,
            showTitle: true,
            allowresize: true,
            counter_separator_label: '/',          
            theme: 'facebook' 
        });
        jQuery('.sidebar .block:last').addClass ('last_block'); 
        jQuery('.sidebar .block:first').addClass ('item_block'); 
        jQuery('.sidebar .block:first-child').addClass ('block_item');
		jQuery('#nav > li > a').append("<em></em>"); 
		 // hide #back-top first
		 jQuery("#back-top").hide();
		 // fade in #back-top
		 jQuery(function () {
		  jQuery(window).scroll(function () {
		   if (jQuery(this).scrollTop() > 100) {
		    jQuery('#back-top').fadeIn();
		   } else {
		    jQuery('#back-top').fadeOut();
		   }
		  });
		
		  // scroll body to 0px on click
		  jQuery('#back-top a').click(function () {
		   jQuery('body,html').animate({
		    scrollTop: 0
		   }, 800);
		   return false;
		  });
		 });

		 
		 jQuery(function () {				
		jQuery('.products-grid li')
		.hover(function(){
			jQuery(this).find('.hover-home-product')
			.stop().animate({top:0}, {duration:400, easing:'easeOutQuart'})
		}, function(){
			jQuery(this).find('.hover-home-product')
			.stop().animate({top:-420}, {duration:400, easing:'easeOutQuart'})
		})
		});

		
		
		  if(navigator.userAgent.indexOf('IE 8')==-1){
			    jQuery('#carousel li')
					.live('mouseenter',function(){
						jQuery(this).find("img").stop()
						.animate({opacity:0},{duration:400});
					})
					.live('mouseleave',function(){
						jQuery(this).find("img").stop()
						.animate({opacity:1},{duration:400});
				});
				 jQuery('#carousel li')
					.live('mouseenter',function(){
						jQuery(this).find("span").stop()
						.animate({opacity:1},{duration:400});
					})
					.live('mouseleave',function(){
						jQuery(this).find("span").stop()
						.animate({opacity:0},{duration:400});
				});
				
		jQuery('#carousel li')
		.live('mouseenter',function(){
			jQuery(this).find(".prod-1, .prod-2, .prod-3, .prod-4, .prod-5, .prod-6").stop()
			.animate({opacity:1},{duration:400});
		})
		.live('mouseleave',function(){
			jQuery(this).find(".prod-1, .prod-2, .prod-3, .prod-4, .prod-5, .prod-6").stop()
			.animate({opacity:0},{duration:400});
	});
		  };
		  
		   if(navigator.userAgent.indexOf('IE 8')!=-1){
			  jQuery('body').addClass('ie-8-fix');
		  };
		   if(navigator.userAgent.indexOf('IE 9')!=-1){
			  jQuery('body').addClass('ie-9-fix');
		  };
        
		jQuery('.social_icons li a').hover(function(){
		   jQuery(this).stop(true,false).animate({marginTop:"5px"}, {duration: 300});
		  },function(){
		   jQuery(this).stop(true,false).animate({marginTop:"0px"}, {duration: 300});
		 });
        
		
});
