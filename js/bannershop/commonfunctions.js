// JavaScript Document
function getXMLHTTP() { 
		var xmlhttp=false;	
		try{
			xmlhttp=new XMLHttpRequest();
		}
		catch(e)	{		
			try{			
				xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e){
				try{
				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e1){
					xmlhttp=false;
				}
			}
		}
		return xmlhttp;
}

function checkcountry(name,cnameurl)
			{	
 					strURL = 'sessionCountry.php?country='+name;
					var req = getXMLHTTP();		
					if(req)
					{
						req.onreadystatechange = function()
						{
							if (req.readyState == 4)
							{
								if (req.status == 200)
								{
									if(req.responseText !== 0)
									{
										//location.reload(true);
										document.getElementById('selectedcountry').innerHTML = name;
										if(name == 'Czech Republic'){
											name = 'CzechRepublic';
										}
										if(name == 'New Zealand'){
											name = 'NewZealand';
										}
										if(name == 'United States'){
											name = 'UnitedStates';
										}
										document.getElementById('mainselectedclass').className  = 'flagSprite ' + name;
										window.location = cnameurl;
									}
								}
							}
						}
						req.open("GET", strURL, true);
						req.send(null);
					}			
				
			}
/*Testimonail Function*/

function testimonial(act)
{
	var recid = document.getElementById('testimonid').value;
	if(act == "next")
	{
 		recid = parseFloat(recid)+parseFloat(1);
	}
	if(act == "prev")
	{
 	  recid = parseFloat(recid)-parseFloat(1);
	}
 	var strURL = 'testimonialrecord.php?recid='+recid;
   	var req = getXMLHTTP();		
	if (req) 
	{
		req.onreadystatechange = function()
		{ 
 			if (req.readyState == 4) 
			{
   				if (req.status == 200)
				{	
 					 var testimonialdata = req.responseText.split("$|$");
					 var testiminial_comment = testimonialdata[0];
					 var testiminial_commentBy = testimonialdata[1];
 					 document.getElementById('testimonal_comment').innerHTML = testiminial_comment;
					 document.getElementById('testimonal_commentBy').innerHTML = '- '+testiminial_commentBy
					 if(act == "next")
					{
						 document.getElementById('testimonid').value = recid;
					}
					if(act == "prev")
					{
					    document.getElementById('testimonid').value = recid;
					}
					 if(document.getElementById('testimonid').value > 1)
					 {
					    document.getElementById('prev_testimonial').style.display = 'block';
					 }
					 else
					 {
					    document.getElementById('prev_testimonial').style.display = 'none';
					 }
					 if(document.getElementById('testimonid').value > 6)
					 {
					    document.getElementById('next_testimonial').style.display = 'none';
					 }
					 else
					 {
					    document.getElementById('next_testimonial').style.display = 'block';
					 }
 				} 
				else 
				{
					alert("There was a problem while using XMLHTTP:\n" + req.statusText);
				}
			}				
		 }			
		 req.open("GET", strURL, true);
		 req.send(null);
	}			
}

/* End Testimonail Function*/
/*Left Navigation*/
function showletteringcategory()
{
	var id = document.getElementById('letteringcatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('letteringcategory').style.display = "block";
		document.getElementById('letteringcatvalueshow').value = '1';
		document.getElementById('letteringcatimage').innerHTML = '<span class="minus"> </span>';
		
		
	}
	else
	{
	   document.getElementById('letteringcategory').style.display = "none";
	   document.getElementById('letteringcatvalueshow').value = '0';
	   document.getElementById('letteringcatimage').innerHTML = '<span class="plus"> </span>';
		
	}
 }
function showbannerstandcategory()
{
    var id = document.getElementById('bannerstandcatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('bannerstandcategory').style.display = "block";
		document.getElementById('bannerstandcatvalueshow').value = '1';
		document.getElementById('bannerstandcat').innerHTML = '<span class="minus"> </span>';
		
		
	}
	else
	{
	   document.getElementById('bannerstandcategory').style.display = "none";
	   document.getElementById('bannerstandcatvalueshow').value = '0';
	   document.getElementById('bannerstandcat').innerHTML = '<span class="plus"> </span>';
 	}
 }
function showvinylbannercategory()
{
	var id = document.getElementById('vinylbannercatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('vinylbannercategory').style.display = "block";
		document.getElementById('vinylbannercatvalueshow').value = '1';
		document.getElementById('vinylbannercat').innerHTML = '<span class="minus"> </span>';
		
		
	}
	else
	{
	   document.getElementById('vinylbannercategory').style.display = "none";
	   document.getElementById('vinylbannercatvalueshow').value = '0';
	   document.getElementById('vinylbannercat').innerHTML = '<span class="plus"> </span>';
 	}
}
function showdecalcategory()
{
    var id = document.getElementById('decalscatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('decalcategory').style.display = "block";
		document.getElementById('decalscatvalueshow').value = '1';
		document.getElementById('decalcatimage').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('decalcategory').style.display = "none";
	   document.getElementById('decalscatvalueshow').value = '0';
	   document.getElementById('decalcatimage').innerHTML = '<span class="plus"> </span>';
 	}
}
function showwindowclingcategory()
{
   var id = document.getElementById('windowclingvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('windowclingcategory').style.display = "block";
		document.getElementById('windowclingvalueshow').value = '1';
		document.getElementById('windowclingcatimage').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('windowclingcategory').style.display = "none";
	   document.getElementById('windowclingvalueshow').value = '0';
	   document.getElementById('windowclingcatimage').innerHTML = '<span class="plus"> </span>';
 	}
}
function showmagneticsigncategory()
{
   var id = document.getElementById('magneticsigncatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('magericsigncategory').style.display = "block";
		document.getElementById('magneticsigncatvalueshow').value = '1';
		document.getElementById('magnetsigncat').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('magericsigncategory').style.display = "none";
	   document.getElementById('magneticsigncatvalueshow').value = '0';
	   document.getElementById('magnetsigncat').innerHTML = '<span class="plus"> </span>';
 	}
}
function bannerandsigncategory()
{
   var id = document.getElementById('bannerandsigncatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('bannerandsigncategory').style.display = "block";
		document.getElementById('bannerandsigncatvalueshow').value = '1';
		document.getElementById('bannerandsigncatimage').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('bannerandsigncategory').style.display = "none";
	   document.getElementById('bannerandsigncatvalueshow').value = '0';
	   document.getElementById('bannerandsigncatimage').innerHTML = '<span class="plus"> </span>';
 	}
}

function showcustombannercategory(){
	var id = document.getElementById('custombannercatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('customvinylbannercategory').style.display = "block";
		document.getElementById('custombannercatvalueshow').value = '1';
		document.getElementById('custombannercat').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('customvinylbannercategory').style.display = "none";
	   document.getElementById('custombannercatvalueshow').value = '0';
	   document.getElementById('custombannercat').innerHTML = '<span class="plus"> </span>';
 	}
}

function showframecategory()
{
    var id = document.getElementById('framecatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('framecategory').style.display = "block";
		document.getElementById('framecatvalueshow').value = '1';
		document.getElementById('framecat').innerHTML = '<span class="minus"> </span>';
		
		
	}
	else
	{
	   document.getElementById('framecategory').style.display = "none";
	   document.getElementById('framecatvalueshow').value = '0';
	   document.getElementById('framecat').innerHTML = '<span class="plus"> </span>';
 	}
 }
 
 function showbirthdaysubcat()
{
    var id = document.getElementById('birthdaycatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('birthdaycategory').style.display = "block";
		document.getElementById('birthdaycatvalueshow').value = '1';
		document.getElementById('birthdaysubcat').innerHTML = '<span class="minus"> </span>';
		
		
	}
	else
	{
	   document.getElementById('birthdaycategory').style.display = "none";
	   document.getElementById('birthdaycatvalueshow').value = '0';
	   document.getElementById('birthdaysubcat').innerHTML = '<span class="plus"> </span>';
 	}
 }
 
 function showschoolubcat()
{
    var id = document.getElementById('schoolcatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('schoolcategory').style.display = "block";
		document.getElementById('schoolcatvalueshow').value = '1';
		document.getElementById('schoolsubcat').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('schoolcategory').style.display = "none";
	   document.getElementById('schoolcatvalueshow').value = '0';
	   document.getElementById('schoolsubcat').innerHTML = '<span class="plus"> </span>';
 	}
 }
 
 function showpolesubcat()
{
    var id = document.getElementById('polevalueshow').value; 
	if(id == 0)
	{
		document.getElementById('polecategory').style.display = "block";
		document.getElementById('polevalueshow').value = '1';
		document.getElementById('polesubcat').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('polecategory').style.display = "none";
	   document.getElementById('polevalueshow').value = '0';
	   document.getElementById('polesubcat').innerHTML = '<span class="plus"> </span>';
 	}
 }
 
 function showdisplaysubcat()
 {
	  var id = document.getElementById('displaycatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('displaycategory').style.display = "block";
		document.getElementById('displaycatvalueshow').value = '1';
		document.getElementById('displaysubcat').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('displaycategory').style.display = "none";
	   document.getElementById('displaycatvalueshow').value = '0';
	   document.getElementById('displaysubcat').innerHTML = '<span class="plus"> </span>';
 	}
 }
 function showsportssubcat()
 {
	  var id = document.getElementById('sportscatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('sportscategory').style.display = "block";
		document.getElementById('sportscatvalueshow').value = '1';
		document.getElementById('sportssubcat').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('sportscategory').style.display = "none";
	   document.getElementById('sportscatvalueshow').value = '0';
	   document.getElementById('sportssubcat').innerHTML = '<span class="plus"> </span>';
 	}
 }
  function showeventsubcat()
 {
	  var id = document.getElementById('eventcatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('eventcategory').style.display = "block";
		document.getElementById('eventcatvalueshow').value = '1';
		document.getElementById('eventsubcat').innerHTML = '<span class="minus"> </span>';
 	}
	else
	{
	   document.getElementById('eventcategory').style.display = "none";
	   document.getElementById('eventcatvalueshow').value = '0';
	   document.getElementById('eventsubcat').innerHTML = '<span class="plus"> </span>';
 	}
 }
 function showpersonlisesubcat()
{
    var id = document.getElementById('personalisecatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('personalisecategory').style.display = "block";
		document.getElementById('personalisecatvalueshow').value = '1';
		document.getElementById('personalisesubcat').innerHTML = '<span class="minus"> </span>';
		
		
	}
	else
	{
	   document.getElementById('personalisecategory').style.display = "none";
	   document.getElementById('personalisecatvalueshow').value = '0';
	   document.getElementById('personalisesubcat').innerHTML = '<span class="plus"> </span>';
 	}
 }
 
function showphotosubcat()
{
	var id = document.getElementById('photocatvalueshow').value; 
	if(id == 0)
	{
		document.getElementById('photocategory').style.display = "block";
		document.getElementById('photocatvalueshow').value = '1';
		document.getElementById('photosubcat').innerHTML = '<span class="minus"> </span>';
		
		
	}
	else
	{
	   document.getElementById('photocategory').style.display = "none";
	   document.getElementById('photocatvalueshow').value = '0';
	   document.getElementById('photosubcat').innerHTML = '<span class="plus"> </span>';
	}
}
 
 function showbusinesssubcat(){
	    var id = document.getElementById('businesscatvalueshow').value; 
		if(id == 0)
		{
			document.getElementById('businesscategory').style.display = "block";
			document.getElementById('businesscatvalueshow').value = '1';
			document.getElementById('businesssubcat').innerHTML = '<span class="minus"> </span>';
			
			
		}
		else
		{
		   document.getElementById('businesscategory').style.display = "none";
		   document.getElementById('businesscatvalueshow').value = '0';
		   document.getElementById('businesssubcat').innerHTML = '<span class="plus"> </span>';
		} 
 }
 
/* Left navigation */

/* Browse image and Yousendit image */

function checkuploadvalue(val)
{
    if(val == 1)
	{
	    document.getElementById('yousendit').checked = true;
		document.getElementById('yousendit1').checked = false;
	   	document.getElementById('uploadusendit').disabled = false;
		document.getElementById('bgimage').disabled = true;
		document.getElementById('sendlater_artwork').value = ""; 
		document.getElementById('uploadusendit').style.opacity = '';
	}
	else if(val == 2)
	{
		document.getElementById('artworklater').checked = true; 
		document.getElementById('yousendit').checked = false;
		document.getElementById('yousendit1').checked = false;
		document.getElementById('uploadusendit').disabled = true;
		document.getElementById('bgimage').disabled = true;
		document.getElementById('sendlater_artwork').value = "yes";
		document.getElementById('uploadusendit').style.opacity = '0.3';
 	}
	else
	{
		document.getElementById('yousendit').checked = false;
		document.getElementById('yousendit1').checked = true;
		document.getElementById('uploadusendit').disabled = true;
		document.getElementById('bgimage').disabled = false;
		document.getElementById('sendlater_artwork').value = "";
		document.getElementById('uploadusendit').style.opacity = '0.3';
	}

}

/* Browse image and Yousendit image */


/*=============== VELCRO OPTIONS ================*/		
function getvelcrooptions(val) {
	if(val == 1 || val == 2) {
		document.getElementById("velcro_options").value = "Top & Bottom Only";
		document.getElementById("divvelcro").style.display = "block";
		document.getElementById('grommetoptions').value = 'No Grommets';
		document.getElementById("grommetoptions").disabled = true;
		document.getElementById('pocketsoptions').disabled = false;
		document.getElementById("grommeterrmsg").style.display = "block";
		
		//Clear grommet price set
		if(document.getElementById('cleargroometprice'))
		{
			if(document.getElementById('cleargroometprice').value != '' && document.getElementById('cleargroometprice').value != '0')
			{
				var grand_total = document.getElementById('total').value;  
				document.getElementById("div_cleargroometprice").innerHTML = '';
 				grand_total= Number(grand_total)-Number(document.getElementById("cleargroometprice").value);
				document.getElementById("cleargroometprice").value=0;
				roundNumbertc(grand_total,2);
			}
		}
		
		getvelcroprice();	
	}
	else {
		document.getElementById("divvelcro").style.display = "none";
		document.getElementById("grommeterrmsg").style.display = "none";
		document.getElementById("grommetoptions").disabled = false;
 		getfinalprice();	
	}
	
 }

function getvelcroprice() {
	var val = document.getElementById("velcro_options").value;
	var velcroprice = document.getElementById("velcroprice").value;
	if(document.getElementById("velcro_material").checked == true || document.getElementById("velcro_installed").checked == true) {
		if(velcroprice != "") {
			var total = document.getElementById("total").value;
			var grand_total=(parseFloat(total) - parseFloat(velcroprice));
			roundNumbertc(grand_total,2);
		}
		
		if(document.getElementById('major')) {
			var major = document.getElementById('major').value;
		}
		else {
			var major = "ft";			
		}
		
			 
		var width = document.getElementById('size_w').value;
		var height = document. getElementById("size_h").value;
		if(major == "in") {
			width =  document.getElementById('size_w').value / 12;	
			height = document. getElementById("size_h").value / 12;
		}
		
		var vel_qty = document. getElementById("velcro_qty").value;
		if(vel_qty == ""){
			document. getElementById("velcro_qty").value = 1;
			vel_qty = 1;
		}
		
		if(document.getElementById("velcro_material").checked == true)
		{
			var Velprice = 0.40;
		}
		else
		{
			var Velprice = 1.01;
		}
		if(val == "Top & Bottom Only") {
			velcroprice = Velprice * (2 * width);
		}
		else if(val == "Two sides Only") {
			velcroprice = Velprice * (2 * height);
		}
		else if(val == "All four sides") {
			velcroprice = Velprice * ((2 * width) + (2 * height));
		}
	//	alert(velcroprice);
		velcroprice = parseFloat(velcroprice) * parseFloat(vel_qty);
 		velcroprice = velcroprice.toFixed(2);
		if(document.getElementById("velcro_material").checked == true )
		{
			if(velcroprice < 4.99) {
			  velcroprice = 4.99;
		    }
		}
		else
		{
			if(velcroprice < 7.99) {
			  velcroprice = 7.99;
		    }
 		}
		//velcroprice = parseFloat(velcroprice) * parseFloat(vel_qty);
 		//velcroprice = velcroprice.toFixed(2);
 		document.getElementById("velcropricehtml").innerHTML = "$"+velcroprice;
		document.getElementById("velcroprice").value = velcroprice;
		getfinalprice();
	}
	else {
		getfinalprice();
	}
}
function getfinalprice()
{
	var velcroprice = document.getElementById("velcroprice").value;
	if(velcroprice == "") {
		velcroprice = 0;	
	}
	var total = document.getElementById("total").value;
	if(document.getElementById("velcro_material").checked == true || document.getElementById("velcro_installed").checked == true) {
		grand_total=(parseFloat(total) + parseFloat(velcroprice));
	}
	else {
		grand_total=(parseFloat(total) - parseFloat(velcroprice));
		document.getElementById("velcroprice").value = 0;
	}
	roundNumbertc(grand_total,2);
}

/*=============== VALCRO OPTIONS ================*/		

/*================ BUNGEES =============== */
function getbungeesprice(id){
	if(id != "" && id > 0) {
		var size = 	document.getElementById("size_"+id).value;
		var qty = document.getElementById("qty_"+id).value;
	}
	else {
		var size = document.getElementById("size").value;
		var qty = document.getElementById("qty").value;	
	}
	var price = "";
	if(size == 10) {
		price = 2.99;
	}
	else if(size == 20) {
		price = 3.99;
	}
	else if(size == 30) {
		price = 4.49;
	}
	else if(size == 40) {
		price = 4.99;
	}
	if(id != "" && id > 0) {
		document.getElementById("price_"+id).value = price;
	}
	else {
		document.getElementById("price").value = price;	
	}
	if(qty != "") {
		price = parseFloat(price) * parseFloat(qty);
	}
	
	price = parseFloat(price).toFixed(2);
	
	if(id != "" && id > 0) {
		document.getElementById("total_display_"+id).innerHTML = "$"+price;
		document.getElementById("total_"+id).value = price;
	}
	else {
		document.getElementById("total_display").innerHTML = "$"+price;
		document.getElementById("total").value = price;	
	}
	
}
/*================ BUNGEES =============== */

/*================ ZIP - TIES =============== */
function getziptiesprice(id){
	
	
	if(id != "" && id > 0) {
		var qty = document.getElementById("qty_"+id).value;
	}
	else {
		var qty = document.getElementById("qty").value;	
	}
	
	var price = 1.99;
	
	if(id != "" && id > 0) {
		document.getElementById("price_"+id).value = price;
	}
	else {
		document.getElementById("price").value = price;	
	}
	if(qty != "") {
		price = parseFloat(price) * parseFloat(qty);
	}
	
	price = parseFloat(price).toFixed(2);
	
	if(id != "" && id > 0) {
		document.getElementById("total_display_"+id).innerHTML = "$"+price;
		document.getElementById("total_"+id).value = price;
	}
	else {
		document.getElementById("total_display").innerHTML = "$"+price;
		document.getElementById("total").value = price;	
	}
	
}
/*================ ZIP - TIES =============== */


/*================ NYLON ROPE =============== */
function getnylonropeprice(id){
	
	if(id != "" && id > 0) {
		var size = 	document.getElementById("size_"+id).value;
		var qty = document.getElementById("qty_"+id).value;
	}
	else {
		var size = document.getElementById("size").value;
		var qty = document.getElementById("qty").value;	
	}
	var price = 0.25;
	if(id != "" && id > 0) {
		document.getElementById("price_"+id).value = price;
	}
	else {
		document.getElementById("price").value = price;	
	}
	if(size != "" && qty != "") {
		price = parseFloat(price) * parseFloat(size) * parseFloat(qty);
	}
	
	price = parseFloat(price).toFixed(2);

	if(id != "" && id > 0) {
		document.getElementById("total_display_"+id).innerHTML = "$"+price;
		document.getElementById("total_"+id).value = price;
	}
	else {
		document.getElementById("total_display").innerHTML = "$"+price;
		document.getElementById("total").value = price;	
	}
	
}
/*================ NYLON ROPE =============== */

/*================ SKYHOOKS ROPE =============== */
function getskyhooksprice(id){
	 if(id != "" && id > 0) {
		var size = 	document.getElementById("size_"+id).value;
		var qty = document.getElementById("qty_"+id).value;
	}
	else {
		var size = document.getElementById("size").value;
		var qty = document.getElementById("qty").value;	
	}
	var price = "";
	if(size == 2) {
		price = 2.63;
	}
	else if(size == 3.5) {
		price = 6;
	}
	
	if(id != "" && id > 0) {
		document.getElementById("price_"+id).value = price;
	}
	else {
		document.getElementById("price").value = price;	
	}
	if(qty != "") {
		price = parseFloat(price) * parseFloat(qty);
	}
	
	price = parseFloat(price).toFixed(2);
	
	if(id != "" && id > 0) {
		document.getElementById("total_display_"+id).innerHTML = "$"+price;
		document.getElementById("total_"+id).value = price;
	}
	else {
		document.getElementById("total_display").innerHTML = "$"+price;
		document.getElementById("total").value = price;	
	}
}
/*================ SKYHOOKS ROPE =============== */

/*================ Banner Clip =============== */
function getbannerclipprice(id){
	
	
	if(id != "" && id > 0) {
		var qty = document.getElementById("qty_"+id).value;
	}
	else {
		var qty = document.getElementById("qty").value;	
	}
	
	var price = 7.99;
	
	if(id != "" && id > 0) {
		document.getElementById("price_"+id).value = price;
	}
	else {
		document.getElementById("price").value = price;	
	}
	if(qty != "") {
		price = parseFloat(price) * parseFloat(qty);
	}
	
	price = parseFloat(price).toFixed(2);
	
	if(id != "" && id > 0) {
		document.getElementById("total_display_"+id).innerHTML = "$"+price;
		document.getElementById("total_"+id).value = price;
	}
	else {
		document.getElementById("total_display").innerHTML = "$"+price;
		document.getElementById("total").value = price;	
	}
	
}
/*================ Banner Clip  =============== */

/*================ Pole brackets =============== */
function getpolebracketprice(id){
	
	
	if(id != "" && id > 0) {
		var qty = document.getElementById("qty_"+id).value;
	}
	else {
		var qty = document.getElementById("qty").value;	
	}
  
	
	var price = 89.00;
	
	if(id != "" && id > 0) {
		document.getElementById("price_"+id).value = price;
	}
	else {
		document.getElementById("price").value = price;	
	}
	if(qty != "") {
		price = parseFloat(price) * parseFloat(qty);
	}
	
	price = parseFloat(price).toFixed(2);
	
	if(id != "" && id > 0) {
		document.getElementById("total_display_"+id).innerHTML = "$"+price;
		document.getElementById("total_"+id).value = price;
	}
	else {
		document.getElementById("total_display").innerHTML = "$"+price;
		document.getElementById("total").value = price;	
	}
	
}
/*================Pole brackets  =============== */

/*================ Pole brackets =============== */
function getwallbracketprice(id){
 	if(id != "" && id > 0) {
		var qty = document.getElementById("qty_"+id).value;
	}
	else {
		var qty = document.getElementById("qty").value;	
	}
  
	
	var price = 89.00;
	
	if(id != "" && id > 0) {
		document.getElementById("price_"+id).value = price;
	}
	else {
		document.getElementById("price").value = price;	
	}
	if(qty != "") {
		price = parseFloat(price) * parseFloat(qty);
	}
	
	price = parseFloat(price).toFixed(2);
	
	if(id != "" && id > 0) {
		document.getElementById("total_display_"+id).innerHTML = "$"+price;
		document.getElementById("total_"+id).value = price;
	}
	else {
		document.getElementById("total_display").innerHTML = "$"+price;
		document.getElementById("total").value = price;	
	}
	
}
/*================Pole brackets  =============== */
 
/********** Finish Option - die cut***********/

function getdiecutprice()
{
	var id = document.getElementById('categoryid').value;
	var value = document.getElementById('finishoptions').value;
 	if(id == 32 || id == 31)
	{ 
		var grand_total = document.getElementById('total').value;
		var base_total = document.getElementById('base_total').value;
 		if(value == 'Die-cut')
		{
			//var diecutprice = base_total * 0.10 ;
			
			// Lamination disable
			if(document.getElementById('nolamination'))
			{
			   if(document.getElementById('glossylamination').checked == true)
			   {
					document.getElementById('nolamination').checked = true;
					var lamprice = document.getElementById('liminationprice').value;
					grand_total= Number(grand_total)-Number(lamprice);
					document.getElementById('liminationprice').value = '';
			   }
			   
			   document.getElementById('limitationpricedisplay').innerHTML = '';
			   document.getElementById('glossylamination').disabled = true;
			   document.getElementById('nolamination').disabled = true;
			}
			var diecutprice = base_total * 0.20 ;
			diecutprice = parseFloat(diecutprice).toFixed(2);
 			document.getElementById("div_finishdiecutprice").innerHTML = "$"+diecutprice;
			document.getElementById("finishdiecutoptions").value = diecutprice;
			grand_total= Number(grand_total)+Number(diecutprice);
			roundNumbertc(grand_total,2);
		}
		else
		{
			var producttype = 'Decals/Posters'; 
			var apply_at = 'Decals';
			if(document.getElementById('producttype')){
				producttype = document.getElementById('producttype').value;		
			}
			if(document.getElementById('apply_at')){
				apply_at = document.getElementById('apply_at').value;		
			}
			if(document.getElementById('nolamination'))
			{
 				if(producttype != 'Clings' && apply_at != 'Static cling'){
					document.getElementById('glossylamination').disabled = false;
   			    	document.getElementById('nolamination').disabled = false;  
				}
			}
 			var diecutprice = document.getElementById('finishdiecutoptions').value;
			document.getElementById("div_finishdiecutprice").innerHTML = "";
			document.getElementById("finishdiecutoptions").value = "";
			grand_total= Number(grand_total)-Number(diecutprice);
			roundNumbertc(grand_total,2);
			 
		}
	}
	if(id == 30 || id == 39 || id == 43 || id == 42)
	{
		var grand_total = document.getElementById('total').value;
		var base_total = document.getElementById('base_total').value;
		var qty = document.getElementById('qty').value;
 		if(value == 'Double Hemmed Finish'){
			var height = document.getElementById('size_h').value;
			var width = document.getElementById('size_w').value;
			var major = 'ft';
			if(document.getElementById('major')){
				major = document.getElementById('major').value;
				if(major == 'in'){
					height = height/12;
					width = width/12;
				}
			}
			var doublehemmprice = (2*(Number(height)+Number(width))*0.27)*(qty);
			doublehemmprice = parseFloat(doublehemmprice).toFixed(2);
			if(doublehemmprice <= 3.99){
					doublehemmprice = 3.99;
				}
 			document.getElementById("div_doublehemmedfinishprice").innerHTML = "$"+doublehemmprice + " <span style='font-size:10px;color:red;'> (Additional cost) </span>";
			document.getElementById("doublehemmedfinishprice").value = doublehemmprice;
			grand_total= Number(grand_total)+Number(doublehemmprice);
			roundNumbertc(grand_total,2);
		}
		else{
			   var doublehemmprice = document.getElementById("doublehemmedfinishprice").value;
			   document.getElementById("div_doublehemmedfinishprice").innerHTML = "";
			   document.getElementById("doublehemmedfinishprice").value = "";
			   grand_total= Number(grand_total)-Number(doublehemmprice);
			   roundNumbertc(grand_total,2);
		}
	}
	
}


//============ Lamination

function checklimitationprice()
{ 
   var str;
   if(document.getElementById('glossylamination').checked == true){
	 str = 'Yes';   
   }
   else{
	 str = 'No';   
   }
 
   var id = document.getElementById('categoryid').value; 
   var subcatid = document.getElementById("sub_cat_id").value;
   if(document.getElementById('nolamination'))
   {
   		if(id == 32 || id == 31 || id == 44){
			 var grand_total = document.getElementById('total').value;
			 var height = document.getElementById('size_h').value; 
			 var width = document.getElementById('size_w').value; 
			 var qty = document.getElementById('qty').value; 
			 var limitationprice = document.getElementById('liminationprice').value; 
 			 if(document.getElementById('major'))
			 {
 			 	var major = document.getElementById('major').value; 
			 	if(major == 'in' || major == 'In'){
				    width = width/12;
				    height = height/12;
				}
			 }
 			 if(str != 'No' && (limitationprice == 0 || limitationprice == '')){
				 limitationprice = width*height*1.09*qty;
				 limitationprice = parseFloat(limitationprice).toFixed(2);
				 document.getElementById("limitationpricedisplay").innerHTML = "$"+limitationprice + " <span style='font-size:10px;color:red;'> (Additional cost) </span>";
				 document.getElementById("liminationprice").value = limitationprice;
				 grand_total= Number(grand_total)+Number(limitationprice);
				 roundNumbertc(grand_total,2);
				 
			 }
			 else if(str == 'No'){
				 document.getElementById("limitationpricedisplay").innerHTML = "";
			     document.getElementById("liminationprice").value = "";
			     grand_total= Number(grand_total)-Number(limitationprice);
			     roundNumbertc(grand_total,2);
			 }
   		}
   }
}

//========================= Material ==================
function getMaterialoptions() 
{
	var catid = document.getElementById("sub_cat_id").value;

 	if(catid == '16' || catid == '35' || catid == '11' || catid == '12' || catid == '28' || catid == '17' || catid == '15' || catid == '80' || catid == '81' || catid == '82')
	{
 		var prd = 'Decals/Posters';
	}
	else
	{
		var prd = document.getElementById("producttype").value; 
	}
   	var apply = document.getElementById("apply_at").value;
 	var grand_total = document.getElementById('total').value;
 	var base_total = document.getElementById('base_total').value;
  	if(catid != 11 && catid != 35 && catid != 12 && catid != 17 && catid != 15 && catid != 80) {
		var material = document.getElementById("material").value;
		var obj = document.getElementById("material");
	}
	
	// Inside validation for Size validation
	var applicationtype;
	if(document.getElementById('producttype'))
	{
	    var applicationtype = document.getElementById("producttype").value; 
	}
	else
	{
		var applicationtype = apply; 
	}
  	if((catid == '15' || catid == '17' || catid == '16' || catid == '28') && (applicationtype == 'Inside of the glass surface' || (applicationtype == 'Inside' && apply == 'Decals')))
	{
		var height = document.getElementById('size_h').value;
		var width = document.getElementById('size_w').value;
		var major = 'ft';
		if(document.getElementById('major')){
			 major = document.getElementById('major').value
		}
 		if(major == 'in' || major == 'In'){
		   	height = height/12;
			height = parseFloat(height).toFixed(2);
			width = width/12;
			width = parseFloat(width).toFixed(2);
		}
			//var sqft = height*width;
			
			if(catid == '17' || catid == '16' || catid == '28')
			{
				if((height >= 3 && width >= 3 )){
					  if(document.getElementById('errorwindowcling'))
					 {  
					    document.getElementById('errorwindowcling').innerHTML = "Please enter size minimum 3' wide x 3' height"; 
						document.getElementById('errorwindowcling').style.display = 'none';
					 }
					 if(document.getElementById('savetocart')){
						document.getElementById('savetocart').disabled=false;
						document.getElementById('savetocart').style.opacity = '';
					}
					if(document.getElementById('addtocart')){
						document.getElementById('addtocart').disabled=false;
						document.getElementById('addtocart').style.opacity = '';
					}
   			}else{
 				
				 if(document.getElementById('errorwindowcling'))
				{
					 document.getElementById('errorwindowcling').innerHTML = "Please enter size minimum 3' wide x 3' height";	
					document.getElementById('errorwindowcling').style.display = 'block';
				}
				if(document.getElementById('savetocart')){
					document.getElementById('savetocart').disabled=true;
					document.getElementById('savetocart').style.opacity = 0.5;
				}
				if(document.getElementById('addtocart')){
					document.getElementById('addtocart').disabled=true;
					document.getElementById('addtocart').style.opacity = 0.5;
				}
				return false;
 			}
 			}
			else if(catid == '15')
			{
			   if((height <= 2 && width <= 3) || (height <=3 && width <= 2))
 			    {
				  if(document.getElementById('errorwindowcling'))
				 {  
				    document.getElementById('errorwindowcling').innerHTML = "Please enter size upto 2' wide x 3' height OR 3' wide x 2' height";
					document.getElementById('errorwindowcling').style.display = 'none';
				 }
				 if(document.getElementById('savetocart')){
					document.getElementById('savetocart').disabled=false;
					document.getElementById('savetocart').style.opacity = '';
				}
				  if(document.getElementById('addtocart')){
					document.getElementById('addtocart').disabled=false;
					document.getElementById('addtocart').style.opacity = '';
				}
		       }
			   else{
 				
				 if(document.getElementById('errorwindowcling'))
				{
					document.getElementById('errorwindowcling').style.display = 'block';
				}
				if(document.getElementById('savetocart')){
					document.getElementById('savetocart').disabled=true;
					document.getElementById('savetocart').style.opacity = 0.5;
				}
				if(document.getElementById('addtocart')){
					document.getElementById('addtocart').disabled=true;
					document.getElementById('addtocart').style.opacity = 0.5;
				}
				return false;
 			}
			}
			
		 
	}
	else
	{
		 if(document.getElementById('errorwindowcling'))
		 {  
 			document.getElementById('errorwindowcling').style.display = 'none';
		 }
		 if(document.getElementById('savetocart')){
			document.getElementById('savetocart').disabled=false;
			document.getElementById('savetocart').style.opacity = '';
		}
		if(document.getElementById('addtocart')){
			document.getElementById('addtocart').disabled=false;
			document.getElementById('addtocart').style.opacity = '';
		}
	}
	
   	if(prd == "Decals/Posters") {
		 	
		  if(apply == 'Static cling'){
			  var lamiyes = document.getElementById('glossylamination').checked; 
			 // if(lamiyes == true){
			   
			   document.getElementById('glossylamination').checked = false; 
			   document.getElementById('glossylamination').disabled = true;
			   document.getElementById('nolamination').checked = true;
			   document.getElementById('nolamination').disabled = true;
			   //checklimitationprice();
			     var limitationprice = document.getElementById('liminationprice').value; 
				 document.getElementById("limitationpricedisplay").innerHTML = "";
				 document.getElementById("liminationprice").value = "";
				 grand_total= Number(grand_total)-Number(limitationprice);
				 roundNumbertc(grand_total,2);
 		  // } 
		  }
		  else{
			   var diecut="nofinish";
			   
			   if(document.getElementById('finishoptions')){
					diecut = document.getElementById('finishoptions').value;
			   }
			   
			   if(diecut != 'Die-cut'){
				   if(document.getElementById('glossylamination')){
					  document.getElementById('glossylamination').disabled = false;
					  document.getElementById('nolamination').disabled = false;
				   }
			   }
		  }
  		if(apply == "Inside of the glass" || apply == "Inside") {
			if(catid != 11  && catid != 12) {
				  if(catid != 35 && catid != 17 && catid != 15 && catid != 80)
				  {
						addOption("Clear");
				  }
				 var applyprice = 0; 
				if(catid == 11 || catid == 12){
					applyprice = document.getElementById('applyatoptions').value;
				}
  				if(catid == 11 || catid == 12) {
					document.getElementById("div_applyatprice").innerHTML = "";
				}
				if(document.getElementById("applyatoptions")){
					document.getElementById("applyatoptions").value = "";
				}
				grand_total= Number(grand_total)-Number(applyprice);
				roundNumbertc(grand_total,2);
			}
			else {
				
				 var applyprice = 0; 
				if(catid == 11 || catid == 12){
					applyprice = document.getElementById('applyatoptions').value;
				}
 				if(catid == 11 || catid == 12) {
					document.getElementById("div_applyatprice").innerHTML = "";
				}
				if(document.getElementById("applyatoptions")){
					document.getElementById("applyatoptions").value = "";
				}
				grand_total= Number(grand_total)-Number(applyprice);
				roundNumbertc(grand_total,2);
				
				var applyprice = base_total * 0.30 ;
				
				applyprice = parseFloat(applyprice).toFixed(2);
 				if(catid == 11 || catid == 12) {
					document.getElementById("div_applyatprice").innerHTML = "$"+applyprice;
				}
				if(document.getElementById("applyatoptions")){
					document.getElementById("applyatoptions").value = applyprice;
				}
				grand_total= Number(grand_total)+Number(applyprice);
				roundNumbertc(grand_total,2);
			}
		}
		else if(apply == "Outside of the glass" || apply == "Outside") {
			  if(catid != 11 && catid != 35 && catid != 12 && catid != 17 && catid != 15 && catid != 80)  {
				addOption("Clear,White");
			}
			
			 var applyprice = 0; 
				if(catid == 11 || catid == 12){
					applyprice = document.getElementById('applyatoptions').value;
				}
			if(catid == 11 || catid == 12) {
				document.getElementById("div_applyatprice").innerHTML = "";
			}
			if(document.getElementById("applyatoptions")){
					document.getElementById("applyatoptions").value = "";
				}
			grand_total= Number(grand_total)-Number(applyprice);
			roundNumbertc(grand_total,2);
		}
	}
	else if(prd == "Clings") {
		
 		/* Lamination disable */
		 if(document.getElementById('glossylamination')){
           var lamino = document.getElementById('nolamination').checked; 			 
		   var lamiyes = document.getElementById('glossylamination').checked; 		
 		   //if(lamiyes == true){
			   document.getElementById('glossylamination').checked = false; 
			   document.getElementById('glossylamination').disabled = true;
			   document.getElementById('nolamination').checked = true;
			   document.getElementById('nolamination').disabled = true;
			   //checklimitationprice();
			     var limitationprice = document.getElementById('liminationprice').value; 
				 document.getElementById("limitationpricedisplay").innerHTML = "";
				 document.getElementById("liminationprice").value = "";
				 grand_total= Number(grand_total)-Number(limitationprice);
				 roundNumbertc(grand_total,2);
 		  // }
		   
		 }
		/*Lamination disable*/
		if(apply == "Inside") {
			if(catid != 11 && catid != 17 && catid != 15 && catid != 80) {
				 if(catid == 32){
					addOption("Static Clear"); 
				 }
				 else{
					addOption("Clear");
				 }
				 var applyprice = 0; 
				if(catid == 11 || catid == 12){
					applyprice = document.getElementById('applyatoptions').value;
				}
				if(catid == 11) {
					document.getElementById("div_applyatprice").innerHTML = "";
				}
				if(document.getElementById("applyatoptions")){
					document.getElementById("applyatoptions").value = "";
				}
				grand_total= Number(grand_total)-Number(applyprice);
				roundNumbertc(grand_total,2);
			}
			else {
				
				 var applyprice = 0; 
				if(catid == 11 || catid == 12){
					applyprice = document.getElementById('applyatoptions').value;
				}
				if(catid == 11) {
					document.getElementById("div_applyatprice").innerHTML = "";
				}
				if(document.getElementById("applyatoptions")){
					document.getElementById("applyatoptions").value = "";
				}
				grand_total= Number(grand_total)-Number(applyprice);
				roundNumbertc(grand_total,2);
				
				var applyprice = base_total * 0.30 ;
				applyprice = parseFloat(applyprice).toFixed(2);
				if(catid == 11) {
					document.getElementById("div_applyatprice").innerHTML = "$"+applyprice;
				}
				if(document.getElementById("applyatoptions")){
					document.getElementById("applyatoptions").value = applyprice;
				}
 				grand_total= Number(grand_total)+Number(applyprice);
				roundNumbertc(grand_total,2);	
			}
		}
		else if(apply == "Outside") {
			if(catid != 11 && catid != 17 && catid != 15 && catid != 80) {
				 if(catid == 32){
					addOption("Static White"); 
				 }
				 else{ 
				  addOption("White");
				 }
			}
			 var applyprice = 0; 
				if(catid == 11 || catid == 12){
					applyprice = document.getElementById('applyatoptions').value;
				}
			if(catid == 11) {
				document.getElementById("div_applyatprice").innerHTML = "";
			}
			if(document.getElementById("applyatoptions")){
					document.getElementById("applyatoptions").value = "";
				}
			grand_total= Number(grand_total)-Number(applyprice);
			roundNumbertc(grand_total,2);
			
		}
	}
	
	if(catid == 28){
		if(apply == 'Decals'){
			document.getElementById('decalsclingmedia').innerHTML = 'White Vinyl (Opaque)';	
			document.getElementById('material').value = 'White Vinyl (Opaque)';
		}
		else{
			document.getElementById('decalsclingmedia').innerHTML = 'Static White Vinyl (Opaque)';	
			document.getElementById('material').value = 'Static White Vinyl (Opaque)';
		}
	}
	else if(catid == 82){
		if(apply == 'Decals'){
			document.getElementById('decalsclingmedia').innerHTML = 'Clear Vinyl';	
			document.getElementById('material').value = 'Clear Vinyl';
		}
		else{
			document.getElementById('decalsclingmedia').innerHTML = 'Static clear Vinyl';	
			document.getElementById('material').value = 'Static clear Vinyl';
		}
	}
   
}

function removeOption(){
  	var ddl = document.getElementById('material');
  	var i;
  	for (i = ddl.length - 1; i>=0; i--) {
		ddl.remove(i);
  	}
}
function addOption(opt){
  	removeOption();
 	var ddl = document.getElementById('material');
	var option = opt.split(",");
	var k;
	for(k=0; k<option.length; k++) {
		var elOptNew = document.createElement('option');	
		elOptNew.text = option[k];
    	elOptNew.value = option[k];
    	try {
        	ddl.add(elOptNew, null); // standards compliant; doesn't work in IE
      	}
      	catch(ex) {
       		ddl.add(elOptNew); // IE only
      	}	
	}
}


function addfinishOption(opt){
	if(document.getElementById('finishoptions')){
		var prevfinish = document.getElementById('finishoptions').value; 
		removefinishOption();
		var ddl = document.getElementById('finishoptions');
		var option = opt.split(",");
		var k;
		for(k=0; k<option.length; k++) {
			var elOptNew = document.createElement('option');	
			elOptNew.text = option[k];
			elOptNew.value = option[k];
			if(option[k] == prevfinish){
				elOptNew.setAttribute("selected","select");
			}
			
			try {
				ddl.add(elOptNew, null); // standards compliant; doesn't work in IE
			}
			catch(ex) {
				ddl.add(elOptNew); // IE only
			}	
		}
	}
}

function removefinishOption(){
	
  	var ddl = document.getElementById('finishoptions');
  	var i;
  	for (i = ddl.length - 1; i>=0; i--) {
		ddl.remove(i);
  	}
}

//=====================================================

function numallow(evt)
{
		 var charCode = (evt.which) ? evt.which : evt.keyCode
		 if (charCode > 31 && (charCode < 43 || charCode > 57 || charCode==8 || charCode==127))
		 {
			return false;
		 }
		else
		{
			return true;
		}
		
}

//=======================================

function getvehiclemagneticprice()
{
	var size = document.getElementById('vehicle_magnetic_size').value;
	var vehiclesize = size.split("x");
	var width = vehiclesize[0];
	var height = vehiclesize[1];
    //alert(width + "  " + height);
	document.getElementById('size_w').value = width;
	document.getElementById('size_h').value = height;
}

function getcustomtablecoversize()
{
	var size = document.getElementById('customtablecover_size').value;
	var customtable = size.split("x");
	var width = customtable[1];
	var height = customtable[0];
 	document.getElementById('size_w').value = width;
	document.getElementById('size_h').value = height;
}

function getretractablebannerstandsize()
{
	var size = document.getElementById('retractable_size').value;
	var customtable = size.split("x");
	var width = customtable[0];
	var height = customtable[1];
 	document.getElementById('size_w').value = width;
	document.getElementById('size_h').value = height;
}

function getxbannestandsize()
{
	var size = document.getElementById('xbannerstantsize').value;
	var customtable = size.split("x");
	var width = customtable[0];
	var height = customtable[1];
 	document.getElementById('size_w').value = width;
	document.getElementById('size_h').value = height;
}

function getbannerstandprice()
{
	var size = document.getElementById('bannerstand_size').value;
	var standsize = size.split("x");
	var width = standsize[0];
	var height = standsize[1];
    //alert(width + "  " + height);
	document.getElementById('size_w').value = width;
	document.getElementById('size_h').value = height;
}


function checkcanopiesprice()
{
	var backwallyes = document.getElementById('backwallgraphicsyes').checked;
	var backwallno = document.getElementById('backwallgraphicsno').checked;
	var backwallprice = document.getElementById('backwallprice').value;
	var totalprice = document.getElementById('total').value;
	var backwallfold = document.getElementById('backwall_fold').value;
    
	var halfwallyes = document.getElementById('halfwallgraphicsyes').checked;
	var halfwallno = document.getElementById('halfwallgraphicsno').checked;
	var halfwallprice = document.getElementById('halfwallprice').value;
	var halfwallfold = document.getElementById('halfwall_fold').value;
	
	var railforbackwall = document.getElementById('rail_back_wall').value;
	var flag_holder = document.getElementById('flag_holder').value;
	var travelbag = document.getElementById('travelbag').value;
	var canopies_qty = '1';
	<!-- Back Wall  -->
    if(document.getElementById('backwallgraphicsyes').checked == true)
	{
	   document.getElementById('backwall_graphics').style.display = "";
	   if(backwallfold == "dual")
	   {
		  totalprice = Number(totalprice)-Number(document.getElementById('backwallprice').value);  
		  document.getElementById('backwallprice').value = '365';
		  document.getElementById('backwallfoldprice').innerHTML = '$365';
	   }
	   else
	   {
		  totalprice = Number(totalprice)-Number(document.getElementById('backwallprice').value);    
	   	  document.getElementById('backwallprice').value = '264';
		  document.getElementById('backwallfoldprice').innerHTML = '$264';
	   }
	   totalprice = Number(totalprice)+Number(document.getElementById('backwallprice').value);
	   document.getElementById('total').value = totalprice;
   
	}
	else
	{
	   document.getElementById('backwall_graphics').style.display = "none";	
	   totalprice = Number(totalprice)-Number(document.getElementById('backwallprice').value);
	   document.getElementById('backwallprice').value = 0;
	   document.getElementById('total').value = totalprice;
	}
	<!-- End Back Wall  -->
	
	<!-- Half Wall  -->
	if(document.getElementById('halfwallgraphicsyes').checked == true)
	{
	     document.getElementById('halfwall_graphics').style.display = "";
	     if(halfwallfold == "dual")
	    {
		  totalprice = Number(totalprice)-Number(document.getElementById('halfwallprice').value);  
		  document.getElementById('halfwallprice').value = '209';
		  document.getElementById('halfwallfoldprice').innerHTML = '$209';
	    }
	    else
	    {
		  totalprice = Number(totalprice)-Number(document.getElementById('halfwallprice').value);    
		  document.getElementById('halfwallprice').value = '159';
		  document.getElementById('halfwallfoldprice').innerHTML = '$159';
	    }
	    totalprice = Number(totalprice)+Number(document.getElementById('halfwallprice').value);
	    document.getElementById('total').value = totalprice; 
	}
	else
	{
		document.getElementById('halfwall_graphics').style.display = "none";	
		totalprice = Number(totalprice)-Number(document.getElementById('halfwallprice').value);
		document.getElementById('halfwallprice').value = 0;
		document.getElementById('total').value = totalprice; 
	}
	<!-- End Half Wall  -->
	
	<!-- rail_back_wall -->
	if(railforbackwall == "yes")
	{
		 document.getElementById('railbackwallprice').style.display = "";
		 if(document.getElementById('railforbackwall').value == 0)
		 {
			 document.getElementById('railforbackwall').value = '34';
			 document.getElementById('railbackwallprice').innerHTML = '$34';
			 totalprice = Number(totalprice)+Number(document.getElementById('railforbackwall').value);
			 document.getElementById('total').value = totalprice;
		 }
	}
	else
	{		 
		 document.getElementById('railbackwallprice').style.display = "none";  
		 totalprice = Number(totalprice)-Number(document.getElementById('railforbackwall').value);
 		 document.getElementById('railforbackwall').value = '0';
		 document.getElementById('total').value = totalprice;
	}
	<!-- end rail_back_wall -->
	
	<!-- Flag Holder -->
	if(flag_holder == "yes")
	{
		 document.getElementById('flagholderpricediv').style.display = "";
		 if(document.getElementById('flagholderprice').value == 0)
		 {
			 document.getElementById('flagholderprice').value = '29';
			 document.getElementById('flagholderpricediv').innerHTML = '$29';
			 totalprice = Number(totalprice)+Number(document.getElementById('flagholderprice').value);
			 document.getElementById('total').value = totalprice;
		 }
	}
	else
	{		 
		 document.getElementById('flagholderpricediv').style.display = "none";  
		 totalprice = Number(totalprice)-Number(document.getElementById('flagholderprice').value);
 		 document.getElementById('flagholderprice').value = '0';
		 document.getElementById('total').value = totalprice;
	}
	<!-- End of Flag Holder -->
	
	<!-- Travel Bag -->
	if(travelbag == "yes")
	{
		 document.getElementById('travelbagpricediv').style.display = "";
		 if(document.getElementById('travelbagprice').value == 0)
		 {
			 document.getElementById('travelbagprice').value = '59';
			 document.getElementById('travelbagpricediv').innerHTML = '$59';
			 totalprice = Number(totalprice)+Number(document.getElementById('travelbagprice').value);
			 document.getElementById('total').value = totalprice;
		 }
	}
	else
	{		 
		 document.getElementById('travelbagpricediv').style.display = "none";  
		 totalprice = Number(totalprice)-Number(document.getElementById('travelbagprice').value);
 		 document.getElementById('travelbagprice').value = '0';
		 document.getElementById('total').value = totalprice;
	}
	<!-- Travel Bag -->
	totalprice = totalprice*canopies_qty;
	var totalfinalprice = parseFloat(totalprice).toFixed(2)
	document.getElementById('total_display').innerHTML =  '$'+totalfinalprice;
}

//===================

function flagpriceupdate(ad)
{
 	//ad = For Admin we have pass arg
	var fold = document.getElementById('flag_fold').value;
	var sizew = document.getElementById('size_w').value;
	var sizeh = document.getElementById('size_h').value;
	var subcatid = document.getElementById('sub_cat_id').value;
	var qty = document.getElementById('qty').value;
	var base = document.getElementById('flag_base').value;
	 
	if(base == 'Cross Base'){
			 document.getElementById('crossbasewater').style.display = 'block';
 			 var waterbags = document.getElementById('waterbags');
 			 if(waterbags.checked == true){
 				document.getElementById('waterbagsprice').style.display = 'inline';
			 }
			 else{
				 document.getElementById('waterbagsprice').style.display = 'none';
			 }
		 }
		 else{
			 document.getElementById('crossbasewater').style.display = 'none';
			 if(document.getElementById('waterbags').checked = true){
				 document.getElementById('waterbags').checked = false;
			 }
			 document.getElementById('waterbagsprice').style.display = 'none';
		 }
	
	 if(document.getElementById('waterbags').checked == true){
			 	var waterbaseselect = 'yes';
		 }
		 else{
			    var waterbaseselect = 'no';
		 }
		 
	if(subcatid == 73)
	{
		var flagtype = 1;
	}
	else
	{ 
	   var flagtype = 2; 
	}
	var req1 = getXMLHTTP();
	if(ad == '1'){
		var contenturl = 'updateflagcontent.php?sizew='+sizew+'&sizeh='+sizeh+'&fold='+fold+'&flagtype='+flagtype+'&base='+base+'&waterbase='+waterbaseselect;
	}
	else{
		var contenturl = '../updateflagcontent.php?sizew='+sizew+'&sizeh='+sizeh+'&fold='+fold+'&flagtype='+flagtype+'&base='+base+'&waterbase='+waterbaseselect;
	}
 	if(req1) 
	{ 
				req1.onreadystatechange = function()
				{ 
					if(req1.readyState == 4) 
					{			
						if(req1.status == 200)
						{	
						    var price = req1.responseText;
 							price = price * qty;
 							if(base == 'Cross Base'){
								 var flagbaseprice = 49*qty;
								 flagbaseprice = parseFloat(flagbaseprice).toFixed(2);
								 price = Number(price) + Number(flagbaseprice);
								 price = parseFloat(price).toFixed(2);
								 document.getElementById('flag_base_pricedisplay').innerHTML = '(Additional $'+flagbaseprice+')';
							}
							else{
								/*var flagbaseprice = 5.99*qty;
								flagbaseprice = parseFloat(flagbaseprice).toFixed(2);
								price = Number(price) + Number(flagbaseprice);
								price = parseFloat(price).toFixed(2);*/
								document.getElementById('flag_base_pricedisplay').innerHTML = '';
							}
							document.getElementById('price_show').value = price;
							document.getElementById('flag_base_price').value = flagbaseprice;
							
							
							var addcost = 4.99*qty;
							addcost = parseFloat(addcost).toFixed(2);
							document.getElementById('waterbagsprice').innerHTML = '(Additional $'+addcost+')';
							document.getElementById('waterbagprice').value = addcost;
							roundNumbertc(price,2);
						}				
					}				
				 }			
				 req1.open("GET", contenturl, true);
				 req1.send(null);
	 }
}

function updateflagcontent(flagtype)
 {
  	  if(flagtype == 1)
	 {
		 var size1 = document.getElementById('flag_size').value;
		 var size2 = size1.split('$#$');
		 var size = size2[1];
		 var model = size2[0].split('x');
		 document.getElementById('size_w').value = model[1];
		 document.getElementById('size_h').value = model[0];
 		 var base = document.getElementById('flag_base').value;
  		 if(base == 'Cross Base'){
			 document.getElementById('crossbasewater').style.display = 'block';
 			 var waterbags = document.getElementById('waterbags');
 			 if(waterbags.checked == true){
 				document.getElementById('waterbagsprice').style.display = 'inline';
			 }
			 else{
				 document.getElementById('waterbagsprice').style.display = 'none';
			 }
		 }
		 else{
			 document.getElementById('crossbasewater').style.display = 'none';
			 if(document.getElementById('waterbags').checked = true){
				 document.getElementById('waterbags').checked = false;
			 }
			 document.getElementById('waterbagsprice').style.display = 'none';
		 }
		 
		 var fold = document.getElementById('flag_fold').value;
		 var qty = document.getElementById('flag_qty').value;
		 if(document.getElementById('waterbags').checked == true){
			 	var waterbaseselect = 'yes';
		 }
		 else{
			    var waterbaseselect = 'no';
		 }
		 
	}
	else
	{
	     var size1 = document.getElementById('flag_teardrop_size').value;
		 var size2 = size1.split('$#$');
		 var size = size2[1];
		 var model = size2[0].split('x');
		 document.getElementById('size_teardrop_w').value = model[1];
		 document.getElementById('size_teardrop_h').value = model[0];
		 var fold = document.getElementById('flag_teardrop_fold').value;
		 var qty = document.getElementById('flag_teardrop_qty').value;
		 
		 var base = document.getElementById('flag_teardrop_base').value;
 		 if(base == 'Cross Base'){
			 document.getElementById('crossbasewater_teardrop').style.display = 'block';
 			 var waterbags = document.getElementById('waterbags_teardrop');
 			 if(waterbags.checked == true){
 				document.getElementById('waterbagsprice_teardrop').style.display = 'inline';
			 }
			 else{
				 document.getElementById('waterbagsprice_teardrop').style.display = 'none';
			 }
		 }
		 else{
			 document.getElementById('crossbasewater_teardrop').style.display = 'none';
			 if(document.getElementById('waterbags_teardrop').checked = true){
				 document.getElementById('waterbags_teardrop').checked = false;
			 }
			 document.getElementById('waterbagsprice_teardrop').style.display = 'none';
		 }
		 
 		 if(document.getElementById('waterbags_teardrop').checked == true){
			 	var waterbaseselect = 'yes';
		 }
		 else{
			    var waterbaseselect = 'no';
		 }
		 
	}
 	 var req1 = getXMLHTTP();
	 var contenturl = 'updateflagcontent.php?id='+size+'&fold='+fold+'&base='+base+'&waterbase='+waterbaseselect;
 	 if(req1) 
	 { 
				req1.onreadystatechange = function()
				{ 
					if(req1.readyState == 4) 
					{			
						if(req1.status == 200)
						{
						    var content = req1.responseText.split('$:$');
							var price = content[0];
 							price = price*qty;
							price = parseFloat(price).toFixed(2);
							var catcontent = content[1];
							if(flagtype == 1)
							{
								document.getElementById('flag_content').innerHTML = catcontent;
								if(base == 'Cross Base'){
									 var flagbaseprice = 49*qty;
									 flagbaseprice = parseFloat(flagbaseprice).toFixed(2);
									 price = Number(price) + Number(flagbaseprice);
									 price = parseFloat(price).toFixed(2);
									 document.getElementById('flag_base_pricedisplay').innerHTML = '(Additional $'+flagbaseprice+')'; 
								}
								else{
									/*var flagbaseprice = 5.99*qty;
									flagbaseprice = parseFloat(flagbaseprice).toFixed(2);
									price = Number(price) + Number(flagbaseprice);
									price = parseFloat(price).toFixed(2);*/
									document.getElementById('flag_base_pricedisplay').innerHTML = ''; 
								}
								document.getElementById('flag_price').innerHTML = price; 
								document.getElementById('total').value = price;
								
								
 									var addcost = 4.99*qty;
									addcost = parseFloat(addcost).toFixed(2);
									document.getElementById('waterbagsprice').innerHTML = '(Additional $'+addcost+')';
								 
							}
							else
							{
							    if(base == 'Cross Base'){
									 var flagbaseprice = 49*qty;
									 flagbaseprice = parseFloat(flagbaseprice).toFixed(2);
									 price = Number(price) + Number(flagbaseprice);
									 price = parseFloat(price).toFixed(2);
									 document.getElementById('teardropflag_base_pricedisplay').innerHTML = '(Additional $'+flagbaseprice+')';
								}
								else{
									/*var flagbaseprice = 5.99*qty;
									flagbaseprice = parseFloat(flagbaseprice).toFixed(2);
									price = Number(price) + Number(flagbaseprice);
									price = parseFloat(price).toFixed(2);*/
									document.getElementById('teardropflag_base_pricedisplay').innerHTML = '';
								}
								document.getElementById('teardrop_flag_content').innerHTML = catcontent;
								document.getElementById('teardrop_flag_price').innerHTML = price; 
								document.getElementById('total_teardrop').value = price;
								 
								var addcost = 4.99*qty;
								addcost = parseFloat(addcost).toFixed(2);
								document.getElementById('waterbagsprice_teardrop').innerHTML = '(Additional $'+addcost+')';
							}
						} 				
					}				
				 }			
				 req1.open("GET", contenturl, true);
				 req1.send(null);
	 }
 }
 
 
function isFloat(value) 
{
	if (/\./.test(value)) 
	{
		return true;
	} 
	else 
	{
		return false;
	}
}


function activepowerreviewtab(cururl,str)
{	
   if(str == 'Reviews')
   {
		document.getElementById('subcatcontenttab1').className = 'tabbing_content dis_none clearfix';
		document.getElementById('subcatcontenttab2').className = 'tabbing_content dis_block clearfix';
		document.getElementById('subcatcontenttab3').className = 'tabbing_content dis_none clearfix';
		if(document.getElementById('subcatcontenttab4'))
		{
			document.getElementById('subcatcontenttab4').className = 'tabbing_content dis_none clearfix';
			document.getElementById('subcattab4').className = '';
		}
		document.getElementById('subcattab1').className = '';
		document.getElementById('subcattab2').className = 'active';
		document.getElementById('subcattab3').className = '';
		
		window.location.href=cururl + "#power-review"  
   }
   else{
	    document.getElementById('subcatcontenttab1').className = 'tabbing_content dis_none clearfix';
		document.getElementById('subcatcontenttab2').className = 'tabbing_content dis_none clearfix';
		document.getElementById('subcatcontenttab3').className = 'tabbing_content dis_none clearfix';
		if(document.getElementById('subcatcontenttab4'))
		{
			document.getElementById('subcatcontenttab4').className = 'tabbing_content dis_block clearfix';
			document.getElementById('subcattab4').className = 'active';
		}
		document.getElementById('subcattab1').className = '';
		document.getElementById('subcattab2').className = '';
		document.getElementById('subcattab3').className = '';
		
		window.location.href=cururl + "#installation-steps"  
   }
}

// TopLine Banner frame
function gettoplineframesize(){
	var toplineframesize = document.getElementById('topline_framesize').value;
 	var framebase = document.getElementById('toplineframe_base').value;
	var shortparapet = document.getElementById('toplineframe_shortparapet').checked;
	var trackupgrade = document.getElementById('toplineframe_exttrackupgrade').checked;
	var flagkit = document.getElementById('toplineframe_flagkit').checked;
	var bannerid = document.getElementById('bannerid').value;
	var subcatid = document.getElementById('product').value;
	var qty = document.getElementById('qty').value;
	
	
	var URL = "gettoplineframesize.php?base="+framebase+'&bannerid='+bannerid+'&subcatid='+subcatid+'&size='+toplineframesize+'&changesize=size';
    var req1 = getXMLHTTP(); 
	if(req1) 
	{ 
				req1.onreadystatechange = function()
				{ 
					if(req1.readyState == 4) 
					{			
						if(req1.status == 200)
						{	
						    var box = req1.responseText.split('#$#'); 
							var selectbox = box[0];
							var price = box[1];
							var width = box[2];
							var height = box[3];
						    document.getElementById('framesize').innerHTML = selectbox;
							document.getElementById('toplineframe_price').innerHTML = price;
							document.getElementById('total').value = price;
							document.getElementById('size_w').value = width;
							document.getElementById('size_h').value = height;
							
							gettoplineframeprice();
						}				
					}				
				 }			
				 req1.open("GET", URL, true);
				 req1.send(null);
	 }
}


function gettoplineframeprice(){
	var toplineframesize = document.getElementById('topline_framesize').value;
 	var framebase = document.getElementById('toplineframe_base').value;
	var shortparapet = document.getElementById('toplineframe_shortparapet').checked;
	var trackupgrade = document.getElementById('toplineframe_exttrackupgrade').checked;
	var flagkit = document.getElementById('toplineframe_flagkit').checked;
	var bannerid = document.getElementById('bannerid').value;
	var subcatid = document.getElementById('product').value;
	var qty = document.getElementById('qty').value;
 	
	var URL = "gettoplineframesize.php?base="+framebase+'&bannerid='+bannerid+'&subcatid='+subcatid+'&size='+toplineframesize;
	document.getElementById('shortparapet_price').value = 0;
 	document.getElementById('exttrackupgrade_price').value = 0;
	document.getElementById('flagkit_price').value = 0;
 	
    var req1 = getXMLHTTP(); 
	if(req1) 
	{ 
				req1.onreadystatechange = function()
				{ 
					if(req1.readyState == 4) 
					{			
						if(req1.status == 200)
						{
							var price = req1.responseText;
							price = price*qty;
							price = parseFloat(price).toFixed(2);
  							var framesizes = toplineframesize.split('x'); 
							var height = framesizes[1];
							var width = framesizes[0];
							document.getElementById('size_w').value = width;
							document.getElementById('size_h').value = height;
							//document.getElementById('toplineframe_price').innerHTML = price;
							//document.getElementById('total').value = price; 
							//alert(shortparapet);
							if(shortparapet == true)
							{
 								var shortparapetval;	
 							    if(framebase == 'dual'){
									shortparapetval = 59*qty;	
								}	
								else{
									shortparapetval = 49*qty; 
								}
								if(document.getElementById('shortparapet_price').value == '' || document.getElementById('shortparapet_price').value == '0')
								{
									shortparapetval = parseFloat(shortparapetval).toFixed(2);
									document.getElementById('toplineframe_shortparapet_dis').innerHTML = '(Additional $'+ shortparapetval +')';
									document.getElementById('shortparapet_price').value = shortparapetval;
									//var price = document.getElementById('total').value;
									price = Number(price)+Number(shortparapetval);
									price = parseFloat(price).toFixed(2);
								}
							}
							else{
								var shortparapet_price = document.getElementById('shortparapet_price').value;	
								//alert(shortparapet_price);
								if(shortparapet_price != '' && shortparapet_price != '0'){
										var price = document.getElementById('total').value;
									    document.getElementById('toplineframe_shortparapet_dis').innerHTML = '';
										document.getElementById('shortparapet_price').value = 0;
										price = Number(price)-Number(shortparapet_price);
								        price = parseFloat(price).toFixed(2);
								}
								if(shortparapet_price == '0'){
									document.getElementById('toplineframe_shortparapet_dis').innerHTML = '';
								}
							}
							
							
							if(trackupgrade == true)
							{
 								var trackupgradeval;	
							    if(framebase == 'dual'){
									trackupgradeval = 99*qty;	
								}	
								else{
									trackupgradeval = 79*qty; 
								}
								if(document.getElementById('exttrackupgrade_price').value == '' || document.getElementById('exttrackupgrade_price').value == '0')
								{
									trackupgradeval = parseFloat(trackupgradeval).toFixed(2);
									document.getElementById('toplineframe_exttrackupgrade_dis').innerHTML = '(Additional $'+ trackupgradeval +')';
									document.getElementById('exttrackupgrade_price').value = trackupgradeval;
									//var price = document.getElementById('total').value;
									price = Number(price)+Number(trackupgradeval);
									price = parseFloat(price).toFixed(2);
								}
							}
							else{
								var trackupgradeval_price = document.getElementById('exttrackupgrade_price').value;	
 								if(trackupgradeval_price != '' && trackupgradeval_price != '0'){
										var price = document.getElementById('total').value;
									    document.getElementById('toplineframe_exttrackupgrade_dis').innerHTML = '';
										document.getElementById('exttrackupgrade_price').value = 0;
										price = Number(price)-Number(trackupgradeval_price);
								        price = parseFloat(price).toFixed(2);
								}
								if(trackupgradeval_price == '0'){
									document.getElementById('toplineframe_exttrackupgrade_dis').innerHTML = '';
								}
							}
							
							
							if(flagkit == true)
							{
 								var flagkitval;	
							    if(framebase == 'dual'){
									flagkitval = 26*qty;	
								}	
								else{
									var width1 = toplineframesize.split("x"); 	
									width1 = width1[0];
									if(width1 >= 10){
										flagkitval = 19.5*qty; 	
									}
									else{
										flagkitval = 13*qty;  
									}
									
								}
								//alert(flagkitval);
								if(document.getElementById('flagkit_price').value == '' || document.getElementById('flagkit_price').value == '0')
								{
									flagkitval = parseFloat(flagkitval).toFixed(2);
									document.getElementById('toplineframe_flagkit_dis').innerHTML = '(Additional $'+ flagkitval +')';
									document.getElementById('flagkit_price').value = flagkitval;
									//var price = document.getElementById('total').value;
									price = Number(price)+Number(flagkitval);
									price = parseFloat(price).toFixed(2);
								}
							}
							else{
								var flagkit_price = document.getElementById('flagkit_price').value;	
 								if(flagkit_price != '' && flagkit_price != '0'){
										var price = document.getElementById('total').value;
									    document.getElementById('toplineframe_flagkit_dis').innerHTML = '';
										document.getElementById('flagkit_price').value = 0;
										price = Number(price)-Number(flagkit_price);
								        price = parseFloat(price).toFixed(2);
								}
								if(flagkit_price == '0'){
									document.getElementById('toplineframe_flagkit_dis').innerHTML = '';
								}
							}
							
 							 
							document.getElementById('toplineframe_price').innerHTML = price;
							document.getElementById('total').value = price; 
						}				
					}				
				 }			
				 req1.open("GET", URL, true);
				 req1.send(null);
	 }
		
}


function gettoplinebannerframeprice()
{
 	var size = document.getElementById('topline_framesize').value;
	var framesize = size.split("x");
	var width = framesize[0];
	var height = framesize[1];
    //alert(width + "  " + height);
	document.getElementById('size_w').value = width;
	document.getElementById('size_h').value = height;
	//document.getElementById('topline_framesize').value = width+'x'+height;
	if(document.getElementById('price_show')){
		getPrice();
	}
}

function gettoplinebannerframesize()
{
 	var framebase = document.getElementById('toplineframe_base').value;
	var sub_cat_id = document.getElementById('sub_cat_id').value;
	var bannerid = document.getElementById('bannerid').value;
	var URL = "gettoplineframesize.php?base="+framebase+'&printready=yes&subcatid='+sub_cat_id+'&bannerid='+bannerid;
	var req1 = getXMLHTTP(); 
	if(req1) 
	{ 
				req1.onreadystatechange = function()
				{ 
					if(req1.readyState == 4) 
					{			
						if(req1.status == 200)
						{
								document.getElementById('bannerframesize').innerHTML = req1.responseText;
								var size = document.getElementById('topline_framesize').value;
								gettoplinebannerframeprice();
						}
					}
				}
	}
 	 req1.open("GET", URL, true);
	 req1.send(null);
}

function gettoplinebanneroptionprice(){
 	var shortparapet;
	if(document.getElementById('toplineframe_shortparapet').checked == true){
		shortparapet = 'yes';
	}
	else{
		shortparapet = 'no';
	}
	var exttrackupgrade;
	if(document.getElementById('toplineframe_exttrackupgrade').checked == true){
		exttrackupgrade = 'yes';
	}
	else{
		exttrackupgrade = 'no';
	}
	var flagkit;
	if(document.getElementById('toplineframe_flagkit').checked == true){
		flagkit = 'yes';
	}
	else{
		flagkit = 'no';
	}
 	var framebase = document.getElementById('toplineframe_base').value;
	var size =  document.getElementById('topline_framesize').value;
 	var qty = document.getElementById('qty').value;
	 if(shortparapet == 'yes'){
		 if(framebase == 'dual'){
			var shortparapetprice = 59*qty;
		 }
		 else{
		   	var shortparapetprice = 49*qty;
		 }
		 if(document.getElementById('shortparapet_price_display')){
			 document.getElementById('shortparapet_price_display').innerHTML = '(Additional $'+ shortparapetprice +')';
		 }
		 document.getElementById('shortparapet_price').value = shortparapetprice;
	 }	
	 else{
		  if(document.getElementById('shortparapet_price_display')){ 
		     document.getElementById('shortparapet_price_display').innerHTML = '';
		  }
		 document.getElementById('shortparapet_price').value = '0';
	 }
	 
	 if(exttrackupgrade == 'yes'){
		 if(framebase == 'dual'){
			var exttrackupgradeprice = 99*qty;
		 }
		 else{
		   var exttrackupgradeprice = 79*qty;
		 }
		  if(document.getElementById('exttrackupgrade_price_display')){
			 document.getElementById('exttrackupgrade_price_display').innerHTML = '(Additional $'+ exttrackupgradeprice +')';
		  }
		 document.getElementById('exttrackupgrade_price').value = exttrackupgradeprice;
	 }	
	 else{
		 if(document.getElementById('exttrackupgrade_price_display')){
		     document.getElementById('exttrackupgrade_price_display').innerHTML = '';
		 }
		 document.getElementById('exttrackupgrade_price').value = '0';
	 }
	 
 	 if(flagkit == 'yes'){
		 if(framebase == 'dual'){
			var flagkitprice = 26*qty;
		 }
		 else{
		 	  var flagkitprice;
 			  var width1 = size.split("x"); 
			  width1 = width1[0];
  			  if(width1 >= 10){
				   flagkitprice = 19.5*qty; 
			  }
			  else{
				   flagkitprice = 13*qty;
			  }
 		 }
		 if(document.getElementById('flagkit_price_display')){
		 	document.getElementById('flagkit_price_display').innerHTML = '(Additional $'+ flagkitprice +')';
		 }
		 document.getElementById('flagkit_price').value = flagkitprice;
	 }	
	 else{
		 	 if(document.getElementById('flagkit_price_display')){
			     document.getElementById('flagkit_price_display').innerHTML = '';
			 }
		 document.getElementById('flagkit_price').value = '0';
	 }
}

/*Ground mount*/

function getgroundmountframeprice(){
	var lagframesize = document.getElementById('groundmount_legframesize').value;
 	var ground_framesize = document.getElementById('groundmount_framesize').value;
 	var framebase = document.getElementById('groundmount_framebase').value;
 	var flagkit = document.getElementById('groundmount_flagkit').checked;
	var bannerid = document.getElementById('bannerid').value;
	var subcatid = document.getElementById('product').value;
	var qty = document.getElementById('qty').value;
 	
	var URL = "getgroundmountframesize.php?lagsize="+lagframesize+"&base="+framebase+'&bannerid='+bannerid+'&subcatid='+subcatid+'&size='+ground_framesize;
  	document.getElementById('flagkit_price').value = 0;
    var req1 = getXMLHTTP(); 
	if(req1) 
	{ 
				req1.onreadystatechange = function()
				{ 
					if(req1.readyState == 4) 
					{			
						if(req1.status == 200)
						{
 								var price = req1.responseText;
								price = price*qty;
								price = parseFloat(price).toFixed(2);
  								var groundframesize = ground_framesize.split('x'); 
								var height = groundframesize[1];
								var width = groundframesize[0];
								document.getElementById('size_w').value = width;
								document.getElementById('size_h').value = height;
 								if(flagkit == true)
								{
									var flagkitval;	
 									var width1 = ground_framesize.split("x"); 	
									
									width1 = width1[0];
 										if(width1 >= 18){
											flagkitval = 26*qty; 	
										}
										else if(width1 >= 10){
											flagkitval = 19.5*qty; 	
										}
										else{
											flagkitval = 13*qty;  
										}
									 
									//alert(flagkitval);
									if(document.getElementById('flagkit_price').value == '' || document.getElementById('flagkit_price').value == '0')
									{
										flagkitval = parseFloat(flagkitval).toFixed(2);
 										if(document.getElementById('groundmount_frameflagkit_dis')){
											document.getElementById('groundmount_frameflagkit_dis').innerHTML = '(Additional $'+ flagkitval +')';
										}
										document.getElementById('flagkit_price').value = flagkitval;
 										price = Number(price)+Number(flagkitval);
										price = parseFloat(price).toFixed(2);
									}
							   }
								else{
									var flagkit_price = document.getElementById('flagkit_price').value;	
									
									if(flagkit_price != '' && flagkit_price != '0'){
											var price = document.getElementById('total').value;
											if(document.getElementById('groundmount_frameflagkit_dis')){
												document.getElementById('groundmount_frameflagkit_dis').innerHTML = '';
											}
											document.getElementById('flagkit_price').value = 0;
											price = Number(price)-Number(flagkit_price);
											price = parseFloat(price).toFixed(2);
									}
									if(flagkit_price == '0'){
										if(document.getElementById('groundmount_frameflagkit_dis')){	
											document.getElementById('groundmount_frameflagkit_dis').innerHTML = '';
										}
									}
							}
							    
								if(document.getElementById('groundmountframe_price')){
									document.getElementById('groundmountframe_price').innerHTML = price;
								}
								document.getElementById('total').value = price; 
						}				
					}				
				 }			
				 req1.open("GET", URL, true);
				 req1.send(null);
	 }
		
}

function getgroundmountframesize(){
	var lagframesize = document.getElementById('groundmount_legframesize').value;
 	var bannerid = document.getElementById('bannerid').value;
	var subcatid = document.getElementById('product').value;
	
	if(document.getElementById('groundmount_framebase')){
 		if(lagframesize == 4){
			document.getElementById('groundmount_framebase').value = 'single'; 
			document.getElementById('groundmount_framebase').disabled = true;
		}
		else{
			document.getElementById('groundmount_framebase').disabled = false; 
		}
	}
	
	var URL = "getgroundmountframesize.php?lagsize="+lagframesize+'&bannerid='+bannerid+'&subcatid='+subcatid+'&changesize=size';
     var req1 = getXMLHTTP(); 
	if(req1) 
	{ 
				req1.onreadystatechange = function()
				{ 
					if(req1.readyState == 4) 
					{			
						if(req1.status == 200)
						{ 
							var box = req1.responseText.split('#$#'); 
							var selectbox = box[0];
 							var width = box[1];
							var height = box[2];
						    document.getElementById('framesize').innerHTML = selectbox;
 							document.getElementById('size_w').value = width;
							document.getElementById('size_h').value = height;
							
							getgroundmountframeprice();
						}				
					}				
				 }			
				 req1.open("GET", URL, true);
				 req1.send(null);
	 }
}

function getgroundmountbannerframesize()
{
 	var lagsize = document.getElementById('groundmount_legframesize').value;
 	var groundmount_framesize = document.getElementById('groundmount_framesize').value;
	var sub_cat_id = document.getElementById('sub_cat_id').value;
	var bannerid = document.getElementById('bannerid').value;
	
	if(document.getElementById('groundmount_framebase')){
 		if(lagsize == 4){
			document.getElementById('groundmount_framebase').value = 'single'; 
			document.getElementById('groundmount_framebase').disabled = true;
		}
		else{
			document.getElementById('groundmount_framebase').disabled = false; 
		}
	}
	
	var URL = "getgroundmountframesize.php?lagsize="+lagsize+'&printready=yes&subcatid='+sub_cat_id+'&bannerid='+bannerid;
	var req1 = getXMLHTTP(); 
	if(req1) 
	{ 
				req1.onreadystatechange = function()
				{ 
					if(req1.readyState == 4) 
					{			
						if(req1.status == 200)
						{
								document.getElementById('bannerframesize').innerHTML = req1.responseText;
								var size = document.getElementById('groundmount_framesize').value;
 								getgroundmountbannerframeprice();
						}
					}
				}
	}
 	 req1.open("GET", URL, true);
	 req1.send(null);
}

function getgroundmountbannerframeprice()
{
 	var size = document.getElementById('groundmount_framesize').value;
	var framesize = size.split("x");
	var width = framesize[0];
	var height = framesize[1];
    //alert(width + "  " + height);
	document.getElementById('size_w').value = width;
	document.getElementById('size_h').value = height;
	//document.getElementById('topline_framesize').value = width+'x'+height;
	if(document.getElementById('price_show')){
		getPrice();
	}
}

/// vinyl lettering multicolor

function checkmulticolor()
{
 	var lettercoloropt = document.getElementById('lettercoloropt');
	var grand_total = document.getElementById('total').value;
	var base_total = document.getElementById('base_total').value;
	
	if(lettercoloropt.value == 'multi color')
	{
			//document.getElementById('multicoloroption').style.display = '';
		    var multicolorprice = base_total * 0.30 ;
			multicolorprice = parseFloat(multicolorprice).toFixed(2);
 			document.getElementById("multicolorpricedisplay").innerHTML = '(Additional $'+ multicolorprice +')';
			document.getElementById("multicolorprice").value = multicolorprice;
			grand_total= Number(grand_total)+Number(multicolorprice);
			roundNumbertc(grand_total,2);
	}
	else
	{
 		//document.getElementById('multicoloroption').style.display = 'none'; 
		var multicolorprice = document.getElementById("multicolorprice").value;
		document.getElementById("multicolorpricedisplay").innerHTML = "";
		document.getElementById("multicolorprice").value = '0';
		grand_total= Number(grand_total)-Number(multicolorprice);
	    roundNumbertc(grand_total,2);
		
	}
}

function showletterpopup(id)
{
 	document.getElementById('letteringcolorpalate'+id).style.display = 'block';
	document.getElementById('nametextid').value = id;
}

function updatecolorpalate(color)
{
   var id = document.getElementById('nametextid').value;
   document.getElementById('text_color_'+id).value = color;
   document.getElementById('letteringcolorpalate'+id).style.display = 'none';
}
 
function showspecificationdata()
{
	 document.getElementById('lightspecification').style.display = 'block';
	 document.getElementById('fadespecification').style.display = 'block';
}

/*white ink options */
function getwhiteinkoptions()
{	
	var id = document.getElementById('categoryid').value;  
	var subcatid = document.getElementById("sub_cat_id").value;
  
   if(id == 32 || id == 31 || id == 44)
   {
 		 if(document.getElementById('whiteinkprice'))
		 {
		 	var val=0;
			val = document.getElementById('whiteink_yes').checked;
			if(val == true)
			{
				val=1;
			}   
			var grand_total = document.getElementById('total').value;
			var base_total = document.getElementById('base_total').value;
			var whiteinkprice = document.getElementById('whiteinkprice');
			if(val == '1' && whiteinkprice.value == '0')
			{
				var newwhiteprice = base_total * 0.20 ;
				newwhiteprice = parseFloat(newwhiteprice).toFixed(2);
				document.getElementById("whiteinkpricedis").innerHTML = '(Additional $'+ newwhiteprice +')';
				document.getElementById("whiteinkprice").value = newwhiteprice;
				grand_total= Number(grand_total)+Number(newwhiteprice);
				roundNumbertc(grand_total,2);
					
			}
			else if(val == '0' && whiteinkprice.value != 0)
			{
				var newwhiteprice = document.getElementById("whiteinkprice").value;
				document.getElementById("whiteinkpricedis").innerHTML = "";
				document.getElementById("whiteinkprice").value = '0';
				grand_total= Number(grand_total)-Number(newwhiteprice);
				roundNumbertc(grand_total,2);
			}
		 }
   }
}

function checkcleargrommetprice()
{
	if(document.getElementById('grommetoptions'))
	{
		var gmtoption = document.getElementById('grommetoptions').value;
		var grand_total = document.getElementById('total').value;
		var base_total = document.getElementById('base_total').value;
 		if(gmtoption == 'Clear Grommets')
		{
				var height = document.getElementById('size_h').value;
				var width = document.getElementById('size_w').value;
				var major = 'ft';
				if(document.getElementById('major')){
					 major = document.getElementById('major').value
				}
				if(major == 'in' || major == 'In'){
					height = height/12;
					height = parseFloat(height).toFixed(2);
					width = width/12;
					width = parseFloat(width).toFixed(2);
				}
				var qty = document.getElementById('qty').value;
				var linierft = (Number(2*(height)) + Number(2*(width)));
				linierft = parseFloat(linierft/3).toFixed(2);
 				var cleargrommrtprice = linierft*0.50*qty;
				cleargrommrtprice = parseFloat(cleargrommrtprice).toFixed(2);
			    document.getElementById("div_cleargroometprice").innerHTML = '(Additional $'+ cleargrommrtprice +')';
				document.getElementById("cleargroometprice").value = cleargrommrtprice;
				grand_total= Number(grand_total)+Number(cleargrommrtprice);
				roundNumbertc(grand_total,2);
				 
		}
		else if(gmtoption != 'Clear Grommets' && (document.getElementById("cleargroometprice").value != "" && document.getElementById("cleargroometprice").value != "0"))
		{
				var cleargmtprice = document.getElementById("cleargroometprice").value;
				document.getElementById("div_cleargroometprice").innerHTML = '';
				document.getElementById("cleargroometprice").value=0;
 				grand_total= Number(grand_total)-Number(cleargmtprice);
				roundNumbertc(grand_total,2);
		}
	}
}

function updateframesize()
{
	  var pos = document.getElementById('position').value;
	  if(pos == 'Vertical')
	  {
		 document.getElementById('framehorizontal').style.display = 'none';
    	 document.getElementById('framevertical').style.display = 'block'; 	
		 var height = document.getElementById('heightv').value;
		 var width = 126.5;
	  }
	  else
	  {
		document.getElementById('framehorizontal').style.display = 'block';
		document.getElementById('framevertical').style.display = 'none';   
		var height = 72;
		var width = document.getElementById('widthh').value;
	  }
 	  document.getElementById('size_w').value = width;
	  document.getElementById('size_h').value = height;
	  if(document.getElementById('price_show'))
	  {
	  	 getPrice();
	  }
}