<?php

session_start();

if(!isset($_SESSION['mapped'])){
	echo 'You do not have sufficient priviliges to perform this action.</p><p>Your session may have expired.';
	exit;
}

?>
<p><span>Please enter your current password.</span></p>
<input type="password" id="cur-passw">
<p><span>Please enter your new password.</span></p>
<input type="password" id="new-passw">
<p>Please make sure your password is longer than 7 characters, includes at least one number and has at least one non-alphanumeric character.</p>
<p><span>Please confirm your password.</span></p>
<input type="password" id="new-passw2">
<input type="button" id="set-new-pass" value="Save My Password">