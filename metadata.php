<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/jquery/jquery-1.8.0.min.js"></script>
<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT);
require_once('include/config.php');

$lotid = intval($_GET['lotid']);
$resume = isset($_GET['resume'])?$_GET['resume']:false;
$uniqid = isset($_GET['uniqid'])?$_GET['uniqid']:0;
if(!is_numeric($uniqid))die('uniqidが不正です');

//編集対象を確定
if($uniqid){
	$res = mysql_query("select * from lotfile where uniqid=$uniqid");
	$row = mysql_fetch_assoc($res);
	if(!$row)die("No data for uniqid $uniqid.");
}else{
	if($resume){
		$res = mysql_query("select * from lotfile where finish = 0 and lotid=$lotid order by ord");
	}else{
		$res = mysql_query("select * from lotfile where lotid=$lotid order by ord");
	}
	$row = mysql_fetch_assoc($res);
	if(!$row) die("No data");
	$uniqid = $row['uniqid'];
}

//ロット内のデータ数
$res = mysql_query("select uniqid from lotfile where lotid=$lotid order by ord");
$num_in_lot = mysql_num_rows($res);
$actualord = 1;
while($row2 = mysql_fetch_assoc($res)){
	if($row2['uniqid'] == $uniqid)break;
	$actualord++;
}

$filedir = $file_basepath.mb_convert_encoding($row['filepath'],'SJIS','UTF-8');
$files = glob($filedir.'/*');

$res = mysql_query("select * from content where uniqid=$uniqid");
$data = mysql_fetch_assoc($res);
if(!$data){
	$data = array(
'md_type' => '','md_title' => '','md_copywriter' => '','md_copywriter_other' => '','md_copyrigher_uri' => '','md_copyrighter_yomi' => '','md_content_year' => '-1','md_content_month' => '-1','md_content_day' => '-1','md_content_hour' => '-1','md_content_min' => '-1','md_content_sec' => '-1','md_publish_year' => '-1','md_publish_month' => '-1','md_publish_day' => '-1','md_setting_year' => '-1','md_setting_month' => '-1','md_seting_day' => '-1','md_setting_place' => '','md_issue_for' => '','md_issue_year' => '-1','md_issue_month' => '-1','md_issue_day' => '-1','md_narrator' => '','md_content_restriction' => ''
	);
}

$res = mysql_query("select * from baseinfo where uniqid=$uniqid");
$baseinfo = mysql_fetch_assoc($res);
$selection1 = $selection2 = $selection3 = $selection4 = $selection5 = '';
if($baseinfo){
	if ($baseinfo['shubetu']=="v"){ $selection1="selected"; } //映像
	if ($baseinfo['shubetu']=="p"){ $selection2="selected"; } //チラシ
	if ($baseinfo['shubetu']=="d"){ $selection3="selected"; } //文書
	if ($baseinfo['shubetu']=="b"){ $selection4="selected"; } //図書・雑誌
	if ($baseinfo['shubetu']=="s"){ $selection5="selected"; } //音声

}

?>
<style>
#formDiv{
	width: 500px;
	height: 100%;
	overflow: scroll;
	float: right;
}
#imageDiv{
	width: 1350px;
	float: left;
	text-align: center;
}
#image{
	max-width: 100%;
	max-height: 100%;
	display: block;
	position: absolute;
}
#imageWrap{
	width: 1350px;
	height: 1000px;
}
th{
	background-color: #9292FF;
	border: 2px solid #ffffff;
}
td{
	border: 2px solid #ffffff;
	background-color: #CEE3F6;
	font-weight: normal;
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
	var $image = $('#image');
	$image.css('transform','rotate('+deg+'deg)')
		.css('width','').css('height','').css('left',0).css('right',0);
	if(deg == 0){
		$image
			.css('max-width',$('#imageWrap').css('width'))
			.css('max-height',$('#imageWrap').css('height'));
		$image.css('top',0);
	}else{
		$image
			.css('max-width',$('#imageWrap').css('height'))
			.css('max-height',$('#imageWrap').css('width'));
		$image.css('top',($image.width()-$image.height())/2);
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
var quit = false;
function setQuit(tf){
	quit = tf;
}
function check(){
 var flag=0;
 if (document.input_form.md_type.value =="" && !quit){
 	 flag=1;
  }
  if (flag){
  	  window.alert('資料種別を選択して下さい');
  	  return false;
  } else {
  	  return true;
  }
}

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

</div>

<div id='formDiv'>
<p>
<h4>ロットNo.<?php printf("%03d",$lotid); ?></h4>
<?php echo "$actualord/$num_in_lot"; ?><br>

<form name="input_form" method ="post" action="write.php" onSubmit="return check()">
<table>
<tr><th>ユニークID</th><td><?php echo $uniqid ?></td></tr>

<?php $md_type='';$series_flag='';$betu_title_flag='';$kiyo_flag='';$iban_flag='';$license_flag='';$inyou_flag='';$gov_issue='';$gov_issue_2='';$gov_issue_chihou='';$gov_issue_miyagi='';$for_handicapped='';$media_code='';$rippou_flag='';$doctor_flag='';$standard_id='';$title='';$title_yomi='';$series_title='';$series_title_yomi='';$betu_series_title='';$betu_series_title_yomi='';$betu_title='';$betu_title_yomi='';$naiyo_saimoku_chosha='';$naiyo_saimoku_title='';$naiyo_saimoku_title_yomi='';$buhenmei='';$buhenmei_yomi='';$makiji_bango='';$makiji_bango_yomi='';$creator='';$creator_yomi='';$contributor='';$contributor_yomi='';$iban='';$iban_chosha='';$publisher='';$subject='';$chuuki='';$youyaku='';$mokuji='';$sakusei_nen='';$sakusei_tuki='';$sakusei_bi='';$online_nen='';$online_tuki='';$online_bi='';$koukai_nen='';$koukai_tuki='';$koukai_bi='';$language='';$is_bubun='';$oya_uri='';$shigen_mei='';$has_bubun='';$ko_uri='';$taisho_basho_uri='';$taisho_basho_ken='';$taisho_basho_shi='';$taisho_basho_banchi='';$taisho_basho_ido='';$taisho_basho_keido='';$satuei_ken='';$satuei_shi='';$satuei_banchi='';$satuei_keido='';$satusei_ido='';$kanko_hindo='';$kanko_status='';$kanko_kanji='';$doctor='';$doctor_bango='';$doctor_nen='';$doctor_tuki='';$doctor_bi='';$doctor_daigaku='';$doctor_daigaku_yomi='';$keisai_go1='';$keisai_go2='';$keisai_shimei='';$keisai_kan='';$keisai_page='';$open_level='';$license_info='';$license_uri='';$license_holder='';$license_chuki='';$origina_shiryo_keitai='';$hakubutu_kubun='';$shosha_flag='';$online_flag='';$teller='';$teller_yomi='';$haifu_basho='';$haifu_basho_yomi='';$haifu_nen='';$haifu_tuki='';$haifu_bi='';$haifu_taisho='';$keiji_basho='';$keiji_basho_yomi='';$keiji_nen='';$keiji_tuki='';$keiji_bi='';$shoshi_flag='';$chizu_kubun='';$seigen=''; ?>

<!--資料種別選択-->
<tr><th class='hissu'>資料種別</th><td>
	<select name = "md_type">
	   <option value=''></option>
       <option value='図書' <?php echo $selection4; ?>>図書</option>
       <option value='記事'>記事</option>"
       <option value='雑誌・新聞' >雑誌・新聞</option>
       <option value='音声・映像' <?php echo $selection5; ?>>音声・映像</option>
       <option value='文書・楽譜' <?php echo $selection3; ?>>文書・楽譜</option>
       <option value='地図・地図帳'>地図・地図帳</option>"
       <option value='ポスター'>ポスター</option>
       <option value='写真' <?php echo $selection1; ?>>写真</option>
       <option value='チラシ' <?php echo $selection2; ?>>チラシ</option>
       <option value='会議録・含資料'>会議録・含資料</option>
       <option value='博物資料'>博物資料</option>
       <option value='オンライン資料'>オンライン資料</option>
       <option value='語り'>語り</option>
       <option value='絵画・絵はがき'>絵画・絵はがき</option>
       <option value='プログラム（スマホアプリ・ゲーム等）'>プログラム（スマホアプリ・ゲーム等）</option>
    </select>

    <!--シリーズか否か-->
    <tr><th class='hissu'>シリーズ（継続資料）</th><td>
	<label><input class='optctrl' type="radio" value=0 name = "series_flag" checked >該当しない　</label>
	<label><input class='optctrl' type="radio" value=1 name = "series_flag">該当する　</label>
    </td></tr>
    <!--別タイトルの有無-->
    <tr><th>別タイトルの有無</th><td>
	<input class='optctrl' type="radio" value=0 name = "betu_title_flag" checked >無　
	<input class='optctrl' type="radio" value=1 name = "betu_title_flag">有　
    <!--寄与者の有無-->
    <tr><th>寄与者（寄贈者）の有無</th><td>
	<input class='optctrl' type="radio" value=0 name = "kiyo_flag" checked >無　
	<input class='optctrl' type="radio" value=1 name = "kiyo_flag">有　
	<!--異版の有無-->
    <tr class='optional optional_図書 optional_雑誌・新聞'><th>異版<font size='-1'>（第x版、改訂版等）</font></th><td>
	<input class='optctrl' type="radio" value=0 name = "iban_flag" checked >該当しない　
	<input class='optctrl' type="radio" value=1 name = "iban_flag">該当する　
	<!--ライセンスの有無-->
    <tr><th>ライセンス(CC等)の有無</th><td>
	<input class='optctrl' type="radio" value=0 name = "license_flag" checked >無　
	<input class='optctrl' type="radio" value=1 name = "license_flag">有　
	<!--引用資料-->
    <tr><th>引用資料<br><font size='-1'>親となる資料からの引用</font></th><td>
	<input class='optctrl' type="radio" value=0 name = "inyou_flag" checked >該当しない　
	<input class='optctrl'type="radio" value=1 name = "inyou_flag">該当する

	<tr><th>政府刊行物・刊行元<br><font size='-1'>x省等が発行元</font></th><td>
	<select name = "gov_issue">
	   <option value='' selected>該当しない</option>
       <option value='AA0'> 衆議院</option>
       <option value='AB0'> 参議院</option>
       <option value='AC0'> 国立国会図書館</option>
       <option value='AD0'> 裁判官弾劾裁判所</option>
       <option value='AE0'> 裁判官訴追委員会</option>
       <option value='BA0'> 会計検査院</option>
       <option value='CA0'> 内閣</option>
       <option value='CB0'> 安全保障会議</option>
       <option value='CC0'> 人事院</option>
       <option value='DA0'> 内閣府</option>
       <option value='DB0'> 宮内庁</option>
       <option value='DC0'> 国家公安委員会</option>
       <option value='DD0'> 警察庁</option>
       <option value='DE0'> 防衛省</option>
       <option value='DF0'> 防衛施設庁</option>
       <option value='DG0'> 金融庁</option>
       <option value='EA0'> 総務省</option>
       <option value='EB0'> 公正取引委員会</option>
       <option value='EC0'> 公害等調整委員会</option>
       <option value='ED0'> 郵政事業庁</option>
       <option value='ED1'> 地方支分部局</option>
       <option value='EE0'> 消防庁</option>
       <option value='FA0'> 法務省</option>
       <option value='FA1'> 地方支分部局</option>
       <option value='FB0'> 司法試験管理委員会</option>
       <option value='FC0'> 公安審査委員会</option>
       <option value='FD0'> 公安調査庁</option>
       <option value='FE0'> 検察庁</option>
       <option value='GA0'> 外務省</option>
       <option value='HA0'> 財務省</option>
       <option value='HA1'> 地方支分部局</option>
       <option value='HB0'> 国税庁</option>
       <option value='KA0'> 文部科学省</option>
       <option value='KB0'> 文化庁</option>
       <option value='LA0'> 厚生労働省</option>
       <option value='LA1'> 地方支分部局</option>
       <option value='LB0'> 中央労働委員会</option>
       <option value='LC0'> 社会保険庁</option>
       <option value='MA0'> 農林水産省</option>
       <option value='MA1'> 地方支分部局</option>
       <option value='MB0'> 食糧庁</option>
       <option value='MC0'> 林野庁</option>
       <option value='MD0'> 水産庁</option>
       <option value='NA0'> 経済産業省</option>
       <option value='NA1'> 地方支分部局</option>
       <option value='NB0'> 資源エネルギー庁</option>
       <option value='NC0'> 特許庁</option>
       <option value='ND0'> 中小企業庁</option>
       <option value='PA0'> 国土交通省</option>
       <option value='PA1'> 地方支分部局</option>
       <option value='PB0'> 船員労働委員会</option>
       <option value='PC0'> 気象庁</option>
       <option value='PD0'> 海上保安庁</option>
       <option value='PE0'> 海難審判庁</option>
       <option value='RA0'> 環境省</option>
       <option value='SA0'> 最高裁判所</option>
       <option value='SB0'> 高等裁判所</option>
       <option value='SC0'> 地方裁判所</option>
       <option value='SD0'> 家庭裁判所</option>
       <option value='TA0'> 公団</option>
       <option value='TB0'> 事業団</option>
       <option value='TC0'> 公庫</option>
       <option value='TD0'> 基金</option>
       <option value='TE0'> 銀行</option>
       <option value='TF0'> その他</option>
       <option value='WA0'> 国立大学等</option>
       <option value='WB0'> 国立大学共同利用機関</option>
    </select>
	</td></tr>
   	<!--官公庁刊行物-->
    <tr><th>官公庁刊行物<br><font size='-1'>該当する場合刊行元（機関名）<br><font size='-1'>x省等の下部機関が発行元</font></th><td>
    <input type='text' name='gov_issue_2' size='40' value='該当しない'></td></tr>
    <!--地方公共団体刊行物 -->
    <tr><th>地方公共団体刊行物<br><font size='-1'>該当する場合刊行元（団体名）</font></th><td>
    <input type='text' name='gov_issue_chihou' size='40' value='該当しない'></td></tr>
    <!--宮城県内地方公共団体刊行物-->
    <tr><th>宮城県内地方公共団体刊行物<br><font size='-1'>宮城県内の自治体が発行元</font></th><td>
    <select name='gov_issue_miyagi'>
    	<option value='' selected>該当しない</option>
		<option value='4100'>仙台市</option>
		<option value='4202'>石巻市</option>
		<option value='4203'>塩竈市</option>
		<option value='4204'>古川市</option>
		<option value='4205'>気仙沼市</option>
		<option value='4206'>白石市</option>
		<option value='4207'>名取市</option>
		<option value='4208'>角田市</option>
		<option value='4209'>多賀城市</option>
		<option value='4210'>泉市</option>
		<option value='4211'>岩沼市</option>
		<option value='4212'>登米市</option>
		<option value='4213'>栗原市</option>
		<option value='4214'>東松島市</option>
		<option value='4215'>大崎市</option>
		<option value='4301'>蔵王町</option>
		<option value='4302'>七ヶ宿町</option>
		<option value='4321'>大河原町</option>
		<option value='4322'>村田町</option>
		<option value='4323'>柴田町</option>
		<option value='4324'>川崎町</option>
		<option value='4341'>丸森町</option>
		<option value='4361'>亘理町</option>
		<option value='4362'>山元町</option>
		<option value='4381'>岩沼町</option>
		<option value='4382'>秋保町</option>
		<option value='4401'>松島町</option>
		<option value='4402'>多賀城町</option>
		<option value='4403'>泉町</option>
		<option value='4404'>七ヶ浜町</option>
		<option value='4405'>宮城町</option>
		<option value='4406'>利府町</option>
		<option value='4421'>大和町</option>
		<option value='4422'>大郷町</option>
		<option value='4423'>富谷町</option>
		<option value='4424'>大衡村</option>
		<option value='4441'>中新田町</option>
		<option value='4442'>小野田町</option>
		<option value='4443'>宮崎町</option>
		<option value='4444'>色麻町</option>
		<option value='4444'>色麻村</option>
		<option value='4445'>加美町</option>
		<option value='4461'>松山町</option>
		<option value='4462'>三本木町</option>
		<option value='4463'>鹿島台町</option>
		<option value='4481'>岩出山町</option>
		<option value='4482'>鳴子町</option>
		<option value='4501'>涌谷町</option>
		<option value='4502'>田尻町</option>
		<option value='4503'>小牛田町</option>
		<option value='4504'>南郷町</option>
		<option value='4505'>美里町</option>
		<option value='4521'>築館町</option>
		<option value='4522'>若柳町</option>
		<option value='4523'>栗駒町</option>
		<option value='4524'>高清水町</option>
		<option value='4525'>一迫町</option>
		<option value='4526'>瀬峰町</option>
		<option value='4527'>鶯沢町</option>
		<option value='4528'>金成町</option>
		<option value='4529'>志波姫町</option>
		<option value='4530'>花山村</option>
		<option value='4541'>迫町</option>
		<option value='4542'>登米町</option>
		<option value='4543'>東和町</option>
		<option value='4544'>中田町</option>
		<option value='4545'>豊里町</option>
		<option value='4546'>米山町</option>
		<option value='4547'>石越町</option>
		<option value='4548'>南方町</option>
		<option value='4561'>河北町</option>
		<option value='4562'>矢本町</option>
		<option value='4563'>雄勝町</option>
		<option value='4564'>河南町</option>
		<option value='4565'>桃生町</option>
		<option value='4566'>鳴瀬町</option>
		<option value='4567'>北上町</option>
		<option value='4581'>女川町</option>
		<option value='4582'>牡鹿町</option>
		<option value='4601'>志津川町</option>
		<option value='4602'>津山町</option>
		<option value='4603'>本吉町</option>
		<option value='4604'>唐桑町</option>
		<option value='4605'>歌津町</option>
		<option value='4606'>南三陸町</option>
    </select>
    <!--視聴覚者向け資料-->
    <tr><th>視聴覚者向け資料</th><td>
    <select name='for_handicapped'>
       	<option value='' selected>該当しない</option>
    	<option value='Braille'>点字</option>
        <option value='DAISY'>DAISY</option>
		<option value='AudioBookInSoundD'>録音図書（DVD・CD）</option>
		<option value='AudioBookInSoundT'>録音図書（カセットテープ）</option>
    </select>

    <!--オリジナル資料の形態-->
    <?php
     $selection0="selected";
     if (isset($baseinfo['media_code'])&&$baseinfo['media_code']=="32"){ $selection32="selected"; } //カセット
     else $selection32 = '';
    ?>
    <tr><th>オリジナル資料の形態</th><td>
    <select name='origina_shiryo_keitai'>
    	<option value= ''  <?php echo $selection0; ?>>該当なし（その他）</option>
		<option value='31'>ＣＤ</option>
		<option value='32' <?php echo $selection32; ?>>カセット</option>
		<option value='33'>レコード</option>
		<option value='34'>リールテープ</option>
		<option value='35'>ＭＤ</option>
		<option value='36'>録音図書</option>
		<option value='39'>録音その他</option>
		<option value='41'>ビデオテープ</option>
		<option value='42'>ＬＤ</option>
		<option value='43'>ＤＶＤ</option>
		<option value='44'>ＥＬＩＢ</option>
		<option value='45'>ブルーレイディスク</option>
		<option value='46'>映像フィルム</option>
		<option value='49'>映像その他</option>
		<option value='51'>磁気テープ</option>
		<option value='52'>ＦＤ</option>
		<option value='53'>ＣＤ－ＲＯＭ</option>
		<option value='54'>ＭＯ</option>
		<option value='59'>機械その他</option>
		<option value='61'>ネガ・ポジ</option>
		<option value='62'>プリント</option>
		<option value='63'>スライぶんしょド</option>
		<option value='69'>写真その他</option>
		<option value='71'>楽譜</option>
		<option value='81'>マイクロＬ</option>
		<option value='82'>マイクロＣ</option>
		<option value='91'>別置解説書</option>
		<option value='99'>その他ＡＶ</option>
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
<td><input type='text' name='standard_id' value='' size='40'></td></tr>

<!--タイトル-->
<tr><th class='opthissu opthissu_図書 opthissu_記事 opthissu_新聞・雑誌 opthissu_音声・映像 opthissu_文書・楽譜 opthissu_地図・地図帳 opthissu_チラシ opthissu_会議録・含資料 opthissu_博物資料 opthissu_オンライン資料opthissu_語り opthissu_絵画・絵はがき opthissu_プログラム（スマホアプリ・ゲーム等）'>タイトル<BR>
<input type='button' value='NDLチェック'><br></th>
<td><input type='text' name='title' size='40' value='<?php echo $baseinfo['title']; ?>'></td></tr>

<!--タイトルのヨミ-->
<tr><th>タイトルのヨミ
<td><input type='text' name='title_yomi' size='40' value='<?php //mecab($title); ?>'></td></tr>
<!-- NDL問い合わせ
if($md_type=="図書"){
	$book_info = ndl_title_info($title);
}-->

<!--シリーズタイトル-->
<tr class='series_flag_option'><th class='opthissu opthissu_図書 opthissu_記事 opthissu_新聞・雑誌 opthissu_音声・映像 opthissu_文書・楽譜 opthissu_地図・地図帳 opthissu_チラシ opthissu_会議録・含資料 opthissu_博物資料 opthissu_オンライン資料opthissu_語り opthissu_絵画・絵はがき opthissu_プログラム（スマホアプリ・ゲーム等）'>シリーズタイトル
<td><input type='text' name='series_title' value='<?php //echo $series_title; ?>'size='40'></td>
<tr class='series_flag_option'><th class='opthissu opthissu_図書 opthissu_記事 opthissu_新聞・雑誌 opthissu_音声・映像 opthissu_文書・楽譜 opthissu_地図・地図帳 opthissu_チラシ opthissu_会議録・含資料 opthissu_博物資料 opthissu_オンライン資料opthissu_語り opthissu_絵画・絵はがき opthissu_プログラム（スマホアプリ・ゲーム等）'>シリーズタイトルのヨミ
<td><input type='text' name='series_title_yomi' value='<?php //echo $series_title_yomi; ?>' size='40'></td></tr>

<!--別タイトル-->
<tr class="betu_title_flag_option"><th>別タイトル　
<td><input type='text' name='betu_title' value='<?php //echo $betu_title; ?>' size='40'></td></tr>
<tr class="betu_title_flag_option"><th>別タイトルのヨミ
<td><input type='text' name='betu_title_yomi' value='<?php //echo $betu_title_yomi; ?>' size='40'></td></tr>

<!--別シリーズタイトル-->
<tr class='betu_title_flag_option'><th>別シリーズタイトル
<td ><input type='text' name='betu_series_title' value='<?php //echo $betu_series_title; ?>' size='40'></td></tr>
<tr class='betu_title_flag_option'><th>別シリーズタイトルのヨミ
<td><input type='text' name ='betu_series_title_yomi' value='<?php //echo $betu_series_title_yomi; ?>' size='40'></td></tr>

<!--内容細目-->
<tr class='optional optional_図書 optional_記事  optional_映像・音声  optional_文書・楽譜 optional_地図・地図帳'><th>内容細目タイトル
<td><input type='text' name='naiyo_saimoku_title' value='<?php //echo $naiyo_saimoku_title_yomi; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>内容細目タイトルのヨミ
<td><input type='text' name='naiyo_saimoku_title_yomi' value='<?php //echo $naiyo_saimoku_title_yomi; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>内容細目著者
<td><input type='text' name='naiyo_saimoku_chosha' value='<?php //echo $naiyo_saimoku_chosha; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>部編名
<td><input type='text' name='buhenmei' value='<?php //echo $buhenmei; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>部編名のヨミ
<td><input type='text' name='buhenmei_yomi' value='<?php //echo $buhenmei_yomi; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>巻次・部編番号
<td><input type='text' name='makiji_bango' value='<?php //echo $makiji_bango; ?>' size='40'></td></tr>
<tr class='optional optional_図書 optional_記事  optional_映像・音声 optional_文書・楽譜 optional_地図・地図帳'><th>巻次・部編番号のヨミ
<td><input type='text' name='makiji_bango_yomi' value='<?php //echo $makiji_bango_yomi; ?>' size='40'></td></tr>

<!--作成者・著者-->
<tr><th class='hissu'>作成者・著者名
<td><input type='text' name='creator' size='40' value='<?php echo $baseinfo['creator'];?>'></td></tr>
<!--図書の場合はNDLに問い合わせ、情報がなければMecabを使う
 if($md_type=="図書"){
	$creator_yomi = yomi($creator_yomi, ndl_creator_yomi($creator)) ;
}
$creator_yomi = yomi($creator_yomi, mecab($creator)) ;
$string .="<td><input type='text' name='creator_yomi' size='40' value='".$creator_yomi."'></td></tr>\n";
-->

<!--寄与者-->
<tr class='kiyo_flag_option'><th>寄与者（寄贈者）
<td><input type='text' name='contributor' size='40' value='<?php echo $baseinfo['contributor']; ?>'></td></tr>
<tr class='kiyo_flag_option'><th>寄与者（寄贈者）のヨミ
<!-- 澤田さん -->
<?php
$contributor_yomi = ($contributor_yomi <> '') ? $contributor_yomi : "";//mecab($contributor) ;
?>
<td><input type='text' name='contributor_yomi' size='40' value='<?php echo $baseinfo['contributor_yomi']; ?>'></td></tr>

<!--異版-->
<tr class='iban_flag_option'><th>異版名(第x版）
<td ><input type='text' name='iban' value='<?php //echo $iban; ?>' size='40'></td></tr>
<tr class='iban_flag_option'><th>異版の著者名
<td><input type='text' name='iban_chosha' value='<?php //echo $iban_chosha; ?>' size='40'></td></tr>

<!--出版社・公開者-->
<tr><th>出版社・公開者
<!--澤田さん-->
<?php
/*
if($md_type=="図書"){
	$publisher = get_info('dc_publisher');
} else {
	$publisher ="";
}
*/
?>
<td><input type='text' name='publisher' size='40' value='<?php //echo $publisher; ?>' ></td></tr>


<!--サブジェクト（キーワード）-->
<tr><th class='opthissu opthissu_音声・映像 opthissu_写真 opthissu_絵画・絵はがき' >主題（キーワード）
<td><input type='text' name='subject' size='40' value='<?php echo $baseinfo['keyword']; ?>'></td></tr>

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
<td><input type='text' name='sakusei_nen' size='4' value='<?php echo $baseinfo['sakusei_nen']; ?>'>年（西暦）
<input type='text' name='sakusei_tuki' size='2' value='<?php echo $baseinfo['sakusei_tuk']; ?>'>月
<input type='text' name='sakusei_bi' size='2' value='<?php echo $baseinfo['sakusei_bi']; ?>'>日
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
<input type='text' name='koukai_bi' size='2' value='<?php $d; ?>'>日
</td></tr>

<!--言語-->
<?php $language = 'JPN'?>
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
<td><input type='text' name='oya_uri' value='<?php echo $oya_uri; ?>' size='40'></td></tr>
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
<td><input type='text' name='taisho_basho_ken' value='<?php echo $taisho_basho_ken; ?>' size='40'></td></tr>
<tr><th>情報資源が対象とする場所（市町村）
<td><input type='text' name='taisho_basho_shi' value='<?php echo $taisho_basho_shi; ?>' size='40'></td></tr>
<tr><th>情報資源が対象とする場所（街路番地）
<td><input type='text' name='taisho_basho_banchi' value='<?php echo $taisho_basho_banchi; ?>' size='40'></td></tr>
<tr><th>情報資源が対象とする場所（緯度）
<td><input type='text' name='taisho_basho_ido' value='<?php echo $taisho_basho_ido; ?>' size='40'></td></tr>
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
<td><input type='text' name='keisai_shimei'  value='<?php echo $keisa_shimei; ?>' size='40'></td></tr>
<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載巻（論文の場合）
<td><input type='text' name='keisai_kan'  value='<?php echo $keisai_kan; ?>' size='40'></td></tr>
<tr class='optional optional_記事 optional_会議録・含資料'><th>掲載ページ
<td><input type='text' name='keisai_page'  value='<?php echo $keisai_page; ?>' size='40'></td></tr>

<!--アクセス制御-->
<tr><th class='hissu'>アクセス制御
<td><select name='open_level'>
	        <option value='0' <?php if ($baseinfo['open_level']=="0") { echo "selected"; } ?>>非公開</option>
            <option value='1' <?php if ($baseinfo['open_level']=="1") { echo "selected"; } ?>>公開</option>
            <option value='2' <?php if ($baseinfo['open_level']=="2") { echo "selected"; } ?>>限定公開</option>
            <option value='3' <?php if ($baseinfo['open_level']=="3") { echo "selected"; } ?>>公開保留</option>
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
   //$haifu_basho_yomi = yomi($haifu_basho_yomi, mecab($haifu_basho));
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
<tr><th>情報の質
<td><input type='radio' name='seigen' value='0' <?php if ($seigen==0) { echo "checked"; } ?>>該当しない
<input type='radio' name='seigen' value='1'     <?php if ($seigen==1) { echo "checked"; } ?>>悲惨（閲覧注意）</td></tr>

<?php
/*
if($md_type=="図書"){
	$pubDate = get_info('pubDate');
	$y = '';
	$m = '';
	$d = '';
	if($pubDate <>''){
		list($y, $m, $d) = explode("-", date("Y-m-d", strtotime($pubDate)));
	}
}
*/

//次画面の変数引き渡し
//$string.="<input type='hidden' name='gov_issue' value='".$gov_issue."'>\n";
//$string.="<input type='hidden' name='gov_issue2' value='".$gov_issue2."'>\n";
//$string.="<input type='hidden' name='gov_issue_chihou' value='".$gov_issue_chihou."'>\n";
//$string.="<input type='hidden' name='gov_issue_miyagi' value='".$gov_issue_miyagi."'>\n";
//$string.="<input type='hidden' name='for_handicapped' value='".$for_handicapped."'>\n";
//$string.="<input type='hidden' name='origina_shiryo_keitai' value='".$origina_shiryo_keitai."'>\n";
//$string.="<input type='hidden' name='rippou_flag' value='".$rippou_flag."'>\n";
//$string.="<input type ='submit' value='確認・登録'>\n";
//$string.="<input type='button'  value='前画面へ' onClick='history.back();'>\n";
//echo $string;
?>

</table>



<!--ここまで-->
<input type='hidden' name='lotid' value='<?php echo $lotid ?>'>
<input type='hidden' name='uniqid' value='<?php echo $uniqid ?>'>
<input type="submit" name='next' value="登録して次へ" onClick="setQuit(false);">
<input type="submit" name='quit' value="中断" onClick="setQuit(true);">
</form>
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
