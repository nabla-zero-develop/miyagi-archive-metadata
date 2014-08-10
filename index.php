<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/jquery/jquery-1.8.0.min.js"></script>
<body>
<style>
/*
td,th{
	border: solid 1px;
}
table{
	border-collapse: collapse;
}
*/
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
	$('input[value=開始]').click(function(){
		var lotid = $(this).attr('lotid');
		location.href = "metadata.php?lotid="+lotid;
});
	$('input[value=再開]').click(function(){
		var lotid = $(this).attr('lotid');
		location.href = "metadata.php?resume=1&lotid="+lotid;
	});
});
</script>

<h3>メタデータ設定システム</h3>

<?php
require_once('include/config.php');
require_once('include/db.php');
require_once('include/login_info.php');

echo "<div style='text-align:right;'>{$_SESSION['username']}</div>";
?>

<hr>
<table  border="3" cellpadding="3">
<tr><th>ロットNo.</th><th>進捗</th><th>作業</th></tr>

<?php

$lots = mysql_get_multi_rows( "select * from lot where userid = {$_SESSION['userid']}");
foreach($lots as $lot){
	$button = "";
	if($lot['status'] == 0){
		$shinchoku = '未着手';
	}elseif($lot['status'] == 1){
		$countdatas = mysql_get_multi_rows("select count(*),finish from lotfile where lotid={$lot{'lotid'}} group by finish");
		$counts = array(0=>0,1=>0);
		$total = 0;
		foreach($countdatas as $c){
			$counts[$c['finish']] = $c['count(*)'];
			$total += $c['count(*)'];
		}
		$shinchoku = "$counts[1]/$total";
		if(isset($counts[1]))$button .= "<input type='button' value='再開' lotid={$lot['lotid']}>";
	}else{
		$shinchoku = '完了';
	}
	$button .= "<input type='button' value='開始' lotid={$lot['lotid']}>";
	echo "<tr><td>{$lot['lotid']}</td><td>$shinchoku</td><td>$button</td></tr>";
}
?>

</table>
</body>
</html>
