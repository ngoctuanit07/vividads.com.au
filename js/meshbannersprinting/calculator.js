// JavaScript Document

var $=jQuery.noConflict();
$('#downloads_video_overlay').hide();

$(document).ready(function(e) {
      $('#cartItems').hide();
	/*making default tab selected*/
	$('#customTab').addClass('calculator_selected_tab');
	
	/*updating and hiding the Main Tabs*/
	
	$('#customTab').click(function(){
		$('#customTab').removeClass('calculator_selected_tab');
		$('#standardTab').removeClass('calculator_selected_tab');
		$('#customTab').addClass('calculator_selected_tab');
		$('#left_panel_custom').show();
		$('#left_panel_standard').hide();
		
	   });
	$('#standardTab').click(function(){
		$('#customTab').removeClass('calculator_selected_tab');
		$('#standardTab').removeClass('calculator_selected_tab');
		$('#standardTab').addClass('calculator_selected_tab');
		$('#left_panel_custom').hide();
		$('#left_panel_standard').show();
	    });
	
	/*Handling the counter for the quantity*/
		
	   $('#quantityBox').keypress(function(e){
		  // alert(e.keyCode);
		   if(e.keyCode==38){			   
			   var preVal = e.target.value;
			    document.getElementById('quantityBox').value = parseFloat(preVal)+1;
			    }
		    if(e.keyCode==40){
			    
			    var preVal = e.target.value;
			    preVal =parseFloat(preVal);
			    if( preVal >1){
			    document.getElementById('quantityBox').value = parseFloat(preVal)-1;
			     }
			    }	    
		   });
		   
	 $('#widthBox').keypress(function(e){
		  // alert(e.keyCode);
		   if(e.keyCode==38){			   
			   var preVal = e.target.value;
			    document.getElementById('widthBox').value = parseFloat(preVal)+1;
			    }
		    if(e.keyCode==40){
			    
			    var preVal = e.target.value;
			    preVal =parseFloat(preVal);
			    if( preVal >1){
			    document.getElementById('widthBox').value = parseFloat(preVal)-1;
			     }
			    }	    
		   });
	 $('#heightBox').keypress(function(e){
		  // alert(e.keyCode);
		   if(e.keyCode==38){			   
			   var preVal = e.target.value;
			    document.getElementById('heightBox').value = parseFloat(preVal)+1;
			    }
		    if(e.keyCode==40){
			    
			    var preVal = e.target.value;
			    preVal =parseFloat(preVal);
			    if( preVal >1){
			    document.getElementById('heightBox').value = parseFloat(preVal)-1;
			     }
			    }	    
		   });	
		   
	 /*update the price */
	 
	 $('#quantityBox,#widthBox,#heightBox, #calculateBtn').keyup(function (e){
		 ///calculate the price///
		 calculatePrice();
		 
		 });	   
		   
	$('#calculateBtn').click(function (){
		 calculatePrice();
		});	      	   
	
	/*zooom panel hide /show*/
	$('#printableTemplate').click(function (){
		$('#zoomPanel').show('fade');
		
		});
	
	$('#zoomPanel').click(function(){
		   $('#zoomPanel').hide('fade');
		});
	
	/*dropDownSelect functionality */
	 dropDownSelect();
	});

/*clicking upon category*/
function dropDownSelect(){
	
	/*displaying categories select box*/
	$('#categorySelectBox').click(function(){		
		$('#categories_dropdown_box').show();
		});
	$('.dropDownBoxRow').click(function(){		
			$('#categories_dropdown_box').hide();
			//alert('f');
		});
		
	
	
	/*displaying Products select box*/
		
	$('#productSelectBox').click(function(){		
		$('#products_dropdown_box').show();
		});
	$('.dropDownBoxRow').click(function(){		
			$('#products_dropdown_box').hide();
			//alert('f');
		});
	
	/*displaying products select box*/
	
	$('#unitSelectBox').click(function(){		
		$('#units_dropdown_box').show();
		});
	
	$('.dropDownBoxRow').click(function(){		
			$('#units_dropdown_box').hide();
			//alert('f');
		});
		
	/*displaying Finish Options select box*/
	
	$('#finishSelectBox').click(function(){		
		$('#finish_dropdown_box').show();
		});
	
	$('.dropDownBoxRow').click(function(){		
			$('#finish_dropdown_box').hide();
			//alert('f');
		});	
		
	/*displaying Sewing Options select box*/
	
	$('#sewingSelectBox').click(function(){		
		$('#sewing_dropdown_box').show();
		});
	
	$('.dropDownBoxRow').click(function(){		
			$('#sewing_dropdown_box').hide();
			//alert('f');
		});	
	
	/*displaying fitting Options select box*/
	
	$('#fittingSelectBox').click(function(){		
		$('#fitting_dropdown_box').show();
		});
	
	$('.dropDownBoxRow').click(function(){		
			$('#fitting_dropdown_box').hide();
			//alert('f');
		});	
		
	/*displaying Shipping Options select box*/
	
	$('#shippingSelectBox').click(function(){		
		$('#shipping_dropdown_box').show();
		});
	
	$('.dropDownBoxRow').click(function(){		
			$('#shipping_dropdown_box').hide();
			//alert('f');
		});			
			
	
	}
	
/*function calculatePrice()*/

function calculatePrice(){
	
	/*collecting variables*/
	/*input variables*/	
	var currProduct ='';
	var majorUnit = document.getElementById('majorInput');
	var quantityBox= document.getElementById('quantityBox');
	var widthBox = document.getElementById('widthBox');
	var heightBox = document.getElementById('heightBox');
	var productPrice = document.getElementById('productPrice');
	
	var printableArea = document.getElementById('printableArea');
	var internalBleed = document.getElementById('internalBleed');
	var iletsDistance = document.getElementById('iletsDistance');
	var totalIlets = document.getElementById('totalIlets');
	var discount = document.getElementById('discount');
	
	/*output variables*/
	var priceBox  = document.getElementById('priceBox');
	var saleDiscount = document.getElementById('saleDiscount');
	var bonusDiscount = document.getElementById('bonusDiscount');
	var subTotal = document.getElementById('subTotal');
	
	/*Interacting with variables*/
	var price = parseFloat(productPrice.value);
	var pUnit = majorUnit.value;
	
	var pQty = parseInt(quantityBox.value);
	var pWidth = parseFloat(widthBox.value);
	var pHeight = parseFloat(heightBox.value);	
	//check if unit is in meters
	//if unit is meter
	if(pUnit =='m'){
		var area = pWidth * pHeight;
		totalArea = area * pQty;	
		var sunit = 'sqm';	
		}
	/* if units are centimeter*/	
	if(pUnit =='cm'){
		pWidth = pWidth/100;
		pHeight = pHeight/100;
		var area = pWidth * pHeight;
		totalArea = area * pQty;	
		var sunit = 'sq cm';	
		}
	/* if units are millimeter */	
		
	if(pUnit =='mm'){
		pWidth = pWidth/1000;
		pHeight = pHeight/1000;
		var area = pWidth * pHeight;
		totalArea = area * pQty;	
		var sunit = 'sq mm';	
		}		
	
	nTotalCost =  totalArea * parseFloat(price);
	nDiscount = nTotalCost * 0.05;
	nBonus = nTotalCost * 0.07;
	
	nSubTotal = nTotalCost - nDiscount-nBonus;
	
	priceBox.value = '$'+nTotalCost.toFixed(2);
	saleDiscount.value = '$'+nDiscount.toFixed(2);
	bonusDiscount.value = '$'+nBonus.toFixed(2);
	subTotal.value  = '$'+nSubTotal.toFixed(2); 
	
	}

	
///function changeCategory(val)///
/*for fetching the products for updated category*/

function changeCategory(categoryId){
	
	var c_category_obj = categoryId.split(':'); 
	var category_id = c_category_obj[0];
	var category_name = c_category_obj[1];
	
	document.getElementById('categoryInput').value=category_id;
	document.getElementById('categoryLabel').innerHTML=category_name;
	 
 }

///function changeProduct(val)///
/*for fetching other options for updated product*/

function changeProduct(productId){
	
	var c_product_obj = productId.split(':'); 
	var product_id = c_product_obj[0];
	var product_name = c_product_obj[1];
	
	document.getElementById('productInput').value=product_id;
	document.getElementById('productLabel').innerHTML=product_name;
	 
 }
 
 
 
///function changeUnit(val)///
/*for fetching other options for updated Units*/

function changeUnit(unitId){
	
	var c_unit_obj = unitId.split(':'); 
	var unit_id = c_unit_obj[0];
	var unit_name = c_unit_obj[1];
	/*IMPORTANT getting first value of majorUnit*/
	var prevUnit = document.getElementById('majorInput').value;
	
	document.getElementById('majorInput').value = unit_id;
	document.getElementById('unitLabel').innerHTML = unit_name;
	document.getElementById('unitLabelA1').innerHTML=unit_id;
	document.getElementById('unitLabelA3').innerHTML=unit_id;
	document.getElementById('unitLabelA4').innerHTML=unit_id;
	var pWidth = document.getElementById('widthBox').value;
	var pHeight = document.getElementById('heightBox').value;
	
	
	if(unit_id =='m'){
		/*if prevous unit were meters then*/
		if(prevUnit=='m'){
			nWidth = pWidth;
			nHeight = pHeight;
			
			}
		/*if prevous Unit were cm*/
		if(prevUnit == 'cm'){
			nWidth = pWidth /100;
			nHeight = pHeight /100;
			
			}
		/*if prevous Unit were mm*/
		if(prevUnit =='mm'){
			nWidth = pWidth/1000;
			nHeight = pHeight/1000;
			
			}
				
		//alert(prevUnit);
		document.getElementById('widthBox').value = nWidth.toFixed(1);
		document.getElementById('heightBox').value=nHeight.toFixed(1);
		}
	
	if(unit_id =='cm'){
		//alert(prevUnit);
		/*if prevous unit were meters then*/
		if(prevUnit=='m'){
			nWidth = pWidth*100;
			nHeight = pHeight*100;
			
			}
		/*if prevous Unit were cm*/
		if(prevUnit == 'cm'){
			nWidth = pWidth;
			nHeight = pHeight;
			
			}
		/*if prevous Unit were mm*/
		if(prevUnit =='mm'){
			nWidth = pWidth/10;
			nHeight = pHeight/10;
			
			}
		document.getElementById('widthBox').value = nWidth.toFixed(1);
		document.getElementById('heightBox').value=nHeight.toFixed(1);
		}	
	 if(unit_id =='mm'){
		 /*if prevous unit were meters then*/
		if(prevUnit=='m'){
			nWidth = pWidth*1000;
			nHeight = pHeight*1000;
			
			}
		/*if prevous Unit were cm*/
		if(prevUnit == 'cm'){
			nWidth = pWidth *10;
			nHeight = pHeight *10;
			
			}
		/*if prevous Unit were mm*/
		if(prevUnit =='mm'){
			nWidth = pWidth;
			nHeight = pHeight;
			
			}
		// alert(prevUnit);
			document.getElementById('widthBox').value = nWidth.toFixed(1);
			document.getElementById('heightBox').value= nHeight.toFixed(1);
		}
		
		 
 } 
 
 /*function for updating the finish options*/
 
 function changeFinishing(finishId){
	 
	 var c_finish_obj = finishId.split(':'); 
	var finish_id = c_finish_obj[0];
	var finish_name = c_finish_obj[1];
	
	document.getElementById('finishInput').value=finish_id;
	document.getElementById('finishLabel').innerHTML=finish_name;
	 }
	
/*function changeSewing for updating the sewing section*/

function changeSewing(sewingId){
	 
	 var c_sewing_obj = sewingId.split(':'); 
	var sewing_id = c_sewing_obj[0];
	var sewing_name = c_sewing_obj[1];
	
	document.getElementById('sewingInput').value=sewing_id;
	document.getElementById('sewingLabel').innerHTML=sewing_name;
	 }
/*function changeFitting for updating the Fitting section*/

function changeFitting(fittingId){
	 
	 var c_fitting_obj = fittingId.split(':'); 
	var fitting_id = c_fitting_obj[0];
	var fitting_name = c_fitting_obj[1];
	
	document.getElementById('fittingInput').value=fitting_id;
	document.getElementById('fittingLabel').innerHTML=fitting_name;
	 }	 
/*function changeShipping for updating the Shipping section*/

function changeShipping(shippingId){
	 
	var c_shipping_obj = shippingId.split(':'); 
	var shipping_id = c_shipping_obj[0];
	var shipping_name = c_shipping_obj[1];
	
	document.getElementById('shippingInput').value=shipping_id;
	document.getElementById('shippingLabel').innerHTML=shipping_name;
	 }	 	 
	