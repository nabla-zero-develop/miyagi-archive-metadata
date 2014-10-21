<?php
include_once(dirname(__FILE__) . "/Classes/PHPExcel.php");
include_once(dirname(__FILE__) . "/Classes/PHPExcel/IOFactory.php");

ignore_user_abort(1);
set_time_limit(86400);
ini_set('memory_limit' ,'1024M');

////
//// 定数
////

// エクセルバージョン
define("EXCEL2007", "Excel2007");
define("EXCEL5", "Excel5");
// エクセルファイルアップロードに関するエラー定義
define("EXCEL_FILE_NOT_PREPARED", "-10");
define("EXCEL_FILE_NOT_UPLOADED", "-20");
// 整理表シートに関するエラー定義
define("SHEET_TITLE_ERR", "-30");
define("KEN_LEFT_END_ERR", "-41");
define("SHI_LEFT_END_ERR", "-42");
define("RIGHT_END_ERR", "-50");
define("HEADER_BOTTOM_ERR", "-60");
define("SHEET_DEF_ERR", "-70");
// 整理表名前
define("INPUT_SHEET_NAME", "整理表");
// 整理表項目位置情報
define("SHEET_TITLE_ROW", "1");
define("SHEET_TITLE_COLUMN", "4");
define("FIRST_DATA_ROW", "9");
define("LAST_COLUMN", "33");
define("DIVISION_NAME_ROW", "2");
define("DIVISION_NAME_COLUMN", "4");
// 整理表列情報
define("SHIRYO_SHUBETU_COLUMN", "1");
define("SHIRYO_SHUBETU_HEADER", "資料種別記号");
define("SHICHOSON_KANRI_BANGOU_COLUMN", "2");
define("SHICHOSON_KANRI_BANGOU_HEADER", "管理番号");
define("SHIRYO_JYURYOUBI_COLUMN", "3");
define("SHIRYO_TEIKYOSHA_COLUMN", "4");
define("SHIRYO_TEIKYOSHA_HEADER", "資料提供者");
define("BUNRUI_CODE_COLUMN", "6");
define("TITLE_COLUMN", "8");
define("TITLE_HEADER", "タイトル");
define("SAKUSEISHA_COLUMN", "9");
define("SAKUSEISHA_HEADER", "作成者・撮影者");
define("SAKUSEIBASHO_COLUMN", "15");
define("SAKUSEIBASHO_HEADER", "撮影場所");
define("KEYWORD_COLUMN", "19");
define("KEYWORD_HEADER", "キーワード");
// メタデータ処理ページ
define("METADATA_INPUT_PAGE", "./metadata45.php");

////
//// 大域変数
////

// エクセルの要求バージョン
$required_excel_version= EXCEL2007;
// 表示する列の定義
$visible_columns = array(
	array(SHIRYO_SHUBETU_COLUMN, SHIRYO_SHUBETU_HEADER),
	array(SHICHOSON_KANRI_BANGOU_COLUMN, SHICHOSON_KANRI_BANGOU_HEADER),
	array(SAKUSEISHA_COLUMN, SAKUSEISHA_HEADER),
	array(TITLE_COLUMN, TITLE_HEADER),
	array(SAKUSEIBASHO_COLUMN, SAKUSEIBASHO_HEADER),
	array(KEYWORD_COLUMN, KEYWORD_HEADER),
	);

// シェル上のテストかウェべページ動作かの判定
$test_flag = !isset($_FILES["upfile"]["name"]);
if($test_flag){
	// テスト用のファイルパス
	$original_file_path = "/home/taka/disks/d/Data/Work/Downloads/食産業振興課【回答様式】基本情報整理表20140606-1.xlsx";
	$uploaded_file_path = tempnam(sys_get_temp_dir(), '');
	copy($original_file_path, $uploaded_file_path);
	//県版か市町村版か(0->県版, 1->市町村版)
	$ken_or_shi = 0;
} else {
	// 実際にアップロードされたファイルのパス
	$original_file_path = $_FILES["upfile"]["name"]; // 日本語表示を含む可能性のある元々のファイル名
	$uploaded_file_path = $_FILES["upfile"]["tmp_name"]; // 内部処理用のテンポラリファイル(エクセルファイルの実体)
	//県版か市町村版か(0->県版, 1->市町村版)
	$ken_or_shi = $_REQUEST['ken_or_shi'];
}

////
//// 関数定義
////

// エクセルのファイルか？
function is_excel_file($file_path, $required_excel_version){
	$ext = pathinfo($file_path, PATHINFO_EXTENSION);
	if($required_excel_version == EXCEL2007){
		return ($ext == "xlsx");
	} else if ($required_excel_version == EXCEL5){
		return ($ext == "xls");
	}
	return FALSE;
}

// ファイル受信
function receive_file($original_file_path, $uploaded_file_path, $test_flag){
	$ext = pathinfo($original_file_path, PATHINFO_EXTENSION);
	// 作業用エクセルファイルの確保
	$excel_file_path = tempnam(sys_get_temp_dir(), 'Excel_').".".$ext;
	if (is_uploaded_file($uploaded_file_path) || $test_flag) {
		if (rename($uploaded_file_path, $excel_file_path)) {
		//if (move_uploaded_file($uploaded_file, $excel_file) || $test_flag) {
			chmod($excel_file_path, 0644); // 不要かも
			return $excel_file_path;
		} else {
			return EXCEL_FILE_NOT_PREPARED;
		}
	} else {
		return EXCEL_FILE_NOT_UPLOADED;
	}
}

// 指定された名前のシートがあるかどうか確認し、取り出す
function find_sheet($sheets, $sheet_name){
    foreach ($sheets as $s => $sheet) {
        if($sheet_name == $sheet->getTitle()){
			return $sheet;
		}
	}
	return FALSE;
}

// セルの内容を文字列で取り出す
function get_text($cell){
	 if (is_null($cell)) return FALSE;
	 $text = "";
	 $value = $cell->getCalculatedValue();
	 if (is_object($value)) {
		 //オブジェクトが返ってきたら、リッチテキスト要素を取得
		 $rtf = $value->getRichTextElements();
		 //配列で返ってくるので、そこからさらに文字列を抽出
		 $parts = array();
		 foreach ($rtf as $v) {
			$parts[] = $v->getText();
		 }
		 //連結する
		$text = implode("", $parts);
	 } else {
		if (!empty($value)) {
			$text = $value;
		}
	}
	//echo "text:".$text."\n";
	return $text;
}

// シートの内容を取り出し配列で返す
function get_sheet_contents($sheet){
	try{
		$rows = $sheet->getHighestDataRow();
		$cols_str = $sheet->getHighestDataColumn();
		$cols = PHPExcel_Cell::columnIndexFromString($cols_str);
		$data = array();
		for($r=1; $r<=$rows; $r++) { //rowは1はじまり
			for($c=0; $c<=$cols; $c++) { //colは0はじまり
				$cell = $sheet->getCellByColumnAndRow($c, $r);
				//echo "CELL: ".$r.":".($c+1)." ".$cell."\n";
				$data[$r][$c]= get_text($cell);
			}
		}
		return $data;
	} catch(Exception $ex){
		return FALSE;
	}
}

// エクセルファイルを読み込み、指定されたシートの内容を配列で返す
function load_sheet($file_path, $sheet_name, $required_excel_version){
	//警告抑止
	error_reporting(0);
	//ワークブックの読み込み
	try{
		//echo "reading ".$file_path;
		//$reader = PHPExcel_IOFactory::createReader($required_excel_version);
		$workbook = PHPExcel_IOFactory::load($file_path);
		//echo "reader created ";
		//$workbook = $reader->load($file_path);
		//echo "read.";
	} catch (Exception $ex){
		//echo $ex;
		return FALSE;
	}
    //シートオブジェクトの取得
    $sheets = $workbook->getAllSheets();
	$sheet = find_sheet($sheets, $sheet_name);
	if(!$sheet){
		return FALSE;
	}
	// 内容取得
	return get_sheet_contents($sheet);
}

// 有効な基本情報整理表かどうかを確認する
function is_valid_seirihyo($data, $ken_or_shi){
	// [row][column]
	if ((trim($data[SHEET_TITLE_ROW][SHEET_TITLE_COLUMN]) <> "基本情報整理表")) return SHEET_TITLE_ERR;
	// 左端
	if($ken_or_shi == 0){
		if ((trim($data[FIRST_DATA_ROW-4][0]) <> "＊課･室・地方機関コード")) return KEN_LEFT_END_ERR;
	} else {
		if ((trim($data[FIRST_DATA_ROW-4][0]) <> "＊市町村ｺｰﾄﾞ")) return SHI_LEFT_END_ERR;
	}
	//右端
	if($ken_or_shi == 0){
		if((trim($data[FIRST_DATA_ROW-4][LAST_COLUMN]) <> "＊作業管理No.")) return RIGHT_END_ERR;
	}else{
		if((trim($data[FIRST_DATA_ROW-4][LAST_COLUMN+1]) <> "＊作業管理No.")) return RIGHT_END_ERR;
	}
		//見出し最下部
	if((trim($data[FIRST_DATA_ROW-1][6]) <> "複数可")) return HEADER_BOTTOM_ERR;
	//大域宣言との整合性確認
	if(FIRST_DATA_ROW != 9) return SHEET_DEF_ERR;
	return TRUE;
}


// HTMLヘッダ部分文字列出力
function output_header(){
	return <<< EOS
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>基本情報整理表登録(2:アップロード内容一覧)</title>
</head>
<body>
<p>
EOS;
}

// CSS
function output_css(){
	return <<< EOS
<STYLE TYPE="text/css">
<!--
.table_0 {
	width: 48%; /* テーブルの横幅 */
	border-collapse: collapse; /* 枠線の表示方法 */
	border: 1px #1C79C6 solid; /* テーブル全体の枠線（太さ・色・スタイル） */
}

.table_1 {
	width: 96%; /* テーブルの横幅 */
	border-collapse: collapse; /* 枠線の表示方法 */
	border: 1px #1C79C6 solid; /* テーブル全体の枠線（太さ・色・スタイル） */
}

.table_1 th {
	border: 1px #1C79C6 solid; /* セルの枠線（太さ・色・スタイル） */
	padding: 4px; /* セル内の余白 */
}

.table_1 td {
	border: 1px #1C79C6 solid;
	padding: 4px;
}

tr.color_0 {
	background-color: #A7C0E8; /* 見出し行の背景色 */
}

tr.color_1 {
	background-color: #C9E2F8; /* 奇数行の背景色 */
}

tr.color_2 {
	background-color: #E3F0FB; /* 偶数行の背景色 */
}

td.color_0 {
	background-color: #A7C0E8; /* 見出し列の背景色 */
}

td.color_1 {
	background-color: #E3F0FB; /* データ列の背景色 */
}
-->
</STYLE>
EOS;
}

// ファイル情報表文字列出力
function output_file_info($original_file_path, $uploaded_file_path, $ken_or_shi, $division_name){
	return "<table class='table_0'>".
			"<caption>アップロードされたファイルの情報</caption>".
			"<tr><td class='color_0'>ファイル名</td><td class='color_1'>".basename($original_file_path)."</td></tr>".
			"<tr><td class='color_0'>ファイルの仕様</td><td class='color_1'>".(($ken_or_shi == 0) ? "県版" : "市町村版")."</td></tr>".
			"<tr><td class='color_0'>部署情報</td><td class='color_1'>".$division_name."</td></tr>".
			"<input type='hidden' name='uploaded_file_path' value='".$uploaded_file_path."'><br><br>\n";
}


// 表示する列かどうか
function is_visible_column($c){
	global $visible_columns;
	foreach($visible_columns as $col_info){
		if($col_info[0] == $c && $c > 0){
			return TRUE;
		}
	}
	return FALSE;
}

// 表示列の見出し
function get_visible_column_header($c){
	global $visible_columns;
	foreach($visible_columns as $col_info){
		if($col_info[0] == $c){
			return $col_info[1];
		}
	}
	return "";
}


// テーブルの出力
function write_table($data, $ken_or_shi, $lot, $filename){
	$num_rows = count($data);
	$s = "<table class='table_1'>\n";
	$s .= "<caption>基本情報整理表主要内容一覧</caption>\n";
	// 見出し
	$s .= "<tr class='color_0'>";
	for ($c=1; $c<=LAST_COLUMN; $c++){
		if(is_visible_column($c)){
			$s .= "<td>".get_visible_column_header($c)."<br></td>\n";
		}
	}
	$s .= "<td></td>\n";
	$s .= "</tr>";

	//市町村コード・件部局コードを取得
	require_once('include/config.php');
	require_once('include/db.php');
	if($ken_or_shi == 0){
		$local_name = $data[DIVISION_NAME_ROW][DIVISION_NAME_COLUMN];
	}else{
		$local_name = $data[DIVISION_NAME_ROW][DIVISION_NAME_COLUMN-2];
	}
	$table = ($ken_or_shi == 0? 'divisioncode': 'citycode');
	$res = mysql_query("select * from $table where name like '%".mysql_real_escape_string($local_name)."'");
	$row = mysql_fetch_assoc($res);
	if($row){
		$local_code = $row['code'];
	}else{
		echo mysql_error();;
		echo ("select * from $table where name like '%".mysql_real_escape_string($local_name)."'");
		die("$local_name:部局コード又は市町村コードがマッチしません");
	}

	//baseinfo_fileテーブルに既に同名のファイルがあれば、そのIDを取得　なければインサート
	$baseinfo_file = mysql_get_single_row(sprintf("select * from baseinfo_file where filename = '%s' and cdcode = %d",
			mysql_real_escape_string($filename),$local_code));
	echo mysql_error();
	if($baseinfo_file){
		$file_id = $baseinfo_file['id'];
	}else{
		mysql_query(sprintf("insert into baseinfo_file (filename,cdcode) values ('%s',%d)",
			mysql_real_escape_string($filename),$local_code));
		echo mysql_error();
		$file_id = mysql_insert_id();
	}


	// データ内容
	for ($r=FIRST_DATA_ROW ; $r<=$num_rows; $r++){
		if (isset($data[$r][1]) && $data[$r][1] != ""){
			$s .= "<tr class='color_". (($r % 2) +1) ."'>"; //<td>".$division_name."</td>";
			$s .= "<form method='post' action ='". METADATA_INPUT_PAGE ."?lot=". $lot ."&"."row_no=".$r."'>\n";
			for ($c=1; $c<=LAST_COLUMN; $c++){
				$item = $data[$r][$c];
				$s .= "<input type='hidden' name='r".$r."c".$c."' value='".$item."'>";
				if(is_visible_column($c)){ //表示項目
					$s .= "<td>".$item."<br></td>";
				}
				$s .= "\n";
			}
			// 整理表以外の項目とボタン
		    $s .= "<td><input type='hidden' name='ken_or_shi' value='".$ken_or_shi."'>\n";
		    $s .= "<input type='hidden' name='orginal_file_path' value='".$original_file_path."'>\n";
		    //$s .= "<input type='submit' value='メタデータ入力'></td>\n";
		    $s .= "</form>\n";
			$s .= "</tr>\n";

//START DBヘ格納
			require_once('include/config.php');
			//基本情報整理表に書かれたデータ（行・列）を変数に格納
			//県版と市町村県版で列が同じ項目

			//A列：課室コード（県版）、市町村コード（市町村版）
			$c = 0;
			$data2['local_code'] =$data[$r][$c];
			//A列のデータは、"#N/A"となるので、別途取得しておいた値を使う
			$data2['local_code'] = $local_code;
			//B列：資料種別
			$c = 1;
			$data2['shubetu'] =$data[$r][$c];
			//C列：課室（県版）、市町村（市町村版）管理番号
			$c = 2;
			$data2['kanri_bango'] =$data[$r][$c];
			//D列：資料受領日
			$c = 3;
			$data2['shiryo_jyuryobi'] =$data[$r][$c];
			//E列：資料提供者
			$c = 4;
			$data2['contributor'] =$data[$r][$c];
			//F列：資料提供者のヨミ
			$c = 5;
			$data2['contributor_yomi'] =$data[$r][$c];
			//G列：分類コード
			$c = 6;
			$data2['bunrui_code'] =$data[$r][$c];
			//H列：文書分類記号（県版）、市町村分類（市町村版）
			$c = 7;
			$data2['bunsho_bunrui'] =$data[$r][$c];
			//I列：タイトル
			$c = 8;  //8行目：タイトル hiddenで次へ(このプログラムの470行目あたり）
			$data2['title'] =$data[$r][$c];
			//J列：撮影者・作成者
			$c = 9;
			$data2['creator'] =$data[$r][$c];
			//K列：作成者のヨミ
			$c = 10;
			$data2['creator_yomi'] =$data[$r][$c];
			//L列：作成日(年)
			$c = 11;
			$data2['sakusei_nen'] = mb_convert_kana($data[$r][$c],'a');
			if(preg_match('/\[([0-9]+)\]/',$data2['sakusei_nen'],$match)){
				$data2['sakusei_nen'] = $match[1];
			}
			$data2['sakusei_nen'] = intval($data2['sakusei_nen']);
			//M列：作成日(月)
			$c = 12;
			$data2['sakusei_tuki'] = mb_convert_kana($data[$r][$c],'a');
					if(preg_match('/\[([0-9]+)\]/',$data2['sakusei_tuki'],$match)){
				$data2['sakusei_tuki'] = $match[1];
			}
			$data2['sakusei_tuki'] = intval($data2['sakusei_tuki']);
			if($data2['sakusei_tuki']>12){
				//月のところへ2/13等と記入してあることがある。その場合、Excelによってシリアル化されて格納されている。
				$tmpstr = PHPExcel_Style_NumberFormat::toFormattedString($data2['sakusei_tuki'], PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
				echo $data2['sakusei_tuki'],$tmpstr,'|';
				$data2['sakusei_tuki'] = date('n',strtotime($tmpstr));
				$data2['sakusei_bi'] = date('j',strtotime($tmpstr));

			}else{
				//N列：作成日(日)
				$c = 13;
				$data2['sakusei_bi'] = mb_convert_kana($data[$r][$c],'a');
						if(preg_match('/\[([0-9]+)\]/',$data2['sakusei_bi'],$match)){
					$data2['sakusei_bi'] = $match[1];
				}
				$data2['sakusei_bi'] = intval($data2['sakusei_bi']);
			}
			//O列：撮影場所（〒番号)
			$c = 14;
			$data2['satuei_basho_zip'] =$data[$r][$c];

			//撮影場所住所
			if($ken_or_shi==0){ //県版
				$c = 15;
				$data2['satuei_basho_address'] =$data[$r][$c];
			}else{
				$c = 16;
				$data2['satuei_basho_address'] =$data[$r][$c];
			}
			//撮影場所住所のヨミ
			if($ken_or_shi==0){ //県版
				$c = 16;
				$data2['satuei_basho_address_yomi'] =$data[$r][$c];
			}else{
				$c = 17;
				$data2['satuei_basho_address_yomi'] =$data[$r][$c];
			}

			//配布場所
			if($ken_or_shi==0){ //県版
				$c = 17;
				$data2['haifu_basho'] =$data[$r][$c];
			}else{
				$c = 18;
				$data2['haifu_basho'] =$data[$r][$c];
			}
			//配布場所のヨミ
			if($ken_or_shi==0){ //県版
				$c = 18;
				$data2['haifu_basho_yomi'] =$data[$r][$c];
			}else{
				$c = 19;
				$data2['haifu_basho_yomi'] =$data[$r][$c];
			}

			//キーワード
			if($ken_or_shi==0){ //県版
				$c = 19;
				$data2['keyword'] =$data[$r][$c];
			}else{
				$c = 20;
				$data2['keyword'] =$data[$r][$c];
			}

			//作成者連絡先住所の〒番号
			if($ken_or_shi==0){ //県版
				$c = 20;
				$data2['renraku_saki_zip'] =$data[$r][$c];
			}else{
				$c = 21;
				$data2['renraku_saki_zip'] =$data[$r][$c];
			}
			//作成者連絡先住所
			if($ken_or_shi==0){ //県版
				$c = 21;
				$data2['renraku_saki_address'] =$data[$r][$c];
			}else{
				$c = 23;
				$data2['renraku_saki_address'] =$data[$r][$c];
			}
			//作成者連絡先電話番号
			if($ken_or_shi==0){ //県版
				$c = 22;
				$data2['renraku_saki_tel'] =$data[$r][$c];
			}else{
				$c = 24;
				$data2['renraku_saki_tel'] =$data[$r][$c];
			}
			//その他の作成者連絡先
			if($ken_or_shi==0){ //県版
				$c = 23;
				$data2['renraku_saki_other'] =$data[$r][$c];
			}else{
				$c = 25;
				$data2['renraku_saki_other'] =$data[$r][$c];
			}

			//権利処理（県版と市町村版で値制約が異なる。県版は「済」「未」、市町村版は9で済）
			//なので、県版の「済」を9に書き換える
			if($ken_or_shi==0){ //県版
				$c = 24;
				$data2['kenri_shori'] =$data[$r][$c];
				if ($kenri_shori=="済"){
					$data2['kenri_shori']="9";
				}else{
					$data2['kenri_shori']="0";
				}
			}else{ //市町村版
				$c = 26;
				$data2['kenri_shori'] =$data[$r][$c];
				if ($data2['kenri_shori'] != "9"){
					$data2['kenri_shori'] = "0"; //未処理の場合は明示的に0を代入
				}
			}

			//公開レベル　県版と市町村版で値制約が異なる。県版は、公開の場合、「公開」で、
			//市町村版の場合は１が公開、2が限定公開、3が公開保留なので市町村側に合わせて
			//公開は1とする。県版のxは、扱いがわからないのでそのままとしておく。
			if($ken_or_shi==0){ //県版
				$c = 25;
				$data2['open_level'] =$data[$r][$c];
				if ($$data2['open_level'] =="公開"){
					$data2['open_level'] ="1";
				}
			}else{
				$c = 27;
				$data2['open_level'] =$data[$r][$c];
			}
			//保留理由
			if($ken_or_shi==0){ //県版
				$c = 26;
				$data2['horyu_reason'] =$data[$r][$c];
			}else{
				$c = 28;
				$data2['horyu_reason'] =$data[$r][$c];
			}
			//媒体コード
			if($ken_or_shi==0){ //県版
				$c = 30;
				$data2['media_code'] =$data[$r][$c];
			}else{
				$c = 32;
				$data2['media_code'] =$data[$r][$c];
			}


			$data2['uniqid'] = $local_code.$data2['kanri_bango'];

			$fields =  '';
			$values = '';
			foreach($data2 as $key => $val){
				$fields .= "$key,";
				$values .= "'".mysql_real_escape_string($val)."',";
			}
			$fields = substr($fields,0,-1);
			$values = substr($values,0,-1);

			mysql_query("delete from baseinfo where uniqid='{$data2['uniqid']}'");
			mysql_query( "insert into baseinfo ($fields,file_id) values ($values,$file_id)" );
			echo mysql_error();

//END DBへ格納
		}
	}
	$s .= "</table>\n";
	return $s;
}

// HTML フッダ部分文字列出力
function output_footer(){
	return <<< EOS
</p>
<a href='admin.php'>管理画面へ</a>
</body>
</html>
EOS;
}


////
//// メイン
////
// HTML/CSS出力
echo output_header();
echo output_css();
// エクセルファイル受け取り
$uploaded = receive_file($original_file_path, $uploaded_file_path, $test_flag);
if($uploaded == EXCEL_FILE_NOT_PREPARED){
	echo "ファイルをアップロードできません。";
} else if($uploaded == EXCEL_FILE_NOT_UPLOADED){
	echo "ファイルが選択されていません。";
} else if (!is_excel_file($original_file_path, $required_excel_version)){
	echo "Excelのファイルのバージョンが異なります。";
} else {
	// ワークシート読み取り
	$data = load_sheet($uploaded, INPUT_SHEET_NAME, $required_excel_version);
	if(!$data){
		echo "Excelのファイルから読み取りをおこなうことができませんでした。";
	} else {
		// エクセルファイル情報出力
		echo output_file_info($original_file_path, $uploaded_file_path, $ken_or_shi, $data[DIVISION_NAME_ROW][DIVISION_NAME_COLUMN]);
		// 整理表フォーマットチェック
		$err = is_valid_seirihyo($data,$ken_or_shi);
		if ($err<0){
			echo "整理表のフォーマットが不適切です。";
			switch ($err){
				case SHEET_TITLE_ERR:
					echo "表のタイトルが「基本情報整理表」になっていないか、タイトルの位置が変更されています。";
					break;
				case KEN_LEFT_END_ERR:
					echo "県の資料のはずなのに、表の左端が「課･室・地方機関コード」になっていません。";
					break;
				case SHI_LEFT_END_ERR:
					echo "市町村の資料のはずなのに、表の左端が「市町村コード」になっていません。";
					break;
				case RIGHT_END_ERR:
					echo "表の右端が「作業管理No」になっていません。";
					break;
				case RIGHT_END_ERR:
					echo "見出しの最下部の位置がずれています。";
					break;
				case SHEET_DEF_ERR:
					echo "内部定義に問題があります。";
					break;
			}
		} else {
			// 整理表内容出力
			$lot ="003";
			echo write_table($data,$ken_or_shi, $lot, basename($original_file_path));
		}
	}
	// テンポラリファイル削除
	if(file_exists($uploaded_file_path)) unlink($uploaded_file_path);
	if(file_exists($uploaded)) unlink($uploaded);
}
// HTML フッダー出力
echo output_footer();
?>

