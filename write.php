<?php
mysql_connect('localhost','root','');
mysql_select_db('metadata_system');
mysql_query('set names utf8');

$lot_id = intval($_POST['lot']);
$id = isset($_POST['id'])?intval($_POST['id']):1;

$res = mysql_query("select count(*) as c from lotfiles where lotid=$lot_id");
$row = mysql_fetch_assoc($res);
if(!$row)die("No data for lot no. $lot_id.");
$num_in_lot = $row['c'];

$res = mysql_query("select * from lotfiles where lotid=$lot_id and id=$id");
$row = mysql_fetch_assoc($res);
if(!$row)die("No data for lot no. $lot_id and id $id.");
$uniqid = $row['uniqid'];

mysql_query("delete from content where uniqid=$uniqid");

$query = sprintf("insert into content (uniqid ,md_type ,md_title ,md_copywriter ,md_copywriter_other ,md_copyrigher_uri ,md_copyrighter_yomi ,md_content_year ,md_content_month ,md_content_day ,md_content_hour ,md_content_min ,md_content_sec ,md_publish_year ,md_publish_month ,md_publish_day ,md_setting_year ,md_setting_month ,md_seting_day ,md_setting_place ,md_issue_for ,md_issue_year ,md_issue_month ,md_issue_day ,md_narrator ,md_content_restriction ) values ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
$uniqid, mysql_real_escape_string($_POST['md_type']), mysql_real_escape_string($_POST['md_title']), mysql_real_escape_string($_POST['md_copywriter']), mysql_real_escape_string($_POST['md_copywriter_other']), mysql_real_escape_string($_POST['md_copyrigher_uri']), mysql_real_escape_string($_POST['md_copyrighter_yomi']), mysql_real_escape_string($_POST['md_content_year']), mysql_real_escape_string($_POST['md_content_month']), mysql_real_escape_string($_POST['md_content_day']), mysql_real_escape_string($_POST['md_content_hour']), mysql_real_escape_string($_POST['md_content_min']), mysql_real_escape_string($_POST['md_content_sec']), mysql_real_escape_string($_POST['md_publish_year']), mysql_real_escape_string($_POST['md_publish_month']), mysql_real_escape_string($_POST['md_publish_day']), mysql_real_escape_string($_POST['md_setting_year']), mysql_real_escape_string($_POST['md_setting_month']), mysql_real_escape_string($_POST['md_seting_day']), mysql_real_escape_string($_POST['md_setting_place']), mysql_real_escape_string($_POST['md_issue_for']), mysql_real_escape_string($_POST['md_issue_year']), mysql_real_escape_string($_POST['md_issue_month']), mysql_real_escape_string($_POST['md_issue_day']), mysql_real_escape_string($_POST['md_narrator']), mysql_real_escape_string($_POST['md_content_restriction']));
$res = mysql_query($query);
if(!$res)die($query.mysql_error());

if($num_in_lot == $id){
	header('Location: index.php');
}else{
	header("Location: metadata.php?lot=$lot_id&id=".($id+1));
}
?>
