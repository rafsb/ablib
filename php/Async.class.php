<?php
class Async extends Activity
{
	public static function each(Array $arr, Closure $fn)
	{
		if(!empty($arr))
		{
			foreach($arr as $k => $v)
			{
				if(function_exists("pcntl_fork"))
				{
					$pid = pcntl_fork();
					if ($pid == -1) {
					     die('could not fork');
					} else if ($pid) {
					     pcntl_wait($status);
					} else {
					    $fn(Convert::atoo(["key"=>$k, "value"=>$v, "pid"=>$pid]));
					    break; 
					}	
				}
				else
				{
					$fn(Convert::atoo(["key"=>$k, "value"=>$v]));
				}
			}
		}
	}
}