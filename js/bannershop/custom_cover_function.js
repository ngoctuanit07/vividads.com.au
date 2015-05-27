function premiumwhitesize(subcatid)
{
    if(subcatid == 59)
	{
		var value = document.getElementById('white_table_throw').value;
		if(value == 'customize')
		{
			document.getElementById('customizesize_whitetable').style.display = 'block';
			 var length = document.getElementById('white_height').value; // 2.5
			 var height = document.getElementById('white_width').value; //6
 		}
		else
		{
		   document.getElementById('customizesize_whitetable').style.display = 'none';
		   size = value.split('x');
		   var length = size[0];
		   var height = size[1];
 		 }
 		 //convert in inch
		 length = (length*12) + 58;
		 height = (height*12) + 38;
		 
		  
     }
	else if(subcatid == 60)
	{
		var value = document.getElementById('fullcolor_table_throw').value;
		if(value == 'customize')
		{
			document.getElementById('customizesize_fullcolortable').style.display = 'block';
			 var length = document.getElementById('fullcolor_height').value;
			 var height = document.getElementById('fullcolor_width').value;
		}
		else
		{
		   document.getElementById('customizesize_fullcolortable').style.display = 'none';
		   size = value.split('x');
		   var length = size[0];
		   var height = size[1];
		 }
		  //convert in inch
		 length = (length*12) + 58;
		 height = (height*12) + 38;
		
     }
	else if(subcatid == 61)
	{
		var value = document.getElementById('tabel_runner').value;
		if(value == 'customize')
		{
			document.getElementById('customizesize_tablerunner').style.display = 'block';
			 var length = document.getElementById('runner_width').value;
			 var height = document.getElementById('runner_height').value;
		}
		else
		{
		   document.getElementById('customizesize_tablerunner').style.display = 'none';
		   size = value.split('x');
		   var length = size[0];
		   var height = size[1];
		 }
		 //convert in inch
		 length = (length*12);
		 height = (height*12); 
     } 
 	 
	 
	 getprice(subcatid,length,height);
}


function getprice(subcatid,length,height)
{
  
   var sqft ;
   sqft = (length*height)/144;
   sqft = parseFloat(sqft).toFixed(2);
    if(subcatid == 59)
   { 
       	var qty = document.getElementById('qty').value;
    	var price = sqft * 1.70;
   }
   else if(subcatid == 60)
   {
        var qty = document.getElementById('fullcolor_qty').value;
    	var price = sqft * 2.05;
		 
   }
   else if(subcatid == 61)
   {
        var qty = document.getElementById('runner_qty').value;
     	var price = sqft * 2.25;
   }
    price = price + 10;
    roundNumbertc(subcatid,price,2); 
   
   
   if(qty > 1)
   { 
       quantity(subcatid);
       qty_disc(subcatid);
   }
   else{
	    if(subcatid == 59){ 
			document.getElementById('customizesize_whitetable_discount').style.display = 'none';
			document.getElementById('customizesize_whitetable_discountprice').innerHTML = '';   
		}
		else if(subcatid == 60)
		{
			document.getElementById('customizesize_fullcolor_discount').style.display = 'none';
			document.getElementById('customizesize_fullcolor_discountprice').innerHTML = '';
		}
   }
}

function roundNumbertc(subcatid,number,decimals)
 {
 	
	var newString;// The new rounded number
	decimals = Number(decimals);
	if (decimals < 1) {
		newString = (Math.round(number)).toString();
	} else {
		var numString = number.toString();
		if (numString.lastIndexOf(".") == -1) {// If there is no decimal point
			numString += ".";// give it one at the end
		}
		var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
		var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we'll end up with
		var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
		if (d2 >= 5) {// Do we need to round up at all? If not, the string will just be truncated
			if (d1 == 9 && cutoff > 0) {// If the last digit is 9, find a new cutoff point
				while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
					if (d1 != ".") {
						cutoff -= 1;
						d1 = Number(numString.substring(cutoff,cutoff+1));
					} else {
						cutoff -= 1;
					}
				}
			}
			d1 += 1;
		} 
		if (d1 == 10) {
			numString = numString.substring(0, numString.lastIndexOf("."));
			var roundedNum = Number(numString) + 1;
			newString = roundedNum.toString() + '.';
		} else {
			newString = numString.substring(0,cutoff) + d1.toString();
		}
	}
	if (newString.lastIndexOf(".") == -1) {// Do this again, to the new string
		newString += ".";
	}
	var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
	for(var i=0;i<decimals-decs;i++) newString += "0";
	//var newNumber = Number(newString);// make it a number if you like
	if(newString != 0.00)
	{
	    if(subcatid == 59)
		{
			document.getElementById("total").value = newString;
			document.getElementById("total_display").innerHTML = "$"+newString;
		}
		else if(subcatid == 60)
		{
			document.getElementById("fullcolor_total").value = newString;
			document.getElementById("fullcolor_total_display").innerHTML = "$"+newString;
		}
		else if(subcatid == 61)
		{
			document.getElementById("runner_total").value = newString;
			document.getElementById("runner_total_display").innerHTML = "$"+newString;
		}
	}
	else
	{ 
	    if(subcatid == 59)
		{
			document.getElementById("total").value = 0;
			document.getElementById("total_display").innerHTML = "$0.00";
		}
		else if(subcatid == 60)
		{
			document.getElementById("fullcolor_total").value = 0;
			document.getElementById("fullcolor_total_display").innerHTML = "$0.00";
		}
		else if(subcatid == 61)
		{
			document.getElementById("runner_total").value = 0;
			document.getElementById("runner_total_display").innerHTML = "$0.00";
		}
	} 
	
 }

function roundNumberdc(subcatid,number,decimals)
		{
	var newString;decimals=Number(decimals);
	if(decimals<1)
	{
		newString=(Math.round(number)).toString();
	}
	else
	{
		var numString=number.toString();
		if(numString.lastIndexOf(".")==-1)
		{
			numString+=".";
		}
		var cutoff=numString.lastIndexOf(".")+decimals;
		var d1=Number(numString.substring(cutoff,cutoff+1));
		var d2=Number(numString.substring(cutoff+1,cutoff+2));
		if(d2>=5)
		{
			if(d1==9&&cutoff>0)
			{
				while(cutoff>0&&(d1==9||isNaN(d1)))
				{
					if(d1!=".")
					{
						cutoff-=1;
						d1=Number(numString.substring(cutoff,cutoff+1));
					}
					else
					{
						cutoff-=1;}
					}
				}
				d1+=1;
			}
			if(d1==10)
			{
				numString=numString.substring(0,numString.lastIndexOf("."));
				var roundedNum=Number(numString)+1;
				newString=roundedNum.toString()+'.';
			}
			else
			{
				newString=numString.substring(0,cutoff)+d1.toString();
			}
		}
	if(newString.lastIndexOf(".")==-1)
	{
		newString+=".";
	}
	var decs=(newString.substring(newString.lastIndexOf(".")+1)).length;
	for(var i=0;i<decimals-decs;i++)
		newString+="0";
	if(newString!=0.00)
	{
	    if(subcatid == 59)
		{
			document.getElementById("qty_discount").value=newString;
		}
		else if(subcatid == 60)
		{
			document.getElementById("fullcolor_qty_discount").value=newString;
		}
		else if(subcatid == 61)
		{
			document.getElementById("runner_qty_discount").value=newString;
		}
		
	}
	else
	{
		if(subcatid == 59)
		{
			document.getElementById("qty_discount").value=0;
		}
		else if(subcatid == 60)
		{
			document.getElementById("fullcolor_qty_discount").value=0;
		}
		else if(subcatid == 61)
		{
			document.getElementById("runner_qty_discount").value=0;
		}
		
	}
}


function quantity(subcatid)
		{ 
		    
			var total_value;
			 if(subcatid == 59)
			 {
						var totprices = document.getElementById("total").value;
						var qty = document.getElementById('qty').value;
			 }
			 else if(subcatid == 60)
			 {
					var totprices =	document.getElementById("fullcolor_total").value;
					var qty = document.getElementById('fullcolor_qty').value;
			 }
			 else if(subcatid == 61)
			 {
					var totprices =	document.getElementById("runner_total").value;
					 var qty = document.getElementById('runner_qty').value;
			 }
			
			 
			if(qty != '')
			{
 			    total_value = parseFloat(totprices) * qty;
				roundNumbertc(subcatid,total_value,2);
			}
			
			else{
				 	if(subcatid == 59)
					{
						document.getElementById("total").value = 0;
						document.getElementById("total_display").innerHTML = "$0.00";
					}
					else if(subcatid == 60)
					{
						document.getElementById("fullcolor_total").value = 0;
						document.getElementById("fullcolor_total_display").innerHTML = "$0.00";
					}
					else if(subcatid == 61)
					{
						document.getElementById("runner_total").value = 0;
						document.getElementById("runner_total_display").innerHTML = "$0.00";
					}
			}
		}