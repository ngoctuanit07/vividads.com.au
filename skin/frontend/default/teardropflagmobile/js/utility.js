jQuery(document).ready(function () {
							
	
	
	activeItem = jQuery("#accordion li:first");
	jQuery(activeItem).addClass('active');
     
	jQuery("#accordion li").click(function(){
	   //alert(jQuery(this).attr('class'));
	   
	  
		
	    if(jQuery(this).attr('class') != 'active')
	    {
		jQuery("#accordion li").removeClass("active");
		jQuery(this).addClass("active");
		
		 if(!jQuery(this).attr('title'))
		{
		    var height = jQuery(this).find('.accro_content').height();
		    jQuery(this).attr('title',60+parseInt(height));
		    height = jQuery(this).attr('title');
		}
		else
		{
		    var height = jQuery(this).attr('title')
		}
		//alert(height)
		
		jQuery(activeItem).animate({height: "38px"}, {duration:500, queue:false});
		jQuery(this).animate({height: height+"px"}, {duration:500, queue:false});
		activeItem = this;
	    }
	    else{
		
		 if(!jQuery(this).attr('title'))
		{
		    var height = jQuery(this).find('.accro_content').height();
		    jQuery(this).attr('title',60+parseInt(height));
		}
		
		jQuery(this).animate({height: "38px"}, {duration:500, queue:false});
		jQuery("#accordion li").removeClass("active");
	    }
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