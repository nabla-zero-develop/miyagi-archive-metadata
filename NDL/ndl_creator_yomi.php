<?php

include_once(dirname(__FILE__) . "/web_ndl_auth.php");

class ndl_creator_yomi {
	// カナ情報を持つ著者の検索
	private static function build_query($creator){
		$q ="
				PREFIX rda: <http://RDVocab.info/ElementsGr2/>
				PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
				PREFIX foaf: <http://xmlns.com/foaf/0.1/>
				PREFIX owl: <http://www.w3.org/2002/07/owl#>
				PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
				PREFIX xsi: <http://www.w3.org/2001/XMLSchema-instance>
				PREFIX xl: <http://www.w3.org/2008/05/skos-xl#>
				PREFIX ndl: <http://ndl.go.jp/dcndl/terms/>
				PREFIX dcndl: <http://ndl.go.jp/dcndl/terms/>
				PREFIX dc: <http://purl.org/dc/elements/1.1/>
				PREFIX dct: <http://purl.org/dc/terms/>
				SELECT * WHERE {
					   ?auth   foaf:primaryTopic ?entity ;
							   xl:prefLabel [xl:literalForm ?preflabel ; ndl:transcription ?yomi] .
					   ?entity foaf:name \"" . $creator  . "\".
					   FILTER (lang(?yomi) = \"ja-Kana\")
					   }";
		return $q;
	}

	//姓名の読みから生年没年を外し、読みを空白でつないで返す
	private static function cleanup($s){
		$parts = explode(",",$s);
		$c = count($parts);
		if($c==0){
			$r = trim($s);
		} else if($c==1){
			$r = trim($parts[0]);
		} else if($c>=2){
			$r = trim($parts[0]) . " " . trim($parts[1]);
		}
		return $r;
	}

	//著者名からその読み仮名を国会図書館に問い合わせ、その結果をカタカナで返す
	public static function request($creator){
		$query = ndl_creator_yomi::build_query(str_replace(" ", "", $creator));
		//echo $query."\n";
		$json = web_ndl_auth::request_json($query);
		//echo $json."\n";
		//var_dump($json);
		if(isset($json[0]{'yomi'}{'value'})){
			$yomi = $json[0]{'yomi'}{'value'} ;
			//echo $yomi;
			return ndl_creator_yomi::cleanup($yomi);
		} else {
			return "";
		}
	}
}
?>
