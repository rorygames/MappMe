<?php

$page_settings = array(
	'title' => 'Login',
	'type' => 'page',
	'css' => array(
		'duru',
		'styles',
		'login'
		),
	'js' => array(
		'jquery',
		'notification',
		'login'
		)
);

require_once(getcwd().'/includes/init.php');

require_once(incRoot().'layout/page.class.php');

$page = new Page($page_settings);

$page->finHead();

?>
<div id="top-splash" class="fc">
	<div id="ts-logo"></div>
	<h1>Welcome to MappMe</h1>
</div>
<div id="bottom-splash">
	<form id="user-splash">
		<p id="bs-us">Please enter your username</p>
		<input type="text" id="username" length="20" autocomplete="off">
		<p id="bs-pw">Please enter your password</p>
		<input type="password" id="password" length="40" autocomplete="off">
		<input type="submit" id="submit" value="Log in">
		<input type="button" id="forgot" value="Forgot?">
	</form>
</div>