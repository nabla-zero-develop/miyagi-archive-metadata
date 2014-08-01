<?php

class cache{

	public static function set($id, $ags, $value, $valid_time) {
		return NULL;
	}

	public static function get($id, $ags) {
		return NULL;
	}

	// 以下仮
	function getCacheFile($idFunc="", $cacheTime=120) {
	  $cacheFile = "./tmp/".$idFunc; // キャッシュファイルの場所
	  if(!function_exists($idFunc)) {
	   return;
	  }
	  if(file_exists($cacheFile)) {
	   $time = filemtime($cacheFile);
	   if(($time + $cacheTime)>time()) {
		return unserialize(file_get_contents($cacheFile));
	   }
	  }
	  $body = call_user_func($idFunc);
	  $file = new SplFileObject($cacheFile, "w"); // PHP5以上
	  $file->fwrite(serialize($body));
	  return $body;
	}
}
?>
