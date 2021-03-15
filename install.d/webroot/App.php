<?php
class App extends Application
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
            , "username" => "root"
        	, "passwd"   => "root"
	        , "database" => "test"
        	, "encoding" => "utf8"
		]
	];

	// @override
	private static $datasources = [	];

}