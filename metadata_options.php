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
				$s .= "<option value='" .$i. "'". (($i==$default_selection) ? ' selected' : '').">".$i."</option>\n";
			} else {
				$s .= "<option value='" .$i[0]. "'". (($i[0]==$default_selection) ? ' selected' : '') .">".$i[1]."</option>\n";
			}
		}
		$s .= "</select>";
	} else {
		$v = '';
		foreach($is as $i){
			if($i==$default_selection){$v = $i;}
		}
		$s = "<input type='text' name='{$var_name}' size='40' value='{$v}' readonly='readonly'>";
	}
	return $s;
}

function output_text_input($var_name, $value, $caller){
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
	if($caller == _INPUT_){
		return <<< EOS
	<textarea name='$var_name' rows="3" cols="30">$value</textarea>
EOS;
	} else {
	return <<< EOS
	<textarea name='$var_name' rows="3" cols="30" readonly="readonly">$value</textarea>
EOS;
	}
}

function output_hidden_input($var_name, $value){
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

?>
