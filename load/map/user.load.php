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

?>

<div class="mm-d-i-rs">
<p><span>Please enter a username.</span></p>
<input type="text" id="n-u-usn" class="mmn-i-t" length="20">
</div>
<div class="mm-d-i-rs">
<p><span>Please enter an initial password.</span></p>
<input type="text" id="n-u-psw" class="mmn-i-t" length="40">
</div>
<div class="mm-d-i-rs">
<p>Please use alphanumeric, dash and underscore characters only.</p>
<input type="button" id="n-u-sub" value="Create User">
</div>