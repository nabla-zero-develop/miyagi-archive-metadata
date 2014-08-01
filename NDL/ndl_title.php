<?php
include_once(dirname(__FILE__) . "/opensearch.php");

class ndl_title {
	public static function request($s, $return_type = 'string'){
		//echo $return_type;
		return ndl_request('title', $s,  $return_type);
	}
}

?>
