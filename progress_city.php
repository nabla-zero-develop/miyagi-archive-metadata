<?php
require_once('include/config.php');
require_once('include/db.php');

?>
<?php
if(isset($_REQUEST['download'])){
	$rows[] = array('自治体コード','自治体名','登録件数','入力件数');

	$cdraw = mysql_get_multi_rows("select local_code as cdcode, name, count(*) as num from baseinfo inner join (select name,`code` from citycode union select name,`code` from divisioncode) cd on local_code = `code` group by cdcode order by cdcode ");
	//$cdraw = mysql_get_multi_rows("select cdcode,name,count(*) as num from lotfile inner join (select name,`code` from citycode union select name,`code` from divisioncode) cd on cdcode = `code` group by cdcode order by cdcode");
	$cds = array();
	foreach($cdraw as $cdraw){
		$cds[$cdraw['cdcode']] = $cdraw;
	}
	$finishes = mysql_get_multi_rows("select cdcode,count(*) as c from lotfile where finish = 1 group by cdcode");
	foreach($finishes as $f){
		$cds[$f['cdcode']]['finish'] = $f['c'];
	}
	foreach($cds as $cd){
		$rows[] = array($cd['cdcode'],$cd['name'],$cd['num'],$cd['finish']);
	}

	mb_convert_variables('SJIS','UTF-8',$rows);

	header('Content-Type: application/octet-stream');
	header("Content-Disposition: attachment; filename=\"progress_city.csv\"");

	foreach($rows as $row){
		foreach($row as &$col){
			$col = '"'.str_replace('"', '""', $col).'"';
		}
		echo implode(',',$row)."\r\n";
	}
	exit();
}
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/jquery/jquery-1.8.0.min.js"></script>

<style>
body{
	text-align: center;
	background-color: #efefef;
}
table{
	margin-left:auto;
	margin-right:auto;
}
</style>

<script>
$(document).ready(function(){
});
</script>
<table  border="3" cellpadding="3">
<tr><th>自治体コード</th><th>自治体名</th><th>登録件数</th><th>入力件数</th></tr>
<?php
$cdraw = mysql_get_multi_rows("select local_code as cdcode, name, count(*) as num from baseinfo inner join (select name,`code` from citycode union select name,`code` from divisioncode) cd on local_code = `code` group by cdcode order by cdcode ");
$cds = array();
foreach($cdraw as $cdraw){
	$cdraw['finish'] = 0;
	$cds[$cdraw['cdcode']] = $cdraw;
}
$finishes = mysql_get_multi_rows("select cdcode,count(*) as c from lotfile where finish = 1 group by cdcode");
foreach($finishes as $f){
	$cds[$f['cdcode']]['finish'] = $f['c'];
}
foreach($cds as $cd){
	printf("<tr><th>%d</th><td>%s</td><td>%d</td><td>%d</td></tr>\n",
		$cd['cdcode'],$cd['name'],$cd['num'],$cd['finish']);
}
?>
</table>
<a href='progress_city.php?download'>CSV取得</a><br>
<a href='admin.php'>管理画面へ</a>
</html>
<?php
?>
