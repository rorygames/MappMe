<?php

class Receiver{

	// Received variables
	private $username;
	private $access_code;
	private $latitude;
	private $longitude;
	private $comment;

	// Database information
	private $db_srv;
	private $db_name;
	private $db_pre;
	private $db_usn;
	private $db_psw;

	public function __construct($mm_db){
		// Load in the database variables
		$this->db_srv = $mm_db['server'];
		$this->db_name = $mm_db['database'];
		$this->db_pre = $mm_db['prefix'];
		$this->db_usn = $mm_db['standard']['usn'];
		$this->db_psw = $mm_db['standard']['psw'];
	}

	public function checkAccess($ac){
		// Set class access code
		$this->access_code = $ac;
		$resp_n = 0;
		$resp_t = "";
		try{
			$db_check = new PDO('mysql:host='.$this->db_srv.';dbname='.$this->db_name.';charset=utf8', $this->db_usn, $this->db_psw,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$ac_check = $db_check->prepare('SELECT username, access_code, firstlog FROM `'.$this->db_pre.'users` WHERE access_code = :asc');
			$ac_check->bindValue(':asc',$this->access_code,PDO::PARAM_STR);
			$ac_check->execute();
			// See if any user currently has the access code
			$ac_rows = $ac_check->rowCount();
			if($ac_rows == 0){
				$resp_n=1;
				$resp_t = "Invalid access code.";
			} else {
				// If the access code is found check to see if it has been activated (ac_type)
				$ac_res = $ac_check->fetch(PDO::FETCH_ASSOC);
				$this->username = $ac_res['username'];
				// 1 means the user has not yet finished their personal MappMe setup. They need to login.
				if($ac_res['firstlog'] == 1){
					$resp_n=1;
					$resp_t = "Your account has not been setup yet. Please sign into MappMe before trying to use this app.";
				} else if($ac_res['access_code'] != $this->access_code){
					// Simply an invalid access code
					$resp_n=1;
					$resp_t = "Invalid access code.";
				}
				// Dont respond with anything if success
			}
		} catch(PDOException $ex) {
			// Report any SQL errors.
			$resp_n=1;
			$resp_t = $ex;
		}
		// Data that will return. 0 will not show on the device (success), anything else will be handled as an error or notification (by the app).
		return $resp_n.$resp_t;
	}

	public function storeMarker($r_data){
		// Store the marker with the data
		$this->latitude = $r_data['lat'];
		$this->longitude = $r_data['long'];
		$this->comment = $r_data['com'];
		try{
			$db_add = new PDO('mysql:host='.$this->db_srv.';dbname='.$this->db_name.';charset=utf8', $this->db_usn, $this->db_psw,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$sm_add = $db_add->prepare('INSERT INTO `'.$this->db_pre.'data`(username,mm_lat,mm_long,comments) VALUES(:usn,:lat,:long,:com)');
			$sm_add->bindValue(':usn',$this->username,PDO::PARAM_STR);
			$sm_add->bindValue(':lat',$this->latitude,PDO::PARAM_STR);
			$sm_add->bindValue(':long',$this->longitude,PDO::PARAM_STR);
			$sm_add->bindValue(':com',$this->comment,PDO::PARAM_STR);
			$sm_add->execute();
			return 0;
		} catch(PDOException $ex) {
			// Report any SQL errors.
			return '1'.$ex;
		}
	}

}

?>