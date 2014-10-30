<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="js/SpryTabbedPanels/SpryTabbedPanels.css" />
<script src="js/SpryTabbedPanels/SpryTabbedPanels.js"></script>
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
td.skipped{
	text-align: right;
}
#KeywordTabbedPanel{
	margin-left:auto;
	margin-right:auto;
	width: 30em;
}
</style>

<script>
$(document).ready(function(){
	$('input[value=開始]').click(function(){
		var lotid = $(this).attr('lotid');
		location.href = "metadata.php?resume=1&lotid="+lotid;
});
	$('input[value=修正]').click(function(){
		var lotid = $(this).attr('lotid');
		location.href = "metadata.php?lotid="+lotid;
});
	$('input[value=再開]').click(function(){
		var lotid = $(this).attr('lotid');
		location.href = "metadata.php?resume=1&lotid="+lotid;
	});
	$('input[value=入力]').click(function(){
		var lotid = $(this).attr('lotid');
		location.href = "metadata.php?skipped=1&lotid="+lotid;
	});
	var KeywordTabbedPanel = new Spry.Widget.TabbedPanels("KeywordTabbedPanel");
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

<?php

$lots = mysql_get_multi_rows( "select * from lot where userid = {$_SESSION['userid']} order by lotid");
$ntabs = floor(count($lots)/100)+1;
?>
<div id='KeywordTabbedPanel' class='TabbedPanels'>
<ul class='TabbedPanelsTabGroup'>
<?php
for($i=0;$i<$ntabs;$i++){
	$label = ($i*100+1).'-'.($i*100+100);
	echo "	<li class='TabbedPanelsTab' tabindex=''>$label</li>\n";
}
echo "</ul>\n";
echo "<div class='TabbedPanelsContentGroup'>\n";
foreach($lots as $idx => $lot){
	if(($idx%100)==0){
		if($idx!=0){
			echo "</table></div>\n";
		}
		echo "<div class='TabbedPanelsContent'>\n";
		echo "<table border='3' cellpadding='3'>\n";
		echo "<tr><th>ロットNo.</th><th>進捗</th><th>作業</th><th>保留データ</th></tr>\n";
	}
	$button = "";
	$skipped_num = '';
	$skipped_button = '';
	if($lot['status'] == 0){
		$shinchoku = '未着手';
		$button .= "<input type='button' value='開始' lotid={$lot['lotid']}>";
	}elseif($lot['status'] == 1){
		$countdatas = mysql_get_multi_rows("select count(*),finish from lotfile where lotid={$lot{'lotid'}} group by finish");
		$counts = array(0=>0,1=>0);
		$total = 0;
		foreach($countdatas as $c){
			$counts[$c['finish']] = $c['count(*)'];
			$total += $c['count(*)'];
		}
		$shinchoku = "$counts[1]/$total";
		if(isset($counts['1'])){
			if(isset($counts['0']) && $counts['0']>0){
				$button .= "<input type='button' value='再開' lotid={$lot['lotid']}>";
			}
			$button .= "<input type='button' value='修正' lotid={$lot['lotid']}>";
		}else{
			$button .= "<input type='button' value='開始' lotid={$lot['lotid']}>";
		}
		if(isset($counts['-1'])){
			$skipped_num = $counts['-1'].'件';
			$skipped_button = "<input type='button' value='入力' lotid={$lot['lotid']}>";
		}else{
			$skipped_num = 'なし';
			$skipped_button = '';
		}
	}else{
		$shinchoku = '完了';
		$button .= "<input type='button' value='修正' lotid={$lot['lotid']}>";
	}
	$lotid = sprintf("%03d",$lot['lotid']);
	echo "<tr><td>{$lotid}</td><td>$shinchoku</td><td>$button</td><td class='skipped'>{$skipped_num}$skipped_button</td></tr>\n";
}
if($lots){
	echo "</table></div>\n";
}
echo "</div>\n";
echo "</div>\n";
?>


</body>
</html>
