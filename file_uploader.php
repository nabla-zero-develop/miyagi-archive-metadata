<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>基本情報整理表登録(1:ファイルアップロード)</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body>
    <form action="./item_list.php" method="post" enctype="multipart/form-data">
        <input type="file" name="upfile"  size="100" /><br />
        <table>
            <tr>
				<td><input type="radio" value=0 name="ken_or_shi" checked="checked">県版</td>
				<td><input type="radio" value=1 name="ken_or_shi">市町村版</td>
			</tr>
        </table>
        <input type="submit" value="アップロード" /><br />
    </form>
  </body>
</html>

