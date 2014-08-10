<?php
require_once(dirname(__FILE__) . "/ndl_creator_yomi.php");
require_once(dirname(__FILE__) . "/mecab.php");
require_once(dirname(__FILE__) . "/ndl_title.php");
require_once(dirname(__FILE__) . "/ndl_isbn.php");
require_once(dirname(__FILE__) . "/utils.php");

class NDL {
	// 汎用呼び出し用)
	private static function caller($class_name, $arg1, $arg2 = NULL){
		$r = FALSE;
		if($arg1 <> "" && $arg2 == NULL){
			$r = $class_name::request($arg1);
		} else if($arg1 <> "" && $arg2 <> NULL){
			$r = $class_name::request($arg1, $arg2);
		}
		return $r;
	}

	// MeCabによる汎用の読みの取得(文字列を返す)
	public static function mecab_yomi($s){
		return NDL::caller('mecab', $s);
	}

	// 国会図書館によるISBNからの情報取得
	public static function ndl_isbn_info($s, $return_type='string'){
		return NDL::caller('ndl_isbn', $s, $return_type);
	}

	// 国会図書館による著者名の読みの取得(文字列を返す)
	public static function ndl_creator_yomi($s){
		return NDL::caller('ndl_creator_yomi', $s);
	}

	// 国会図書館による書名からの情報取得
	public static function ndl_title_info($s, $return_type='string'){
		return NDL::caller('ndl_title', $s, $return_type);
	}

}
?>
