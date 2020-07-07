<?php

function generate_sheet_url(){
	global $db;
	for($i = 0; $i < 100; $i++){
		$url = generate_random_string(4);
		$sheet = $db->getSheet(Array('url' => $url));
		if(!$sheet) return $url;
	}
	die('Unable to generate URL');
}

function generate_random_string($length){
	$characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
	$max = strlen($characters) - 1;
	if(function_exists('rand_int')) $func = 'rand_int';
	$func = 'rand';
	$string = '';
	for($i = 0; $i < $length; $i++){
		$string .= substr($characters,$func(0,$max),1);
	}
	return $string;
}

function index_redirect(){
	header("Location: /powers/");
	die();
}

include('init.php');

//print_r($_SERVER);
$path_info = explode('/',$_SERVER['REQUEST_URI']);
$path = '';
if(!empty($path_info[2])){
	if(strpos($path_info[2],'?') !== FALSE) list($path_info[2]) = explode('?',$path_info[2]);
	if(strpos($path_info[2],'#') !== FALSE) list($path_info[2]) = explode('#',$path_info[2]);
	$url_path = $path_info[2];
}
if(!empty($url_path) && strlen($url_path) == 4){
	$sheet = $db->getSheet(Array('url' => $url_path));
	if(!$sheet) index_redirect();
	$spheres = $db->getSheetPowers($sheet['powers']);
	if(empty($spheres)) index_redirect();
	include('display.php');
}elseif(!empty($_POST['path'])){
	foreach($_POST['path'] as $pathid => $level){
		if(!ctype_digit((string)$pathid) || !ctype_digit((string)$level)) die('Invalid input');
		if($level > 5) die('Invalid level');
		if($level < 1) unset($_POST['path'][$pathid]);
	}
	if(empty($_POST['path'])) index_redirect();
	ksort($_POST['path']);
	
	
	$powers = json_encode($_POST['path']);
	$crc = sprintf("%u",crc32($powers));
	$set = Array('crc' => $crc, 'powers' => $powers);
	$sheet = $db->getSheet($set);
	if(!$sheet){
		//need to add sheet
		$url = generate_sheet_url();
		$set['url'] = $url;
		$db->query("insert into sheets set ".$db->set($set));
		$id = $db->insert_id();
		if(!$id) die('Error creating url');
		$sheet = $db->getSheet(Array('id' => $id));
	}
	if($sheet){ //we already have this sheet
		header("Location: /powers/".$sheet['url']);
		die();
	}else{
		die('Error getting sheet');	
	}
}else{
	$spheres = $db->getSpheresPaths();
	include("form.php");
	
}
