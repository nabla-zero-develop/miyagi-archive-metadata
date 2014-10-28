<?php
require_once 'include/config.php';
require_once 'include/db.php';

$query = "select uniqid,md_type,series_flag,betu_title_flag,kiyo_flag,iban_flag,license_flag,inyou_flag,gov_issue,gov_issue_2,gov_issue_chihou,gov_issue_miyagi,for_handicapped,original_shiryo_keitai,rippou_flag,doctor_flag,standard_id,title,title_yomi,series_title,series_title_yomi,betu_series_title,betu_series_title_yomi,betu_title,betu_title_yomi,naiyo_saimoku_chosha,naiyo_saimoku_title,naiyo_saimoku_title_yomi,buhenmei,buhenmei_yomi,makiji_bango,makiji_bango_yomi,contributor,contributor_yomi,creator,creator_yomi,iban,iban_chosha,publisher,keyword,chuuki,youyaku,mokuji,sakusei_nen,sakusei_tuki,sakusei_bi,online_nen,online_tuki,online_bi,koukai_nen,koukai_tuki,koukai_bi,language,is_bubun,oya_uri,shigen_mei,has_bubun,ko_uri,taisho_basho_uri,taisho_basho_ken,taisho_basho_shi,taisho_basho_banchi,taisho_basho_ido,taisho_basho_keido,satuei_ken,satuei_shi,satuei_banchi,satuei_keido,satuei_ido,kanko_hindo,kanko_status,kanko_kanji,doctor,doctor_bango,doctor_nen,doctor_tuki,doctor_bi,doctor_daigaku,doctor_daigaku_yomi,keisai_go1,keisai_go2,keisai_shimei,keisai_kan,keisai_page,open_level,license_info,license_uri,license_holder,license_chuki,origina_shiryo_keitai,hakubutu_kubun,shosha_flag,online_flag,teller,teller_yomi,haifu_basho,haifu_basho_yomi,haifu_nen,haifu_tuki,haifu_bi,haifu_taisho,keiji_basho,keiji_basho_yomi,keiji_nen,keiji_tuki,keiji_bi,shoshi_flag,chizu_kubun,seigen,skip_reason from metadata";
$rows = mysql_get_multi_rows($query);

$header = array('ユニークID','資料種別','シリーズ（継続資料）か否か','別タイトルの有無','寄与者（寄贈者）の有無','異版の有無','ライセンスの有無','引用資料','政府刊行物・刊行元','官公庁刊行物','地方公共団体刊行物','宮城県内地方公共団体刊行物','視聴覚者向け資料','オリジナル資料の形態','立法資料','博士論文','原資料の標準番号','タイトル','タイトルのヨミ','シリーズタイトル','シリーズタイトルのヨミ','別シリーズタイトル','別シリーズタイトルのヨミ','別タイトル','別タイトルのヨミ','内容細目タイトル','内容細目タイトルのヨミ','内容細目著者','部編名','部編名のヨミ','巻次・部編番号','巻次・部編番号のヨミ','寄与者（寄贈者）','寄与者（寄贈者）のヨミ','作成者','作成者のヨミ','異版名(第x版）','異版の著者名','出版社・公開者','サブジェクト（キーワード）','注記等','要約','目次','作成・撮影日','作成・撮影日','作成・撮影日','Online資料採取日','Online資料採取日','Online資料採取日','公開日','公開日','公開日','言語','引用資料 ～の一部分である','引用資料 親URIへの参照','引用資料 参照する情報資源の名称','引用資料 ～を一部分として持つ','引用資料 子URIへの参照','情報資源が対象とする場所(URI)','情報資源が対象とする場所（県名）','情報資源が対象とする場所（市町村）','情報資源が対象とする場所（街路番地）','情報資源が対象とする場所（緯度）','情報資源が対象とする場所（経度）','撮影場所（県）','撮影場所（市町村）','撮影場所（街路番地）','撮影場所（経度）','撮影場所（緯度）','刊行頻度','刊行状態','刊行巻次','博士論文 学位','博士論文 報告番号','博士論文 年','博士論文 月','博士論文 日','博士論文 授与大学','博士論文 授与大学のヨミ','通巻番号等 掲載通号','通巻番号等 掲載号','通巻番号等 掲載誌名','通巻番号等 掲載巻（論文の場合）','通巻番号等 掲載ページ','公開レベル','ライセンス情報','URIへの参照','ライセンス保有者名','権利・利用条件に関する注記','資料形態','博物資料の区分','書写資料','オンラインジャーナル','話者','話者のヨミ','配布場所','配布場所のヨミ','配付日時','配付日時','配付日時','配布対象（被災者等）','配布場所','配布場所のヨミ','掲示・配付日時','掲示・配付日時','掲示・配付日時','書誌データ','地図か地図帳か','情報の質 0:該当しない,1:悲惨（閲覧注意）','入力スキップ理由');
array_unshift($rows,$header);

mb_convert_variables('SJIS','UTF-8',$rows);

header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"metadata.csv\"");

foreach($rows as $row){
	foreach($row as &$col){
		$col = '"'.str_replace('"', '""', $col).'"';
	}
	echo implode(',',$row)."\r\n";
}

?>