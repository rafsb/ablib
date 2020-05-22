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

	public static function similarity(Array $arr1, Array $arr2){

		$x = 0;
		Vector::each($arr1, function($v, $i) use (&$x){ $x += sqrt($v*$v + $i*$i); });
		$x = pow(sizeof($arr1), 2) !== 0 ? $x / pow(sizeof($arr1), 2) : 0;
		
		$y = 0;
		Vector::each($arr2, function($v, $i) use (&$y){ $y += sqrt($v*$v + $i*$i); });
		$y = $y / pow(sizeof($arr2), 2);

		return abs(($x - $y) / ((sizeof($arr1) + sizeof($arr2)) / 2));
	}

	public function test(){

		$s = 0;
		$e = 10;
		$i = $e;
		while(--$i){
			
			$a = [];
			$b = [];
			Loop::iterate(0, 20, function($i) use (&$a, &$b, $e){ $a[] = rand(0,$e); $b[] = rand(0,$e); });
			echo ($e - $i) . ") ";
			$s = self::similarity($a, $b);
			echo "ab = $s";
			$s = self::similarity($a, $a);
			echo ", aa = $s";
			$s = self::similarity($b, $b);
			echo ", bb = $s" . PHP_EOL;

		}
	}

	public static function fit(Array $arr, int $fit=10){

		if(!sizeof($arr)) return [];
        $narr = [ $arr[0] ];
        $x = sizeof($arr) / ($fit - 1);
        $i = $x;

        while($i<sizeof($arr)){
            $narr[] = self::calc($arr, $i);
            $i+=$x;
        }

        $narr[] = $arr[sizeof($arr)-1];
        return $narr;
    
    }	

}