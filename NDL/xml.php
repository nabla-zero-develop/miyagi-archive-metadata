<?php

// XMLオブジェクトを配列に変換する
 function xml_to_array($xmlobj){
    $arr = array();
    if (is_object($xmlobj)){
      $xmlobj = get_object_vars($xmlobj);
    } else {
      $xmlobj = $xmlobj;
    }

    foreach ($xmlobj as $key => $val) {
      if (is_object($xmlobj[$key])) {
        $arr[$key] = xml_to_array($val);
      } else if (is_array($val)) {
        foreach($val as $k => $v) {
          if (is_object($v) || is_array($v)) {
            $arr[$key][$k] = xml_to_array($v);
          } else {
            $arr[$key][$k] = $v;
          }
        }
      } else {
        $arr[$key] = $val;
      }
    }
    return $arr;
}

// XMLを取得して、配列として返す
function fetch_xml_as_array($api_url, $param, $type = 'GET'){
    $data = http_build_query($param, '', '&');
    $header = array('Content-Type: application/x-www-form-urlencoded', 'Content-Length: '.strlen($data));
    $context = stream_context_create(array('http' => array('method'  => $type, 'header'  => implode("\r\n", $header), 'content' => $data, 'timeout'=>10)));	
    $xml = file_get_contents($api_url, false, $context);
    if (false === $xml) return false;
    $xml2 = preg_replace("/<(\/*)(\w+):(\w+)/",'<${1}${2}_${3}',$xml);
    $name_space_replaced_xml = preg_replace("/<dc_identifier xsi:type=\"dcndl:ISBN\">(\d+)<\/dc_identifier>/",'<isbn>${1}</isbn>',$xml2);
    $parse_xml = simplexml_load_string($name_space_replaced_xml,'SimpleXMLElement', LIBXML_NOCDATA);
    if (false === $parse_xml) return false;
    return xml_to_array($parse_xml);
}
?>
