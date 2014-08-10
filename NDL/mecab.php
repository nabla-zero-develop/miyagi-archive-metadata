<?php
require_once(dirname(__FILE__) . "/utils.php");
define('MECAB1',  '/usr/local/bin/mecab');
define('MECAB2',  '/usr/bin/mecab');

class mecab {
	public static function request($s){
		$args = ' -Oyomi ';
		$r = "";
		//echo $s;
		if(file_exists(MECAB1)){
			$r = command(MECAB1.$args, $s);
		} else if(file_exists(MECAB2)){
			$r = command(MECAB2.$args, $s);
		} else {
			//echo "MeCab not found.\n";
			return NULL;
		}
		//var_dump($r);
		if(isset($r[0][0])){
			return trim($r[0][0]);
		}
		return NULL;
	}
}
?>
