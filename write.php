<?php
require_once('include/config.php');
require_once('include/db.php');

$lotid = intval($_REQUEST['lotid']);
$uniqid = isset($_REQUEST['uniqid'])?$_REQUEST['uniqid']:0;
$resume = isset($_REQUEST['resume'])?$_REQUEST['resume']:0;
$skipped = isset($_REQUEST['skipped'])?$_REQUEST['skipped']:0;
if(!is_numeric($uniqid))die('uniqidが不正です');

if(isset($_REQUEST['quit'])){
	header('Location: index.php');
	die();
}

//データ書き込み
mysql_query("delete from metadata where uniqid=$uniqid");

$items = array('uniqid', 'md_type', 'series_flag', 'betu_title_flag', 'kiyo_flag', 'iban_flag', 'license_flag', 'inyou_flag', 'gov_issue', 'gov_issue_2', 'gov_issue_chihou', 'gov_issue_miyagi', 'for_handicapped', 'original_shiryo_keitai', 'rippou_flag', 'doctor_flag', 'standard_id', 'title', 'title_yomi', 'series_title', 'series_title_yomi', 'betu_series_title', 'betu_series_title_yomi', 'betu_title', 'betu_title_yomi', 'naiyo_saimoku_chosha', 'naiyo_saimoku_title', 'naiyo_saimoku_title_yomi', 'buhenmei', 'buhenmei_yomi', 'makiji_bango', 'makiji_bango_yomi', 'creator', 'creator_yomi', 'contributor', 'contributor_yomi', 'iban', 'iban_chosha', 'publisher', 'keyword', 'chuuki', 'youyaku', 'mokuji', 'sakusei_nen', 'sakusei_tuki', 'sakusei_bi', 'online_nen', 'online_tuki', 'online_bi', 'koukai_nen', 'koukai_tuki', 'koukai_bi', 'language', 'is_bubun', 'oya_uri', 'shigen_mei', 'has_bubun', 'ko_uri', 'taisho_basho_uri', 'taisho_basho_ken', 'taisho_basho_shi', 'taisho_basho_banchi', 'taisho_basho_ido', 'taisho_basho_keido', 'satuei_ken', 'satuei_shi', 'satuei_banchi', 'satuei_keido', 'satusei_ido', 'kanko_hindo', 'kanko_status', 'kanko_kanji', 'doctor', 'doctor_bango', 'doctor_nen', 'doctor_tuki', 'doctor_bi', 'doctor_daigaku', 'doctor_daigaku_yomi', 'keisai_go1', 'keisai_go2', 'keisai_shimei', 'keisai_kan', 'keisai_page', 'open_level', 'license_info', 'license_uri', 'license_holder', 'license_chuki', 'hakubutu_kubun', 'shosha_flag', 'online_flag', 'teller', 'teller_yomi', 'haifu_basho', 'haifu_basho_yomi', 'haifu_nen', 'haifu_tuki', 'haifu_bi', 'haifu_taisho', 'keiji_basho', 'keiji_basho_yomi', 'keiji_nen', 'keiji_tuki', 'keiji_bi', 'shoshi_flag', 'chizu_kubun', 'seigen', 'skip_reason');
$values = array();
foreach($items as $item)$values[] = "'".mysql_real_escape_string($_REQUEST[$item])."'";
$query = sprintf("insert into metadata (%s) values (%s)", implode(',',$items), implode(',',$values));
$res = mysql_query($query);
if(!$res)die($query.mysql_error());

$query = sprintf("insert into metadata_log (log_ip,log_user,%s) values ('%s',%d,%s)",
				implode(',',$items), $_SERVER["REMOTE_ADDR"], $_SESSION['userid'], implode(',',$values));
$res = mysql_query($query);
if(!$res)die($query.mysql_error());

//入力スキップ
if($_REQUEST['skip_reason']){
	mysql_query("update lotfile set finish=-1,finish_date=null where uniqid=$uniqid");
}else{
	mysql_query("update lotfile set finish=1,finish_date=now() where uniqid=$uniqid");
}

$res = mysql_query("select * from lot where lotid=$lotid");
$lot = mysql_fetch_assoc($res);
if(!$lot['start_date']){
	mysql_query("update lot set start_date = now() where lotid = $lotid");
}
mysql_query("update lot set status = 1 where lotid = $lotid");

//ロット内のデータ数
$res = mysql_query("select uniqid,finish from lotfile where lotid=$lotid order by ord");
$num_in_lot = mysql_num_rows($res);
while($row2 = mysql_fetch_assoc($res)){
	if($row2['uniqid'] == $uniqid)break;
}
if($resume){
	//未入力のものを探す
	while($row2 = mysql_fetch_assoc($res)){
		if($row2['finish'] == 0)break;
	}
}elseif($skipped){
	//スキップされたものを探す
	while($row2 = mysql_fetch_assoc($res)){
		if($row2['finish'] == -1)break;
	}
}else{
	$row2 = mysql_fetch_assoc($res);
}

if($row2){
	header("Location: metadata.php?lotid=$lotid&uniqid={$row2['uniqid']}&resume={$resume}&skipped={$skipped}");
}else{
	//ロット中の全てのデータの書き込みが終わっているか
	$unfinish_num = mysql_get_value("select count(*) from lotfile where finish <> 1 and lotid = $lotid");
	if($unfinish_num == 0){
		mysql_query("update lot set status = 2, finish_date = now() where lotid = $lotid");
	}
	header('Location: index.php');
}

?>
