<?php
define ("DEFAULT_DB","spumedb");
define("DEBUG",1);

class App {
	public function mysql_config($database=DEFAULT_DB){
		$_DATABASES = IO::jout(IO::root("etc/sql.d/$database.json"));
		return $_DATABASES ? $_DATABASES->datasources->{$database} : null;
	}

	public function config($field=null){
		$_CONFIG = IO::jout(IO::root("etc/project.json"));
		if($field && isset($_CONFIG->{$field})) return $_CONFIG->{$field};
		return $_CONFIG;
	}

	public function devel(){ return App::config("developer"); }

	public function project_name(){ return App::config("project_name"); }

}