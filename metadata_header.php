<?php

// 大域
$jquery = './js/jquery/jquery-1.8.0.min.js';

function output_header(){
	global $jquery;
	return <<< EOS
<!DOCTYPE html>
<html>
	<!-- header -->
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<script type="text/javascript" src="{$jquery}"></script>
		<script src="js/jquery-ui-1.11.0.custom/jquery-ui.min.js"></script>
		<script src="js/jquery.mousewheel.min.js"></script>
		<script src="js/jquery.iviewer/jquery.iviewer.min.js"></script>
		<link rel="stylesheet" type="text/css" href="js/jquery.iviewer/jquery.iviewer.css" />
		<link rel="stylesheet" type="text/css" href="js/SpryTabbedPanels/SpryTabbedPanels.css" />
		<script src="js/SpryTabbedPanels/SpryTabbedPanels.js"></script>

EOS;
}

function output_css($show_image_flag){
	if($show_image_flag){
		$width = "100";
		$height = "100";
	} else {
		$width = "0";
		$height = "0";
	}
	return <<< EOS
<style>
#formDiv{
	width: 750px;
	height: 950px;
	overflow: scroll;
	float: right;
	font-size: 20px;
}
#imageDiv{
	width: 1100px;
	float: left;
}
#imageWrap{
	width: 1100px;
	height: 950px;
	position: relative;
}
th{
	background-color: #9292FF;
	border: 2px solid #ffffff;
	font-weight: normal;
	text-align: left;
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
    background-color: #ff8080;
}
input, select, textarea{
	font-size: 100%;
}
.iviewer_loading img{
        opacity: 0.5;
        -ms-filter: "alpha(opacity=50)";
}
.iviewer_loading {
	background: #ffffff url(loading.gif) no-repeat fixed 490px 365px;
}
</style>
EOS;
}

function output_image_script($files){
	$ff = array();
	foreach($files as $file) $ff[] = "'image.php?image=".urlencode($file)."'";
	$f = implode(',', $ff);
	return <<< EOS
<script>
var images = [$f];
var degree = 0;
var currImgIdx;
var zoom = null;
function rotate(deg){
	degree = deg;
	$('#imageWrap').iviewer('angle',deg,true);
}
function chgImage(idx){
	currImgIdx = idx;
	var \$div = $("#imageWrap");
	if(\$div.iviewer('info','src')){
		zoom = \$div.iviewer('info','zoom');
	}
	\$div.iviewer('loadImage', images[idx]);
	$('#imageIdx').val(idx+1);
}

function prevImage(){
	if(currImgIdx>0){
		chgImage(currImgIdx-1);
	}
}

function nextImage(){
	if(currImgIdx<images.length-1){
		chgImage(currImgIdx+1);
	}
}

function lastImage(){
	chgImage(images.length-1);
}

function zoom_in(){
	$('#imageWrap').iviewer('zoom_by',Math.sqrt(2));
}

function zoom_out(){
	$("#imageWrap").iviewer('zoom_by',-Math.sqrt(2));
}

function zoom_fit(){
	$("#imageWrap").iviewer('fit');
}

function iviewerFinishLoad(){
	var \$div = $("#imageWrap");
	if(zoom){
		\$div.iviewer('set_zoom',zoom);
	}
	var ow = \$div.iviewer('info','orig_width');
	var oh = \$div.iviewer('info','orig_height');
	var options = \$div.iviewer('info','options');
	var divw = \$div.width();
	var divh = \$div.height();
	var zoom_min = Math.min(divw/ow,divh/oh)*100;
	if(zoom_min > 100){zoom_min = 100;}
	options.zoom_min = zoom_min;
	rotate(degree);
}

var zoom = false;
$(document).ready(function(){
	if(images.length==1){
		$('#filename').html('1/1');
	}else{
		var size = (images.length<10?1:(images.length<100?2:3));
		$('#filename').css('color','#f00').css('font-size','130%')
			.html("<input type='text' size="+size+" id='imageIdx' class='imeDisable'>"+'/'+images.length+"<span id='aaa'></span>");
		$('#imageIdx').bind('change keyup',function(){
			var idx = $(this).val();
			if(idx>=1 && idx<=images.length){
				chgImage($(this).val()-1);
			}
		});

	}
	$('#imageWrap').iviewer({zoom_max: 100,zoom_min: 10,onFinishLoad: iviewerFinishLoad});
	chgImage(0);

	//IEのみ有効
	document.onhelp = function(){return false;};//F1キーでヘルプを抑止
	document.onkeydown = key_event;
});

function key_event(){
	// IE以外のブラウザではeventがundefinedになるので終了
	if(typeof event == "undefined")return;
    // 発生したイベントのキーコードを取得
	var code = event.keyCode;

	//F1-F12キーであれば、無効化する(F1キー：112,... F12キー:123)
	if(event.keyCode >= 112 && event.keyCode <= 123)
	{
		event.keyCode = null;
		event.returnValue = false;
	}
	//該当するキーコードで分岐。それぞれのcase内に、実行したい独自の処理を記述する。
	switch(code){
		// F1キー
		case 112:
			chgImage(0);
			break;
		// F2キー
		case 113:
			prevImage();
			break;
		// F3キー
		case 114:
			nextImage();
			break;
		// F4キー
		case 115:
			lastImage();
			break;
		// F5キー
		case 116:
			rotate(-90);
			break;
		// F6キー
		case 117:
			rotate(0);
			break;
		// F7キー
		case 118:
			break;
		// F8キー
		case 119:
			rotate(90);
			break;
		// F9キー
		case 120:
			zoom_in();
			break;
		// F10キー
		case 121:
			zoom_out();
			break;
		// F11キー
		case 122:
			zoom_fit();
			break;
		// F12キー
		case 123:
			break;
		default:
			return true;
			break;
	}
	return false;
}
</script>
EOS;
}

function output_item_script(){
	return <<< EOS
<script>
//資料種別による項目変更
//trタグを<tr class='optional optional_図書'>とすると図書が選択されたときに表示される
//<tr class='optional optional_type1 optional_type2'>等とすると複数の選択肢(type1およびtype2)で表示できる
$(document).ready(function(){
	$('select[name=md_type]').change(showOptional);
	showOptional();
	$('textarea,input').bind('change',function(){
		var s = $(this).val();
		if(s.indexOf("\\n")>-1 || s.indexOf("\\r")>-1 ){
			s = s.split("\\n").join("").split("\\r").join("");
			$(this).val(s);
		}
		if($(this).attr('name') && $(this).attr('name').match(/_yomi$/)){
			if(s.match(/[ぁ-ん]/)){
				s = s.replace(/[ぁ-ん]/g, function(ss) {
		   			return String.fromCharCode(ss.charCodeAt(0) + 0x60);
				});
				$(this).val(s);
			}
		}
	});
	$('#ord').change(ordChange);
});

function showOptional(){
	var type = $('select[name=md_type]').val();
	//alert(type);
	$('.optional').css('display','none');
	$('.optional_'+type).css('display','');
	$('.opthissu').removeClass('hissu');
	$('.opthissu_'+type).addClass('hissu');
	$('.optional').each(function(){
		if($(this).css('display') == 'none'){
			$(this).find('input.optctrl[value=0]').each(function(){
				showOptCtrl($(this).attr('name'),true);
			});
		}else{
			$(this).find('input.optctrl[value=0]').each(function(){
				showOptCtrl($(this).attr('name'));
			});
		}
	});
}

//class optctrl
$(document).ready(function(){
	$('input.optctrl').change(function(){showOptCtrl($(this).attr('name'))});
	$('input.optctrl[value=0]').each(function(){showOptCtrl($(this).attr('name'))});
});

function showOptCtrl(name,hide){
	if($('input[name='+name+']:checked').val()==1 && !hide){
		$('.'+name+'_option').css('display','');
	}else{
		$('.'+name+'_option').css('display','none');
	}
}

// 遷移
var skipCheck = 0;
function setSkipCheck(val){
	skipCheck = val;
}

function check(){
	if(skipCheck == 1){
		return true;
	}else if(skipCheck == 2){
		if(document.input_form.skip_reason.value == ""){
			window.alert('スキップする理由を入力してください');
			return false;
		}else{
			return true;
		}
	}

	var message = '';
	if(document.input_form.skip_reason.value != ""){
		message += "入力スキップの理由欄に記入されています\\n";
	}
	//if (document.input_form.md_type.value ==""){
	//	 message += "資料種別を選択して下さい\\n";
	//}
	$('tr').each(function(){
		var \$tr = $(this);
		if(\$tr.css('display') != 'none'){
			if(\$tr.children('th').hasClass('hissu')){
				if(\$tr.children('td').children('input').val() == '' || \$tr.children('td').children('select').val() == '' || \$tr.children('td').children('textarea').val() == ''){
					var text = \$tr.children('th').html();
					var text = text.split('<')[0];
					message += text+"が入力されていません\\n";
				}
			}
		}
	});
	if (message.length > 0){
		  window.alert(message);
		  return false;
	} else {
		  return true;
	}
}

// Enterサブミット防止
$(function() {
  $(document).on("keypress", "input:not(.allow_submit)", function(event) {
	return event.which !== 13;
  });
  $(document).on("keypress", "textarea:not(.allow_submit)", function(event) {
	return event.which !== 13;
  });
});


//Ctrl+Enterでサブミットさせる
$(document).keydown(function(ev){
	if(ev.keyCode == 13){
		if(ev.ctrlKey){
			setSkipCheck(0);
			$('form[name=input_form]').submit();
			return false;
		}
	}
});


// 読み
function yomi(field, yomi_field, init_value) {
	s = $("input[name='" + field + "']").val();
	if(s == undefined){s = $("textarea[name='" + field + "']").val();}
	\$yomi = $("input[name='" + yomi_field + "']");
	if(\$yomi.length == 0){
		\$yomi = $("textarea[name='" + yomi_field + "']");
	}
	s = $.trim(s);
	if(s.length == 0){s = init_value};
	if(s == "@" || s == "＠" ){
			\$yomi.val(s);
	}else if(s != ""){
		$.ajax({
			url: './mecab_proxy.php',
			dataType: 'text',
			data: {"s": s },
			success: function(data) {
				//alert(data);
				\$yomi.val(data);
				return data;
			},
			error: function(data) {
				//alert("error:"+data);
			}
		} );
	};
};

function ndl_check(){
	s = $.trim($("input[name='standard_id']").val());
	if(s != ""){
		$.ajax({
			url: './isbn.php',
			dataType: 'text',
			data: {"s": s },
			success: function(data) {
				fields = data.split("\t");
				if(fields.length>1){
					$("textarea[name='title']").val(fields[1]);
					$("textarea[name='title_yomi']").val(fields[17]);
					$("textarea[name='creator']").val(fields[7]);
					$("input[name='publisher']").val(fields[25]);
					//s = fields[21] + "";
					//alert(s.length() );
					//if(s.length() == 7){
					//	dt = Date.parse(fields[21]+"-01");
					//	$("input[name='koukai_nen'").val(dt.getYear());
					//	$("input[name='koukai_tsuki'").val(dt.getMonth());
					//} else {
					//	dt = Date.parse(fields[21]);
					//	$("input[name='koukai_nen'").val(dt.getYear());
					//	$("input[name='koukai_tsuki'").val(dt.getMonth());
					//	$("input[name='koukai_bi'").val(dt.getDay());
					//}
					//$("textarea[name='creator_yomi]'").val(yomi('creator', 'creator_yomi', ''));
					yomi('creator', 'creator_yomi', '');
				}else{
					alert('データが取得できませんでした');
				}
			},
			error: function(data) {
				//alert("error:"+data);
			}
		} );
	};
}
function ordChange(){
	var ord = $(this).val();
	resume = $('input[name=resume]').val();
	skip = $('input[name=skip]').val();
	lotid = $('input[name=lotid]').val();
	location.href = 'metadata.php?resume='+resume+'&skip='+skip+'&lotid='+lotid+'&ord='+ord
}
</script>
EOS;
}

function output_map_script(){
	return <<< EOS
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&language=ja"></script>
<script src="js/jquery-ui-1.11.0.custom/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="js/jquery-ui-1.11.0.custom/jquery-ui.min.css" />
<script>
	$(document).ready(function(){
		$('#mapDialog').dialog({
			autoOpen: false,
			buttons: {    // ボタンを設定
				"保存": function(event) {
					$('input[name='+addressPrefix+'basho_ken]').val($('#addressKen').text());
					$('input[name='+addressPrefix+'basho_shi]').val($('#addressShi').text());
					$('input[name='+addressPrefix+'basho_banchi]').val($('#addressBanchi').text());
					$('input[name='+addressPrefix+'basho_ido]').val($('#addressIdo').text());
					$('input[name='+addressPrefix+'basho_keido]').val($('#addressKeido').text());
					$(this).dialog("close");
        		},
        		"キャンセル": function() { $(this).dialog("close"); }
    		},
			width: 800,
			height: 800
		});
	})

	var addressPrefix = '';
	function getAddress(prefix){
		if(!mapInitialized){
			mapInitialized = true;
			init_google_map();
		}
		addressPrefix = prefix;
		$('#mapDialog').dialog("open");
	}

	var map;
	var marker;
	var initlat = 38.268839;
	var initlng = 140.872103;
	var mapInitialized = false;
	function init_google_map(){
		var map_div = document.getElementById('mapDiv');
		var initCenter = new google.maps.LatLng(initlat, initlng);
		var map_opts = {
			zoom: 14,
			center: initCenter,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scaleControl: true
		};

		map = new google.maps.Map( map_div, map_opts );

		marker = new google.maps.Marker({map:map, position:initCenter, draggable:true });

		google.maps.event.addListener(map, 'click', function(e) {
			marker.setPosition(e.latLng);
		});

	}
	function doRevGeoCode(){
		var markpos = marker.getPosition();
		$('#addressKen').text('取得中');
		$('#addressShi').text('');
		$('#addressBanchi').text('');
		$('#addressIdo').text(markpos.lat());
		$('#addressKeido').text(markpos.lng());
		//逆ジオコーディング
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode(
		{ latLng: markpos, region: 'jp' },
		function( results, status ){
			var addressKen='';var addressShi='';var addressBanchi='';
			var addressStr='';
			if (status == google.maps.GeocoderStatus.OK) {
				if(results && results.length > 0){
					//typesに"political"が含まれるものを探す
					for(var i in results){
						//console.log(results[i].formatted_address);
						var types = results[i].types;
						for(var j in types){
							//console.log(types[j]);
							if(types[j] == 'political'){
								addressStr = results[i].formatted_address;
								//break;
								components = results[i].address_components;
								for(var k in components){
									types2 = components[k].types;
									for(var l in types2){
										if(types2[l] == 'sublocality_level_1'||
												types2[l] == 'sublocality_level_2'||
												types2[l] == 'sublocality_level_3'||
												types2[l] == 'sublocality_level_4'||
												types2[l] == 'sublocality_level_5'){
											var postfix='';
											if((types2[l] == 'sublocality_level_3'||types2[l] == 'sublocality_level_4')&&addressBanchi.length>0){
												addressBanchi = '-'+addressBanchi;
												}
											addressBanchi = components[k].long_name+addressBanchi;
										}else if(types2[l] == 'locality'){
											addressShi = components[k].long_name+addressShi;
										}else if(
										types2[l] == 'administrative_area_level_1'){
											addressKen = components[k].long_name;
										}
									}
								}
								break;
							}
							if(addressStr.length > 0) break;
						}
					}
				}
			}
			if(addressKen.length>0){
				$('#addressKen').text(addressKen);
				$('#addressShi').text(addressShi);
				$('#addressBanchi').text(addressBanchi);
			}else{
				$('#addressKen').text('');
			}
		}
		);
	}
	</script>
EOS;
}
