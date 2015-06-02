function getXMLHTTP() { //fuction to return the xml http object
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
	
	function reviewhelpful(review_id, optionid, customer_id) {
		var urlv= document.getElementById('reviewparam').value;
		var strURL=urlv+"review_id/"+review_id+"/option/"+optionid+"/customer_id/"+customer_id;
		var req = getXMLHTTP();
		if (req) 
		{
			req.onreadystatechange = function() 
			{
				if (req.readyState == 4) 
				{
					// only if "OK"
					if (req.status == 200) 
					{
						//alert(req.responseText);						
						document.getElementById(review_id).innerHTML=req.responseText;						
					} else 
					{
						alert("There was a problem while using XMLHTTP:\n" + req.statusText);
					}
				}				
			}			
			req.open("GET", strURL, true);
			req.send(null);
		}		
	}
