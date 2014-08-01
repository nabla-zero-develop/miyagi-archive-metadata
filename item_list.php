<?php
include_once(dirname(__FILE__) . "/Classes/PHPExcel.php");
include_once(dirname(__FILE__) . "/Classes/PHPExcel/IOFactory.php");

ignore_user_abort(1);
set_time_limit(86400);

//定数 
define("EXCEL2007", "Excel2007");
define("EXCEL5", "Excel5");
define("EXCEL_FILE_NOT_PREPARED", "-10");
define("EXCEL_FILE_NOT_UPLOADED", "-20");
//
define("INPUT_SHEET_NAME", "整理表");
//
define("SHEET_TITLE_ERR", "-30");
define("KEN_LEFT_END_ERR", "-41");
define("SHI_LEFT_END_ERR", "-42");
define("RIGHT_END_ERR", "-50");
define("HEADER_BOTTOM_ERR", "-60");
define("SHEET_DEF_ERR", "-70");
//
define("SHEET_TITLE_ROW", "1");
define("SHEET_TITLE_COLUMN", "4");
define("FIRST_DATA_ROW", "9");
define("LAST_COLUMN", "33");
define("DIVISION_NAME_ROW", "2");
define("DIVISION_NAME_COLUMN", "4");
//
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
define("SAKUSEISHA_HEADER", "作成・撮影者");
define("SAKUSEIBASHO_COLUMN", "15");
define("SAKUSEIBASHO_HEADER", "撮影場所");
define("KEYWORD_COLUMN", "19");
define("KEYWORD_HEADER", "キーワード");
//
define("METADATA_INPUT_PAGE", "./metadata4.php");

$test_flag = !isset($_FILES["upfile"]["name"]); 
$required_excel_version= EXCEL2007;
$visible_columns = array(
	array(SHIRYO_SHUBETU_COLUMN, SHIRYO_SHUBETU_HEADER),
	array(SHICHOSON_KANRI_BANGOU_COLUMN, SHICHOSON_KANRI_BANGOU_HEADER),
	array(SAKUSEISHA_COLUMN, SAKUSEISHA_HEADER),
	array(TITLE_COLUMN, TITLE_HEADER),
	array(SAKUSEIBASHO_COLUMN, SAKUSEIBASHO_HEADER),
	array(KEYWORD_COLUMN, KEYWORD_HEADER),
	);

// シェル上のテストかどうか	
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
	if((trim($data[FIRST_DATA_ROW-4][LAST_COLUMN]) <> "＊作業管理No.")) return RIGHT_END_ERR;
	//見出し最下部 
	if((trim($data[FIRST_DATA_ROW-1][6]) <> "複数可")) return HEADER_BOTTOM_ERR;
	//大域宣言との整合性確認
	if(FIRST_DATA_ROW != 9) return SHEET_DEF_ERR;
	return TRUE;
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
function write_table($data, $ken_or_shi, $lot){
	$division_name = $data[DIVISION_NAME_ROW][DIVISION_NAME_COLUMN];
	$num_rows = count($data);
	
	//echo "div: " .$division_name ."\n";
	//echo "lines:" . $num_rows."\n";
	$s = "<table class='table_1'>\n";
	// 見出し
	$s .= "<tr class='color_0'>";
	for ($c=1; $c<=LAST_COLUMN; $c++){
		if(is_visible_column($c)){
			$s .= "<td>".get_visible_column_header($c)."<br></td>\n";
		}
	}
	$s .= "<td></td>\n";
	$s .= "</tr>";
	// データ内容
	for ($r=FIRST_DATA_ROW ; $r<=$num_rows; $r++){
		if (isset($data[$r][1]) && $data[$r][1] != ""){
			$s .= "<tr class='color_". (($r % 2) +1) ."'>"; //<td>".$division_name."</td>";
			$s .= "<form method='post' action ='". METADATA_INPUT_PAGE ."?lot=". $lot ."&"."row_no=".$r."'>\n";
			for ($c=1; $c<=LAST_COLUMN; $c++){
				$item = $data[$r][$c];
				$s .= "<input type='hidden' name='r".$r."c".$c."' value='".$item."'>";
				if(is_visible_column($c)){
					$s .= "<td>".$item."<br></td>";
				}
				$s .= "\n"; 
		    }
		    $s .= "<input type='hidden' name='ken_or_shi' value='".$ken_or_shi."'>\n";
		    $s .= "<td><input type='submit' value='メタデータ入力'>\n";
		    $s .= "</form>\n";
			$s .= "</tr>\n";
		}
	}
	$s .= "</table>\n";
	return $s;
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
	background-color: #C9E2F8; /* 見出し列の背景色 */ 
} 

td.color_1 { 
	background-color: #E3F0FB; /* データ列の背景色 */ 
} 
--> 
</STYLE> 
EOS;
}

// ファイル情報文字列出力
function output_file_info($original_file_path, $uploaded_file_path, $ken_or_shi){
	return "<table class='table_1'>".
			"<tr><td class='color_0'>アップロードされたファイル</td><td class='color_1'>".basename($original_file_path)."</td></tr>".
			"<tr><td class='color_0'>ファイルの仕様</td><td class='color_1'>".(($ken_or_shi == 0) ? "県" : "市町村")."</td></tr>".
			"<input type='hidden' name='uploaded_file_path' value='".$uploaded_file_path."'><br><br>\n";
}

// HTML フッダ部分文字列出力
function output_footer(){
	return <<< EOS
</p>
</body>
</html>
EOS;
}


// メイン
echo output_header();
echo output_css();
$uploaded = receive_file($original_file_path, $uploaded_file_path, $test_flag);
if($uploaded == EXCEL_FILE_NOT_PREPARED){
	echo "ファイルをアップロードできません。";
} else if($uploaded == EXCEL_FILE_NOT_UPLOADED){
	echo "ファイルが選択されていません。";
} else if (!is_excel_file($original_file_path, $required_excel_version)){
	echo "Excelのファイルのバージョンが異なります。";
} else {
	echo output_file_info($original_file_path, $uploaded_file_path, $ken_or_shi);
	$data = load_sheet($uploaded, INPUT_SHEET_NAME, $required_excel_version);
	if(!$data){
		echo "Excelのファイルから読み取りをおこなうことができませんでした。";
	} else {
		$err = is_valid_seirihyo($data);
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
			$lot ="003";
			echo write_table($data,$ken_or_shi, $lot);
		}
	}
	//
	if(file_exists($uploaded_file_path)) unlink($uploaded_file_path);
	if(file_exists($uploaded)) unlink($uploaded);	
}
echo output_footer();
?>

