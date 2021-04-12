<?php
class Vector extends Activity {

	public static function of_size(int $x)
	{
		return array_keys(array_fill(0, $x > 0 ? $x : 1, null));
	}

	public static function extract(Array $arr, Closure $fn)
	{
		$return = [];
		if(!empty($arr))
		{
			foreach($arr as $k=>&$v)
			{
				$tmp = $fn($v,$k);
				if($tmp !== null) $return[] = $tmp;
			}
		}
		return $return;
	}

	public static function clear(array $arr)
	{
		return array_slice(array_filter($arr, function($item){ return trim($item); }), 0);
	}

	public static function iterate(float $beg, float $end, Closure $fn, float $step=null)
	{
		$step = $step ? $step : 1.0;
		for(;$beg!=$end;$beg+=$step) $fn($beg);
	}

	public static function parallel(float $beg, float $end, Closure $fn, float $step=null)
	{
		$step = $step ? $step : 1.0;
		for(;$beg!=$end;$beg+=$step)
		{
			if(function_exists("pcntl_fork"))
			{
				$status = null;
				$pid = pcntl_fork();
				if(!$pid)
				{ 
					$fn($beg); 
					die;
				}
				pcntl_wait($null);
			} else $fn($beg);
		}
	}

	public static function each(Array $arr, Closure $fn)
	{
		if(!empty($arr)) foreach($arr as $k=>&$v) $fn($v,$k);
	}

	public static function async(Array $arr, Closure $fn)
	{
		if(!empty($arr))
		{
			foreach($arr as $k=>&$v)
			{				
				if(function_exists("pcntl_fork"))
				{
					$pid = pcntl_fork();
					if(!$pid)
					{ 
						$fn($v,$k); 
						die;
					}
					pcntl_wait($null);
				} else self::each($arr, $fn);
			}
		}
	}

	public static function similarity(Array $arr1, Array $arr2)
	{

		$x = 0;
		Vector::each($arr1, function($v, $i) use (&$x)
		{ $x += sqrt($v*$v + $i*$i); });
		$x = pow(sizeof($arr1), 2) !== 0 ? $x / pow(sizeof($arr1), 2) : 0;
		
		$y = 0;
		Vector::each($arr2, function($v, $i) use (&$y)
		{ $y += sqrt($v*$v + $i*$i); });
		$y = $y / pow(sizeof($arr2), 2);

		return abs(($x - $y) / ((sizeof($arr1) + sizeof($arr2)) / 2));
	}

	public static function fit(Array $arr, int $fit=10)
	{

		if(!sizeof($arr)) return [];
        $narr = [ $arr[0] ];
        $x = sizeof($arr) / ($fit - 1);
        $i = $x;

        while($i<sizeof($arr))
		{
            $narr[] = self::calc($arr, $i);
            $i+=$x;
        }

        $narr[] = $arr[sizeof($arr)-1];
        return $narr;
    
    }

    public static function sum(Array $mix) {
    	$x = 0;
		self::each($mix, function($y) use (&$x)
		{ $x+=$y; });
		return $x;
	}

	public static function average(Array $mix) {
		return self::sum($mix) / sizeof($mix);
	}

	public static function harmonic_average(Array $mix) {
		$x = 0;
		self::each($mix, function($y) use (&$x)
		{ $x+=(1/$y); });
		return sizeof($mix) / $x;
	}

	public static function linear_trend(Array $mix, int $h=null) {
        $np = sizeof($mix);
        $m = $b = $x = $y = $x2 = $xy = 0;
        if($h===null) $h = $np;
        self::each($mix, function($n, $i) use (&$x, &$y, &$xy, &$x2)
		{
            $x = $x + $i;
            $y = $y + $n;
            $xy = $xy + $i * $n;
            $x2 = $x2 + $i * $i;
        });
        $z = $np * $x2 - $x * $x;
        if($z)
		{
            $m = ($np * $xy - $x * $y) / $z;
            $b = ($y * $x2 - $x * $xy) / $z;
        }
        return $m * $h + $b;
	}

	public static function interpolate(Array $mix, float $x=null) {
		$result = .0;
		// $xarr = array_keys($mix);
		Vector::each($mix, function($yi, $xi) use ($mix, $x, &$result)
		{
			$lag = 1;
			Vector::each($mix, function($yn, $xn) use ($x, $xi, &$lag)
			{
				if($xn != $xi) $lag *= ( ($x - $xn) / ($xi - $xn) );
			});
			$result += ( $yi * $lag );
		});
	    return $result;
	}

	public static function fourier(Array $mix=null, bool $sign=true) {
		
		// $mix = [ 0, 1, 2, 3, 4 ];

		$n = count($mix);
	   	$acc = 1;
	   	while($acc < $n) $acc*=2;
	   	while($n++ < $acc) $mix[] = 0;
	   	$n = count($mix);

	   	// print_r($mix); die;

	   	// $n = count($mix);

	   	$isign = $sign ? 1 : -1;
	   	
	   	$j = 1;

	   	for ($i = 1; $i < $n; $i += 2) {
	      	if ($j > $i) {
	        	$mix[($j)] = $mix[($i)];
	         	$mix[($i)] = $mix[($j)];
	         	$mix[($j+1)] = $mix[($i+1)];
	         	$mix[($i+1)] = $mix[($j+1)];
	      	}
	      	$n /= 2;
	      	$m = $n;
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

	            $tempr = $wr * $mix[$j]     - $wi * $mix[($j+1)];
	            $tempi = $wr * $mix[($j+1)] + $wi * $mix[$j];

	            $mix[$j]     = $mix[$i]     - $tempr;
	            $mix[($j+1)] = $mix[($i+1)] - $tempi;

	            $mix[$i]     += $tempr;
	            $mix[($i+1)] += $tempi;

	         }
	         $wtemp = $wr;
	         $wr = ($wr * $wpr) - ($wi    * $wpi) + $wr;
	         $wi = ($wi * $wpr) + ($wtemp * $wpi) + $wi;
	      }
	      $mmax = $istep;
	   }

	   for ($i = 1; $i < count($mix); $i++) { 
	      $mix[$i] *= sqrt(2/$n);                   # Normalize the mix
	      if (abs($mix[$i]) < 1E-8) $mix[$i] = 0;  # Let's round small numbers to zero
	      $mix[($i-1)] = $mix[$i];                # We need to shift array back (see beginning)
	   }

	   return $mix;

	}

	public static function mixtrend(Array $serie, Array $trend_array)
	{
		$keys = array_merge(array_keys($serie), $trend_array);
		self::each($keys, function($v) use (&$serie)
		{
			if(empty($serie[$v]))
			{
				$serie[$v] = Vector::linear_trend($serie, $v);
				ksort($serie);
			}
		});
		return $serie;
	}

	public static function smooth(Array $serie, int $aggregate = 3)
	{
		$return = [];
		Vector::each($serie, function($n, $i) use ($serie, $aggregate, &$return) {
			if($i < $aggregate) $return[] = $n;
			else $return[] = Vector::average(array_slice($serie, $i - $aggregate, $aggregate));
		});
		return $return;
	}

	public static function blur(Array $serie, int $ngbr=5)
	{
		$return = [];
		$ngbr = min($ngbr, floor(count($serie)/2));
		$len = count($serie);
		Vector::iterate(0, count($serie), function($i) use ($serie, $ngbr, $len, &$return) {
			$from = max(0, $i - $ngbr);
			$offset = $ngbr*2+1;
			$return[] = Vector::average(array_slice($serie, $from, min($len-$from, $offset)));
		});
		return $return;
	}

	public function test()
	{
		$v = new Vector();
		$a = [];		
		$v->iterate(2, 58, function($x) use (&$a) {
			$a[] = random_int(20, 100);
		});

		$a[3] = 200;
		$a[4] = null;
		$a[5] = 500;
		$a[6] = 0;
		$a[20] = null;
		$a[21] = 100;
		$a[40] = null;
		$a[41] = 300;

		return [ 
			$a
			, $v->blur($a,2)
			, $v->blur($a,3)
			, $v->blur($a,4)
			, $v->blur($a,5)
		];
	}
}