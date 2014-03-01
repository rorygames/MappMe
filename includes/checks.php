<?php

// Make sure the system is installed
if(!file_exists(getcwd().'/includes/conn/conn.php')){
	if(!file_exists(getcwd().'/includes/conn/site.php')){
		header( 'Location: setup' ) ;
	}
}

// Is the user logged in

function isLoggedIn()
{
    if(isset($_SESSION['mapped'],$_SESSION['usn']) && $_SESSION['mapped'] && $_SESSION['usn'])
        return true;
    return false;
}

// Setup cookie (session) options before session begins.

session_set_cookie_params(86400,siteUrl());

session_start();

if($page_settings['type'] == 'page'){
	// If the user has already logged in
	if(isLoggedIn()){
		header('Location: '.siteUrl().'map');
		exit();
	}
}
if($page_settings['type'] == 'map'){
	// If the user hasn't logged in then redirect them
	if(!isLoggedIn()){
		header('Location: '.siteUrl());
	    exit();
	} else {
		// Load the user settings if they are logged in
		loadUser();
	}
}

function loadUser(){
	global $user_settings,$mm_db;
	try{
		$us_grab = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$us_set = $us_grab->prepare('SELECT * FROM `'.$mm_db['prefix'].'users` WHERE username = :usn');
		$us_set->bindValue(':usn',$_SESSION['usn'],PDO::PARAM_STR);
		$us_set->execute();
		$us_s_res = $us_set->fetch(PDO::FETCH_ASSOC);
		$user_settings['usn'] = $us_s_res['username'];
		$user_settings['per'] = $us_s_res['permissions'];
		$user_settings['zoom'] = $us_s_res['zoom'];
		$user_settings['slat'] = $us_s_res['starting_lat'];
		$user_settings['slong'] = $us_s_res['starting_long'];
		$user_settings['first'] = $us_s_res['firstlog'];
		$us_s_res = null;
		$us_grab = null;
	} catch(PDOException $ex){
		echo $ex;
		exit();
	}
}

?>