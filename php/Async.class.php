<?php
class Async extends Activity
{
	public static function each(Array $arr, Closure $fn)
	{
		// print_r($arr); die;
		if(!empty($arr))
		{
			foreach($arr as $v)
			{
				if(function_exists("pcntl_fork"))
				{
					if(DEBUG)
					{
						echo "$v queued..." . PHP_EOL;
					}

					$status = null;
					$pid = pcntl_fork();
					switch($pid)
					{
						case -1: die('could not fork'); break;
						case  0: $fn($v); 				break; 
						default: pcntl_wait($status); 	break;
					}

					if(DEBUG)
					{
						echo "$v finished..." . PHP_EOL;
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