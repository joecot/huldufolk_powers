<?php

class DB{
	private $host;
	private $user;
	private $pass;
	private $database;
	private $db = false;
	function __construct($host, $user, $pass, $db){
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->database = $db;
	}
	function connect(){
		//echo "Connect DB?\n";
		if(!$this->db){
			$this->db = new mysqli($this->host, $this->user, $this->pass, $this->database);
			if ($this->db->connect_error) {
				die('Connect Error (' . $this->db->connect_errno . ') '
				. $this->db->connect_error);
			}//else echo "DB Connected\n";
		}//else echo "Have DB\n";
	}
	
	function query($query){
		$this->connect();
		//echo $query."\n";
		$res = $this->db->query($query);
		if($this->db->error){
			echo $query."\n";
			die('Query error: '.$this->db->error);
		}
		return $res;
	}
	
	function insert_id(){
		return $this->db->insert_id;
	}
	
	function escape($string){
		$this->connect();
		return $this->db->real_escape_string($string);
	}
	
	function set($values, $seperator=', '){
		$set = Array();
		foreach($values as $key => $value){
			$set[] = "`".$this->escape($key)."` = '".$this->escape($value)."'";
		}
		return implode($seperator, $set);
	}
	
	function ids($ids){
		foreach($ids as $key => $id){
			if(!ctype_digit((string)$id)) unset($ids[$key]);
		}
		return '('.implode(',',$ids).')';
	}
	
	function getSphere($search){
		if(!empty($search['id']) && is_numeric($search['id'])){
			$res = $this->query("select * from spheres where id='$value'");
		}elseif(!empty($search['name'])){
			$res = $this->query("select * from spheres where name='".$this->escape($search['name'])."'");
		}else return false;
		if(!$res->num_rows) return false;
		$sphere = $res->fetch_assoc();
		return $sphere;
	}
	function getSpheres($search = Array()){
		$where = '';
		if(!empty($search['sphereids'])){
			$where = "where id in ".$this->ids($search['sphereids']);
		}
		$select = 'id, name';
		if(!empty($search['full'])) $select='*';
		$res = $this->query("select $select from spheres $where order by id");
		$spheres = Array();
		if(!$res->num_rows) die('Error fetching spheres');
		while($sphere = $res->fetch_assoc()) $spheres[$sphere['id']] = $sphere;
		return $spheres;
	}
	function getPath($search){
		if(!empty($search['id']) && is_numeric($search['id'])){
			$res = $this->query("select * from paths where id='$value'");
		}elseif(!empty($search['name'])){
			$res = $this->query("select * from paths where name='".$this->escape($search['name'])."'");
		}else return false;
		if(!$res->num_rows) return false;
		$path = $res->fetch_assoc();
		return $path;
	}
	function getPaths($search = Array()){
		$where = '';
		if(!empty($search['sphere']) && is_numeric($search['sphere'])) $where = "where sphere='".$this->escape($sphere)."'";
		elseif(!empty($search['pathids'])){
			$where = "where id in ".$this->ids($search['pathids']);
		}
		$select = 'id, sphere, name';
		if(!empty($search['full'])) $select='*';
		$res = $this->query("select $select from paths $where order by sphere, id");
		$paths = Array();
		if(!$res->num_rows) die('Error fetching spheres');
		while($path = $res->fetch_assoc()) $paths[$path['id']] = $path;
		return $paths;
	}
	function getSpheresPaths(){
		$spheres = $this->getSpheres();
		$paths = $this->getPaths();
		foreach($paths as $path){
			$sphereid = $path['sphere'];
			$spheres[$sphereid]['paths'][$path['id']] = $path;
		}
		return $spheres;
	}
	
	function getLevel($search){
		if(!empty($search['id']) && is_numeric($search['id'])){
			$res = $this->query("select * from levels where id='$value'");
		}elseif(!empty($search['path']) && !empty($search['level']) && is_numeric($search['level'])){
			$res = $this->query("select * from levels where path='".$this->escape($search['path'])."' and level='".$this->escape($search['level'])."'");
		}else return false;
		if(!$res->num_rows) return false;
		$level = $res->fetch_assoc();
		return $level;
	}
	
	function getSheet($search){
		if(!empty($search['id']) && ctype_digit((string)$search['id'])){
			$res = $this->query("select * from sheets where id='$search[id]'");
		}elseif(!empty($search['url'])){
			$res = $this->query("select * from sheets where url='".$this->escape($search['url'])."'");
		}elseif(!empty($search['powers']) && !empty($search['crc'])){
			$res = $this->query("select * from sheets where ".$this->set(Array('crc' => $search['crc'], 'powers' => $search['powers']),' AND '));
		}else return false;
		if(!$res->num_rows) return false;
		$sheet = $res->fetch_assoc();
		return $sheet;
	}
	
	function getSheetPowers($powers){
		$powers = json_decode($powers,TRUE);
		if(!is_array($powers)){echo "no powers"; return false;}
		//echo "powers\n"; print_r($powers);
		$powers = array_filter($powers,function($level){ return ($level > 0 && $level <= 5);}); //filter out unbought paths
		//echo "filtered powers\n"; print_r($powers);
		if(empty($powers)) return false;
		$pathids = array_keys($powers);
		$paths = $this->getPaths(Array('pathids' => $pathids, 'full' => TRUE));
		if(empty($paths)) {echo "no paths"; return false;}
		$sphereids = array_column($paths,'sphere');
		$spheres = $this->getSpheres(Array('sphereids' => $sphereids, 'full' => TRUE));
		if(empty($spheres)) {echo "no spheres"; return false;}
		//echo "paths\n";print_r($paths);
		foreach($paths as $path){
			if(!isset($powers[$path['id']])) continue;
			$levelnum = $powers[$path['id']];
			if(!ctype_digit((string)$levelnum)) continue;
			if(!$path['flat']){
				$query = "select * from levels where path='$path[id]' and level <='$levelnum'";
				$res = $this->query($query);
				if(!$res || !$res->num_rows) continue;
				$path['levels'] = Array();
				while($level = $res->fetch_assoc()) $path['levels'][$level['level']] = $level;
			}
			$path['levelnum'] = $levelnum;
			$spheres[$path['sphere']]['paths'][$path['id']] = $path;
		}
		return $spheres;
	}
	
}