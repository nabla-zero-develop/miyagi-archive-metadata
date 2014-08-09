<?php
	//DB設定
	mysql_connect('localhost','root','');
	mysql_select_db('metadata_system_');
	mysql_query('set names utf8');

	//メタデータ付与対象ファイルのルート
	$file_basepath=mb_convert_encoding("",'SJIS','UTF-8');

	//mbの設定
	mb_language('Japanese');
	mb_internal_encoding('UTF-8');
	mb_detect_order('SJIS,EUC-JP,JIS,UTF-8,UTF-16LE,ASCII');

	session_start();

?>
