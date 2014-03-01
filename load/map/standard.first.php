<?php

session_start();

if(!isset($_SESSION['mapped'])){
	echo '1You do not have sufficient priviliges to perform this action. Your session may have expired.';
	exit;
}

if(!isset($_POST['ac'])){
	echo '1Invalid input, please try again.';
	exit;
}

$username = $_SESSION['usn'];
$ac = $_POST['ac'];

require_once('../../includes/conn/conn.php');

try{
	$pdo = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$addAC = $pdo->prepare('UPDATE `'.$mm_db['prefix'].'users` SET access_code = :ac, firstlog = 0 WHERE username = :usn');
	$addAC->bindValue(':ac',$ac,PDO::PARAM_STR);
	$addAC->bindValue(':usn',$username,PDO::PARAM_STR);
	$addAC->execute();
	echo '0';
} catch(PDOException $ex){
	echo '1'.$ex;
	exit;
}


?>