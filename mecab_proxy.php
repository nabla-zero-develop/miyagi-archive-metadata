<?php
$URL = 'http://116.58.166.134/mecab/mecab.php?s='.urlencode($_GET['s']);
$context = stream_context_create(array(
		'http' => array('ignore_errors' => true)
));
$body = file_get_contents($URL, false, $context);
//http_response_code();
//var_dump($http_response_header);
foreach($http_response_header as $header)header($header);
echo $body;

?>
