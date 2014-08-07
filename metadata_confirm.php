<?php
	
//確認、登録へ
include_once(dirname(__FILE__) . "/NDL/NDL.php");
include_once(dirname(__FILE__) . "/NDL/utils.php");
include_once(dirname(__FILE__) . "/metadata_utils.php");
include_once(dirname(__FILE__) . "/metadata_headers.php");
include_once(dirname(__FILE__) . "/metadata_items.php");

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
		<table>
			<?php echo metadata_items_first($items, _CONFIRM_); ?>
			<tr><td></td></tr>
			<?php echo output_items_last($items, _CONFIRM_); ?>
		</table>
		<?php echo output_handover_items($items, _CONFIRM_); ?>
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
