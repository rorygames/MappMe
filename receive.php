<?php

// Check if system has been setup
if(!file_exists(getcwd().'/includes/conn/conn.php')){
	if(!file_exists(getcwd().'/includes/conn/site.php')){
		echo '1MappMe has not been configured on this server/url yet.';
		exit;
	}
}

// Latitude, longitude, comment, access code. Time, username, etc are handled by the server.
if(!isset($_POST['lat'],$_POST['long'],$_POST['com'],$_POST['ac'])) {
	echo '1Missing variables';
	exit;
}

$r_data = array(
	'lat' => $_POST['lat'],
	'long' => $_POST['long'],
	'com' => $_POST['com'],
	'ac' => $_POST['ac']
);

// Access code MUST equal to 8 characters.
// You can change this to fit your own system but remember to change the access code generation to match.
// Generation can be found in includes (map.class.php) and load (set.access.php)
if(strlen($r_data['ac']) != 8){
	echo '1Invalid access code';
	exit;
}

require_once(getcwd().'/includes/map/receive.class.php');
require_once(getcwd().'/includes/conn/conn.php');

$receive = new Receiver($mm_db);

$ac_result = $receive->checkAccess($r_data['ac']);

if($ac_result[0] != 0){
	echo $ac_result;
	exit;
}

echo $receive->storeMarker($r_data);

?>