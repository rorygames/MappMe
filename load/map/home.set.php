<?php

session_start();

if(!isset($_SESSION['mapped'])){
	echo '1You do not have sufficient priviliges to perform this action.</p><p>Your session may have expired.';
	exit;
}

if(!isset($_POST['mmlat'],$_POST['mmlong'],$_POST['zoom'])){
	echo '1Invalid input, please try again.';
	exit;
}

$username = $_SESSION['usn'];
$mmlat = $_POST['mmlat'];
$mmlong = $_POST['mmlong'];
$zoom = $_POST['zoom'];

require_once('../../includes/conn/conn.php');

try{
	$pdo = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$setHome = $pdo->prepare('UPDATE `'.$mm_db['prefix'].'users` SET starting_lat = :la, starting_long = :lo, zoom = :zm WHERE username = :usn');
	$setHome->bindValue(':la',$mmlat,PDO::PARAM_STR);
	$setHome->bindValue(':lo',$mmlong,PDO::PARAM_STR);
	$setHome->bindValue(':zm',$zoom,PDO::PARAM_INT);
	$setHome->bindValue(':usn',$username,PDO::PARAM_STR);
	$setHome->execute();
	echo '0Home position saved.';
	$pdo=null;
} catch(PDOException $ex){
	echo '1'.$ex;
}

?>