<?php
class Core {
	
	private static $config = 
	[

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

	private static $datasources =
	[

    	"default"  => []

	];

	public static function response($status,$data){
		$data = ["status"=>$status,"data"=>$data];
		if(DEBUG)
		{
			Request::sess("DEBUG",array_merge(is_array(Request::sess("DEBUG"))?Request::sess("DEBUG"):[],$data));
			IO::log(json_encode($data) . PHP_EOL, JSON_PRETTY_PRINT);
		}
		return $status;
	}
	
	public static function bin($bin,$args=null){
		$cmd = "sh " . IO::root("src/bin") . DS . $bin;
		if($args&&is_array($args)) foreach($args as $a) $cmd .= " " . $a;
		return shell_exec($cmd);
	}

	public static function connections($datasource=DEFAULT_DB)
	{
    	$tmp = isset(self::$datasources[$datasource]) ? self::$datasources[$datasource] : [];

    	if(!isset($tmp["host"]))     $tmp["host"]     = self::$config["database_credentials"]["host"];
    	if(!isset($tmp["username"])) $tmp["username"] = self::$config["database_credentials"]["username"];
    	if(!isset($tmp["passwd"]))   $tmp["passwd"]   = self::$config["database_credentials"]["passwd"];
    	if(!isset($tmp["database"])) $tmp["database"] = self::$config["database_credentials"]["database"];
    	if(!isset($tmp["encoding"])) $tmp["encoding"] = self::$config["database_credentials"]["encoding"];

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

}