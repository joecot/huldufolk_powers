<?php

include('init.php');

if(($handle = fopen("powers.csv", "r")) === FALSE) die('Error opening file');

$headers = fgetcsv($handle, 0, ",");
$headers = array_map("trim", $headers);
print_r($headers);

while(($row = fgetcsv($handle, 0, ",")) !==FALSE){
	$row = array_map("trim", $row);
	$item = array_combine($headers,$row);
	//print_r($item);
	switch($item['type']){
		case 'sphere':{
			print_r($item);
			$sphere = $db->getSphere(Array('name' => $item['name']));
			if($sphere){
				$query = "update spheres set description='".$db->escape($item['description'])."' where id='$sphere[id]'";
			}else{
				$set = $db->set(Array('name' => $item['name'], 'description' => $item['description']));
				$query = "insert into spheres set $set";
			}
			echo $query."\n";
			$db->query($query);
			break;
		}
		case 'path':{
			$sphere = $db->getSphere(Array('name' => $item['sphere']));
			if(!$sphere) die('Cannot find sphere: '.$item['sphere']);
			
			$path = $db->getPath(Array('name' => $item['name']));
			if($path){
				if($path['sphere'] != $sphere['id']) die('Sphere ID mismatch');
				$db->query("update paths set description='".$db->escape($item['description'])."' where id='$path[id]'");
			}else{
				if(strpos($item['name'],'Lesser') !== FALSE) $flat = 1;
				else $flat = 0;
				$set = $db->set(Array('name' => $item['name'], 'description' => $item['description'], 'sphere' => $sphere['id'], 'flat' => $flat));
				$query = "insert into paths set $set";
				$db->query($query);
			}
			break;
		}
		case 'level':{
			print_r($item);
			$path = $db->getPath(Array('name' => $item['path']));
			if(!$path) die('Cannot find path: '.$item['path']);
			$level = $db->getLevel(Array('path' =>$path['id'], 'level' => $item['level']));
			if($level){
				$db->query("update levels set description='".$db->escape($item['description'])."' where id='$level[id]'");
			}else{
				$set = $db->set(Array(
					'name' => $item['name'],
					'description' => $item['description'],
					'level' => $item['level'],
					'path' => $path['id']));
				$query = "insert into levels set $set";
				$db->query($query);
			}
			break;
		}
	}
}