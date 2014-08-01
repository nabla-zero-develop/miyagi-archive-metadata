<?php
include_once(dirname(__FILE__) . "/opensearch.php");

class ndl_isbn {
	public static function request($s, $return_type = 'string'){
		return ndl_request(’isbn’, $s,  $return_type);
	}
}
?>
