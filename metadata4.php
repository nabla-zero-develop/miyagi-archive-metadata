<?php
mysql_connect('localhost','root','');
mysql_select_db('metadata_system');
mysql_query('set names utf8');

$lot_id = intval($_GET['lot']);
$id = isset($_GET['id'])?intval($_GET['id']):1;

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
	max-width: 100%;
	max-height: 100%;
	display: block;
}
#imageWrap{
	width: 00px;
	height: 00px;
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
function check(){
 var flag=0;
 if (document.input_form.md_type.value ==""){
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
<p>
<h4>ロットNo.<?php printf("%03d",$lot_id); ?></h4>
<?php echo "$id/$num_in_lot"; ?><br>

<form name="input_form" method ="post" action="metadata5.php" onSubmit="return check()">
<table>
<tr><th>ユニークID</th><td><?php echo $uniqid ?></td></tr>
	
<!--資料種別選択-->
<?php
$row_no     = $_GET['row_no'];
$ken_or_shi = $_REQUEST['ken_or_shi']; 

//基本情報整理表に書かれたデータ（行・列）を変数に格納
//県版と市町村県版で列が同じ項目

//A列：課室コード（県版）、市町村コード（市町村版）
$rc="r".$row_no."c0";
$local_code = $_REQUEST[$rc];
//B列：資料種別
$rc="r".$row_no."c1";
$shubetu = $_REQUEST[$rc];
if ($shubetu=="v"){ $selection1="selected"; } //映像
if ($shubetu=="p"){ $selection2="selected"; } //チラシ
if ($shubetu=="d"){ $selection3="selected"; } //文書
if ($shubetu=="b"){ $selection4="selected"; } //図書・雑誌
if ($shubetu=="s"){ $selection5="selected"; } //音声
//C列：課室（県版）、市町村（市町村版）管理番号
$rc="r".$row_no."c2";
$kanri_bango = $_REQUEST[$rc];
//D列：資料受領日
$rc="r".$row_no."c3";
$shiryo_jyuryobi = $_REQUEST[$rc];
//E列：資料提供者
$rc="r".$row_no."c4";
$contributor = $_REQUEST[$rc];
//F列：資料提供者のヨミ
$rc="r".$row_no."c5";
$contributor_yomi = $_REQUEST[$rc];
//G列：分類コード
$rc="r".$row_no."c6";
$bunrui_code = $_REQUEST[$rc];
//H列：文書分類記号（県版）、市町村分類（市町村版）
$rc="r".$row_no."c7";
$bunsho_bunrui = $_REQUEST[$rc];
//I列：タイトル
$rc="r".$row_no."c8";  //8行目：タイトル hiddenで次へ(このプログラムの470行目あたり）
$title = $_REQUEST[$rc];
//J列：撮影者・作成者
$rc="r".$row_no."c9";
$creator = $_REQUEST[$rc];
//K列：作成者のヨミ
$rc="r".$row_no."c10";
$creator_yomi = $_REQUEST[$rc];
//L列：作成日(年)
$rc="r".$row_no."c11";
$sakusei_nen = $_REQUEST[$rc];
//M列：作成日(月)
$rc="r".$row_no."c12";
$sakusei_tuki = $_REQUEST[$rc];
//N列：作成日(日)
$rc="r".$row_no."c13";
$sakusei_bi = $_REQUEST[$rc];
//O列：撮影場所（〒番号)
$rc="r".$row_no."c14";
$satuei_basho_zip = $_REQUEST[$rc];

//撮影場所住所
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c15";
   $satuei_basho_address = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c16";
   $satuei_basho_address = $_REQUEST[$rc];
}
//撮影場所住所のヨミ
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c16";
   $satuei_basho_address_yomi = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c17";
   $satuei_basho_address_yomi = $_REQUEST[$rc];
}

//配布場所
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c17";
   $haifu_basho = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c18";
   $haifu_basho = $_REQUEST[$rc];
}
//配布場所のヨミ
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c18";
   $haifu_basho_yomi = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c19";
   $haifu_basho_yomi = $_REQUEST[$rc];
}

//キーワード
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c19";
   $keyword = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c20";
   $keyword = $_REQUEST[$rc];
}

//作成者連絡先住所の〒番号
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c20";
   $renraku_saki_zip = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c21";
   $renraku_saki_zip = $_REQUEST[$rc];
}
//作成者連絡先住所
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c21";
   $renraku_saki_address = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c23";
   $renraku_saki_address = $_REQUEST[$rc];
}
//作成者連絡先電話番号
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c22";
   $renraku_saki_tel = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c24";
   $renraku_saki_tel = $_REQUEST[$rc];
}
//その他の作成者連絡先
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c23";
   $renraku_saki_other = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c25";
   $renraku_saki_other = $_REQUEST[$rc];
}

//権利処理（県版と市町村版で値制約が異なる。県版は「済」「未」、市町村版は9で済）
//なので、県版の「済」を9に書き換える
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c24";
   $kenri_shori = $_REQUEST[$rc];
   if ($kenri_shori=="済"){
   	   $kenri_shori=="9";
   }else{
   	   $kenri_shori=="0";
   }
}else{ //市町村版
   $rc="r".$row_no."c26";
   $kenri_shori = $_REQUEST[$rc];
   if ($kenri_shori != "9"){
       $kenri_shori = "0"; //未処理の場合は明示的に0を代入
   }
}

//公開レベル　県版と市町村版で値制約が異なる。県版は、公開の場合、「公開」で、
//市町村版の場合は１が公開、2が限定公開、3が公開保留なので市町村側に合わせて
//公開は1とする。県版のxは、扱いがわからないのでそのままとしておく。
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c25";
   $open_level = $_REQUEST[$rc];
   if ($open_level =="公開"){
   	   $open_level ="1";
   }
}else{
   $rc="r".$row_no."c27";
   $open_level = $_REQUEST[$rc];
}
//保留理由
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c26";
   $horyu_reason = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c28";
   $horyu_reason = $_REQUEST[$rc];
}
//媒体コード
if($ken_or_shi==0){ //県版
   $rc="r".$row_no."c30";
   $media_code = $_REQUEST[$rc];
}else{
   $rc="r".$row_no."c32";
   $media_code = $_REQUEST[$rc];
}

?>

<tr><th class='hissu'>資料種別</font></th><td>
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
	<input type="radio" value=0 name = "series_flag" checked >該当しない　
	<input type="radio" value=1 name = "series_flag">該当する　
    </td></tr>	
    <!--別タイトルの有無-->
    <tr><th>別タイトルの有無</th><td>
	<input type="radio" value=0 name = "betu_title_flag" checked >無　
	<input type="radio" value=1 name = "betu_title_flag">有　
    <!--寄与者の有無-->
    <tr><th>寄与者（寄贈者）の有無</th><td>
	<input type="radio" value=0 name = "kiyo_flag" checked >無　
	<input type="radio" value=1 name = "kiyo_flag">有　    
	<!--異版の有無-->
    <tr class='optional optional_図書' class='optional optional_雑誌・新聞'><th>異版<font size='-1'>（第x版、改訂版等）</font></th><td>
	<input type="radio" value=0 name = "ihan_flag" checked >該当しない　
	<input type="radio" value=1 name = "ihan_flag">該当する　
	<!--ライセンスの有無-->
    <tr><th>ライセンス(CC等)の有無</th><td>
	<input type="radio" value=0 name = "license_flag" checked >無　
	<input type="radio" value=1 name = "license_flag">有　
	<!--引用資料-->
    <tr><th>引用資料<br><font size='-1'>親となる資料からの引用</font></th><td>
	<input type="radio" value=0 name = "inyou_flag" checked >該当しない　
	<input type="radio" value=1 name = "inyou_flag">該当する　

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
   	<!--官公庁刊行物-->
    <tr><th>官公庁刊行物<br><font size='-1'>該当する場合刊行元（機関名）<br><font size='-1'>x省等の下部機関が発行元</font></th><td>
    <input type='text' name='gov_issue_2' size='40' value='該当しない'></td></tr>
    <!--地方公共団体刊行物 -->
    <tr><th>官公庁刊行物<br><font size='-1'>該当する場合刊行元（団体名）</font></th><td>
    <input type='text' name='gov_issue_chihou' size='40' value='該当しない'></td></tr>-->
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
     if ($media_code=="32"){ $selection32="selected"; } //カセット
    ?>
    <tr class='optional optional_図書 optional_記事 optional_雑誌・新聞 optional_音声・映像 optional_文書・楽譜 optional_地図・地図帳 optional_記事 optional_写真 optional_音声・映像 optional_会議録・含資料 optional_博物資料 optional_音声・映像 optional_語り optional_プログラム（スマホアプリ・ゲーム等）'>
    <th>オリジナル資料の形態</th><td>
    <select name='origina_shiryo_keitai'>
    	<option value= ''  <?php echo $selection0; ?>>該当しない</option>
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
	<input type="radio" value=0 name = "doctor_flag" checked >該当しない　
	<input type="radio" value=1 name = "doctor_flag">該当する　

</td></tr>
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
<input type="submit" value="決定">
</form>
	

<!--
<input type="submit" name='next' value="登録して次へ">
<input type="submit" name='quit' value="中断">
-->

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
