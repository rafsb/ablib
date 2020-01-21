<?php
class Vector extends Activity {

	public static function extract(Array $arr, Closure $fn){
		$return = [];
		if(!empty($arr)){
			foreach($arr as $k=>$v){
				$tmp = $fn($v,$k);
				if($tmp !== null) $return[] = $tmp;
			}
		}
		return $return;
	}

	public static function each(Array $arr, Closure $fn){
		// print_r($arr); die;
		if(!empty($arr)){
			foreach($arr as $k=>$v){
				if(function_exists("pcntl_fork")){
					if(DEBUG) echo "$k queued... ";

					$status = null;
					$pid = pcntl_fork();
					switch($pid){
						case -1: die('could not fork'); break;
						case  0: $fn($v,$k); 				break; 
						default: pcntl_wait($status); 	break;
					}

					if(DEBUG) echo "finished..." . PHP_EOL;
				} else $fn($v,$k);
			}
		}
	}
}