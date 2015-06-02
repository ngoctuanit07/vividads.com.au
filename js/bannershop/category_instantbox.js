function roundNumbertc(number, decimals) {
     if (document.getElementById("product").value != '') {
        var newString;
        decimals = Number(decimals);
        if (decimals < 1) {
            newString = (Math.round(number)).toString();
        } else {
            var numString = number.toString();
            if (numString.lastIndexOf(".") == -1) {
                numString += ".";
            }
            var cutoff = numString.lastIndexOf(".") + decimals;
            var d1 = Number(numString.substring(cutoff, cutoff + 1));
            var d2 = Number(numString.substring(cutoff + 1, cutoff + 2));
            if (d2 >= 5) {
                if (d1 == 9 && cutoff > 0) {
                    while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
                        if (d1 != ".") {
                            cutoff -= 1;
                            d1 = Number(numString.substring(cutoff, cutoff + 1));
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
                newString = numString.substring(0, cutoff) + d1.toString();
            }
        }
        if (newString.lastIndexOf(".") == -1) {
            newString += ".";
        }
         var decs = (newString.substring(newString.lastIndexOf(".") + 1)).length;
        for (var i = 0; i < decimals - decs; i++) newString += "0";
         if (newString != 0.00) {
            document.getElementById("total").value = newString;
            document.getElementById("total_display").innerHTML = "$" + newString;
        } else {
            document.getElementById("total").value = 0;
            document.getElementById("total_display").innerHTML = "$0.00";
        }
    } else {
        document.getElementById("total_display").innerHTML = "$0.00";
    }
}function roundNumbergt(number, decimals) {
	
	var number1 = number.toFixed(10);
	number1 = number1.toString();
	number = number1.substring(0, number1.length-8);
	
      if (document.getElementById("product").value != '') {
        var newString;
        decimals = Number(decimals);
        if (decimals < 1) {
            newString = (Math.round(number)).toString();
        } else {
            var numString = number.toString();
            if (numString.lastIndexOf(".") == -1) {
                numString += ".";
            }
            var cutoff = numString.lastIndexOf(".") + decimals;
            var d1 = Number(numString.substring(cutoff, cutoff + 1));
            var d2 = Number(numString.substring(cutoff + 1, cutoff + 2));
            if (d2 >= 5) {
                if (d1 == 9 && cutoff > 0) {
                    while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
                        if (d1 != ".") {
                            cutoff -= 1;
                            d1 = Number(numString.substring(cutoff, cutoff + 1));
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
                newString = numString.substring(0, cutoff) + d1.toString();
            }
        }
        if (newString.lastIndexOf(".") == -1) {
            newString += ".";
        }
		 
        var decs = (newString.substring(newString.lastIndexOf(".") + 1)).length;
        for (var i = 0; i < decimals - decs; i++)
        newString += "0";
        if (newString != 0.00 || newString != '') {
            var grandtot = "TOTAL PRICE : $" + newString;
            document.getElementById('grand_total').value = newString;
            jQuery("#grandtotaldisplay").html("TOTAL PRICE : $" + newString);
        } else {
            document.getElementById("grand_total").value = 0;
            document.getElementById("total_display").innerHTML = "$0.00";
            document.getElementById("grandtotaldisplay").innerHTML = "TOTAL PRICE : $0.00";
        }
    } else {
        document.getElementById("total_display").innerHTML = "$0.00";
        document.getElementById("shipping_display").innerHTML = "$0.00";
    }
}function roundNumbersc(number, decimals) {
    var newString;
    decimals = Number(decimals);
    if (decimals < 1) {
        newString = (Math.round(number)).toString();
    } else {
        var numString = number.toString();
        if (numString.lastIndexOf(".") == -1) {
            numString += ".";
        }
        var cutoff = numString.lastIndexOf(".") + decimals;
        var d1 = Number(numString.substring(cutoff, cutoff + 1));
        var d2 = Number(numString.substring(cutoff + 1, cutoff + 2));
        if (d2 >= 5) {
            if (d1 == 9 && cutoff > 0) {
                while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
                    if (d1 != ".") {
                        cutoff -= 1;
                        d1 = Number(numString.substring(cutoff, cutoff + 1));
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
            newString = numString.substring(0, cutoff) + d1.toString();
        }
    }
    if (newString.lastIndexOf(".") == -1) {
        newString += ".";
    }
    var decs = (newString.substring(newString.lastIndexOf(".") + 1)).length;
    for (var i = 0; i < decimals - decs; i++)
    newString += "0";
    if (newString != 0.00) {
        document.getElementById("shipping").value = newString;
        document.getElementById("shipping_display").innerHTML = "$" + newString;
    } else {
        document.getElementById("shipping").value = 0;
        document.getElementById("shipping_display").innerHTML = "$0.00";
    }
}function roundNumberdc(number, decimals) {
    var newString;
    decimals = Number(decimals);
    if (decimals < 1) {
        newString = (Math.round(number)).toString();
    } else {
        var numString = number.toString();
        if (numString.lastIndexOf(".") == -1) {
            numString += ".";
        }
        var cutoff = numString.lastIndexOf(".") + decimals;
        var d1 = Number(numString.substring(cutoff, cutoff + 1));
        var d2 = Number(numString.substring(cutoff + 1, cutoff + 2));
        if (d2 >= 5) {
            if (d1 == 9 && cutoff > 0) {
                while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
                    if (d1 != ".") {
                        cutoff -= 1;
                        d1 = Number(numString.substring(cutoff, cutoff + 1));
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
            newString = numString.substring(0, cutoff) + d1.toString();
        }
    }
    if (newString.lastIndexOf(".") == -1) {
        newString += ".";
    }
    var decs = (newString.substring(newString.lastIndexOf(".") + 1)).length;
    for (var i = 0; i < decimals - decs; i++) newString += "0";
    if (newString != 0.00) {
        document.getElementById("qty_discount").value = newString;
    } else {
        document.getElementById("qty_discount").value = 0;
    }
}
function increaseCounter(field) {
    if (document.getElementById("product").value == '') {
        alert("Please Select Product.");
        document.getElementById('product').focus();
        return false;
    }
    var str1 = document.getElementById(field);
    var major = document.getElementById('major').value;
	if(major == "in")
	{
		var limit = 1200;
	}
	else
	{
		var limit = 100;	
	}
    if (str1.value == '') {
        str1.value = 2;
    } else if (str1.value <= limit-1) {
        if (isFloat(eval(str1.value) + 1)) {
            str1.value = (eval(str1.value) + 1).toFixed(2);
        } else {
            str1.value = eval(str1.value) + 1;
        }
    }
}

function sidebannerprice() {
    if (document.getElementById("product").value == '') {
        alert("Please Select Product.");
        document.getElementById('product').focus();
        return false;
    }
    var total_value;
    if (document.getElementById('side').checked) {
        if (document.getElementById('qty').value != '') {
            document.getElementById('side').value = "on";
			 
            total_value = parseInt(document.getElementById('price').value) * document.getElementById('qty').value * 1.75;
            roundNumbertc(total_value, 2);
        } else {
            document.getElementById('total').value = 0;
            document.getElementById("total_display").innerHTML = "$0.00";
        }
    } else {
        if (document.getElementById('qty').value != '') {
            document.getElementById('side').value = "";
            total_value = parseInt(document.getElementById('price').value) * document.getElementById('qty').value;
            roundNumbertc(total_value, 2);
        } else {
            document.getElementById('side').value = "";
            document.getElementById('total').value = 0;
            document.getElementById("total_display").innerHTML = "$0.00";
        }
    }
}
function quantity() {
    if (document.getElementById("product").value == '') {
        alert("Please Select Product.");
        document.getElementById('product').focus();
        return false;
    }
    if (document.getElementById("shipping_method").value == '') {
        document.getElementById("shipping").value = 0;
        document.getElementById("shipping_display").innerHTML = "$0.00";
    }
    var total_value;
	
    if (document.getElementById('qty').value != '') {
        if (document.getElementById('qty').value == "More") {
			document.getElementById("QtyTextBox").style.display = "block";
			
             total_value = parseInt(document.getElementById('price').value) * document.getElementById('qtyText').value;
              var qnty = document.getElementById('qtyText').value;
             roundNumbertc(total_value, 2);
        } else {
            document.getElementById("QtyTextBox").style.display = "none";
            total_value = (document.getElementById('price').value) * (document.getElementById('qty').value);
            var qnty = document.getElementById('qty').value;
            roundNumbertc(total_value, 2);
        }
    } else {
        document.getElementById('total').value = 0;
        document.getElementById("total_display").innerHTML = "$0.00";
    }
    
    
	
}

