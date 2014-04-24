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

	// User Environment
	private $ip;
	private $agent;

	// Database connections
	private $db_name;
	private $db_server;
	private $db_username;
	private $db_password;
	private $db_pre;
	private $salt;
	private $pdo;

	public function __construct($usn,$psw,$db,$salt,$ip4,$usa){
		$this->username = $usn;
		$this->password = $psw;
		$this->db_name = $db['database'];
		$this->db_server = $db['server'];
		$this->db_pre = $db['prefix'];
		$this->db_username = $db['standard']['usn'];
		$this->db_password = $db['standard']['psw'];
		$this->salt = $salt;
		$this->ip = $ip4;
		$this->agent = $usa;
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
		try{
			$logUser = $this->pdo->prepare('INSERT INTO `'.$this->db_pre.'logs`(type,username,ip,browser,os) VALUES(:typ,:usn,:ip,:brows,:os)');
			$logUser->bindValue(':typ','login',PDO::PARAM_STR);
			$logUser->bindValue(':usn',$this->username,PDO::PARAM_STR);
			$logUser->bindValue(':ip',$this->ip,PDO::PARAM_STR);
			$logUser->bindValue(':brows',$this->getBrowser(),PDO::PARAM_STR);
			$logUser->bindValue(':os',$this->getOS(),PDO::PARAM_STR);
			$logUser->execute();
		} catch(PDOException $ex){
			echo '1'.$ex;
			exit;
		}
		session_start();
		$_SESSION['mapped'] = true;
		$_SESSION['usn'] = $this->username;
		$_SESSION['permissions'] = $this->permissions;
	}

	private function getOS() {
		$os_platform = "Unknown OS";
		$os_array = array(
			'/windows nt 6.3/i' => 'Windows 8.1',
			'/windows nt 6.2/i' => 'Windows 8',
			'/windows nt 6.1/i' => 'Windows 7',
			'/windows nt 6.0/i' => 'Windows Vista',
			'/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
			'/windows nt 5.1/i' => 'Windows XP',
			'/windows xp/i'=>  'Windows XP',
			'/windows nt 5.0/i' => 'Windows 2000',
			'/windows me/i' => 'Windows ME',
			'/win98/i' => 'Windows 98',
			'/win95/i' => 'Windows 95',
			'/win16/i' => 'Windows 3.11',
			'/macintosh|mac os x/i' => 'Mac OS X',
			'/mac_powerpc/i' => 'Mac OS 9',
			'/linux/i' => 'Linux',
			'/ubuntu/i' => 'Ubuntu',
			'/iphone/i' => 'iPhone',
			'/ipod/i' => 'iPod',
			'/ipad/i' => 'iPad',
			'/android/i' => 'Android',
			'/blackberry/i' => 'BlackBerry',
			'/webos/i' => 'Mobile'
		);
		foreach ($os_array as $regex => $value) {
			if (preg_match($regex, $this->agent)) {
				$os_platform = $value;
			}
		}
		return $os_platform;
	}

	private function getBrowser() {
		$browser = "Unknown Browser";
		$browser_array = array(
			'/msie/i' => 'Internet Explorer',
			'/firefox/i'=> 'Firefox',
			'/safari/i'=> 'Safari',
			'/chrome/i'=> 'Chrome',
			'/opera/i'=> 'Opera',
			'/netscape/i' => 'Netscape',
			'/maxthon/i' => 'Maxthon',
			'/konqueror/i' => 'Konqueror',
			'/mobile/i'=> 'Handheld Browser'
		);
		foreach ($browser_array as $regex => $value) {
			if (preg_match($regex, $this->agent)) {
				$browser = $value;
			}
		}
		return $browser;
	}

}

?>