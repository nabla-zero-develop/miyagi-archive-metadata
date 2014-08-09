<?php
include_once(dirname(__FILE__) . "/NDL/NDL.php");
$tag = 's';
if (array_key_exists($tag, $_GET) && $_GET[$tag] <> ""){
	echo NDL::mecab_yomi($_GET['s']);
} else {
	echo '';
}
?>
