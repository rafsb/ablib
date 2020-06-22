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

	public static function iterate(float $beg, float $end, Closure $fn, float $step=null){
		$step = $step ? $step : 1.0;
		for(;$beg!=$end;$beg+=$step) $fn($beg);
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

    public static function sum(Array $mix) {
    	$x = 0;
		self::each($mix, function($y) use (&$x){ $x+=$y; });
		return $x;
	}

	public static function average(Array $mix) {
		return self::sum($mix) / sizeof($mix);
	}

	public static function harmonic_average(Array $mix) {
		$x = 0;
		self::each($mix, function($y) use (&$x){ $x+=(1/$y); });
		return sizeof($mix) / $x;
	}

	public static function linear_trend(Array $mix, Number $h=null) {
        $np = sizeof($mix);
        $m = $b = $x = $y = $x2 = $xy = 0;
        if($h===null) $h = $np;
        self::each($mix, function($n, $i) use (&$x, &$y, &$xy, &$x2){
            $x = $x + $i;
            $y = $y + $n;
            $xy = $xy + $i * $n;
            $x2 = $x2 + $i * $i;
        });
        $z = $np * $x2 - $x * $x;
        if($z){
            $m = ($np * $xy - $x * $y) / $z;
            $b = ($y * $x2 - $x * $xy) / $z;
        }
        return $m * $h + $b;
	}

	public static function interpolate(Array $mix, float $x=null) {
		if($x===null) $x = count($mix)+1;
        $yi = $mix;
        $xi = array_keys($mix);
        $n  = count($mix);
        $sum = 0;
        self::iterate(0, $n, function($k) use ($x, $xi, $yi, $n, &$sum){
        	$prod = 1;
        	Vector::iterate(0, $n, function($i) use ($x, $xi, $k, &$prod){
        		if ($i!=$k) $prod = $prod * ($x - $xi[$i]) / ($xi[$k] - $xi[$i]);
        	});
            $sum += $yi[$k] * $prod;
        });
        return $sum;
	}

}