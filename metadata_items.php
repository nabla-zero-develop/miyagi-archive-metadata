<?php

include_once(dirname(__FILE__) . "/metadata_utils.php");
include_once(dirname(__FILE__) . "/metadata_options.php");

function metadata_items_first($items, $caller){
	$_uniqid = $items['uniqid'];
	$_md_type = output_md_type_selection($items['md_type']);
    $_series_flag = output_radio('series_flag', $items['series_flag'], '該当しない', '該当する'); 
	$_betu_title_flag = output_radio('betu_title_flag', $items['betu_title_flag'], '無', '有'); 
	$_kiyo_flag = output_radio('kiyo_flag', $items['kiyo_flag'], '無', '有'); 
	$_iban_flag = output_radio('iban_flag', $items['iban_flag'], '該当しない', '該当する');
	$_license_flag = output_radio('license_flag', $items['license_flag'], '無', '有');
	$_inyou_flag = output_radio('inyou_flag', $items['inyou_flag'], '該当しない', '該当する'); 
	$_gov_issue = output_gov_issue_selection($items['gov_issue']);
	$_gov_issue_2 = output_text_input('gov_issue_2', $items['gov_issue_2']);
	$_gov_issue_chihou = output_text_input('gov_issue_chihou', $items['gov_issue_chihou']);
	$_gov_issue_miyagi = output_gov_issue_miyagi_selection($items['gov_issue_miyagi']); 
	$_for_handicapped = output_for_handicapped_selection($items['for_handicapped']);
	// 整理表の段階でカセットテープが指定されていたら、入力時のみカセットテープ選択に自動設定する
	$_original_shiryo_keitai = output_original_shiryo_keitai_selection((($items['media_code'] == "32") && ($caller == _INPUT_)) ? "32" : $original_shiryo_keitai);
	$_rippou_flag = output_radio('rippou_flag', $items['rippou_flag'], '該当しない', '該当する'); 
	$_doctor_flag = output_radio('doctor_flag', $items['doctor_flag'], '該当しない', '該当する'); 

	return <<< EOS
	<tr><th>ユニークID</th><td>$_uniqid </td></tr>
	<tr><th class='hissu'>資料種別</th><td>$_md_type</td></tr>
	<tr><th class='hissu'>シリーズ（継続資料）</th><td>$_series_flag</td></tr>	
	<tr><th>別タイトルの有無</th><td>$_betu_title_flag</td></tr>	
	<tr><th>寄与者（寄贈者）の有無</th><td>$_kiyo_flag</td></tr>
	<tr class='optional optional_図書 optional_雑誌・新聞'><th>異版<font size='-1'>（第x版、改訂版等）</font></th><td>$_iban_flag</td></tr>	
	<tr><th>ライセンス(CC等)の有無</th><td><td>$_license_flag</td></tr>
	<tr><th>引用資料<br><font size='-1'>親となる資料からの引用</font></th><td>$_inyou_flag</td></tr>
	<tr><th>政府刊行物・刊行元<br><font size='-1'>x省等が発行元</font></th><td>$_gov_issue</td></tr>
	<tr><th>官公庁刊行物<br><font size='-1'>該当する場合刊行元（機関名）<br><font size='-1'>x省等の下部機関が発行元</font></th><td>$_gov_issue_2</td></tr>
	<!--地方公共団体刊行物 --><tr><th>地方公共団体刊行物<br><font size='-1'>該当する場合刊行元（団体名）</font></th><td>$_gov_issue_chihou</td></tr>
    <tr><th>宮城県内地方公共団体刊行物<br><font size='-1'>宮城県内の自治体が発行元</font></th><td>$_gov_issue_miyagi</td></tr>
	<tr><th>視聴覚者向け資料</th><td>$_for_handicapped</td></tr>
	<tr><th>オリジナル資料の形態</th><td>$_original_shiryo_keitai</td></tr>
    <tr><th>立法資料</th><td>$_rippou_flag</td></tr>
    <tr class='optional optional_図書'><th>博士論文</th><td>$_doctor_flag</td></tr>	
EOS;
}

function output_items_last($items){
	$text_fields = array('standard_id', 'title', 'title_yomi', 'series_title', 'series_title_yomi', 'betu_title', 'betu_title_yomi', 'betu_series', 'betu_series_yomi', 
	'naiyo_saimoku_title_yomi',	'naiyo_saimoku_title_yomi','naiyo_saimoku_chosha', 'buhenmei', 'buhenmei_yomi', 'makiji_bango', 'makiji_bango_yomi',
	'creator', 'contributor', 'contributor_yomi', 'iban', 'iban_chosha','publisher',
	'keyword', 'chuuki', 'youyaku', 'mokuji', 'is_bubun', 'ioya_uri', 'shigen_mei', 'has_bubun', 'ko_uri',
	'taisho_basho_uri', 'taisho_basho_keni', 'taisho_basho_shi', 'taisho_basho_banchii', 'taisho_basho_ido',
	'taisho_basho_keido');
	foreach($text_fields as $f){
		$$f = output_text_input($f, $items[$f]);
	}
	$sakusei_nen = $item['sakusei_nen'];
	$sakusei_tuki = $item['sakusei_tuki'];
	$sakusei_bi = $item['sakusei_bi'];
	$online_nen = $item['online_nen'];
	$online_tuki = $item['online_tuki'];
	$online_bi = $item['online_bi'];
	$koukai_nen = $item['koukai_nen'];
	$koukai_tuki = $item['koukai_tuki'];
	$koukai_hi = $item['koukai_hi'];
	$shiryo_keitai = output_shiryo_keitai_selection($items['shiryo_keitai']);
	$language = output_for_handicapped_selection($items['language']);
	//
	$class_option1= 'optional optional_図書 optional_記事 optional_新聞・雑誌 optional_音声・映像 optional_文書・楽譜 optional_地図・地図帳 optional__チラシ optional_会議録・含資料 optional_博物資料 optional_オンライン資料 optional__語り optional__絵画・絵はがき optional_プログラム（スマホアプリ・ゲーム等）';
	$class_option2= 'optional optional_図書 optional_記事  optional_映像・音声  optional_文書・楽譜 optional_地図・地図帳';
	$class_option3='optional optional_図書 optional_記事 optional_雑誌・新聞 optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳 optional_チラシ optional_会議録・含資料 optional_博物資料 optional_絵画・絵はがき'; //目次
	$class_option4='optional optional_音声・映像 optional_地図・地図帳 optional_写真 optional_語り optional_絵画・絵はがき';
	return <<< EOS
	<tr><th>標準番号(ISBN等)<br><input type='button' value='NDLチェック'></th><td>$standard_id</td></tr>	
	<tr><th class='$class_option1'>タイトル<br><input type='button' value='NDLチェック'></th><td>$title</td></tr>		
	<tr class='series_flag_option'><th class='$class_option1'>イトルのヨミ</th><td>$title_yomi</td></tr>
	<tr class='series_flag_option'><th class='$class_option1'>シリーズタイトル</th><td>$series_title</td></tr>
	<tr class='series_flag_option'><th class='$class_option1>シリーズタイトルのヨミ</th><td>$series_title_yomi</td></tr>	 
	<tr class='betu_title_flag_option'><th>別タイトル</th><td>$betu_title</td></tr>
	<tr class='betu_title_flag_option'><th>別タイトルのヨミ</th><td>$betu_title_yomi</td></tr>
	<tr class='betu_title_flag_option'><th>別シリーズタイトル</th><td>$betu_series_title</td></tr>
	<tr class='betu_title_flag_option'><th>別シリーズタイトルのヨミ</th><td>$betu_series_title_yomi</td></tr>	
	<tr class='$class_option2'><th>内容細目タイトル</th>$naiyo_saimoku_title_yomiv<td></td></tr>	
	<tr class='$class_option2'><th>内容細目タイトルのヨミ</th><td>$naiyo_saimoku_title_yomi</td></tr>	
	<tr class='$class_option2'><th>内容細目著者</th><td>$naiyo_saimoku_chosha</td></tr>	
	<tr class='$class_option2'><th>部編名</th><td>$buhenmei</td></tr>	
	<tr class='$class_option2'><th>部編名のヨミ</th><td>$buhenmei_yomi</td></tr>	
	<tr class='$class_option2'><th>巻次・部編番号</th><td>$makiji_bango</td></tr>	
	<tr class='$class_option2'><th>巻次・部編番号のヨミ</th><td>$makiji_bango_yomi</td></tr>	
	<tr><th class='hissu'>作成者・著者名</th><td>$creator</td></tr>
	<tr class='kiyo_flag_option'><th>寄与者（寄贈者）</th><td>$contributor</td></tr>	
	<tr class='kiyo_flag_option'><th>寄与者（寄贈者）のヨミ</th><td>$contributor_yomi</td></tr>
	<tr class='iban_flag_option'><th>異版名(第x版）</th><td>$iban</td></tr>
	<tr class='iban_flag_option'><th>異版の著者名</th><td>$iban_chosha</td></tr>
	<tr><th>出版社・公開者</th><td>$publisher</td></tr>
	<tr><th class='optional optional_音声・映像 optional_写真 optional_絵画・絵はがき'>主題（キーワード）</th><td>$keyword</td></tr>
	<tr><th>注記等</th><td>$chuuki</td></tr>
	<tr><th>要約</th><td>$youyaku</td></tr>
	<tr class='$class_option3'><th>目次</th><td>$mokuji</td></tr>
	<tr><th>作成・撮影日</th><td>
		<input type='text' name='sakusei_nen' size='4' value='$sakusei_nen'>年（西暦）
		<input type='text' name='sakusei_tuki' size='2' value='$sakusei_tuki; ?>'>月
		<input type='text' name='sakusei_bi' size='2' value='$sakusei_bi; ?>'>日</td></tr>
	<tr class='optional optional_オンライン資料'><th>オンライン資料採取日</th><td>
		<input type='text' name='online_nen' value='$online_nen' size='4'>年（西暦）
		<input type='text' name='online_tuki' value='$online_tuki' size='2'>月
		<input type='text' name='onlilne_bi' value='$online_bi' size='2'>日</td></tr>
	<tr><th>公開日</th><td>
		<input type='text' name='koukai_nen' size='4' value='$koukai_nen'>年（西暦）
		<input type='text' name='koukai_tuki' size='2' value='$koukai_tuki'>月
        	<input type='text' name='koukai_hi' size='2' value='$koukai_hi>日</td></tr>
	<tr><th>言語</th><td>$language</td></tr>
	<!--引用資料-->
	<tr class='inyou_flag_option'><th>～の一部分である</th><td>$is_bubun</td></tr>
	<tr class='inyou_flag_option'><th>親URIへの参照</th><td>$ioya_uri</td></tr>
	<tr class='inyou_flag_option'><th>参照する情報資源の名称</th><td>$shigen_mei</td></tr>
	<tr class='inyou_flag_option'><th>～を一部分として持つ</th><td>$has_bubun</td></tr>
	<tr class='inyou_flag_option'><th>子URIへの参照</th><td>$ko_uri</td></tr>
    <!--情報資源が対象とする場所-->
	<tr><th>情報資源が対象とする場所(URI)<br><input type='button' value='地図から取得'></th><td>$taisho_basho_uri</td></tr>
	<tr><th>情報資源が対象とする場所（県名）</th><td>$taisho_basho_keni</td></tr>
	<tr><th>情報資源が対象とする場所（市町村）</th><td>$taisho_basho_shi</td></tr>
	<tr><th>情報資源が対象とする場所（街路番地）</th><td>$taisho_basho_banchii</td></tr>
	<tr><th>情報資源が対象とする場所（緯度）</th><td>$taisho_basho_ido</td></tr>
	<tr><th>情報資源が対象とする場所（経度）</th><td>$taisho_basho_keido</td></tr>








	<!--撮影場所-->
	<tr class='$class_option4'><th>撮影場所（緯度）<br><input type='button' value='地図から取得'></th><td>$satusei_ido</td></tr>
	<tr class='$class_option4'><th>撮影場所（経度）</th><td>$satuei_keido</td></tr>
	<tr class='$class_option4'><th>撮影場所（県名）
		<!--とりあえず、撮影場所の住所を県のところに表示させておく。基本情報整理表には、複数が入力されている場合あり-->
		</th><td>$satuei_basho_address</td></tr>
	<tr class='$class_option4'><th>撮影場所（市町村）</th><td>$satuei_shi</td></tr>
	<tr class='$class_option4'><th>撮影場所（街路番地）</th><td>$satuei_banch</td></tr>

	<!--刊行頻度・状態・巻次（雑誌の場合のみ）-->
	<tr class='optional optional_雑誌・新聞'><th>刊行頻度</th><td>$kanko_hindo</td></tr>
	<tr class='series_flag_option'><th>刊行状態<td><select name='kanko_status'>
	        <option value='u' <?php if ($kanko_status=="c") { echo "selected"; } ?>>不明</option>
            <option value='c' <?php if ($kanko_status=="d") { echo "selected"; } ?>>刊行中</option>
            <option value='d' <?php if ($kanko_status=="u") { echo "selected"; } ?>>廃刊</option>
    </select>
	</td></tr>
	<tr class='optional optional_雑誌・新聞'><th>刊行巻次</th><td>$kanko_kanji</td></tr>
	
	<!--博士論文-->
	<tr class="doctor_flag_option"><th>学位</th><td>$doctor</td></tr>
	<tr class="doctor_flag_option"><th>報告番号</th><td>$doctor_bango</td></tr> 
	<tr class="doctor_flag_option"><th>授与年月日</th><td>
		<input type='text' name='doctor_nen' value='$doctor_nen' size='4'>年（西暦）
		<input type='text' name='doctor_tuki' value='$doctor_tuki' size='2'>月　
		<input type='text' name='doctor_bi' value='$doctor_bi' size='2'>日</td></tr>
	<tr class="doctor_flag_option"><th>授与大学</th><td>$doctor_daigaku</td></tr>
	<tr class="doctor_flag_option"><th>授与大学のヨミ</th><td>$doctor_daigaku_yomi</td></tr>
	<tr class="doctor_flag_option"><th></th><td></tr>

	<!--通巻番号等-->
	<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載通号</th><td>$keisai_go1</td></tr>
	<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載号</th><td>$keisai_go2</td></tr>
	<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載誌名</th><td>$keisa_shimei</td></tr>
	<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載巻（論文の場合）</th><td>$keisai_kan</td></tr>
	<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載ページ</th><td>$keisai_page</td></tr>

	<!--アクセス制御-->
	<tr><th class='hissu'>アクセス制御
	<td><select name='open_level'>
			<option value='0' <?php if ($open_level=="0") { echo "selected"; } ?>>非公開</option>
            <option value='1' <?php if ($open_level=="1") { echo "selected"; } ?>>公開</option>
            <option value='2' <?php if ($open_level=="2") { echo "selected"; } ?>>限定公開</option>
            <option value='3' <?php if ($open_level=="3") { echo "selected"; } ?>>公開保留</option>
		</select>
	</td></tr>

	<tr class='license_flag_option'><th>ライセンス情報</th><td>$license_info</td></tr>
	<tr class='license_flag_option'><th>URIへの参照</th><td>$license_uri</td></tr>
	<tr class='license_flag_option'><th>ライセンス保有者名</th><td>$license_holder</td></tr>
	<tr class='license_flag_option'><th>権利・利用条件に関する注記</th><td>$license_chuki</td></tr>
	<tr class='optional optional_図書 optional_記事 optional_新聞・雑誌 optional_文書・楽譜 optional_地図・地図帳 optional_ポスター optional_チラシ optional_会議録・含資料 optional_絵画・絵はがき'><th>資料形態（大活字等)</th><td>$shiryo_keitai </td></tr>

	<!--博物資料の区分 -->
	<tr class='optional optional_博物資料'><th>博物資料の区分</th><td>
		<input type='radio' value='人工物' name = 'hakubutu_kubun' <?php if ($hakubutu_kubun=="人工物") { echo "checked"; } ?>>人工物
		<input type='radio' value='自然物' name = 'hakubutu_kubun' <?php if ($hakubutu_kubun=="自然物") { echo "checked"; } ?>>自然物
	</td></tr>

	<!--書写資料-->
	<tr class='optional optional_図書 optional_記事 optional_新聞・雑誌 optional_文書・楽譜 optional_地図・地図帳 optional_ポスター optional_チラシ optional_会議録・含資料 optional_絵画・絵はがき'><th>書写資料</th><td>
		<input type='radio' value=0 name = 'shosha_flag' <?php if ($shosha_flag==0) { echo "checked"; } ?>>該当しない
		<input type='radio' value=1 name = 'shosha_flag' <?php if ($shosha_flag==1) { echo "checked"; } ?>>該当する
	</td></tr>

	<!--オンラインジャーナル-->
	<tr class='optional optional_記事 optional_雑誌・新聞'><th>オンラインジャーナル（学術系）</th><td>
		<input type='radio' value=0 name = 'online_flag' <?php if ($online_flag==0) { echo "checked"; } ?>>該当しない
		<input type='radio' value=1 name = 'online_flag' <?php if ($online_flag==1) { echo "checked"; } ?>>該当する
	</td></tr>

	<tr class='optional optional_語り'><th>話者</th><td>$teller</td></tr>
	<tr class='optional optional_語り'><th>話者のヨミ</th><td>$teller_yomi</td></tr>

	<!--配布場所とヨミ、配付日時、配付対象-->
	<tr class='optional optional_チラシ optional_会議録・含資料'><th>配布場所</th><td>$haifu_basho</td></tr>
	<tr class='optional optional_チラシ optional_会議録・含資料'><th>配布場所のヨミ</th><td>$haifu_basho_yomi</td></tr>
	<tr class='optional optional_チラシ optional_会議録・含資料'><th>配付日時</th><td>
		<input type='text' name='haifu_nen' value='$haifu_nen' size='4'>年（西暦）
		<input type='text' name='haifu_tuki' value='$haifu_tuki' size='2'>月
		<input type='text' name='haifu_bi' value='$haifu_bi' size='2'>日</td ></tr>
	<tr class='optional optional_チラシ optional_会議録・含資料'><th>配布対象（被災者等）</th><td>$haifu_taisho</td></tr>

	<!--掲示・設置場所等 -->
	<tr class='optional optional_ポスター optional_博物資料'><th>掲示・設置場所</th><td>$keiji_basho</td></tr>
	<tr class='optional optional_ポスター optional_博物資料'><th>掲示・設置場所のヨミ</th><td>$keiji_basho_yomi</td></tr>
	<tr class='optional optional_ポスター optional_博物資料'><th>掲示・配付日時</th><td>
		<input type='text' name='keiji_nen' value='$keiji_nen' size='4'>年（西暦）
		<input type='text' name='keiji_tuki' value='$keiji_tuki' size='2'>月　
		<input type='text' name='keiji_bi' value='$keiji_bi' size='2'>日
	</td></tr>
	
	<!--書誌データ-->
	<tr class='optional optional_図書 optional_新聞・雑誌 ptional_記事'><th>書誌データ</th><td>
		<input type='radio' value=0 name = 'shoshi_flag' <?php if ($shoshi_flag==0) { echo "checked"; } ?>>該当しない
		<input type='radio' value=1 name = 'shoshi_flag' <?php if ($shoshi_flag==1) { echo "checked"; } ?>>該当する
	</td></tr>

	<!--地図か地図帳か-->
	<tr class='optional optional_地図・地図帳'><th>地図か地図帳か</th><td>
		<input type='radio' value=1 name = 'chizu_kubun' <?php if ($chizu_kubun==0) { echo "checked"; } ?>>地図
		<input type='radio' value=2 name = 'chizu_kubun' <?php if ($chizu_kubun==1) { echo "checked"; } ?>>地図帳
	</td></tr>

	<!--閲覧注意-->
	<tr><th>情報の質</th><td>
		<td><?php echo output_radio('seigen', $seigen, '該当しない', '悲惨（閲覧注意）'); ?></td></tr>
EOS;
}

function  output_handover_items($items){
  $hidden_fields = array(
  	        // 基本情報整理表より
                'local_code','kanri_bango','contributor','contributor_yomi','bunrui_code',
                'bunsho_bunrui','title','creator','creator_yomi','sakusei_nen',
                'sakusei_tuki','sakusei_hi','satuei_basho_zip','satuei_basho_address',
                'satuei_basho_address_yomi','haifu_basho','haifu_basho_yomi','keyword',
                'renraku_saki_zip','renraku_saki_address','renraku_saki_tel','renraku_saki_other',
                'kenri_shori','horyu_reason','open_level','media_code',
                //
                'lot_id','id'); // lotをlot_idに統一
	foreach($hidden_fields as $f){
		$$f = output_hidden_input($f, $items[$f]);
	}
}

