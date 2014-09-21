<?php

include_once(dirname(__FILE__) . "/metadata_utils.php");

function output_radio($name, $flag, $first, $second, $caller, $default_flag=0){
	$checked_0 = ($default_flag <> 1) ? 'checked' : '';
	$checked_1 = ($default_flag == 1) ? 'checked' : '';
	if($caller == _INPUT_){
		$d = '';
	} else {
		$d = ' disabled="disabled" ';
	}
	return <<< EOS
	<label><input class='optctrl' type='radio' value=0 name='$name' $checked_0 $d>{$first}</label>　
	<label><input class='optctrl' type='radio' value=1 name='$name' $checked_1 $d>{$second}</label>
EOS;
}

function selection($name, $is, $default_selection, $caller, $type=1){
	if($caller == _INPUT_){
		$s = "<select name = '$name'>";
		foreach($is as $i){
			if($type == 1){
				$i = htmlspecialchars($i);
				$s .= "<option value='" .$i. "'". (($i==$default_selection) ? ' selected' : '').">".$i."</option>\n";
			} else {
				$i[0] = htmlspecialchars($i[0]);
				$s .= "<option value='" .$i[0]. "'". (($i[0]==$default_selection) ? ' selected' : '') .">".$i[1]."</option>\n";
			}
		}
		$s .= "</select>";
	} else {
		$v = '';
		foreach($is as $i){
			if($i==$default_selection){$v = $i;}
		}
		$v = htmlspecialchars($v);
		$s = "<input type='text' name='{$var_name}' size='40' value='{$v}' readonly='readonly'>";
	}
	return $s;
}

function output_text_input($var_name, $value, $caller){
	$value = htmlspecialchars($value);
	if($caller == _INPUT_){
		return <<< EOS
	<input type='text' name='$var_name' size='40' value='$value'>
EOS;
	} else {
	return <<< EOS
	<input type='text' name='$var_name' size='40' value='$value' readonly="readonly">
EOS;
	}
}

function output_text_area($var_name, $value, $caller){
	$value = htmlspecialchars($value);
	if($caller == _INPUT_){
		return <<< EOS
	<textarea name='$var_name' rows="3" cols="33">$value</textarea>
EOS;
	} else {
	return <<< EOS
	<textarea name='$var_name' rows="3" cols="33" readonly="readonly">$value</textarea>
EOS;
	}
}

function output_hidden_input($var_name, $value){
	$value = htmlspecialchars($value);
	return <<< EOS
	<input type='hidden' name='$var_name' size='40' value='$value'>
EOS;
}

function output_yomi_button($field, $yomi_field, $init_value){
	return <<< EOS
	<button id="text-button-{$field}" onClick="yomi('{$field}', '{$yomi_field}', '{$init_value}'); return false;">候補</button>
EOS;
}

function output_md_type_selection($md_type, $caller){
	$md_types = array(array('',''), array('図書','a:図書'), array('記事','b:記事'), array('雑誌・新聞','c:雑誌・新聞'),
					array('音声・映像','d:音声・映像'),array('文書・楽譜','e:文書・楽譜'),
					array('地図・地図帳','f:地図・地図帳'),array('ポスター','g:ポスター'),array('写真','h:写真'),
					array('チラシ','i:チラシ'),array('会議録・含資料','j:会議録・含資料'),
					array('博物資料','k:博物資料'),array('オンライン資料','l:オンライン資料'),array('語り','m:語り'),
					array('絵画・絵はがき','n:絵画・絵はがき'),
					array('プログラム（スマホアプリ・ゲーム等）','o:プログラム（スマホアプリ・ゲーム等）'));
	return selection('md_type', $md_types, $md_type, $caller, 2);
}

function output_gov_issue_selection($gov_issue, $caller){
	$gov_types = array(array("","該当しない"),array('AA0',"衆議院"),array('AB0',"参議院"),array('AC0',"国立国会図書館"),
	        array('AD0',"裁判官弾劾裁判所"),array('AE0',"裁判官訴追委員会"),array('BA0',"会計検査院"),array('CA0',"内閣"),array('CB0',"安全保障会議"),
                array('CC0',"人事院"),array('DA0',"内閣府"),array('DB0',"宮内庁"),array('DC0',"国家公安委員会"),array('DD0',"警察庁"),
                array('DE0',"防衛省"),array('DF0',"防衛施設庁"),array('DG0',"金融庁"),array('EA0',"総務省"),array('EB0',"公正取引委員会"),
                array('EC0',"公害等調整委員会"),array('ED0',"郵政事業庁"),array('ED1',"地方支分部局"),array('EE0',"消防庁"),array('FA0',"法務省"),
                array('FA1',"地方支分部局"),array('FB0',"司法試験管理委員会"),array('FC0',"公安審査委員会"),
                array('FD0',"公安調査庁"),array('FE0',"検察庁"),array('GA0',"外務省"),array('HA0',"財務省"),array('HA1',"地方支分部局"),
                array('HB0',"国税庁"),array('KA0',"文部科学省"),array('KB0',"文化庁"),array('LA0',"厚生労働省"),array('LA1',"地方支分部局"),
                array('LB0',"中央労働委員会"),array('LC0',"社会保険庁"),array('MA0',"農林水産省"),array('MA1',"地方支分部局"),
                array('MB0',"食糧庁"),array('MC0',"林野庁"),array('MD0',"水産庁"),array('NA0',"経済産業省"),array('NA1',"地方支分部局"),
                array('NB0',"資源エネルギー庁"),array('NC0',"特許庁"),array('ND0',"中小企業庁"),array('PA0',"国土交通省"),
                array('PA1',"地方支分部局"),array('PB0',"船員労働委員会"),array('PC0',"気象庁"),array('PD0',"海上保安庁"),array('PE0',"海難審判庁"),
                array('RA0',"環境省"),array('SA0',"最高裁判所"),array('SB0',"高等裁判所"),array('SC0',"地方裁判所"),array('SD0',"家庭裁判所"),
                array('TA0',"公団"),array('TB0',"事業団"),array('TC0',"公庫"),array('TD0',"基金"),array('TE0',"銀行"),
                array('TF0',"その他"),array('WA0',"国立大学等"),array('WB0',"国立大学共同利用機関"));
	return selection('gov_issue', $gov_types, $gov_issue, $caller, 2);
}

function output_gov_issue_miyagi_selection($gov_issue_miyagi, $caller){
	$gov_types_miyagi = array(array('', '該当しない'),array('4100', '仙台市'),array('4202', '石巻市'),array('4203', '塩竈市'),
                array('4204', '古川市'),array('4205', '気仙沼市'),array('4206', '白石市'),array('4207', '名取市'),
                array('4208', '角田市'),array('4209', '多賀城市'),array('4210', '泉市'),array('4211', '岩沼市'),
                array('4212', '登米市'),array('4213', '栗原市'),array('4214', '東松島市'),array('4215', '大崎市'),array('4301', '蔵王町'),
                array('4302', '七ヶ宿町'),array('4321', '大河原町'),array('4322', '村田町'),array('4323', '柴田町'),
                array('4324', '川崎町'),array('4341', '丸森町'),array('4361', '亘理町'),array('4362', '山元町'),
                array('4381', '岩沼町'),array('4382', '秋保町'),array('4401', '松島町'),array('4402', '多賀城町'),
                array('4403', '泉町'),array('4404', '七ヶ浜町'),array('4405', '宮城町'),array('4406', '利府町'),
                array('4421', '大和町'),array('4422', '大郷町'),array('4423', '富谷町'),array('4424', '大衡村'),
                array('4441', '中新田町'),array('4442', '小野田町'),array('4443', '宮崎町'),array('4444', '色麻町'),
                array('4444', '色麻村'),array('4445', '加美町'),array('4461', '松山町'),array('4462', '三本木町'),
                array('4463', '鹿島台町'),array('4481', '岩出山町'),array('4482', '鳴子町'),array('4501', '涌谷町'),
                array('4502', '田尻町'),array('4503', '小牛田町'),array('4504', '南郷町'),array('4505', '美里町'),
                array('4521', '築館町'),array('4522', '若柳町'),array('4523', '栗駒町'),array('4524', '高清水町'),
                array('4525', '一迫町'),array('4526', '瀬峰町'),array('4527', '鶯沢町'),array('4528', '金成町'),
                array('4529', '志波姫町'),array('4530', '花山村'),array('4541', '迫町'),array('4542', '登米町'),
                array('4543', '東和町'),array('4544', '中田町'),array('4545', '豊里町'),array('4546', '米山町'),
                array('4547', '石越町'),array('4548', '南方町'),array('4561', '河北町'),array('4562', '矢本町'),
                array('4563', '雄勝町'),array('4564', '河南町'),array('4565', '桃生町'),array('4566', '鳴瀬町'),
                array('4567', '北上町'),array('4581', '女川町'),array('4582', '牡鹿町'),array('4601', '志津川町'),
                array('4602', '津山町'),array('4603', '本吉町'),array('4604', '唐桑町'),array('4605', '歌津町'),array('4606', '南三陸町'));
	return selection('gov_issue_miyagi', $gov_types_miyagi, $gov_issue_miyagi, $caller, 2);
}

function output_for_handicapped_selection($for_handicapped, $caller){
	$for_handicapped_types = array(array('', '該当しない'), array('Braille', '点字'), array('DAISY','DAISY'),
	array('AudioBookInSoundD', '録音図書（DVD・CD）'), array('AudioBookInSoundT', '録音図書（カセットテープ）'));
	return selection('for_handicapped', $for_handicapped_types, $for_handicapped, $caller, 2);
}

function output_shiryo_keitai_selection($shiryo_keitai, $caller){
	$shiryo = array(array('0','該当しない'), array('03', '大活字'), array('04', '文庫本'), array('05', '新書'), array('85', '絵本'),
		array('06', '大型絵本'),	array('07', '紙芝居'), array('08', '紙芝居舞台'), array('09', 'かるた'), array('10', '絵葉書'),
		array('11', 'ちりめん本'), array('12', '大型紙芝居'));
	return selection('shiryo_keitai', $shiryo, $shiryo_keitai, $caller, 2);
}

function output_language_selection($language, $caller){
	$languages = array(array('JPN','日本語'),array('ENG','英語'),array('CHI','中国語'),
		array('KOR','韓国語'),array('GER','ドイツ語'),array('FRE','フランス語'),
		array('SPA','スペイン語'),array('ITA','イタリア語'),array('RUS','ロシア語'),
		array('POR','ポルトガル語'), array('TGL','タガログ語'));
	return selection('language', $languages, $language, $caller, 2);
}

function output_original_shiryo_keitai_selection($original_shiryo_keitai, $caller){
	$shiryos = array(array('','該当しない'),array('31','ＣＤ'),array('32','カセット'),array('33','レコード'),array('34','リールテープ'),
            array('35','ＭＤ'),array('36','録音図書'),array('39','録音その他'),array('41','ビデオテープ'),
            array('42','ＬＤ'),array('43','ＤＶＤ'),array('44','ＥＬＩＢ'),array('45','ブルーレイディスク'),
            array('46','映像フィルム'),array('49','映像その他'),array('51','磁気テープ'),array('52','ＦＤ'),
            array('53','ＣＤ－ＲＯＭ'),array('54','ＭＯ'),array('59','機械その他'),array('61','ネガ・ポジ'),
            array('62','プリント'),array('63','スライド'),array('69','写真その他'),array('71','楽譜'),
            array('81','マイクロＬ'),array('82','マイクロＣ'),array('91','別置解説書'),array('92','その他ＡＶ'));
	return selection('original_shiryo_keitai', $shiryos, $original_shiryo_keitai, $caller, 2);
}

function output_kanko_status_selection($kanko_status, $caller){
	$status = array(array('c','不明'),array('d','刊行中'),array('u','廃刊'));
	return selection('kanko_status', $status, $kanko_status, $caller, 2);
}

function output_open_level_selection($open_level, $caller){
	$levels = array(array('-1','判断保留'),/*array('0','非公開'),*/array('1','公開'),array('2','限定公開'),array('3','公開保留'));
	if($open_level === 0||$open_level === '0')$open_level = 3;
	return selection('open_level', $levels, $open_level, $caller, 2).(is_numeric($open_level)?'':'基本情報整理表未入力');
}

function output_keywords_tab($keywords, $caller){
	$tabs = array('資料種別' => array('新聞','地図','楽譜'),
					'現象' => array('津波', '地震', '火災', '液状化', '浸水'),
					'被災' => array('がれき', '建物', '道路', '鉄道', '船', '車', '飛行機', 'まちなみ', '地盤', '海岸', 
									'防潮堤', '水門', '橋', '漁業施設', '石碑', '墓地', '風評被害'), 
					'組織' => array('府省庁', '都道府県', '市区町村', '自衛隊', '警察', '海上保安庁', '海外救援隊',
									'消防', '赤十字', '社会福祉協議会', '医療', 'NPO', '第3者委員会'), 
					'原子力' => array('放射能'), 
					'避難・救助' => array('避難', '救助', '捜索', '行方不明', 'ご遺体'), 
					'災害対策' => array('災害情報', '被災情報', '広報', '津波避難ビル'), 
					'震災復興' => array('がれき除去', '工事', 'ボランティア活動', '再建', 'かさ上げ', '区画整理', '防災集団移転'),
					'救援・支援' => array( '避難所', '避難者', '物資', '心の支援', '起業', '思い出の品'),
					'くらし' => array( '仮設住宅', '風呂', '給水', '悪臭', '食事', 'トイレ', '住宅再建', '移転'), 
					'お金' => array( '融資', '補助金', '義援金'),
					'保健福祉' => array( '医療', '高齢者', '障がい者', '介護', '乳幼児', '健康管理'), 
					'工業' => array( '電気', '燃料', '工場'), 
					'商業' => array( '店舗', '復興商店街'), 
					'農業・林業' => array( '水田', '畑', '農業施設', '畜産', '森林'),
					'水産業' => array( '漁船', '養殖', '漁具', '漁業施設'), 
					'上下水道' => array( '水道', '下水道', '処理施設'), 
					'通信' => array( '防災無線', '通信設備', '電話', 'インターネット', '放送'),
					'教育' => array( '保育園', '幼稚園', '小学校', '中学校', '高等学校', '大学', '各種学校', '教育委員会', '社会教育'),
					'文化財' => array( '寺', '神社', '仏像', '美術品', '文化財'), 
					'観光' => array( 'ホテル', '旅館', '民宿', 'ツアー'));

	if($caller == _INPUT_){
		$readonly = 'readonly';
	}else{
		$readonly = '';
	}
	//タブ
	$lis = '';
	foreach($tabs as $tab => $chks){
		$tab = htmlspecialchars($tab);
		$lis .= "<li class='TabbedPanelsTab' tabindex='$tabidx'>$tab</li>";
	}
	$tabGroup = "<ul class='TabbedPanelsTabGroup'>$lis</ul>";
	//タブの中身
	$contents = '';
	foreach($tabs as $tab => $chks){
		$tab = htmlspecialchars($tab);
		$inputs = '';
		foreach($chks as $chk){
			$chk = htmlspecialchars($chk,ENT_QUOTES);
			$inputs .= "<label style='word-break:keep-all;'><input type='checkbox' class='keyword' value='$chk' $readonly>$chk</label> ";
		}
		$contents .= "<div class='TabbedPanelsContent'>$inputs</div>";
	}
	$contentsGroup = "<div class='TabbedPanelsContentGroup'>$contents</div>";

	$keywords = htmlspecialchars($keywords,ENT_QUOTES);
	$str .= <<<__JS__
<div id='KeywordTabbedPanel' class='TabbedPanels'> $tabGroup $contentsGroup</div>

<script type="text/javascript">
<!--
var KeywordTabbedPanel = new Spry.Widget.TabbedPanels("KeywordTabbedPanel");
$(document).ready(function(){
	//hiddenフィールドからチェックボックス・自由入力欄へ
	var keywords = $('#md_keyword').val().split(';');
	var keywordOthers = [];
	for(var i=0; i<keywords.length; i++){
		if($('input[class=keyword][value='+keywords[i]+']').length){
			$('input[class=keyword][value='+keywords[i]+']').attr('checked','checked');
		}else{
			keywordOthers.push(keywords[i]);
		}
	}
	$('input[class=keyword_other]').val(keywordOthers.join(';'));
	$('input.keyword').click(keywordClick);
	$('input[class=keyword_other]').change(keywordClick);
	keywordClick();
});
//選択済みキーワードを表示
function keywordClick(){
	var str = 'なし';
	var keywords = [];
	$('input[class=keyword]').filter(':checked').each(function(){
		keywords.push($(this).val());
	});
	if(keywords.length > 0){
		str = keywords.join(';');
	}
	$('#keyword_display').html(str);
	var keywordOthers = $('input[class=keyword_other]').val();
	if(keywordOthers)keywords.push(keywordOthers);
	$('#md_keyword').val(keywords.join(';'));
}

//-->
</script>
	選択済みキーワード：<span id='keyword_display'>なし</span><br>
    <font size="2">自由入力キーワード</font><font color="#000000" size="2">（複数入力可)</font><br>
      &nbsp;<input type="text" class='keyword_other' size="30"  $readonly>&nbsp;<br>
      <input type="hidden" name="keyword" id="md_keyword" value="$keywords">

__JS__;
	return $str;
}
?>
