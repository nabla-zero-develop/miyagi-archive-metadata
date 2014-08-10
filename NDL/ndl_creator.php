<?php
require_once(dirname(__FILE__) . "/opensearch.php");

class ndl_creator {
	public static function request($s, $return_type = 'string'){
		return ndl_request('creator', $s,  $return_type);
	}
}

?>
