<?php
define ("DEFAULT_DB","spumedb");
define("DEBUG",true);

class App {
	public function mysql_config($database=DEFAULT_DB){
		$_DATABASES = [
			DEFAULT_DB => [
				"base" => "spumedb",
				"user" => "spume",
				"pass" => "spudba",
				"host" => "127.0.0.1"
			]
		];
		return isset($_DATABASES[$database]) ? Convert::atoo($_DATABASES[$database]) : null;
	}

	public function config($field=null){
		$_CONFIG = [
			"Developer_senior" 	=> "rafsb",
			"Developer_jr" 		=> "sugar",
			"Copyright" 		=> "spume.co",
			"Project_name" 		=> "Long Time Tools"
		];
		if($field && isset($_CONFIG[$field])) return $_CONFIG[$field];
		return $_CONFIG;
	}

	public function devel(){ return App::config("Developer"); }

	public function project_name(){ return App::config("Project_name"); }

}