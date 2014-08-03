<?php
	
//確認、登録へ
include_once(dirname(__FILE__) . "/NDL/NDL.php");
include_once(dirname(__FILE__) . "/NDL/utils.php");
include_once(dirname(__FILE__) . "/metadata_utils.php");
include_once(dirname(__FILE__) . "/metadata_headers.php");
include_once(dirname(__FILE__) . "/metadata_items.php");
include_once(dirname(__FILE__) . "/metadata_handovers.php");

$lot_id = intval($_GET['lot']);
$id = isset($_GET['id'])?intval($_GET['id']):1;
list($data, $num_in_lot, $uniqid, $files) = get_data_from_db($lot_id, $id);

//以下の変数は、「基本情報整理表の県版・市町村版との違い、対応変数一覧.xlsxの順
//基本情報整理表データの受け取り
$seirihyo_items = array('local_code', 'shubetu', 'kanri_bango','shiryou_jyuryoubi','contributor','contributor_yomi','bunrui_code','bunsho_bunrui',
	'title', 'creator', 'creator_yomi', 'sakusei_nen', 'sakusei_tuki','sakusei_bi',
	'satuei_basho_zip',  //「撮影」と「作成」は違うので注意！
	'satuei_basho_address', 'satuei_basho_address_yomi', 'haifu_basho', 'haifu_basho_yomi', 'keyword', 'renraku_saki_zip', 'renraku_saki_address',
    'renraku_saki_tel', 'renraku_saki_other', 'kenri_shori', 'open_level', 'horyu_reason','media_code');
$items = array();
foreach($seirihyo_items as $s){
	$$s = $_REQUEST[$s];
	$items += array($s => $$s);
}

//metadata_inputより受け取り
$metadata_input_items = array()
	'md_type', //資料種別
	'series_flag', //シリーズ資料（継続資料）か否か
	'ihan_flag', //異版の有無
	'betu_title_flag', //別タイトルが存在するか否か
	'kiyo_flag', //寄与者（寄贈者）がいるかいないか
	'inyou_flag', //引用資料か否か
	'doctor_flag', //博士論文か否か
	'license_flag', //ライセンス設定があるかないか
	'gov_issue', //政府刊行物か否か
	'gov_issue2', //官公庁刊行物
	'gov_issue_chihou', //地方公共団体資料か否か
	'gov_issue_miyagi', //宮城県内市町村資料か否か
	'for_handicapped', //障害者向け資料か否か
	'origina_shiryo_keitai', //オリジナル資料の形態
	'rippou_flag', //立法資料か否か
	'betsu_title', 'betsu_title_yomi', 'series_title', 'series_title_yomi', 'naiyo_saimoku_title', 'betu_series_title_yomi', 'naiyo_saimoku_title_yomi',
	'naiyo_saimoku_chosha','naiyo_saimoku_chosha_yomi','buhenmei','buhenmei_yomi','makiji_bango','makiji_bango_yomi','iban', 'iban_chosha',
	'publisher','chuuki','youyaku','mokuji','standard_id','sakusei_nen','sakusei_tuki','sakusei_bi',
	'online_nen','online_tuki','online_bi','koukai_nen','koukai_tuki','koukai_bi','language','is_bubun','oya_uri','shigen_mei','has_bubun','taisho_basho_uri',
	'taisho_basho_ken','taisho_basho_shi','taisho_basho_banchi','taisho_basho_ido','taisho_basho_keido','satuei_ido','satuei_keido','satuei_ken','satuei_shi',
	'satuei_banchi','kanko_hindo','kanko_status','kanko_kanji','doctor_bango','doctor_nen','doctor_tuki','doctor_bi','doctor_daigaku','keisai_go1','keisai_go2',
	'keisa_shimei','keisai_ka','keisai_page','license_info','license_uri','license_holder','license_chuki','gov_issue','gov_issue_2','gov_issue_miyagi','gov_issue_chihou',
	'for_handicapped','hakubutu_kubun','shiryo_keitai','origina_shiryo_keitai','shosha_flag','online_flag','shoshi_flag','chizu_kubun','haifu_taisho',
	'keiji_nen','keiji_tuki','keiji_bi','keiji_basho','keiji_basho_yomi','sekou_taisho','sekou_nen','sekou_tuki','sekou_bi','teller','teller_yomi','seigen');
foreach($seirihyo_items as $s){
	$$s = $_REQUEST[$s];
	$items += array($s => $$s);
}
$items += array('uniqid' => $uniqid);

///
echo output_header();
echo output_css();
echo output_item_script();
?>

<div id='formDiv'>
	<p>
	<h4>ロットNo.<?php printf("%03d", $lot_id); ?></h4>
	<?php echo "$id/$num_in_lot"; ?><br>

	<font size='+1'><b>　　　　　　　　　　　入力内容を確認して下さい</font></b>
	<form name="input_form" method ="post" action="write.php" onSubmit="return check()">
	<input type='hidden' name='lot' value='<?php echo $lot_id ?>'>
	<input type='hidden' name='id' value='<?php echo $id ?>'>
	<table>
		<?php echo metadata_items1($items); ?>


    <!--オリジナル資料の形態-->
    <tr><th>オリジナル資料の形態</th><td>
	<td><?php echo output_original_shiryo_keitai_selection($original_shiryo_keitai); ?></td></tr>
    	
    <!--立法資料-->
    <tr><th>立法資料</th><td>
	<input type="radio" value=0 name = "rippou_flag" checked >該当しない　
	<input type="radio" value=1 name = "rippou_flag">該当する　

	<!--博士論文-->
    <tr class='optional optional_図書'><th>博士論文</th><td>
	<label><input type="radio" class='optctrl' value=0 name="doctor_flag" checked >該当しない　</label>
	<label><input type="radio" class='optctrl' value=1 name="doctor_flag">該当する　</label>

	<tr><td>　</td>　</tr>

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


	<?php echo output_items_last(); ?>
</table>
<?php echo output_handover_items(); ?>
<input type='button' value="前画面へ" onClick="history.back();">
<input type="submit" value="決定">
<!--
<input type="submit" name='next' value="登録して次へ">
<input type="submit" name='quit' value="中断">
--->
</form>
</div>
</body>
</html>
