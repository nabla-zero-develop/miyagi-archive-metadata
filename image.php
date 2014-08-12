<?php
require_once(dirname(__FILE__) . '/include/config.php');
$image = $_REQUEST['image'];

//TODO:不正入力防止
//if(preg_match('!(^|/)../!',$image))die();

header('Content-Type: ' . mime_content_type($image));
header("Content-Length: " . filesize($image));

readfile($image);
?>