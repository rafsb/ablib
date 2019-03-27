<?php
define ("DEFAULT_DB","spumedb");
define("DEBUG",1);

class App {
	public static  function mysql_config($database=DEFAULT_DB){
		$file = IO::root() . "etc/sql.d/" . $database . ".json";
		if(is_file($file)) $db = IO::jout($file);
		else $db = null;
		if($db) $db->{'base'} = $database;
		return $db;
	}

	public static  function config($field=null){
		$_CONFIG = IO::jout(IO::root("etc/project.json"));
		if($field && isset($_CONFIG->{$field})) return $_CONFIG->{$field};
		return $_CONFIG;
	}

	public static function devel(){ return App::config("developer"); }

	public static function project_name(){ return App::config("project_name"); }

}