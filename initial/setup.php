<?php

if(!isset($_POST['stage'])){
	echo "1Unable to get current stage.";
	exit;
}

$stage = $_POST['stage'];

require_once('../includes/setup/setup.class.php');

if(file_exists('../includes/conn/conn.php')){
	require_once('../includes/conn/conn.php');
}

$mm_setup = new Setup();

switch($stage){
	case 0:
		$dbname = $_POST['dbname'];
		$dbusn = $_POST['dbusn'];
		$dbpsw = $_POST['dbpsw'];
		$mm_setup->checkConnection($dbname,$dbusn,$dbpsw,"localhost");
	break;
	case 1:
		$dbname = $_POST['dbname'];
		$dbusn = $_POST['dbusn'];
		$dbpsw = $_POST['dbpsw'];
		$dbprefix = $_POST['dbpre'];
		$mm_setup->createDB($dbname,$dbusn,$dbpsw,$dbprefix,"localhost");
	break;
	case 3:
		$amusn = $_POST['adminusn'];
		$ampsw = $_POST['adminpsw'];
		$ampsw2 = $_POST['adminpsw2'];
		$surl = $_POST['surl'];
		$burl = $_POST['burl'];
		$mm_setup->createUser($mm_db['database'],$mm_db['admin']['usn'],$mm_db['admin']['psw'],$mm_db['prefix'],$mm_db['server'],$amusn,$ampsw,$surl,$burl);
	break;
}

?>