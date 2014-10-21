<?php
require_once(dirname(__FILE__) . "/xml.php");

function identity($r, $dummy=NULL){
	return $r;
}

function attribute_str($key, $item, $callback = ""){
	$r = "";
	if (array_key_exists($key, $item)){
		$r = $item{$key};
		if($callback <> ""){
			$r = call_user_func($callback, $r);
		}
	}
	return $r;
}

function get_info_as_string($info_array){
	$attributes = array('title', // 0
								'link', // 2
								'description', // 4
								'author', // 6
								'category', // 8
								'guid', // 10
								'pubDate', // 12
								'dc_title', // 14
								'dcndl_titleTranscription', // 16
								'dc_creator', // 18
								'dcndl_seriesTitle', // 20
								'dcndl_seriesTitleTranscription', // 22
								'dc_publisher', // 24
								'dcterms_issued'); // 26
	$r = '';
	foreach($info_array as $info){
		foreach($attributes as $k){
			$r = $r.$k."\t".(isset($info[$k]) ? (is_array($info[$k])?implode(';',$info[$k]):$info[$k]) : '')."\t";
		}
		$r = $r."\n";
	}
// 未サポート部分(2階層以上のところ)
//         ["dc_identifier"]=>      array(2) {        [0]=>        string(13) "9784000069731"        [1]=>        string(8) "22120838"      }
//         ["dc_subject"]=>      array(3) {        [0]=>        string(15) "数理統計学"        [1]=>        string(5) "MA211"        [2]=>        string(3) "417"      }      ["dcterms_description"]=>      string(25) "文献あり 索引あり"      ["rdfs_seeAlso"]=>      array(5) {        [0]=>        array(1) {          ["@attributes"]=>          array(1) {            ["rdf_resource"]=>            string(33) "http://id.ndl.go.jp/bib/023600627"
	return $r;
}

function ndl_request($tag, $value, $return_type = 'string'){
	echo "$tag$value\n";
	$parsed = fetch_xml_as_array('http://iss.ndl.go.jp/api/opensearch?', array($tag => $value), 'GET');
	$num_items = $parsed{"channel"}{"openSearch_totalResults"};
	if($return_type == 'string'){
		$return_fn = 'get_info_as_string';
	} else if ($return_type == 'array'){
		$return_fn = 'identity';
    }
	if($num_items == 0){
		return FALSE;
	} else if(isset($parsed{"channel"}{"item"})){
		$info = $parsed{"channel"}{"item"};
		if($num_items == 1){
			return $return_fn(array($info));
		} else {
			return $return_fn($info);
		}
	}
}


//array(2) {  ["@attributes"]=>  array(9) {
//    ["xmlns_dcterms"]=>    string(25) "http://purl.org/dc/terms/"
//    ["xmlns_rdf"]=>    string(43) "http://www.w3.org/1999/02/22-rdf-syntax-ns#"
//    ["xmlns_dcndl"]=>    string(29) "http://ndl.go.jp/dcndl/terms/"
//    ["version"]=>    string(3) "2.0"
//    ["xmlns_dc"]=>    string(32) "http://purl.org/dc/elements/1.1/"
//    ["xmlns_openSearch"]=>    string(39) "http://a9.com/-/spec/opensearchrss/1.0/"
//    ["xmlns_xsi"]=>    string(41) "http://www.w3.org/2001/XMLSchema-instance"
//    ["xmlns_rdfs"]=>    string(37) "http://www.w3.org/2000/01/rdf-schema#"
//    ["xmlns_dcmitype"]=>    string(28) "http://purl.org/dc/dcmitype/"  }
//    ["channel"]=>  array(8) {
//       ["title"]=>    string(57) "9784000069731 - 国立国会図書館サーチ OpenSearch"
//       ["link"]=>    string(54) "http://iss.ndl.go.jp/api/opensearch?isbn=9784000069731"
//       ["description"]=>    string(38) "Search results for isbn=9784000069731 "
//       ["language"]=>    string(2) "ja"
//       ["openSearch_totalResults"]=>    string(1) "1"
//       ["openSearch_startIndex"]=>    string(1) "1"
//       ["openSearch_itemsPerPage"]=>    array(0) {    }
//       ["item"]=>    array(18) {
//         ["title"]=>      string(115) "データ解析のための統計モデリング入門 _ 一般化線形モデル・階層ベイズモデル・MCMC"
//         ["link"]=>      string(51) "http://iss.ndl.go.jp/books/R100000002-I023600627-00"
//         ["description"]=>      string(611) "<p>岩波書店,9784000069731</p><ul><li>タイトル： データ解析のための統計モデリング入門 _ 一般化線形モデル・階層ベイズモデル・MCMC</li><li>タイトル（読み）： データ カイセキ ノ タメ ノ トウケイ モデリング ニュウモン _ イッパンカ センケイ モデル カイソウ ベイズ モデル エムシーエムシー</li><li>責任表示： 久保拓弥 著,</li><li>シリーズ名： 確率と情報の科学</li><li>シリーズ名（読み）： カクリツ ト ジョウホウ ノ カガク</li><li>NDC(9)： 417</li></ul>"
//         ["author"]=>      string(17) "久保拓弥 著,"
//         ["category"]=>      string(3) "本"
//         ["guid"]=>      string(51) "http://iss.ndl.go.jp/books/R100000002-I023600627-00"
//         ["pubDate"]=>      string(31) "Fri, 16 Nov 2012 09_00_00 +0900"
//         ["dc_title"]=>      string(115) "データ解析のための統計モデリング入門 _ 一般化線形モデル・階層ベイズモデル・MCMC"
//         ["dcndl_titleTranscription"]=>      string(181) "データ カイセキ ノ タメ ノ トウケイ モデリング ニュウモン _ イッパンカ センケイ モデル カイソウ ベイズ モデル エムシーエムシー"
//         ["dc_creator"]=>      string(16) "久保拓弥 著"
//         ["dcndl_seriesTitle"]=>      string(24) "確率と情報の科学"
//         ["dcndl_seriesTitleTranscription"]=>      string(46) "カクリツ ト ジョウホウ ノ カガク"
//         ["dc_publisher"]=>      string(12) "岩波書店"
//         ["dcterms_issued"]=>      string(4) "2012"
//         ["dc_identifier"]=>      array(2) {        [0]=>        string(13) "9784000069731"        [1]=>        string(8) "22120838"      }
//         ["dc_subject"]=>      array(3) {        [0]=>        string(15) "数理統計学"        [1]=>        string(5) "MA211"        [2]=>        string(3) "417"      }      ["dcterms_description"]=>      string(25) "文献あり 索引あり"      ["rdfs_seeAlso"]=>      array(5) {        [0]=>        array(1) {          ["@attributes"]=>          array(1) {            ["rdf_resource"]=>            string(33) "http://id.ndl.go.jp/bib/023600627"          }        }        [1]=>        array(1) {          ["@attributes"]=>          array(1) {            ["rdf_resource"]=>            string(75) "http://opacsvr01.library.pref.nara.jp/mylimedio/search/book.do?bibid=497130"          }        }        [2]=>        array(1) {          ["@attributes"]=>          array(1) {            ["rdf_resource"]=>            string(82) "http://www.library.pref.fukui.jp/winj/opac/switch-detail-iccap.do?bibid=1105089047"          }        }        [3]=>        array(1) {          ["@attributes"]=>          array(1) {            ["rdf_resource"]=>            string(55) "http://web.oml.city.osaka.lg.jp/webopac_i_ja/0012523484"          }        }        [4]=>        array(1) {          ["@attributes"]=>          array(1) {            ["rdf_resource"]=>            string(80) "http://www.lib.pref.fukuoka.jp/winj/opac/switch-detail-iccap.do?bibid=1109350060"          }        }      }    }  }}

?>
