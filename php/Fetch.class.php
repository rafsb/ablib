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
		return Core::response($ret, "fetch call with curl pass");
	}


	public static function request($url,$fields=null,$fn=null,$method=GET)
	{
		if($url)
		{
			$ret = self::call($url,$fields,$method);
			return $fn ? Core::response($fn($ret), "request with callback pass") : Core::response($ret, "request pass");
		}
		return Core::response(0, "no URL given");
	}

	public static function get($url,$fields=null,$fn=null)
	{
		return self::request($url,$fields,$fn,GET);
	}

	public static function post($url,$fields=null,$fn=null)
	{
		return self::request($url,$fields,$fn,POST);
	}

}