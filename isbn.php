<?php
include_once(dirname(__FILE__) . "/NDL/NDL.php");
$tag = 's';
if (array_key_exists($tag, $_GET) && $_GET[$tag] <> ""){
	echo NDL::ndl_isbn_info($_GET['s'], 'string');
} else {
	echo '';
}
?>
