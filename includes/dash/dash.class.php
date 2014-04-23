<?php

/*

MappMe created by Rory Clark
http://rorywebdev.com

*/

class Dashboard{
	
	private $db;
	private $us;
	private $pdo;

	function __construct($user,$database){
		$this->db = $database;
		$this->us = $user;
	}

	public function buildPage(){
		$this->addTitle();
		echo '<div class="ct-wrap">';
		$this->pdo = new PDO('mysql:host='.$this->db['server'].';dbname='.$this->db['database'].';charset=utf8', $this->db['admin']['usn'], $this->db['admin']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$this->addStats();
		echo '</div>';
	}

	private function addTitle(){
		echo '<div id="title-block" class="fc">'."\n";
		echo '<div id="title-rs">'."\n";
		echo '<div id="logo"></div>'."\n";
		echo '<div id="title-text">MappMe | '.$this->us['usn'].'</div>'."\n";
		echo '<div id="title-buttons">'."\n";
		echo '<a href="'.siteUrl().'logout">Log out</a>'."\n";
		echo '<a id="return-map" href="map">Return to Map</a>'."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
	}

	private function addStats(){
		$this->loginStats();
		$this->userCount();
		$this->markerStats();		
		$this->userSettingInfo();
	}

	private function insertBox($title,$subtitle,$value,$subval,$valarr){
		echo '<div class="stat-box">'."\n";
		echo '<h1>'.$title.'</h1>'."\n";
		echo '<div class="stat-content cf">'."\n".'<div class="left-stat fc">'."\n";
		echo '<div class="ls-top">'.$value.'</div>';
		echo '<div class="ls-bottom">'.$subval.'</div>';
		echo '</div>'."\n".'<div class="right-stat">'."\n".'<div class="ri-rs">';
		echo '<h2>'.$subtitle.'</h2>';
		$rows = 0;
		while($rows < count($valarr)){
			echo '<div class="rs-row">'.$valarr[$rows].'</div>'."\n";
			$rows++;
		}
		echo '</div>'."\n".'</div>'."\n".'</div>'."\n".'</div>';
	}

	private function insertBoxRow($title,$infArr){
		echo '<div class="stat-box row">'."\n";
		echo '<h1>'.$title.'</h1>'."\n";
		echo '<div class="stat-content cf">'."\n";
		$arrC = 0;
		while($arrC < 4){
			echo '<div class="row-box">'."\n";
			echo '<div class="rb-rs fc">'."\n";
			echo '<div class="ls-top">'.$infArr[$arrC]['val'].'</div>'."\n";
			echo '<div class="ls-bottom">'.$infArr[$arrC]['desc'].'</div>'."\n";
			echo '</div>'."\n";
			echo '</div>'."\n";
			$arrC++;
		}
		echo '</div>'."\n";
		echo '</div>'."\n";
	}

	private function loginStats(){
		try{
			$getLogins = $this->pdo->prepare('SELECT * FROM `'.$this->db['prefix'].'logs` ORDER BY `id` DESC');
			$getLogins->execute();
			$loginRows = $getLogins->rowCount();
			$loginData = $getLogins->fetchAll();
			$loginArr = array();
			$larrC = 0;
			while($larrC < 5){
				$thisRow = $loginData[$larrC]['username'].' - '.$loginData[$larrC]['ip'].' - '.$loginData[$larrC]['os'].' - '.$loginData[$larrC]['browser'];
				$loginArr[$larrC] = $thisRow;
				$larrC++;
			}
			$this->insertBox('Logins','5 Most Recent Logins (User - IP - OS - Browser)',number_format($loginRows),'Total Logins',$loginArr);
			$getLogins = null;
			$loginArr = null;
			$loginData = null;
		} catch(PDOException $ex){
			echo $ex;
			exit;
		}
	}

	private function markerStats(){
		try{
			$getMarkers = $this->pdo->prepare('SELECT * FROM `'.$this->db['prefix'].'data` ORDER BY `id` DESC');
			$getMarkers->execute();
			$gMRows = $getMarkers->rowCount();
			$gMRes = $getMarkers->fetchAll();
			$gMArr = array();
			$arrC = 0;
			while($arrC < 5){
				$thisRow = $gMRes[$arrC]['time'].' - '.$gMRes[$arrC]['username'];
				$gMArr[$arrC] = $thisRow;
				$arrC++;
			}
			$this->insertBox('Map Markers','5 Most Recent Markers (Date/Time - User)',number_format($gMRows),'Plotted Markers',$gMArr);
			$getMarkers = null;
			$gMRows = null;
			$gMArr = null;
		} catch(PDOException $ex){
			echo $ex;
			exit;
		}
	}

	private function userCount(){
		try{
			$usCount = $this->pdo->prepare('SELECT id,username,firstlog FROM `'.$this->db['prefix'].'users` ORDER BY `id` DESC');
			$usCount->execute();
			$usRows = $usCount->rowCount();
			$usData = $usCount->fetchAll();
			$usDataArr = array();
			$usRounds = 0;
			while($usRounds < 5){
				if($usData[$usRounds]['firstlog'] == 0){
					$active = 'Active';
				} else {
					$active = '<span>Inactive</span>';
				}
				$usDataArr[$usRounds] = $usData[$usRounds]['username'].' - '.$active;
				$usRounds++;
			}
			$this->insertBox('Users','5 Most Recent Users (Username - Active)',number_format($usRows),'Total Users',$usDataArr);
		} catch(PDOException $ex){
			echo $ex;
			exit;
		}
	}

	private function userSettingInfo(){
		try{
			$statArr = array();
			$getPolys = $this->pdo->prepare('SELECT * FROM `'.$this->db['prefix'].'users` WHERE map_settings LIKE :ply');
			$getPolys->bindValue(':ply','%"POLY_LINES"%',PDO::PARAM_STR);
			$getPolys->execute();
			$theseRows = $getPolys->rowCount();
			$statArr[0]['val'] = number_format($theseRows);
			$statArr[0]['desc'] = 'Using Polylines';
			$getPolys = null;
			$getCom = $this->pdo->prepare('SELECT * FROM `'.$this->db['prefix'].'users` WHERE map_settings LIKE :gct');
			$getCom->bindValue(':gct','%"NO_COMMENTS"%',PDO::PARAM_STR);
			$getCom->execute();
			$theseRows = $getCom->rowCount();
			$statArr[1]['val'] = number_format($theseRows);
			$statArr[1]['desc'] = 'Hiding Comments';
			$getCom = null;
			$getTimes = $this->pdo->prepare('SELECT * FROM `'.$this->db['prefix'].'users` WHERE map_settings LIKE :gti');
			$getTimes->bindValue(':gti','%"NO_TIME"%',PDO::PARAM_STR);
			$getTimes->execute();
			$theseRows = $getTimes->rowCount();
			$statArr[2]['val'] = number_format($theseRows);
			$statArr[2]['desc'] = 'Hiding Times';
			$getTimes = null;
			$getLts = $this->pdo->prepare('SELECT * FROM `'.$this->db['prefix'].'users` WHERE map_settings LIKE :gtlo OR map_settings LIKE :gtla');
			$getLts->bindValue(':gtlo','%"NO_LONG"%',PDO::PARAM_STR);
			$getLts->bindValue(':gtla','%"NO_LAT"%',PDO::PARAM_STR);
			$getLts->execute();
			$theseRows = $getLts->rowCount();
			$statArr[3]['val'] = number_format($theseRows);
			$statArr[3]['desc'] = 'Hiding Lat/Long';
			$getLts = null;
			$this->insertBoxRow('User Settings',$statArr);
		} catch(PDOException $ex){
			echo $ex;
			exit;
		}
	}

	function __destruct(){
		$this->db = null;
		$this->us = null;
		$this->pdo = null;
	}

}

?>