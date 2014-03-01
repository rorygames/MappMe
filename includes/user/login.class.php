<?php

/*

MappMe created by Rory Clark
http://rorywebdev.com

*/

class Login{

	// Private variables
	private $username;
	private $password;
	private $hashed;
	private $permissions;

	// Database connections
	private $db_name;
	private $db_server;
	private $db_username;
	private $db_password;
	private $db_pre;
	private $salt;
	private $pdo;

	public function __construct($usn,$psw,$db,$salt){
		$this->username = $usn;
		$this->password = $psw;
		$this->db_name = $db['database'];
		$this->db_server = $db['server'];
		$this->db_pre = $db['prefix'];
		$this->db_username = $db['standard']['usn'];
		$this->db_password = $db['standard']['psw'];
		$this->salt = $salt;
		try{
			$this->pdo = new PDO('mysql:host='.$this->db_server.';dbname='.$this->db_name.';charset=utf8', $this->db_username, $this->db_password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$checkUsn = $this->pdo->prepare('SELECT username FROM `'.$this->db_pre.'users` WHERE username = :usn');
			$checkUsn->bindValue(':usn',$this->username,PDO::PARAM_STR);
			$checkUsn->execute();
			$checkRows = $checkUsn->rowCount();
			if($checkRows != 0){
				$this->checkPassword();
			} else {
				$this->pdo = null;
				echo '1Invalid username or password.';
			}
		} catch(PDOException $ex){
			echo '1'.$ex;
		}
		
	}

	private function checkPassword(){
		try{
			$checkPassword = $this->pdo->prepare('SELECT username,password,permissions FROM `'.$this->db_pre.'users` WHERE username = :usn');
			$checkPassword->bindValue(':usn',$this->username,PDO::PARAM_STR);
			$checkPassword->execute();
			$checkPRes = $checkPassword->fetch(PDO::FETCH_ASSOC);
			$this->username = $checkPRes['username'];
			$this->hashed = $checkPRes['password'];
			$this->permissions = $checkPRes['permissions'];
			if($this->validatePassword() == true){
				$this->createSession();
				$this->pdo=null;
				echo '0map';				
			} else {
				$this->pdo=null;
				echo '1Invalid username or password.';
			}
		} catch(PDOException $ex){
			echo '1'.$ex;
		}
	}

	private function validatePassword(){
		return ($this->encryptPassword() == $this->hashed);
	}

	private function encryptPassword(){
		return hash('sha512',$this->password . $this->salt);
	}

	private function createSession(){
		session_start();
		$_SESSION['mapped'] = true;
		$_SESSION['usn'] = $this->username;
		$_SESSION['permissions'] = $this->permissions;
	}

}

?>