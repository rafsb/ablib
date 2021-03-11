<?php

// define("USER", 0);
// define("EDITOR", 1);
// define("ADMIN", 7);
// define("ROOT", 8);
// define("DEV", 9);

class Application
{
	// @override
	private static $config = [
    	"developer"                 => "DEV Team"
        , "project_name"            => "MobileApi"
        , "driver"                  => DISK
        , "get_config_min_level"    => MANAGER
        , "hash_algorithm"          => SHA512
        , "database_credentials"    => [
			"host" 		 => "127.0.0.1"
            , "user"     => "root"
        	, "pass"     => ""
	        , "database" => "test"
			, "encoding" => "utf8"
			, "port"     => "3306"
		]
	];

	// @override
	private static $datasources = [	];

	public static function connections($datasource=DEFAULT_DB)
	{

		$tmp = [];

		if(isset(self::$datasources[$datasource])) $tmp[$datasource] = self::$datasources[$datasource];
		else if(is_file(IO::root("etc/sql.d/conf/$datasource.json"))) $tmp = (array)IO::jout("etc/sql.d/conf/$datasource.json");

    	if(!isset($tmp["host"]))     $tmp["host"]     = static::$config["database_credentials"]["host"];
    	if(!isset($tmp["user"]))     $tmp["user"]     = static::$config["database_credentials"]["user"];
    	if(!isset($tmp["pass"]))     $tmp["pass"]     = static::$config["database_credentials"]["pass"];
    	if(!isset($tmp["database"])) $tmp["database"] = static::$config["database_credentials"]["database"];
    	if(!isset($tmp["encoding"])) $tmp["encoding"] = static::$config["database_credentials"]["encoding"];

		return $tmp;
	}

    public static function driver($drv=null) {
       	return $drv ? ($drv==self::config("driver") ? true : false) : self::config("driver");
    }

	public static function config($field=null) 
	{
		if($field && isset(static::$config[$field])) return static::$config[$field];
		return Convert::atoo(static::$config);
	}

	public static function devel() {
		return App::config("developer");
	}

	public static function project_name() {
		return App::config("project_name");
	}

	public static function init() {
		if(!API_NEEDS_LOGIN||User::logged()) (new Home)->render();
		else (new Login)->render();
	}
}