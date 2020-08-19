<?php
if(!defined("POST")) define("POST","POST");
if(!defined("GET"))  define("GET","GET");

class Fetch extends Activity 
{

	private static function call($url,$fields=null,$method=GET)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url . ($method==GET?"?".Convert::atoh($fields):""));
		if($method==POST)
		{
			curl_setopt($c, CURLOPT_POST, 1);
			curl_setopt($c, CURLOPT_POSTFIELDS, Convert::atoh($fields));
			curl_setopt($c, CURLOPT_CUSTOMREQUEST, "POST");
		}
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		$ret = curl_exec($c);
		curl_close ($c);
		return $ret;
	}


	public static function request($url,$fn=null,$method=GET,$fields=null)
	{
		if($url)
		{
			$ret = self::call($url,$fields,$method);
			return $fn ? $fn($ret) : $ret;
		}
		return Core::response(0, "no URL given");
	}

	public static function get($url,$fn=null,$fields=null)
	{
		return self::request($url,$fn,GET,$fields);
	}

	public static function post($url,$fn=null,$fields=null)
	{
		return self::request($url,$fn,POST,$fields);
	}

}