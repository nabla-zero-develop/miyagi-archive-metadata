<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/jquery/jquery-1.8.0.min.js"></script>
<script>
$(document).ready(function(){
        $('select[name="update_insert"]').change(update_insert_change);
        update_insert_change();
});
function update_insert_change(){
        if($('select[name=update_insert]').val() == ''){
                $('input[type=submit][name=do]').attr('disabled','disabled');
        }else{
                $('input[type=submit][name=do]').removeAttr('disabled');
        }
}
</script>
<form action='lot_upload.php' method="post" enctype="multipart/form-data">
データ追加/更新<select name='update_insert'><option value=''>選択してください</option><option value='insert'>追加</option><option value='update'>更新</option></select><br>
<input type='file' name='lot_csv'><br>
<input type='submit' name='do' value='送信'>
</form>
<a href='lot.php'>ロット一覧へ</a>
<hr>

<?php
require_once('include/config.php');

if(isset($_REQUEST['update_insert'])){
	$error = '';
	if($_FILES['lot_csv']['error']){
		switch($_FILES['lot_csv']['error']){
		case UPLOAD_ERR_NO_FILE:
			$error = 'ファイルが指定されていません。';
			break;
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			$error = 'ファイルが大きすぎます。';
			break;
		default:
			$error = 'エラーが発生しました。';
			break;
		}
	}

	if( !$error and $_FILES['lot_csv']['size']==0 ){
		$error = '空のファイルです。';
	}

	//Exelファイルの排除
	if( !$error and preg_match('/\.xlsx?$/i',$_FILES['lot_csv']['name'],$aaa))
	{
		$error = 'アップロードされたファイルはEXELファイルのようです。<br>';
		$error .= 'csv形式のファイルをアップロードしてください。<br>';
	}

	if($error){
		echo $error;
	}else{

		//文字コードの変換
		$tmp = file_get_contents($_FILES['lot_csv']['tmp_name']);
		if(mb_check_encoding($tmp,'SJIS')) $encoding = 'SJIS';
		elseif(mb_check_encoding($tmp,'UTF-16')) $encoding = 'UTF-16';
		elseif(mb_check_encoding($tmp,'UTF-8')) $encoding = 'UTF-8';
		else $encoding = mb_detect_encoding($tmp);
		if( $encoding != 'UTF-8' ){
			$tmp = mb_convert_encoding($tmp,'UTF-8', $encoding);
			$file = tmpfile();
			fwrite($file,$tmp);
			fseek($file,0);
		}else{
			$file = fopen( $_FILES['lot_csv']['tmp_name'], 'r' );
			//BOMの除去
			if( ord(substr($tmp,0,1)) == 0xef and ord(substr($tmp,1,1)) == 0xbb and ord(substr($tmp,2,1)) == 0xbf ){
				fseek($file,3);
			}
		}
		unset($tmp);
		$delimiter = ",";

		mysql_query('START TRANSACTION');
		$error = 0;
		setlocale(LC_ALL, 'ja_JP.UTF-8');
		$newlotid = 0;
		$numrows = 0;
		$lotMaxOrd = array();
		while($row = fgetcsv($file,null,$delimiter)){
			if($row[0] == 'ユニークID'){//ヘッダー行をスキップ
				continue;
			}
			$uniqid = $row[0];
			$lotid = intval($row[1]);
			$ord = intval($row[2]);
			$filepath = mysql_real_escape_string($row[3]);
			$finish = intval($row[4]);
			if(!file_exists($file_basepath.$filepath)){//ファイルがあるかチェック
				echo "指定されたディレクトリがありません<br>\n";
				$error++;
			}

			//新ロットの追加
			if(!$lotid){
				if(!$newlotid){
					$sql = 'insert into lot (regist_date) values (now())';
					$res = mysql_query($sql);
					if(!$res)die(mysql_error());
					$newlotid = mysql_insert_id();
					$lotMaxOrd[$newlotid] = 0;
				}
				$lotid = $newlotid;
			}

			//ord(同一ロット内での順序)
			if(!$ord){
				if(isset($lotMaxOrd[$lotid])){
					$ord = ++$lotMaxOrd[$lotid];
				}else{
					$sql = "select max(ord) from lotfile where lotid = $lotid";
					$res = mysql_query($sql);
					$sqlrow = mysql_fetch_array($res);
					if($sqlrow){
						$ord = 1 + intval($sqlrow[0]);
					}else{
						$ord = 1;
					}
					$lotMaxOrd[$lotid] = $ord;
				}
			}

			if($_REQUEST['update_insert']=='update'){
				$sql = 'update lotfile set'.
						" lotid=$lotid,ord=$ord,filepath='$filepath'".
						",finish=$finish".
						" where uniqid = $uniqid";
				$res = mysql_query($sql);
				if(!$res){
					$error++;
					echo "エラーが発生しました:".mysql_error()."<br>\n";
					echo "SQL:$sql<br>";
				}elseif(mysql_affected_rows()!=1){
					//TODO $error++;
					//TODO echo "ユニークID:{$uniqid}に対応するレコードがありません。<br>\n";
				}
			}elseif($_REQUEST['update_insert']=='insert'){
				$sql = "insert into lotfile".
						"(uniqid,lotid,ord,filepath,finish) values".
						"($uniqid,$lotid,$ord,'$filepath',$finish)";
				$res = mysql_query($sql);
				if(!$res){
					$error++;
					echo "エラーが発生しました:".mysql_error()."<br>\n";
					echo "SQL:$sql<br>";
				}
			}

			$numrows++;
		}
		if($error){
			mysql_query('ROLLBACK');
			echo "{$error}件のエラーが発生しました。<br>\n";
		}else{
			mysql_query('COMMIT');
			echo "{$numrows}件登録・更新しました。<br>\n";
			if($newlotid){
				echo "新しいロットIDは{$newlotid}です。<br>\n";
			}
		}
	}
}
?>
<hr>
<a href='lot_tmpl.csv'>CSVテンプレートファイル</a>
<ul>
	<li>1行目は削除する必要はありません。</li>
	<li>新たにロットを作成したい場合は、ロットNoを空欄にしてください。全てのデータが同一ロットとして登録されます。ロットを分けたい場合は、ロットごとにCSVファイルを作成して登録してください。</li>
	<li>順序はロット内での順序です。基本的に空欄にしてください。空欄にした場合は、CSVファイル内に出現する順番になります。指定する場合は、他のデータとの重複が起こらないよう注意してください。</li>
</ul>

<form action='downlot.php'>
ロットNo.<select name='lotid'><option value='0'>全て</option>
<?php
$sql = "select * from lot order by lotid";
$res = mysql_query($sql);
while($row = mysql_fetch_assoc($res)){
	echo "<option>$row[lotid]</option>";
}

?>
</select><input type='submit' value='ダウンロード'>
</form>
</html>
