<?php

if(!defined("DEBUG"))                   define("DEBUG",                 true);
if(!defined("API_NEEDS_LOGIN"))         define("API_NEEDS_LOGIN",       true);
if(!defined("API_NEEDS_DEVICE_HASH"))   define("API_NEEDS_DEVICE_HASH", false);
if(!defined("API_MAX_LOG_LINES"))       define("API_MAX_LOG_LINES",     1024);

class App extends Application
{
	// @override
	protected static $config = [
    	"developer"                 => "DEV Team"
        , "project_name"            => "Project_name"
        , "driver"                  => EPersistances::DISK
        , "get_config_min_level"    => EUsers::MANAGER
        , "hash_algorithm"          => EHashes::SHA512
        , "encrypt_cipher"          => "aes-128-gcm"//openssl_get_cipher_methods()[0]
        , "encrypt_key"             => "ZmFhdQ=="
        , "database_credentials"    => [
			"host" 		 => "127.0.0.1"
            , "username" => "root"
        	, "passwd"   => "root"
	        , "database" => "test"
        	, "encoding" => "utf8"
		]
        , "twitter_api" => [
            "token_uri" => "https://api.twitter.com/oauth2/token?grant_type=client_credentials"
            , "api_key"    => "sRJlxgISKEIt0p86pso91lJNE"
            , "api_secret" => "WY1ezEB0f85sih8kDwMh2vofJXsaJ3JMukwdjEUSAhvWevT9Nf"
        ]
        , "twitter_api_30day" => [
            "tweets_uri"    => "https://api.twitter.com/1.1/tweets/search/30day/alpha.json"
            , "bearer"     => "AAAAAAAAAAAAAAAAAAAAAGdXAAEAAAAARIudW%2Fo6kdciMmj0b5ReV7jZAgI%3DLVqbVoDW5TToJ9OlNMA0GF3uRP6mv3WfeP15IOUEUdtqlL5mu6"
        ]
	];

	// @override
	protected static $datasources = [ ];
}