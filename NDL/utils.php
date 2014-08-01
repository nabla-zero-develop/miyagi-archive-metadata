<?php
date_default_timezone_set('Asia/Tokyo');

// 引数の取得
function get_arg($args, $tag){
	if (array_key_exists($tag, $args) && $args[$tag] <> ""){
		$value = $args[$tag];
	} else {
		$value = "";
	}
	return $value;
}

// 標準入力を与えてLinuxのCUIコマンドを実行し、その結果を配列で返す
function command($prog, $input='', $encode='UTF-8') {
	$temp_file = tempnam(sys_get_temp_dir(), 'process_open_error_log_');
	$results = array();
	//echo $prog."\n";
	//echo $input."\n";
	if(function_exists('proc_open')){
		$dspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array('file', $temp_file, 'a'));
		$process = proc_open($prog, $dspec, $pipes);
		if(is_resource($process)) {
			fwrite($pipes[0], mb_convert_encoding($input, $encode, 'auto') . "\n");
			fclose($pipes[0]);
			$outputs = array();
			while(!feof($pipes[1])) {
				array_push($outputs, mb_convert_encoding(fgets($pipes[1], 1024), $encode, 'auto'));
			}
			fclose($pipes[1]);
			foreach($outputs as $output){
				array_push($results, split("\t", $output));
			}
		}
	}
	unlink($temp_file);
	return $results;
}

// 日付の書式をYYYYmmddにして文字列として返す
function conv_date($s){
	return date("Y-m-d", strtotime($s));
}

// 著者名表記の標準化
function clean_author($s){
	$s = preg_replace('/\s+\［*共*著\］*,*\s*$/','',$s);
	$s = preg_replace('/\s+\［*共*編\］*,*\s*$/','',$s);
	$s = preg_replace('/\s+\［*共*訳\］*,*\s*$/','',$s);
	$s = preg_replace('/\s+\［*他*著\］*,*\s*$/','',$s);
	$s = preg_replace('/\s+\［*他*編\］*,*\s*$/','',$s);
	$s = preg_replace('/\s+\［*他*訳\］*,*\s*$/','',$s);
	$s = preg_replace('/\s+他,*\s*$/','',$s);
	$s = preg_replace('/\s*,.*$/','',$s);
	return $s;
}


?>
