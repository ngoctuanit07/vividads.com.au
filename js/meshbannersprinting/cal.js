$(document).ready(function() {
	
	$('#countries').dropkick({
		change: function (value) {
			window.open(value, '_self');
		}
	});
	
	setTimeout(function () {
		displaypriceinfo()
	},500);
	
	var currmeasure = $("input#convert").val();
	resetfields();
	
	if($("input#calmode").val() == "0") {
		changetab(0);
	}
	else if($("input#calmode").val() == "1") {
		changetab(1);
	}
	
	$("#moreButton").bind('mouseover',function(){
        $('#morePopup').css('display','block');
	});
	
	$("#morePopup").bind('mouseout',function(){
		$(this).css('display','none');
	});

	$('#quantity').keyup(function(e){
		displaypriceinfo();
	});
	
	$('#width').keyup(function(e){
		displaypriceinfo();
	});
	
	$('#height').keyup(function(e){
		displaypriceinfo();
	});

	$("#btncalculate").click(function() {
	  displaypriceinfo();
	});

	$("#btnAddQuote").click(function() {
		drawTable();
	});

	$("#btnSubmit").click(function() {
		checkCalc();
	});

	$(".newquote").click(function(e) {
		e.preventDefault();
		$this=$(this);
		$.post("clearsession.php", function(data) {
				window.location="calculator.php?m="+$("input#calmode").val();
		});
	})

	$("#btnreset").click(function() {
		var curprd = $("input#materialid").val();
		document.getElementById("width").value = "";
		document.getElementById("height").value = "";
		document.getElementById("quantity").value = 1;
		showfinish(curprd);
		showeyeletspace(0,1);
		document.getElementById("origprice").value = $defaultcurrsymbol+"0.00";
		document.getElementById("saleprice").value = $defaultcurrsymbol+"0.00";
		document.getElementById("proddisc").value = $defaultcurrsymbol+"0.00";
		document.getElementById("subtotalprice").value = $defaultcurrsymbol+"0.00";
		$(".stdqty").val('');
		return false;
	});

	$('#displayeyespace_classic').ddslick({
		width: 260,
		onSelected: function(data){
			document.getElementById("eyespaceid").value = data.selectedData.value;
			displaypriceinfo();
		}
	});

	$('#displayeyespace_metric').ddslick({
		width: 260,
		onSelected: function(data){
			document.getElementById("eyespaceid").value = data.selectedData.value;
			displaypriceinfo();
		}
	});
	
	$('#displayeyelets').ddslick({
		width: 260,
		truncateDescription: true,
		onSelected: function(data){
			document.getElementById("eyeletsid").value = data.selectedData.value;
			showeyeletspace($("input#materialid").val(),data.selectedData.value);
			displaypriceinfo();
		}
	});
	
	$('#displaysewcolor').ddslick({
		width: 260,
		onSelected: function(data){
			document.getElementById("sewid").value = data.selectedData.value;
		}
	});
	
	$('.stdsewcolor').dropkick({
		 change: function (value, label) {
			$("input#sewid").val(value);
			$thisparent=$(this).parents(".lineitem-container");
			selectStdProd($thisparent.find("input.hid_codeid").val());
		}
	});
	
	$('#subopt2_metric').ddslick({
		width: 95,
		onSelected: function(data){
			document.getElementById("subopt2_hid").value = data.selectedData.value;
			displaypriceinfo();
		}
	});
	
	$('#subopt2_classic').ddslick({
		width: 95,
		onSelected: function(data){
			document.getElementById("subopt2_hid").value = data.selectedData.value;
			displaypriceinfo();
		}
	});
	
	$('#subopt1_metric').ddslick({
		width: 95,
		onSelected: function(data){
			document.getElementById("subopt1_hid").value = data.selectedData.value;
			displaypriceinfo();
		}
	});
	
	$('#subopt1_classic').ddslick({
		width: 95,
		onSelected: function(data){
			document.getElementById("subopt1_hid").value = data.selectedData.value;
			displaypriceinfo();
		}
	});

	$('#displayfinish').ddslick({
		width: 260,
		truncateDescription: true,
		onSelected: function(data){
			document.getElementById("finishid").value = data.selectedData.value;
			showsubopt(data.selectedData.value);
			showeyelets(data.selectedData.value);
			showsewcolor(data.selectedData.value);
			displaypriceinfo();
		}
	});
	
	$('#convertdrop').ddslick({
		width: 260,
		onSelected: function(data){
			var newmeasure = data.selectedData.value;
			$("input#convert").val(newmeasure);
			$(".lblunit").html(newmeasure);

			var newwidth = measurementConvert(currmeasure, newmeasure, $("#width").val());
			var newheight = measurementConvert(currmeasure, newmeasure, $("#height").val());
			currmeasure = $("#convert").val();
			
			if($("#calmode").val() == "0") {
				if(newwidth != 0) { $("#width").val(newwidth); }
				if(newheight != 0) { $("#height").val(newheight); }
					showsubopt($("#finishid").val());
					showeyeletspace($("#materialid").val(),$("#eyeletsid").val())
			}
		
			$("span.stdwidth").each(function(index) {
				var $thisparent=$(this).parents(".lineitem-container");
				var thisval=$(this).html();
				var oriwidth=$thisparent.find(".hid_stdoriwidth").val();
				var newunit=measurementConvert("mm", newmeasure, oriwidth);
				if(newmeasure == "ft") {
					$(this).html(newunit+"\'"+newmeasure);
				}
				else if(newmeasure == "in") {
					$(this).html(newunit+"\""+newmeasure);
				}
				else {
					$(this).html(newunit+newmeasure);
				}
			});
			
			$("span.stdheight").each(function(index) {
				var $thisparent=$(this).parents(".lineitem-container");
				var thisval=$(this).html();
				var oriwidth=$thisparent.find(".hid_stdoriheight").val();
				var newunit=measurementConvert("mm", newmeasure, oriwidth);
				if(newmeasure == "ft") {
					$(this).html(newunit+"\'"+newmeasure);
				}
				else if(newmeasure == "in") {
					$(this).html(newunit+"\""+newmeasure);
				}
				else {
					$(this).html(newunit+newmeasure);
				}
			});
			
			$("span.stdspacename").each(function(index) {
				var $thisparent=$(this).parents(".lineitem-container");
				if(newmeasure == "ft" || newmeasure == "in") { 
					var thisval=$(this).html();
					if(thisval != "") {
						var orispace=$thisparent.find(".hid_stdorispace").val();
						var numb = orispace.match(/\d/g);
						orispace = numb.join("");
						var newunit=measurementConvert("mm", "in", orispace);
						$(this).html(newunit+"\"in");
					}
				}
				else {
					var orispace=$thisparent.find(".hid_stdorispace").val();
					$(this).html(orispace);
				}
			});
				displaypriceinfo();
	
			}
		});
	

	$('#material').ddslick({
		width: 260,
		onSelected: function(data){
			var prodid=data.selectedData.value;
			var stdprodid = $("#stdprodid").val();
			var calmode = $("#calmode").val();
			
			
			if(calmode == "1") {
				resetfields();
				showstdprods(prodid);
				$("#materialid").val(prodid);
			}
			else {
				$("#materialid").val(prodid);
				$(".lngdes").hide();
				$("#lngdes_"+data.selectedData.value).show();
				showfinish(data.selectedData.value);
				showeyeletspace(data.selectedData.value,1);
				showbonusdiscnt(0,prodid,0);
				displaypriceinfo();
			}
			
    	}
	});
	/************************** Download Template and Download Info Sheet. ***************************************/
$(document).on('click','.dwntemplt', function(e) {
	if($(this).attr("href") == "#") {
		e.preventDefault();
		var $this=$(this);
		var itemindx=$(this).find("span.dwnlinkindex").html();

		var $thisparent=$this.parents("tr.tblinfolinks");

					
		$.ajax({
				type: "POST",
				url: "inc/generatetemplate.php",
				data: "itemindex="+itemindx,
				beforeSend: function () {
					$this.hide();
					$thisparent.find("#dwntemplticon_"+itemindx).show();
					$thisparent.find("#dwntemplticon_"+itemindx).html("<img src='ticons/anim-loading.gif' />&nbsp;Generating... Please wait");
				},
				success: function (data) {
					if(data != "") {
						$thisparent.find("#dwntemplticon_"+itemindx).hide();
						$this.show();
						$this.html("2. Easy Template");
						$this.attr("href","download.php?file=fileserver/mos/"+data);
						$.fileDownload("download.php?file=fileserver/mos/"+data);
					}
					else {
						$this.show();
						$this.html("2. Easy Template");
					}
				}
		 });//end of ajax
		}
	});//end of click
	
	$(document).on('click','.dwninfotmpt', function(e) {
	if($(this).attr("href") == "#") {
		e.preventDefault();
		var $this=$(this);
		var itemindex=$(this).find("span.itemind").html();
		
		var $thisparent=$this.parents("tr.tblinfolinks");

					
		$.ajax({
				type: "POST",
				url: "inc/infogenerate.php",
				data: "ind="+itemindex+"&mode=1",
				beforeSend: function () {
					$this.hide();
					$thisparent.find("#dwninfotemplticon_"+itemindex).show();
					$thisparent.find("#dwninfotemplticon_"+itemindex).html("<img src='ticons/anim-loading.gif' />&nbsp;Generating... Please wait");
				},
				success: function (data) {
					if(data != "") {
						$thisparent.find("#dwninfotemplticon_"+itemindex).hide();
						$this.show();
						$this.html("1. Info Sheet");
						$this.attr("href","download.php?file=fileserver/mos/"+data);
						$.fileDownload("download.php?file=fileserver/mos/"+data);
					}
					else {
						$this.show();
						$this.html("1. Info Sheet");
					}
				}
		 });//end of ajax
		}
	});//end of click
		/************************** END Download Template and Download Info Sheet. ***************************************/

	function getSelectedValue(id) {
		return $("#" + id).find("dt a span.value").html();
	}

function showfinish(prdid) {
	var finarr=prdfinarray[prdid];
	$("#displayfinish ul li").hide();
	var defaultval=showelements("#displayfinish",finarr,"finishid");
	$("#finsec").show();
	showsubopt(defaultval);
	showeyelets(defaultval);
	showsewcolor(defaultval);
}

function showsewcolor (finid) {
	var sewcolorarr=sewarr[finid];
	$("#lblsewcolor").html("&nbsp;");
	$("#sewcolorsec").hide();
	$("input#sewid").val(0);
	$("#sewinfo").hide();
	
	if(sewcolorarr.length > 0) {
		if(sewcolorarr[0] != 0) {
			$("#lblsewcolor").html("Sewing");
			$("#displaysewcolor ul li").hide();
			showelements("#displaysewcolor",sewcolorarr,"sewid");
			$("#sewcolorsec").show();
			$("#sewinfo").show();
		}
		else {
			$("#lblsewcolor").html("&nbsp;");
			$("#sewcolorsec").hide();
			$("input#sewid").val(0);
			$("#sewinfo").hide();
		}
	}
	
}

function showsubopt(finid) {
	var suboptarr=finsubopt[finid];
	var convert=$("#convert").val();
	var visidiv1,visidiv2;
	
	$("#lblsubopt1").html("&nbsp;");
	$("#lblsubopt2").html("&nbsp;");
	$("#subopt1sec").hide();
	$("#subopt2sec").hide();
	$("input#subopt1_hid").val(0);
	$("input#subopt2_hid").val(0);
	
	if(convert == "in" || convert == "ft") {
		$("#subopt1_metric_span").hide();
		$("#subopt2_metric_span").hide();
		$("#subopt1_classic_span").show();
		$("#subopt2_classic_span").show();
		visidiv1 ="#subopt1_classic";
		visidiv2 = "#subopt2_classic";
	}
	else {
		$("#subopt1_metric_span").show();
		$("#subopt2_metric_span").show();
		$("#subopt1_classic_span").hide();
		$("#subopt2_classic_span").hide();
		visidiv1 ="#subopt1_metric";
		visidiv2 ="#subopt2_metric";
	}
	
	
	if(suboptarr.length == 2) {
		if(suboptarr[0] != 0) {
			$("#lblsubopt1").html(suboptarr[0][0]);
			$(visidiv1+" ul li").hide();
			showelements(visidiv1,suboptarr[0][1],"subopt1_hid");
			$("#subopt1sec").show();
			
			if(suboptarr[1] != 0) {
				$("#lblsubopt2").html(suboptarr[1][0]);
				$(visidiv2+" ul li").hide();
				showelements(visidiv2,suboptarr[1][1],"subopt2_hid");
				$("#subopt2sec").show();
			}//subopt2 array
		}//subopt1 array
	}//suboptarr length
}//end of showsubopt

function showeyelets(finid) {
	var eyearr=fineyelets[finid];
	$("#lbleyelets").html("&nbsp;");
	$("#eyeletsec").hide();
	$("input#eyeletsid").val(1);
	$("#eyeletinfo").hide();
	
	if(eyearr.length > 0) {
		if(eyearr[0] != 0) {
			$("#lbleyelets").html("Fittings");
			$("#displayeyelets ul li").hide();
			showelements("#displayeyelets",eyearr,"eyeletsid");
			$("#eyeletsec").show();
			$("#eyeletinfo").show();
			showeyeletspace(0,1);
		}
		else {
			$("#lbleyelets").html("&nbsp;");
			$("#eyeletsec").hide();
			$("input#eyeletsid").val(1);
			$("#lbleyespace").html("&nbsp;");
			$("#eyeletspacesec").hide();
			$("input#eyespaceid").val(0);
			$("#eyeletinfo").hide();
		}
	}
}

function showelements(jobj,arr,hid) {
$default=$(jobj+" ul li#"+arr[0]);
$(jobj+" a.dd-selected").html($default.find("a.dd-option").html());
var defaultval=$default.find("input.dd-option-value").val();
$(jobj).find("input.dd-selected-value").val(defaultval);
$(jobj+" a.dd-selected").find("input.dd-option-value").remove();
$default.find("a.dd-option").addClass("dd-option-selected");

	for(i=0;i<arr.length;i++) {
		$(jobj+" ul li#"+arr[i]).show();
	}
	$("input#"+hid).val(defaultval);
	displaypriceinfo();
	return defaultval;
}


function showeyeletspace(prodid,eyeid) {
	var convert=$("#convert").val();
	var visidiv;
	if(convert == "in" || convert == "ft") {
		$("#displayeyespace_metric").hide();
		$("#displayeyespace_classic").show();
		visidiv ="#displayeyespace_classic";
	}
	else {
		$("#displayeyespace_metric").show();
		$("#displayeyespace_classic").hide();
		visidiv ="#displayeyespace_metric";
	}
	
  if(prodid > 1) {
	var eyespacearr=eyespace[prodid];
	$("#lbleyespace").html("&nbsp;");
	$("#eyeletspacesec").hide();
	$("input#eyespaceid").val(0);
	
	if(eyespacearr.length > 0) {
		if(eyespacearr[0] != 0) {
			var isshweyespace = shweyespace[eyeid];
			if(isshweyespace[0]) {
				$("#eyeletspacesec").show();
				$("#lbleyespace").html("Spacing");
				$(visidiv+" ul li").hide();
				showelements(visidiv,eyespacearr,"eyespaceid");
				$("#eyeletspacesec").show();
			}
			else {
				$("#lbleyespace").html("&nbsp;");
				$("#eyeletspacesec").hide();
			}
		}
		else {
			$("#lbleyespace").html("&nbsp;");
			$("#eyeletspacesec").hide();
		}
	}//end of if eyespacearr.length >0
	else {
		$("#lbleyespace").html("&nbsp;");
		$("#eyeletspacesec").hide();
	}
  }//end of if prodid > 1
  else {
	  	 $("#lbleyespace").html("&nbsp;");
		 $("#eyeletspacesec").hide();
  }
}

});//end of document ready



function isNumberKey(evt,obj) {
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	//alert(charCode);
	if (charCode > 31 && (charCode < 46 || charCode > 57)) {
		return false;
	}
	else if(charCode== 48) {
		var tot=(obj.value)+0;
		obj.value=tot;
		return false;
	}
	/*else if(charCode == 8) {
		return false;
	}
	else if(obj.value.charAt(0) == 0) {
		obj.value="1";
	}*/
	return true;
}

function doKeyUpValidation(text) {
    var validationRegex = /[1-9]/g;
    if (!validationRegex.test(text.value)) {
        alert('Please enter only numbers.');
    }
}

function disableinput() {
	return false;
}


function changetab(calmode) {
	var material=$("#materialid").val();
	if(calmode == 0) {
		$("#tab-custom").attr('class', 'tab-custom-selected');
		$("#tab-standard").attr('class', 'tab-standard-deselected');
		$("#calc-top").attr('class', 'calc-top-green');
		$("#calctophigh").attr('class', 'calctophigh-green');
		$("#calctopdrop").attr('class', 'calctopdrop-green');
		$("#custom-fields").show();
		$("#standard-fields").hide();
		$("#calmode").val(calmode);
		showbonusdiscnt(0,material,0);
	} else if(calmode == 1) {
		$("#tab-custom").attr('class', 'tab-custom-deselected');
		$("#tab-standard").attr('class', 'tab-standard-selected');
		$("#calc-top").attr('class', 'calc-top-lightgreen');
		$("#calctophigh").attr('class', 'calctophigh-lightgreen');
		$("#calctopdrop").attr('class', 'calctopdrop-lightgreen');
		$("#custom-fields").hide();
		$("#standard-fields").show();
		$("#calmode").val(calmode);
		showstdprods(material);
		showbonusdiscnt(1,material,$("#stdprodid").val());
	}
	$('#btnreset').trigger('click');
}

function showbonusdiscnt(calmode,prodid,stdprodid) {
	$.post("inc/calactions.php", {mode: -1, material: ""+prodid+"", calmode: ""+calmode+"", stdprodid: ""+stdprodid+""}, function(data){
			if(data != null) {
			 
			  if(data.proddisc > 0) {
				if(calmode != null)  {
					$(".proddisc").html("");
					$("#proddisc_"+prodid).html("Bonus Discount "+data.proddisc+"%").removeClass("visihidden");
				}
			  }
			}//end of if data != 0
		});
}
function resetfields() {
	document.getElementById("width").value = "";
	document.getElementById("height").value = "";
	document.getElementById("quantity").value = 1;
	document.getElementById("origprice").value = $defaultcurrsymbol+"0.00";
	document.getElementById("saleprice").value = $defaultcurrsymbol+"0.00";
	document.getElementById("proddisc").value = $defaultcurrsymbol+"0.00";
	document.getElementById("subtotalprice").value = $defaultcurrsymbol+"0.00";
	//$(".stdqty").val('');
}

function showstdprods(prdid) {

	$(".stdprdssection").hide();
	$(".pr_"+prdid).show();
	$(".stdqty").val('');
	$("#width").val('');
	$("#height").val('');
	//$(".pr_"+prdid+":first").find(".stdqty").focus();
	if($(".pr_"+prdid).length > 0) {
		$("#nostdprds").hide();
		var divid=$(".pr_"+prdid+":first").find(".lineitem-container").attr("id");
		var values = $("#"+divid+"_vals").val().split(",");	
		$(".pr_"+prdid).find(".lineitem-container").removeClass("standard-selected");
		$(".pr_"+prdid).find(".arrow-container .orange-arrow").remove();
		$(".pr_"+prdid+":first").find(".lineitem-container").addClass("standard-selected");
		$(".pr_"+prdid+":first").find(".arrow-container").html('<div class="orange-arrow"></div>');
		$("#stdprodid").val(values[9]);
		showbonusdiscnt(1,prdid,$("#stdprodid").val());
	}
	else {
		$("#nostdprds").show();
	}
}

function selectStdProd(divid) {
	resetfields();
	if($("input#calmode").val() == 1) {
		$(".lineitem-container").removeClass("standard-selected");
		$(".lineitem-container").find(".arrow-container").html('&nbsp;');
	
		var currqty = $("#"+divid).find(".stdqty").val();
		var values = $("#"+divid+"_vals").val().split(",");	
		var newqty = $("#"+divid+"_qty").val();
		
		$(".stdqty").val('');
		$("#"+divid).find(".stdqty").val(currqty);
		$("#"+divid).addClass("standard-selected");
		$("#"+divid).find(".arrow-container").html('<div class="orange-arrow"></div>');
		
		$("#quantity").val(newqty);
		$("#materialid").val(values[0]);
		$("#width").val(values[1]);
		$("#height").val(values[2]);
		$("#finishid").val(values[4]);
		$("#subopt1_hid").val(values[5]);
		$("#subopt2_hid").val(values[6]);
		$("#eyeletsid").val(values[7]);
		$("#eyespaceid").val(values[8]);
		$("#stdprodid").val(values[9]);
		$("input#sewid").val($("#"+divid).find(".stdsewcolor").val());
		displaypriceinfo();
    }
}


function calcStdProd(divid) {	
	var newqty = $("#"+divid+"_qty").val();	
	$("#quantity").val(newqty);	
	displaypriceinfo();
}


function deleteRow(j) {
	$.post("inc/calactions.php", {mode: 2, itemnum: ""+j+""}, function(data) {
			itemlist=data.newlist;
			if(itemlist.length == 0) {
				window.location="calculator.php?m="+$("input#calmode").val();
				return;
			} 
			else {
				var tablestart = '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="cart-table"><tr><td width="4%"><img src="ticons/bin.gif" alt="Bin / Delete" class="bin-icon" /></td><td width="8%"><b>Qty</b></td><td width="70%"><b>Item</b></td><td width="19%" align="center"><b>Price '+$defaultregion+'</b></td></tr>';
				var tableend = '</table>';
				var tr,itemlist,lbl1,lbl2,lbl1val,lbl2val,lbleyespace,lbl1fullvalue,lblsewcolor;
				
			tr = tablestart;
			
				for(var i=0; i<itemlist.length; i++) {
					if(itemlist[i]["lbl1"] == "0") {lbl1=""; lbl1val="";} else {lbl1=" "+itemlist[i]["lbl1"]+": "; lbl1val=itemlist[i]["subopt1val"]; }
					if(itemlist[i]["lbl2"] == "0") {lbl2=""; lbl2val="";} else {lbl2=" "+itemlist[i]["lbl2"]+": "; lbl2val=itemlist[i]["subopt2val"]; }
					lblsewcolor= ", Sewing: "+itemlist[i]["sewcolor"];
					if(itemlist[i]["eyename"] == "None") {lbleye="";} else {lbleye=", Fittings: "+itemlist[i]["eyename"];}
					if(itemlist[i]["eyespace"] == "0") {lbleyespace = "";} else { lbleyespace= ", Fitting Spacing: "+itemlist[i]["eyespace"]; }
					
					
					lbl1fullvalue=lbl1+lbl1val;
					lbl2fullvalue=lbl2+lbl2val;
					var outwidth,outheight,unitsymbol;
					
					if(itemlist[i]["conversion"] == "ft") {
						unitsymbol="'";
					}
					else if(itemlist[i]["conversion"] == "in") {
						unitsymbol="\"";
					}
					else {
						unitsymbol="";
					}
													
					outwidth = measurementConvert("mm",itemlist[i]["conversion"], itemlist[i]["width"], 0)+unitsymbol+itemlist[i]["conversion"];
					outheight = measurementConvert("mm",itemlist[i]["conversion"], itemlist[i]["height"], 0)+unitsymbol+itemlist[i]["conversion"];
					
					tr += '<tr>';
					tr += '<td valign="top"><a href="javascript:deleteRow('+i+');" class="del padicon"></a></td>';
					tr += '<td valign="top">'+itemlist[i]["qty"]+'</td>';
					tr += '<td valign="top">'+outwidth+' x '+outheight+' '+itemlist[i]["matname"]+' <span class="lightgrey">'+itemlist[i]["finshname"]+lbl1fullvalue+lbl2fullvalue+lblsewcolor+lbleye+''+lbleyespace+'</span></td>';
					tr += '<td class="price" valign="top">'+$defaultcurrsymbol+itemlist[i]["price"]+'</td>';
					tr += '</tr>';
					
				/************************** Download Template and Download Info Sheet.***************************************/
					tr += '<tr class="tblinfolinks">';
					tr += '<td valign="top"></td>';
					tr += '<td valign="top"></td>';
					tr += '<td valign="top"><span class="artworkimg"><img src="ticons/icon-text-joiner.png" /></span><a href="#" class="dwninfotmpt" title="Download Info Sheet">1. Info Sheet<span class="itemind hide">'+i+'</span></a><span class="hide processicon" id="dwninfotemplticon_'+i+'"></span>&nbsp;|&nbsp;<a href="#" class="dwntemplt" title="Download Easy Template">2. Easy Template<span class="dwnlinkindex hide">'+i+'</span></a><span class="hide processicon" id="dwntemplticon_'+i+'"></span></td>';
					tr += '<td class="price" valign="top"></td>';
					tr += '</tr>';
				/************************** END Download Template and Download Info Sheet.***************************************/
				}//end of for
			}//end of else
			
			tr += tableend;
			$("#itemList").html(tr);
			
			$("input#totalprice").val($defaultcurrsymbol+data.totpayamt);
			if(data.shipamount == "0.00") {
				$("input#shipping").val("FREE");
				$("span.lblshipinfo").hide();
			} else {
				$("input#shipping").val($defaultcurrsymbol+data.shipamount);
				$("span.lblshipinfo").show();
			}
			
			$("#weight").html("<b>Weight:</b> "+data.shipweight+" kg");
			$("#shipmessage").html(data.shipmsg);
			$("#turnaround").html(data.eta);
			$("#calc-turnaround").html(data.eta);
	});//end of $.POST
}

function drawTable() {

	var calmode = $("#calmode").val();
	var quantity = $("#quantity").val();
	var material = $("#materialid").val();
	var finish = $("#finishid").val();
	var suboptval1 = $("#subopt1_hid").val();
	var suboptval2 = $("#subopt2_hid").val();
	var sewid= $("#sewid").val();
	var eyeletsid = $("#eyeletsid").val();
	var eyespaceid = $("#eyespaceid").val();
	var stdprodid = $("#stdprodid").val();

	var lbl1val=$("#lblsubopt1").html();
	var lbl2val=$("#lblsubopt2").html();

	if(lbl1val == "&nbsp;") {lbl1val=0;}
	if(lbl2val == "&nbsp;") {lbl2val=0;}

	var convert = $("input#convert").val();
	var width = $("#width").val();
	var height = $("#height").val();
	if(calmode == "0") {
		width = measurementConvert(convert,"mm", width, 0);
		height = measurementConvert(convert,"mm", height, 0);
	}
	
	if(quantity > 0 && width > 0 && height > 0) {
			$.post("inc/calactions.php", {mode: 1,conversion: ""+convert+"",calmode: ""+calmode+"", quantity: ""+quantity+"", width: ""+width+"", height: ""+height+"", material: ""+material+"", finish: ""+finish+"", subopt1: ""+suboptval1+"", subopt2: ""+suboptval2+"",sewcolorid: ""+sewid+"", eyeid: ""+eyeletsid+"", eyespaceid: ""+eyespaceid+"",lbl1: ""+lbl1val+"", lbl2: ""+lbl2val+"", stdprodid: ""+stdprodid+""}, function(data) {
			if(data != 0) {
				var tablestart = '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="cart-table"><tr><td width="4%"><img src="ticons/bin.gif" alt="Bin / Delete" class="bin-icon" /></td><td width="8%"><b>Qty</b></td><td width="70%"><b>Item</b></td><td width="19%" align="center"><b>Price '+$defaultregion+'</b></td></tr>';
				var tableend = '</table>';
				var tr,itemlist,lbl1,lbl2,lbl1val,lbl2val,lbleyespace,lbl1fullvalue,lblsewcolor;
				var outwidth,outheight,unitsymbol;
				
				tr = tablestart;
				itemlist=data.newlist;
				
				
				for(var i=0; i<itemlist.length; i++) {
					if(itemlist[i]["lbl1"] == "0") {lbl1=""; lbl1val="";} else {lbl1=" "+itemlist[i]["lbl1"]+": "; lbl1val=itemlist[i]["subopt1val"]; }
					if(itemlist[i]["lbl2"] == "0") {lbl2=""; lbl2val="";} else {lbl2=" "+itemlist[i]["lbl2"]+": "; lbl2val=itemlist[i]["subopt2val"]; }
					lblsewcolor= ", Sewing: "+itemlist[i]["sewcolor"];
					if(itemlist[i]["eyename"] == "None") {lbleye="";} else {lbleye=", Fittings: "+itemlist[i]["eyename"];}
					if(itemlist[i]["eyespace"] == "0") {lbleyespace = "";} else { lbleyespace= ", Fitting Spacing: "+itemlist[i]["eyespace"]; }
					
					lbl1fullvalue=lbl1+lbl1val;
					lbl2fullvalue=lbl2+lbl2val;
					
					if(itemlist[i]["conversion"] == "ft") {
						unitsymbol="'";
					}
					else if(itemlist[i]["conversion"] == "in") {
						unitsymbol="\"";
					}
					else {
						unitsymbol="";
					}
													
					outwidth = measurementConvert("mm",itemlist[i]["conversion"], itemlist[i]["width"], 0)+unitsymbol+itemlist[i]["conversion"];
					outheight = measurementConvert("mm",itemlist[i]["conversion"], itemlist[i]["height"], 0)+unitsymbol+itemlist[i]["conversion"];
						  	
					tr += '<tr>';
					tr += '<td valign="top"><a href="javascript:deleteRow('+i+');" class="del padicon"></a></td>';
					tr += '<td valign="top">'+itemlist[i]["qty"]+'</td>';
					tr += '<td valign="top">'+outwidth+' x '+outheight+' '+itemlist[i]["matname"]+' <span class="lightgrey">'+itemlist[i]["finshname"]+lbl1fullvalue+lbl2fullvalue+lblsewcolor+lbleye+''+lbleyespace+'</span></td>';
					tr += '<td class="price" valign="top">'+$defaultcurrsymbol+itemlist[i]["price"]+'</td>';
					tr += '</tr>';
					
				/************************** Download Template and Download Info Sheet.***************************************/
					tr += '<tr class="tblinfolinks">';
					tr += '<td valign="top"></td>';
					tr += '<td valign="top"></td>';
					tr += '<td valign="top"><span class="artworkimg"><img src="ticons/icon-text-joiner.png" /></span><a href="#" class="dwninfotmpt" title="Download Info Sheet">1. Info Sheet<span class="itemind hide">'+i+'</span></a><span class="hide processicon" id="dwninfotemplticon_'+i+'"></span>&nbsp;|&nbsp;<a href="#" class="dwntemplt" title="Download Easy Template">2. Easy Template<span class="dwnlinkindex hide">'+i+'</span></a><span class="hide processicon" id="dwntemplticon_'+i+'"></span></td>';
					tr += '<td class="price" valign="top"></td>';
					tr += '</tr>';
				/************************** END Download Template and Download Info Sheet.***************************************/
				}//end of for
				tr += tableend;
				$("#itemList").html(tr);
				
				$("input#totalprice").val($defaultcurrsymbol+data.totpayamt);
				if(data.shipamount == "0.00") {
					$("input#shipping").val("FREE");
					$("span.lblshipinfo").hide();
				} else {
					$("input#shipping").val($defaultcurrsymbol+data.shipamount);
					$("span.lblshipinfo").show();
				}
				
				if(data.ispaid == "1") {
					$("#errordpaid").show();
					$btnaddquote=$("#btnAddQuote");
					$btnaddquote.removeClass("btn-primary").addClass("disabled");
					$btnaddquote.attr("id","btnDisabled");

				}
				else {
					$("#errordpaid").hide();
				}
				
				$("#weight").html("<b>Weight:</b> "+data.shipweight+" kg");
				$("#shipmessage").html(data.shipmsg);
				$("#turnaround").html(data.eta);
				$("#calc-turnaround").html(data.eta);
			}//end of if data != 0
		});

	}

}

function displaypriceinfo () {

	var quantity = $("#quantity").val();
	var material = $("input#materialid").val();
	var finish = $("#finishid").val();
	var suboptval1 = $("#subopt1_hid").val();
	var suboptval2 = $("#subopt2_hid").val();
	var eyeletsid = $("#eyeletsid").val();
	var eyespaceid = $("#eyespaceid").val();
	var stdprodid = $("#stdprodid").val();
	var calmode = $("#calmode").val();
	
	var convert = $("input#convert").val();
	var width = $("#width").val();
	var height = $("#height").val();
	
	if(calmode == "0") {
		width = measurementConvert(convert,"mm", width, 0);
		height = measurementConvert(convert,"mm", height, 0);
	}
	
	if(quantity > 0 && width > 0 && height > 0) {
	
		$.post("inc/calactions.php", {quantity: ""+quantity+"", width: ""+width+"", height: ""+height+"", material: ""+material+"", finish: ""+finish+"", mode: 0, calmode: ""+calmode+"", subopt1: ""+suboptval1+"",subopt2: ""+suboptval2+"", eyeid: ""+eyeletsid+"",eyespaceid: ""+eyespaceid+"", stdprodid: ""+stdprodid+""}, function(data){
			if(data != 0) {
			  $("#origprice").val($defaultcurrsymbol+data.origprice);
			  $("#saleprice").val($defaultcurrsymbol+data.salepricestorewide);
			  $("#proddisc").val($defaultcurrsymbol+data.saleprice);
			  $("#subtotalprice").val($defaultcurrsymbol+data.totalprice);
			 
			  if(data.proddisc > 0) {
				if(data.calmode == 0)  {
					$(".proddisc").html("");
					$("#proddisc_"+material).html("Bonus Discount "+data.proddisc+"%").removeClass("visihidden");
				}
				else if(data.calmode == 1){
					$(".proddisc").html("");
					  $("#proddisc_"+material).html("Bonus Discount "+data.bonusdisclbl+"%").removeClass("visihidden");
				}
				else {
					$(".proddisc").html("");
					$("#proddisc_"+material).html("Bonus Discount").removeClass("visihidden");
				}
			  }
			}//end of if data != 0
		});
		
	}
	
	
	return false;

}

function printQuote() {
	newwin=window.open('estimate.php','printwin','menubar=yes,left=100,top=100,width=860,height=800,scrollbars=yes');
}
function checkCalc() {
	var totalprice = document.getElementById("totalprice").value;
	
	if(totalprice == $defaultcurrsymbol+"0.00") {
		return false;
	} else {
		$("#calculate").submit();
		return true;
	}
}