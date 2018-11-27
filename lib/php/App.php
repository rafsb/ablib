<?php
define ("DEFAULT_DB","spumedb");
define("DEBUG",true);

class App {
	public function mysql_config($database=DEFAULT_DB){
		$_DATABASES = IO::jout(IO::root("webroot/conf.json"));
		return isset($_DATABASES->datasources) && isset($_DATABASES->datasources->{$database}) ? $_DATABASES->datasources->{$database} : null;
	}

	public function config($field=null){
		$_CONFIG = IO::jout(IO::root("webroot/conf.json"));
		if($field && isset($_CONFIG->{$field})) return $_CONFIG->{$field};
		return $_CONFIG;
	}

	public function devel(){ return App::config("developer"); }

	public function project_name(){ return App::config("project_name"); }

}