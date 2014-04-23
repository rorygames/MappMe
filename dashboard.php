<?php

$page_settings = array(
	'title' => 'Dashboard',
	'type' => 'admin',
	'css' => array(
		'duru',
		'styles',
		'dash'
		),
	'js' => array(
		'jquery',
		'notification'
		)
);

require_once(getcwd().'/includes/init.php');

require_once(incRoot().'layout/page.class.php');

$page = new Page($page_settings);

require_once(incRoot().'dash/dash.class.php');

$dashb = new Dashboard($user_settings,$mm_db);

$page->finHead();

$dashb->buildPage();

?>