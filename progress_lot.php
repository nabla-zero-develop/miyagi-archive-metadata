<?php
require_once('include/config.php');
require_once('include/db.php');

?>
<?php
if(isset($_REQUEST['download'])){
	$rows[] = array('ロット','入力件数','未入力件数');

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
		$rows[] = array($l['lotid'],$l['finish'],$l['unfinish']);
	}
	mb_convert_variables('SJIS','UTF-8',$rows);

	header('Content-Type: application/octet-stream');
	header("Content-Disposition: attachment; filename=\"progress_lot.csv\"");

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
<?php
require_once('include/config.php');
require_once('include/db.php');

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
<a href='progress_lot.php?download'>CSV取得</a><br>
<a href='admin.php'>管理画面へ</a>
</html>
<?php
?>
