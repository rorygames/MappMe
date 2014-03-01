<?php

session_start();

if(!isset($_SESSION['mapped'])){
	echo '1You do not have sufficient priviliges to perform this action.</p><p>Your session may have expired.';
	exit;
}

if(!isset($_POST['pll'],$_POST['tl'],$_POST['ml'],$_POST['nc'],$_POST['nt'],$_POST['nla'],$_POST['nlo'])){
	echo '1There was an error with your input.</p><p>Please try again.';
	exit;
}

require_once('../../includes/conn/conn.php');

$me_set_arr = array();

$username = $_SESSION['usn'];
$polyl = $_POST['pll'];
$timel = $_POST['tl'];
$markl = $_POST['ml'];
$nocom = $_POST['nc'];
$notime = $_POST['nt'];
$nolat = $_POST['nla'];
$nolong = $_POST['nlo'];

if($polyl == 'true'){
	array_push($me_set_arr,'POLY_LINES');
}

if($nocom == 'true'){
	array_push($me_set_arr,'NO_COMMENTS');
}

if($notime == 'true'){
	array_push($me_set_arr,'NO_TIME');
}

if($nolat == 'true'){
	array_push($me_set_arr,'NO_LAT');
}

if($nolong == 'true'){
	array_push($me_set_arr,'NO_LONG');
}

$me_set_arr['MARK_TIME_LIMIT'] = $timel;
$me_set_arr['MARK_LIMIT'] = $markl;

$me_arr_serial = serialize($me_set_arr);

try{
	$me_set = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$me_set_p = $me_set->prepare('UPDATE `'.$mm_db['prefix'].'users` SET map_settings = :meset WHERE username = :usn');
	$me_set_p->bindValue(':usn',$username,PDO::PARAM_STR);
	$me_set_p->bindValue(':meset',$me_arr_serial,PDO::PARAM_STR);
	$me_set_p->execute();
} catch(PDOException $ex){
	echo '1'.$ex;
	exit;
}

echo '0';

?>