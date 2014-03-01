<?php

/*

MappMe created by Rory Clark
http://rorywebdev.com

*/

class AddUser{
	
	// User variables
	private $salt;
	private $usn;
	private $psw;
	private $hashed;

	// Database connections
	private $db_name;
	private $db_server;
	private $db_username;
	private $db_password;
	private $db_pre;
	private $pdo;

	function __construct($user,$pass,$db,$sal){
		$this->db_name = $db['database'];
		$this->db_server = $db['server'];
		$this->db_pre = $db['prefix'];
		$this->db_username = $db['admin']['usn'];
		$this->db_password = $db['admin']['psw'];
		$this->salt = $sal;
		$this->usn = $user;
		$this->psw = $pass;
		try{
			$this->pdo = new PDO('mysql:host='.$this->db_server.';dbname='.$this->db_name.';charset=utf8', $this->db_username, $this->db_password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$checkUsn = $this->pdo->prepare('SELECT username FROM `'.$this->db_pre.'users` WHERE username = :usn');
			$checkUsn->bindValue(':usn',$this->usn,PDO::PARAM_STR);
			$checkUsn->execute();
			$checkRows = $checkUsn->rowCount();
			if($checkRows == 0){
				$this->addToDB();
			} else {
				echo '1The username you chose is already in use.';
			}
		} catch(PDOException $ex){
			echo '1'.$ex;
		}
	}

	private function addToDB(){
		$this->hashed = $this->encryptPassword();
		try{
			$userAdd = $this->pdo->prepare('INSERT INTO `'.$this->db_pre.'users`(username,password,permissions) VALUES(:usn,:psw,:per)');
			$userAdd->bindValue(':usn',$this->usn,PDO::PARAM_STR);
			$userAdd->bindValue(':psw',$this->hashed,PDO::PARAM_STR);
			$userAdd->bindValue(':per',0,PDO::PARAM_INT);
			$userAdd->execute();
			echo '0User '.$this->usn.' added to MappMe.</p><p>They will now be able to log in and use the system.';
		} catch(PDOException $ex){
			echo '1'.$ex;
		}
	}

	private function encryptPassword(){
		return hash('sha512',$this->psw . $this->salt);
	}

	function __destruct(){
		$this->pdo = null;
		$this->db_name = null;
		$this->db_server = null;
		$this->db_pre = null;
		$this->db_username = null;
		$this->db_password = null;
		$this->hashed = null;
		$this->salt = null;
		$this->usn = null;
		$this->psw = null;
	}

}

?>