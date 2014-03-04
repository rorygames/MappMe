<?php

/*

MappMe created by Rory Clark
http://rorywebdev.com

*/

class MappMe{

	// Private global arrays
	private $db;
	private $us;
	private $pdo;

	// Personal map settings
	private $my_set;

	// Marker data
	private $markers;
	private $markerRows;

	private $df_mm = array(
		'lat' => '35.15612603930962',
		'long' => '30.721084542236312',
		'img' => 'img/marker/marker.png'
		);

	function __construct($user,$database){
		$this->db = $database;
		$this->us = $user;
		if($this->us['first'] == 0){
			$this->plotMap();
		}
	}

	private function plotMap(){
		echo "\n".'<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='.grabSetting('API_KEY').'&sensor=false"></script>' . "\n";
		echo '<script type="text/javascript" id="MappMeJS">'."\n";
		echo 'var MappMe,MM_Image,MM_Options,MM_Markers = new Array(),MM_Marker;'."\n";
		$this->getMySettings();
		// Set the marker image
		echo 'MM_Image = "'.$this->df_mm['img'].'";'."\n";
		// Start Map Initialiser
		echo 'function RunMappMe(){'."\n";		
		// Start Options
		echo 'MM_Options = {'."\n";
		if($this->us['slat'] == "" || $this->us['slong'] == ""){
			$this->us['slat'] = $this->df_mm['lat'];
			$this->us['slong'] = $this->df_mm['long'];
		}
		echo 'center: new google.maps.LatLng('.$this->us['slat'].', '.$this->us['slong'].'),';
		echo 'mapTypeControl: false,panControl: true,zoomControl: true,scaleControl: false,streetViewControl: true,zoom: '.$this->us['zoom'];
		// End Options
		echo '}'."\n";
		// Set the MappMe variable
		echo 'MappMe = new google.maps.Map(document.getElementById("map-canvas"),MM_Options);'."\n";		
		// Get markers
		$this->getMarkers();
		// End Map Initialiser
		echo '}'."\n";
		echo 'google.maps.event.addDomListener(window, "load", RunMappMe);'."\n";
		echo '</script>';
	}

	private function getMySettings(){
		try{
			$this->pdo = new PDO('mysql:host='.$this->db['server'].';dbname='.$this->db['database'].';charset=utf8', $this->db['standard']['usn'], $this->db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$preSettings = $this->pdo->prepare('SELECT map_settings FROM `'.$this->db['prefix'].'users` WHERE username = :usn');
			$preSettings->bindValue(':usn',$this->us['usn'],PDO::PARAM_STR);
			$preSettings->execute();
			$myset = $preSettings->fetch(PDO::FETCH_ASSOC);
			if($myset['map_settings'] != ""){
				$this->my_set = unserialize($myset['map_settings']);
			} else {
				$this->my_set = array();
			}
		} catch(PDOException $ex){
			echo $ex;
			exit;
		}
	}

	private function getMarkers(){
		try{
			$timestr = "";
			if(array_key_exists('MARK_TIME_LIMIT', $this->my_set)){
				switch($this->my_set['MARK_TIME_LIMIT']){
					case 1:
					$timestr = "AND time >= NOW() - INTERVAL 24 HOUR";
					break;
					case 2:
					$timestr = "AND time >= NOW() - INTERVAL 7 DAY";
					break;
					case 3:
					$timestr = "AND time >= NOW() - INTERVAL 1 MONTH";
					break;
					case 4:
					$timestr = "AND time >= NOW() - INTERVAL 1 YEAR";
					break;
				}
			}
			$limstr = "";
			if(array_key_exists('MARK_LIMIT', $this->my_set)){
				switch($this->my_set['MARK_LIMIT']){
					case 1:
					$limstr = " LIMIT 100";
					break;
					case 2:
					$limstr = " LIMIT 50";
					break;
					case 3:
					$limstr = " LIMIT 25";
					break;
					case 4:
					$limstr = " LIMIT 10";
					break;
					case 5:
					$limstr = " LIMIT 5";
					break;
				}
			}
			$preMarkers = $this->pdo->prepare('SELECT * FROM `'.$this->db['prefix'].'data` WHERE username = :usn '.$timestr.' ORDER BY time DESC'.$limstr);
			$preMarkers->bindValue(':usn',$this->us['usn'],PDO::PARAM_STR);
			$preMarkers->execute();
			$this->markerRows = $preMarkers->rowCount();
			$this->markers = $preMarkers->fetchAll();
			if($this->markerRows != 0){
				$this->placeMarkers($this->markers,$this->markerRows);
			}
		} catch(PDOException $ex){
			echo $ex;
			exit;
		}
	}

	private function placeMarkers($marks,$counter){
		if(in_array('POLY_LINES',$this->my_set)){
			$rounds = 0;
			$poly_str = "";
			echo 'MM_PolyLs = [';
			while($rounds < $counter){
				$poly_str .= 'new google.maps.LatLng('.$marks[$rounds]['mm_lat'].','.$marks[$rounds]['mm_long'].'),';
				$rounds++;
			}
			$poly_str = substr($poly_str,0,-1);
			echo $poly_str.'];'."\n";
			echo 'var MM_PolyLsP = new google.maps.Polyline({path: MM_PolyLs,geodesic: true,clickable: false,strokeColor: "#F86752",strokeOpacity: 1.0,strokeWeight: 2});'."\n";
			echo 'MM_PolyLsP.setMap(MappMe);';
		}
		$rounds = 0;
		$mm_item_str = "";
		echo 'MM_iw = new google.maps.InfoWindow({content: ""});';
		echo 'MM_Items = [';
		while($rounds < $counter){
			if(!in_array('NO_TIME',$this->my_set)){
				$mm_item_str .= '["'.$marks[$rounds]['time'].'"';
			} else {
				$mm_item_str .= '[""';
			}
			$mm_item_str .=',"'.$marks[$rounds]['mm_lat'].'","'.$marks[$rounds]['mm_long'].'"';
			if(!in_array('NO_COMMENTS',$this->my_set)){
				$mm_item_str .= ',"'.$marks[$rounds]['comments'].'"';
			}
			$mm_item_str .= '],';
			$rounds++;
		}
		$mm_item_str = substr($mm_item_str,0,-1);
		echo $mm_item_str . '];';
		echo 'for (var i = 0; i < MM_Items.length; i++) {MM_Marker = new google.maps.Marker({position: new google.maps.LatLng(MM_Items[i][1], MM_Items[i][2]),map: MappMe,icon : MM_Image,animation: google.maps.Animation.DROP';
			if(!in_array('NO_TIME',$this->my_set)){',title: MM_Items[i][0]';}
			echo '});
			MM_Markers.push(MM_Marker);
			google.maps.event.addListener(MM_Marker, "click", (function(MM_Marker, i) {return function(){
				MappMe.panTo(MM_Marker.getPosition());
				MM_iw.setContent("';
		if(!in_array('NO_COMMENTS',$this->my_set)){echo '<p><span>"+MM_Items[i][3]+"</span></p>';}
		if(!in_array('NO_TIME',$this->my_set)){echo '<p>"+MM_Items[i][0]+"</p>';}
		if(!in_array('NO_LAT',$this->my_set)){echo '<p><span>Lat:</span> "+MM_Items[i][1]+"</p>';}
		if(!in_array('NO_LONG',$this->my_set)){echo '<p><span>Long:</span> "+MM_Items[i][2]+"</p>';} 
		echo '");MM_iw.open(MappMe, MM_Marker);}})(MM_Marker, i));}';

	}

	public function buildMap(){
		if($this->us['first'] == 0){
			$this->addMap();
		} else {
			switch($this->us['per']){
				case 2:
				$this->firstAdmin();
				break;
				default:
				$this->firstUser();
				break;
			}
		}
	}

	private function addMap(){
		echo '<div id="map-rs">'."\n";
		echo '<div id="map-canvas"></div>'."\n";
		echo '</div>'."\n";
		echo '<div id="title-block" class="fc">'."\n";
		echo '<div id="title-rs">'."\n";
		echo '<div id="logo"></div>'."\n";
		echo '<div id="title-text">MappMe | '.$this->us['usn'].'</div>'."\n";
		echo '<div id="title-buttons">'."\n";
		echo '<a href="'.siteUrl().'logout">Log out</a>'."\n";
		echo '<a id="me-settings">Settings</a>'."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
		$this->addBottom();
	}

	private function addBottom(){
		echo '<div id="bottom-block">'."\n";
		echo '<div id="bottom-rs">'."\n";
		echo '<div id="button-rs">'."\n";
		echo '<div class="b-single-rs"><input type="button" id="set-home" class="bottom-button" value="Set Home"></div>';
		echo '<div class="b-single-rs"><input type="button" id="new-code" class="bottom-button" value="New Code"></div>';
		echo '<div class="b-single-rs"><input type="button" id="change-pw" class="bottom-button" value="Change Password"></div>';
		if($this->us['per'] == 2){
			echo '<div class="b-single-rs"><input type="button" id="new-user" class="bottom-button" value="New User"></div>';
		}
		echo '</div>'."\n";
		echo '<div id="bottom-info-rs">'."\n";
		echo '<p id="cur-home"><span>Current Home</span> '.$this->us['slat'].', '.$this->us['slong'].' (Zoom: '.$this->us['zoom'].')</p>'."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
	}

	private function firstAdmin(){
		echo '<script src="'.siteUrl().'js/map/first.admin.js"></script>';
		echo '<div class="first-load fc">'."\n";
		echo 'Welcome to MappMe'."\n";
		echo '</div>'."\n";
		echo '<div class="second-load">'."\n";
		echo '<p>Hello there '.$this->us['usn'].' and welcome to MappMe.</p>'."\n";
		echo '<p>Before you start using MappMe we need to sort your mapping key and give you an access code.</p>'."\n";
		echo '<br>'."\n";
		echo '<p>MappMe requires a Google Maps API key. You can get one <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">here</a>.</p>'."\n";
		echo '<p><span>Please enter your API key into the box below.</span> Entering an invalid key will cause MappMe to work incorrectly.</p>'."\n";
		echo '<input type="text" id="map-key" class="first-text">'."\n";
		echo '<p>You will be able to change more settings once you start using MappMe.</p>'."\n";
		echo '<h1 class="c-h1-i">Your generated code is</h1>'."\n";
		echo '<h1 id="gen-code" class="c-h1-i">'.$this->genAC(8).'</h1>'."\n";
		echo '<p class="c-h1-i"><span>Your access code is case sensitive.</span></p>'."\n";
		echo '<p>Enter this code into the "Access Code" field on your MappMe Transmitter app on your phone or tablet. This will allow you to send your location and will be tied directly to your account.</p>'."\n";
		echo '<p>This will be the only time that you see this code on the MappMe web client. You can generate a new one at a later date.</p>'."\n";
		echo '<p>You will need your access code (from your mobile device) incase you want to change your password or if you forget it.</p>'."\n";
		echo '<br>'."\n";
		echo '<p>Once you&#39;ve entered your API key into the box above and code into your device hit the button below. MappMe will then save your details.</p>'."\n";
		echo '<input type="button" id="first-save" class="first-button" value="Save My Details">'."\n";
		echo '</div>';
	}

	private function firstUser(){
		echo '<script src="'.siteUrl().'js/map/first.standard.js"></script>';
		echo '<div class="first-load fc">'."\n";
		echo 'Welcome to MappMe'."\n";
		echo '</div>'."\n";
		echo '<div class="second-load">'."\n";
		echo '<p>Hello there '.$this->us['usn'].' and welcome to MappMe.</p>'."\n";
		echo '<p>Before you start using MappMe we need to give you an access code.</p>'."\n";
		echo '<h1 class="c-h1-i">Your generated code is</h1>'."\n";
		echo '<h1 id="gen-code" class="c-h1-i">'.$this->genAC(8).'</h1>'."\n";
		echo '<p class="c-h1-i"><span>Your access code is case sensitive.</span></p>'."\n";
		echo '<p>Enter this code into the "Access Code" field on your MappMe Transmitter app on your phone or tablet. This will allow you to send your location and will be tied directly to your account.</p>'."\n";
		echo '<p>This will be the only time that you see this code on the MappMe web client. You can generate a new one at a later date.</p>'."\n";
		echo '<p>You will need your access code (from your mobile device) incase you want to change your password or if you forget it.</p>'."\n";
		echo '<br>'."\n";
		echo '<h1 class="c-h1-i">Your receive URL is</h1>'."\n";
		echo '<h1 class="c-h1-i">'.siteUrl().'</h1>'."\n";
		echo '<p>Enter the URL above into your <span>Receive URL</span> box on your phone.</p>'."\n";
		echo '<p>Once you&#39;ve entered your code into your device hit the button below and MappMe will save your details.</p>'."\n";
		echo '<p>You can change your password on the next page.</p>'."\n";
		echo '<input type="button" id="first-save" class="first-button" value="Save My Details">'."\n";
		echo '</div>';
	}

	private function genAC($length){
		// Generate a random access code for the first loads
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		// Check and make sure the access code isn't already in use.
		try{
			$acPDO = new PDO('mysql:host='.$this->db['server'].';dbname='.$this->db['database'].';charset=utf8', $this->db['standard']['usn'], $this->db['standard']['psw'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			$acCheck = $acPDO->prepare('SELECT access_code FROM `'.$this->db['prefix'].'users` WHERE access_code = :ac');
			$acCheck->bindValue(':ac',$randomString,PDO::PARAM_STR);
			$acCheck->execute();
			$acRows = $acCheck->rowCount();
		} catch(PDOException $ex){
			echo $ex;
		}
		if($acRows == 0){
			$acPDO = null;
			return $randomString;
		} else {
			$acPDO = null;
			$this->genAC($length);
		}
		
	}

	function __destruct(){
		$this->pdo = null;
		$this->db = null;
		$this->us = null;
		$this->markers = null;
		$this->markerRows = null;

	}

}

?>