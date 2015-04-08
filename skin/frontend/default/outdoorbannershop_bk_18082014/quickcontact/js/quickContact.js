jQuery(document).ready(function(){

jQuery('#contactform').submit(function(){

	var action = jQuery(this).attr('action');
	var $_SKIN_URL = "<?php echo $this->getSkinUrl();?>";
	jQuery('#submit')
		.before('<img src="'+$_SKIN_URL+'quickcontact/images/opc-ajax-loader.gif" class="loader" style="padding-right:10px;"/>')
        
		.attr('disabled','disabled');

    
	jQuery.post(action, jQuery('#contactform').serialize(true),
		function(data){
			jQuery('#contactform #submit').attr('disabled','');
			jQuery('.response').remove();
			jQuery('#contactform').before('<span class="response">'+data+'</span>');
			jQuery('.response').slideDown();
			
			if ( jQuery('.response').text().indexOf('Thank you') != -1 ) { 
				jQuery('#subject').val('');
				jQuery('#content').val('');
			}
			jQuery('#contactform img.loader').fadeOut(500,function(){jQuery(this).remove()});
		}
	);

	return false;

});
});