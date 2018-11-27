<?php
class Page {
	public function args($arg=null){
		if($arg && isset($this -> argv) && isset($this -> argv[$arg])) return $this -> argv[$arg];
		return $this -> argv;
	}

	public function loadView($view){
		$view = IO::root() . "webroot" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $view . ".php";
		if(is_file($view)) include_once $view;
		else if(DEBUG) echo PHP_EOL . "NO VIEW FOUND: V[$view]";
	}

	public function render(){
		$view = IO::root() . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . get_called_class() . ".php";
		if(is_file($view)) include_once $view;
	}

	public function __construct(){
		$this -> argv = Request::in();
	}
}