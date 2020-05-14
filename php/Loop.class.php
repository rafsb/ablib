<?php
class Loop extends Activity
{
	public static function iterate(int $beg, int $end, Closure $fn){
		$sig = $beg < $end ? 1 : -1;
		for($i=$beg; $i !== $end; $i+=$sig) $fn($i);
	}

	public static function async(int $beg, int $end, Closure $fn){
		$sig = $beg < $end ? 1 : -1;
		for($i=$beg; $i !== $end; $i+=$sig){
			if(function_exists("pcntl_fork")){
				$status = null;
				$pid = pcntl_fork();
				if(!$pid){ 
					$fn($i); 
					exit;
				}
				pcntl_wait($status);
			} else self::iterate($beg, $end, $fn);
		}
	}
}