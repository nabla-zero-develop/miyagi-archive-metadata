<?php
	
//確認、登録へ

include_once(dirname(__FILE__) . "/NDL/NDL.php");
include_once(dirname(__FILE__) . "/NDL/utils.php");

mysql_connect('localhost','root','');
mysql_select_db('metadata_system');
mysql_query('set names utf8');

$lot_id = intval($_REQUEST['lot']);
$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):1;

$res = mysql_query("select count(*) as c from lotfiles where lotid=$lot_id");
$row = mysql_fetch_assoc($res);
if(!$row)die("No data for lot no. $lot_id.");
$num_in_lot = $row['c'];

$res = mysql_query("select * from lotfiles where lotid=$lot_id and id=$id");
$row = mysql_fetch_assoc($res);
if(!$row)die("No data for lot no. $lot_id and id $id.");
$uniqid=$row['uniqid'];

$filedir = $row['filepath'];
$files = glob($filedir.'/*');

$res = mysql_query("select * from content where uniqid=$uniqid");
$data = mysql_fetch_assoc($res);
if(!$data){
	$data = array(
'md_type' => '','md_title' => '','md_copywriter' => '','md_copywriter_other' => '','md_copyrigher_uri' => '','md_copyrighter_yomi' => '','md_content_year' => '-1','md_content_month' => '-1','md_content_day' => '-1','md_content_hour' => '-1','md_content_min' => '-1','md_content_sec' => '-1','md_publish_year' => '-1','md_publish_month' => '-1','md_publish_day' => '-1','md_setting_year' => '-1','md_setting_month' => '-1','md_seting_day' => '-1','md_setting_place' => '','md_issue_for' => '','md_issue_year' => '-1','md_issue_month' => '-1','md_issue_day' => '-1','md_narrator' => '','md_content_restriction' => ''
	);
}
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
<table>
	
	
<tr><th>ユニークID</th><td><?php echo $uniqid ?></td></tr>

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

//metadata4より受け取り
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
	
//metadata5より受け取り
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


//タイトル
$string ="<tr><th>タイトル　\n";
if ($md_type!="写真" and $md_type!="ポスター"){
    $string ="<tr><th class='hissu'>タイトル　\n";
} else {
	$string ="<tr><th>タイトル　\n";
}
$string .="	<br><input type='button' value='NDLチェック'><br></th>\n";
$string .="<td><input type='text' name='title' size='40' value='". $title ."'></td></tr>\n";
//タイトルのヨミ

if ($md_type!="写真" and $md_type!="ポスター"){
	$string .="<tr><th class='hissu'>タイトルのヨミ\n";
}else{
	$string .="<tr><th>タイトルのヨミ\n";
}
$string .="<td><input type='text' name='title_yomi' size='40' value='". mecab($title) ."'></td></tr>\n";
// NDL問い合わせ
if($md_type=="図書"){
	$book_info = ndl_title_info($title);
}

//別タイトル
if ($betu_title_flag==1){
    if ($md_type!="写真" and $md_type!="ポスター"){
	    $string .="<tr><th class='hissu'>別タイトル　";
	}else{
	    $string .="<tr><th>別タイトル　";
	}
    $string .="<td><input type='text' name='betu_title' size='40'></td></tr>\n";
    //別タイトルのヨミ
    if ($md_type!="写真" and $md_type!="ポスター"){
	    $string .="<tr><th class='hissu'>タイトルのヨミ\n";
    }else{
        $string .="<tr><th>タイトルのヨミ\n";
    }
    $string .="<td><input type='text' name='betu_title_yomi' size='40'></td></tr>\n";
}


//作成者
$string .="<tr><th class='hissu'>作成者・著者\n";
 if($md_type=="図書"){
	$creator = yomi($creator, clean_author(get_info('author')));
}
$string .="<td><input type='text' name='creator' size='40' value='".$creator."'></td></tr>\n";
//作成者のヨミ
$string .="<tr><th class='hissu'>作成者・著者のヨミ\n";
// 図書の場合はNDLに問い合わせ、情報がなければMecabを使う
 if($md_type=="図書"){
	$creator_yomi = yomi($creator_yomi, ndl_creator_yomi($creator)) ;
}
$creator_yomi = yomi($creator_yomi, mecab($creator)) ;
$string .="<td><input type='text' name='creator_yomi' size='40' value='".$creator_yomi."'></td></tr>\n";

if ($series_flag==1){
	if ($md_type!="写真" and $md_type!="ポスター"){
       	$string .="<tr><th class='hissu'>シリーズタイトル\n";
    }else{
        $string .="<tr><th>シリーズタイトル\n";
    }
    $string .="<td><input type='text' name='series_title' value='".$series_title." size='40'></td></tr>\n";
    
	if ($md_type!="写真" and $md_type!="ポスター"){
            $string .="<tr><th class='hissu'>シリーズタイトルのヨミ\n";
    }else{
        $string .="<tr><th>シリーズタイトルのヨミ\n";
    }
    $string .="<td><input type='text' name='series_title_yomi' size='40' value='".$series_title_yomi."></td></tr>\n";
    
    if($betu_title_flag==1){
	   if ($md_type!="写真" and $md_type!="ポスター"){
       	   $string .="<tr><th class='hissu'>別シリーズタイトル\n";
       }else{
           $string .="<tr><th>別シリーズタイトル\n";
       }
       $string .="<td><input type='text' name='betu_series_title' size='40' value='".$betu_series_title."></td></tr>\n";
	   if ($md_type!="写真" and $md_type!="ポスター"){
           $string .="<tr><th class='hissu'>別シリーズタイトルのヨミ\n";
       }else{
           $string .="<tr><th>別シリーズタイトルのヨミ\n";
       }
    $string .="<td><input type='text' name='betu_series_title_yomi' size='40' value='".$series_title_yomi."></td></tr>\n";
    }
    
    if ($md_type=="図書" or $md_type=="記事" or $md_type=="雑誌・新聞" or $md_type=="音声・映像" or $md_type=="文書・楽譜" or $md_type=="地図・地図帳"){
        $string .="<tr><th>内容細目タイトル\n";
        $string .="<td><input type='text' name='naiyo_saimoku_title' size='40' value='".$naiyo_saimoku_title."></td></tr>\n";
        $string .="<tr><th>内容細目タイトルのヨミ\n";
        $string .="<td><input type='text' name='naiyo_saimoku_title_yomi' size='40' value='".$naiyo_saimoku_title_yomi."'></td></tr>\n";
        $string .="<tr><th>内容細目著者\n";
        $string .="<td><input type='text' name='naiyo_saimoku_chosha' size='40' value='".$naiyo_saimoku_chosha."'></td></tr>\n";
        $string .="<tr><th>内容細目著者のヨミ\n";
        $string .="<td><input type='text' name='naiyo_saimoku_chosha_yomi' size='40' value='".$naiyo_saimoku_chosha_yomi."'></td></tr>\n";
        $string .="<tr><th>部編名\n";
        $string .="<td><input type='text' name='buhenmei' size='40 value='".$buhenmei."'></td></tr>\n";
        $string .="<tr><th>部編名のヨミ\n";
        $string .="<td><input type='text' name='buhenmei_yomi' size='40' value='".$buhenmei_yomi."'></td></tr>\n";
        $string .="<tr><th>巻次・部編番号\n";
        $string .="<td><input type='text' name='makiji_bango' size='40' value='".$makiji_bango."'></td></tr>\n";
        $string .="<tr><th>巻次・部編番号のヨミ\n";
        $string .="<td><input type='text' name='makiji_bango_yomi' size='40' value='".$makiji_bango_yomi."'></td></tr>\n";
    }
    //刊行頻度
    if ($md_type=="図書" or $md_type=="記事" or $md_type=="雑誌・新聞" or $md_type=="文書・楽譜" or $md_type=="地図・地図帳" or $md_type=="チラシ" or $md_type=="会議録・含資料" or $md_type=="絵画・絵はがき"){
        $string .="<tr><th>刊行頻度\n";
        $string .="<td><input type='text' name='kanko_hindo' size='40' value='".$kanko_hindo."'></td></tr>\n";
        $string .="<tr><th>刊行状態\n";
        if ($kanko_status=="c"){ $selected_c ="selected";}
        if ($kanko_status=="d"){ $selected_d ="selected";}
        if ($kanko_status=="u"){ $selected_u ="selected";}
        $string .="<td><select name='kanko_status'>\n";
        $string .="   <option value='c' $selected_c>刊行中</option>\n";
        $string .="   <option value='d' $selected_d>廃刊</option>\n";
        $string .="   <option value='u' $selected_u>不明</option>\n";
        $string .="</select>\n";
        $string .="</td></tr>";
        $string .="<tr><th>刊行巻次\n";
        $string .="<td><input type='text' name='kanko_kanji' size='40' value='".$kanko_kanji."'></td></tr>\n";
    }
    if ($md_type=="記事" or $md_type=="会議録・含資料"){
        $string .="<tr><th>掲載通号\n";
        $string .="<td><input type='text' name='keisai_go1' size='40' value='".$keisai_go1."'></td></tr>\n";
        $string .="<tr><th>掲載号\n";
        $string .="<td><input type='text' name='keisai_go2' size='40' value='".$keisai_go2."'></td></tr>\n";
        $string .="<tr><th>掲載誌名\n";
        $string .="<td><input type='text' name='keisai_shimei' size='40' value='".$keisai_shimei."'></td></tr>\n";
        $string .="<tr><th>掲載巻（論文の場合）\n";
        $string .="<td><input type='text' name='keisai_kan' size='40' value='".$keisai_kan."'></td></tr>\n";
        $string .="<tr><th>掲載ページ\n";
        $string .="<td><input type='text' name='keisai_page' size='40' value='".$keisai_page."'></td></tr>\n";
     }
}
//原資料の標準番号
$string .="<tr><th>標準番号(ISBN等)\n";
$string .="	<br><input type='button' value='NDLチェック'>\n";
$string .="<td><input type='text' name='standard_id' size='40' value='".$standard_id."'></td></tr>\n";

//話者
if ($md_type=="語り"){ 
    $string .="<tr><th>話者\n";
    $string .="<td><input type='text' name='teller' size='40' value='".$teller."'></td></tr>\n";
    $string .="<tr><th>話者のヨミ\n";
    $string .="<td><input type='text' name='teller_yomi' size='40' value='".$teller_yomi."'></td></tr>\n";
}

//寄与者
if ($kiyo_flag == 1){
	    $string .="<tr><th>寄与者（寄贈者）\n";
        $string .="<td><input type='text' name='contributor' size='40' value='".$contributor."'></td></tr>\n";
        $string .="<tr><th>寄与者（寄贈者）のヨミ\n";
		$contributor_yomi = ($contributor_yomi <> '') ? $contributor_yomi : mecab($contributor) ;
        $string .="<td><input type='text' name='contributor_yomi' size='40' value='".$contributor_yomi."'></td></tr>\n";
}
//異版
if($ihan_flag == 1){
	if($md_type=="図書" or $md_type=="雑誌・新聞"){
	    $string .="<tr><th>異版名(第x版）\n";
        $string .="<td><input type='text' name='iban' size='40' value='".$iban."'></td></tr>\n";
        $string .="<tr><th>異版の著者名\n";
        $string .="<td><input type='text' name='iban_chosha' size='40' value='".$iban_chosha."'></td></tr>\n";
	}
}

//出版社・公開者情報
$string .="<tr><th>出版社・公開者\n";
$string .="<td><input type='text' name='publisher' size='40'value='".get_info('dc_publisher')."'></td></tr>\n";
//サブジェクト（キーワード）
if ($md_type=="写真" or $md_type=="ポスター"){
     $string .="<tr><th class='hissu'>主題（キーワード）\n";
}else{
     $string .="<tr><th>主題（キーワード）\n";
}
$string .="<td><input type='text' name='subject' size='40' value='".$keyword."'></td></tr>\n";

//注記
$string .="<tr><th>注記等\n";
$string .="<td><input type='text' name='chuuki' size='40' value='".$chuuki."'></td></tr>\n";

//要約
$string .="<tr><th>要約\n";
$string .="<td><input type='text' name='youyaku' size='40' value='".$youyaku."'></td></tr>\n";

//目次
if ($md_type!="ポスター" and $md_type!="写真" and $md_type!="オンライン資料" and $md_type!="語り"){
    $string .="<tr><th>目次\n";
    $string .="<td><input type='text' name='mokuji' size='40' value='".$mokuji."'></td></tr>\n";
}
//作成日
$string .="<tr><th>作成・撮影日\n";
$string .="<td><input type='text' name='sakusei_nen' size='4' value='".$sakusei_nen."'>年（西暦）　";
$string .="<input type='text' name='sakusei_tuki' size='2     value='".$sakusei_tuki."'>月　";
$string .="<input type='text' name='sakusei_bi' size='2'      value='".$sakusei_bi."'>日";
$string .="</td></tr>\n";

//情報資源採取日
if ($md_type=="オンライン資料"){
    $string .="<tr><th>Online資料収集日\n";
    $string .="<td><input type='text' name='online_nen' size='4' value='".$online_nen."'>年（西暦）　";
    $string .="<input type='text' name='online_tuki' size='2' value='".$online_tuki."'>月　";
    $string .="<input type='text' name='onlilne_bi' size='2' value='".$online_bi."'>日";
    $string .="</td></tr>\n";
}
//公開日

if($md_type=="図書"){
	$pubDate = get_info('pubDate');
	$y = '';
	$m = '';
	$d = '';
	if($pubDate <>''){
		list($y, $m, $d) = explode("-", date("Y-m-d", strtotime($pubDate)));
	}
}
$string .="<tr><th>公開日\n";
$string .="<td><input type='text' name='koukai_nen' size='4' value='".$y."'>年（西暦）　";
$string .="<input type='text' name='koukai_tuki' size='2' value='".$m."'>月　";
$string .="<input type='text' name='koukai_bi' size='2' value='".$d."'>日";
$string .="</td></tr>\n";

//言語
if ($language=="JPN"){ $selected_JPN="selected";}
if ($language=="ENG"){ $selected_ENG ="selected";}
if ($language=="CHI"){ $selected_CHI ="selected";}
if ($language=="KOR"){ $selected_KOR ="selected";}
if ($language=="GER"){ $selected_GER ="selected";}
if ($language=="FRE"){ $selected_FRE ="selected";}
if ($language=="SPA"){ $selected_SPA ="selected";}
if ($language=="ITA"){ $selected_ITA ="selected";}
if ($language=="RUS"){ $selected_RUS ="selected";}
if ($language=="POR"){ $selected_POR ="selected";}
if ($language=="TGL"){ $selected_TGL ="selected";}

if ($md_type!="写真"){
    $string .="<tr><th>言語\n";
    $string .="<td><select name='language'>\n";
    $string .="   <option value='JPN' $selected_JPN>日本語</option>\n";
    $string .="   <option value='ENG' $selected_ENG>英語</option>\n";
    $string .="   <option value='CHI' $selected_CHI>中国語</option>\n";
    $string .="   <option value='KOR' $selected_KOR>韓国語</option>\n";
    $string .="   <option value='GER' $selected_GER>ドイツ語</option>\n";
    $string .="   <option value='FRE' $selected_FRE>フランス語</option>\n";
    $string .="   <option value='SPA' $selected_SPA>スペイン語</option>\n";
    $string .="   <option value='ITA' $selected_ITA>イタリア語</option>\n";
    $string .="   <option value='RUS' $selected_RUS>ロシア語</option>\n";
    $string .="   <option value='POR' $selected_POR>ポルトガル語</option>\n";
    $string .="   <option value='TGL' $selected_TGL>タガログ語</option>\n";
    $string .="</select>\n";
    $string .="</td></tr>";
}

//引用資料か
if ($inyou_flag==1){
    $string .="<tr><th>～の一部分である\n";
    $string .="<td><input type='text' name='is_bubun' size='40' value='".$is_bubun."'></td></tr>\n";
    $string .="<tr><th>親URIへの参照\n";
    $string .="<td><input type='text' name='oya_uri' size='40' value='".$oya_uri."'></td></tr>\n";
    $string .="<tr><th>参照する情報資源の名称\n";
    $string .="<td><input type='text' name='shigen_mei' size='40' value='".$shigen_mei."'></td></tr>\n";
    $string .="<tr><th>～を一部分として持つ\n";
    $string .="<td><input type='text' name='has_bubun' size='40' value='".$has_bubun."'></td></tr>\n";
    $string .="<tr><th>子URIへの参照\n";
    $string .="<td><input type='text' name='ko_uri' size='40' value='".$ko_uri."'></td></tr>\n";
}
//情報資源が対象とする場所
$string .="<tr><th></th><td></tr>";
$string .="<tr><th></th><td></tr>";
$string .="<tr><th>情報資源が対象とする場所(URI)<br>\n";
$string .="<input type='button' value='地図から取得'>\n";
$string .="<td><input type='text' name='taisho_basho_uri' size='40' value='".$taisho_basho_uri."'></td></tr>\n";
$string .="<tr><th>情報資源が対象とする場所（県名）\n";
$string .="<td><input type='text' name='taisho_basho_ken' size='40' value='".$taisho_basho_ken."'></td></tr>\n";
$string .="<tr><th>情報資源が対象とする場所（市町村）\n";
$string .="<td><input type='text' name='taisho_basho_shi' size='40' value='".$taisho_basho_shi."'></td></tr>\n";
$string .="<tr><th>情報資源が対象とする場所（街路番地）\n";
$string .="<td><input type='text' name='taisho_basho_banchi' size='40' value='".$taisho_basho_banchi."'></td></tr>\n";
$string .="<tr><th>情報資源が対象とする場所（緯度）\n";
$string .="<td><input type='text' name='taisho_basho_ido' size='40' value='".$taisho_basho_ido."'></td></tr>\n";
$string .="<tr><th>情報資源が対象とする場所（経度）\n";
$string .="<td><input type='text' name='taisho_basho_keido' size='40' value='".$taisho_basho_keido."'></td></tr>\n";

//配布場所とヨミ、配付日時、配付対象
if($md_type=="チラシ" or $md_type=="会議録・含資料"){
   $string .="<tr><th>配布場所<br>\n";
   $string .="<td><input type='text' name='haifu_basho' size='40' value='".$haifu_basho."'></td></tr>\n";
   $string .="<tr><th>配布場所のヨミ<br>\n";
   $haifu_basho_yomi = yomi($haifu_basho_yomi, mecab($haifu_basho));
   $string .="<td><input type='text' name='haifu_basho_yomi' size='40' value='".$haifu_basho_yomi."'></td></tr>\n";
   $string .="<tr><th>配付日時\n";
   $string .="<td><input type='text' name='haifu_nen' size='4' value='".$haifu_nen."'>年（西暦）　";
   $string .="<input type='text' name='haifu_tuki' size='2' value='".$haifu_tuki."'>月　";
   $string .="<input type='text' name='haifu_bi' size='2' value='".$haifu_bi."'>日";
   $string .="</td></tr>\n";
   $string .="<tr><th>配布対象<br>\n";
   $string .="<td><input type='text' name='haifu_taisho' size='40' value='".$haifu_taisho."'></td></tr>\n";
}

//掲示・設置場所等
if($md_type=="ポスター" or $md_type=="博物資料"){
   $string .="<tr><th>掲示・設置場所<br>\n";
   $string .="<td><input type='text' name='keiji_basho' size='40' value='".$keiji_basho."'></td></tr>\n";
   $string .="<tr><th>掲示・設置場所のヨミ<br>\n";
   $string .="<td><input type='text' name='keiji_basho_yomi' size='40' value='".$keiji_basho_yomi."'></td></tr>\n";
   $string .="<tr><th>掲示・配付日時\n";
   $string .="<td><input type='text' name='keiji_nen' size='4' value='".$keiji_nen."'>年（西暦）　";
   $string .="<input type='text' name='keiji_tuki' size='2' value='".$keiji_tuki."'>月　";
   $string .="<input type='text' name='keiji_bi' size='2' value='".$keiji_bi."'>日";
   $string .="</td></tr>\n";
}
//撮影場所
if($md_type=="音声・映像" or $md_type=="地図・地図帳" or $md_type=="" or $md_type=="写真" or $md_type=="語り" or $md_type=="絵画・絵はがき"){
   $string .="<tr><th>撮影場所（緯度）<br>\n";
   $string .="<input type='button' value='地図から取得'>\n"; 
   $string .="<td><input type='text' name='satusei_ido' size='40' value='".$satuei_ido."'></td></tr>\n";
   $string .="<tr><th>撮影場所（経度）\n";
   $string .="<td><input type='text' name='satuei_keido' size='40' value='".$satuei_keido."'></td></tr>\n"; 
   $string .="<tr><th>撮影場所（県名）\n";
   //とりあえず、撮影場所の住所を県のところに表示させておく
   //基本情報整理表には、複数が入力されている場合あり
   $string .="<td><input type='text' name='satuei_ken' size='40' value='".$satuei_basho_address."'></td></tr>\n";
   $string .="<tr><th>撮影場所（市町村）\n";
   $string .="<td><input type='text' name='satuei_shi' size='40' value='".$satuei_shi."'></td></tr>\n";
   $string .="<tr><th>撮影場所（街路番地）\n";
   $string .="<td><input type='text' name='satuei_banchi' size='40' value='".$satuei_banchi."'></td></tr>\n";
   $string .="<tr><th></th><td></tr>";
   $string .="<tr><th></th><td></tr>";
}
//博士論文
if($md_type=="図書" and $doctor_flag==1){
   $string .="<tr><th>学位<br>\n";
   //$string .="<td><input type='text' name='doctor' size='40' value='".$doctor."'></td></tr>\n";
   $string .="<tr><th>報告番号\n";
   $string .="<td><input type='text' name='doctor_bango' size='40' value='".$doctor_bango."'></td></tr>\n"; 
   $string .="<tr><th>授与年月日\n";
   $string .="<td><input type='text' name='doctor_nen' size='4' value='".$doctor_nen."'>年（西暦）　";
   $string .="<input type='text' name='doctor_tuki' size='2' value='".$doctor_tuki."'>月　";
   $string .="<input type='text' name='doctor_bi' size='2' value='".$doctor_bi."'>日";
   $string .="</td></tr>\n";
   $string .="<tr><th>授与大学\n";
   $string .="<td><input type='text' name='doctor_daigaku' size='40' value='".$doctor_daigaku."'></td></tr>\n";
   $string .="<tr><th>授与大学のヨミ\n";
   $string .="<td><input type='text' name='doctor_daigaku_yomi' size='40' value='".$doctor_daigaku_yomi."'></td></tr>\n";
   $string .="<tr><th></th><td></tr>";
}

$string .="<tr><th></th><td></tr>";
$string .="<tr><th></th><td></tr>";
if ($license_flag==1){
   $string .="<tr><th>ライセンス情報\n";
   $string .="<td><input type='text' name='license_info' size='40' value='".$license_info."'></td></tr>\n";
   $string .="<tr><th>ライセンス情報のURI\n";
   $string .="<td><input type='text' name='license_uri' size='40' value='".$license_uri."'></td></tr>\n";
   $string .="<tr><th>ライセンス保有者\n";
   $string .="<td><input type='text' name='license_holder' size='40' value='".$license_holder."'></td></tr>\n";
   $string .="<tr><th>権利・利用条件に関する注記\n";
   $string .="<td><input type='text' name='license_chuki' size='40' value='".$license_chuki."'></td></tr>\n";
   $string .="<tr><th></th><td></tr>";
   $string .="<tr><th></th><td></tr>";
}

//資料形態
if ($md_type!="音声・映像" and $md_type!="写真" and $md_type!="博物資料" and $md_type!="オンライン資料" and $md_type!="語り" and $md_type!="絵画・絵はがき" and $md_type!="プログラム（スマホアプリ・ゲーム等）"){
   $string .=" <tr><th>資料形態（大活字等)</th><td>\n";
   if ($shiryo_keitai=="")  { $selected_00="selected";}
   if ($shiryo_keitai=="03"){ $selected_03="selected";}
   if ($shiryo_keitai=="04"){ $selected_04 ="selected";}
   if ($shiryo_keitai=="05"){ $selected_05 ="selected";}
   if ($shiryo_keitai=="85"){ $selected_85 ="selected";}
   if ($shiryo_keitai=="06"){ $selected_06 ="selected";}
   if ($shiryo_keitai=="07"){ $selected_07 ="selected";}
   if ($shiryo_keitai=="08"){ $selected_08 ="selected";}
   if ($shiryo_keitai=="09"){ $selected_09 ="selected";}
   if ($shiryo_keitai=="10"){ $selected_10 ="selected";}
   if ($shiryo_keitai=="11"){ $selected_11 ="selected";}
   if ($shiryo_keitai=="12"){ $selected_12 ="selected";}
   $string.=" <select name='shiryo_keitai'>\n";
   $string.="		<option value=''   $selected_00> 該当しない</option>\n"; 	
   $string.="		<option value='03' $selected_03> 大活字</option>\n";
   $string.="	    <option value='04' $selected_04> 文庫本</option>\n";
   $string.="		<option value='05' $selected_05> 新書</option>\n";
   $string.="		<option value='85' $selected_85> 絵本</option>\n";
   $string.="		<option value='06' $selected_06> 大型絵本</option>\n";
   $string.="		<option value='07' $selected_07> 紙芝居</option>\n";
   $string.="		<option value='08' $selected_08> 紙芝居舞台</option>\n";
   $string.="		<option value='09' $selected_09> かるた</option>\n";
   $string.="		<option value='10' $selected_10> 絵葉書</option>\n";
   $string.="		<option value='11' $selected_11> ちりめん本</option>\n";
   $string.="		<option value='12' $selected_12> 大型紙芝居</option>\n";
   $string.=" </select>\n";
   $string .="</td></tr>\n";
}
//博物資料の区分
if($md_type=="博物資料"){
   if ($hakubutu_kubun=="人工物")  { $selected_art="checked";}
   if ($hakubutu_kubun=="自然物")  { $selected_nat="checked";}
   $string.=" <tr><th>博物資料の区分</th><td>\n";
   $string.="<input type='radio' value='人工物' name = 'hakubutu_kubun' $selected_art>人工物\n";
   $string.="<input type='radio' value='自然物' name = 'hakubutu_kubun' $selected_nat>自然物\n";
   $string .="</td></tr>\n";
}
//書写資料
if($md_type=="図書" or $md_type=="記事" or $md_type=="雑誌・新聞" or $md_type=="文書・楽譜" or $md_type=="地図・地図帳" or $md_type=="ポスター" or $md_type=="チラシ" or $md_type=="会議録・含資料" or $md_type=="絵画・絵はがき"){
   if ($shosha_flag==0)  { $shosha_0="checked";}
   if ($shosha_flag==1)  { $shosha_1="checked";}
   $string.=" <tr><th>書写資料</th><td>\n";
   $string.="<input type='radio' value=0 name = 'shosha_flag' $shosha_0>該当しない\n";
   $string.="<input type='radio' value=1 name = 'shosha_flag' $shosha_1>該当する\n";
   $string .="</td></tr>\n";
}
//オンラインジャーナル
if($md_type=="記事" or $md_type=="雑誌・新聞"){
   if ($online_flag==0)  { $online_0="checked";}
   if ($online_flag==1)  { $online_1="checked";}
   $string.=" <tr><th>オンラインジャーナル（学術系）</th><td>\n";
   $string.="<input type='radio' value=0 name = 'online_flag' $online_0>該当しない\n";
   $string.="<input type='radio' value=1 name = 'online_flag' $online_1>該当する\n";
   $string .="</td></tr>\n";
}
//書誌データ
if($md_type=="図書" or $md_type=="雑誌・新聞" or $md_type=="記事"){
   if ($shoshi_flag==0)  { $shoshi_0="checked";}
   if ($shoshi_flag==1)  { $shoshi_1="checked";}	
	
   $string.=" <tr><th>書誌データ</th><td>\n";
   $string.="<input type='radio' value=0 name = 'shoshi_flag' $shoshi_0>該当しない\n";
   $string.="<input type='radio' value=1 name = 'shoshi_flag' $shoshi_1>該当する\n";
   $string .="</td></tr>\n";
}
//地図帳
if($md_type=="地図・地図帳"){
   if ($chizu_kubun==0)  { $chizu_kubun_0="checked";}
   if ($chizu_kubun==1)  { $chizu_kubun_1="checked";}
	
   $string.=" <tr><th>地図か地図帳か</th><td>\n";
   
   $string.="<input type='radio' value=1 name = 'chizu_kubun' $chizu_kubun_0>地図\n";
   $string.="<input type='radio' value=2 name = 'chizu_kubun' $chizu_kubun_1>地図帳\n";
   $string .="</td></tr>\n";
}

//立法資料
if ($rippou_flag==1){
   if($md_type!="博物資料" and $md_type!="絵画・絵はがき"){ 
      $string .="<tr><th>掲示・配付日時\n";
      $string .="<td><input type='text' name='sekou_nen' size='4' value='".$sekou_nen."'>年（西暦）　";
      $string .="<input type='text' name='sekou_tuki' size='2' value='".$sekou_tuki."'>月　";
      $string .="<input type='text' name='sekou_bi' size='2' value='".$sekou_bi."'>日";
      $string .="<tr><th>施行対象\n";
      $string .="<td><input type='text' name='sekou_taisho' size='40' value='".$sekou_taisho."'></td></tr>\n";
   }
}
//閲覧制限
$string .="<tr><th>情報の質\n";
if ($seigen==0)  { $seigen_0="checked";}
if ($seigen==1)  { $seigen_1="checked";}
$string .="<td><input type='radio' name='seigen' value='0' $seigen_0>該当しない\n";
$string .="    <input type='radio' name='seigen' value='1' $sengen_1>悲惨、ショックを与える</td></tr>\n";
$string .="</table>\n";
//次画面の変数引き渡し
$string.="<input type='hidden' name='gov_issue' value='".$gov_issue."'>\n";
$string.="<input type='hidden' name='gov_issue2' value='".$gov_issue2."'>\n";
$string.="<input type='hidden' name='gov_issue_chihou' value='".$gov_issue_chihou."'>\n";
$string.="<input type='hidden' name='gov_issue_miyagi' value='".$gov_issue_miyagi."'>\n";
$string.="<input type='hidden' name='for_handicapped' value='".$for_handicapped."'>\n";
$string.="<input type='hidden' name='origina_shiryo_keitai' value='".$origina_shiryo_keitai."'>\n";
$string.="<input type='hidden' name='rippou_flag' value='".$rippou_flag."'>\n";

$string.="<input type ='submit' value='確認・登録'>\n";
$string.="<input type='button'  value='前画面へ' onClick='history.back();'>\n";
$string.="</form>";
	
echo $string;

?>





<!--
</form>
<input type="submit" name='next' value="登録して次へ">
<input type="submit" name='quit' value="中断">
--->
	
</div>
</body>
</html>
<?php
function numArray($start,$end,$unknown = false){
	$a = array();
	if($unknown)$a[-1] = '';
	for($i=$start;$i<=$end;$i++){
		$a[$i] = $i;
	}
	return $a;
}
function outputSelect($name,$options,$value,$valueiskey = false){
	global $data;
	$value = $data[$name];
	echo "<select name='$name'>";
	foreach($options as $key => $val){
		if($valueiskey)$key = $val;
		$selected = ($value == $key)? "selected='selected'": '';
		echo "<option value='$key' $selected>$val</option>";
	}
	echo "</select>";
}
function outputText($name,$value,$imeDisable=false,$size=70){
	global $data;
	$value = $data[$name];
	$class = $imeDisable? 'imeDisable':'';
	echo "<input type='text' name='$name' value='$value' size=$size class='$class'>";
}
?>
