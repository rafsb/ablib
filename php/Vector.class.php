<?php
class Vector extends Activity {

	public static function extract(Array $arr, Closure $fn){
		$return = [];
		if(!empty($arr)){
			foreach($arr as $k=>&$v){
				$tmp = $fn($v,$k);
				if($tmp !== null) $return[] = $tmp;
			}
		}
		return $return;
	}


	public static function each(Array $arr, Closure $fn){
		if(!empty($arr)) foreach($arr as $k=>&$v) $fn($v,$k);
	}

	public static function async(Array $arr, Closure $fn){
		if(!empty($arr)){
			foreach($arr as $k=>&$v){				
				if(function_exists("pcntl_fork")){
					$status = null;
					$pid = pcntl_fork();
					if(!$pid){ 
						$fn($v,$k); 
						die;
					}
					pcntl_wait($null);
				} else self::each($arr, $fn);
			}
		}
	}

}