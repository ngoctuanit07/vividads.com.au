function getQutefunc()
{var id='#dialog';var maskHeight=jQuery(document).height();var maskWidth=jQuery(window).width();jQuery('#mask').css({'width':maskWidth,'height':maskHeight});jQuery('#mask').fadeIn(1000);jQuery('#mask').fadeTo("slow",0.8);var winH=jQuery(window).height();var winW=jQuery(window).width();jQuery(id).css('top',winH/2-jQuery(id).height()/2);jQuery(id).css('left',winW/2-jQuery(id).width()/2);jQuery(id).fadeIn(2000);jQuery('#mask').click(function(){});}
jQuery(document).ready(function(){jQuery('.window .magento_close').click(function(e){e.preventDefault();jQuery('#mask').fadeOut(1000);jQuery('.window').fadeOut(1000);});});function getQuote(qty)
{if(qty==0)
{alert("Please add items in your shopping cart first.")}else{getQutefunc();}}
function cartHide()
{if(document.getElementById('topCartContent').style.display=="none")
{jQuery('#topCartContent').slideDown('200',function(){});}else{jQuery('#topCartContent').slideUp('200',function(){});}}
function cartClose()
{jQuery('#topCartContent').slideUp('slow',function(){});}
jQuery(document).ready(function(){jQuery(".sign-min").click(function(){jQuery("#mini-login-top").slideToggle("slow");});});var Translator=new Translate([]);enUS={"m":{"wide":["January","February","March","April","May","June","July","August","September","October","November","December"],"abbr":["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]}};Calendar._DN=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];Calendar._SDN=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];Calendar._FD=0;Calendar._MN=["January","February","March","April","May","June","July","August","September","October","November","December"];Calendar._SMN=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];Calendar._am="AM";Calendar._pm="PM";Calendar._TT={};Calendar._TT["INFO"]="About the calendar";Calendar._TT["ABOUT"]="DHTML Date/Time Selector\n"+"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n"+"For latest version visit: http://www.dynarch.com/projects/calendar/\n"+"Distributed under GNU LGPL. See http://gnu.org/licenses/lgpl.html for details."+"\n\n"+"Date selection:\n"+"- Use the \xab, \xbb buttons to select year\n"+"- Use the "+String.fromCharCode(0x2039)+", "+String.fromCharCode(0x203a)+" buttons to select month\n"+"- Hold mouse button on any of the above buttons for faster selection.";Calendar._TT["ABOUT_TIME"]="\n\n"+"Time selection:\n"+"- Click on any of the time parts to increase it\n"+"- or Shift-click to decrease it\n"+"- or click and drag for faster selection.";Calendar._TT["PREV_YEAR"]="Prev. year (hold for menu)";Calendar._TT["PREV_MONTH"]="Prev. month (hold for menu)";Calendar._TT["GO_TODAY"]="Go Today";Calendar._TT["NEXT_MONTH"]="Next month (hold for menu)";Calendar._TT["NEXT_YEAR"]="Next year (hold for menu)";Calendar._TT["SEL_DATE"]="Select date";Calendar._TT["DRAG_TO_MOVE"]="Drag to move";Calendar._TT["PART_TODAY"]=' ('+"Today"+')';Calendar._TT["DAY_FIRST"]="Display %s first";Calendar._TT["WEEKEND"]="0,6";Calendar._TT["CLOSE"]="Close";Calendar._TT["TODAY"]="Today";Calendar._TT["TIME_PART"]="(Shift-)Click or drag to change value";Calendar._TT["DEF_DATE_FORMAT"]="%b %e, %Y";Calendar._TT["TT_DATE_FORMAT"]="%B %e, %Y";Calendar._TT["WK"]="Week";Calendar._TT["TIME"]="Time:";CalendarDateObject._LOCAL_TIMZEONE_OFFSET_SECONDS=-28800;function quote_login()
{flag=1;if(document.getElementById('email').value=='')
{document.getElementById('email').className=document.getElementById('email').className+" validation-failed";flag=0;}
if(document.getElementById('pass').value=='')
{document.getElementById('pass').className=document.getElementById('pass').className+" validation-failed";flag=0;}
if(flag==0)
return false;else
quote_ajax_login();}
Calendar.setup({inputField:'inhanddate',ifFormat:"%Y-%m-%d",showsTime:false,button:'inhanddate_trig',singleClick:true});function same_billing(check)
{if(check)
{document.getElementById('top_billing:telephone').value=document.getElementById('top_shipping:telephone').value;document.getElementById('top_billing:postcode').value=document.getElementById('top_shipping:postcode').value;document.getElementById('top_billing:city').value=document.getElementById('top_shipping:city').value;document.getElementById('top_billing:street1').value=document.getElementById('top_shipping:street1').value;document.getElementById('top_billing:street2').value=document.getElementById('top_shipping:street2').value;var idx1=document.getElementById('top_shipping:country_id').selectedIndex;var idx2=document.getElementById('top_shipping:region_id').selectedIndex;document.getElementById('top_billing:region_id').options[idx2].selected=true;document.getElementById('top_billing:country_id').options[idx1].selected=true;}
else{document.getElementById('top_billing:telephone').value='';document.getElementById('top_billing:postcode').value='';document.getElementById('top_billing:city').value='';document.getElementById('top_billing:street1').value='';document.getElementById('top_billing:street2').value='';document.getElementById('top_billing:region_id').options[0].selected=true;document.getElementById('top_billing:country_id').options[0].selected=true;}}
function changeHover(element,catId)
{jQuery('#cat_image_'+jQuery('#catimage').val()).hide();jQuery('#catimage').attr('value',catId);jQuery('#cat_image_'+catId).show();}
function settop(element)
{jQuery(element).parent().parent().find('li').removeClass('select');jQuery(element).parent().addClass('select');jQuery('#topcat').attr('value',jQuery(element).parent().index('li'));}
jQuery(document).ready(function(){jQuery('#topcat').attr('value',jQuery('html').find('.select').index('li'));});function onHover(element)
{jQuery(element).find('.display-list-category-details').show();jQuery(element).hover(function()
{jQuery(element).find('.display-list-category-details').show();},function()
{jQuery(element).find('.display-list-category-details').hide();});}
var idarray=Array();function linkto(url)
{jQuery('.waitdiv').show();jQuery('.overlay').show();location.href=url;}
jQuery(document).ready(function(){jQuery('.right_float').bxSlider({mode:'vertical',slideMargin:5});jQuery(window).scroll(function(){;var height=jQuery('.product-shop').css('height').split('px');if(parseInt(height)<550)
{if(jQuery(this).scrollTop()>200&&jQuery(this).scrollTop()<480)
jQuery('.product-shop').css('top',jQuery(this).scrollTop()-220);}});jQuery(".bx-wrapper").css('left','0px');jQuery(".bx-wrapper").delay(1500).animate({'left':'350px'},1200);jQuery('.mini-upsell-ite').click(function(){jQuery('.total_div').find("li").css('display','none');var currentid=jQuery(this).attr('id');var splitval=currentid.split('_');var tergetid='#details_'+splitval[1];jQuery("#currentid").val(splitval[1]);var nowid=idarray.indexOf(jQuery("#currentid").val());if(!idarray[nowid-1])
jQuery(".addprev").css('display','none');else
jQuery(".addprev").css('display','block');if(!idarray[nowid+1])
jQuery(".addnext").css('display','none');else
jQuery(".addnext").css('display','block');jQuery(tergetid).css('display','block');jQuery('.total_div').css('display','block');jQuery('.overlay').css('display','block');});jQuery(".close_div").click(function(){jQuery('.total_div').css('display','none');jQuery('.overlay').hide();});jQuery(".addnext").click(function(){var nowid=idarray.indexOf(jQuery("#currentid").val());nowid++;var val=idarray[nowid];if(idarray[nowid-1])
jQuery(".addprev").css('display','block');if(val)
{jQuery('#details_'+jQuery("#currentid").val()).css('display','none');var tergetid='#details_'+val;jQuery("#currentid").val(val);jQuery(tergetid).css('display','block');nowid++;if(!idarray[nowid])
jQuery(".addnext").css('display','none');}});jQuery(".addprev").click(function(){var nowid=idarray.indexOf(jQuery("#currentid").val());nowid--;var val=idarray[nowid];if(idarray[nowid+1])
jQuery(".addnext").css('display','block');if(val)
{jQuery('#details_'+jQuery("#currentid").val()).css('display','none');var tergetid='#details_'+val;jQuery("#currentid").val(val);jQuery(tergetid).css('display','block');nowid--;if(!idarray[nowid])
jQuery(".addprev").css('display','none');}});});function getNextVal(array1,val){array1=array1.slice(0).sort(function(a,b){return a-b;});for(var i=0;i<array1.length;i++)
if(array1[i]>=val)
return array1[i];}
function changePrice(element)
{var orginal=jQuery('#popup_price').val();var addval=jQuery('option:selected',element).attr('price');var net=parseInt(orginal)+parseInt(addval);alert(net);jQuery('.popup_price').html('$'+net);}
jQuery(document).ready(function(){var tmp=jQuery(".success-msg ul li span").html();var has_val=tmp.indexOf("was added to your shopping cart.");if(has_val>0)
{jQuery(".amount a").trigger("click");}});jQuery(document).ready(function(){jQuery(".hover_pro").hover(function(e){if(e.target.className=="product-image"||e.target.className=="product-name")
{jQuery(this).find(".product-detail-content").addClass("open");jQuery(this).find(".product-detail-content").animate({"top":0},700);}},function(){jQuery(this).find(".product-detail-content").removeClass("open");jQuery(this).find(".product-detail-content").animate({"top":400},700);});});
