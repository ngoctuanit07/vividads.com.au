jQuery(document).ready(function () {
							
	
	
	activeItem = jQuery("#accordion li:first");
	jQuery(activeItem).addClass('active');
     
	jQuery("#accordion li").click(function(){
	    
	    jQuery("#accordion li").removeClass("active");
	    jQuery(this).addClass("active");
	    
	    jQuery(activeItem).animate({width: "35px"}, {duration:500, queue:false});
	    jQuery(this).animate({width: "450px"}, {duration:500, queue:false});
	    activeItem = this;
	});
	
	//jQuery(".go_to_nxt_tab").click(function(){
	//	
	//	arr=Array();
	//	
	//	arr=jQuery(this).attr("id").split("__");
	//	
	//	var make_id="#acc__"+arr[1];
	//	jQuery(activeItem).animate({width: "50px"}, {duration:300, queue:false});
	//	jQuery(make_id).animate({width: "450px"}, {duration:300, queue:false});
	//	activeItem = jQuery(make_id);
	//	
	//});
	
		

});