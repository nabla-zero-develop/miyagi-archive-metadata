<?php
require_once('include/config.php');
require_once('include/db.php');

$start = isset($_REQUEST['start'])?intval($_REQUEST['start']):0;
$limit = isset($_REQUEST['limit'])?intval($_REQUEST['limit']):100;
?>
<?php
if(isset($_REQUEST['download'])){
	$rows[] = array('ユニークID','自治体名','ロットID','ステータス');

	$sql = <<<__SQL__
	select baseinfo.uniqid,name as cdname,lotid,finish from baseinfo
	 left join lotfile on baseinfo.uniqid = lotfile.uniqid
	left join (select name,`code` from citycode union select name,`code` from divisioncode) cd on floor(baseinfo.uniqid/1000000) = `code`
__SQL__;
	$lists = mysql_get_multi_rows($sql);

	foreach($lists as $item){
		$rows[] = array($item['uniqid'],$item['cdname'],$item['lotid'],$item['finish']);
	}

	mb_convert_variables('SJIS','UTF-8',$rows);

	header('Content-Type: application/octet-stream');
	header("Content-Disposition: attachment; filename=\"baseinfo_list.csv\"");

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

<?php
//ページング
$num = mysql_get_value('select count(*) from baseinfo');

if($num > $limit){
	$pagenum = intval(ceil($num/$limit));
	$options = '';
	for($i=0;$i<$num/$limit;$i++){
		$ii = $i+1;
		$options .= "<option name='start' value='$i'>$ii</option>";
	}
?>
<form>
<select name='start'><?php echo $options; ?></select>/<?php echo $pagenum; ?>ページ
<input type='submit' value='Go'>
</form>
<?php
}
?>

<table  border="3" cellpadding="3">
<tr><th>ユニークID</th><th>自治体名</th><th>ロットID</th><th>ステータス</th></tr>
<?php
$sql = <<<__SQL__
select baseinfo.uniqid,name as cdname,lotid,finish from baseinfo
 left join lotfile on baseinfo.uniqid = lotfile.uniqid
left join (select name,`code` from citycode union select name,`code` from divisioncode) cd on floor(baseinfo.uniqid/1000000) = `code`
limit $start,$limit
__SQL__;
$lists = mysql_get_multi_rows($sql);

foreach($lists as $item){
	printf("<tr><th>%d</th><td>%s</td><td>%03d</td><td>%d</td></tr>\n",
		$item['uniqid'],$item['cdname'],$item['lotid'],$item['finish']);
}
?>
</table>
<a href='baseinfo_list.php?download'>CSV取得</a><br>
<a href='admin.php'>管理画面へ</a>
</html>
