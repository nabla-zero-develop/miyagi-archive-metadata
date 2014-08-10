<?php

// 大域
$jquery = './js/jquery/jquery-1.8.0.min.js';

function output_header(){
	global $jquery;
	return <<< EOS
<!DOCTYPE html>
<html>
	<!-- header -->
		<!-- meta http-equiv="Content-Type" content="text/html; charset=UTF-8" -->
		<script type="text/javascript" src="{$jquery}"></script>
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
	max-width: $width%;
	max-height: $height%;
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
	font-weight: normal;
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
EOS;
}

function output_image_script($files){
	$ff = array();
	foreach($files as $file) $ff[] = "'$file'";
	$f = implode(',', $ff);
	return <<< EOS
<script>
var images = [$f];
var degree = 0;
function rotate(deg){
	degree = deg;
	var \$image = $('#image');
	\$image.css('transform','rotate('+deg+'deg)')
		.css('width','').css('height','').css('left',0).css('right',0);
	if(deg == 0){
		\$image
			.css('max-width',$('#imageWrap').css('width'))
			.css('max-height',$('#imageWrap').css('height'));
		\$image.css('top',0);
	}else{
		\$image
			.css('max-width',$('#imageWrap').css('height'))
			.css('max-height',$('#imageWrap').css('width'));
		\$image.css('top',(\$image.width()-\$image.height())/2);
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
});

function showOptional(){
	var type = $('select[name=md_type]').val();
	//alert(type);
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

// 遷移
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

// Enterサブミット防止
$(function() {
  $(document).on("keypress", "input:not(.allow_submit)", function(event) {
    return event.which !== 13;
  });
});


// 読み
function yomi(field, yomi_field, init_value) {
	s = $.trim($("input[name='" + field + "']").val());
	if(s.length == 0){s = init_value};
	if(s != ""){
		$.ajax({
			url: './mecab.php',
			dataType: 'text',
			data: {"s": s },
			success: function(data) {
				//alert(data);
				$("input[name='" + yomi_field + "']").val(data);
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
				$("input[name='title']").val(fields[1]);
				$("input[name='title_yomi']").val(fields[15]);
				$("input[name='creator']").val(fields[7]);
				$("input[name='publisher']").val(fields[19]);
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
				//	$("input[name='koukai_hi'").val(dt.getDay());
				//}
				$("input[name='creator_yomi'").val(yomi('creator', 'creator_yomi', ''));
			},
			error: function(data) {
				//alert("error:"+data);
			}
		} );
	};
}
</script>
EOS;
}
