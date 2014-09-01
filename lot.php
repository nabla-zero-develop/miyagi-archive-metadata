<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/jquery/jquery-1.8.0.min.js"></script>
<?php
require_once('include/config.php');
require_once('include/db.php');

$users = array();
$sql = "select * from users order by userid";
$res = mysql_query($sql);
while($row = mysql_fetch_assoc($res)){
	$users[$row['userid']] = $row['username'];
}
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
	$('button.lot_user').click(lot_user);
	$('select.lot_user').change(lot_user_sel);
});
function lot_user(){
	var lotid = $(this).attr('lotid');
	var userid = $('select.lot_user[lotid='+lotid+']').val();
	$('select.lot_user[lotid='+lotid+']').attr('disabled','disabled');
	$('button.lot_user[lotid='+lotid+']').attr('disabled','disabled');
	$.ajax( "lot_user.php?lotid="+lotid+"&userid="+userid )
		.done(function() {

		})
		.fail(function() {
			alert( "error" );
		})
		.always(function() {
			$('select.lot_user[lotid='+lotid+']').removeAttr('disabled');
			$('button.lot_user[lotid='+lotid+']').removeAttr('disabled');
		});
}
function lot_user_sel(){
	var lotid = $(this).attr('lotid');
	$('button.lot_user[lotid='+lotid+']').removeAttr('disabled');
}
</script>

<a href='lot_upload.php'>CSVで編集</a>
<table  border="3" cellpadding="3">
<tr><th>ロットNo.</th><th>進捗</th><th>開始日時</th><th>終了日時</th><th>割り当て</th></tr>
<?php
$lots = mysql_get_multi_rows("select * from lot order by lotid");
foreach($lots as $lot){
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
	}else{
		$shinchoku = '完了';
	}

	printf("<tr><th>%03d</th><td>%s</td><td>%s</td><td>%s</td><td>%s<button lotid='%d' class='lot_user' disabled>変更</button></td></tr>\n",
		$lot['lotid'],$shinchoku,format_date($lot['start_date']),format_date($lot['finish_date']),users_select($lot['lotid'],$lot['userid']),$lot['lotid']);
}
?>
</table>
<a href='admin.php'>管理画面へ</a>
</html>
<?php
function format_date($str){
	return $str? date('m月d日 H:i',strtotime($str)): '-';
}
function users_select($lotid,$userid){
	global $users;
	$options = "<option value=''></option>";
	foreach($users as $uid => $uname){
		$selected = ($userid == $uid? 'selected=selected': '');
		$options .= "<option value='$uid' $selected>$uid:$uname</option>";
	}
	return "<select class='lot_user' lotid=$lotid>$options</select>";
}
?>
