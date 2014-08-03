<?php
function get_data_from_db($lot_id, $id){
	mysql_connect('localhost','root','');
	mysql_select_db('metadata_system');
	mysql_query('set names utf8');

	$res = mysql_query("select count(*) as c from lotfiles where lotid=$lot_id");
	$row = mysql_fetch_assoc($res);
	if(!$row)die("No data for lot no. $lot_id.");
	$num_in_lot = $row['c'];
	$res = mysql_query("select * from lotfiles where lotid=$lot_id and id=$id");
	//
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
		return array($data, $num_in_lot, $uniqid, $files);
}

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
