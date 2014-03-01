<?php

session_start();

if(!isset($_SESSION['mapped'])){
	echo '1You do not have sufficient priviliges to perform this action. Your session may have expired.';
	exit;
}

require_once('../../includes/conn/conn.php');

function genAC($length){
	global $mm_db;
	// Generate a random access code for the first loads
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	// Check and make sure the access code isn't already in use.
	try{
		$acPDO = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$acCheck = $acPDO->prepare('SELECT access_code FROM `'.$mm_db['prefix'].'users` WHERE access_code = :ac');
		$acCheck->bindValue(':ac',$randomString,PDO::PARAM_STR);
		$acCheck->execute();
		$acRows = $acCheck->rowCount();
	} catch(PDOException $ex){
		echo $ex;
	}
	if($acRows == 0){
		$acPDO = null;
		return $randomString;
	} else {
		$acPDO = null;
		genAC($length);
	}
}

?>
<p class="c-h1-i">Your new access code is</p>
<h1 id="new-gen-code" class="c-h1-i"><?php echo genAC(8); ?></h1>
<p class="c-h1-i">Your access code is case sensitive.</p>
<input type="button" id="me-set-code" value="Save My Code">