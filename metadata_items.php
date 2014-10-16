<?php

include_once(dirname(__FILE__) . "/metadata_utils.php");
include_once(dirname(__FILE__) . "/metadata_options.php");

function metadata_items_first($items, $caller){
	$_uniqid = $items['uniqid'];
	$_md_type = output_md_type_selection($items['md_type'], $caller);
    $_series_flag = output_radio('series_flag', $items['series_flag'], '該当しない', '該当する', $caller, $items['series_flag']);
	$_betu_title_flag = output_radio('betu_title_flag', $items['betu_title_flag'], '無', '有', $caller, $items['betu_title_flag']);
	$_kiyo_flag = output_radio('kiyo_flag', $items['kiyo_flag'], '無', '有', $caller, $items['kiyo_flag']);
	$_iban_flag = output_radio('iban_flag', $items['iban_flag'], '該当しない', '該当する', $caller, $items['iban_flag']);
	$_license_flag = output_radio('license_flag', $items['license_flag'], '無', '有', $caller, $items['license_flag']);
	$_inyou_flag = output_radio('inyou_flag', $items['inyou_flag'], '該当しない', '該当する', $caller, $items['inyou_flag']);
	$_gov_issue = output_gov_issue_selection($items['gov_issue'], $caller);
	$_gov_issue_2 = output_text_input('gov_issue_2', $items['gov_issue_2'], $caller);
	$_gov_issue_chihou = output_text_input('gov_issue_chihou', $items['gov_issue_chihou'], $caller);
	$_gov_issue_miyagi = output_gov_issue_miyagi_selection($items['gov_issue_miyagi'], $caller);
	$_for_handicapped = output_for_handicapped_selection($items['for_handicapped'], $caller);
	// 整理表の段階でカセットテープが指定されていたら、入力時のみカセットテープ選択に自動設定する
	$_original_shiryo_keitai = output_original_shiryo_keitai_selection((($items['media_code'] == "32") && ($caller == _INPUT_)) ? "32" : $items['original_shiryo_keitai'], $caller);
	$_rippou_flag = output_radio('rippou_flag', $items['rippou_flag'], '該当しない', '該当する', $caller, $items['rippou_flag']);
	$_doctor_flag = output_radio('doctor_flag', $items['doctor_flag'], '該当しない', '該当する', $caller, $items['doctor_flag']);
	$_satuei_ken = output_text_input('satuei_ken',(isset($items['satuei_ken'])?$items['satuei_ken']:$items['satuei_basho_address']),$caller);
	$_keyword = output_keywords_tab($items['keyword'],$caller);

	$text_fields = array('standard_id', 'series_title',  'betu_title',  'betu_series', 'betu_series_title','naiyo_saimoku_title',
			'naiyo_saimoku_chosha', 'naiyo_saimoku_chosha_yomi', 'buhenmei',  'makiji_bango',
		'contributor','iban', 'iban_chosha','publisher',
		'chuuki', 'youyaku', 'mokuji', 'is_bubun', 'oya_uri', 'shigen_mei', 'has_bubun', 'ko_uri',
		'taisho_basho_uri', 'taisho_basho_ken', 'taisho_basho_shi', 'taisho_basho_banchi', 'taisho_basho_ido', 'taisho_basho_keido',
		'satusei_ido','satuei_keido','satuei_shi','satuei_banchi','kanko_hindo', 'kanko_kanji',
		'doctor','doctor_bango', // 'doctor_nen', 'doctor_tuki', 'doctor_bi',
		'doctor_daigaku',
		'keisai_go1', 'keisai_go2', 'keisai_shimei', 'keisai_kan', 'keisai_page', 'license_info','license_uri','license_holder','license_chuki','shiryo_keitai',
		'teller',  'haifu_taisho', 'haifu_basho', // 'haifu_nen', 'haifu_tuki', 'haifu_bi',
		'keiji_basho',
		'series_title_yomi','betu_title_yomi', 'betu_series_yomi', 'betu_series_title_yomi','naiyo_saimoku_title_yomi','buhenmei_yomi','makiji_bango_yomi', 'contributor_yomi', 'doctor_daigaku_yomi','teller_yomi','haifu_basho_yomi', 'keiji_basho_yomi', 'sekou_taisho');
		// 'keiji_nen', 'keiji_tuki', 'keiji_bi');
	foreach($text_fields as $f){
		$$f = output_text_input($f, $items[$f], $caller);
	}
	$text_area_fields = array('title', 'title_yomi', 'creator', 'creator_yomi');
	foreach($text_area_fields as $f){
		$$f = output_text_area($f, $items[$f], $caller);
	}
	$yomi_fields = array('title', 'creator', 'series_title','betu_title', 'betu_series', 'betu_series_title','naiyo_saimoku_chosha','naiyo_saimoku_title','buhenmei','makiji_bango', 'contributor', 'doctor_daigaku','teller','haifu_basho', 'keiji_basho');
	if($caller == _INPUT_){
		foreach($yomi_fields as $f){
			${$f.'_button'} = output_yomi_button($f, $f."_yomi", $items[$f]);
		}
	}
	$sakusei_nen = $items['sakusei_nen'];
	$sakusei_tuki = $items['sakusei_tuki'];
	$sakusei_bi = $items['sakusei_bi'];
	$online_nen = $items['online_nen'];
	$online_tuki = $items['online_tuki'];
	$online_bi = $items['online_bi'];
	$koukai_nen = $items['koukai_nen'];
	$koukai_tuki = $items['koukai_tuki'];
	$koukai_bi = $items['koukai_bi'];
	$doctor_nen = $items['doctor_nen'];
	$doctor_tuki = $items['doctor_tuki'];
	$doctor_bi = $items['doctor_bi'];
	$haifu_nen = $items['haifu_nen'];
	$haifu_tuki = $items['haifu_tuki'];
	$haifu_bi = $items['haifu_bi'];
	$keiji_nen = $items['keiji_nen'];
	$keiji_tuki = $items['keiji_tuki'];
	$keiji_bi = $items['keiji_bi'];
	$sekou_nen = $items['sekou_nen'];
	$sekou_tuki = $items['sekou_tuki'];
	$sekou_bi = $items['sekou_bi'];
	$shiryo_keitai = output_shiryo_keitai_selection($items['shiryo_keitai'], $caller);
	$language = output_language_selection($items['language'], $caller);
	$kanko_status = output_kanko_status_selection($items['kanko_status'], $caller);
	$open_level = output_open_level_selection($items['open_level'], $caller);
	$hakubutu_kubun = output_radio('hakubutu_kubun', $items['hakubutu_kubun'], '人工物', '自然物', $caller, $items['hakubutu_kubun']);
	$shosha_flag = output_radio('shosha_flag', $items['shosha_flag'], '該当しない', '該当する', $caller, $items['shosha_flag']);
	$online_flag = output_radio('online_flag', $items['online_flag'], '該当しない', '該当する', $caller, $items['online_flag']);
	$shoshi_flag = output_radio('shoshi_flag', $items['shoshi_flag'], '該当しない', '該当する', $caller, $items['shoshi_flag']);
	$chizu_kubun = output_radio('chizu_kubun', $items['chizu_kubun'], '地図', '地図帳', $caller, $items['chizu_kubun']);
	$seigen = output_radio('seigen', $items['seigen'], '該当しない', '悲惨（閲覧注意）', $caller, $items['seigen']);
	//
	$ndl_button = ($caller == _INPUT_) ? "<input type='button' value='国会図書館問い合わせ' onClick='ndl_check(); return false;'>" : '';
	//
	$class_hissu1= 'opthissu opthissu_図書 opthissu_記事 opthissu_新聞・雑誌 opthissu_音声・映像 opthissu_文書・楽譜 opthissu_地図・地図帳 opthissu_チラシ opthissu_会議録・含資料 opthissu_博物資料 opthissu_オンライン資料opthissu_語り opthissu_絵画・絵はがき opthissu_プログラム（スマホアプリ・ゲーム等）';
	$class_option2= 'optional optional_図書 optional_記事  optional_映像・音声  optional_文書・楽譜 optional_地図・地図帳';
	$class_option3='optional optional_図書 optional_記事 optional_雑誌・新聞 optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳 optional_チラシ optional_会議録・含資料 optional_博物資料 optional_絵画・絵はがき'; //目次
	$class_option4='optional optional_音声・映像 optional_地図・地図帳 optional_写真 optional_語り optional_絵画・絵はがき';

	return <<< EOS
	<tr><th>ユニークID</th><td>$_uniqid </td></tr>
	<tr><th class='hissu'>1.資料種別</th><td>$_md_type</td></tr>
	<tr><th>24.標準番号(ISBN等)<br>$ndl_button</th><td>$standard_id</td></tr>
	<tr><th class='$class_hissu1'>2.タイトル</th><td>$title</td></tr>
	<tr><th class='$class_hissu1'>3.タイトルのヨミ<br>$title_button</th><td>$title_yomi</td></tr>
	<tr><th class='hissu'>4.作成者・著者名</th><td>$creator</td></tr>
	<tr><th class='hissu'>5.作成者・著者名のヨミ$creator_button</th><td>$creator_yomi</td></tr>
	<tr><th class='opthissu opthissu_音声・映像 opthissu_写真 opthissu_絵画・絵はがき'>6.主題（キーワード）</th><td>$_keyword</td></tr>
	<tr><th class='hissu'>7.アクセス制御</th><td>$open_level</td></tr>
	<tr><th class='hissu'>8.シリーズ（継続資料）</th><td>$_series_flag</td></tr>
	<tr class='series_flag_option'><th class='$class_hissu1'>9.シリーズタイトル</th><td>$series_title</td></tr>
	<tr class='series_flag_option'><th class='$class_hissu1'>10.シリーズタイトルのヨミ<br>$series_title_button</th><td>$series_title_yomi</td></tr>
	<tr class='series_flag_option'><th>65.刊行状態</th><td>$kanko_status</td></tr>
	<tr><th>19.宮城県内地方公共団体刊行物<br><font size='-1'>宮城県内の自治体が発行元</font></th><td>$_gov_issue_miyagi</td></tr>
	<tr><th>18.地方公共団体刊行物<br><font size='-1'>該当する場合刊行元（団体名）</font></th><td>$_gov_issue_chihou</td></tr>
	<tr><th>22.立法資料</th><td>$_rippou_flag</td></tr>
	<tr class="rippou_flag_option"><th>97.施行対象</th><td>$sekou_taisho</td></tr>
	<tr class="rippou_flag_option"><th>98.施行日</th><td>
		<input type='text' class='imeDisable' name='sekou_nen' size='4' value='$sekou_nen'>年
		<input type='text' class='imeDisable' name='sekou_tuki' size='2' value='$sekou_tuki'>月　
		<input type='text' class='imeDisable' name='sekou_bi' size='2' value='$sekou_bi'>日
	</td></tr>
	<tr><th>44.作成・撮影日</th><td>（西暦）
		<input type='text' class='imeDisable' name='sakusei_nen' size='4' value='$sakusei_nen'>年
		<input type='text' class='imeDisable' name='sakusei_tuki' size='2' value='$sakusei_tuki'>月
		<input type='text' class='imeDisable' name='sakusei_bi' size='2' value='$sakusei_bi'>日</td></tr>
	<tr class='$class_option4'><th>61.撮影場所（県名）
		<!--とりあえず、撮影場所の住所を県のところに表示させておく。基本情報整理表には、複数が入力されている場合あり-->
		</th><td>$_satuei_ken</td></tr>
	<tr class='$class_option4'><th>62.撮影場所（市町村）</th><td>$satuei_shi</td></tr>
	<tr class='$class_option4'><th>63.撮影場所（街路番地）</th><td>$satuei_banchi</td></tr>
	<tr class='optional optional_語り'><th>85.話者</th><td>$teller</td></tr>
	<tr class='optional optional_語り'><th>86.話者のヨミ<br>$teller_button</th><td>$teller_yomi</td></tr>
	<tr class='optional optional_チラシ optional_会議録・含資料'><th>87.配布場所</th><td>$haifu_basho</td></tr>
	<tr class='optional optional_チラシ optional_会議録・含資料'><th>88.配布場所のヨミ<br>$haifu_basho_button</th><td>$haifu_basho_yomi</td></tr>
	<tr class='optional optional_チラシ optional_会議録・含資料'><th>89.配付日時</th><td>（西暦）
		<input type='text' class='imeDisable' name='haifu_nen' size='4' value='$haifu_nen'>年
		<input type='text' class='imeDisable' name='haifu_tuki' value='$haifu_tuki' size='2'>月
		<input type='text' class='imeDisable' name='haifu_bi' value='$haifu_bi' size='2'>日</td ></tr>
	<tr class='optional optional_チラシ optional_会議録・含資料'><th>90.配布対象（被災者等）</th><td>$haifu_taisho</td></tr>
	<tr class='optional optional_ポスター optional_博物資料'><th>91.掲示・設置場所</th><td>$keiji_basho</td></tr>
	<tr class='optional optional_ポスター optional_博物資料'><th>92.掲示・設置場所のヨミ<br>$keiji_basho_button</th><td>$keiji_basho_yomi</td></tr>
	<tr><th>96.情報の質</th><td>$seigen</td></tr>

	<tr><td colspan='2'>
			<input type="submit" name='next' value="登録して次へ" onClick="setSkipCheck(0);">
			<input type="submit" name='quit' value="中断" onClick="setSkipCheck(1);">
	</td></tr>
	<tr><td></td></tr>

	<tr><th>11.別タイトルの有無</th><td>$_betu_title_flag</td></tr>
	<tr class='betu_title_flag_option'><th>25.別タイトル</th><td>$betu_title</td></tr>
	<tr class='betu_title_flag_option'><th>26.別タイトルのヨミ<br>$betu_title_button</th><td>$betu_title_yomi</td></tr>
	<tr class='betu_title_flag_option'><th>27.別シリーズタイトル</th><td>$betu_series_title</td></tr>
	<tr class='betu_title_flag_option'><th>28.別シリーズタイトルのヨミ<br>$betu_series_title_button</th><td>$betu_series_title_yomi</td></tr>
	<tr><th>12.寄与者（寄贈者）の有無</th><td>$_kiyo_flag</td></tr>
	<tr class='kiyo_flag_option'><th>36.寄与者（寄贈者）</th><td>$contributor</td></tr>
	<tr class='kiyo_flag_option'><th>37.寄与者（寄贈者）のヨミ<br>$contributor_button</th><td>$contributor_yomi</td></tr>
	<tr class='optional optional_図書 optional_雑誌・新聞'><th>13.異版<font size='-1'>（第x版、改訂版等）</font></th><td>$_iban_flag</td></tr>
	<tr class='iban_flag_option'><th>38.異版名(第x版）</th><td>$iban</td></tr>
	<tr class='iban_flag_option'><th>39.異版の著者名</th><td>$iban_chosha</td></tr>
	<tr><th>14.ライセンス(CC等)の有無</th><td>$_license_flag</td></tr>
	<tr class='license_flag_option'><th>77.ライセンス情報</th><td>$license_info</td></tr>
	<tr class='license_flag_option'><th>78.URIへの参照</th><td>$license_uri</td></tr>
	<tr class='license_flag_option'><th>79.ライセンス保有者名</th><td>$license_holder</td></tr>
	<tr class='license_flag_option'><th>80.権利・利用条件に関する注記</th><td>$license_chuki</td></tr>
	<tr><th>15.引用資料<br><font size='-1'>親となる資料からの引用</font></th><td>$_inyou_flag</td></tr>
	<!--引用資料-->
	<tr class='inyou_flag_option'><th>48.～の一部分である</th><td>$is_bubun</td></tr>
	<tr class='inyou_flag_option'><th>49.親URIへの参照</th><td>$oya_uri</td></tr>
	<tr class='inyou_flag_option'><th>50.参照する情報資源の名称</th><td>$shigen_mei</td></tr>
	<tr class='inyou_flag_option'><th>51.～を一部分として持つ</th><td>$has_bubun</td></tr>
	<tr class='inyou_flag_option'><th>52.子URIへの参照</th><td>$ko_uri</td></tr>
	<tr><th>16.政府刊行物・刊行元<br><font size='-1'>x省等が発行元</font></th><td>$_gov_issue</td></tr>
	<tr><th>17.官公庁刊行物<br><font size='-1'>該当する場合刊行元（機関名）<br>x省等の下部機関が発行元</font></th><td>$_gov_issue_2</td></tr>
	<tr><th>20.視聴覚者向け資料</th><td>$_for_handicapped</td></tr>
	<tr><th>21.オリジナル資料の形態</th><td>$_original_shiryo_keitai</td></tr>
    <tr class='optional optional_図書'><th>23.博士論文</th><td>$_doctor_flag</td></tr>
	<!--博士論文-->
	<tr class="doctor_flag_option"><th>67.学位</th><td>$doctor</td></tr>
	<tr class="doctor_flag_option"><th>68.報告番号</th><td>$doctor_bango</td></tr>
	<tr class="doctor_flag_option"><th>69.授与年月日</th><td>（西暦）
		<input type='text' class='imeDisable' name='doctor_nen' value='$doctor_nen' size='4'>年
		<input type='text' class='imeDisable' name='doctor_tuki' value='$doctor_tuki' size='2'>月　
		<input type='text' class='imeDisable' name='doctor_bi' value='$doctor_bi' size='2'>日</td></tr>
	<tr class="doctor_flag_option"><th>70.授与大学</th><td>$doctor_daigaku</td></tr>
	<tr class="doctor_flag_option"><th>71.授与大学のヨミ<br>$doctor_daigaku_button</th><td>$doctor_daigaku_yomi</td></tr>
	<tr><th>40.出版社・公開者</th><td>$publisher</td></tr>
	<tr class='optional optional_オンライン資料'><th>45.オンライン資料採取日</th><td>（西暦）
		<input type='text' class='imeDisable' name='online_nen' value='$online_nen' size='4'>年
		<input type='text' class='imeDisable' name='online_tuki' value='$online_tuki' size='2'>月
		<input type='text' class='imeDisable' name='online_bi' value='$online_bi' size='2'>日</td></tr>
	<tr><th>46.公開日</th><td>（西暦）
		<input type='text' class='imeDisable' name='koukai_nen' size='4' value='$koukai_nen'>年
		<input type='text' class='imeDisable' name='koukai_tuki' size='2' value='$koukai_tuki'>月
        <input type='text' class='imeDisable' name='koukai_bi' size='2' value='$koukai_bi'>日</td></tr>
	<tr><th>47.言語</th><td>$language</td></tr>
	<!--刊行頻度・状態・巻次（雑誌の場合のみ）-->
	<tr class='optional optional_雑誌・新聞'><th>64.刊行頻度</th><td>$kanko_hindo</td></tr>
	<tr class='optional optional_雑誌・新聞'><th>66.刊行巻次</th><td>$kanko_kanji</td></tr>
	<!--通巻番号等-->
	<tr class='optional optional_記事 optional_会議録・含資料'><th>72.掲載通号</th><td>$keisai_go1</td></tr>
	<tr class='optional optional_記事 optional_会議録・含資料'><th>73.掲載号</th><td>$keisai_go2</td></tr>
	<tr class='optional optional_記事 optional_会議録・含資料'><th>74.掲載誌名</th><td>$keisai_shimei</td></tr>
	<tr class='optional optional_記事 optional_会議録・含資料'><th>75.掲載巻（論文の場合）</th><td>$keisai_kan</td></tr>
	<tr class='optional optional_記事 optional_会議録・含資料'><th>76.掲載ページ</th><td>$keisai_page</td></tr>
	<tr class='optional optional_図書 optional_記事 optional_新聞・雑誌 optional_文書・楽譜 optional_地図・地図帳 optional_ポスター optional_チラシ optional_会議録・含資料 optional_絵画・絵はがき'><th>81.資料形態（大活字等)</th><td>$shiryo_keitai </td></tr>
	<tr class='optional optional_博物資料'><th>82.博物資料の区分</th><td>$hakubutu_kubun</td></tr>
	<tr class='optional optional_図書 optional_記事 optional_新聞・雑誌 optional_文書・楽譜 optional_地図・地図帳 optional_ポスター optional_チラシ optional_会議録・含資料 optional_絵画・絵はがき'><th>83.書写資料</th><td>$shosha_flag</td></tr>
	<tr class='optional optional_記事 optional_雑誌・新聞'><th>84.オンラインジャーナル（学術系）</th><td>$online_flag</td></tr>
	<tr class='optional optional_ポスター optional_博物資料'><th>93.掲示・配付日時</th><td>（西暦）
		<input type='text' class='imeDisable' name='keiji_nen' size='4' value='$keiji_nen'>年
		<input type='text' class='imeDisable' name='keiji_tuki' size='2' value='$keiji_tuki'>月　
		<input type='text' class='imeDisable' name='keiji_bi' size='2' value='$keiji_bi'>日
	</td></tr>
	<tr class='optional optional_図書 optional_新聞・雑誌 ptional_記事'><th>94.書誌データ</th><td>$shoshi_flag</td></tr>
	<tr class='optional optional_地図・地図帳'><th>95.地図か地図帳か</th><td>$chizu_kubun</td></tr>

	<tr><td colspan='2'>
			<input type="submit" name='next' value="登録して次へ" onClick="setSkipCheck(0);">
			<input type="submit" name='quit' value="中断" onClick="setSkipCheck(1);">
	</td></tr>
	<tr><td></td></tr>

	<tr class='$class_option2'><th>29.内容細目タイトル</th><td>$naiyo_saimoku_title</td></tr>
	<tr class='$class_option2'><th>30.内容細目タイトルのヨミ<br>$naiyo_saimoku_title_button</th><td>$naiyo_saimoku_title_yomi</td></tr>
	<tr class='$class_option2'><th>31.内容細目著者</th><td>$naiyo_saimoku_chosha</td></tr>
	<tr class='$class_option2'><th>99.内容細目著者のヨミ<br>$naiyo_saimoku_chosha_button</th><td>$naiyo_saimoku_chosha_yomi</td></tr>
	<tr class='$class_option2'><th>32.部編名</th><td>$buhenmei</td></tr>
	<tr class='$class_option2'><th>33.部編名のヨミ<br>$buhenmei_button</th><td>$buhenmei_yomi</td></tr>
	<tr class='$class_option2'><th>34.巻次・部編番号</th><td>$makiji_bango</td></tr>
	<tr class='$class_option2'><th>35.巻次・部編番号のヨミ<br>$makiji_bango_button</th><td>$makiji_bango_yomi</td></tr>
	<tr><th>41.注記等</th><td>$chuuki</td></tr>
	<tr><th>42.要約</th><td>$youyaku</td></tr>
	<tr class='$class_option3'><th>43.目次</th><td>$mokuji</td></tr>
    <!--情報資源が対象とする場所-->
	<tr><th>53.情報資源が対象とする場所(URI)<br><input type='button' value='地図から取得' onClick="getAddress('taisho_');return false;"></th><td>$taisho_basho_uri</td></tr>
	<tr><th>54.情報資源が対象とする場所（県名）</th><td>$taisho_basho_ken</td></tr>
	<tr><th>55.情報資源が対象とする場所（市町村）</th><td>$taisho_basho_shi</td></tr>
	<tr><th>56.情報資源が対象とする場所（街路番地）</th><td>$taisho_basho_banchi</td></tr>
	<tr><th>57.情報資源が対象とする場所（緯度）</th><td>$taisho_basho_ido</td></tr>
	<tr><th>58.情報資源が対象とする場所（経度）</th><td>$taisho_basho_keido</td></tr>
	<tr class='$class_option4'><th>59.撮影場所（緯度）<br><input type='button' value='地図から取得' onClick="getAddress('satuei_');return false;"></th><td>$satusei_ido</td></tr>
	<tr class='$class_option4'><th>60.撮影場所（経度）</th><td>$satuei_keido</td></tr>


EOS;
}

function  output_handover_items($items){
  $hidden_fields = array(
  	        // 基本情報整理表より
                'local_code','kanri_bango','contributor','contributor_yomi','bunrui_code',
                'bunsho_bunrui','title','creator','creator_yomi','sakusei_nen',
                'sakusei_tuki','sakusei_bi','satuei_basho_zip','satuei_basho_address',
                'satuei_basho_address_yomi','haifu_basho','haifu_basho_yomi','keyword',
                'renraku_saki_zip','renraku_saki_address','renraku_saki_tel','renraku_saki_other',
                'kenri_shori','horyu_reason','open_level','media_code',
                //
                'lot', 'lot_id', 'lotid', 'id', 'uniqid'); // lot, lotid, lot_idが混在
	foreach($hidden_fields as $f){
		$$f = output_hidden_input($f, $items[$f]);
	}
	// こちらだけでいいのでは？
	foreach($items as $f){
		$$f = output_hidden_input($f, $items[$f]);
	}
}

