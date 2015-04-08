
function checkaddtocart()
{
   
	var chks = document.getElementsByName('cart_delete[]');
	var cartid = "";
	var k=0;
	var count = 0;
	for(i=0;i < document.orderform.elements.length;i++)
	{
		if(document.orderform.elements[i].name == 'cart_delete[]')
		{
			if(document.orderform.elements[i].checked)
			{
				count++;
			}
		}
	}
	
	
	if(count == 0)
	{
		alert('You must select atleast one product to proceed.');
		return false;
	}
	else
	{
		for (var i = 0; i < chks.length; i++)
		{
			if(chks[i].checked)
			 {
				 cartid += chks[i].value;
				 cartid += ":";	 
				 
			 }
			 
			 
		}
	
 	  document.getElementById('bannersize_flex_id').value = cartid;
	}
 
}
 
function valid_extension(FILE_STR,BID)
{
	
	var CHECK_FILE = FILE_STR.toLowerCase();
	var VALID_EXTENSIONS = new Array();
	if(BID == 8)
	{
		VALID_EXTENSIONS[0] = ".ai";
		VALID_EXTENSIONS[1] = ".AI";
		VALID_EXTENSIONS[2] = ".cdr";
		VALID_EXTENSIONS[3] = ".CDR";
		VALID_EXTENSIONS[4] = ".eps";
		VALID_EXTENSIONS[5] = ".EPS";
		VALID_EXTENSIONS[6] = ".svg";
		VALID_EXTENSIONS[7] = ".SVG";
		VALID_EXTENSIONS[8] = "";			
	}
	else
	{
		VALID_EXTENSIONS[0] = ".jpeg";
		VALID_EXTENSIONS[1] = ".pdf";
		VALID_EXTENSIONS[2] = ".doc";
		VALID_EXTENSIONS[3] = ".jpg";
		VALID_EXTENSIONS[4] = ".docx";
		VALID_EXTENSIONS[5] = ".eps";
		VALID_EXTENSIONS[6] = ".cdr";
		VALID_EXTENSIONS[7] = ".ai";
		VALID_EXTENSIONS[8] = ".gif";
		VALID_EXTENSIONS[9] = ".psd";
		VALID_EXTENSIONS[10] = ".tif";
		VALID_EXTENSIONS[11] = ".tiff";
		VALID_EXTENSIONS[12] = ".ppt";
		VALID_EXTENSIONS[13] = ".png";
		VALID_EXTENSIONS[14] = ".bmp";
		VALID_EXTENSIONS[15] = "";
	}
	/*if(FILE_STR != '')
	{*/
	for (EXT_LOOP = 0; EXT_LOOP < VALID_EXTENSIONS.length; EXT_LOOP++)
	{
		VALID_EXTENSION = VALID_EXTENSIONS[EXT_LOOP];
		POS = CHECK_FILE.indexOf(VALID_EXTENSION);
 		if (POS > 0)
		{
			EXT_SUBTRING = CHECK_FILE.substring(POS, CHECK_FILE.length);
			if (VALID_EXTENSION.length == EXT_SUBTRING.length)
				return true;
		}
	}
	/*}else{
		return true;
	}*/
	return false;
	
	return true;
}

function isDigit (c)
{
	return ((c >= "0") && (c <= "9"));
}

function isInteger (s)
{
  var i;

  //if (isInteger.arguments.length == 1) return 0;
 // else return (isInteger.arguments[1] == true);

  for (i = 0; i < s.length; i++)
  {
	 var c = s.charAt(i);

	 if (!isDigit(c)) return false;
  }

  return true;
}
 
function checkNumeric()
{
   
	if(isNaN(document.getElementById("size_w").value))
	{
		document.getElementById("size_w").value= 2;
		alert("Please enter only numeric value for width.");
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
	
	
	if(isNaN(document.getElementById("qty").value))
	{
		document.getElementById("qty").value= 1;
		alert("Please enter only numeric value for quantity.");
		document.getElementById("qty").focus();
		return false;	
	}
	else
	{
		if (document.getElementById("qty").value < 0)
		{
		    document.getElementById("qty").value = 1;
			alert("Please do not enter negative value for quantity.");
			document.getElementById("qty").focus();
			return false;
		}  
		
	}
	
	// Window Clings (Opaque) Size validation
 	var subcatid = document.getElementById('sub_cat_id').value;
	//alert(subcatid);
   	if(subcatid == 15 || subcatid == 17 || subcatid == 16 || subcatid == 28){
		if(document.getElementById("apply_at"))
		{
			if(subcatid == 28)
			{
			   var apply = document.getElementById("producttype").value;	
			}
			else
			{
 	    		var apply = document.getElementById("apply_at").value;
			}
			//alert(apply);
	  	    if(apply == 'Inside of the glass surface' || (apply == 'Inside' && document.getElementById("apply_at").value == 'Decals')){
			    var height = document.getElementById('size_h').value;
				var width = document.getElementById('size_w').value;
				var major = 'ft';
				if(document.getElementById('major')){
					 major = document.getElementById('major').value
				}
				if(major == 'in'){
					height = height/12;
					height = parseFloat(height).toFixed(2);
					width = width/12;
					width = parseFloat(width).toFixed(2);
				}
			   //var sqft = height*width;
			   //alert(height + ' ' + width);
			   if(subcatid == 17 || subcatid == 16 || subcatid == 28)
			   {
				   if((height >=3 && width >= 3)){
					    document.getElementById('errorwindowcling').innerHTML = "Please enter size minimum 3' wide x 3' height";	 
 						document.getElementById('errorwindowcling').style.display = 'none';
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
					 document.getElementById('errorwindowcling').innerHTML = "Please enter size minimum 3' wide x 3' height";	   
 					 document.getElementById('errorwindowcling').style.display = 'block';
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
			   else 
			   {
				   if((height <= 2 && width <= 3) || (height <=3 && width <= 2))
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
		}
	}
	
	return true;
	  
}
function chksubmit(id)
{
	
	if(document.getElementById("size_w").value=='' || document.getElementById("size_h").value=='')
	{
		alert("Please Enter Size.");
		return false;
	}
	var width = document.getElementById("size_w").value;
	var height = document.getElementById("size_h").value;
	 
	if(document.getElementById("major").value == "in") {
		width = document.getElementById("size_w").value / 12;
		height = document.getElementById("size_h").value / 12;
	}
    
	
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 1 || document.getElementById("bid").value == 8))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	
	if(((width > 20 && height > 20) || (height > 20) || (width > 100) ) && (document.getElementById("bid").value == 6))
	{
		alert("Please enter width upto 100ft. & height upto 20ft. for Vinyl Signs/Decals(Posters)");
		return false;
	}
	
	
	if(((width > 20 && height > 20) || (height > 20) || (width > 100) ) && (document.getElementById("bid").value == 2))
	{
		alert("Please enter width upto 100ft. & height upto 20ft. for Perforated Window Signs");
		return false;
	}
	
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 3))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 4))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	if((width > 8 && height > 8) && (document.getElementById("bid").value == 10))
	{
		alert("Please enter width upto 8ft or height upto 8ft.");
		return false;
	}
	if((width > 5 && height > 5) && (document.getElementById("bid").value == 11))
	{
		alert("Please enter width upto 5ft or height upto 5ft.");
		return false;
	}
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 12))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 13))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
 
	if(((width > 2 || height > 5)) && (document.getElementById("bid").value == 14))
	{
		alert("For special low price on sizes over 2ft. wide and 5ft. height contact us sales@bannerbuzz.com");
		return false;
	}
 
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 9))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
    
	if(((width > 5 && height > 5)) && (document.getElementById("bid").value == 3))
	{
		alert("Please enter width upto 5ft or height upto 5ft.");
		return false;
	}
	
	if(document.getElementById('qty').value=="")
	{
		alert("Please enter exact no. of banner you want to print.");
		document.getElementById('qty').focus();
		return false;
	}
	if (document.getElementById('qty').value <= 0)
	{
		alert("Please specify a quantity greater than zero.");
		document.getElementById('qty').focus();
		return false;
	}
	if(!isInteger(document.getElementById('qty').value))
	{
		alert("Please enter integer value.");
		document.getElementById('qty').focus();
		return false;
	}
	if (!((document.getElementById('qty').value >= 1) && (document.getElementById('qty').value < 100000)))
	{
		alert("If You Have order greater than 100000 \nThen Please Contact Us through Phone.");
		document.getElementById('qty').focus();
		return false;
	}
if(document.getElementById('bgimage') != null){	
	if (document.getElementById('bgimage').value == "" && document.getElementById('yousendit').checked == "" && document.getElementById('artworklater').checked == "")
	{
		alert("Please attach your print ready file or Upload through You Send It.");
		document.getElementById('bgimage').focus();
		return false;
	}
   if(document.getElementById('yousendit').checked == "" && document.getElementById('artworklater').checked == ""){
	if (!valid_extension(document.getElementById('bgimage').value,document.getElementById("bid").value))
	{
		alert("Uploaded file has invalid extension: '" + document.getElementById('bgimage').value + "'. Please upload recommended valid format file");
		document.getElementById('bgimage').focus();
		return false;
	}
   }
}
	return true;
}

function chksubmit_product_detail(id)
{
	if(document.getElementById("size_w").value=='' || document.getElementById("size_h").value=='')
	{
		alert("Please Enter Size.");
		return false;
	}

	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bid").value == 1 || document.getElementById("bid").value == 8))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	
	if(((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bid").value == 6))
	{
		alert("Please enter width upto 100ft. & height upto 20ft. for Vinyl Signs/Decals(Posters)");
		return false;
	}
	
	
	if(((document.getElementById("size_w").value > 20 && document.getElementById("size_h").value > 20) || (document.getElementById("size_h").value > 20) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bid").value == 2))
	{
		alert("Please enter width upto 100ft. & height upto 20ft. for Perforated Window Signs");
		return false;
	}
	
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bid").value == 3))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bid").value == 4))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	if((document.getElementById("size_w").value > 8 && document.getElementById("size_h").value > 8) && (document.getElementById("bid").value == 10))
	{
		alert("Please enter width upto 8ft or height upto 8ft.");
		return false;
	}
	
	if((document.getElementById("size_w").value > 5 && document.getElementById("size_h").value > 5) && (document.getElementById("bid").value == 11))
	{
		alert("Please enter width upto 5ft or height upto 5ft.");
		return false;
	}
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bid").value == 13))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bid").value == 9))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	if(((document.getElementById("size_w").value > 24 || document.getElementById("size_h").value > 60)) && (document.getElementById("bid").value == 14))
	{
		alert("For special low price on sizes over 2ft. wide and 5ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	if(((document.getElementById("size_w").value > 5 && document.getElementById("size_h").value > 5)) && (document.getElementById("bid").value == 3))
	{
		alert("Please enter width upto 5ft or height upto 5ft.");
		return false;
	}
	 
	if(document.getElementById('qty').value=="")
	{
		alert("Please enter exact no. of banner you want to print.");
		document.getElementById('qty').focus();
		return false;
	}
	if (document.getElementById('qty').value <= 0)
	{
		alert("Please specify a quantity greater than zero.");
		document.getElementById('qty').focus();
		return false;
	}
	if(!isInteger(document.getElementById('qty').value))
	{
		alert("Please enter integer value.");
		document.getElementById('qty').focus();
		return false;
	}
	if (!((document.getElementById('qty').value >= 1) && (document.getElementById('qty').value < 100000)))
	{
		alert("If You Have order greater than 100000 \nThen Please Contact Us through Phone.");
		document.getElementById('qty').focus();
		return false;
	}
	
	return true;
}


function chksubmit_edit(id)
{
	
	if(document.getElementById("size_w").value=='' || document.getElementById("size_h").value=='')
	{
		alert("Please Enter Size.");
		return false;
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
		
	 
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 1 || document.getElementById("bid").value == 8))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	
	if(((width > 240 && height > 240) || (height > 240) || (width > 1200) ) && (document.getElementById("bid").value == 6))
	{
		alert("Please enter width upto 1200Inch. & height upto 240Inch. for Vinyl Signs/Decals(Posters)");
		return false;
	}
	
	
	if(((width > 20 && height > 20) || (height > 20) || (width > 100) ) && (document.getElementById("bid").value == 2))
	{
		alert("Please enter width upto 100ft. & height upto 20ft. for Perforated Window Signs");
		return false;
	}
	
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 3))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 4))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	if((width > 8 && height > 8) && (document.getElementById("bid").value == 10))
	{
		alert("Please enter width upto 8ft or height upto 8ft.");
		return false;
	}
	
	if((width > 5 && height > 5) && (document.getElementById("bid").value == 11))
	{
		alert("Please enter width upto 5ft or height upto 5ft.");
		return false;
	}
	
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 12))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 13))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
 
	if(((width > 2 || height > 5)) && (document.getElementById("bid").value == 14))
	{
		alert("For special low price on sizes over 2ft. wide and 5ft. height contact us sales@bannerbuzz.com");
		return false;
	}
 
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 9))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		return false;
	}
 
    if(((width > 5 && height > 5)) && (document.getElementById("bid").value == 3))
	{
		alert("Please enter width upto 5ft or height upto 5ft.");
		return false;
	}
	
	if(document.getElementById('qty').value=="")
	{
		alert("Please enter exact no. of banner you want to print.");
		document.getElementById('qty').focus();
		return false;
	}
	if (document.getElementById('qty').value <= 0)
	{
		alert("Please specify a quantity greater than zero.");
		document.getElementById('qty').focus();
		return false;
	}
	if(!isInteger(document.getElementById('qty').value))
	{
		alert("Please enter integer value.");
		document.getElementById('qty').focus();
		return false;
	}
	if (!((document.getElementById('qty').value >= 1) && (document.getElementById('qty').value < 100000)))
	{
		alert("If You Have order greater than 100000 \nThen Please Contact Us through Phone.");
		document.getElementById('qty').focus();
		return false;
	}
	 
	if(document.getElementById('bgimage') != null){
	if (document.getElementById('bgimage').value == "" && document.getElementById('yousendit').checked == "" && document.getElementById('pro_image').value == "" && document.getElementById('artworklater').checked == "")
	{
		alert("Please attach your print ready file or Upload through You Send It.");
		document.getElementById('bgimage').focus();
		return false;
	}
   if(document.getElementById('yousendit').checked == "" && document.getElementById('pro_image').value == "" && document.getElementById('artworklater').checked == ""){
	if (!valid_extension(document.getElementById('bgimage').value,document.getElementById("bid").value))
	{
		alert("Uploaded file has invalid extension: '" + document.getElementById('bgimage').value + "'. Please upload recommended valid format file");
		document.getElementById('bgimage').focus();
		return false;
	}
   }
	}
	return true;
}

function checksizeonblur()
{
 	if(document.getElementById("size_w").value=='' || document.getElementById("size_h").value=='')
	{
		alert("Please Enter Size.");
		return false;
	}
	var width = document.getElementById("size_w").value;
	var height = document.getElementById("size_h").value;
 	if(document.getElementById("major").value == "in") {
		width = document.getElementById("size_w").value / 12;
		height = document.getElementById("size_h").value / 12;
	}
    
 	
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 1 || document.getElementById("bid").value == 8))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 1200;
			document.getElementById("size_h").value = 360; 
		}
		else
		{
			document.getElementById("size_w").value = 100;
			document.getElementById("size_h").value = 30;
		}
		return false;
	}
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 9))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 1200;
			document.getElementById("size_h").value = 360; 
		}
		else
		{
			document.getElementById("size_w").value = 100;
			document.getElementById("size_h").value = 30;
		}
		return false;
	}
	if(((width > 20 && height > 20) || (height > 20) || (width > 100) ) && (document.getElementById("bid").value == 6))
	{
		alert("Please enter width upto 100ft. & height upto 20ft. for Vinyl Signs/Decals(Posters)");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 1200;
			 document.getElementById("size_h").value = 240; 
		}
		else
		{
		document.getElementById("size_w").value = 100;
		document.getElementById("size_h").value = 20;
		}
		return false;
	}
	if(((width > 20 && height > 20) || (height > 20) || (width > 100) ) && (document.getElementById("bid").value == 2))
	{
		alert("Please enter width upto 100ft. & height upto 20ft. for Perforated Window Signs");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 1200;
			document.getElementById("size_h").value = 240; 
		}
		else
		{
		document.getElementById("size_w").value = 100;
		document.getElementById("size_h").value = 20;
		}
		return false;
	}
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 3))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 1200;
			document.getElementById("size_h").value = 360; 
		}
		else
		{
		document.getElementById("size_w").value = 100;
		document.getElementById("size_h").value = 30;
		}
		return false;
	}
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 4))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 1200;
			document.getElementById("size_h").value = 360; 
		}
		else
		{
		document.getElementById("size_w").value = 100;
		document.getElementById("size_h").value = 30;
		}
		return false;
	}
	if((width > 8 && height > 8) && (document.getElementById("bid").value == 10))
	{
		alert("Please enter width upto 8ft or height upto 8ft.");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 96;
			document.getElementById("size_h").value = 96; 
		}
		else
		{
		document.getElementById("size_w").value = 8;
		document.getElementById("size_h").value = 8;
		}
		return false;
	}
	if((width > 5 && height > 5) && (document.getElementById("bid").value == 11))
	{
		alert("Please enter width upto 5ft or height upto 5ft.");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 60;
			 document.getElementById("size_h").value = 60; 
		}
		else
		{
		document.getElementById("size_w").value = 5;
		document.getElementById("size_h").value = 5;
		}
		return false;
	}
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 12))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 1200;
			document.getElementById("size_h").value = 360; 
		}
		else
		{
		document.getElementById("size_w").value = 100;
		document.getElementById("size_h").value = 30;
		}
		return false;
	}
	if(((width > 30 && height > 30) || (height > 30) || (width > 100) ) && (document.getElementById("bid").value == 13))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
		if(document.getElementById("major").value == "in")
		{
			 document.getElementById("size_w").value = 1200;
			document.getElementById("size_h").value = 360; 
		}
		else
		{
			document.getElementById("size_w").value = 100;
			document.getElementById("size_h").value = 30;
		}
		return false;
	}
	
	if(((width > 2 || height > 5)) && (document.getElementById("bid").value == 14))
	{
		alert("For special low price on sizes over 2ft. wide and 5ft. height contact us sales@bannerbuzz.com");
	
	   document.getElementById("size_w").value = 24;
	   document.getElementById("size_h").value = 60; 
		return false;
	}
	
	if(((width > 5 && height > 5)) && (document.getElementById("bid").value == 3))
	{
		alert("Please enter width upto 5ft or height upto 5ft.");
		if(document.getElementById("major").value == "in")
		{
		    document.getElementById("size_w").value = 60;
			document.getElementById("size_h").value = 60; 
		}
		else
		{
			document.getElementById("size_w").value = 5;
			document.getElementById("size_h").value = 5;
		}
	    return false;
	}
}
/* Increase/Decrease Counter For Banner Size*/
function increaseCounter(field)
{
	
	var str1 = document.getElementById(field);
	
	
	if(document.getElementById("major").value == "in")
	{
		var limit = 1200;
	}
	else
	{
		var limit = 100;
	}
	
	if (str1.value == '')
	{
		str1.value = 2;
	}
	else if(str1.value<=limit)
	{
		if(isFloat(eval(str1.value)+1))	
		{
			str1.value = (eval(str1.value)+1).toFixed(2);
			
			}
		else
		{
			str1.value = eval(str1.value)+1;
		}
	}
	
}
function isFloat(val){
	
	
	return parseFloat(val);
	
	}

function decreaseCounter(field)
{
	var str1 = document.getElementById(field);
	var bannerid = document.getElementById('bannerid').value;	 
	 
	var start;
	if(bannerid == 12)
	{
	   	start = 6;
		var condition = str1.value > start
	}
	else
	{
		start = 2;
		var condition = str1.value >= start;
	}
	
	if (str1.value == '')
	{
		str1.value = start;
	}
	else if(condition)
	{
		if(isFloat(eval(str1.value)-1))	
		{
			str1.value = (eval(str1.value)-1).toFixed(2);
		}
		else
		{
			str1.value = eval(str1.value)-1;
		}
		
	}
}

function bannersizechange()
{
	var val = document.getElementById('bannersize').options[document.getElementById('bannersize').selectedIndex].title;
	//var size =parseInt(document.getElementById('bannersize').value.substr(1));
	var dolar = parseInt(val.substr(1));
	document.getElementById('price').value = dolar;
}
 
function browseimage()
{
	if(document.getElementById('yousendit').checked)
	{
		document.getElementById('yousendit').checked = "";	
	}
	return true;
}
function yousend_it(ysi_link,p_id)
{
		if(document.getElementById('yousendit').checked)
		{
			document.getElementById('bgimage').disabled = true;
 			NewWindow(ysi_link,'BannerbuzzYouSendIt','950','650','0','yes');
			document.getElementById('ysi_id').value = p_id;
			
		 
		}
		else{
			document.getElementById('bgimage').disabled = false;
 		}
		return true;
}

function yousend_it_link(ysi_link,p_id)
{
		if(document.getElementById('yousendit').checked)
		{
			document.getElementById('yousendit').checked = false;	
		}
		else
		{
			document.getElementById('yousendit').checked = true;	
		}
	
		if(document.getElementById('yousendit').checked)
		{
			document.getElementById('bgimage').disabled = true;
			//window.open('http://dropbox.yousendit.com/BannerBuzz','_blank');
			NewWindow(ysi_link,'BannerbuzzYouSendIt','950','650','0','yes');
			document.getElementById('ysi_id').value = p_id;
		 
		}
		else{
			document.getElementById('bgimage').disabled = false;
 		}
		return true;
}


function yousend_it_tool()
{
		if(document.getElementById('yousendit').checked)
		{
			document.getElementById('logoimage').disabled = true;
			window.open('http://dropbox.yousendit.com/BannerBuzz','_blank');
 		}
		else{
			document.getElementById('logoimage').disabled = false;
		}
		return true;
}
function confirmDelete(Frm)
{	
	var count = 0;
	for(i=0;i < document.orderform.elements.length;i++)
	{
		if(document.orderform.elements[i].name == 'cart_delete[]')
		{
			if(document.orderform.elements[i].checked)
			{
				count++;
			}
		}
	}
	if(count == 0)
	{
		alert('You must select atleast one product to proceed.');
	}
	else
	{
		if(confirm('Are you sure you want to remove '+count+' item(s) from the list?'))
		{
			document.orderform.submit();
		}
	}
}

function remove_image(product_id,page)
		{
 		    document.getElementById('pro_image').value = '';
			document.getElementById('sendlater_artwork').value = '';
			 var catid = document.getElementById('categoryid').value;
			  var randomno = Math.floor((Math.random()*99999999)+1);
			var url = "remove_image.php?product_id="+product_id+"&page="+page;
			var req = getXMLHTTP();		
				if (req) 
				{ 
					//function to be called when state is changed
					req.onreadystatechange = function()
					{
						//when state is completed i.e 4
						if (req.readyState == 4) 
						{			
							// only if http status is "OK"
							if (req.status == 200)
							{
							 document.getElementById('pro_img').style.display = 'none';
							 document.getElementById('img_span').style.display = 'none';
							 document.getElementById('pro_image').value = '';
							 document.getElementById('sendlater_artwork').value = '';
							 document.getElementById('bgimage').disabled = false;
							 document.getElementById('artworklater').disabled = false;
							 document.getElementById('yousendit1').disabled = false;
							 document.getElementById('yousendit').disabled = false;
							 document.getElementById('imageuploadopt1').innerHTML = "<a href='javascript:void(0);' id='selectopt1' onclick='checkuploadvalue(0);'>Upload files up to 15 MB</a>";
								 document.getElementById('imageuploadopt3').innerHTML = "<a href='javascript:void(0);' id='selectopt3' onclick='checkuploadvalue(2);'>I’ll send my artwork file later. Please send me the link to upload my file.</a>";
								  document.getElementById('imageuploadopt2').innerHTML = '<a href="javascript:void(0);" onclick="checkuploadvalue(1);return yousend_it(\'sitedrop_yousendit.php?p_id='+randomno+'\',\''+randomno+'\');" style="color:#000000;">Upload file on FTP server  (up to 2GB)</a>';
						     document.getElementById('cartnextbtn').setAttribute("onclick","return chksubmit("+catid+")");
							 
							}
						}
					}
					req.open("GET", url, true);
					req.send(null);
				}
			
		}	
		
function remove_printreadyimage(imagename)
{ 
     var catid = document.getElementById('categoryid').value;
	 var randomno = Math.floor((Math.random()*99999999)+1);
	 
      var url = "remove_printreadyimage.php?catid="+catid;
 	 var req = getXMLHTTP();		
	 if (req) 
	 { 
					//function to be called when state is changed
					req.onreadystatechange = function()
					{
 						if (req.readyState == 4) 
						{			
 							if (req.status == 200)
							{
 								 document.getElementById('pro_img').style.display = 'none';
								 document.getElementById('img_span').style.display = 'none';
								 document.getElementById('sendlater_artwork').value = '';
								 document.getElementById('bgimage').disabled = false;
								 document.getElementById('artworklater').disabled = false;
								 document.getElementById('yousendit1').disabled = false;
						         document.getElementById('yousendit').disabled = false;
								 document.getElementById('imageuploadopt1').innerHTML = "<a href='javascript:void(0);' id='selectopt1' onclick='checkuploadvalue(0);'>Upload files up to 15 MB</a>";
								 document.getElementById('imageuploadopt3').innerHTML = "<a href='javascript:void(0);' id='selectopt3' onclick='checkuploadvalue(2);'>I’ll send my artwork file later. Please send me the link to upload my file.</a>";
								  document.getElementById('imageuploadopt2').innerHTML = '<a href="javascript:void(0);" onclick="checkuploadvalue(1);return yousend_it(\'sitedrop_yousendit.php?p_id='+randomno+'\',\''+randomno+'\');" style="color:#000000;">Upload file on FTP server  (up to 2GB)</a>';
								 
								 document.getElementById('cartnextbtn').setAttribute("onclick","return chksubmit("+catid+")");
								 document.getElementById('cartnextbtn').setAttribute("onclick","return chksubmit("+catid+")");
 							}
						}
					}
					req.open("GET", url, true);
					req.send(null);
				}
}

 function showcustomprint_size()
		{
		   document.getElementById('customprint_size').style.display = 'block';
		}
		
function checkemailtofriend(){
	document.getElementById('light').style.display='block';
	document.getElementById('fade').style.display='block';
}
function sendinformation(){
	var emailadd = document.getElementById('fromemailadd');
	var personalmsg = document.getElementById('personalmessage');
	document.getElementById('overlay').style.height = "0px";
	document.getElementById('overlay').style.display='none';
	var sendername = document.getElementById('yourname');
	var str=0;
	if(emailadd.value == '' || emailadd.value == 'Email:'){
		document.getElementById('emailaddmsg').style.display = 'block';
		document.getElementById('emailaddmsg').innerHTML = 'Please enter email address';
		str = 1;	
	}
	else {
		var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		if(!emailadd.value.match(emailExp)){
			 document.getElementById('emailaddmsg').style.display = 'block';
			 document.getElementById('emailaddmsg').innerHTML = 'Please enter your correct email address';
			 str = 1;
		}	
		
	}
	 
	if(sendername.value == '' || sendername.value == 'Your name'){
		document.getElementById('yornameerr').style.display = 'block';
		document.getElementById('yornameerr').innerHTML = 'Please enter your name';
		str = 1;	
	}
	
	if(str == 1){
	   return false;	
	}
	else{
		 document.getElementById('waitmsg').style.display = 'block';
		 var imagepath = document.getElementById('curimagepath').value;
 		 var sendmsgurl = 'sendproductdetail.php?emailadd='+emailadd.value+'&cutname='+sendername.value+'&permsg='+personalmsg.value+'&imgpath='+imagepath;
		 var req = getXMLHTTP();
		 if (req) 
		 { 
 				req.onreadystatechange = function()
				{ 
 					if (req.readyState == 4) 
					{			
 						if (req.status == 200)
						{	 
						   if(req.responseText == '1'){
							   document.getElementById('waitmsg').innerHTML = 'Your email was sent successfully !';
							   document.getElementById('fromemailadd').value = 'Email:';
							   document.getElementById('personalmessage').value = 'Message';
							   document.getElementById('yourname').value = 'Your name';
							   document.getElementById('emailaddmsg').style.display = 'none';
							   document.getElementById('yornameerr').style.display = 'none';
							   document.getElementById('overlay').style.height = "0px";
						   }
						} 
						else 
						{
							alert("There was a problem while using XMLHTTP:\n" + req.statusText);
						}
					}				
				 }			
				 req.open("GET", sendmsgurl, true);
				 req.send(null);
			}	
	}
}