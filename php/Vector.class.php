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

	public static function calc(Array $arr, int $alg=SUM, float $at=null){

		$return = 0;

		switch ($alg){
            
            case (SUM):
            	Vector::each($arr, function($x) use (&$return){ $return+=$x; }); 
            	return $return;
            
            case (AVERAGE): 
            	return self::calc($arr, SUM)/sizeof($arr);

            case (HARMONIC):
            	Vector::each($arr, function($x) use (&$return){ $return+=$x ? (1/$x) : 0; });
            	return sizeof($arr) / $return;
            
            case (TREND):
                $m = $b = $x = $y = $x2 = $xy = $z = 0;
                $np = sizeof($arr);
                if($at === null) $at = $np;
                Vector::each($arr, function($n, $i) use (&$m, &$b, &$x, &$y, &$x2, &$xy, &$z, &$return){
                    $x = $x + $i;
                    $y = $y + $n;
                    $xy = $xy + $i * $n;
                    $x2 = $x2 + $i * $i;
                });
                $z = $np*$x2 - $x*$x;
                if($z){
                    $m = ($np*$xy - $x*$y)/$z;
                    $b = ($y*$x2 - $x*$xy)/$z;
                }
                return $m * $at + $b;
            
            /* TODO POLINOMIAL FORMULA */
            case (POLINOMIAL): break;

            case (PROGRESS) :
                return Vector::calc(Vector::extract($arr, function($x ,$i) use ($arr){ return $i&&$arr[$i-1] ? $arr[$i]/$arr[$i-1] : 1; }), AVERAGE);

            case (MAX):
                return max($arr);
            
            case (MIN):
            	return min($arr);

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