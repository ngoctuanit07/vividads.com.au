function printContent(id, id2, date, id3, id4, id5, id6){
lineitems=document.getElementById(id).innerHTML;
features=document.getElementById(id2).innerHTML;
weight=id5;
message=id6;

newwin=window.open('','printwin','menubar=yes,left=100,top=100,width=860,height=800,scrollbars=yes')
newwin.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n')
newwin.document.write('<html xmlns="http://www.w3.org/1999/xhtml">\n<head>\n')
newwin.document.write('<title>Print Page</title>\n')
newwin.document.write('<script>\n')
newwin.document.write('function chkstate(){\n')
newwin.document.write('if(document.readyState=="complete"){\n')
//newwin.document.write('window.close()\n')
newwin.document.write('}\n')
newwin.document.write('else{\n')
newwin.document.write('setTimeout("chkstate()",2000)\n')
newwin.document.write('}\n')
newwin.document.write('}\n')
newwin.document.write('function print_win(){\n')
newwin.document.write('window.print();\n')
newwin.document.write('chkstate();\n')
newwin.document.write('}\n')
newwin.document.write('<\/script>\n')
newwin.document.write('<link href="css/print-quote.css" rel="stylesheet" type="text/css" />\n')
newwin.document.write('</head>\n')
newwin.document.write('<body onload="print_win()">\n')
//newwin.document.write('<body>\n')
newwin.document.write('<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td><img src="images/logo.png" width="220" height="128" alt="DuraBanners.com" class="logo" /></td><td align="right"><p class="estimate-heading">www.durabanners.com<br /><b>Estimate Created On:</b> '+date+'</p></td></tr></table>')
newwin.document.write(lineitems)
newwin.document.write(features)
newwin.document.write('<table width="94%" border="0" cellpadding="0" cellspacing="0" class="totals"><tr><td width="65%"><p class="left"><b>Weight: </b>'+weight+' kg</p><p class="right">'+message+'</p></td><td width="10%" align="right"><b>SHIPPING</b></td><td align="right" width="25%">'+id3+'</td></tr><tr><td></td><td align="right" class="finalprice"><b>TOTAL</b></td><td align="right" class="finalprice">'+$defaultcurrsymbol+id4+'</td></tr></table>')
newwin.document.write('</body>\n')
newwin.document.write('</html>\n')
newwin.document.close()
}