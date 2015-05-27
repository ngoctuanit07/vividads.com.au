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
		/*alert(POS);*/
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
	
	if(document.getElementById("bid").value == 12){
			if(document.getElementById("size_w").value < 6){
				 document.getElementById("size_w").value = 6;
			}
			if(document.getElementById("size_h").value < 6){
				 document.getElementById("size_h").value = 6;
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
  	if(subcatid == 15 || subcatid == 16 || subcatid == 17  || subcatid == 28){
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
					if((height <= 2 && width <= 3) || (height <=3 && width <= 2)){
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
  				
		}
		}
	}
	
	
	return true;
	  
}
function chksubmit(id)
{ 
	if(id==29)
	{
		if(document.getElementById('basic_color').value=="Select Color Combination")
		{
			alert("Please select Banner Color.");
			document.getElementById('basic_color').focus();
			return false;
		}
		 
		
		if(document.getElementById("size_w").value=='' || document.getElementById("size_h").value=='')
		{
			alert("Please Enter Size.");
			return false;
		}
//alert('hi');
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
	}
	else
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
		alert("Please enter width upto 8ft or height upto 8ft");
		return false;
	}
	if((document.getElementById("size_w").value > 5 && document.getElementById("size_h").value > 5) && (document.getElementById("bid").value == 11))
	{
		alert("Please enter width upto 5ft or height upto 5ft");
		return false;
	}	
	
	if(((document.getElementById("size_w").value > 30 && document.getElementById("size_h").value > 30) || (document.getElementById("size_h").value > 30) || (document.getElementById("size_w").value > 100) ) && (document.getElementById("bid").value == 12))
	{
		alert("For special low price on sizes over 100ft. wide and 30ft. height contact us sales@bannerbuzz.com");
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
	if(((document.getElementById("size_w").value > 5 && document.getElementById("size_h").value > 5)) && (document.getElementById("bid").value == 3))
	{
		 
		alert("Please enter width upto 5ft or height upto 5ft.");
		return false;
	}
	if(document.getElementById("bid").value == 14)
   {
	    if(document.getElementById("sub_cat_id").value == 65)
		{
			var width = document.getElementById("size_w").value / 12;
			var height = document.getElementById("size_h").value / 12;
		}
		else
		{
			var width = document.getElementById("size_w").value ;
			var height = document.getElementById("size_h").value;
		}
 	if(((width > 2 || height > 5)) && (document.getElementById("bid").value == 14))
	{
		alert("For special low price on sizes over 2ft. wide and 5ft. height contact us sales@bannerbuzz.com");
		return false;
	}
	
	
	
   }
	
	
	if(document.getElementById('logoimage1').value != ""){
		if ((!valid_extension(document.getElementById('logoimage1').value,document.getElementById("bid").value)) && document.getElementById('logoimage1').value != 'sentthroughyousendit.jpg')
		{
			alert("Uploaded file has invalid extension: '" + document.getElementById('logoimage1').value + "'. Please upload recommended valid format file");
			document.getElementById('logoimage1').focus();
			return false;
		}
	   }
	   
	    if(document.getElementById('logoimage2').value != ""){
		if (!valid_extension(document.getElementById('logoimage2').value,document.getElementById("bid").value))
		{
			alert("Uploaded file has invalid extension: '" + document.getElementById('logoimage2').value + "'. Please upload recommended valid format file");
			document.getElementById('logoimage2').focus();
			return false;
		}
	   }
	   
	    if(document.getElementById('logoimage3').value != ""){
		if (!valid_extension(document.getElementById('logoimage3').value,document.getElementById("bid").value))
		{
			alert("Uploaded file has invalid extension: '" + document.getElementById('logoimage3').value + "'. Please upload recommended valid format file");
			document.getElementById('logoimage3').focus();
			return false;
		}
	   }
	   
	  
	 if(id != 38)
	 {
		 if(document.getElementById('banner_color').value=="")
			{
				alert("Please select Banner Color.");
				document.getElementById('banner_color').focus();
				return false;
			}
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
	}
	<!-- Added by Pranav Dave to display the message of non recommended combination of Stiched finish with size-->
	/*var size = document.getElementById('bannersize').value.split('x');
	var sizesqft = size[0]*size[1];
	//document.getElementById('sizesqft').value = sizesqft;
	if(document.getElementById('finishoptions')!=null)
	{
	if(sizesqft >= 50 && document.getElementById('finishoptions').value == 'Stiched Finish')
	{
		
		document.getElementById('nostichedmsg').innerHTML = "Note: Stiched Finish for a banner of size '"+size[0]+"x"+size[1]+"' is not a recommended option as it might not hold out.";
		document.getElementById('nostichedmsg').style.clear = "both";
		document.getElementById('nostichedmsg').style.display = "block";
	
		//window.location.hash = '#nostichedmsg';
		document.getElementById('finishoptions').focus();
		return false;
	}
	}*/
	return true;
}
/* Increase/Decrease Counter For Banner Size*/


function quantity()
{
	
	var total_value;
if(document.getElementById('qty').value != '')
		{
			var priceshow = document.getElementById('price_show').value;
			priceshow = priceshow.replace(/[^\d\.\-\ ]/g, '');
			total_value = (priceshow) * document.getElementById('qty').value;		
			roundNumbertc(total_value,2);
		}else{
		document.getElementById('total').value = 0;	
		document.getElementById("total_display").innerHTML = "$0.00";
		}

}

function increaseCounter(field)
{
	var str1 = document.getElementById(field);
	if (str1.value == '')
	{
		str1.value = 2;
	}
	else if(str1.value<=99)
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

function decreaseCounter(field)
{
	var str1 = document.getElementById(field);
	var categoryid = document.getElementById('categoryid').value;
	var start;
	if(categoryid == 42)
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
<!-- Added by Pranav Dave to display the message of non recommended combination of Stiched finish with size-->
function nostitchedfinish()
{
	var size_h = document.getElementById('size_h').value;
	var size_w = document.getElementById('size_w').value;
	var sizesqft = size_w*size_h;
	//document.getElementById('sizesqft').value = sizesqft;
	if(document.getElementById('finishoptions')!=null)
	{
		if(sizesqft >= 50 && document.getElementById('finishoptions').value == 'Stitched Finish')
		{
			
			document.getElementById('nostitchedmsg').innerHTML = "Note: Stitched Finish for a banner of size '"+size_w+"x"+size_h+"' is not a recommended option as it might not hold out.";
			document.getElementById('nostitchedmsg').style.clear = "both";
			document.getElementById('nostitchedmsg').style.display = "block";
		
			//window.location.hash = '#nostichedmsg';
			document.getElementById('finishoptions').focus();
			return false;
		}else
		{
			document.getElementById('nostitchedmsg').innerHTML = "";
			document.getElementById('nostitchedmsg').style.display = "none";
			return true;
		}
	}
}

function bannersizechange()
{
	var val = document.getElementById('bannersize').options[document.getElementById('bannersize').selectedIndex].title;
	var dolar = parseFloat(val.substr(1));
	document.getElementById('price').value = dolar;
}

// for clipart
var clipartWindow;

function openClipartWindow(which)
{
	clipartWindow = window.open("clipart.php?clip=" + which,"clipart1","status=yes,resizable=no,scrollbars=yes,height=500,width=535");
	clipartWindow.focus();
}
function setClipart(which, art)
{
	if (which=="1")
	{
		document.getElementById('clipartimg1').src = "images/clipart/" + art;
		document.orderform.clipartimg1name.value = art;
	}

	if (which=="2")
	{
		document.getElementById('clipartimg2').src = "images/clipart/" + art;
		document.orderform.clipartimg2name.value = art;
	}
}

function resetClipart(which)
{
	if (which=="1")
	{
		document.getElementById('clipartimg1').src = "images/clipart_question.gif";
		document.orderform.clipartimg1name.value = "";
	}
	if (which=="2")
	{
		document.getElementById('clipartimg2').src = "images/clipart_question.gif";
		document.orderform.clipartimg2name.value = "";
	}
}

function isDigit (c)
{
	return ((c >= "0") && (c <= "9"));
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
			document.getElementById('logoimage1').disabled = true;
			document.getElementById('logoimage2').disabled = true;
			document.getElementById('logoimage3').disabled = true;
			//window.open('http://dropbox.yousendit.com/BannerBuzz','_blank');
			NewWindow(ysi_link,'BannerbuzzYouSendIt','950','650','0','yes');
			document.getElementById('ysi_id').value = p_id;
			//return false;
		}else{
			document.getElementById('logoimage1').disabled = false;
			document.getElementById('logoimage2').disabled = false;
			document.getElementById('logoimage3').disabled = false;
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
			document.getElementById('logoimage1').disabled = true;
			document.getElementById('logoimage2').disabled = true;
			document.getElementById('logoimage3').disabled = true;
			//window.open('http://dropbox.yousendit.com/BannerBuzz','_blank');
			NewWindow(ysi_link,'BannerbuzzYouSendIt','950','650','0','yes');
			document.getElementById('ysi_id').value = p_id;
			//return false;
		}else{
			document.getElementById('logoimage1').disabled = false;
			document.getElementById('logoimage2').disabled = false;
			document.getElementById('logoimage3').disabled = false;
		}
		return true;
}

function showcustomprint_size()
		{
		   document.getElementById('customprint_size').style.display = 'block';
		}