(function($){
	
	$(document).ready(function() {
		var currentUrl= $(location).attr('href');
		$('.nm_menu_top_item').each(function() {
			var url= $(this).children('a').attr('href');
		    if ((url==currentUrl)||(url+'index.php'==currentUrl)){
		    	$('.nm_menu_top_item').each(function() {
		    		$(this).removeClass('active');
		    	});
		    	$(this).addClass('active');
		    }
		});
	});
	
	function getCookie(c_name)
	{
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++)
	{
	  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
	  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
	  x=x.replace(/^\s+|\s+$/g,"");
	  if (x==c_name)
	    {
	    return unescape(y);
	    }
	  }
	}
	
	function setCookie(c_name,value,exdays)
	{
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
		document.cookie=c_name + "=" + c_value;
	}
})(jQuery);