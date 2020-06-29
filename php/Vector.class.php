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

	

	public static function fourier(Array $mix, bool $sign=trua) {

	   $n = count($mix);
	   $j = 1;

	   for ($i = 1; $i < $n; $i += 2) {
	      if ($j > $i) {
	         list($data[($j+0)], $data[($i+0)]) = array($data[($i+0)], $data[($j+0)]);
	         list($data[($j+1)], $data[($i+1)]) = array($data[($i+1)], $data[($j+1)]);
	      }

	      $m = $n >> 1;

	      while (($m >= 2) && ($j > $m)) {
	         $j -= $m;
	         $m = $m >> 1;
	      }

	      $j += $m;

	   }

	   $mmax = 2;
	 
	   while ($n > $mmax) {  # Outer loop executed log2(nn) times
	      $istep = $mmax << 1;

	      $theta = $isign * 2*pi()/$mmax;

	      $wtemp = sin(0.5 * $theta);
	      $wpr   = -2.0*$wtemp*$wtemp;
	      $wpi   = sin($theta);
	 
	      $wr = 1.0;
	      $wi = 0.0;
	      for ($m = 1; $m < $mmax; $m += 2) {  # Here are the two nested inner loops
	         for ($i = $m; $i <= $n; $i+= $istep) {

	            $j = $i + $mmax;

	            $tempr = $wr * $data[$j]     - $wi * $data[($j+1)];
	            $tempi = $wr * $data[($j+1)] + $wi * $data[$j];

	            $data[$j]     = $data[$i]     - $tempr;
	            $data[($j+1)] = $data[($i+1)] - $tempi;

	            $data[$i]     += $tempr;
	            $data[($i+1)] += $tempi;

	         }
	         $wtemp = $wr;
	         $wr = ($wr * $wpr) - ($wi    * $wpi) + $wr;
	         $wi = ($wi * $wpr) + ($wtemp * $wpi) + $wi;
	      }
	      $mmax = $istep;
	   }

	   for ($i = 1; $i < count($data); $i++) { 
	      $data[$i] *= sqrt(2/$n);                   # Normalize the data
	      if (abs($data[$i]) < 1E-8) $data[$i] = 0;  # Let's round small numbers to zero
	      $mix[($i-1)] = $data[$i];                # We need to shift array back (see beginning)
	   }

	   return $mix;

	}

	public function test(){

		// $a =  [
		// 	"0" => 2318.2418
		// 	, "1" => 2517.8829333333238
		// 	, "2" => 2259.7786666666752
		// 	, "3" => 2351.638933333324
		// 	, "4" => 2322.0694
		// 	, "5" => 2298.2210000000005
		// 	, "6" => 2404.4522666666758
		// 	, "7" => 2506.4970666666763
		// 	, "9" => 2549.3887999999993
		// 	, "10" => 2659.0203333333225
		// 	, "11" => 2771.6392
		// 	, "12" => 3053.0719999999997
		// 	, "14" => 3345.7405333333195
		// 	, "15" => 2724.0887999999995
		// ];
		
		// echo Vector::interpolate($a, 8);

		$i = 10;
		$m = 10;
		echo "$i" . PHP_EOL;
		$i = $m >> 1;
		echo "$i" . PHP_EOL;
		$i = $m >> 1;
		echo "$i" . PHP_EOL;
		$i = $m >> 1;
		echo "$i" . PHP_EOL;
		$i = $m >> 1;
		echo "$i" . PHP_EOL;
	}
}