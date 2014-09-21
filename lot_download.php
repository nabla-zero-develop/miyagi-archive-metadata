<?php
require_once 'include/config.php';

$lotid = intval($_REQUEST['lotid']);
$sql = "select uniqid,lotid,ord,filepath,finish from lotfile ".($lotid? "where lotid = $lotid": '');
$res = mysql_query($sql);
$rows = array(explode( ",","ユニークID,ロットID,順序,ファイルパス,終了"));
while($row = mysql_fetch_row($res)){
	$rows[] = $row;
}

mb_convert_variables('SJIS','UTF-8',$rows);

header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"lot$lotid.csv\"");

foreach($rows as $row){
	foreach($row as &$col){
		$col = '"'.str_replace('"', '""', $col).'"';
	}
	echo implode(',',$row)."\r\n";
}

?>