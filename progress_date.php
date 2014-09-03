<?php
require_once('include/config.php');
require_once('include/db.php');

?>
<?php
if(isset($_REQUEST['download'])){
	$rows[] = array('入力日','入力件数');

	$dates = mysql_get_multi_rows("select DATE_FORMAT(finish_date,'%c/%e') as finish_date_date,count(*) as num from lotfile where finish = 1 group by finish_date order by finish_date_date");
	foreach($dates as $date){
		$rows[] = array($date['finish_date_date'],$date['num']);
	}

	mb_convert_variables('SJIS','UTF-8',$rows);

	header('Content-Type: application/octet-stream');
	header("Content-Disposition: attachment; filename=\"progress_date.csv\"");

	foreach($rows as $row){
		foreach($row as &$col){
			$col = '"'.str_replace('"', '""', $col).'"';
		}
		echo implode(',',$row)."\r\n";
	}
	exit();
}
?><html>
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
<tr><th>入力日</th><th>入力件数</th></tr>
<?php
$dates = mysql_get_multi_rows("select DATE_FORMAT(finish_date,'%c/%e') as finish_date_date,count(*) as num from lotfile where finish = 1 group by finish_date order by finish_date_date");
foreach($dates as $date){
	printf("<tr><th>%s</th><td>%s</td></tr>\n",
		$date['finish_date_date'],$date['num']);
}
?>
</table>
<a href='progress_date.php?download'>CSV取得</a><br>
<a href='admin.php'>管理画面へ</a>
</html>
<?php
?>
