<?php
class Async extends Activity
{
	public static function each(Array $arr, Closure $fn)
	{
		if(!empty($arr))
		{
			$status = null;
			foreach($arr as $v)
			{
				if(function_exists("pcntl_fork"))
				{
					// die("exists");
					switch($pid = pcntl_fork())
					{
						case -1: die('could not fork'); 		break;
						case  0: $fn($v); 						break; 
						default: pcntl_waitpid($pid, $status); 	break;
					}
				}
				else
				{
					$fn($v);
				}
			}
		}
	}
}