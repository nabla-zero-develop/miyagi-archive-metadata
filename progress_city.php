<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/jquery/jquery-1.8.0.min.js"></script>
<?php
require_once('include/config.php');
require_once('include/db.php');

?>

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
$cdraw = mysql_get_multi_rows("select cdcode,name,count(*) as num from lotfile inner join (select name,`code` from citycode union select name,`code` from divisioncode) cd on cdcode = `code` group by cdcode order by cdcode");
$cds = array();
foreach($cdraw as $cdraw){
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
<a href='admin.php'>管理画面へ</a>
</html>
<?php
?>
