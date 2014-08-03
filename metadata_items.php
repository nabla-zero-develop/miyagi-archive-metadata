<?php

include_once(dirname(__FILE__) . "metadata_options.php");

function metadata_items1($items){
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
	$_for_handicapped = output_for_handicapped($items['for_handicapped']);
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
	<!--地方公共団体刊行物 --><tr><th>官公庁刊行物<br><font size='-1'>該当する場合刊行元（団体名）</font></th><td>$_gov_issue_chihou</td></tr>
    <tr><th>宮城県内地方公共団体刊行物<br><font size='-1'>宮城県内の自治体が発行元</font></th><td><td>$_gov_issue_miyagi</td></tr>
	<tr><th>視聴覚者向け資料</th><td><td>$_for_handicapped</td></tr>
EOS;
}

function output_items_last($items){
	$_contributor = output_text_input('contributor', $items['contributor']);
	$_contributor_yomi = output_text_input('contributor_yomi', $items['contributor_yomi']);
	$_iban = output_text_input('iban', $items['iban']);
	$_iban_chosha = output_text_input('iban_chosha', $items['iban_chosha']);
	$_publisher = output_text_input('publisher', $items['publisher']);
	return <<< EOS
	<tr class='kiyo_flag_option'><th>寄与者（寄贈者）</th><td>$_contributor</td></tr>	
	<tr class='kiyo_flag_option'><th>寄与者（寄贈者）のヨミ</th><td>$_contributor_yomi</td></tr>
	<tr class='iban_flag_option'><th>異版名(第x版）</th><td>$_iban</td></tr>
	<tr class='iban_flag_option'><th>異版の著者名</th><td>$_iban_chosha</td></tr>
	<tr><th>出版社・公開者</th><td>$_publisher</td></tr>

<!--サブジェクト（キーワード）-->
<tr><th class='opthissu opthissu_音声・映像 opthissu_写真 opthissu_絵画・絵はがき' >主題（キーワード）
<td><input type='text' name='subject' size='40' value='<?php echo $keyword; ?>'></td></tr>

<!--注記・要約-->
<tr><th>注記等
<td><input type='text' name='chuuki' value='<?php $chuuki; ?>' size='40'></td></tr>
<tr><th>要約
<td><input type='text' name='youyaku' value='<?php $youyaku; ?>' size='40'></td></tr>

<!--目次-->
<tr class='optional optional_図書 optional_記事 optional_雑誌・新聞 optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳 optional_チラシ optional_会議録・含資料 optional_博物資料 optional_絵画・絵はがき'><th>目次
<td><input type='text' name='mokuji' value='<?php echo $mokuji; ?>' size='40'></td></tr>

<!--作成日-->
<tr><th>作成・撮影日
<td><input type='text' name='sakusei_nen' size='4' value='<?php echo $sakusei_nen; ?>'>年（西暦）
<input type='text' name='sakusei_tuki' size='2' value='<?php echo $sakusei_tuk; ?>'>月
<input type='text' name='sakusei_bi' size='2' value='<?php echo $sakusei_bi; ?>'>日
</td></tr>

<!--情報資源採取日-->
<tr class='optional optional_オンライン資料'><th>Online資料採取日
<td><input type='text' name='online_nen' value='<?php $online_nen; ?>' size='4'>年（西暦）
<input type='text' name='online_tuki' value='<?php $online_tuki; ?>' size='2'>月
<input type='text' name='onlilne_bi' value='<?php $online_bi; ?>' size='2'>日
</td></tr>

<!--公開日・出版日-->
<tr><th>公開日
<!--澤田さん-->
<td><input type='text' name='koukai_nen' size='4' value='<?php $y; ?>'>年（西暦）
<input type='text' name='koukai_tuki' size='2' value='<?php $m; ?>'>月
<input type='text' name='koukai_hi' size='2' value='<?php $d; ?>'>日
</td></tr>

<!--言語-->
<tr><th>言語
<td><select name='language'>
            <option value='JPN' <?php if ($language=="JPN") { echo "selected"; } ?>>日本語</option>
            <option value='ENG' <?php if ($language=="ENG") { echo "selected"; } ?>>英語</option>
            <option value='CHI' <?php if ($language=="CHI") { echo "selected"; } ?>>中国語</option>
            <option value='KOR' <?php if ($language=="KOR") { echo "selected"; } ?>>韓国語</option>
            <option value='GER' <?php if ($language=="GER") { echo "selected"; } ?>>ドイツ語</option>
            <option value='FRE' <?php if ($language=="FRE") { echo "selected"; } ?>>フランス語</option>
            <option value='SPA' <?php if ($language=="SPA") { echo "selected"; } ?>>スペイン語</option>
            <option value='ITA' <?php if ($language=="ITA") { echo "selected"; } ?>>イタリア語</option>
            <option value='RUS' <?php if ($language=="RUS") { echo "selected"; } ?>>ロシア語</option>
            <option value='POR' <?php if ($language=="POR") { echo "selected"; } ?>>ポルトガル語</option>
            <option value='TGL' <?php if ($language=="TGL") { echo "selected"; } ?>>タガログ語</option>
    </select>
</td></tr>

<!--引用資料-->
<tr class='inyou_flag_option'><th>～の一部分である
<td><input type='text' name='is_bubun' value='<?php echo $is_bubun; ?>' size='40'></td></tr>
<tr class='inyou_flag_option'><th>親URIへの参照
<td><input type='text' name='oya_uri' value='<?php echo $ioya_uri; ?>' size='40'></td></tr>
<tr class='inyou_flag_option'><th>参照する情報資源の名称
<td><input type='text' name='shigen_mei' value='<?php echo $shigen_mei; ?>' size='40'></td></tr>
<tr class='inyou_flag_option'><th>～を一部分として持つ
<td><input type='text' name='has_bubun' value='<?php echo $has_bubun; ?>' size='40'></td></tr>
<tr class='inyou_flag_option'><th>子URIへの参照
<td><input type='text' name='ko_uri' value='<?php echo $ko_uri; ?>' size='40'></td></tr>

<!--情報資源が対象とする場所-->
<tr><th>情報資源が対象とする場所(URI)<br>
<input type='button' value='地図から取得'>
<td><input type='text' name='taisho_basho_uri' value='<?php echo $taisho_basho_uri; ?>' size='40'></td></tr>
<tr><th>情報資源が対象とする場所（県名）
<td><input type='text' name='taisho_basho_ken' value='<?php echo $taisho_basho_keni; ?>' size='40'></td></tr>
<tr><th>情報資源が対象とする場所（市町村）
<td><input type='text' name='taisho_basho_shi' value='<?php echo $taisho_basho_shi; ?>' size='40'></td></tr>
<tr><th>情報資源が対象とする場所（街路番地）
<td><input type='text' name='taisho_basho_banchi' value='<?php echo $taisho_basho_banchii; ?>' size='40'></td></tr>
<tr><th>情報資源が対象とする場所（緯度）
<td><input type='text' name='taisho_basho_ido' value='<?php echo $taisho_basho_id; ?>' size='40'></td></tr>
<tr><th>情報資源が対象とする場所（経度）
<td><input type='text' name='taisho_basho_keido' value='<?php echo $taisho_basho_keido; ?>' size='40'></td></tr>

<!--撮影場所-->
<tr class='optional optional_音声・映像 optional_地図・地図帳 optional_写真 optional_語り optional_絵画・絵はがき' ><th>撮影場所（緯度）<br>
<input type='button' value='地図から取得'>
<td><input type='text' name='satusei_ido' value='<?php echo $satusei_ido; ?>' size='40'></td></tr>
<tr class='optional optional_音声・映像 optional_地図・地図帳 optional_写真 optional_語り optional_絵画・絵はがき'><th>撮影場所（経度）
<td><input type='text' name='satuei_keido' value='<?php echo $satuei_keido; ?>' size='40'></td></tr>
<tr class='optional optional_音声・映像 optional_地図・地図帳 optional_写真 optional_語り optional_絵画・絵はがき'><th>撮影場所（県名）
<!--とりあえず、撮影場所の住所を県のところに表示させておく
    基本情報整理表には、複数が入力されている場合あり-->
<td><input type='text' name='satuei_ken' size='40' value='<?php echo $satuei_basho_address; ?>'></td></tr>
<tr class='optional optional_音声・映像 optional_地図・地図帳 optional_写真 optional_語り optional_絵画・絵はがき'><th>撮影場所（市町村）
<td><input type='text' name='satuei_shi' value='<?php echo $satuei_shi; ?>' size='40'></td></tr>
<tr class='optional optional_音声・映像 optional_地図・地図帳 optional_写真 optional_語り optional_絵画・絵はがき'><th>撮影場所（街路番地）
<td><input type='text' name='satuei_banchi' value='<?php echo $satuei_banch; ?>' size='40'></td></tr>

<!--刊行頻度・状態・巻次（雑誌の場合のみ）-->
<tr class='optional optional_雑誌・新聞'><th>刊行頻度
<td><input type='text' name='kanko_hindo' value ='<?php echo $kanko_hindo; ?>' size='40'></td></tr>
<tr class='series_flag_option'><th>刊行状態
<td><select name='kanko_status'>
	        <option value='u' <?php if ($kanko_status=="c") { echo "selected"; } ?>>不明</option>
            <option value='c' <?php if ($kanko_status=="d") { echo "selected"; } ?>>刊行中</option>
            <option value='d' <?php if ($kanko_status=="u") { echo "selected"; } ?>>廃刊</option>
    </select>
</td></tr>
<tr class='optional optional_雑誌・新聞'><th>刊行巻次
<td><input type='text' name='kanko_kanji' value='<?php echo $kanko_kanji; ?>'  size='40'></td></tr>
	
<!--博士論文-->
<tr class="doctor_flag_option"><th>学位<br>
<td><input type='text' name='doctor' value='<?php echo $doctor; ?>' size='40'></td></tr>
<tr class="doctor_flag_option"><th>報告番号
<td><input type='text' name='doctor_bango' value='<?php echo $doctor_bango; ?>' size='40'></td></tr> 
<tr class="doctor_flag_option"><th>授与年月日
<td><input type='text' name='doctor_nen' value='<?php echo $doctor_nen; ?>' size='4'>年（西暦）
<input type='text' name='doctor_tuki' value='<?php echo $doctor_tuki; ?>' size='2'>月　
<input type='text' name='doctor_bi' value='<?php echo $doctor_bi; ?>' size='2'>日
</td></tr>
<tr class="doctor_flag_option"><th>授与大学
<td><input type='text' name='doctor_daigaku' value='<?php echo $doctor_daigaku; ?>' size='40'></td></tr>
<tr class="doctor_flag_option"><th>授与大学のヨミ
<td><input type='text' name='doctor_daigaku_yomi' value='<?php echo $doctor_daigaku_yomi; ?>' size='40'></td></tr>
<tr class="doctor_flag_option"><th></th><td></tr>

<!--通巻番号等-->
<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載通号
<td><input type='text' name='keisai_go1' value='<?php echo $keisai_go1; ?>' size='40'></td></tr>
<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載号
<td><input type='text' name='keisai_go2'  value='<?php echo $keisai_go2; ?>' size='40'></td></tr>
<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載誌名
<td><input type='text' name='keisa_shimei'  value='<?php echo $keisa_shimei; ?>' size='40'></td></tr>
<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載巻（論文の場合）
<td><input type='text' name='keisai_kan'  value='<?php echo $keisai_kan; ?>' size='40'></td></tr>
<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載ページ
<td><input type='text' name='keisai_page'  value='<?php echo $keisai_page; ?>' size='40'></td></tr>


<!--アクセス制御-->
<tr><th class='hissu'>アクセス制御
<td><select name='open_level'>
	        <option value='0' <?php if ($open_level=="0") { echo "selected"; } ?>>非公開</option>
            <option value='1' <?php if ($open_level=="1") { echo "selected"; } ?>>公開</option>
            <option value='2' <?php if ($open_level=="2") { echo "selected"; } ?>>限定公開</option>
            <option value='3' <?php if ($open_level=="3") { echo "selected"; } ?>>公開保留</option>
    </select>
</td></tr>

<tr class='license_flag_option'><th>ライセンス情報
<td><input type='text' name='license_info' value='<?php echo $license_info; ?>' size='40'></td></tr>
<tr  class='license_flag_option'><th>URIへの参照
<td><input type='text' name='license_uri' value='<?php echo $license_uri; ?>' size='40'></td></tr>
<tr class='license_flag_option'><th>ライセンス保有者名
<td><input type='text' name='license_holder' value='<?php echo $license_holder; ?>' size='40'></td></tr>
<tr  class='license_flag_option'><th>権利・利用条件に関する注記
<td><input type='text' name='license_chuki' value='<?php echo $license_chuki; ?>' size='40'></td></tr>

<!--資料形態-->
<tr class='optional optional_図書 optional_記事 optional_新聞・雑誌 optional_文書・楽譜 optional_地図・地図帳 optional_ポスター optional_チラシ optional_会議録・含資料 optional_絵画・絵はがき'><th>資料形態（大活字等)</th><td>
<select name='shiryo_keitai'>
		<option value='' <?php if ($shiryo_keitai=="0") { echo "selected"; } ?>> 該当しない</option>
		<option value='03' <?php if ($shiryo_keitai=="03") { echo "selected"; } ?>> 大活字</option>
	    <option value='04' <?php if ($shiryo_keitai=="04") { echo "selected"; } ?>> 文庫本</option>
		<option value='05' <?php if ($shiryo_keitai=="05") { echo "selected"; } ?>> 新書</option>
		<option value='85' <?php if ($shiryo_keitai=="85") { echo "selected"; } ?>> 絵本</option>
		<option value='06' <?php if ($shiryo_keitai=="06") { echo "selected"; } ?>> 大型絵本</option>
		<option value='07' <?php if ($shiryo_keitai=="07") { echo "selected"; } ?>> 紙芝居</option>
		<option value='08' <?php if ($shiryo_keitai=="08") { echo "selected"; } ?>> 紙芝居舞台</option>
		<option value='09' <?php if ($shiryo_keitai=="09") { echo "selected"; } ?>> かるた</option>
		<option value='10' <?php if ($shiryo_keitai=="10") { echo "selected"; } ?>> 絵葉書</option>
		<option value='11' <?php if ($shiryo_keitai=="11") { echo "selected"; } ?>> ちりめん本</option>
		<option value='12' <?php if ($shiryo_keitai=="12") { echo "selected"; } ?>> 大型紙芝居</option>
 </select>
</td></tr>



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



	<!--話者-->
<tr class='optional optional_語り'><th>話者
<td><input type='text' name='teller' value='<?php echo $teller; ?>' size='40'></td></tr>
<tr class='optional optional_語り'><th>話者のヨミ
<td><input type='text' name='teller_yomi' value='<?php echo $teller_yomi; ?>' size='40'></td></tr>

<!--配布場所とヨミ、配付日時、配付対象-->
<tr class='optional optional_チラシ optional_会議録・含資料'><th>配布場所<br>
<td><input type='text' name='haifu_basho' size='40' value='<?php echo $haifu_basho; ?>'></td></tr>
<!--澤田さん-->
<?php
   $haifu_basho_yomi = yomi($haifu_basho_yomi, mecab($haifu_basho));
?>
<tr><th>配布場所のヨミ<br> 
<td><input type='text' name='haifu_basho_yomi' size='40' value='<?php echo $haifu_basho_yomi; ?>'></td></tr>
<tr class='optional optional_チラシ optional_会議録・含資料'><th>配付日時
<td><input type='text' name='haifu_nen' value='<?php echo $haifu_nen; ?>' size='4'>年（西暦）
<input type='text' name='haifu_tuki' value='<?php echo $haifu_tuki; ?>' size='2'>月
<input type='text' name='haifu_bi' value='<?php echo $haifu_bi; ?>' size='2'>日
</td ></tr>
<tr class='optional optional_チラシ optional_会議録・含資料'><th>配布対象（被災者等）
<td><input type='text' name='haifu_taisho' value='<?php echo $haifu_taisho; ?>' size='40'></td></tr>

<!--掲示・設置場所等 -->
<tr class='optional optional_ポスター optional_博物資料'><th>掲示・設置場所<br>
<td><input type='text' name='keiji_basho' value='<?php echo $keiji_basho; ?>' size='40'></td></tr>
<tr class='optional optional_ポスター optional_博物資料'><th>掲示・設置場所のヨミ<br>
<td><input type='text' name='keiji_basho_yomi' value='<?php echo $keiji_basho_yomi; ?>' size='40'></td></tr>
<tr class='optional optional_ポスター optional_博物資料'><th>掲示・配付日時
<td><input type='text' name='keiji_nen' value='<?php echo $keiji_nen; ?>' size='4'>年（西暦）
<input type='text' name='keiji_tuki' value='<?php echo $keiji_tuki; ?>' size='2'>月　
<input type='text' name='keiji_bi' value='<?php echo $keiji_bi; ?>' size='2'>日
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
}