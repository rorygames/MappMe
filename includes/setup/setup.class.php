<?php

/*

MappMe created by Rory Clark
http://rorywebdev.com

*/

class Setup{

	// Private variables for generated database users
	private $mm_db;
	private $mm_1_usn;
	private $mm_1_psw;
	private $mm_2_usn;
	private $mm_2_psw;

	// Private variables for user account
	private $am_usn;
	private $am_psw;

	// Salt for the site
	private $site_salt;

	public function checkConnection($dbname,$dbusn,$dbpsw,$server){
		try{
			// Test the database connection with the settings.
			$db_test = new PDO('mysql:host='.$server.';dbname='.$dbname.';charset=utf8', $dbusn, $dbpsw,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			// Show the database user permissions
			$test_user = $db_test->prepare('SHOW GRANTS FOR :usn@:srv');
			$test_user->bindValue(':usn',$dbusn,PDO::PARAM_STR);
			$test_user->bindValue(':srv',$server,PDO::PARAM_STR);
			$test_user->execute();
			$testc = $test_user->rowCount();
			$testc--;
			$testres = $test_user->fetchAll();
			// Make sure it has all privileges & grant. Checking the full string would require knowing the 'password' field.
			$correctStr1 = "GRANT ALL PRIVILEGES ON *.* TO '".$dbusn."'@'".$server;
			$correctStr2 = " WITH GRANT OPTION";
			$correctStrC = 0;
			if (strpos($testres[$testc][0],$correctStr1) !== false) {
			    $correctStrC++;
			}
			if (strpos($testres[$testc][0],$correctStr2) !== false) {
			    $correctStrC++;
			}
			if($correctStrC != 0){
				echo '0The database connection has been established and your database user has sufficient access.</p><p>Press continue to start building the database.';
			} else {
				echo '1Your database user does not have sufficient privileges. Please change their settings and try again.</p><br><p><span>Your current user state is:</span></p><p>'.$testres[$testc][0].'</p><br><p><span>MappMe requires:</span></p><p>'.$correctStr1.$correctStr2.'</p>';
			}
			$db_test = null;		
		} catch(PDOException $ex){
			$db_test = null;
			echo '1Unable to connect to your database. Please change your settings and try again.';			
		}
	}

	public function createDB($dbname,$dbusn,$dbpsw,$dbprefix,$server){
		try{

			// Build the tables for database. Config, Data and Users.
			$db_build = new PDO('mysql:host='.$server.';dbname='.$dbname.';charset=utf8', $dbusn, $dbpsw,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$config = $db_build->prepare("CREATE TABLE IF NOT EXISTS `".$dbprefix."config` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` text COLLATE utf8_unicode_ci NOT NULL, `value` text COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			$config->execute();
			$config=null;
			$data = $db_build->prepare("CREATE TABLE IF NOT EXISTS `".$dbprefix."data` (`id` int(11) NOT NULL AUTO_INCREMENT, `username` text COLLATE utf8_unicode_ci NOT NULL, `mm_lat` text COLLATE utf8_unicode_ci NOT NULL, `mm_long` text COLLATE utf8_unicode_ci NOT NULL, `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `comments` text COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			$data->execute();
			$data=null;
			$users = $db_build->prepare("CREATE TABLE IF NOT EXISTS `".$dbprefix."users` (`id` int(11) NOT NULL AUTO_INCREMENT, `username` text COLLATE utf8_unicode_ci NOT NULL, `password` text COLLATE utf8_unicode_ci NOT NULL, `access_code` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', `permissions` tinyint(1) NOT NULL DEFAULT '0', `zoom` tinyint(2) NOT NULL DEFAULT '2', `starting_lat` text COLLATE utf8_unicode_ci NOT NULL, `starting_long` text COLLATE utf8_unicode_ci NOT NULL, `firstlog` tinyint(1) NOT NULL DEFAULT '1', `map_settings` text COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			$users->execute();
			$users=null;

			// Create the standard user and give them limited permissions, this will be used for receiving data and for standard user access.
			$this->mm_1_usn = $this->createRandom(6);
			// Prefix username to allow easy identification.
			$this->mm_1_usn = 'mm-'.$this->mm_1_usn;
			$this->mm_1_psw = $this->createRandom(30);
			$create1 = $db_build->prepare("CREATE USER '".$this->mm_1_usn."'@'".$server."' IDENTIFIED BY '".$this->mm_1_psw."';");
			$create1->execute();
			$create1=null;

			$create1_data = $db_build->prepare("GRANT SELECT , INSERT , UPDATE (`username`, `comments`) ON `".$dbname."`.`".$dbprefix."data` TO  '".$this->mm_1_usn."'@'".$server."';");
			$create1_data->execute();
			$create1_data=null;

			$create1_users = $db_build->prepare("GRANT SELECT , UPDATE (`password` ,`access_code` ,`zoom` ,`starting_lat` ,`starting_long` ,`firstlog` ,`map_settings`) ON `".$dbname."`.`".$dbprefix."users` TO  '".$this->mm_1_usn."'@'".$server."';");
			$create1_users->execute();
			$create1_users=null;

			$create1_config = $db_build->prepare("GRANT SELECT ON `".$dbname."`.`".$dbprefix."config` TO  '".$this->mm_1_usn."'@'".$server."';");
			$create1_config->execute();
			$create1_config=null;

			// Create the administrator user. This will be only available to the administrator when logged in. Allows for extra items such as delete.
			$this->mm_2_usn = $this->createRandom(6);
			// Prefix admin username to allow easy identification.
			$this->mm_2_usn = 'mm-a-'.$this->mm_2_usn;
			$this->mm_2_psw = $this->createRandom(30);
			$create2 = $db_build->prepare("CREATE USER '".$this->mm_2_usn."'@'".$server."' IDENTIFIED BY '".$this->mm_2_psw."';");
			$create2->execute();
			$create2=null;

			$create2_data = $db_build->prepare("GRANT SELECT , INSERT , UPDATE , DELETE ON `".$dbname."`.`".$dbprefix."data` TO  '".$this->mm_2_usn."'@'".$server."';");
			$create2_data->execute();
			$create2_data=null;

			$create2_users = $db_build->prepare("GRANT SELECT , INSERT , UPDATE , DELETE ON `".$dbname."`.`".$dbprefix."users` TO  '".$this->mm_2_usn."'@'".$server."';");
			$create2_users->execute();
			$create2_users=null;

			$create2_config = $db_build->prepare("GRANT SELECT , INSERT , UPDATE , DELETE ON `".$dbname."`.`".$dbprefix."config` TO  '".$this->mm_2_usn."'@'".$server."';");
			$create2_config->execute();
			$create2_config=null;

			// Delete super user, no longer needed once databases have been created.
			$revoke_user = $db_build->prepare("REVOKE ALL PRIVILEGES, GRANT OPTION FROM  '".$dbusn."'@'".$server."';");
			$revoke_user->execute();
			$revoke_user=null;

			$db_build=null;

			// Build the connection files
			if (!is_dir("../includes/conn/")) {
			  // Create connection directory (if it doesn't exist)
			  mkdir("../includes/conn/");
			}

			// Write connections array to file
			file_put_contents('../includes/conn/conn.php', '<?php $mm_db = array("server" => "'.$server.'","database" => "'.$dbname.'","prefix" => "'.$dbprefix.'","standard" => array("usn" => "'.$this->mm_1_usn.'","psw" => "'.$this->mm_1_psw.'"),"admin" => array("usn" => "'.$this->mm_2_usn.'","psw" => "'.$this->mm_2_psw.'")); ?>');
			
			echo '0';

		} catch(PDOException $ex){
			$db_build = null;
			echo '1'.$ex;
		}
	}

	public function createUser($db_name,$db_usn,$db_psw,$db_pre,$server,$amusn,$ampsw,$base_url,$base_path){
		// Generate salt for site
		$this->site_salt = $this->genSalt();
		// Encrypt admin password with salt
		$this->am_psw = $this->encryptPass($ampsw);
		try{
			// Insert the user information into the database.
			$db_am = new PDO('mysql:host='.$server.';dbname='.$db_name.';charset=utf8', $db_usn, $db_psw,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$am_create = $db_am->prepare('INSERT INTO `'.$db_pre.'users`(username,password,permissions) VALUES(:usn,:psw,:per)');
			$am_create->bindValue(':usn',$amusn,PDO::PARAM_STR);
			$am_create->bindValue(':psw',$this->am_psw,PDO::PARAM_STR);
			$am_create->bindValue(':per',2,PDO::PARAM_INT);
			$am_create->execute();
			$am_create=null;

			// Set base public url
			$am_bu = $db_am->prepare('INSERT INTO `'.$db_pre.'config`(type,value) VALUES(:ty,:va)');
			$am_bu->bindValue(':ty','SITE_URL',PDO::PARAM_STR);
			$am_bu->bindValue(':va',$base_url,PDO::PARAM_STR);
			$am_bu->execute();
			$am_bu=null;

			// Set base document path
			$am_bp = $db_am->prepare('INSERT INTO `'.$db_pre.'config`(type,value) VALUES(:ty,:va)');
			$am_bp->bindValue(':ty','BASE_PATH',PDO::PARAM_STR);
			$am_bp->bindValue(':va',$base_path,PDO::PARAM_STR);
			$am_bp->execute();
			$am_bp=null;

			$db_am=null;
		} catch(PDOException $ex){
			$db_am=null;
			echo '1'.$ex;
		}

		// Build the salt file
		if (!is_dir("../includes/conn/")) {
		  // Create connection directory (if it doesn't exist)
		  mkdir("../includes/conn/");
		}

		// Write salt to file
		file_put_contents('../includes/conn/site.php', "<?php ".'$mm_site'." = array('salt' => '".$this->site_salt."'); ?>");

		echo '0';
	}

	private function createRandom($length){
		// Generate a random string to be used for the database usernames.
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	private function genSalt(){
		// Generate the salt for the user passwords.
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?';
		$randomString = '';
		$length = 100;
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	private function encryptPass($password){
		// Encrypt and store the admin password.
		return hash(sha512,$password . $this->site_salt);
	}
}

?>