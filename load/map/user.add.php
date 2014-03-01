<?php

session_start();

if(!isset($_SESSION['mapped'])){
	echo '1You do not have sufficient priviliges to perform this action.</p><p>Your session may have expired.';
	exit;
}

if($_SESSION['permissions'] != 2){
	echo '1You do not have sufficient priviliges to perform this action.</p><p>Your session may have expired.';
	exit;
}

if(!isset($_POST['usn'],$_POST['psw'])){
	echo '1Invalid input, please try again.';
	exit;
}

require_once('../../includes/user/adduser.class.php');
require_once('../../includes/conn/conn.php');
require_once('../../includes/conn/site.php');

$username = $_POST['usn'];
$password = $_POST['psw'];

$addUser = new AddUser($username,$password,$mm_db,$mm_site['salt']);


?>