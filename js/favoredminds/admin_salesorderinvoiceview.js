function round (val, precision, mode)
{
    var retVal=0,v='',integer='',decimal='',decp=0,negative=false;
    var _round_half_oe = function (dtR,dtLa,even){
        if (even === true) {
            if (dtLa == 50) {
                if ((dtR % 2) === 1) {
                    if (dtLa >= 5) {
                        dtR+=1;
                    } else {
                        dtR-=1;
                    }
                }
            }else if (dtLa >= 5) {
                dtR+=1;
            }
        }else{
            if (dtLa == 5) {
                if ((dtR % 2) === 0) {
                    if (dtLa >= 5) {
                        dtR+=1;
                    }else{
                        dtR-=1;
                    }
                }
            }else if (dtLa >= 5) {
                dtR+=1;
            }
        }
 
        return dtR;
    };
    var _round_half_ud = function (dtR,dtLa,up) {
        if (up === true) {
            if (dtLa>=5) {
                dtR+=1;
            }
        }else{
            if (dtLa>5) {
                dtR+=1;
            }
        }
        return dtR;
    };
    var _round_half = function (val,decplaces,mode){
    /*Declare variables
         *V       - string representation of Val
         *Vlen    - The length of V - used only when rounding intgerers
 
         *VlenDif - The difference between the lengths of the original V
         *          and the V after being truncated
         *decp    - character in index of . [decimal place] in V
         *integer - Integr protion of Val
         *decimal - Decimal portion of Val
         *DigitToRound - The digit to round
 
         *DigitToLookAt- The digit to comapre when rounding
         *
         *round - A function to do the rounding
         */
        var v = val.toString(),vlen=0,vlenDif=0;
        var decp = v.indexOf('.');
 
        var digitToRound = 0,digitToLookAt = 0;
        var integer='',decimal='';
        var round = null,bool=false;
        switch (mode) {
            case 'up':
                bool = true;
                // Fall-through
            case 'down':
                round = _round_half_ud;
                break;
            case 'even':
                bool = true;
            case 'odd':
                round = _round_half_oe;
                break;
        }
        if (decplaces < 0){ //Int round
            vlen=v.length;
 
            decplaces = vlen + decplaces;
            digitToLookAt = Number(v.charAt(decplaces));
            digitToRound  = Number(v.charAt(decplaces-1));
            digitToRound  = round(digitToRound,digitToLookAt,bool);
            v = v.slice(0,decplaces-1);
            vlenDif = vlen-v.length-1;
 
            if (digitToRound == 10){
                v = String(Number(v)+1)+"0";
            }else{
                v+=digitToRound;
            }
 
            v = Number(v)*(Math.pow(10,vlenDif));
        }else if (decplaces > 0){
            integer=v.slice(0,decp);
            decimal=v.slice(decp+1);
            digitToLookAt = Number(decimal.charAt(decplaces));
 
            digitToRound  = Number(decimal.charAt(decplaces-1));
            digitToRound  = round(digitToRound,digitToLookAt,bool);
            decimal=decimal.slice(0,decplaces-1);
            if (digitToRound==10){
                v=Number(integer+'.'+decimal)+(1*(Math.pow(10,(0-decimal.length))));
            }else{
                v=Number(integer+'.'+decimal+digitToRound);
            }
        }else{
            integer=v.slice(0,decp);
            decimal=v.slice(decp+1);
            digitToLookAt = Number(decimal.charAt(decplaces));
 
            digitToRound  = Number(integer.charAt(integer.length-1));
            digitToRound  = round(digitToRound,digitToLookAt,bool);
            decimal='0';
            integer = integer.slice(0,integer.length-1);
            if (digitToRound==10){
                v=Number(integer)+1;
            }else{
                v=Number(integer+digitToRound);
            }
        }
        return v;
    };
 
 
    //precision optional - defaults 0
    if (typeof precision == 'undefined') {
        precision = 0;
    }
    //mode optional - defaults round half up
    if (typeof mode == 'undefined') {
        mode = 'PHP_ROUND_HALF_UP';
    }
 
    if (val < 0){ //Remember if val is negative
        negative = true;
    }else{
        negative = false;
    }
 
    v = Math.abs(val).toString(); //Take a string representation of val
    decp = v.indexOf('.');        //And locate the decimal point
    if ((decp == -1) && (precision >=0)){
   /* If there is no deciaml point and the precision is greater than 0
         * there is no need to round, return val
         */
        return val;
    }else{
        if (decp == -1){
            //There are no decimals so intger=V and decimal=0
            integer = v;
            decimal = '0';
        }else{
            //Otherwise we have to split the decimals from the integer
            integer = v.slice(0,decp);
            if (precision >= 0){
                //If the precision is greater than 0 then split the decimals from the integer
                //We truncate the decimals to a number of places equal to the precision requested+1
                decimal = v.substr(decp+1,precision+1);
            }else{
                //If the precision is less than 0 ignore the decimals - set to 0
                decimal = '0';
            }
        }
        if ((precision > 0) && (precision >= decimal.length)){
            /*If the precision requested is more decimal places than already exist
            * there is no need to round - return val
            */
            return val;
        }else if ((precision < 0) && (Math.abs(precision) >= integer.length)){
           /*If the precison is less than 0, and is greater than than the
             *number of digits in integer, return 0 - mimics PHP
             */
            return 0;
        }
        val = Number(integer+'.'+decimal); // After sanitizing recreate val
    }
 
    //Call approriate function based on passed mode, fall through for integer constants
    switch (mode){
        case 0:
        case 'PHP_ROUND_HALF_UP':
            retVal = _round_half(val,precision,'up');
            break;
        case 1:
        case 'PHP_ROUND_HALF_DOWN':
            retVal = _round_half(val, precision,'down');
            break;
        case 2:
        case 'PHP_ROUND_HALF_EVEN':
            retVal = _round_half(val,precision,'even');
            break;
        case 3:
        case 'PHP_ROUND_HALF_ODD':
            retVal = _round_half(val,precision,'odd');
            break;
    }
    if (negative){
        return 0-retVal;
    }else{
        return retVal;
    }
}
	
function sprintf ( ) {
    // *     example 1: sprintf("%01.2f", 123.1);
    // *     returns 1: 123.10

 
    var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
    var a = arguments, i = 0, format = a[i++];
 
    // pad()
    var pad = function (str, len, chr, leftJustify) {
        if (!chr) {chr = ' ';}
        var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
        return leftJustify ? str + padding : padding + str;
    };
 
    // justify()
    var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
        var diff = minWidth - value.length;
        if (diff > 0) {
            if (leftJustify || !zeroPad) {
                value = pad(value, minWidth, customPadChar, leftJustify);
            } else {
                value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
            }
        }
        return value;
    };
 
    // formatBaseX()
    var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        prefix = prefix && number && {'2': '0b', '8': '0', '16': '0x'}[base] || '';
        value = prefix + pad(number.toString(base), precision || 0, '0', false);
        return justify(value, prefix, leftJustify, minWidth, zeroPad);
    };
 
    // formatString()
    var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
        if (precision != null) {
            value = value.slice(0, precision);
        }
        return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
    };
 
    // doFormat()
    var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
        var number;
        var prefix;
        var method;
        var textTransform;
        var value;
 
        if (substring == '%%') {return '%';}
 
        // parse flags
        var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false, customPadChar = ' ';
        var flagsl = flags.length;
        for (var j = 0; flags && j < flagsl; j++) {
            switch (flags.charAt(j)) {
                case ' ': positivePrefix = ' '; break;
                case '+': positivePrefix = '+'; break;
                case '-': leftJustify = true; break;
                case "'": customPadChar = flags.charAt(j+1); break;
                case '0': zeroPad = true; break;
                case '#': prefixBaseX = true; break;
            }
        }
 
        // parameters may be null, undefined, empty-string or real valued
        // we want to ignore null, undefined and empty-string values
        if (!minWidth) {
            minWidth = 0;
        } else if (minWidth == '*') {
            minWidth = +a[i++];
        } else if (minWidth.charAt(0) == '*') {
            minWidth = +a[minWidth.slice(1, -1)];
        } else {
            minWidth = +minWidth;
        }
 
        // Note: undocumented perl feature:
        if (minWidth < 0) {
            minWidth = -minWidth;
            leftJustify = true;
        }
 
        if (!isFinite(minWidth)) {
            throw new Error('sprintf: (minimum-)width must be finite');
        }
 
        if (!precision) {
            precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
        } else if (precision == '*') {
            precision = +a[i++];
        } else if (precision.charAt(0) == '*') {
            precision = +a[precision.slice(1, -1)];
        } else {
            precision = +precision;
        }
 
        // grab value using valueIndex if required?
        value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];
 
        switch (type) {
            case 's': return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
            case 'c': return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
            case 'b': return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'o': return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'x': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'X': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
            case 'u': return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'i':
            case 'd':
                number = parseInt(+value, 10);
                prefix = number < 0 ? '-' : positivePrefix;
                value = prefix + pad(String(Math.abs(number)), precision, '0', false);
                return justify(value, prefix, leftJustify, minWidth, zeroPad);
            case 'e':
            case 'E':
            case 'f':
            case 'F':
            case 'g':
            case 'G':
                number = +value;
                prefix = number < 0 ? '-' : positivePrefix;
                method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
                textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
                value = prefix + Math.abs(number)[method](precision);
                return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
            default: return substring;
        }
    };
 
    return format.replace(regex, doFormat);
}
	
	
	window.onload	= function()
	{
		//alert(customerOrderComment);
		
		var elem		= document.getElementById("page:main-container");
		
		var parentdiv	= false;
		var count		= 0;
		var countlimit	= 3;
		
		
		
		var classname	= "";
		
		var grandTotal		= 0;
		var grandCommission	= 0;
		var grandShipping	= 0;
		
		var content		= "";
		content		 	+= "<div class=\"entry-edit\">";
		content			+= "            <div class=\"entry-edit-head\">";
		content			+= "                <h4 class=\"icon-head head-shipping-method\">Vendor Statistics</h4>";
		content			+= "            </div>";
		content			+= "</div>";
		
		
		
		
		content			+= "<div class=\"grid np\">";
		content			+= "	<div class=\"hor-scroll\">";
		content			+= "		<table cellspacing=\"0\" class=\"data order-tables\">";
		content			+= "			<col/>";
		content			+= "			<col width=\"150\"/>";
		content			+= "			<col/>";
		content			+= "			<col/>";
		content			+= "			<col/>";
		content			+= "			<col/>";
		content			+= "			<col/>";
		content			+= "			<thead>";
		content			+= "				<tr class=\"headings\">";
		content			+= "					<th>Vendor</th>";
		content			+= "					<th><span class=\"nobr\">Products</span></th>";
		content			+= "					<th><span class=\"nobr\">Qty</span></th>";
		content			+= "					<th><span class=\"nobr\">Item Price</span></th>";
		content			+= "					<th><span class=\"nobr\">Commission</span></th>";
		content			+= "					<th><span class=\"nobr\">Item Shipping Cost</span></th>";
		content			+= "					<th class=\"last\"><span class=\"nobr\">Total</span></th>";
		content			+= "				</tr>";
		content			+= "			</thead>";
		
		
		for(var i in orderData)
		{
			if(i in orderData && !isNaN(i))
			{
				classname		= classname=="" || classname=="odd" ? "even" : "odd";
				
				content			+= "			<tbody class=\""+classname+"\">";
				content			+= "				<tr class=\"border\">";
				content			+= "					<td class=\"a-center giftmessage-single-item\" valign=\"middle\" rowspan=\""+(orderData[i]["items"].length+1)+"\" style=\"vertical-align:middle !important;\">";
				content			+= "						<b>"+orderData[i]["vendor_data"]["company_name"]+"</b><br>(commission "+(100-orderData[i]["vendor_data"]["commission"])+"%)";
				content			+= "					</td>";
				content			+= "				</tr>";
				
				
				for(var j in orderData[i]["items"])
				{
					if(j in orderData[i]["items"] && !isNaN(j))
					{
						var commission		= round((orderData[i]["items"][j]["price"]/100)*(100-orderData[i]["vendor_data"]["commission"]));
						var price			= round(orderData[i]["items"][j]["price"],2);
						var qty_ordered		= round(orderData[i]["items"][j]["qty_ordered"])*1;
						var shipping_cost	= round(orderData[i]["items"][j]["shipping_cost"],2);
						var totalcommission	= round(commission*qty_ordered,2);
						var vendor_total	= round(totalcommission + (shipping_cost*qty_ordered),2);
						
						grandTotal			+= vendor_total;
						grandCommission		+= totalcommission;
						grandShipping		+= round((shipping_cost*qty_ordered),2);
						
						content			+= "				<tr class=\"border\">";
						content			+= "					<td class=\"a-center\">"+orderData[i]["items"][j]["name"]+"</td>";
						content			+= "					<td class=\"a-center\">"+qty_ordered+"</td>";
						content			+= "					<td class=\"a-center\">$"+sprintf("%01.2f", price)+"</td>";
						content			+= "					<td class=\"a-center\">$"+sprintf("%01.2f", commission)+"</td>";
						content			+= "					<td class=\"a-center\">$"+sprintf("%01.2f", shipping_cost)+"</td>";
						content			+= "					<td class=\"a-right last\">";
						content			+= "						<span class=\"price\">$"+sprintf("%01.2f", vendor_total)+"</span>";
						content			+= "					</td>";
						content			+= "				</tr>";
					}
				}
				
				content			+= "			</tbody>";
				
				
				// place the "total" row
				classname		= classname=="" || classname=="odd" ? "even" : "odd";
				
				content			+= "			<tbody class=\""+classname+"\">";
				content			+= "				<tr class=\"border\">";
				content			+= "					<td class=\"a-right\" valign=\"middle\" colspan=\"4\">";
				content			+= "						";
				content			+= "					</td>";
				content			+= "					<td class=\"a-center\" valign=\"middle\">";
				content			+= "						<span class=\"price\"><b>$"+sprintf("%01.2f", grandCommission)+"</b></span>";
				content			+= "					</td>";
				content			+= "					<td class=\"a-center\" valign=\"middle\">";
				content			+= "						<span class=\"price\"><b>$"+sprintf("%01.2f", grandShipping)+"</b></span>";
				content			+= "					</td>";
				content			+= "					<td class=\"a-right last\" valign=\"middle\">";
				content			+= "						<span class=\"price\"><b>$"+sprintf("%01.2f", grandTotal)+"</b></span>";
				content			+= "					</td>";
				content			+= "				</tr>";
				content			+= "			</tbody>";
				
				grandTotal		= 0;
				grandCommission	= 0;
				grandShipping	= 0;
				
			}
		}
		
		
		
		
		
		content			+= "		</table>";
		content			+= "	</div>";
		content			+= "</div>";
		content			+= "<br>";
		
		
		
		parentdiv		= elem;
		
		if(parentdiv!==false)
		{
			for(var iparentdiv in parentdiv.childNodes)
			{
				if(parentdiv.childNodes[iparentdiv].className=="box-right" && count<=countlimit)
				{
					count++;
					if(count==countlimit)
					{
						parentdiv.childNodes[iparentdiv].innerHTML	= parentdiv.childNodes[iparentdiv].innerHTML + content;
					}
				}
			}
		}
		
		
		
	};