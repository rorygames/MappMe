<?php

$mm_settings;
$user_settings;

require_once(getcwd().'/includes/conn/conn.php');

function loadSettings(){
	global $mm_settings,$mm_db;
	try{
		$mm_set_db = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$mm_set_load = $mm_set_db->prepare('SELECT * FROM `'.$mm_db['prefix'].'config`');
		$mm_set_load->execute();
		$mm_set_res = $mm_set_load->fetchAll();
		$mm_set_count = count($mm_set_res);
		$mm_set_rounds = 0;
		while($mm_set_rounds < $mm_set_count){
			$mm_pre_set[0][$mm_set_res[$mm_set_rounds]['type']] = $mm_set_res[$mm_set_rounds]['value'];
			$mm_set_rounds++;
		}
		$mm_settings = $mm_pre_set[0];
	} catch(PDOException $ex){
		echo $ex;
		exit;
	}
}

loadSettings();

function grabSetting($setting){
	global $mm_settings;
	if(array_key_exists($setting,$mm_settings)){
		return $mm_settings[$setting];
	} else {
		return 0;
	}	
}

function docRoot(){
	return grabSetting('BASE_PATH').'/';
}

function incRoot(){
	$inc = docRoot().'includes/';
	return $inc;
}

function siteUrl(){
	global $mm_settings;
	$url = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.grabSetting('SITE_URL').'/' :  'https://'.grabSetting('SITE_URL').'/';
	return $url;
}

function getBaseUrl(){
	global $mm_settings;
	$url = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.grabSetting('SITE_URL').'/' :  'https://'.grabSetting('SITE_URL').'/';
	$url .= $_SERVER["REQUEST_URI"];
	return $url1[0];
}

if(!isset($mm_quick_page)){
	require_once(incRoot().'checks.php');
}

?>