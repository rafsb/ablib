<?php
if(!defined("DEBUG")) define("DEBUG",  true);
if(!defined("LOGIN_REQUIRED")) define("LOGIN_REQUIRED",  false);

class App
{
	private static $config = [
    	"developer"                 => "DEV Team"
        , "project_name"            => "MobileApi"
        , "driver"                  => DISK
        , "get_config_min_level"    => MANAGER
        , "hash_algorithm"          => SHA512
        , "database_credentials"    => [
			"host" 		 => "127.0.0.1"
            , "username" => "root"
        	, "passwd"   => ""
	        , "database" => "test"
        	, "encoding" => "utf8"
		]
	];

	private static $datasources = [

    	"default"  => []

	];

	public static function connections($datasource=DEFAULT_DB)
	{
    	$tmp = isset(self::$datasources[$datasource]) ? self::$datasources[$datasource] : [];
		$config = self::$config["database_credentials"];
		
    	if(!isset($tmp["host"]))     $tmp["host"]     = $config["host"];
    	if(!isset($tmp["username"])) $tmp["username"] = $config["username"];
    	if(!isset($tmp["passwd"]))   $tmp["passwd"]   = $config["passwd"];
    	if(!isset($tmp["database"])) $tmp["database"] = $config["database"];
    	if(!isset($tmp["encoding"])) $tmp["encoding"] = $config["encoding"];

		return $tmp;
	}

    public function driver($drv=null) {
       	return $drv ? ($drv==self::config("driver") ? true : false) : self::config("driver");
    }

	public static  function config($field=null) {
		if($field && isset(self::$config[$field])) return self::$config[$field];
		return User::level(self::$config["get_config_min_level"]) ? self::$config : null;
	}

	public static function devel() {
		return App::config("developer");
	}

	public static function project_name() {
		return App::config("project_name");
	}

	public static function init() {
		if(!LOGIN_REQUIRED||User::logged()) (new Home)->render();
		else (new Login)->render();
	}
}

