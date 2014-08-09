<?php
require_once ('include/config.php');
$lotid = intval($_REQUEST['lotid']);
$userid = intval($_REQUEST['userid']);
$sql = "update lot set userid=$userid where lotid=$lotid";
$res = mysql_query($sql);
if($res){
	header('HTTP', true, 200);
	die('aaa');
}else{
	header('HTTP', true, 403);
	die('aaa');
}
?>