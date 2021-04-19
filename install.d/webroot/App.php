<?php


define("DEBUG", 				true);
define("USER_ONLY_HASH_MODE",	true);
define("API_NEEDS_DEVICE_HASH", false);
define("SHADOW_FILE", 			"var/users/shadow");


class App extends Application
{
	// @override
	private static $datasources = [	
		"default" => [
			"host" 		 => "127.0.0.1"
            , "username" => "root"
        	, "passwd"   => "root"
	        , "database" => "test"
        	, "encoding" => "utf8"
		]
	];

}