<?php
	
//確認、登録へ
include_once(dirname(__FILE__) . "/metadata_utils.php");
include_once(dirname(__FILE__) . "/NDL/NDL.php");
include_once(dirname(__FILE__) . "/NDL/utils.php");
$lot_id = intval($_GET['lot']);
$id = isset($_GET['id'])?intval($_GET['id']):1;
$data = get_data_from_db($lot_id, $id);
?>

<html>
<script type="text/javascript" src="js/jquery/jquery-1.8.0.min.js"></script>
<style>
#imageDiv{
	width: 00px;
	float: left;
	text-align: center;
}
#image{
	max-width: 0%;
	max-height: 0%;
	display: block;
}
#imageWrap{
	width: 0px;
	height: 0px;
}
th{
	background-color: #9292FF;
	border: 2px solid #ffffff;
}
td{
	border: 2px solid #ffffff;
	background-color: #CEE3F6;
}
table{
	border-collapse: collapse;
}
.imeDisable{
	ime-mode: disabled;
}
th.hissu{
	background-color: #ff0000;
}
</style>
<script>
var images = [
<?php
	$ff = array();
	foreach($files as $file)$ff[] = "'$file'";
	echo implode(',',$ff);
?>
];
var degree = 0;
function rotate(deg){
	degree = deg;
	$('#image').css('transform','rotate('+deg+'deg)')
		.css('width','').css('height','').css('left',0).css('right',0);
	if(deg == 0){
		$('#image')
			.css('max-width',$('#imageWrap').css('width'))
			.css('max-height',$('#imageWrap').css('height'));
	}else{
		$('#image')
			.css('max-width',$('#imageWrap').css('height'))
			.css('max-height',$('#imageWrap').css('width'));
	}
}
function chgImage(idx){
	rotate(0);
	$('#image').attr('src','');
	$('#image').attr('src',images[idx]);
	$('#filename').html(''+(idx+1)+'/'+images.length);
}
function prevImage(){
	var src = $('#image').attr('src');
	var i = 0;
	for(i=0;i<images.length;i++){
		if(src == images[i])break;
	}
	chgImage(i==0?0:i-1);
}
function nextImage(){
	var src = $('#image').attr('src');
	var i = 0;
	for(i=0;i<images.length;i++){
		if(src == images[i])break;
	}
	chgImage(i==images.length-1?images.length-1:i+1);
}
function lastImage(){
	chgImage(images.length-1);
}
var zoom = false;
$(document).ready(function(){
	chgImage(0);
	$('#image').click(function(e){
		if(zoom){
			rotate(degree);
			zoom = false;
		}else{
			//alert(''+(e.pageX-$(this).offset().left)+','+(e.pageY-$(this).offset().top));
			zoom = true;
		}
	});
});

//資料種別による項目変更
//trタグを<tr class='optional optional_図書'>とすると図書が選択されたときに表示される
//<tr class='optional optional_type1 optional_type2'>等とすると複数の選択肢(type1およびtype2)で表示できる
$(document).ready(function(){
	$('select[name=md_type]').change(showOptional);
	showOptional();
});
function showOptional(){
	var type = $('select[name=md_type]').val();
	$('.optional').css('display','none');
	$('.optional_'+type).css('display','');
	$('.opthissu').removeClass('hissu');
	$('.opthissu_'+type).addClass('hissu');
}
//class optctrl
$(document).ready(function(){
	$('input.optctrl').change(function(){showOptCtrl($(this).attr('name'))});
	$('input.optctrl').each(function(){showOptCtrl($(this).attr('name'))});
});
function showOptCtrl(name){
	if($('input[name='+name+']:checked').val()==1){
		$('.'+name+'_option').css('display','');
	}else{
		$('.'+name+'_option').css('display','none');
	}
}


</script>
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
<h4>ロットNo.<?php printf("%03d",$lot_id); ?></h4>
<?php echo "$id/$num_in_lot"; ?><br>
<!--<form method='post' action='write.php'> write.phpをmetadata6.phpに変更--> 

<form method='post' action='metadata6.php'>

<input type='hidden' name='lot' value='<?php echo $lot_id ?>'>
<input type='hidden' name='id' value='<?php echo $id ?>'>


<?php

function yomi_check($yomi){
	if($yomi <> "" && $yomi <> NULL){
		return $yomi;
	}
	return "";
}

//MeCabによる読みの取得
function mecab($item){
	return yomi_check(NDL::mecab_yomi($item));
}

// 国会図書館による著者名の読みの取得(文字列を返す)
function ndl_creator_yomi($item){
	return yomi_check(NDL::ndl_creator_yomi($item));
}

// 国会図書館による書名からの情報取得(文字列を返す)
$book_info = '';
function ndl_title_info($item){
	return NDL::ndl_title_info($item, 'array');
}

function get_info($key){
	global $book_info;
	//echo $book_info[0][$key]."\n";
	if(isset($book_info[0][$key])){
		$r = $book_info[0][$key];
	}	else {
		$r = '';
	}
	return $r;
}

function yomi($s, $s2){
	return (($s <> '') && ($s <> '@')  && ($s <> '＠')) ? $s : $s2;
}

//以下の変数は、「基本情報整理表の県版・市町村版との違い、対応変数一覧.xlsxの順
//基本情報整理表データの受け取り
$local_code                = $_REQUEST['local_code'];
$shubetu                   = $_REQUEST['shubetu'];
$kanri_bango               = $_REQUEST['kanri_bango'];
$shiryou_jyuryoubi         = $_REQUEST['shiryou_jyuryoubi'];
$contributor               = $_REQUEST['contributor'];
$contributor_yomi          = $_REQUEST['contributor_yomi'];
$bunrui_code               = $_REQUEST['bunrui_code'];
$bunsho_bunrui             = $_REQUEST['bunsho_bunrui'];
$title                     = $_REQUEST['title'];
$creator                   = $_REQUEST['creator'];
$creator_yomi              = $_REQUEST['creator_yomi'];
$sakusei_nen               = $_REQUEST['sakusei_nen'];
$sakusei_tuki              = $_REQUEST['sakusei_tuki'];
$sakusei_bi                = $_REQUEST['sakusei_bi'];
$satuei_basho_zip          = $_REQUEST['satuei_basho_zip']; //「撮影」と「作成」は違うので注意！
$satuei_basho_address      = $_REQUEST['satuei_basho_address'];
$satuei_basho_address_yomi = $_REQUEST['satuei_basho_address_yomi'];
$haifu_basho               = $_REQUEST['haifu_basho'];
$haifu_basho_yomi          = $_REQUEST['haifu_basho_yomi'];
$keyword                   = $_REQUEST['keyword'];
$renraku_saki_zip          = $_REQUEST['renraku_saki_zip'];
$renraku_saki_address      = $_REQUEST['renraku_saki_address'];
$renraku_saki_tel          = $_REQUEST['renraku_saki_tel'];
$renraku_saki_other        = $_REQUEST['renraku_saki_other'];
$kenri_shori               = $_REQUEST['kenri_shori'];
$open_level                = $_REQUEST['open_level'];
$horyu_reason              = $_REQUEST['horyu_reason'];
$media_code                = $_REQUEST['media_code'];
//基本情報整理表ここまで

//metadata45より受け取り
$md_type     = $_REQUEST['md_type']; //資料種別
$series_flag = $_REQUEST['series_flag']; //シリーズ資料（継続資料）か否か
$ihan_flag   = $_REQUEST['ihan_flag']; //異版の有無
$betu_title_flag = $_REQUEST['betu_title_flag']; //別タイトルが存在するか否か
$kiyo_flag = $_REQUEST['kiyo_flag']; //寄与者（寄贈者）がいるかいないか
$inyou_flag= $_REQUEST['inyou_flag']; //引用資料か否か
$doctor_flag=$_REQUEST['doctor_flag']; //博士論文か否か
$license_flag=$_REQUEST['license_flag']; //ライセンス設定があるかないか
$gov_issue =$_REQUEST['gov_issue']; //政府刊行物か否か
$gov_issue2=$_REQUEST['gov_issue2']; //官公庁刊行物
$gov_issue_chihou=$_REQUEST['gov_issue_chihou']; //地方公共団体資料か否か
$gov_issue_miyagi=$_REQUEST['gov_issue_miyagi']; //宮城県内市町村資料か否か
$for_handicapped =$_REQUEST['for_handicapped']; //障害者向け資料か否か
$origina_shiryo_keitai = $_REQUEST['origina_shiryo_keitai']; //オリジナル資料の形態
$rippou_flag = $_REQUEST['rippou_flag']; //立法資料か否か

$betsu_title       = $_REQUEST['betsu_title'];
$betsu_title_yomi  = $_REQUEST['betsu_title_yomi'];
$series_title      = $_REQUEST['series_title'];
$series_title_yomi = $_REQUEST['series_title_yomi'];
$naiyo_saimoku_title= $_REQUEST['naiyo_saimoku_title'];
$betu_series_title_yomi = $_REQUEST['betu_series_title_yomi'];
$naiyo_saimoku_title_yomi=$_REQUEST['naiyo_saimoku_title_yomi'];
$naiyo_saimoku_chosha = $_REQUEST['naiyo_saimoku_chosha'];
$naiyo_saimoku_chosha_yomi=$_REQUEST['naiyo_saimoku_chosha_yomi'];
$buhenmei           = $_REQUEST['buhenmei'];
$buhenmei_yomi      = $_REQUEST['buhenmei_yomi'];
$makiji_bango       = $_REQUEST['makiji_bango'];
$makiji_bango_yomi  = $_REQUEST['makiji_bango_yomi'];
$iban         = $_REQUEST['iban'];
$iban_chosha  = $_REQUEST['iban_chosha'];
$publisher    = $_REQUEST['publisher'];
$chuuki       = $_REQUEST['chuuki'];
$youyaku      = $_REQUEST['youyaku'];
$mokuji       = $_REQUEST['mokuji'];
$standard_id  = $_REQUEST['standard_id'];

$sakusei_nen  = $_REQUEST['sakusei_nen'];
$sakusei_tuki = $_REQUEST['sakussi_tuki'];
$sakusei_bi   = $_REQUEST['sakussi_bi'];
$online_nen   = $_REQUEST['online_nen'];
$online_tuki  = $_REQUEST['online_tuki'];
$online_bi    = $_REQUEST['online_bi'];
$online_nen   = $_REQUEST['online_nen'];
$online_tuki  = $_REQUEST['online_tuki'];
$online_bi    = $_REQUEST['online_bi'];
$koukai_nen   = $_REQUEST['koukai_nen'];
$koukai_tuki  = $_REQUEST['koukai_tuki'];
$koukai_bi    = $_REQUEST['koukai_bi'];
$language     = $_REQUEST['language'];
$is_bubun     = $_REQUEST['is_bubun'];
$oya_uri      = $_REQUEST['oya_uri'];
$shigen_mei   = $_REQUEST['shigen_mei'];
$has_bubun    = $_REQUEST['has_bubun'];
$taisho_basho_uri    = $_REQUEST['taisho_basho_uri'];
$taisho_basho_ken    = $_REQUEST['taisho_basho_ken'];
$taisho_basho_shi    = $_REQUEST['taisho_basho_shi'];
$taisho_basho_banchi = $_REQUEST['taisho_basho_banchi'];
$taisho_basho_ido    = $_REQUEST['taisho_basho_ido'];
$taisho_basho_keido  = $_REQUEST['taisho_basho_keido'];
$satuei_ido    = $_REQUEST['satuei_ido'];
$satuei_keido  = $_REQUEST['satuei_keido'];
$satuei_ken    = $_REQUEST['satuei_ken'];
$satuei_shi    = $_REQUEST['satuei_shi'];
$satuei_banchi = $_REQUEST['satuei_banchi'];
$kanko_hindo   = $_REQUEST['kanko_hindo'];
$kanko_status  = $_REQUEST['kanko_status'];
$kanko_kanji   = $_REQUEST['kanko_kanji'];

$doctor_bango    = $_REQUEST['doctor_bango'];
$doctor_nen      = $_REQUEST['doctor_nen'];
$doctor_tuki     = $_REQUEST['doctor_tuki']; 
$doctor_bi       = $_REQUEST['doctor_bi']; 
$doctor_daigaku  = $_REQUEST['doctor_daigaku'];

$keisai_go1      = $_REQUEST['keisai_go1'];
$keisai_go2      = $_REQUEST['keisai_go2'];
$keisa_shimei    = $_REQUEST['keisa_shimei'];
$keisai_ka       = $_REQUEST['keisai_ka'];
$keisai_page     = $_REQUEST['keisai_page'];

$license_info   = $_REQUEST['license_info'];
$license_uri    = $_REQUEST['license_uri'];
$license_holder = $_REQUEST['license_holder'];
$license_chuki  = $_REQUEST['license_chuki'];

$gov_issue        = $_REQUEST['gov_issue'];
$gov_issue_2      = $_REQUEST['gov_issue_2'];
$gov_issue_miyagi = $_REQUEST['gov_issue_miyagi'];
$gov_issue_chihou = $_REQUEST['gov_issue_chihou'];
$for_handicapped  = $_REQUEST['for_handicapped'];
$hakubutu_kubun   = $_REQUEST['hakubutu_kubun'];
$shiryo_keitai    = $_REQUEST['shiryo_keitai'];
$origina_shiryo_keitai = $_REQUEST['origina_shiryo_keitai'];
$shosha_flag      = $_REQUEST['shosha_flag'];
$online_flag      = $_REQUEST['online_flag'];
$shoshi_flag      = $_REQUEST['shoshi_flag'];
$chizu_kubun      = $_REQUEST['chizu_kubun'];

$haifu_taisho = $_REQUEST['haifu_taisho'];
$keiji_nen  = $_REQUEST['keiji_nen'];   
$keiji_tuki = $_REQUEST['keiji_tuki'];
$keiji_bi   = $_REQUEST['keiji_bi'];
$keiji_basho = $_REQUEST['keiji_basho'];
$keiji_basho_yomi = $_REQUEST['keiji_basho_yomi'];
$sekou_taisho = $_REQUEST['sekou_taisho'];
$sekou_nen = $_REQUEST['sekou_nen'];
$sekou_tuki = $_REQUEST['sekou_tuki'];
$sekou_bi = $_REQUEST['sekou_bi'];
$teller   = $_REQUEST['teller'];
$teller_yomi = $_REQUEST['teller_yomi'];
$seigen = $_REQUEST['seigen'];
?>
<font size='+1'><b>　　　　　　　　　　　入力内容を確認して下さい</font></b>
<form name="input_form" method ="post" action="write.php" onSubmit="return check()">
<table>
<tr><th>ユニークID</th><td><?php echo $uniqid; ?></td></tr>
<tr><th class='hissu'>資料種別</th><td>
	<select name = "md_type">
       <option value='図書'                                 <?php if ($md_type=="図書") { echo "selected"; } ?>>図書</option>
       <option value='記事'                                 <?php if ($md_type=="記事") { echo "selected"; } ?>>記事</option>
       <option value='雑誌・新聞'                           <?php if ($md_type=="雑誌・新聞") { echo "selected"; } ?>>雑誌・新聞</option>
       <option value='音声・映像'                           <?php if ($md_type=="音声・映像") { echo "selected"; } ?>>音声・映像</option>
       <option value='文書・楽譜'                           <?php if ($md_type=="文書・楽譜") { echo "selected"; } ?>>文書・楽譜</option>
       <option value='地図・地図帳'                         <?php if ($md_type=="地図・地図帳") { echo "selected"; } ?>>地図・地図帳</option>
       <option value='ポスター'                             <?php if ($md_type=="ポスター") { echo "selected"; } ?>>ポスター</option>
       <option value='写真'                                 <?php if ($md_type=="写真") { echo "selected"; } ?>>写真</option>
       <option value='チラシ'                               <?php if ($md_type=="チラシ") { echo "selected"; } ?>>チラシ</option>
       <option value='会議録・含資料'                       <?php if ($md_type=="会議録・含資料") { echo "selected"; } ?>>会議録・含資料</option>
       <option value='博物資料'                             <?php if ($md_type=="博物資料") { echo "selected"; } ?>>博物資料</option>
       <option value='オンライン資料'                       <?php if ($md_type=="オンライン資料") { echo "selected"; } ?>>オンライン資料</option>
       <option value='語り'                                 <?php if ($md_type=="語り") { echo "selected"; } ?>>語り</option>
       <option value='絵画・絵はがき'                       <?php if ($md_type=="絵画・絵はが") { echo "selected"; } ?>>絵画・絵はがき</option>
       <option value='プログラム（スマホアプリ・ゲーム等）' <?php if ($md_type=="プログラム（スマホアプリ・ゲーム等）") { echo "selected"; } ?>>プログラム（スマホアプリ・ゲーム等）</option>
    </select>

    <!--シリーズか否か-->
    <tr><th class='hissu'>シリーズ（継続資料）</th><td>
	<label><input class='optctrl' type="radio" value=0 name = "series_flag" <?php if ($series_flagg==0) { echo "checked"; } ?>>該当しない　</label>
	<label><input class='optctrl' type="radio" value=1 name = "series_flag" <?php if ($series_flagg==1) { echo "checked"; } ?>>該当する　</label>
    </td></tr>	
    <!--別タイトルの有無-->
    <tr><th>別タイトルの有無</th><td>
	<input class='optctrl' type="radio" value=0 name = "betu_title_flag" <?php if ($betu_title_flag==0) { echo "checked"; } ?>>無　
	<input class='optctrl' type="radio" value=1 name = "betu_title_flag" <?php if ($betu_title_flag==1) { echo "checked"; } ?>>有　
    <!--寄与者の有無-->
    <tr><th>寄与者（寄贈者）の有無</th><td>
	<input class='optctrl' type="radio" value=0 name = "kiyo_flag" <?php if ($kiyo_flag==0) { echo "checked"; } ?>>無　
	<input class='optctrl' type="radio" value=1 name = "kiyo_flag" <?php if ($kiyo_flag==1) { echo "checked"; } ?>>有　    
	<!--異版の有無-->
    <tr class='optional optional_図書 optional_雑誌・新聞'><th>異版<font size='-1'>（第x版、改訂版等）</font></th><td>
	<input class='optctrl' type="radio" value=0 name = "iban_flag" <?php if ($iban_flag==0) { echo "checked"; } ?>>該当しない　
	<input class='optctrl' type="radio" value=1 name = "iban_flag" <?php if ($iban_flag==1) { echo "checked"; } ?>>該当する　
	<!--ライセンスの有無-->
    <tr><th>ライセンス(CC等)の有無</th><td>
	<input class='optctrl' type="radio" value=0 name = "license_flag" <?php if ($ilicense_flag==0) { echo "checked"; } ?>>無　
	<input class='optctrl' type="radio" value=1 name = "license_flag" <?php if ($ilicense_flag==1) { echo "checked"; } ?>>有　
	<!--引用資料-->
    <tr><th>引用資料<br><font size='-1'>親となる資料からの引用</font></th><td>
	<input class='optctrl' type="radio" value=0 name = "inyou_flag" <?php if ($inyou_flag==0) { echo "checked"; } ?>>該当しない　
	<input class='optctrl' type="radio" value=1 name = "inyou_flag" <?php if ($inyou_flag==1) { echo "checked"; } ?>>該当する　

	<tr><th>政府刊行物・刊行元<br><font size='-1'>x省等が発行元</font></th><td>
	<select name = "gov_issue">
	   <option value=''    <?php if ($gov_issue=="") { echo "selected"; } ?>>該当しない</option>
       <option value='AA0' <?php if ($gov_issue=="AA0") { echo "selected"; } ?>> 衆議院</option>
       <option value='AB0' <?php if ($gov_issue=="AB0") { echo "selected"; } ?>> 参議院</option>
       <option value='AC0' <?php if ($gov_issue=="AC0") { echo "selected"; } ?>> 国立国会図書館</option>
       <option value='AD0' <?php if ($gov_issue=="AD0") { echo "selected"; } ?>> 裁判官弾劾裁判所</option>
       <option value='AE0' <?php if ($gov_issue=="AE0") { echo "selected"; } ?>> 裁判官訴追委員会</option>
       <option value='BA0' <?php if ($gov_issue=="BA0") { echo "selected"; } ?>> 会計検査院</option>
       <option value='CA0' <?php if ($gov_issue=="CA0") { echo "selected"; } ?>> 内閣</option>
       <option value='CB0' <?php if ($gov_issue=="CB0") { echo "selected"; } ?>> 安全保障会議</option>
       <option value='CC0' <?php if ($gov_issue=="CC0") { echo "selected"; } ?>> 人事院</option>
       <option value='DA0' <?php if ($gov_issue=="DA0") { echo "selected"; } ?>> 内閣府</option>
       <option value='DB0' <?php if ($gov_issue=="DB0") { echo "selected"; } ?>> 宮内庁</option>
       <option value='DC0' <?php if ($gov_issue=="DC0") { echo "selected"; } ?>> 国家公安委員会</option>
       <option value='DD0' <?php if ($gov_issue=="DD0") { echo "selected"; } ?>> 警察庁</option>
       <option value='DE0' <?php if ($gov_issue=="DE0") { echo "selected"; } ?>> 防衛省</option>
       <option value='DF0' <?php if ($gov_issue=="DF0") { echo "selected"; } ?>> 防衛施設庁</option>
       <option value='DG0' <?php if ($gov_issue=="DG0") { echo "selected"; } ?>> 金融庁</option>
       <option value='EA0' <?php if ($gov_issue=="EA0") { echo "selected"; } ?>> 総務省</option>
       <option value='EB0' <?php if ($gov_issue=="EB0") { echo "selected"; } ?>> 公正取引委員会</option>
       <option value='EC0' <?php if ($gov_issue=="EC0") { echo "selected"; } ?>> 公害等調整委員会</option>
       <option value='ED0' <?php if ($gov_issue=="ED0") { echo "selected"; } ?>> 郵政事業庁</option>
       <option value='ED1' <?php if ($gov_issue=="ED1") { echo "selected"; } ?>> 地方支分部局</option>
       <option value='EE0' <?php if ($gov_issue=="EE0") { echo "selected"; } ?>> 消防庁</option>
       <option value='FA0' <?php if ($gov_issue=="FA0") { echo "selected"; } ?>> 法務省</option>
       <option value='FA1' <?php if ($gov_issue=="FA1") { echo "selected"; } ?>> 地方支分部局</option>
       <option value='FB0' <?php if ($gov_issue=="FB0") { echo "selected"; } ?>> 司法試験管理委員会</option>
       <option value='FC0' <?php if ($gov_issue=="FC0") { echo "selected"; } ?>> 公安審査委員会</option>
       <option value='FD0' <?php if ($gov_issue=="FD0") { echo "selected"; } ?>> 公安調査庁</option>
       <option value='FE0' <?php if ($gov_issue=="EF0") { echo "selected"; } ?>> 検察庁</option>
       <option value='GA0' <?php if ($gov_issue=="GA0") { echo "selected"; } ?>> 外務省</option>
       <option value='HA0' <?php if ($gov_issue=="HA0") { echo "selected"; } ?>> 財務省</option>
       <option value='HA1' <?php if ($gov_issue=="HA1") { echo "selected"; } ?>> 地方支分部局</option>
       <option value='HB0' <?php if ($gov_issue=="HB0") { echo "selected"; } ?>> 国税庁</option>
       <option value='KA0' <?php if ($gov_issue=="KA0") { echo "selected"; } ?>> 文部科学省</option>
       <option value='KB0' <?php if ($gov_issue=="KB0") { echo "selected"; } ?>> 文化庁</option>
       <option value='LA0' <?php if ($gov_issue=="LA0") { echo "selected"; } ?>> 厚生労働省</option>
       <option value='LA1' <?php if ($gov_issue=="LA1") { echo "selected"; } ?>> 地方支分部局</option>
       <option value='LB0' <?php if ($gov_issue=="LB0") { echo "selected"; } ?>> 中央労働委員会</option>
       <option value='LC0' <?php if ($gov_issue=="LC0") { echo "selected"; } ?>> 社会保険庁</option>
       <option value='MA0' <?php if ($gov_issue=="MA0") { echo "selected"; } ?>> 農林水産省</option>
       <option value='MA1' <?php if ($gov_issue=="MA1") { echo "selected"; } ?>> 地方支分部局</option>
       <option value='MB0' <?php if ($gov_issue=="MB0") { echo "selected"; } ?>> 食糧庁</option>
       <option value='MC0' <?php if ($gov_issue=="MC0") { echo "selected"; } ?>> 林野庁</option>
       <option value='MD0' <?php if ($gov_issue=="MD0") { echo "selected"; } ?>> 水産庁</option>
       <option value='NA0' <?php if ($gov_issue=="NA0") { echo "selected"; } ?>> 経済産業省</option>
       <option value='NA1' <?php if ($gov_issue=="NA1") { echo "selected"; } ?>> 地方支分部局</option>
       <option value='NB0' <?php if ($gov_issue=="NB0") { echo "selected"; } ?>> 資源エネルギー庁</option>
       <option value='NC0' <?php if ($gov_issue=="NC0") { echo "selected"; } ?>> 特許庁</option>
       <option value='ND0' <?php if ($gov_issue=="ND0") { echo "selected"; } ?>> 中小企業庁</option>
       <option value='PA0' <?php if ($gov_issue=="PA0") { echo "selected"; } ?>> 国土交通省</option>
       <option value='PA1' <?php if ($gov_issue=="PA1") { echo "selected"; } ?>> 地方支分部局</option>
       <option value='PB0' <?php if ($gov_issue=="PB0") { echo "selected"; } ?>> 船員労働委員会</option>
       <option value='PC0' <?php if ($gov_issue=="PC0") { echo "selected"; } ?>> 気象庁</option>
       <option value='PD0' <?php if ($gov_issue=="PD0") { echo "selected"; } ?>> 海上保安庁</option>
       <option value='PE0' <?php if ($gov_issue=="PE0") { echo "selected"; } ?>> 海難審判庁</option>
       <option value='RA0' <?php if ($gov_issue=="RA0") { echo "selected"; } ?>> 環境省</option>
       <option value='SA0' <?php if ($gov_issue=="SA0") { echo "selected"; } ?>> 最高裁判所</option>
       <option value='SB0' <?php if ($gov_issue=="SB0") { echo "selected"; } ?>> 高等裁判所</option>
       <option value='SC0' <?php if ($gov_issue=="SC0") { echo "selected"; } ?>> 地方裁判所</option>
       <option value='SD0' <?php if ($gov_issue=="SD0") { echo "selected"; } ?>> 家庭裁判所</option>
       <option value='TA0' <?php if ($gov_issue=="TA0") { echo "selected"; } ?>> 公団</option>
       <option value='TB0' <?php if ($gov_issue=="TB0") { echo "selected"; } ?>> 事業団</option>
       <option value='TC0' <?php if ($gov_issue=="TC0") { echo "selected"; } ?>> 公庫</option>
       <option value='TD0' <?php if ($gov_issue=="TD0") { echo "selected"; } ?>> 基金</option>
       <option value='TE0' <?php if ($gov_issue=="TE0") { echo "selected"; } ?>> 銀行</option>
       <option value='TF0' <?php if ($gov_issue=="EF0") { echo "selected"; } ?>> その他</option>
       <option value='WA0' <?php if ($gov_issue=="WA0") { echo "selected"; } ?>> 国立大学等</option>
       <option value='WB0' <?php if ($gov_issue=="WB0") { echo "selected"; } ?>> 国立大学共同利用機関</option>
    </select>
	</td></tr>
   	<!--官公庁刊行物-->
    <tr><th>官公庁刊行物<br><font size='-1'>該当する場合刊行元（機関名）<br><font size='-1'>x省等の下部機関が発行元</font></th><td>
    <input type='text' name='gov_issue_2' size='40' value='<?php echo $gov_issue_2; ?>'></td></tr>
    <!--地方公共団体刊行物 -->
    <tr><th>官公庁刊行物<br><font size='-1'>該当する場合刊行元（団体名）</font></th><td>
    <input type='text' name='gov_issue_chihou' size='40' value='<?php echo $gov_issue_chihou; ?>'></td></tr>
    <!--宮城県内地方公共団体刊行物-->
    <tr><th>宮城県内地方公共団体刊行物<br><font size='-1'>宮城県内の自治体が発行元</font></th><td>
    <select name='gov_issue_miyagi'>
    	<option value=''     <?php if ($gov_issue_miyagi=="") { echo "selected"; } ?>>該当しない</option>
        <option value='4100' <?php if ($gov_issue_miyagi=="4100") { echo "selected"; } ?>>仙台市</optio
        <option value='4202' <?php if ($gov_issue_miyagi=="4202") { echo "selected"; } ?>>石巻市</option>
        <option value='4203' <?php if ($gov_issue_miyagi=="4203") { echo "selected"; } ?>>塩竈市</option>
        <option value='4204' <?php if ($gov_issue_miyagi=="4204") { echo "selected"; } ?>>古川市</option>
        <option value='4205' <?php if ($gov_issue_miyagi=="4205") { echo "selected"; } ?>>気仙沼市</option>
        <option value='4206' <?php if ($gov_issue_miyagi=="4206") { echo "selected"; } ?>>白石市</option>
        <option value='4207' <?php if ($gov_issue_miyagi=="4207") { echo "selected"; } ?>>名取市</option>
        <option value='4208' <?php if ($gov_issue_miyagi=="4208") { echo "selected"; } ?>>角田市</option>
        <option value='4209' <?php if ($gov_issue_miyagi=="4209") { echo "selected"; } ?>>多賀城市</option>
        <option value='4210' <?php if ($gov_issue_miyagi=="4210") { echo "selected"; } ?>>泉市</option>
        <option value='4211' <?php if ($gov_issue_miyagi=="4211") { echo "selected"; } ?>>岩沼市</option>
        <option value='4212' <?php if ($gov_issue_miyagi=="4212") { echo "selected"; } ?>>登米市</option>
        <option value='4213' <?php if ($gov_issue_miyagi=="4213") { echo "selected"; } ?>>栗原市</option>
        <option value='4214' <?php if ($gov_issue_miyagi=="4214") { echo "selected"; } ?>>東松島市</option>
        <option value='4215' <?php if ($gov_issue_miyagi=="4215") { echo "selected"; } ?>>大崎市</option>
        <option value='4301' <?php if ($gov_issue_miyagi=="4301") { echo "selected"; } ?>>蔵王町</option>
        <option value='4302' <?php if ($gov_issue_miyagi=="4302") { echo "selected"; } ?>>七ヶ宿町</option>
        <option value='4321' <?php if ($gov_issue_miyagi=="4321") { echo "selected"; } ?>>大河原町</option>
        <option value='4322' <?php if ($gov_issue_miyagi=="4322") { echo "selected"; } ?>>村田町</option>
        <option value='4323' <?php if ($gov_issue_miyagi=="4323") { echo "selected"; } ?>>柴田町</option>
        <option value='4324' <?php if ($gov_issue_miyagi=="4324") { echo "selected"; } ?>>川崎町</option>
        <option value='4341' <?php if ($gov_issue_miyagi=="4341") { echo "selected"; } ?>>丸森町</option>
        <option value='4361' <?php if ($gov_issue_miyagi=="4361") { echo "selected"; } ?>>亘理町</option>
        <option value='4362' <?php if ($gov_issue_miyagi=="4362") { echo "selected"; } ?>>山元町</option>
        <option value='4381' <?php if ($gov_issue_miyagi=="4381") { echo "selected"; } ?>>岩沼町</option>
        <option value='4382' <?php if ($gov_issue_miyagi=="4382") { echo "selected"; } ?>>秋保町</option>
        <option value='4401' <?php if ($gov_issue_miyagi=="4401") { echo "selected"; } ?>>松島町</option>
        <option value='4402' <?php if ($gov_issue_miyagi=="4402") { echo "selected"; } ?>>多賀城町</option>
        <option value='4403' <?php if ($gov_issue_miyagi=="4403") { echo "selected"; } ?>>泉町</option>
        <option value='4404' <?php if ($gov_issue_miyagi=="4404") { echo "selected"; } ?>>七ヶ浜町</option>
        <option value='4405' <?php if ($gov_issue_miyagi=="4405") { echo "selected"; } ?>>宮城町</option>
        <option value='4406' <?php if ($gov_issue_miyagi=="4406") { echo "selected"; } ?>>利府町</option>
        <option value='4421' <?php if ($gov_issue_miyagi=="4421") { echo "selected"; } ?>>大和町</option>
        <option value='4422' <?php if ($gov_issue_miyagi=="4422") { echo "selected"; } ?>>大郷町</option>
        <option value='4423' <?php if ($gov_issue_miyagi=="4423") { echo "selected"; } ?>>富谷町</option>
        <option value='4424' <?php if ($gov_issue_miyagi=="4424") { echo "selected"; } ?>>大衡村</option>
        <option value='4441' <?php if ($gov_issue_miyagi=="4441") { echo "selected"; } ?>>中新田町</option>
        <option value='4442' <?php if ($gov_issue_miyagi=="4442") { echo "selected"; } ?>>小野田町</option>
        <option value='4443' <?php if ($gov_issue_miyagi=="4443") { echo "selected"; } ?>>宮崎町</option>
        <option value='4444' <?php if ($gov_issue_miyagi=="4444") { echo "selected"; } ?>>色麻町</option>
        <option value='4445' <?php if ($gov_issue_miyagi=="4445") { echo "selected"; } ?>>色麻村</option>
        <option value='4461' <?php if ($gov_issue_miyagi=="4461") { echo "selected"; } ?>>加美町</option>
        <option value='4462' <?php if ($gov_issue_miyagi=="4462") { echo "selected"; } ?>>松山町</option>
        <option value='4463' <?php if ($gov_issue_miyagi=="4463") { echo "selected"; } ?>>三本木町</option>
        <option value='4481' <?php if ($gov_issue_miyagi=="4481") { echo "selected"; } ?>>鹿島台町</option>
        <option value='4482' <?php if ($gov_issue_miyagi=="4482") { echo "selected"; } ?>>岩出山町</option>
        <option value='4501' <?php if ($gov_issue_miyagi=="4501") { echo "selected"; } ?>>鳴子町</option>
        <option value='4502' <?php if ($gov_issue_miyagi=="4502") { echo "selected"; } ?>>涌谷町</option>
        <option value='4503' <?php if ($gov_issue_miyagi=="4503") { echo "selected"; } ?>>田尻町</option>
        <option value='4504' <?php if ($gov_issue_miyagi=="4504") { echo "selected"; } ?>>小牛田町</option>
        <option value='4505' <?php if ($gov_issue_miyagi=="4505") { echo "selected"; } ?>>南郷町</option>
        <option value='4521' <?php if ($gov_issue_miyagi=="4521") { echo "selected"; } ?>>美里町</option>
        <option value='4522' <?php if ($gov_issue_miyagi=="4522") { echo "selected"; } ?>>築館町</option>
        <option value='4523' <?php if ($gov_issue_miyagi=="4523") { echo "selected"; } ?>>若柳町</option>
        <option value='4524' <?php if ($gov_issue_miyagi=="4524") { echo "selected"; } ?>>栗駒町</option>
        <option value='4525' <?php if ($gov_issue_miyagi=="4525") { echo "selected"; } ?>>高清水町</option>
        <option value='4526' <?php if ($gov_issue_miyagi=="4526") { echo "selected"; } ?>>一迫町</option>
        <option value='4527' <?php if ($gov_issue_miyagi=="4527") { echo "selected"; } ?>>瀬峰町</option>
        <option value='4528' <?php if ($gov_issue_miyagi=="4528") { echo "selected"; } ?>>鶯沢町</option>
        <option value='4529' <?php if ($gov_issue_miyagi=="4529") { echo "selected"; } ?>>金成町</option>
        <option value='4530' <?php if ($gov_issue_miyagi=="4530") { echo "selected"; } ?>>志波姫町</option>
        <option value='4541' <?php if ($gov_issue_miyagi=="4541") { echo "selected"; } ?>>花山村</option>
        <option value='4542' <?php if ($gov_issue_miyagi=="4542") { echo "selected"; } ?>>迫町</option>
        <option value='4543' <?php if ($gov_issue_miyagi=="4543") { echo "selected"; } ?>>登米町</option>
        <option value='4544' <?php if ($gov_issue_miyagi=="4544") { echo "selected"; } ?>>東和町</option>
        <option value='4545' <?php if ($gov_issue_miyagi=="4545") { echo "selected"; } ?>>中田町</option>
        <option value='4546' <?php if ($gov_issue_miyagi=="4546") { echo "selected"; } ?>>豊里町</option>
        <option value='4547' <?php if ($gov_issue_miyagi=="4547") { echo "selected"; } ?>>米山町</option>
        <option value='4548' <?php if ($gov_issue_miyagi=="4548") { echo "selected"; } ?>>石越町</option>
        <option value='4561' <?php if ($gov_issue_miyagi=="4561") { echo "selected"; } ?>>南方町</option>
        <option value='4562' <?php if ($gov_issue_miyagi=="4562") { echo "selected"; } ?>>河北町</option>
        <option value='4563' <?php if ($gov_issue_miyagi=="4563") { echo "selected"; } ?>>矢本町</option>
        <option value='4564' <?php if ($gov_issue_miyagi=="4564") { echo "selected"; } ?>>雄勝町</option>
        <option value='4565' <?php if ($gov_issue_miyagi=="4565") { echo "selected"; } ?>>河南町</option>
        <option value='4566' <?php if ($gov_issue_miyagi=="4566") { echo "selected"; } ?>>桃生町</option>
        <option value='4567' <?php if ($gov_issue_miyagi=="4567") { echo "selected"; } ?>>北上町</option>
        <option value='4581' <?php if ($gov_issue_miyagi=="4581") { echo "selected"; } ?>>女川町</option>
        <option value='4582' <?php if ($gov_issue_miyagi=="4582") { echo "selected"; } ?>>牡鹿町</option>
        <option value='4601' <?php if ($gov_issue_miyagi=="4601") { echo "selected"; } ?>>志津川町</option>
        <option value='4602' <?php if ($gov_issue_miyagi=="4602") { echo "selected"; } ?>>津山町</option>
        <option value='4603' <?php if ($gov_issue_miyagi=="4603") { echo "selected"; } ?>>本吉町</option>
        <option value='4604' <?php if ($gov_issue_miyagi=="4604") { echo "selected"; } ?>>唐桑町</option>
        <option value='4605' <?php if ($gov_issue_miyagi=="4605") { echo "selected"; } ?>>歌津町</option>
        <option value='4606' <?php if ($gov_issue_miyagi=="4606") { echo "selected"; } ?>>南三陸町</option>
    </select>
    <!--視聴覚者向け資料-->
    <tr><th>視聴覚者向け資料</th><td>
    <select name='for_handicapped'>
       	<option value='' <?php if ($for_handicapped=="") { echo "selected"; } ?>>該当しない</option>
    	<option value='Braille' <?php if ($for_handicapped=="Braille") { echo "selected"; } ?>>点字</option>
        <option value='DAISY' <?php if ($for_handicapped=="DAISY") { echo "selected"; } ?>>DAISY</option>
		<option value='AudioBookInSoundD' <?php if ($for_handicapped=="AudioBookInSoundD") { echo "selected"; } ?>>録音図書（DVD・CD）</option>
		<option value='AudioBookInSoundT' <?php if ($for_handicapped=="AudioBookInSoundT") { echo "selected"; } ?>>録音図書（カセットテープ）</option>
    </select>

    <!--オリジナル資料の形態-->
    <tr><th>オリジナル資料の形態</th><td>
    <select name='origina_shiryo_keitai'>
    	<option value= ''  <?php if ($origina_shiryo_keitai=="") { echo "selected"; } ?>>該当なし（その他）</option>
		<option value='31' <?php if ($origina_shiryo_keitai=="31") { echo "selected"; } ?>>ＣＤ</option>
		<option value='32' <?php if ($origina_shiryo_keitai=="32") { echo "selected"; } ?>>カセット</option>
		<option value='33' <?php if ($origina_shiryo_keitai=="33") { echo "selected"; } ?>>>レコード</option>
		<option value='34' <?php if ($origina_shiryo_keitai=="34") { echo "selected"; } ?>>リールテープ</option>
		<option value='35' <?php if ($origina_shiryo_keitai=="35") { echo "selected"; } ?>>>ＭＤ</option>
		<option value='36' <?php if ($origina_shiryo_keitai=="36") { echo "selected"; } ?>>録音図書</option>
		<option value='39' <?php if ($origina_shiryo_keitai=="39") { echo "selected"; } ?>>録音その他</option>
		<option value='41' <?php if ($origina_shiryo_keitai=="41") { echo "selected"; } ?>>ビデオテープ</option>
		<option value='42' <?php if ($origina_shiryo_keitai=="42") { echo "selected"; } ?>>ＬＤ</option>
		<option value='43' <?php if ($origina_shiryo_keitai=="43") { echo "selected"; } ?>>ＤＶＤ</option>
		<option value='44' <?php if ($origina_shiryo_keitai=="44") { echo "selected"; } ?>>ＥＬＩＢ</option>
		<option value='45' <?php if ($origina_shiryo_keitai=="45") { echo "selected"; } ?>>>ブルーレイディスク</option>
		<option value='46' <?php if ($origina_shiryo_keitai=="46") { echo "selected"; } ?>>映像フィルム</option>
		<option value='49' <?php if ($origina_shiryo_keitai=="49") { echo "selected"; } ?>>映像その他</option>
		<option value='51' <?php if ($origina_shiryo_keitai=="51") { echo "selected"; } ?>>磁気テープ</option>
		<option value='52' <?php if ($origina_shiryo_keitai=="52") { echo "selected"; } ?>>ＦＤ</option>
		<option value='53' <?php if ($origina_shiryo_keitai=="53") { echo "selected"; } ?>>ＣＤ－ＲＯＭ</option>
		<option value='54' <?php if ($origina_shiryo_keitai=="54") { echo "selected"; } ?>>ＭＯ</option>
		<option value='59' <?php if ($origina_shiryo_keitai=="59") { echo "selected"; } ?>>機械その他</option>
		<option value='61' <?php if ($origina_shiryo_keitai=="61") { echo "selected"; } ?>>ネガ・ポジ</option>
		<option value='62' <?php if ($origina_shiryo_keitai=="62") { echo "selected"; } ?>>>プリント</option>
		<option value='63' <?php if ($origina_shiryo_keitai=="63") { echo "selected"; } ?>>スライド</option>
		<option value='69' <?php if ($origina_shiryo_keitai=="69") { echo "selected"; } ?>>写真その他</option>
		<option value='71' <?php if ($origina_shiryo_keitai=="71") { echo "selected"; } ?>>楽譜</option>
		<option value='81' <?php if ($origina_shiryo_keitai=="81") { echo "selected"; } ?> >マイクロＬ</option>
		<option value='82' <?php if ($origina_shiryo_keitai=="82") { echo "selected"; } ?> >マイクロＣ</option>
		<option value='91' <?php if ($origina_shiryo_keitai=="91") { echo "selected"; } ?>>別置解説書</option>
		<option value='99' <?php if ($origina_shiryo_keitai=="92") { echo "selected"; } ?>>その他ＡＶ</option>
    </select>
    	
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
<!--図書の場合はNDLに問い合わせ、情報がなければMecabを使う
if($md_type=="図書"){
	$creator_yomi = yomi($creator_yomi, ndl_creator_yomi($creator)) ;
}
$creator_yomi = yomi($creator_yomi, mecab($creator)) ;
$string .="<td><input type='text' name='creator_yomi' size='40' value='".$creator_yomi."'></td></tr>\n";
-->

<!--寄与者-->
<tr class='kiyo_flag_option'><th>寄与者（寄贈者）
<td><input type='text' name='contributor' size='40' value='<?php echo $contributor; ?>'></td></tr>
<tr class='kiyo_flag_option'><th>寄与者（寄贈者）のヨミ
<!--澤田さん-->
<?php
$contributor_yomi = ($contributor_yomi <> '') ? $contributor_yomi : mecab($contributor) ;
$string .="<td><input type='text' name='contributor_yomi' size='40' value='".$contributor_yomi."'></td></tr>\n";
?>

<!--異版-->
<tr class='iban_flag_option'><th>異版名(第x版）
<td ><input type='text' name='iban' value='<?php echo $iban; ?>' size='40'></td></tr>
<tr class='iban_flag_option'><th>異版の著者名
<td><input type='text' name='iban_chosha' value='<?php echo $iban_chosha; ?>' size='40'></td></tr>

<!--出版社・公開者-->
<tr><th>出版社・公開者
<!--澤田さん-->
<?php 
if($md_type=="図書"){
	$publisher = get_info('dc_publisher');
} else {
	$publisher ="";
}
?>
<td><input type='text' name='publisher' size='40' value='<?php echo $publisher; ?>' ></td></tr>

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
<td><input type='text' name='haifu_nen' value='<?php echo $haifu_nen; ?>'size='4'>年（西暦）
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
<tr><th>情報の質
<td><input type='radio' name='seigen' value='0' <?php if ($seigen==0) { echo "checked"; } ?>>該当しない
<input type='radio' name='seigen' value='1'     <?php if ($seigen==1) { echo "checked"; } ?>>悲惨（閲覧注意）</td></tr>

<?php 
if($md_type=="図書"){
	$pubDate = get_info('pubDate');
	$y = '';
	$m = '';
	$d = '';
	if($pubDate <>''){
		list($y, $m, $d) = explode("-", date("Y-m-d", strtotime($pubDate)));
	}
}
?>
	
</table>


<!--基本情報整理表より-->
<input type='hidden'name='ken_or_shi' value='<?php echo $ken_or_shi ?>'>
<input type='hidden' name='local_code' value='<?php echo $local_code; ?>'>
<input type='hidden' name='kanri_bango' value='<?php echo $kanri_bango; ?>'>
<input type='hidden' name='contributor' value='<?php echo $contributor; ?>'>
<input type='hidden' name='contributor_yomi' value='<?php echo $contributor_yomi; ?>'>	
<input type='hidden' name='bunrui_code' value='<?php echo $bunrui_code; ?>'>
<input type='hidden' name='bunsho_bunrui' value='<?php echo $bunsho_bunrui; ?>'>
<input type='hidden' name='title' value='<?php echo $title; ?>'>
<input type='hidden' name='creator'      value='<?php echo $creator; ?>'>
<input type='hidden' name='creator_yomi' value='<?php echo $creator_yomi; ?>'>
<input type='hidden' name='sakusei_nen'  value='<?php echo $sakusei_nen; ?>'>
<input type='hidden' name='sakusei_tuki' value='<?php echo $sakusei_tuki; ?>'>
<input type='hidden' name='sakusei_hi'   value='<?php echo $sakusei_hi; ?>'>
<input type='hidden' name='satuei_basho_zip'          value='<?php echo $satuei_basho_zip; ?>'>
<input type='hidden' name='satuei_basho_address'      value='<?php echo $satuei_basho__address; ?>'>
<input type='hidden' name='satuei_basho_address_yomi' value='<?php echo $satuei_basho_address_yomi; ?>'>
<input type='hidden' name='haifu_basho'      value='<?php echo $haifu_basho; ?>'>
<input type='hidden' name='haifu_basho_yomi' value='<?php echo $haifu_basho_yomi; ?>'>	
<input type='hidden' name='keyword' value='<?php echo $kyeword; ?>'>
<input type='hidden' name='renraku_saki_zip'     value='<?php echo $renraku_saki_zip; ?>'>
<input type='hidden' name='renraku_saki_address' value='<?php echo $renraku_saki_address; ?>'>
<input type='hidden' name='renraku_saki_tel'     value='<?php echo $renraku_saki_tel; ?>'>
<input type='hidden' name='renraku_saki_other'   value='<?php echo $renraku_saki_other; ?>'>
<input type='hidden' name='kenri_shori'  value='<?php echo $kenri_shori; ?>'>
<input type='hidden' name='horyu_reason' value='<?php echo $horyu_reason; ?>'>
<input type='hidden' name='open_level'   value='<?php echo $open_level; ?>'>
<input type='hidden' name='media_code'   value='<?php echo $media_code; ?>'>

<!--ここまで-->
<input type='hidden' name='lot' value="<?php echo $lot_id; ?>">
<input type='hidden' name='id' value="<?php echo $id; ?>">
<input type='button' value="前画面へ" onClick="history.back();">
<input type="submit" value="決定">
</form>
	
<!--
</form>
<input type="submit" name='next' value="登録して次へ">
<input type="submit" name='quit' value="中断">
--->
	
</div>
</body>
</html>
