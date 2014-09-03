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
<tr><th>ロット</th><th>入力件数</th><th>未入力件数</th></tr>
<?php
$lotlaws = mysql_get_multi_rows("select lotid,count(*) as num from lotfile group by lotid order by lotid");
$lots = array();
foreach($lotlaws as $lotlaw){
	$lotlaw['finish'] = 0;
	$lotlaw['unfinish'] = $lotlaw['num'];
	$lots[$lotlaw['lotid']] = $lotlaw;
}
$finishes = mysql_get_multi_rows("select lotid,count(*) as c from lotfile where finish = 1 group by cdcode");
foreach($finishes as $f){
	$lotid = $f['lotid'];
	$lot = &$lots[$lotid];
	$lot['finish'] = $f['c'];
	$lot['unfinish'] = $lot['num'] - $f['c'];
}
foreach($lots as $l){
	printf("<tr><th>%03d</th><td>%s</td><td>%d</td></tr>\n",
		$l['lotid'],$l['finish'],$l['unfinish']);
}
?>
</table>
<a href='admin.php'>管理画面へ</a>
</html>
<?php
?>
