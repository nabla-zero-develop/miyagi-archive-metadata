<?php
include_once(dirname(__FILE__) . "/NDL/NDL.php");
include_once(dirname(__FILE__) . "/NDL/utils.php");
include_once(dirname(__FILE__) . "/metadata_utils.php");
include_once(dirname(__FILE__) . "/metadata_header.php");
include_once(dirname(__FILE__) . "/metadata_items.php");

function get_item($row_no, $col_num){
	if(isset($_REQUEST["r".$row_no."c".$col_num])){
		return $_REQUEST["r".$row_no."c".$col_num];
	} else {
		return '[整理表データ無し]';
	}
}

if(isset($_GET['row_no'])){
	$row_no = $_GET['row_no'];
	$ken_or_shi = $_REQUEST['ken_or_shi']; 
	$lot_id = intval($_GET['lot']);
	$id = isset($_GET['id'])?intval($_GET['id']):1;
} else {
	$row_no = 1;
	$ken_or_shi = 0; 
	$lot_id = '003';
	$id = 1;
}
$show_image_flag = TRUE;

// DB取得
list($data, $num_in_lot, $uniqid, $files) = get_data_from_db($lot_id, $id);

//基本情報整理表に書かれたデータ（行・列）を変数に格納
//県版と市町村県版で列が同じ項目

//A列：課室コード（県版）、市町村コード（市町村版）

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

$items = array();
foreach(array($common_items, $ken_items,  $shi_items) as $is){
	foreach($is as $i){
		$$i[0] = get_item($row_no, $i[1]);
		$items += array($i[0] => $$i[0]);
	}
}

// 種別
$md_type = '';
if ($shubetu=="v"){$md_type="映像"; } //映像
if ($shubetu=="p"){$md_type="チラシ"; } //チラシ
if ($shubetu=="d"){$md_type="文書"; } //文書
if ($shubetu=="b"){$md_type="図書・雑誌"; } //図書・雑誌
if ($shubetu=="s"){$md_type="音声"; } //音声
$items += array('md_type' => $md_type);

//権利処理（県版と市町村版で値制約が異なる。県版は「済」「未」、市町村版は9で済）
//なので、県版の「済」を9に書き換える
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
$items += array('kenri_shori' => $kenri_shori);

//公開レベル　県版と市町村版で値制約が異なる。県版は、公開の場合、「公開」で、
//市町村版の場合は１が公開、2が限定公開、3が公開保留なので市町村側に合わせて
//公開は1とする。県版のxは、扱いがわからないのでそのままとしておく。
if($ken_or_shi==0){ //県版
   if ($open_level =="公開"){
   	   $open_level ="1";
   }
}
$items += array('open_level' => $open_level);

// 図書情報
$koukai_nen = '';
$koukai_tsuki = '';
$koukai_hi = '';
if($md_type=="図書"){
	$pubDate = get_info('pubDate');
	if($pubDate <>''){
		list($koukai_nen, $koukai_tsuki, $koukai_hi) = explode("-", date("Y-m-d", strtotime($pubDate)));
	}
	// 図書の場合はNDLに問い合わせ、情報がなければMecabを使う
	$creator_yomi = yomi($creator_yomi, ndl_creator_yomi($creator)) ;
	$creator_yomi = yomi($creator_yomi, mecab($creator)) ;
	$publisher = get_info('dc_publisher');
} else {
	$publisher = '';
}
$items += array('koukai_nen' => $koukai_nen);
$items += array('koukai_tsuki' => $koukai_tsuki);
$items += array('koukai_hi' => $koukai_hi);
$items += array('creator_yomi' => $creator_yomi);
$items += array('publisher' => $publisher);

// 整理表では提供されない情報
$new_items = array('series_flag', 'betu_title_flag', 'kiyo_flag', 'iban_flag', 'license_flag', 'inyou_flag',
		'gov_issue', 'gov_issue_2', 'gov_issue_chihou', 'gov_issue_miyagi', 'for_handicapped',
		'original_shiryo_keitai', 'rippou_flag', 'doctor_flag', 'standard_id', 'title_yomi', 'series_title', 'series_title_yomi', 'betu_title', 'betu_title_yomi', 'betu_series', 'betu_series_yomi', 'betu_series_title', 'betu_series_title_yomi', 'naiyo_saimoku_title_yomi', 'naiyo_saimoku_title_yomi', 'naiyo_saimoku_chosha', 'buhenmei', 'buhenmei_yomi', 'makiji_bango', 'makiji_bango_yomi', 'iban', 'iban_chosha', 'chuuki', 'youyaku', 'mokuji', 'is_bubun', 'ioya_uri', 'shigen_mei', 'has_bubun', 'ko_uri', 'taisho_basho_uri', 'taisho_basho_keni', 'taisho_basho_shi', 'taisho_basho_banchii', 'taisho_basho_ido', 'taisho_basho_keido', 'satusei_ido', 'satuei_keido', 'satuei_shi', 'satuei_banch', 'kanko_hindo', 'kanko_kanji', 'doctor', 'doctor_bango', 'doctor_nen', 'doctor_tuki', 'doctor_bi', 'doctor_daigaku', 'doctor_daigaku_yomi', 'keisai_go1', 'keisai_go2', 'keisa_shimei', 'keisai_kan', 'keisai_page', 'license_info', 'license_uri', 'license_holder', 'license_chuki', 'shiryo_keitai', 'teller', 'teller_yomi', 'haifu_taisho', 'haifu_nen', 'haifu_tuki', 'haifu_bi', 'keiji_basho', 'keiji_basho_yomi', 'keiji_nen', 'keiji_tuki', 'keiji_bi', 'sakusei_bi', 'online_nen', 'online_tuki', 'online_bi', 'koukai_tuki', 'shiryo_keitai', 'language', 'kanko_status', 'hakubutu_kubun', 'shosha_flag', 'online_flag', 'shoshi_flag', 'chizu_kubun', 'seigen');

foreach($new_items as $i){
	$items += array($i => '');
}

// データベースによる情報
$items += array('id' => $id);
$items += array('lot_id' => $lot_id);
$items += array('uniqid' => $uniqid);

///
echo output_header();
echo output_css($show_image_flag);
echo output_item_script();
echo output_image_script($files);
?>

</header>
<body>
	<div id='imageDiv'>
		<div id='imageWrap'>
			<img src='' id='image'>
		</div>
		<br>
		<!--
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
		-->
	</div>

	<div id='formDiv'>
		<p>
		<h4>ロットNo.<?php printf("%03d", $lot_id); ?></h4>
		<?php echo "$id/$num_in_lot"; ?><br>
		<form name="input_form" method ="post" action="./metadata_confirm.php" onSubmit="return check()">
			<table>
				<?php echo metadata_items_first($items, _INPUT_); ?>
				<tr><td></td></tr>
				<?php echo output_items_last($items, _INPUT_); ?>
			</table>
			<?php echo output_handover_items($items, _INPUT_); ?>
			<input type="submit" value="確認画面へ">
			<!--
			<input type="submit" name='next' value="登録して次へ">
			<input type="submit" name='quit' value="中断">
			-->
		</form>
	</div>
</body>
</html>
