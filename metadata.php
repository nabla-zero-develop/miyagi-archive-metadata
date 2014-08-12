<?php
require_once(dirname(__FILE__) . '/include/config.php');
require_once(dirname(__FILE__) . "/NDL/NDL.php");
require_once(dirname(__FILE__) . "/NDL/utils.php");
require_once(dirname(__FILE__) . "/metadata_utils.php");
require_once(dirname(__FILE__) . "/metadata_header.php");
require_once(dirname(__FILE__) . "/metadata_items.php");
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT);

//
define('SELF', '_INPUT_'); // 確認画面のコードを統合するときには呼び出され方で自動判別させる
$is_input = (SELF == _INPUT_);
$show_image_flag = $is_input;

//
$items = array();
if(isset($_GET['lotid'])){
	// lotidが渡された場合(データベースからの情報取得)
	if(DEBUG_NO_DB) die('実行環境か引数の渡し方が間違っています');
	$lotid = intval($_GET['lotid']);
	$resume = isset($_GET['resume'])?$_GET['resume'] : false;
	$uniqid = isset($_GET['uniqid'])?$_GET['uniqid'] : 0;
	if(!is_numeric($uniqid)) die('uniqidが不正です');

	//編集対象を確定
	if($uniqid){
		$res = mysql_query("select * from lotfile where uniqid=$uniqid");
		$row = mysql_fetch_assoc($res);
		if(!$row) die("No data for uniqid $uniqid.");
	}else{
		if($resume){
			$res = mysql_query("select * from lotfile where finish = 0 and lotid=$lotid order by ord");
		}else{
			$res = mysql_query("select * from lotfile where lotid=$lotid order by ord");
		}
		$row = mysql_fetch_assoc($res);
		if(!$row) die("No data");
		$uniqid = $row['uniqid'];
	}
	//ロット内のデータ数
	$res = mysql_query("select uniqid from lotfile where lotid=$lotid order by ord");
	$num_in_lot = mysql_num_rows($res);
	$actualord = 1;
	while($row2 = mysql_fetch_assoc($res)){
		if($row2['uniqid'] == $uniqid)break;
		$actualord++;
	}

	$filedir = $file_basepath.mb_convert_encoding($row['filepath'],'SJIS','UTF-8');
	$files = glob($filedir.'/*');

	$res = mysql_query("select * from content where uniqid=$uniqid");
	$data = mysql_fetch_assoc($res);
	if(!$data){
		$data = array(
	'md_type' => '','md_title' => '','md_copywriter' => '','md_copywriter_other' => '','md_copyrigher_uri' => '','md_copyrighter_yomi' => '','md_content_year' => '-1','md_content_month' => '-1','md_content_day' => '-1','md_content_hour' => '-1','md_content_min' => '-1','md_content_sec' => '-1','md_publish_year' => '-1','md_publish_month' => '-1','md_publish_day' => '-1','md_setting_year' => '-1','md_setting_month' => '-1','md_seting_day' => '-1','md_setting_place' => '','md_issue_for' => '','md_issue_year' => '-1','md_issue_month' => '-1','md_issue_day' => '-1','md_narrator' => '','md_content_restriction' => ''
		);
	}

	$res = mysql_query("select * from baseinfo where uniqid=$uniqid");
	$items = mysql_fetch_assoc($res); // $baseinfo相当
} else {
	// 基本情報整理表からのデータ引き受け
	if(isset($_REQUEST['row_no'])){
		$row_no = $_REQUEST['row_no'];
		$ken_or_shi = $_REQUEST['ken_or_shi'];
		$lot_id = intval($_REQUEST['lot']);
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):1;
	} else {
		// ダミー情報によりテストできるようにする
		$row_no = 1;
		$ken_or_shi = 0;
		$lot_id = '003';
		$id = 1;
	}
	// DB取得
	list($data, $num_in_lot, $uniqid, $files) = get_data_from_db($lot_id, $id);
}


//基本情報整理表に書かれたデータ（行・列）を変数に格納
//県版と市町村県版で列が同じ項目

//A列：課室コード（県版）、市町村コード（市町村版）

function get_item($row_no, $col_num){
	if(isset($_REQUEST["r".$row_no."c".$col_num])){
		return $_REQUEST["r".$row_no."c".$col_num];
	} else {
		//return '[整理表データ無し]';
		return '';
	}
}

$common_items = array(
	array('local_code', 0),
	array('shubetu', 1), //B列：資料種別
	array('kanri_bango', 2),//C列：課室（県版）、市町村（市町村版）管理番号
	array('shiryo_jyuryobi', 3),//D列：資料受領日
	array('contributor', 4),//E列：資料提供者
	array('contributor_yomi', 5),//F列：資料提供者のヨミ
	array('bunrui_code', 6),//G列：分類コード
	array('bunsho_bunrui', 7),//H列：文書分類記号（県版）、市町村分類（市町村版）
	array('title', 8),//I列：タイトル hiddenで次へ(このプログラムの-行目あたり）
	array('creator', 9),//J列：撮影者・作成者
	array('creator_yomi', 10),//K列：作成者のヨミ
	array('sakusei_nen', 11),//L列：作成日(年)
	array('sakusei_tuki', 12),//M列：作成日(月)
	array('sakusei_bi', 13),//N列：作成日(日)
	array('satuei_basho_zip', 14));//O列：撮影場所（〒番号)

$ken_items = array(
	array('satuei_basho_address', 15),	//撮影場所住所
	array('satuei_basho_address_yomi', 16),	//撮影場所住所のヨミ
	array('haifu_basho', 17),	//配布場所
	array('haifu_basho_yomi', 18),	//配布場所のヨミ
	array('keyword', 19),	//キーワード
	array('renraku_saki_zip', 20),	//作成者連絡先住所の〒番号
	array('renraku_saki_address', 21),	//作成者連絡先住所
	array('renraku_saki_tel', 22),	//作成者連絡先電話番号
	array('renraku_saki_other', 23),	//その他の作成者連絡先
	array('kenri_shori', 24),	//権利処理
	array('open_level', 25),	//公開レベル　
	array('horyu_reason', 26),	//保留理由
	array('media_code', 30));	//媒体コード

$shi_items = array(
	array('satuei_basho_address', 16),	//撮影場所住所
	array('satuei_basho_address_yomi', 17),	//撮影場所住所のヨミ
	array('haifu_basho', 18),	//配布場所
	array('haifu_basho_yomi', 19),	//配布場所のヨミ
	array('keyword', 20),	//キーワード
	array('renraku_saki_zip', 21),	//作成者連絡先住所の〒番号
	array('renraku_saki_address', 23),	//作成者連絡先住所
	array('renraku_saki_tel', 24),	//作成者連絡先電話番号
	array('renraku_saki_other', 25),	//その他の作成者連絡先
	array('kenri_shori', 26),	//権利処理
	array('open_level', 27),	//公開レベル　
	array('horyu_reason', 28),	//保留理由
	array('media_code', 32));	//媒体コード

foreach(array($common_items, $ken_items,  $shi_items) as $is){
	foreach($is as $i){
		if(isset($items[$i[0]])){
			$$i[0] = $items[$i[0]];
		} else {
			$$i[0] = get_item($row_no, $i[1]);
			$items[$i[0]] = $$i[0];
		}
	}
}

// 種別
if(!isset($md_type) && $is_input){
	$md_type = '';
	if ($shubetu=="v"){$md_type="映像"; } //映像
	if ($shubetu=="p"){$md_type="チラシ"; } //チラシ
	if ($shubetu=="d"){$md_type="文書"; } //文書
	if ($shubetu=="b"){$md_type="図書・雑誌"; } //図書・雑誌
	if ($shubetu=="s"){$md_type="音声"; } //音声
}
$items['md_type'] = $md_type;

//権利処理（県版と市町村版で値制約が異なる。県版は「済」「未」、市町村版は9で済）
//なので、県版の「済」を9に書き換える
if($is_input){
	if($ken_or_shi==0){ //県版
		if ($kenri_shori=="済"){
		   $kenri_shori=="9";
		}else{
		   $kenri_shori=="0";
		}
		}else{ //市町村版
		if ($kenri_shori != "9"){
		   $kenri_shori = "0"; //未処理の場合は明示的に0を代入
		}
	}
}
$items['kenri_shori'] = $kenri_shori;

//公開レベル　県版と市町村版で値制約が異なる。県版は、公開の場合、「公開」で、
//市町村版の場合は１が公開、2が限定公開、3が公開保留なので市町村側に合わせて
//公開は1とする。県版のxは、扱いがわからないのでそのままとしておく。
if($is_input){
	if($ken_or_shi==0){ //県版
		if ($open_level =="公開"){
		$open_level ="1";
		}
	}
}
$items['open_level'] = $open_level;

// 図書情報
if(FALSE){
	$is_input = TRUE;
	$md_type = '図書';
	$creator = '沢野伸浩';
	$creator_yomi = '';
	$title = '幻影の明治';
	$title = '日本語の技術';
	$standard_id  = '9784582836547';
}
if($is_input && $md_type=="図書"){
	// 著者名の読み
	$creator_yomi = yomi($creator_yomi, ndl_creator_yomi($creator)) ; // クラス名付けると動かない！？
	$creator_yomi = yomi($creator_yomi, mecab($creator)) ;
	// 公開日
	if(!isset($koukai_nen)){$koukai_nen = '';}
	if(!isset($koukai_tsuki)){$koukai_tsuki = '';}
	if(!isset($koukai_hi)){$koukai_hi = '';}
	if(!isset($publisher)){$publisher = '';}
	// NDLに問い合わせ
	if($title != ''){
		$bi = ndl_title_info($title); // クラス名付けると動かない！？
		if($koukai_nen == '' && $koukai_tsuki == '' && $koukai_hi == ''){
			$pubDate = get_info($bi, 'pubDate');
			if($pubDate != ''){
				list($koukai_nen, $koukai_tsuki, $koukai_hi) = explode("-", date("Y-m-d", strtotime($pubDate)));
			}
		}
		if($publisher == ''){
			$publisher = get_info($bi, 'dc_publisher');
		}
	}
	if($standard_id != ''){
		$bi = NDL::ndl_isbn_info($standard_id, 'array'); // クラス名必須
		// NDL情報を優先し強制的書き換え
		$title = get_info($bi, 'dc_title');
		$title_yomi = get_info($bi, 'dcndl_titleTranscription');
		$creator = clean_author(get_info($bi, 'dc_creator'));
		$creator_yomi = ndl_creator_yomi($creator); // クラス名付けると動かない！？
		$creator_yomi = yomi($creator_yomi, mecab($creator)) ;
		$pubDate = get_info($bi, 'pubDate');
		if($pubDate != ''){
			list($koukai_nen, $koukai_tsuki, $koukai_hi) = explode("-", date("Y-m-d", strtotime($pubDate)));
		}
		$publisher = get_info($bi, 'dc_publisher');
	}
}
//
if(FALSE){
	echo $creator."\n";
	echo $creator_yomi."\n";
	echo $koukai_nen."-".$koukai_tsuki."-".$koukai_hi."\n";
	echo $publisher."\n";
	echo $title."\n";
	echo $title_yomi."\n";
	echo $creator."\n";
	echo $creator_yomi."\n";
	die;
}
$items['koukai_nen'] = $koukai_nen;
$items['koukai_tsuki'] = $koukai_tsuki;
$items['koukai_hi'] = $koukai_hi;
$items['creator_yomi'] = $creator_yomi;
$items['publisher'] = $publisher;

// 整理表では提供されない情報
$new_items = array('series_flag', 'betu_title_flag', 'kiyo_flag', 'iban_flag', 'license_flag', 'inyou_flag',
		'gov_issue', 'gov_issue_2', 'gov_issue_chihou', 'gov_issue_miyagi', 'for_handicapped',
		'original_shiryo_keitai', 'rippou_flag', 'doctor_flag', 'standard_id', 'title_yomi', 'series_title', 'series_title_yomi', 'betu_title', 'betu_title_yomi', 'betu_series', 'betu_series_yomi', 'betu_series_title', 'betu_series_title_yomi', 'naiyo_saimoku_title', 'naiyo_saimoku_title_yomi', 'naiyo_saimoku_title_yomi', 'naiyo_saimoku_chosha', 'buhenmei', 'buhenmei_yomi', 'makiji_bango', 'makiji_bango_yomi', 'iban', 'iban_chosha', 'chuuki', 'youyaku', 'mokuji', 'is_bubun', 'ioya_uri', 'shigen_mei', 'has_bubun', 'ko_uri', 'taisho_basho_uri', 'taisho_basho_keni', 'taisho_basho_shi', 'taisho_basho_banchii', 'taisho_basho_ido', 'taisho_basho_keido', 'satusei_ido', 'satuei_keido', 'satuei_shi', 'satuei_banch', 'kanko_hindo', 'kanko_kanji', 'doctor', 'doctor_bango', 'doctor_nen', 'doctor_tuki', 'doctor_bi', 'doctor_daigaku', 'doctor_daigaku_yomi', 'keisai_go1', 'keisai_go2', 'keisa_shimei', 'keisai_kan', 'keisai_page', 'license_info', 'license_uri', 'license_holder', 'license_chuki', 'shiryo_keitai', 'teller', 'teller_yomi', 'haifu_taisho', 'haifu_nen', 'haifu_tuki', 'haifu_bi', 'keiji_basho', 'keiji_basho_yomi', 'keiji_nen', 'keiji_tuki', 'keiji_bi', 'sakusei_bi', 'online_nen', 'online_tuki', 'online_bi', 'koukai_tuki', 'shiryo_keitai', 'language', 'kanko_status', 'hakubutu_kubun', 'shosha_flag', 'online_flag', 'shoshi_flag', 'chizu_kubun', 'seigen');

foreach($new_items as $i){
	if(isset($items[$i])){
		$$i = $items[$i];
	} else {
		$items += array($i => '');
		$$i = '';
	}
}

// 引数での上書き
foreach($items as $k => $v){
	if($v == '' && $_REQUEST[$k] != ''){
		$items[$k] = $_REQUEST[$k];
		$$k = $_REQUEST[$k];
	}
}

// データベースによる情報
$items['id'] = $id;
$items['lot_id'] = $lot_id;
$items['uniqid'] = $uniqid;

///
echo output_header();
echo output_css($show_image_flag);
echo output_item_script();
echo output_image_script($files);
echo output_map_script();
?>

</header>
<body>
	<div id='imageDiv'>
		<div id='imageWrap'>
			<img src='' id='image'>
		</div>
		<br>
		<span>
			<input type='button' value='<<' onClick='chgImage(0)'>
			<input type='button' value='<' onClick='prevImage()'>
			<span id='filename'></span>
			<input type='button' value='>' onClick='nextImage()'>
			<input type='button' value='>>' onClick='lastImage()'>
			</span>
			<span>
			<input type='button' value='左回転' onClick='rotate(-90);'>
			<input type='button' value='無回転' onClick='rotate(0);'>
			<input type='button' value='右回転' onClick='rotate(90);'>
		</span>
	</div>

	<div id='formDiv'>
		<p>
		<h4>ロットNo.<?php printf("%03d", $lotid); ?></h4>
		<?php echo "$actualord/$num_in_lot"; ?><br>
		<!-- form name="input_form" method ="post" action="./metadata_confirm.php" onSubmit="return check()" -->
		<!-- form name="input_form" -->
		<form name="input_form" method ="post" action="write.php" onSubmit="return check()">
			<table>
				<?php echo metadata_items_first($items, _INPUT_); ?>
				<tr><td></td></tr>
				<?php echo output_items_last($items, _INPUT_); ?>
			</table>
			<?php echo output_handover_items($items, _INPUT_); ?>

			<input type="hidden" name="lotid" value="<?php echo $lotid; ?>">
			<input type="hidden" name="uniqid" value="<?php echo $uniqid; ?>">
			<input type="submit" name='next' value="登録して次へ" onClick="setSkipCheck(0);">
			<input type="submit" name='quit' value="中断" onClick="setSkipCheck(1);">
			&nbsp;&nbsp;&nbsp;<input type="submit" name='next' value="入力スキップ" onClick="setSkipCheck(2);">
			理由：<input type="text" name="skip_reason">
			<!--  <input type="submit" value="確認画面へ"> -->
		</form>
	</div>

	<div id='mapDialog'>
		<div id="mapDiv" style="width:750px; height:600px; margin-left:5px; margin-right:5px;">&nbsp;</div>
		<button onClick="doRevGeoCode();">マーカー位置の住所取得</button>
			<span id="addressKen"></span> <span id="addressShi"></span> <span id="addressBanchi"></span>
			<span style="display:none;">(<span id="addressKeido"></span>,<span id="addressIdo"></span>)</span>
	</div>
</body>

	</div>
</body>
</html>
