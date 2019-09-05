<?php
class Async extends Activity
{
	public static function each(Array $arr, Closure $fn)
	{
		if(!empty($arr)) parallel\run(function($o) use ($fn){
			require_once __DIR__ . DIRECTORY_SEPARATOR . "constants.php";
			require_once __DIR__ . DIRECTORY_SEPARATOR . "autoload.php";
			$fn($o);
		}, $arr);
	}
}