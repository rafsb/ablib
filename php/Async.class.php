<?php
class Async extends Activity
{
	public static function each(Array $arr, Closure $fn)
	{
		$break = 10;
		if(!empty($arr))
		{
			foreach($arr as $k => $v)
			{
				if($break-->0)
				{
					$pid = pcntl_fork();
					// if(!$pid)
					// {
						$fn(Convert::atoo(["key"=>$k, "value"=>$v, "pid"=>$pid]));
						break;
					// }
					// else if($pid == -1)
					// {
					// 	Core::response(0, "errors found...");
					// 	exit();
					// }
					// else
					// {
					// 	pcntl_wait($status);
					// 	Core::response($status,"process finished with no errors...");
					// 	Debug::show();
					// }
				}
			}
		// if(!empty($arr)) parallel\run(function($o) use ($fn){
		// 	require_once __DIR__ . DIRECTORY_SEPARATOR . "constants.php";
		// 	require_once __DIR__ . DIRECTORY_SEPARATOR . "autoload.php";
		// 	$fn($o);
		// }, $arr);
		}
	}
}