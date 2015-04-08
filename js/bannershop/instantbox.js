

function post() {
    if (document.getElementById("product").value == '') {
        alert("Please Select Product.");
        document.getElementById('product').focus();
        return false;
    }
    if (document.getElementById("size_w").value == '' || document.getElementById("size_h").value == '') {
        alert("Please Enter Size.");
        return false;
    }
    if (document.getElementById("size_w").value > 30 || document.getElementById("size_h").value > 10) {
        alert("For special low price on sizes over 30ft wide and 10ft height contact us on sales@bannerbuzz.com.");
        return false;
    }
    var cid = document.getElementById('product').value;
    var session_id = document.getElementById('sessid').value;
    document.instant.action = '';
    document.instant.submit();
}
function instant_popupp_post() {
    var bannerid = document.getElementById('bannerid').value;
	var cateid = document.getElementById('product').value;
	var categoryid = cateid.split(":"); 
	var cid = categoryid[0];
	var subcatid = categoryid[1];
	
    var major = document.getElementById('major').value;
    if (major == 'in' && cid != 37) {
        if (isFloat(document.getElementById('size_h').value / 12)) {
            document.getElementById('size_h').value = (document.getElementById('size_h').value / 12).toFixed(2);
        } else {
            document.getElementById('size_h').value = (document.getElementById('size_h').value / 12);
        }
        if (isFloat(document.getElementById('size_w').value / 12)) {
            document.getElementById('size_w').value = (document.getElementById('size_w').value / 12).toFixed(2);
        } else {
            document.getElementById('size_w').value = (document.getElementById('size_w').value / 12);
        }
		document.getElementById('major').value = "ft";
		
    }
    var size_w = document.getElementById('size_w').value;
    var size_h = document.getElementById('size_h').value;
    var size = document.getElementById('size').value;
    if (document.getElementById('qty').value == "More") {
        var qty = document.getElementById('qty').value + "&q=" + document.getElementById('qtyText').value;
    } else {
        var qty = document.getElementById('qty').value;
    }
    var shipping_method = document.getElementById('shipping_method').value;
    var side = document.getElementById('side').value;
    var price = document.getElementById('price').value;
    var session_id = document.getElementById('sessid').value;
    if (document.getElementById("size_w").value == '') {
        alert('Please Enter Width');
        document.getElementById("size_w").focus();
        return false;
    }
    if (document.getElementById("size_h").value == '') {
        alert('Please Enter Height');
        document.getElementById("size_h").focus();
        return false;
    }
    if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 1 || document.getElementById("bannerid").value == 8)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 6)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 2)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 2)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 3)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 4)) {
        return false;
    }
	if((document.getElementById("size_w").value > 8 && document.getElementById("size_h").value > 8)  && (document.getElementById("bannerid").value == 10 || document.getElementById("bannerid").value == 11))
	{
	 	  return false;
	}
	 if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 12)) {
        return false;
    }
	 if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 13)) {
        return false;
    }
	
	if((document.getElementById("size_w").value > 2 || document.getElementById("size_h").value > 5)  && (document.getElementById("bannerid").value == 14))
	{
	 	  return false;
	}
	
	if((document.getElementById("size_w").value > 5 && document.getElementById("size_h").value > 5)  && (document.getElementById("bannerid").value == 3))
	{
	 	  return false;
	}
	 
	 if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 9)) {
        return false;
    }
	
    var page_index = document.getElementById('product').selectedIndex;
    var pagename = document.getElementById('product').options[page_index].title;
     if (pagename != '') {
        if (cid == "") {
            alert("Please select the Product.");
			document.getElementById('major').value = "ft";
            document.getElementById('product').focus();
            return false;
        } else {
			document.instant.action = pagename;
        }
    } else {
        if (cid == 30) {
			document.instant.action = "30-5/full-color-vinyl-banners.html";
        } else if (cid == 31) {
			document.instant.action = "31-6/perforated-window-signs.html";
        } else if (cid == 32) {
			document.instant.action = "32-7/vinyl-decals.html"; 
         } else if (cid == 33) {
			 document.instant.action = "33-3/canvas-banners.html"; 
         } else if (cid == 34) {
			document.instant.action = "34-4/backlit-banners.html"; 
         } else if (cid == 37) {
			document.instant.action = "yard-signs.html"; 
         } else if (cid == 38) {
			document.instant.action = "vinyl-lettering.html"; 
        } else if (cid == 39) {
			document.instant.action = "mesh-banners.html"; 
        } else if (cid == 40) {
			document.instant.action = "cloth-fabric-banners.html"; 
        } else if (cid == 41) {
			document.instant.action = "custom-table-covers.html"; 
        } else if (cid == 42) {
			document.instant.action = "billboard-printing.html"; 
        } else if (cid == 43) {
			document.instant.action = "step-and-repeat-banners.html";  
        } else if (cid == 44) {
			document.instant.action = "floor-decals-signs.html";  
        } else if (cid == 45) {
			document.instant.action = "magnetic-signs.html";
        }
		
		else if (cid == "") {
            alert("Please select the Product.");
			document.getElementById('major').value = "ft";
            document.getElementById('product').focus();
            return false;
        }
     }
	 
	 document.instant.submit();
}

function instant_popup_post() {
    var bannerid = document.getElementById('bannerid').value;
	var cateid = document.getElementById('product').value;
	var categoryid = cateid.split(":"); 
	var cid = categoryid[0];
	var subcatid = categoryid[1];
	
    var major = document.getElementById('major').value;
    if (major == 'in' && cid != 37) {
        if (isFloat(document.getElementById('size_h').value / 12)) {
            document.getElementById('size_h').value = (document.getElementById('size_h').value / 12).toFixed(2);
        } else {
            document.getElementById('size_h').value = (document.getElementById('size_h').value / 12);
        }
        if (isFloat(document.getElementById('size_w').value / 12)) {
            document.getElementById('size_w').value = (document.getElementById('size_w').value / 12).toFixed(2);
        } else {
            document.getElementById('size_w').value = (document.getElementById('size_w').value / 12);
        }
		document.getElementById('major').value = "ft";
    }
    var size_w = document.getElementById('size_w').value;
    var size_h = document.getElementById('size_h').value;
    var size = document.getElementById('size').value;
    var qty = document.getElementById('qty').value;
    var shipping_method = document.getElementById('shipping_method').value;
    var side = document.getElementById('side').value;
    var price = document.getElementById('price').value;
    var session_id = document.getElementById('sessid').value;
    if (document.getElementById("size_w").value == '') {
        alert('Please Enter Width');
        document.getElementById("size_w").focus();
        return false;
    }
    if (document.getElementById("size_h").value == '') {
        alert('Please Enter Height');
        document.getElementById("size_h").focus();
        return false;
    }
    if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 1 || document.getElementById("bannerid").value == 8)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 6)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 2)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 3)) {
        return false;
    }
    if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 4)) {
        return false;
    }
	if((document.getElementById("size_w").value > 8 && document.getElementById("size_h").value > 8)  && (document.getElementById("bannerid").value == 10 || document.getElementById("bannerid").value == 11))
	{
	 	  return false;
	}
	 if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 12)) {
        return false;
    }
	if (((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 13)) {
        return false;
    }
	
	if((document.getElementById("size_w").value > 2 || document.getElementById("size_h").value > 5)  && (document.getElementById("bannerid").value == 14))
	{
	 	  return false;
	}
	
	if((document.getElementById("size_w").value > 5 && document.getElementById("size_h").value > 5)  && (document.getElementById("bannerid").value == 3))
	{
	 	  return false;
	}
	
    var page_index = document.getElementById('product').selectedIndex;
    var pagename = document.getElementById('product').options[page_index].id;
    if (pagename != '') {
        if (cid == "") {
            alert("Please select the Product.");
			document.getElementById('major').value = "ft";
            document.getElementById('product').focus();
            return false;
        } else {
			document.instant.action = pagename;
        }
    } else {
        if (cid == 30) {
			document.instant.action = "30-5/full-color-vinyl-banners.html";
        } else if (cid == 31) {
			document.instant.action = "31-6/perforated-window-signs.html";
        } else if (cid == 32) {
			document.instant.action = "32-7/vinyl-decals.html"; 
         } else if (cid == 33) {
			 document.instant.action = "33-3/canvas-banners.html"; 
         } else if (cid == 34) {
			document.instant.action = "34-4/backlit-banners.html"; 
         } else if (cid == 37) {
			document.instant.action = "yard-signs.html"; 
         } else if (cid == 38) {
			document.instant.action = "vinyl-lettering.html"; 
        } else if (cid == 39) {
			document.instant.action = "mesh-banners.html"; 
        } else if (cid == 40) {
			document.instant.action = "cloth-fabric-banners.html"; 
        } else if (cid == 41) {
			document.instant.action = "custom-table-covers.html"; 
        } else if (cid == 42) {
			document.instant.action = "billboard-printing.html"; 
        } else if (cid == 43) {
			document.instant.action = "step-and-repeat-banners.html";  
        } else if (cid == 44) {
			document.instant.action = "floor-decals-signs.html";  
        } else if (cid == 45) {
			document.instant.action = "magnetic-signs.html";
        }
		else if (cid == "") {
            alert("Please select the Product.");
			document.getElementById('major').value = "ft";
            document.getElementById('product').focus();
            return false;
        }
     }
	 
	 document.instant.submit();
	 
}

function validEmail(frmName, field, msg) {
    var regEx = /^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/;
    var str1 = document.getElementById(field);
    var isValidE = regEx.test(str1.value);
    if (isValidE) return true;
    else {
        alert(msg);
        str1.focus();
        return false;
    }
}
function decreaseCounter(field) {
      if (document.getElementById("product").value == '') {
        alert("Please Select Product.");
        document.getElementById('product').focus();
        return false;
    }
    var str1 = document.getElementById(field);
	
 	var categoryid = document.getElementById('categoryid').value;
	categoryid = categoryid.split(":"); 
	var catid = categoryid[0];
	var subcatid = categoryid[1];
 	var start;
	if(catid == 42)
	{
	   	start = 6;
		var condition = str1.value > start
	}
	else
	{
		start = 2;
		var condition = str1.value >= start;
	}
	
	
    if (str1.value == '') {
        str1.value = start;
    } else if (condition) {
        if (isFloat(eval(str1.value) - 1)) {
            str1.value = (eval(str1.value) - 1).toFixed(2);
        } else {
            str1.value = eval(str1.value) - 1;
        }
    }
}

function gotoPrintReadyForm() {
    var cid = document.getElementById('product').value;
    var session_id = document.getElementById('sessid').value;
    document.instant.action = 'print_ready_form.php?id=' + cid + '&osCsid=' + session_id;
    document.instant.submit();
}

function gotoBannerTool() {
    var cid = document.getElementById('product').value;
    var session_id = document.getElementById('sessid').value;
    document.instant.action = 'banner_tool.php?id=' + cid + '&osCsid=' + session_id;
    document.instant.submit();
}

function gotoOrderForm() {
    var cid = document.getElementById('product').value;
    var session_id = document.getElementById('sessid').value;
    document.instant.action = 'order_form.php?id=' + cid + '&osCsid=' + session_id;
    document.instant.submit();
}

function sidebanner() {
 
	var prd = document.getElementById('product').value.split(":");
	
	
        if (document.getElementById('side').checked) {
            document.getElementById('side').checked = false;
            sidebannerprice();
            bprice();
            Shipping();
            document.getElementById('side').disabled = true;
        } else {
            document.getElementById('side').disabled = false;
    
    }
}
/*small category has min qty 5 Added by ketan*/
function loadqtycombo() {
    /*Added by ketan*/
	if(document.getElementById('categoryid').value == "")
	{
		document.getElementById('categoryid').value = 30;	
	}
    
        var drp = "<select id='qty' name='qty' onchange='quantity(),bprice(),Shipping(),qty_disc();' class='textfield dropdown-s'>";

        for (var i = 1; i <= 100; i++) {
            drp += "<option value=" + i;

            drp += ">";
            drp += i;
            drp += "</option>";

        }
        for (var i = 150; i <= 500; i += 50) {
            drp += "<option value=" + i;

            drp += ">";
            drp += i;
            drp += "</option>";

        }
        for (var i = 600; i <= 2000; i += 100) {
            drp += "<option value=" + i;

            drp += ">";
            drp += i;
            drp += "</option>";

        }
        drp += "<option value='More'>More</option>";
        drp += "</select>";
      // alert(drp);
        document.getElementById('qtydrp').innerHTML = drp;
   // }


}


function getvehiclemagneticpriceinstant()
{
	var size = document.getElementById('vehicle_magnetic_size').value;
	var vehiclesize = size.split("x");
	var width = vehiclesize[0]/12;
	var height = vehiclesize[1]/12;
    //alert(width + "  " + height);
	document.getElementById('size_w').value = width;
	document.getElementById('size_h').value = height;
}



function threeasywayorder(btnid)
{
	var cateid1 = document.getElementById('3wayproduct').value;
	var categoryid = cateid1.split(":"); 
	var catid = categoryid[0];
	var subcatid = categoryid[1]; 
	
	
	
	if(catid == 35)
     {
 		  if(subcatid == 29)
		  {
			 window.location = 'retractable-rollup-banner-stands.html';
		  }
		  else if(subcatid == 31)
		  {
			 window.location = 'rotating-banner-stands.html';
		  }
		  else if(subcatid == 78)
		  {
			 window.location = 'x-banner-stands.html';
		  }
		  else if(subcatid == 79)
		  {
			 window.location = 'backdrop.html';
		  }
		  else if(subcatid == 105)
		  {
			 
			 window.location = 'bamboo-banner-stands.html';
		  }
		  else if(subcatid == 109)
		  {
			window.location = 'a-frame.html';
		  }
		  else
		  {
		     window.location = 'banner-stands.html';
		  }
		  
		  
		  
		  
		  
		  return false;
	}
	
	if(catid == 41)
	{
		window.location = 'custom-table-covers.html';
		return false;
	}
	if(catid == 46)
	{
		window.location = 'flags.html';
		return false;
	}
	if(catid == 47)
	{
		window.location = 'canopies.html';
		return false;
	}
	if(subcatid == 94)
	  {
		 window.location = 'topline-banner-frame.html';
		 return false;
	  }
	if(subcatid == 96)
	{
		window.location = 'ez-post-banner-stand.html';
		return false;
	}
	if(subcatid == 103)
	{
		window.location = 'ground-mount-banner-frame.html';
		return false;
	}

    var qty=1;
	var width = 3;
	var height = 2;
 	if(catid == 42)
	{	
		width = 6;
		height = 6;
	}
	else if(catid == 37)
	{
		 width = 2;
		 height = 1.5;
	}
	else if(catid == 45 || catid == 44 || catid == 32 || catid == 38 || catid == 31 || catid == 40 || catid == 34 || catid == 39 || catid == 33)
	{	
	    if(catid == 45){
			width = 1;
			height = 1;
		}else{
			width = 2;
			height = 2;
		}
	}
	
	var major = 'ft';
	if(catid == 32 || catid == 38 || catid == 45 || catid == 37){
		major = 'in';	
 	}
 	var URL;
	
	if(btnid == 0)
	{
	    if(catid == 35)
		{
			document.getElementById('letusbtn').disabled=true;
			document.getElementById('letusbtn').style.opacity = 0.2;
		}
		else
		{
		   	document.getElementById('letusbtn').disabled=false;
			document.getElementById('letusbtn').style.opacity = '';
		
		}
		if(catid == 46 || catid == 47)
		{
			document.getElementById('prdbtn').disabled=true;
			document.getElementById('prdbtn').style.opacity = 0.2;
		}
		else
		{
			document.getElementById('prdbtn').disabled=false;
			document.getElementById('prdbtn').style.opacity = '';
		}
		 
	}
	else if(btnid == 1)
	{
		URL = 'print_ready_form.php?&id='+catid+'&sub_cat_id='+subcatid+'&info_id=5&size_w='+width+'&size_h='+height+'&m='+major+'&qty='+qty;
	}
	else if(btnid == 2)
	{
	    if(catid == 38)
		{
			var tool = 2;
		}
		else
		{
		   var tool = 1;
		}
		URL = 'banner_tool.php?width='+width+'&height='+ height +'&unit=ft&tool='+tool+'&catId='+subcatid+'&info_id=5&qty='+qty;
	}
	else if(btnid == 3)
	{
		URL = 'order_form.php?id='+catid+'&sub_cat_id='+subcatid+'&info_id=5&size_w='+width+'&size_h='+height+'&qty='+qty;
	}
	
	if(btnid != 0){
		   // window.location = URL; 
	}
	
	
	if(btnid==1){
	
	
	}
	
	
		
}

function checkNumeric()
{
   var major = document.getElementById('major').value;
 
   if(major == 'ft')
  {
   	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bannerid").value == 1 || document.getElementById("bannerid").value == 8))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
	if(((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100)) && (document.getElementById("bannerid").value == 6)){
		document.getElementById("vinylsize").style.display = "block";
		document.getElementById("vinylsize2").style.display = "none";
		document.getElementById('appsize').style.display = "none";	
		return false;
	}
	else
	{
		document.getElementById("vinylsize").style.display = "none";
	}
	if(((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bannerid").value == 2))
	{
		document.getElementById("vinylsize2").style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById('appsize').style.display = "none";
		return false;
	}
	else
	{
	document.getElementById("vinylsize2").style.display = "none";
	}
	
	
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bannerid").value == 3))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
	
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bannerid").value == 4))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
	
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bannerid").value == 7))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
	if(document.getElementById("size_w").value > 8 && document.getElementById("size_h").value > 8 && (document.getElementById("bannerid").value == 10 || document.getElementById("bannerid").value == 11))
	{
	  document.getElementById("clothmsg").style.display = "block";
	  return false;
	}
	else
	{
		document.getElementById("clothmsg").style.display = "none";
	}
	
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bannerid").value == 12))
	{
		document.getElementById('billboarderr').style.display = "block";
		return false;
	}
	else
	{
		document.getElementById('billboarderr').style.display = "none";
	}
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bannerid").value == 13))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}

	
	
	if(((document.getElementById("size_w").value > 2 || document.getElementById("size_h").value > 5)) && (document.getElementById("bannerid").value == 14))
	{
		document.getElementById('megnaticsign').style.display = "block";
		return false;
	}
	else
	{
		document.getElementById('megnaticsign').style.display = "none";
	}
	

	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bannerid").value == 9))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
    
	if(((document.getElementById("size_w").value > 5 && document.getElementById("size_h").value > 5)) && (document.getElementById("bannerid").value == 3))
	{
		document.getElementById('canvasbanner').style.display = "block";
		return false;
	}
	else
	{
		document.getElementById('canvasbanner').style.display = "none";
	}
 }
  if(major == 'in')
 {
 
   	if(((document.getElementById("size_w").value > 360 && document.getElementById("size_h").value > 360) || (document.getElementById("size_h").value > 360) || (document.getElementById("size_w").value > 1200) ) && (document.getElementById("bannerid").value == 1 || document.getElementById("bannerid").value == 8))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
	if(((document.getElementById("size_w").value > 240 && document.getElementById("size_h").value > 240) || (document.getElementById("size_h").value > 240) || (document.getElementById("size_w").value > 1200)) && (document.getElementById("bannerid").value == 6)){
		document.getElementById("vinylsize").style.display = "block";
		document.getElementById("vinylsize2").style.display = "none";
		document.getElementById('appsize').style.display = "none";	
		return false;
	}
	else
	{
		document.getElementById("vinylsize").style.display = "none";
	}
	if(((document.getElementById("size_w").value > 240 && document.getElementById("size_h").value > 240) || (document.getElementById("size_h").value > 240) || (document.getElementById("size_w").value > 1200) ) && (document.getElementById("bannerid").value == 2))
	{
		document.getElementById("vinylsize2").style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById('appsize').style.display = "none";
		return false;
	}
	else
	{
	document.getElementById("vinylsize2").style.display = "none";
	}
	
	
	if(((document.getElementById("size_w").value > 360 && document.getElementById("size_h").value > 360) || (document.getElementById("size_h").value > 360) || (document.getElementById("size_w").value > 1200) ) && (document.getElementById("bannerid").value == 3))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
	
	if(((document.getElementById("size_w").value > 360 && document.getElementById("size_h").value > 360) || (document.getElementById("size_h").value > 360) || (document.getElementById("size_w").value > 1200) ) && (document.getElementById("bannerid").value == 4))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
	
	if(((document.getElementById("size_w").value > 360 && document.getElementById("size_h").value > 360) || (document.getElementById("size_h").value > 360) || (document.getElementById("size_w").value > 1200) ) && (document.getElementById("bannerid").value == 7))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
	if(document.getElementById("size_w").value > 96 && document.getElementById("size_h").value > 96 && (document.getElementById("bannerid").value == 10 || document.getElementById("bannerid").value == 11))
	{
	  document.getElementById("clothmsg").style.display = "block";
	  return false;
	}
	else
	{
		document.getElementById("clothmsg").style.display = "none";
	}
    if(((document.getElementById("size_w").value > 360 && document.getElementById("size_h").value > 360) || (document.getElementById("size_h").value > 360) || (document.getElementById("size_w").value > 1200) ) && (document.getElementById("bannerid").value == 12))
	{
		document.getElementById('billboarderr').style.display = "block";
		return false;
	}
	else
	{
		document.getElementById('billboarderr').style.display = "none";
	}
	if(((document.getElementById("size_w").value > 360 && document.getElementById("size_h").value > 360) || (document.getElementById("size_h").value > 360) || (document.getElementById("size_w").value > 1200) ) && (document.getElementById("bannerid").value == 13))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}

    
	if(((document.getElementById("size_w").value > 24 || document.getElementById("size_h").value > 60)) && (document.getElementById("bannerid").value == 14))
	{
		document.getElementById('megnaticsign').style.display = "block";
		return false;
	}
	else
	{
		document.getElementById('megnaticsign').style.display = "none";
	}
	
	if(((document.getElementById("size_w").value > 60 && document.getElementById("size_h").value > 60)) && (document.getElementById("bannerid").value == 3))
	{

		document.getElementById('canvasbanner').style.display = "block";
		return false;
	}
	else
	{
		document.getElementById('canvasbanner').style.display = "none";
	}
	

	if(((document.getElementById("size_w").value > 360 && document.getElementById("size_h").value > 360) || (document.getElementById("size_h").value > 360) || (document.getElementById("size_w").value > 1200) ) && (document.getElementById("bannerid").value == 9))
	{
		document.getElementById('appsize').style.display = "block";
		document.getElementById("vinylsize").style.display = "none";
		document.getElementById("vinylsize2").style.display = "none";
		return false;
	}
	else
	{
		document.getElementById('appsize').style.display = "none";
	}
 

 }
	
	if(isNaN(document.getElementById("size_w").value))
	{	alert("Please enter only numeric value for width.");
		document.getElementById("size_w").value= 2;	
		document.getElementById("size_w").focus();
		return false;	
	}else
	{
		if (document.getElementById("size_w").value < 0)
		{
		    document.getElementById("size_w").value = 2;
			alert("Please do not enter negative value for width.");
			document.getElementById("size_w").focus();
			return false;
		}  
	}
	if(isNaN(document.getElementById("size_h").value))
	{
		document.getElementById("size_h").value= 2;
		alert("Please enter only numeric value for height.");
		document.getElementById("size_h").focus();
		return false;	
	}
	else
	{
		if (document.getElementById("size_h").value < 0)
		{
		    document.getElementById("size_h").value = 2;
			alert("Please do not enter negative value for height.");
			document.getElementById("size_h").focus();
			return false;
		}  
	}
	if(isNaN(document.getElementById("qtyText").value))
	{	alert("Please enter only numeric value for Quantity.");
		document.getElementById("qtyText").value= 1;	
		document.getElementById("qtyText").focus();
		return false;	
	}else
	{
		if (document.getElementById("qtyText").value < 0)
		{
		    document.getElementById("qtyText").value = 1;
			alert("Please do not enter negative value for Quantity.");
			document.getElementById("qtyText").focus();
			return false;
		}  
	}
	return true;
}

function makeactive(tab) {
    
	if(tab == 1)
	{
 		document.getElementById("tab2").className = ""; 
		document.getElementById("tab3").className = ""; 
	}
	else if(tab == 2)
	{
	   document.getElementById("tab1").className = "";
 		document.getElementById("tab3").className = ""; 
	}
	else if(tab == 3)
	{
	   document.getElementById("tab1").className = "";
	   document.getElementById("tab2").className = ""; 
 	}
	document.getElementById("tab"+tab).className = "active"; 
	callAHAH('ajax/bannershopajax.php?content='+tab+'&crAction=tabs', 'content', '', 'Error');
 }
function callAHAH(url, pageElement, callMessage, errorMessage) {
     document.getElementById(pageElement).innerHTML = callMessage;
     try {
     req = new XMLHttpRequest(); /* e.g. Firefox */
     } catch(e) {
       try {
       req = new ActiveXObject("Msxml2.XMLHTTP");  /* some versions IE */
       } catch (e) {
         try {
         req = new ActiveXObject("Microsoft.XMLHTTP");  /* some versions IE */
         } catch (E) {
          req = false;
         } 
       } 
     }
     req.onreadystatechange = function() {responseAHAH(pageElement, errorMessage);};
     req.open("GET",url,true);
     req.send(null);
  }

function responseAHAH(pageElement, errorMessage) {
   var output = '';
   if(req.readyState == 4) {
	   
      if(req.status == 200) {
         output = req.responseText;
         document.getElementById(pageElement).innerHTML = output;
         } else {
         document.getElementById(pageElement).innerHTML = errorMessage+"\n"+output;
         }
		 
      }else{
		 output='<img src="js/bannershop/loading.gif" style="margin-left:70px;"/>'
		 document.getElementById(pageElement).innerHTML = output;
		   
		  }
  }