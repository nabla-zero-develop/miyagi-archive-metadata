<?php
include_once(dirname(__FILE__) . "/NDL/NDL.php");
include_once(dirname(__FILE__) . "/NDL/utils.php");
include_once(dirname(__FILE__) . "/metadata_utils.php");
include_once(dirname(__FILE__) . "/metadata_headers.php");
include_once(dirname(__FILE__) . "/metadata_items.php");
include_once(dirname(__FILE__) . "/metadata_handovers.php");

function get_item($col_num){
	global $row_no;
	return $_REQUEST["r".$row_no."c".$col_num];
}

// DB取得
$lot_id = intval($_GET['lot']);
$id = isset($_GET['id'])?intval($_GET['id']):1;
list($data, $num_in_lot, $uniqid, $files) = get_data_from_db($lot_id, $id);

//基本情報整理表に書かれたデータ（行・列）を変数に格納
//県版と市町村県版で列が同じ項目

//A列：課室コード（県版）、市町村コード（市町村版）
$row_no     = $_GET['row_no'];
$ken_or_shi = $_REQUEST['ken_or_shi']; 

$common_items = array(
	array('local_code', 0),
	array('shubetu', 1), //B列：資料種別
	array('kanri_bango', 2),//C列：課室（県版）、市町村（市町村版）管理番号
	array('shiryo_jyuryobi', 3),//D列：資料受領日
	array('contributor', 4),//E列：資料提供者
	array('contributor_yomi', 5),//F列：資料提供者のヨミ
	array('bunrui_code', 6),//G列：分類コード
	array('bunsho_bunrui', 7),//H列：文書分類記号（県版）、市町村分類（市町村版）
	array('title ', 8),//I列：タイトル hiddenで次へ(このプログラムの-行目あたり）
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
		$$i[0] = get_item($i[1]);
		$items += array($s => $$s);
	}
}

// 種別
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

if($md_type=="図書"){
	$pubDate = get_info('pubDate');
	$y = '';
	$m = '';
	$d = '';
	if($pubDate <>''){
		list($y, $m, $d) = explode("-", date("Y-m-d", strtotime($pubDate)));
	}
	// 図書の場合はNDLに問い合わせ、情報がなければMecabを使う
	$creator_yomi = yomi($creator_yomi, ndl_creator_yomi($creator)) ;
	$creator_yomi = yomi($creator_yomi, mecab($creator)) ;
	$publisher = get_info('dc_publisher');
}
$items += array('y' => $y);
$items += array('m' => $m);
$items += array('d' => $d);
$items += array('creator_yomi' => $creator_yomi);
$items += array('publisher' => $publisher);

///
echo output_header();
echo output_css();
echo output_item_script();
echo output_image_script($files);
?>

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
	<h4>ロットNo.<?php printf("%03d",$lot_id); ?></h4>
	<?php echo "$id/$num_in_lot"; ?><br>

	<form name="input_form" method ="post" action="metadata65.php" onSubmit="return check()">
	<table>
		<?php echo metadata_items1(); ?>

    <!--オリジナル資料の形態-->
    <?php
     $selection0="selected";
     if ($media_code=="32"){ $selection32="selected"; } //カセット
    ?>
    <tr><th>オリジナル資料の形態</th><td>
    <select name='origina_shiryo_keitai'>
    	<option value= ''  <?php echo $selection0; ?>>該当なし（その他）</option>
		<option value='32' <?php echo $selection32; ?>>カセット</option>
	<td><?php echo output_original_shiryo_keitai_selection($original_shiryo_keitai); ?></td></tr>

    <!--立法資料-->
    <tr><th>立法資料</th><td>
	<input type="radio" value=0 name = "rippou_flag" checked >該当しない　
	<input type="radio" value=1 name = "rippou_flag">該当する　

	<!--博士論文-->
    <tr class='optional optional_図書'><th>博士論文</th><td>
	<label><input type="radio" class='optctrl' value=0 name="doctor_flag" checked >該当しない　</label>
	<label><input type="radio" class='optctrl' value=1 name="doctor_flag">該当する　</label>

<tr><td></td></tr>

<!--原資料の標準番号-->
<tr><th>標準番号(ISBN等)<BR>
<input type='button' value='NDLチェック'>
<td><input type='text' name='standard_id' value='<?php echo $standard_id; ?>' size='40'></td></tr>

<!--タイトル-->
<tr><th class='opthissu opthissu_図書 opthissu_記事 opthissu_新聞・雑誌 opthissu_音声・映像 opthissu_文書・楽譜 opthissu_地図・地図帳 opthissu_チラシ opthissu_会議録・含資料 opthissu_博物資料 opthissu_オンライン資料opthissu_語り opthissu_絵画・絵はがき opthissu_プログラム（スマホアプリ・ゲーム等）'>タイトル<BR>
<input type='button' value='NDLチェック'><br></th>
<td><input type='text' name='title' size='40' value='<?php echo $title; ?>'></td></tr>

<!--タイトルのヨミ-->
<tr><th>タイトルのヨミ
<td><input type='text' name='title_yomi' size='40' value='<?php //mecab($title); ?>'></td></tr>
<!-- NDL問い合わせ
if($md_type=="図書"){
	$book_info = ndl_title_info($title);
}-->
	
<!--シリーズタイトル-->
<tr class='series_flag_option'><th class='opthissu opthissu_図書 opthissu_記事 opthissu_新聞・雑誌 opthissu_音声・映像 opthissu_文書・楽譜 opthissu_地図・地図帳 opthissu_チラシ opthissu_会議録・含資料 opthissu_博物資料 opthissu_オンライン資料opthissu_語り opthissu_絵画・絵はがき opthissu_プログラム（スマホアプリ・ゲーム等）'>シリーズタイトル
<td><input type='text' name='series_title' value='<?php echo $series_title; ?>'size='40'></td>
<tr class='series_flag_option'><th class='opthissu opthissu_図書 opthissu_記事 opthissu_新聞・雑誌 opthissu_音声・映像 opthissu_文書・楽譜 opthissu_地図・地図帳 opthissu_チラシ opthissu_会議録・含資料 opthissu_博物資料 opthissu_オンライン資料opthissu_語り opthissu_絵画・絵はがき opthissu_プログラム（スマホアプリ・ゲーム等）'>シリーズタイトルのヨミ
<td><input type='text' name='series_title_yomi' value='<?php echo $series_title_yomi; ?>' size='40'></td></tr>	
 
<!--別タイトル-->
<tr class="betu_title_flag_option"><th>別タイトル　
<td><input type='text' name='betu_title' value='<?php echo $betu_title; ?>' size='40'></td></tr>
<tr class="betu_title_flag_option"><th>別タイトルのヨミ
<td><input type='text' name='betu_title_yomi' value='<?php echo $betu_title_yomi; ?>' size='40'></td></tr>

<!--別シリーズタイトル-->
<tr class='betu_title_flag_option'><th>別シリーズタイトル
<td ><input type='text' name='betu_series_title' value='<?php echo $betu_series_title; ?>' size='40'></td></tr>
<tr class='betu_title_flag_option'><th>別シリーズタイトルのヨミ
<td><input type='text' name ='betu_series_title_yomi' value='<?php echo $betu_series_title_yomi; ?>' size='40'></td></tr>	

<!--内容細目-->	
<tr class='optional optional_図書 optional_記事  optional_映像・音声  optional_文書・楽譜 optional_地図・地図帳'><th>内容細目タイトル	
<td><input type='text' name='naiyo_saimoku_title' value='<?php echo $naiyo_saimoku_title_yomi; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>内容細目タイトルのヨミ	
<td><input type='text' name='naiyo_saimoku_title_yomi' value='<?php echo $naiyo_saimoku_title_yomi; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>内容細目著者
<td><input type='text' name='naiyo_saimoku_chosha' value='<?php echo $naiyo_saimoku_chosha; ?>' size='40'></td></tr>	
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>部編名
<td><input type='text' name='buhenmei' value='<?php echo $buhenmei; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>部編名のヨミ
<td><input type='text' name='buhenmei_yomi' value='<?php echo $buhenmei_yomi; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>巻次・部編番号
<td><input type='text' name='makiji_bango' value='<?php echo $makiji_bango; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>巻次・部編番号のヨミ
<td><input type='text' name='makiji_bango_yomi' value='<?php echo $makiji_bango_yomi; ?>' size='40'></td></tr>	
	
<!--作成者・著者-->
<tr><th class='hissu'>作成者・著者名
</td><tr></td>


	<?php echo output_handover_items(); ?>
</table>
<?php echo output_handover_items(); ?>
<input type="submit" value="確認画面へ">
<!--
<input type="submit" name='next' value="登録して次へ">
<input type="submit" name='quit' value="中断">
--></form>
</div>
</body>
</html>
