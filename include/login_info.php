<?php
if(isset($_REQUEST['userid'])){
	$_SESSION['userid'] = intval($_REQUEST['userid']);
}elseif(!isset($_SESSION['userid'])){
	$_SESSION['userid'] = 1;
}

$userid = $_SESSION['userid'];
$userinfo = mysql_get_single_row( "select * from users where userid={$userid}" );

$_SESSION['username'] = $userinfo['username'];
$_SESSION['userid'] = $userinfo['userid'];

?>