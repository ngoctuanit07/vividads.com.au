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
	
function getBillingState(strURL)
{	
	var req = getXMLHTTP();		
	if (req) 
	{ 
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
			{
				if (req.status == 200)
				{
					if(req.responseText != 0)
					{ 
						document.getElementById("billing_state").innerHTML = req.responseText;
					}
				}
			}
		 }
		 req.open("GET", strURL, true);
		 req.send(null);
	}			
}


function getShippingState(strURL)
{	
	var req = getXMLHTTP();		
	if (req) 
	{ 
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
			{
				if (req.status == 200)
				{
					if(req.responseText != 0)
					{ 
						document.getElementById("shipping_state").innerHTML = req.responseText;
					}
				}
			}
		 }
		 req.open("GET", strURL, true);
		 req.send(null);
	}			
}