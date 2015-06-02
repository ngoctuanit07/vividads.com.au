<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Store / Website Relations Script</title>
</head>

<body>

<h1> Store / Website Relations </h1>
<hr/>

<?php 

		$_connection =  mysql_connect('localhost','tablethr_vivid','bdbww2lqdd(#');
		$_db = mysql_select_db('tablethr_vividmagento',$_connection);
		
		$_query = 'select cs.store_id as cssid , cs.code as cscode, csg.name as cgcode, cs.website_id as cswid, cs.group_id as csgid,csg.group_id as cggid, csg.website_id as cgwid, csg.default_store_id as cgsid, cw.code as cwcode,cw.website_id as cwwid, cw.default_group_id as cwgid  from core_store cs  left join core_store_group csg on cs.code=csg.name left join core_website cw ON cs.code=cw.code order by cs.code asc';
		
		$_result = mysql_query($_query) or die(mysql_error());
		$str = '';
		$i=1;
		while($_rows = mysql_fetch_object($_result)){
				
				$str.='<br/>'.$i.'-- <b>Code</b> ';
				$str.=' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong><strong>cs</strong></strong>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>cg</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>cw</strong> ';
				$str.='<br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; w &nbsp; | &nbsp; g | &nbsp; s   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; |  &nbsp;&nbsp;&nbsp;&nbsp; w &nbsp; | &nbsp; g | &nbsp; s &nbsp;&nbsp;&nbsp;&nbsp;|  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; w &nbsp; | &nbsp; g  ';
				
				$str.='<br/>'.$_rows->cscode;
				$str.='<br/>'.$_rows->cgcode;
				$str.='<br/>'.$_rows->cwcode;
				
				$str.='<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_rows->cswid.' | '.$_rows->csgid.' | '.$_rows->cssid;
				
				$str.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_rows->cgwid.' | '.$_rows->cggid.' | '.$_rows->cgsid;
				
				$str.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_rows->cwwid.' | '.$_rows->cwgid;
				
				$str.='<br/><br/>----------------------------------------------------------------------------------------------------------------------';
				$i++;
			}
		echo $str;
?>


</body>
</html>