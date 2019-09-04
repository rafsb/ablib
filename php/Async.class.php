<?php
class Async extends Activity
{
	public static function each(Array $arr, Closure $fn)
	{
		if(!empty($arr)) parallel\run(function($o) use ($fn){
			require __DIR__ . DIRECTORY_SEPARATOR . "autoload.php";
			//print_r($autoLoadFunction);
			// echo __DIR__ . DIRECTORY_SEPARATOR . "autoload.php\n";
			// spl_autoload_register($autoLoadFunction);
			$fn($o);
		}, $arr);
	}
}