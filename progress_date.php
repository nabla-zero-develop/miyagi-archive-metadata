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
<tr><th>入力日</th><th>入力件数</th></tr>
<?php
$dates = mysql_get_multi_rows("select DATE_FORMAT(finish_date,'%c/%e') as finish_date_date,count(*) as num from lotfile where finish = 1 group by finish_date order by finish_date_date");
foreach($dates as $date){
	printf("<tr><th>%s</th><td>%s</td></tr>\n",
		$date['finish_date_date'],$date['num']);
}
?>
</table>
<a href='admin.php'>管理画面へ</a>
</html>
<?php
?>
