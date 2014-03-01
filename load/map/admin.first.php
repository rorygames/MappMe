<?php

session_start();

if($_SESSION['permissions'] != 2){
	echo '1You do not have sufficient priviliges to perform this action. Your session may have expired.';
	exit;
}

if(!isset($_POST['gmk'],$_POST['ac'])){
	echo '1Invalid input, please try again.';
	exit;
}

$username = $_SESSION['usn'];
$gmKey = $_POST['gmk'];
$ac = $_POST['ac'];
$alreadykeyd = 0;

require_once('../../includes/conn/conn.php');

try{
	$pdo = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['admin']['usn'], $mm_db['admin']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$checkKey = $pdo->prepare('SELECT * FROM `'.$mm_db['prefix'].'config` WHERE type = "API_KEY"');
	$checkKey->execute();
	$checkKrow = $checkKey->rowCount();
	if($checkKrow == 0){
		$addKey = $pdo->prepare('INSERT INTO `'.$mm_db['prefix'].'config`(type,value) VALUES("API_KEY",:key)');
		$addKey->bindValue(':key',$gmKey,PDO::PARAM_STR);
		$addKey->execute();
	} else{
		$alreadykeyd++;
	}
	$addAC = $pdo->prepare('UPDATE `'.$mm_db['prefix'].'users` SET access_code = :ac, firstlog = 0 WHERE username = :usn');
	$addAC->bindValue(':ac',$ac,PDO::PARAM_STR);
	$addAC->bindValue(':usn',$username,PDO::PARAM_STR);
	$addAC->execute();
	if($alreadykeyd == 0){
		echo '0';
	} else {
		echo '2There is already an API key is use.</p><p>To prevent improper changing please edit it via your database administration software.</p><p>Refresh this page to start using MappMe</p><p>Your account details have been saved.';
	}
} catch(PDOException $ex){
	echo '1'.$ex;
	exit;
}


?>