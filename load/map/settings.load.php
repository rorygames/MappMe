<?php

session_start();

if(!isset($_SESSION['mapped'])){
	echo 'You do not have sufficient priviliges to perform this action.</p><p>Your session may have expired.';
	exit;
}

require_once('../../includes/conn/conn.php');

try{
	$me_set = new PDO('mysql:host='.$mm_db['server'].';dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$me_res_p = $me_set->prepare('SELECT map_settings FROM `'.$mm_db['prefix'].'users` WHERE username = :usn');
	$me_res_p->bindValue(':usn',$_SESSION['usn'],PDO::PARAM_STR);
	$me_res_p->execute();
	$me_res_rc = $me_res_p->rowCount();
	if($me_res_rc != 0){
		$me_res = $me_res_p->fetch(PDO::FETCH_ASSOC);
		if($me_res['map_settings'] == ""){
			$me_res_arr = array();
		} else {
			$me_res_arr = unserialize($me_res['map_settings']);
		}
	} else {
		$me_res_arr = array();
	}
} catch(PDOException $ex){
	echo $ex;
	exit;
}

?>
<h1>Map</h1>
<label for="s-polyl"><input type="checkbox" id="s-polyl" <?php if(in_array("POLY_LINES",$me_res_arr)){ echo "checked";} ?>>Enable polylines.</label>
<h1>Markers</h1>
<label for="n-limit">
	<select id="n-limit">
		<?php if(array_key_exists("MARK_LIMIT",$me_res_arr)){ ?>
		<option value="0"<?php if($me_res_arr['MARK_LIMIT'] == 0){echo' selected';}?>>No limit</option><option value="1"<?php if($me_res_arr['MARK_LIMIT'] == 1){echo' selected';}?>>100 markers</option><option value="2"<?php if($me_res_arr['MARK_LIMIT'] == 2){echo' selected';}?>>50 markers</option><option value="3"<?php if($me_res_arr['MARK_LIMIT'] == 3){echo' selected';}?>>25 markers</option><option value="4"<?php if($me_res_arr['MARK_LIMIT'] == 4){echo' selected';}?>>10 markers</option><option value="5"<?php if($me_res_arr['MARK_LIMIT'] == 5){echo' selected';}?>>5 markers</option>
		<?php } else { ?>
		<option value="0">No limit</option><option value="1">100 markers</option><option value="2">50 markers</option><option value="3">25 markers</option><option value="4">10 markers</option><option value="5">5 markers</option>
		<?php } ?>
	</select>
	Marker limit (most recent).
</label>
<label for="t-limit">
	<select id="t-limit">
		<?php if(array_key_exists("MARK_TIME_LIMIT",$me_res_arr)){ ?>
		<option value="0"<?php if($me_res_arr['MARK_TIME_LIMIT'] == 0){echo' selected';}?>>No time limit</option><option value="1"<?php if($me_res_arr['MARK_TIME_LIMIT'] == 1){echo' selected';}?>>Past 24 hours</option><option value="2"<?php if($me_res_arr['MARK_TIME_LIMIT'] == 2){echo' selected';}?>>Past 7 days</option><option value="3"<?php if($me_res_arr['MARK_TIME_LIMIT'] == 3){echo' selected';}?>>Past month</option><option value="4"<?php if($me_res_arr['MARK_TIME_LIMIT'] == 4){echo' selected';}?>>Past year</option>
		<?php } else { ?>
		<option value="0">No time limit</option><option value="1">Past 24 hours</option><option value="2">Past 7 days</option><option value="3">Past month</option><option value="4">Past year</option>
		<?php } ?>
	</select>
	Marker time limit.
</label>
<h1>Marker Information</h1>
<label for="s-n-com"><input type="checkbox" id="s-n-com" <?php if(in_array("NO_COMMENTS",$me_res_arr)){ echo "checked";} ?>>Hide comments.</label>
<label for="s-n-time"><input type="checkbox" id="s-n-time" <?php if(in_array("NO_TIME",$me_res_arr)){ echo "checked";} ?>>Hide times.</label>
<label for="s-n-lat"><input type="checkbox" id="s-n-lat" <?php if(in_array("NO_LAT",$me_res_arr)){ echo "checked";} ?>>Hide latitudes.</label>
<label for="s-n-long"><input type="checkbox" id="s-n-long" <?php if(in_array("NO_LONG",$me_res_arr)){ echo "checked";} ?>>Hide longitudes.</label>
<input type="button" id="me-set-save" value="Save My Settings">
<?php
// Please do not remove this! Thank you!
?>
<div class="mm-rwd-i-rs">
<p class="mm-rwd-info">MappMe created by <a href="http://rorywebdev.com" target="_blank">Rory Clark</a> 2014.</p>
<p class="mm-rwd-info">Open source software licensed under <a href="https://github.com/rorywebdev/MappMe/blob/master/LICENSE" target="_blank">MIT</a>.</p>
</div>