Validation.add('MW_validate_media', 'Disallowed url type !', function(v) {
	
	if (v!=''){
		var fileName = v;
		var ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
		if(ext == "gif" || ext == "jpeg" || ext == "jpg" ||	ext == 'png' || 
				ext == 'avi' || ext == 'flv' || ext == 'swf' || ext == 'mp3' || ext == 'mp4' ||
				strpos(fileName, 'www.youtube.com') )
		{
			return true;
		} 
		else
		{
			return false;
		}
	}else 
		return true;
});

function strpos (haystack, needle, offset) {
    // Finds position of first occurrence of a string within another  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/strpos
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Onno Marsman    
    // +   bugfixed by: Daniel Esteban
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: strpos('Kevin van Zonneveld', 'e', 5);
    // *     returns 1: 14
    var i = (haystack + '').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}