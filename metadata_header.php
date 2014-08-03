<?php

function output_header(){
	return <<< EOS
<html>
<script type="text/javascript" src="js/jquery/jquery-1.8.0.min.js"></script>
EOS;
}

function output_css($show__image_flag){
	if($show_image_flag){
		$width = "100";
		$height = "100";
	} else {
		$width = "0";
		$height = "0";
	}
	return <<< EOS
<style>
#imageDiv{
	width: 00px;
	float: left;
	text-align: center;
}
#image{
	max-width: $width%;
	max-height: $height%;
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
EOS;
}

function output_item_script(){
	return <<< EOS
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

EOS;
}
