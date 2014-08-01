<?php

include_once(dirname(__FILE__) . "/cache.php");
const cache_key = 'web_ndl_auth::request';

class web_ndl_auth {
	// NDLにSPARQLクエリを渡し、JSONで返す
	// json 取得例:
	//{ "head": { "vars": [ "auth", "entity", "preflabel", "yomi" ] }, 
	//  "results": { "bindings": [ { "auth": { "type": "uri", "value": "http://id.ndl.go.jp/auth/ndlna/00054222" }, 
	//                                       "entity": { "type": "uri", "value": "http://id.ndl.go.jp/auth/entity/00054222" }, 
	//                                      "preflabel": { "type": "literal", "value": "\u590f\u76ee, \u6f31\u77f3, 1867-1916" }, 
	//                                      "yomi": { "type": "literal", "value": "\u30ca\u30c4\u30e1, \u30bd\u30a6\u30bb\u30ad, 1867-1916", "xml:lang": "ja-Kana" } } ] } } 
	public static function request_json($query){
		$json = array();
		try{
			$c = cache::get(cache_key, $query);
			if($c <> NULL){
				$json_str = $c;
			} else {
				//echo "sparql:".$query."\n";
				$url = 'http://id.ndl.go.jp/auth/ndla?query=' . urlencode($query)  . '&output=json';
				//echo "uri:".$url."\n";
				$context = stream_context_create(array('http' => array('ignore_errors' => true, 'timeout'=>10)));
				$json_str = file_get_contents($url, FALSE, $context);            
				if($json_str === FALSE){
					if(count($http_response_header) > 0){//$http_response_headerは、file_get_contents関数によって自動的に設定される
						list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);
						switch ($status_code) {
							case '200':
								break;
							case '404':
								break;
							default:
								break;
						}
					} else {
						//echo "TIMEOUT!\n";
					}
			}
			//var_dump($http_response_header);
			//var_dump($json_str);
			//echo $http_response_header; 
			//echo "json str:".$json_str."\n";
			}
				cache::set(cache_key, $query, $json_str, 3600);
				$j = json_decode($json_str, true);
				$json = $j{'results'}{'bindings'};
		} catch (Exception $e) {
				$json = NULL;
		}
	// finally  {	//  PHP5.5-
	//	return $json;
	//}
		return $json;
	}
}
?>
