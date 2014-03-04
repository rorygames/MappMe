<?php

// Setup extra items (hard coded URL and directory)
function curPageURL() {
	$pageURL = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://' : 'https://';
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

$thisURL = parse_url(curPageURL());

$thispage = $thisURL['host'] . substr($thisURL['path'],0,-5);

$page_settings = array(
	'title' => 'Setup',
	'type' => 'quick',
	'css' => array(
		'duru',
		'styles',
		'setup'
		),
	'js' => array(
		'jquery',
		'notification',
		'setup'
		)
);

require_once(getcwd().'/includes/init.php');

require_once(getcwd().'/includes/layout/page.class.php');

$page = new Page($page_settings);

$page->finHead();

?>
<div id="top-setup" class="fc">
	Welcome to MappMe
</div>
<div id="middle-setup">
	<div id="first-setup" class="s-block open">
		<?php
		if(file_exists(getcwd().'/includes/conn/conn.php')){
			require_once(getcwd().'/includes/conn/conn.php');
			try{
				$db_test = new PDO('mysql:host=localhost;dbname='.$mm_db['database'].';charset=utf8', $mm_db['standard']['usn'], $mm_db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
				$db_test = null;
				echo '<h1>Your database connections have already been created. Be careful when changing your setup.</h1>';
			} catch(PDOException $ex){
				$db_test = null;
			}
		}
		?>
		<p>Just before you start using MappMe there are a few items we need to setup.</p>
		<h1>MappMe Requirements</h1>
		<ul>
			<li>PHP 5.1 or higher</li>
			<li>MySQL 5 or higher</li>
			<li>PDO PHP plugin</li>
			<li>An internet accessible server (via a URL, Xampp etc. will not work correctly)</li>
			<li><a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">Google Developers API Key</a> (for Google Maps access)</li>
		</ul>
		<h1>Database Information</h1>
		<p>You will need to create a new, empty, local (on this server) database and a <span>global</span> user that has <span>all privileges (including CREATE USER).</span></p>
		<p>This user will only be used for setup, other users will be created to manage specific items.</p>
		<p>Please use alphanumeric characters, dashes and underscores only.</p>
		<h2>Database Name</h2>
		<input type="text" class="setup-input-text" id="db-name">
		<h2>Database Username</h2>
		<input type="text" class="setup-input-text" id="db-username">
		<h2>Database Password</h2>
		<input type="text" class="setup-input-text" id="db-password">
		<h2>Database Prefix</h2>
		<input type="text" class="setup-input-text" id="db-prefix" value="mm-">
		<input type="button" id="db-check" class="setup-button" value="Check Details">
	</div>
	<div id="second-setup" class="s-block">
		<p>Great! Your database has been created and the connections have been made.</p>
		<p>We've removed all the permissions from your global database user. You can now remove it.</p>
		<h1>Server Details</h1>
		<p>Your server details have been obtained automatically. If they are incorrect then please change them now.</p>
		<p>Inputting incorrect details will cause MappMe to error.</p>
		<p><span>Base URL (excluding protocol)</span></p>
		<input type="text" class="setup-input-text" id="server-url" value="<?php echo $thispage; ?>">
		<p><span>Base Server Directory</span></p>
		<input type="text" class="setup-input-text" id="server-path" value="<?php echo getcwd(); ?>">
		<h1>Admin Account</h1>
		<p>You will need an admin account to use MappMe so we'll create one now.</p>
		<p>Admin accounts are different to normal accounts as they let you add and remove users but also see other people's MappMarkers.</p>
		<p>When you log in for the first time you will be asked to input a few settings so MappMe can function properly, including your Google Maps API key.</p>
		<h2>Admin Username</h2>
		<input type="text" class="setup-input-text" id="admin-name">
		<p>Please use alphanumeric characters for your username with a minimum of 4 characters.</p>
		<h2>Admin Password</h2>
		<input type="password" class="setup-input-text" id="admin-password">
		<p>Please make sure your password is longer than 7 characters, includes at least one number and has at least one non-alphanumeric character.</p>
		<h2>Confirm Password</h2>
		<input type="password" class="setup-input-text" id="admin-password2">
		<input type="button" id="ad-check" class="setup-button" value="Check Details">
	</div>
	<div id="third-setup" class="s-block">
		<p>Wonderful! MappMe has been successfully installed and setup.</p>
		<p>You will now be able to log into the system and start Mapping.</p>
		<h1>Make sure to remove "setup.php" and the "initial" folder from your server now.</h1>
		<p>Leaving these files on the root of MappMe may lead to security issues on your system. You have been warned.</p>
		<p>A backup of these installation files can be found under the <span>includes/setup/_backup/</span> folder. These are included for future repairs or re-installs.</p>
		<p><span>Make sure you have the MappMe Transmitter application installed on your phone before logging in, you will when you log into your account.</span></p>
		<input type="button" id="finish-setup" class="setup-button" value="Log into MappMe">
	</div>
</div>