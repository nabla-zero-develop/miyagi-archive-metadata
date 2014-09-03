<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>基本情報整理表登録(1:ファイルアップロード)</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
    table.baseinfo_file{

    }
    </style>
  </head>
  <body>
    <form action="./baseinfo_write.php" method="post" enctype="multipart/form-data">
        <input type="file" name="upfile"  size="100" /><br />
        <table>
            <tr>
				<td><input type="radio" value=0 name="ken_or_shi" checked="checked">県版</td>
				<td><input type="radio" value=1 name="ken_or_shi">市町村版</td>
			</tr>
        </table>
        <input type="submit" value="アップロード" /><br />
    </form>
	<h2>アップロード済み基本情報整理表</h2>
		<table id='baseinfo_file' cellpadding="3" border="3">
<?php
require_once('include/config.php');
require_once('include/db.php');
$baseinfo_files = mysql_get_multi_rows("select * from baseinfo_file inner join (select name,`code` from citycode union select name,`code` from divisioncode) cd on cdcode = `code`");
foreach($baseinfo_files as $baseinfo_file){
	$cdname = $baseinfo_file['name'];
	$filename = htmlspecialchars($baseinfo_file['filename']);
	echo "<tr><td>$cdname</td><td>$filename</td></tr>\n";
}
?>

		</table>
    </body>
</html>

