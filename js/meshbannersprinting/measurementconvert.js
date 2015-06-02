function measurementConvert(inputtype, outputtype, inputnum) {

	var output=0;
	var decimalplaces;

	// inputtype = "mm", "cm", "m", "in", "ft", "yd"
	// outputtype = "mm", "cm", "m", "in", "ft", "yd"
	// inputnum = The number you wish to convert
	// decimalplaces = The number of decimal places you want on the output
	
		
	switch (inputtype) {

			case "mm": 
				switch (outputtype) {
				
					case "mm":
						output = inputnum * 1;
						decimalplaces = 0;
						break;
				
					case "cm":
						output = inputnum / 10;
						decimalplaces = 1;
						break;
						
					case "m":
						output = inputnum / 1000;
						decimalplaces = 2;
						break;
						
					case "in":
						output = inputnum / 25.4;
						decimalplaces = 0;
						break;
						
					case "ft":
						output = inputnum / 304.8;
						decimalplaces = 0;
						break;
					
					case "yd":
						output = inputnum / 914.41112;
						decimalplaces = 0;
						break;						
					
				}		
			var n = output.toFixed(decimalplaces);
			return n;
			break;


			case "cm":
				switch (outputtype) {

					case "mm":
						output = inputnum * 10;
						decimalplaces = 0;
						break;

					case "cm":
						output = inputnum * 1;
						decimalplaces = 1;
						break;

					case "m":
						output = inputnum / 100;
						decimalplaces = 2;
						break;
						
					case "in":
						output = inputnum / 2.54;
						decimalplaces = 0;
						break;

					case "ft":
						output = inputnum / 30.48;
						decimalplaces = 0;
						break;										
					
					case "yd":
						output = inputnum / 91.441112;
						decimalplaces = 0;
						break;
											
				}
				
			var n = output.toFixed(decimalplaces);
			return n;
			break;


			case "m":
				switch (outputtype) {

					case "mm":
						output = inputnum * 1000;
						decimalplaces = 0;
						break;

					case "cm":
						output = inputnum * 100;
						decimalplaces = 1;
						break;

					case "m":
						output = inputnum * 1;
						decimalplaces = 2;
						break;

					case "in":
						output = inputnum / 0.0254;
						decimalplaces = 0;
						break;

					case "ft":
						output = inputnum / 0.3048;
						decimalplaces = 0;
						break;					
					
					case "yd":
						output = inputnum / 0.91441112;
						decimalplaces = 0;
						break;
											
				}
			
			var n = output.toFixed(decimalplaces);
			return n;
			break;


			case "in":
				switch (outputtype) {

					case "mm":
						output = inputnum * 25.4;
						decimalplaces = 0;
						break;

					case "cm":
						output = inputnum * 2.54;
						decimalplaces = 1;
						break;

					case "m":
						output = inputnum * 0.0254;
						decimalplaces = 2;
						break;

					case "in":
						output = inputnum * 1;
						decimalplaces = 0;
						break;

					case "ft":
						output = inputnum / 12;
						decimalplaces = 0;
						break;					
					
					case "yd":
						output = inputnum / 36;
						decimalplaces = 0;
						break;
											
				}
			
			var n = output.toFixed(decimalplaces);
			return n;
			break;


			case "ft":
				switch (outputtype) {

					case "mm":
						output = inputnum * 304.8;
						decimalplaces = 0;
						break;

					case "cm":
						output = inputnum * 30.48;
						decimalplaces = 1;
						break;

					case "m":
						output = inputnum * 0.3048;
						decimalplaces = 2;
						break;

					case "in":
						output = inputnum * 12;
						decimalplaces = 0;
						break;

					case "ft":
						output = inputnum * 1;
						decimalplaces = 0;
						break;					
					
					case "yd":
						output = inputnum / 3;
						decimalplaces = 0;
						break;
											
				}
			
			var n = output.toFixed(decimalplaces);
			return n;
			break;


			case "yd":
				switch (outputtype) {

					case "mm":
						output = inputnum * 914.41112;
						decimalplaces = 0;
						break;

					case "cm":
						output = inputnum * 91.441112;
						decimalplaces = 1;
						break;

					case "m":
						output = inputnum * 0.91441112;
						decimalplaces = 2;
						break;

					case "in":
						output = inputnum * 36;
						decimalplaces = 0;
						break;

					case "ft":
						output = inputnum * 3;
						decimalplaces = 0;
						break;					
					
					case "yd":
						output = inputnum * 1;
						decimalplaces = 0;
						break;
											
				}

			var n = output.toFixed(decimalplaces);
			return n;
			break;

			default: 
				return inputnum;
			 break;
		}//end of switch
}
