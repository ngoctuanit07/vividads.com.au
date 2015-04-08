$(document).ready(function() {
	
	$('#countries').dropkick({
		change: function (value) {
			window.open(value, '_self');
		}
	});
	
	$("#btnDisabled").hide();

	$("#btnSubmit").click(function() {
	  drawTable();
	});
	
	$(document).on('click','.btnAddQuote', function(e) {
		$this=$(this);
		$thisparent=$this.parents(".opt");
		var convert = $thisparent.find("span.conversion").html();
		var quantity = $thisparent.find("span.itemqty").html();
		var width = $thisparent.find("span.itemwidth").html();
		var height = $thisparent.find("span.itemheight").html();
		var material = $thisparent.find("span.itemprodid").html();
		var finish = $thisparent.find("span.itemfinid").html();
		var suboptval1 = $thisparent.find("span.itemsubopt1").html();
		var suboptval2 = $thisparent.find("span.itemsubopt2").html();
		var sewcolorid = $thisparent.find("span.itemsewcolorid").html();
		var eyeletsid = $thisparent.find("span.itemeyeid").html();
		var eyespaceid = $thisparent.find("span.itemeyespaceid").html();
		var lbl1val = $thisparent.find("span.itemlbl1").html();
		var lbl2val = $thisparent.find("span.itemlbl2").html();
		var itemindex= $thisparent.find("span.itemindex").html();
		$thisparent.find(".btnStartOrder").removeClass("btn-success").addClass("btn-template");
		$thisparent.find(".btnStartOrder").html("<img src='ticons/anim-loading.gif' />&nbsp;Calculating...<br />Please wait");
		$thisparent.find(".btnStartOrder").off();
		
		var newwidth= measurementConvert(convert, "mm", width);
		var newheight= measurementConvert(convert, "mm", height);
		
	  		$.post("inc/calactions.php", {mode: 1,conversion: ""+convert+"",calmode: ""+0+"", quantity: ""+quantity+"", width: ""+newwidth+"", height: ""+newheight+"", material: ""+material+"", finish: ""+finish+"", subopt1: ""+suboptval1+"", subopt2: ""+suboptval2+"", sewcolorid: ""+sewcolorid+"" , eyeid: ""+eyeletsid+"", eyespaceid: ""+eyespaceid+"",lbl1: ""+lbl1val+"", lbl2: ""+lbl2val+"", stdprodid: ""+0+""}, function(data) {
					if(data.newlist.length > 0) {
						$thisparent.find(".btnStartOrder").removeClass("hide");
						$.post("inc/easyaddtoquote.php", {itemindex: ""+itemindex+""},function (data) {
							if(data == "1") {
								$thisparent.find(".btnStartOrder").removeClass("btn-template").addClass("btn-success");
								$thisparent.find(".btnStartOrder").html("<i class='icon-circle-arrow-right icon-white'></i><span>Start Order</span>");
							}
						});
					}
			});
	});
	
	if($("input#calmode").val() == "0") {
		changetab(0);
	}
	else if($("input#calmode").val() == "1") {
		changetab(1);
	}
	
	$(document).on('click','.delItem',function(e) {
		$this=$(this);
		$thisparent=$this.parents(".preview-item-container");
		var itemindex=$thisparent.find("span.itemindex").html();
		$thisparent.hide();
		
		$.post("generatepreview.php", {mode: 1, itemindex: ""+itemindex+""}, function(data) {
			if(data != 0) {
					var itemlist=data;
					var hidestr = "";
					var ourstr = "";
					var stylestr = "";
					var btntext = "";
					var eyetext = "";
					var unitsymbol ="";
					
					for(var i=0; i<itemlist.length; i++) {
									
						if(itemlist[i]["addtoquote"] == "0") {
							hidestr="hide";
						}
						else {
							stylestr="btn-success";
							btntext="<i class='icon-circle-arrow-right icon-white'></i> <span>Start Order</span>";
						}
						if(itemlist[i]["eyeletinfo"] != "0") {
							eyetext=itemlist[i]["eyeletinfo"];
						}
						if(itemlist[i]["conversion"] == "ft") {unitsymbol = "'";}
						else if(itemlist[i]["conversion"] == "in") {unitsymbol = "\"";}
						else {unitsymbol = "";}
						
						ourstr = ourstr +'<div class="preview-item-container">';
						ourstr = ourstr +'<div class="desc">';
						ourstr = ourstr +'<div class="calicons"><div class="bin"><img class="bin-icon" alt="Bin / Delete" src="ticons/bin.gif"></div><div class="lblqty">Qty</div><div class="lblitem">Item</div></div>';
						
						ourstr = ourstr +'<div class="caldesc">';
						ourstr = ourstr +'<div class="delicon"><a title="Remove Line Item" class="del padicon delItem"></a></div>';
						ourstr = ourstr +'<div class="qtydesc"><p>'+itemlist[i]["qty"]+'</p></div>';
						ourstr = ourstr +'<div class="itemdesc"><p>'+itemlist[i]["width"]+unitsymbol+''+itemlist[i]["conversion"]+' x '+itemlist[i]["height"]+unitsymbol+''+itemlist[i]["conversion"]+' '+itemlist[i]["orientation"]+'&nbsp;'+itemlist[i]["prodname"]+'</p></div>';
						ourstr = ourstr +'<div class="clear"></div>';
						ourstr = ourstr +'<div class="otherdesc"><p><b>Inc:</b> '+itemlist[i]["finishinfo"]+' Sewing: '+itemlist[i]["sewcolor"]+'</p>';
						ourstr = ourstr +eyetext+'<br />';
						ourstr = ourstr +'<p class="blue"><b>Create artwork at:</b></p>';
						ourstr = ourstr +'<p>'+itemlist[i]["widthtext"]+'</p>';
						ourstr = ourstr +'<p>'+itemlist[i]["heighttext"]+'</p>';
						ourstr = ourstr +'<br />';
						ourstr = ourstr +'<p>'+itemlist[i]["filesizetext"]+'</p></div></div>';
						ourstr = ourstr +'<div class="clear"></div>';
						ourstr = ourstr +'</div>';
						ourstr = ourstr +'<div class="opt">';
						ourstr = ourstr +'<button type="button" class="btn btn-primary btnAddQuote"> <i class="icon-plus icon-white"></i> <span>Add to Quote</span> </button>';
						ourstr = ourstr +'<button type="button" class="btn '+stylestr+' btnStartOrder '+hidestr+'" style="width:130px; margin:10px 0 10px 10px; float:left;">'+btntext+'</button>';
						ourstr = ourstr +'<p class="brown free-help"><b>Free Download Help</b></p>';
						ourstr = ourstr +'<button type="button" class="btn btn-template btnInfoTemplate"><i class="durapdf"></i> <span>Info<br />Sheet</span><input type="hidden" name="fileurl" class="fileurl" value="0" /></button>';
						ourstr = ourstr +'<button type="button" class="btn btn-template btnTemplate"><i class="durapdf"></i> <span>Easy Template</span><input type="hidden" name="tempfileurl" class="tempfileurl" value="0" /></button>';
						ourstr = ourstr +'<span class="itemwidth hide">'+itemlist[i]["width"]+'</span>';
						ourstr = ourstr +'<span class="itemheight hide">'+itemlist[i]["height"]+'</span>';
						ourstr = ourstr +'<span class="itemprodid hide">'+itemlist[i]["prodid"]+'</span>';
						ourstr = ourstr +'<span class="itemprodname hide">'+itemlist[i]["prodname"]+'</span>';
						ourstr = ourstr +'<span class="itemfinid hide">'+itemlist[i]["finishid"]+'</span>';
						ourstr = ourstr +'<span class="itemfinname hide">'+itemlist[i]["finishinfo"]+'</span>';
						ourstr = ourstr +'<span class="itemmosvalues hide">'+itemlist[i]["mosvalues"]+'</span>';
						ourstr = ourstr +'<span class="itemsafetyvals hide">'+itemlist[i]["safetyvals"]+'</span>';
						ourstr = ourstr +'<span class="itemqty hide">'+itemlist[i]["qty"]+'</span>';
						ourstr = ourstr +'<span class="itemlbl1 hide">'+itemlist[i]["label1"]+'</span>';
						ourstr = ourstr +'<span class="itemsubopt1 hide">'+itemlist[i]["subopt1"]+'</span>';
						ourstr = ourstr +'<span class="itemlbl2 hide">'+itemlist[i]["label2"]+'</span>';
						ourstr = ourstr +'<span class="itemsubopt2 hide">'+itemlist[i]["subopt2"]+'</span>';
						ourstr = ourstr +'<span class="itemsewcolor hide">'+itemlist[i]["sewcolor"]+'</span>';
						ourstr = ourstr +'<span class="itemsewcolorid hide">'+itemlist[i]["sewcolorid"]+'</span>';
						ourstr = ourstr +'<span class="itemeyeid hide">'+itemlist[i]["eyeid"]+'</span>';
						ourstr = ourstr +'<span class="itemeyename hide">'+itemlist[i]["eyename"]+'</span>';	 
						ourstr = ourstr +'<span class="itemeyespaceid hide">'+itemlist[i]["eyespaceid"]+'</span>';
						ourstr = ourstr +'<span class="itemeyespace hide">'+itemlist[i]["eyespacename"]+'</span>';
						ourstr = ourstr +'<span class="conversion hide">'+itemlist[i]["conversion"]+'</span>';
						ourstr = ourstr +'<span class="itemindex hide">'+i+'</span>';
						ourstr = ourstr +'<p class="optimg"><img src="'+itemlist[i]["prodimage"]+'" /></p>';
						ourstr = ourstr +'<p><b>1.</b> The Blue line is your Finish Size. You may fill colour (bleed) past the <a href="artworkhow.php">MOS</a> edges, please keep text and critical elements inside the Blue.</p>';
						ourstr = ourstr +'<p><b>2.</b> Do not put Fitting, Pole Pocket, Hem, Colour Lines/Bars or Registration Marks on your design file. We do this for you.</p>';
						ourstr = ourstr +'<p><b>3.</b> File &gt; Save As &gt; PDF</p></div>';
						ourstr = ourstr +'<div class="preview"><img src="'+itemlist[i]["outputfile"]+'" align="center" /></div>';
						ourstr = ourstr +'<div class="line-drawings">'+itemlist[i]["linedrawing"]+'</div>';
						ourstr = ourstr +'</div>';
	  
					}//end of for
					$("#itemlist").html(ourstr);
					
				}//end of if data != 0
		});
	});
		
$(document).on('click','.btnStartOrder', function(e) {
	window.location="calculator.php";
});

	var currmeasure = $("input#convert").val();
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
		}
	});
	
	$("#newquote").click(function(e) {
		$this=$(this);
		$.post("clearsessionmos.php", function(data) {
			e.preventDefault();
			drawTable();
			location.reload();
		});
	})
	
	$('#displayeyespace_classic').ddslick({
		width: 260,
		onSelected: function(data){
			document.getElementById("eyespaceid").value = data.selectedData.value;
		}
	});

	$('#displayeyespace_metric').ddslick({
		width: 260,
		onSelected: function(data){
			document.getElementById("eyespaceid").value = data.selectedData.value;
		}
	});

	$('#displayeyelets').ddslick({
		width: 260,
		onSelected: function(data){
			document.getElementById("eyeletsid").value = data.selectedData.value;
			showeyeletspace($("input#materialid").val(),data.selectedData.value);
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
		}
	});
	
	$('#subopt2_classic').ddslick({
		width: 95,
		onSelected: function(data){
			document.getElementById("subopt2_hid").value = data.selectedData.value;
		}
	});
	
	$('#subopt1_metric').ddslick({
		width: 95,
		onSelected: function(data){
			document.getElementById("subopt1_hid").value = data.selectedData.value;
		}
	});
	
	$('#subopt1_classic').ddslick({
		width: 95,
		onSelected: function(data){
			document.getElementById("subopt1_hid").value = data.selectedData.value;
		}
	});

	$('#displayfinish').ddslick({
		width: 260,
		onSelected: function(data){
			document.getElementById("finishid").value = data.selectedData.value;
			showsubopt(data.selectedData.value);
			showeyelets(data.selectedData.value);
			showsewcolor(data.selectedData.value);
		}
	});
	
	$('#material').ddslick({
		width: 260,
		onSelected: function(data){
			document.getElementById("materialid").value = data.selectedData.value;
			$(".proddisc").hide();
			$("#proddisc_"+data.selectedData.value).show();
			$(".lngdes").hide();
			$("#lngdes_"+data.selectedData.value).show();
			showfinish(data.selectedData.value);
    	}
	});

	$("#btnreset").click(function() {
		var curprd = $("input#materialid").val();
		document.getElementById("width").value = "";
		document.getElementById("height").value = "";
		document.getElementById("quantity").value = 1;
		showfinish(curprd);
		showeyeletspace(0,1);
		$(".stdmosqty").val('');
		return false;
	});
	
		/************************** Download Template and Download Info Sheet. ***************************************/
$(document).on('click','.btnInfoTemplate', function(e) {
	if($(this).find("input.fileurl").val() == "0") {
		e.preventDefault();
		var $this=$(this);
		var $thisparent=$this.parents(".preview-item-container");
		
		var jmode = "2";
		var jitemindex = $thisparent.find(".itemindex").html();
		
		$this.html("<img src='ticons/anim-loading.gif' />&nbsp;Generating... Please wait");
					
		$.ajax({
				type: "POST",
				url: "inc/easyinfosheetcreate.php",
				data: "mode="+jmode+"&itemindex="+jitemindex,
				cache: false,
				success: function (data) {
					if(data != "") {
						$this.html("<i class='durapdf'></i><span>Info<br />Sheet</span><input type='hidden' name='fileurl' class='fileurl' value='"+data+"' />");
						$.fileDownload("download.php?file=fileserver/mos/"+data);
					}
					else {
						$this.html("<i class='durapdf'></i><span>Info<br />Sheet</span><input type='hidden' name='fileurl' class='fileurl' value='0' />");
					}
				}
		 });//end of ajax
		}//end of if fileurl is 0
		else {
			$.fileDownload("download.php?file=fileserver/mos/"+$(this).find("input.fileurl").val());
		}
	});//end of click
	
	$(document).on('click','.btnTemplate', function(e) {
	if($(this).find("input.tempfileurl").val() == "0") {
		e.preventDefault();
		var $this=$(this);		
		var $thisparent=$this.parents(".preview-item-container");

		var jinputwidth = $thisparent.find(".itemwidth").html();
		var jinputheight = $thisparent.find(".itemheight").html();
		var jmosvalues = $thisparent.find(".itemmosvalues").html();
		var jsafetyvals = $thisparent.find(".itemsafetyvals").html();
		var jfinid = $thisparent.find(".itemfinid").html();		
		var jlabel1 = $thisparent.find(".itemlbl1").html();
		var jsubopt1 = $thisparent.find(".itemsubopt1").html();
		var jlabel2 = $thisparent.find(".itemlbl2").html();
		var jsubopt2 = $thisparent.find(".itemsubopt2").html();		
		var jprodid = $thisparent.find(".itemprodid").html();
		var conversion = $thisparent.find(".conversion").html();
		var jmode = "0";
		
		$this.html("<img src='ticons/anim-loading.gif' />&nbsp;Generating... Please wait");
					
		$.ajax({
				type: "POST",
				url: "inc/easytempcreate.php",
				data: "mode="+jmode+"&inputwidth="+jinputwidth+"&inputheight="+jinputheight+"&mosvalues="+jmosvalues+"&safetyvals="+jsafetyvals+"&finid="+jfinid+"&label1="+jlabel1+"&subopt1="+jsubopt1+"&label2="+jlabel2+"&subopt2="+jsubopt2+"&prodid="+jprodid+"&conversion="+conversion,
				cache: false,
				success: function (data) {
					if(data != "") {
						$this.html("<i class='durapdf'></i><span>Easy Template</span><input type='hidden' name='tempfileurl' class='tempfileurl' value='"+data+"' />");
						$.fileDownload("download.php?file=fileserver/mos/"+data);
					}
					else {
						$this.html("<i class='durapdf'></i><span>Easy Template</span><input type='hidden' name='tempfileurl' class='tempfileurl' value='0' />");
					}
				}
		 });//end of ajax
		}//end of if fileurl is 0
		else {
				$.fileDownload("download.php?file=fileserver/mos/"+$(this).find("input.tempfileurl").val());
		}
	});//end of click
		/************************** END Download Template and Download Info Sheet. ***************************************/
	

	
	function showfinish(prdid) {
		var finarr=prdfinarray[prdid];
		$("#displayfinish ul li").hide();
		var defaultval=showelements("#displayfinish",finarr,"finishid");
		$("#finsec").show();
		showstdprods(prdid);
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
	});

	
function changetab(calmode) {
	var material=$("#materialid").val();
	var dropunit=$("input#convert").val();
	if(calmode == 0) {
		$("#tab-custom").attr('class', 'tab-custom-blue');
		$("#tab-mos-standard").attr('class', 'tab-mos-standard-deselected');
		$("#calc-top").attr('class', 'calc-top-blue');
		$("#custom-fields").show();
		$("#standard-fields").hide();
		$("#calmode").val(calmode);
		$(".custdesc").show();
		$(".stddesc").hide();
	} else if(calmode == 1) {
		$("#tab-custom").attr('class', 'tab-custom-deselected');
		$("#tab-mos-standard").attr('class', 'tab-mos-standard-selected');
		$("#calc-top").attr('class', 'calc-top-lightblue');
		$("#custom-fields").hide();
		$("#standard-fields").show();
		$("#calmode").val(calmode);
		$("#sewid").val(0);
		$(".custdesc").hide();
		$(".stddesc").show();
		$("span.stdwidth").each(function(index) {
			var $thisparent=$(this).parents(".lineitem-container");
			var thisval=$(this).html();
			var oriwidth=$thisparent.find(".hid_stdoriwidth").val();
			var newunit=measurementConvert("mm", dropunit, oriwidth);
			$(this).html(newunit+dropunit);
		});
		$("span.stdheight").each(function(index) {
			var $thisparent=$(this).parents(".lineitem-container");
			var thisval=$(this).html();
			var oriwidth=$thisparent.find(".hid_stdoriheight").val();
			var newunit=measurementConvert("mm", dropunit, oriwidth);
			$(this).html(newunit+dropunit);
		});
	}

	$(".proddisc").html("");
	$('#btnreset').trigger('click');
}

function showstdprods(prdid) {
	for(a = 0; a < prdarray.length; a++) {
		$(".pr_"+prdarray[a]).hide();
	}
	$(".pr_"+prdid).show();
	$(".stdmosqty").val('');
	//$("#width").val('');
	//$("#height").val('');
	if($(".pr_"+prdid).length > 0) {
		$("#nostdprds").hide();
		$(".pr_"+prdid+":first").find(".lineitem-container").addClass("mos-standard-selected");
		$(".pr_"+prdid+":first").find(".arrow-container").html('<div class="orange-arrow"></div>');
		selectStdProd($(".pr_"+prdid+":first").find(".lineitem-container").attr("id"));
	}
	else {
		$("#nostdprds").show();
	}
}

function selectStdProd(divid) {
	if($("input#calmode").val() == 1) {
	$(".lineitem-container").removeClass("mos-standard-selected");
	$(".lineitem-container").find(".arrow-container").html('&nbsp;');

	var currqty = $("#"+divid).find(".stdmosqty").val();

	$(".stdmosqty").val('');
	$("#"+divid).find(".stdmosqty").val(currqty);
	$("#"+divid).addClass("mos-standard-selected");
	$("#"+divid).find(".arrow-container").html('<div class="orange-arrow"></div>');

	var values = $("#"+divid+"_vals").val().split(",");	
	var newqty = $("#"+divid+"_qty").val();

	$("input#quantity").val(newqty);
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
	}
}
function calcStdProd(divid) {	
	var newqty = $("#"+divid+"_qty").val();	
	$("#quantity").val(newqty);	
}


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
	
	function drawTable() {
		
		

		var quantity = document.getElementById("quantity").value;
		var width = document.getElementById("width").value;
		var height = document.getElementById("height").value;
		var material = document.getElementById("materialid").value;
		var finish = document.getElementById("finishid").value;
		var suboptval1 = document.getElementById("subopt1_hid").value;
		var suboptval2 = document.getElementById("subopt2_hid").value;
		var eyeletsid = document.getElementById("eyeletsid").value;
		var eyespaceid = document.getElementById("eyespaceid").value;
		var conversion = document.getElementById("convert").value;
		var calmode = document.getElementById("calmode").value;
		var sewid= $("#sewid").val();
		
		var lbl1val=$("#lblsubopt1").html();
		var lbl2val=$("#lblsubopt2").html();

		if(lbl1val == "&nbsp;") {lbl1val=0;}
		if(lbl2val == "&nbsp;") {lbl2val=0;}

		if(quantity > 0 && width > 0 && height > 0 && width !="" && height !="") {	
			$("#mos-load").show();
			$("#btnSubmit").hide();
			$("#btnDisabled").show();
			$.post("generatepreview.php", {mode: 3, calmode: ""+calmode+"",conversion: ""+conversion+"", qty: ""+quantity+"", width: ""+width+"", height: ""+height+"", prodid: ""+material+"", finish: ""+finish+"", subopt1: ""+suboptval1+"", subopt2: ""+suboptval2+"",sewcolorid: ""+sewid+"", lbl1: ""+lbl1val+"", lbl2: ""+lbl2val+"", eyeid: ""+eyeletsid+"", eyespaceid: ""+eyespaceid+"", itemindex: ""+1+""}, function(data) {
				var ourstr="";
				$("#width").val("");
				$("#height").val("");
				$("#mos-load").hide();
				$("#btnSubmit").show();
				$("#btnDisabled").hide();
				$(".stdmosqty").val("");
				
				if(data != 0) {
					var itemlist=data;
					var hidestr = ""
					var ourstr = "";
					var stylestr="";
					var btntext="";
					var eyetext="";
					var unitsymbol ="";
					
					for(var i=0; i<itemlist.length; i++) {
									
						if(itemlist[i]["addtoquote"] == "0") {
							hidestr="hide";
						}
						else {
							stylestr="btn-success";
							btntext="<i class='icon-circle-arrow-right icon-white'></i> <span>Start Order</span>";
						}
						if(itemlist[i]["eyeletinfo"] != "0") {
							eyetext=itemlist[i]["eyeletinfo"];
						}
						if(itemlist[i]["conversion"] == "ft") {unitsymbol = "'";}
						else if(itemlist[i]["conversion"] == "in") {unitsymbol = "\"";}
						else {unitsymbol = "";}
						
						ourstr = ourstr +'<div class="preview-item-container">';
						ourstr = ourstr +'<div class="desc">';
						ourstr = ourstr +'<div class="calicons"><div class="bin"><img class="bin-icon" alt="Bin / Delete" src="ticons/bin.gif"></div><div class="lblqty">Qty</div><div class="lblitem">Item</div></div>';
						
						ourstr = ourstr +'<div class="caldesc">';
						ourstr = ourstr +'<div class="delicon"><a title="Remove Line Item" class="del padicon delItem"></a></div>';
						ourstr = ourstr +'<div class="qtydesc"><p>'+itemlist[i]["qty"]+'</p></div>';
						ourstr = ourstr +'<div class="itemdesc"><p>'+itemlist[i]["width"]+unitsymbol+''+itemlist[i]["conversion"]+' x '+itemlist[i]["height"]+unitsymbol+''+itemlist[i]["conversion"]+' '+itemlist[i]["orientation"]+'&nbsp;'+itemlist[i]["prodname"]+'</p></div>';
						ourstr = ourstr +'<div class="clear"></div>';
						ourstr = ourstr +'<div class="otherdesc"><p><b>Inc:</b> '+itemlist[i]["finishinfo"]+' Sewing: '+itemlist[i]["sewcolor"]+'</p>';
						ourstr = ourstr +eyetext+'<br />';
						ourstr = ourstr +'<p class="blue"><b>Create artwork at:</b></p>';
						ourstr = ourstr +'<p>'+itemlist[i]["widthtext"]+'</p>';
						ourstr = ourstr +'<p>'+itemlist[i]["heighttext"]+'</p>';
						ourstr = ourstr +'<br />';
						ourstr = ourstr +'<p>'+itemlist[i]["filesizetext"]+'</p></div></div>';
						ourstr = ourstr +'<div class="clear"></div>';
						ourstr = ourstr +'</div>';
						ourstr = ourstr +'<div class="opt">';
						ourstr = ourstr +'<button type="button" class="btn btn-primary btnAddQuote"> <i class="icon-plus icon-white"></i> <span>Add to Quote</span> </button>';
						ourstr = ourstr +'<button type="button" class="btn '+stylestr+' btnStartOrder '+hidestr+'" style="width:130px; margin:10px 0 10px 10px; float:left;">'+btntext+'</button>';
						ourstr = ourstr +'<p class="brown free-help"><b>Free Download Help</b></p>';
						ourstr = ourstr +'<button type="button" class="btn btn-template btnInfoTemplate"><i class="durapdf"></i> <span>Info<br />Sheet</span><input type="hidden" name="fileurl" class="fileurl" value="0" /></button>';
						ourstr = ourstr +'<button type="button" class="btn btn-template btnTemplate"><i class="durapdf"></i> <span>Easy Template</span><input type="hidden" name="tempfileurl" class="tempfileurl" value="0" /></button>';
						ourstr = ourstr +'<span class="itemwidth hide">'+itemlist[i]["width"]+'</span>';
						ourstr = ourstr +'<span class="itemheight hide">'+itemlist[i]["height"]+'</span>';
						ourstr = ourstr +'<span class="itemprodid hide">'+itemlist[i]["prodid"]+'</span>';
						ourstr = ourstr +'<span class="itemprodname hide">'+itemlist[i]["prodname"]+'</span>';
						ourstr = ourstr +'<span class="itemfinid hide">'+itemlist[i]["finishid"]+'</span>';
						ourstr = ourstr +'<span class="itemfinname hide">'+itemlist[i]["finishinfo"]+'</span>';
						ourstr = ourstr +'<span class="itemmosvalues hide">'+itemlist[i]["mosvalues"]+'</span>';
						ourstr = ourstr +'<span class="itemsafetyvals hide">'+itemlist[i]["safetyvals"]+'</span>';
						ourstr = ourstr +'<span class="itemqty hide">'+itemlist[i]["qty"]+'</span>';
						ourstr = ourstr +'<span class="itemlbl1 hide">'+itemlist[i]["label1"]+'</span>';
						ourstr = ourstr +'<span class="itemsubopt1 hide">'+itemlist[i]["subopt1"]+'</span>';
						ourstr = ourstr +'<span class="itemlbl2 hide">'+itemlist[i]["label2"]+'</span>';
						ourstr = ourstr +'<span class="itemsubopt2 hide">'+itemlist[i]["subopt2"]+'</span>';
						ourstr = ourstr +'<span class="itemsewcolor hide">'+itemlist[i]["sewcolor"]+'</span>';
						ourstr = ourstr +'<span class="itemsewcolorid hide">'+itemlist[i]["sewcolorid"]+'</span>';
						ourstr = ourstr +'<span class="itemeyeid hide">'+itemlist[i]["eyeid"]+'</span>';
						ourstr = ourstr +'<span class="itemeyename hide">'+itemlist[i]["eyename"]+'</span>';	 
						ourstr = ourstr +'<span class="itemeyespaceid hide">'+itemlist[i]["eyespaceid"]+'</span>';
						ourstr = ourstr +'<span class="itemeyespace hide">'+itemlist[i]["eyespacename"]+'</span>';
						ourstr = ourstr +'<span class="conversion hide">'+itemlist[i]["conversion"]+'</span>';
						ourstr = ourstr +'<span class="itemindex hide">'+i+'</span>';
						ourstr = ourstr +'<p class="optimg"><img src="'+itemlist[i]["prodimage"]+'" /></p>';
						ourstr = ourstr +'<p><b>1.</b> The Blue line is your Finish Size. You may fill colour (bleed) past the <a href="artworkhow.php">MOS</a> edges, please keep text and critical elements inside the Blue.</p>';
						ourstr = ourstr +'<p><b>2.</b> Do not put Fitting, Pole Pocket, Hem, Colour Lines/Bars or Registration Marks on your design file. We do this for you.</p>';
						ourstr = ourstr +'<p><b>3.</b> File &gt; Save As &gt; PDF</p></div>';
						ourstr = ourstr +'<div class="preview"><img src="'+itemlist[i]["outputfile"]+'" align="center" /></div>';
						ourstr = ourstr +'<div class="line-drawings">'+itemlist[i]["linedrawing"]+'</div>';
						ourstr = ourstr +'</div>';
	  
					}//end of for
					$("#itemlist").html(ourstr);
					
				}//end of if data != 0
			
			});
		}
	
	}
	
	
