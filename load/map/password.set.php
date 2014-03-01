<?php

session_start();

if(!isset($_SESSION['mapped'])){
	echo '1You do not have sufficient priviliges to perform this action.</p><p>Your session may have expired.';
	exit;
}

if(!isset($_POST['ol'],$_POST['npsw'],$_POST['npsw2'])){
	echo '1You do not have sufficient priviliges to perform this action.</p><p>Your session may have expired.';
	exit;
}

$curPassw = "";
$pdo;

$username = $_SESSION['usn'];
$oldPsw = $_POST['ol'];
$newPsw = $_POST['npsw'];
$newPsw2 = $_POST['npsw2'];

if($oldPsw == ""){
	echo '1Please do not leave the current password field blank.';
	exit;
}

$p_letter = preg_match('@[A-Za-z]@', $newPsw);
$p_number = preg_match('@[0-9]@', $newPsw);
$p_char = preg_match('@[\W_]@', $newPsw);

if($newPsw == ""){
	echo '1Please do not leave the new password field blank.';
	exit;
} else {
	if(!$p_letter||!$p_number||!$p_char||strlen($newPsw) < 7||strlen($newPsw) > 40){
		echo '1Please make sure you meet the password requirements.';
		exit;
	}
}

if($newPsw != $newPsw2){
	echo '1Your passwords do not match.</p><p>Please try again.';
	exit;
}

require_once('../../includes/conn/conn.php');
require_once('../../includes/conn/site.php');

try{
	$pdo = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$usnCheck_p = $pdo->prepare('SELECT password FROM `'.$mm_db['prefix'].'users` WHERE username = :usn');
	$usnCheck_p->bindValue(':usn',$username,PDO::PARAM_STR);
	$usnCheck_p->execute();
	$usnCheck_res = $usnCheck_p->fetch(PDO::FETCH_ASSOC);
	$curPassw = $usnCheck_res['password'];
	if(validatePassword($oldPsw,$mm_site['salt'],$curPassw) == true){
		$pswUpd = $pdo->prepare('UPDATE `'.$mm_db['prefix'].'users` SET password = :psw WHERE username = :usn');
		$pswUpd->bindValue(':usn',$username,PDO::PARAM_STR);
		$pswUpd->bindValue(':psw',encryptPassword($newPsw,$mm_site['salt']),PDO::PARAM_STR);
		$pswUpd->execute();
	} else {
		echo '1Your current password is incorrect.';
		$pdo=null;
		exit;
	}
} catch(PDOException $ex){
	echo '1'.$ex;
	exit;
}
$pdo=null;
echo '0Your password has been updated.';

function validatePassword($psw,$salt,$hash){
	return (encryptPassword($psw,$salt) == $hash);
}

function encryptPassword($psw,$salt){
	return hash('sha512',$psw . $salt);
}

?>