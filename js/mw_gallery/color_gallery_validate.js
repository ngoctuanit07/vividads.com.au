var $j_mw_gallery = jQuery.noConflict();
var tmp = 0;



$j_mw_gallery(function(){	
	
	function rgb2hex(r, g, b) {
	    var a = new Array();
	    a[0] = r;
	    a[1] = g;
	    a[2] = b;
	    var c = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
	    var d = '';
	    for (var i = 0; i < a.length; i++) {
	        dec = parseInt(a[i]);
	        d += c[parseInt(dec / 16)] + c[dec % 16];
	    }
	    return d;
	};
	
	//color picker
	$j_mw_gallery('#gallery_info_photo_background_color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
		$j_mw_gallery(el).val(rgb['r'] + ',' + rgb['g'] + ',' + rgb['b']);
		$j_mw_gallery(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			var rgb = this.value.split(','); 
			$j_mw_gallery(this).ColorPickerSetColor(rgb2hex(rgb[0],rgb[1],rgb[2]));
		}
	})
	
	$j_mw_gallery('#gallery_info_simple_photo_background_color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
		$j_mw_gallery(el).val(rgb['r'] + ',' + rgb['g'] + ',' + rgb['b']);
		$j_mw_gallery(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			var rgb = this.value.split(','); 
			$j_mw_gallery(this).ColorPickerSetColor(rgb2hex(rgb[0],rgb[1],rgb[2]));
		}
	})
	
	.bind('keyup', function(){
		var rgb = this.value.split(','); 
		$j_mw_gallery(this).ColorPickerSetColor(rgb2hex(rgb[0],rgb[1],rgb[2]));
	});
	

});
