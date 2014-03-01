<?php
// Class to build the standard page layout
class Page{

	// Private variables only read by the class
	private $title;
	private $page;
	private $css;
	private $js;
	
	// Loads in the page variables
	function __construct($settings){
		$this->title = $settings['title'];
		$this->css = $settings['css'];
		$this->js = $settings['js'];
		$this->page = $settings['type'];
		// Prints required HTML items
		echo '<!DOCTYPE html>'."\n".'<html>';
		echo "\n".'<meta charset="UTF-8">';
		if($this->title == ""){
			echo "\n<title>MappMe</title>";
		} else {
			echo "\n<title>".$this->title." | MappMe</title>";
		}
		// Please do not remove this!
		echo "\n".'<meta name="generator" content="MappMe" />';
		echo "\n".'<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />';
		echo "\n".'<link rel="icon" href="favicon.ico" type="image/x-icon">';
		$this->addCSS();
		$this->addJS();
	}

	function finHead(){
		echo "\n</head>\n<body>\n";
	}

	// Automatically add in the page footer on page end (destruct)
	function __destruct(){
		if($this->page == 'map'){
			echo "\n".'<div id="mm-d-bg">'."\n";
			echo '<div id="mm-d-bg-rs">'."\n";
			echo '<div id="mm-dialogue">'."\n";
			echo '<input type="button" id="mm-d-close" value="Close">'."\n";
			echo '<div id="mm-d-title" class="fc">'."\n";
			echo '</div>'."\n";
			echo '<div id="mm-d-rs">'."\n";
			echo '<div id="mm-d-content">'."\n";
			echo '</div>'."\n";
			echo '</div>'."\n";
			echo '</div>'."\n";
			echo '</div>'."\n";
			echo '</div>';
		}
		echo "\n".'<div id="mm-notification">';
		echo "\n".'<div id="mm-n-title" class="fc"></div>';
		echo "\n".'<div id="mm-n-loading-rs"><div id="mm-n-loading"></div></div>';
		echo "\n".'<div id="mm-n-rs"><div id="mm-n-text"></div></div>';
		echo "\n".'<input type="button" id="mm-n-close" value="Close">';
		echo "\n</div>\n</body>\n</html>";
	}

	// Load in the css
	private function addCSS(){
		$rounds = 0;
		$count = count($this->css);
		while($rounds < $count){
			echo "\n<link href='".siteUrl()."css/".$this->css[$rounds].".css' rel='stylesheet' type='text/css'>";
			$rounds++;
		}
	}

	// Load in the js
	private function addJS(){
		$rounds = 0;
		$count = count($this->js);
		while($rounds < $count){
			echo "\n<script src='".siteUrl()."js/".$this->js[$rounds].".js'></script>";
			$rounds++;
		}
	}

}

?>