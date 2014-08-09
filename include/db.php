<?php
function mysql_get_multi_rows($query,$die_in_error = true){
	$res = mysql_query($query);
	if($res){
		$rows =array();
		while($row = mysql_fetch_assoc($res)){
			$rows[] = $row;
		}
		return $rows;
	}elseif($die_in_error){
		mysql_die_in_error();
	}else{
		return false;
	}
}

function mysql_get_single_row($query,$die_in_error = true){
	$res = mysql_query($query);
	if($res){
		$row = mysql_fetch_assoc($res);
		if(!$row) return false;
		return $row;
	}elseif($die_in_error){
		mysql_die_in_error();
	}else{
		return false;
	}
}

function mysql_get_value($query,$die_in_error = true){
	$res = mysql_query($query);
	if($res){
		$row = mysql_fetch_array($res);
		if(!$row) return false;
		return $row[0];
	}elseif($die_in_error){
		mysql_die_in_error();
	}else{
		return false;
	}
}

function mysql_die_in_error(){
	if(mysql_error()){
		die(mysql_erro());
	}
}
?>