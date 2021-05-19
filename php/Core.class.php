<?php
class Core {

	public static function response($status,$data=null)
	{
		if(DEBUG) IO::log(
			(is_object($status)||is_array($status) ? Convert::json($status) : $status) 
			. " => " . 
			(is_object($data)||is_array($data) ? Convert::json($data) : $data)
		);
		return $status;// . (DEBUG ? " | " . $data : "");
	}
	
	public static function bin($bin, $args=null)
	{
		$cmd = "sh " . IO::root("assets/bin") . DS . $bin;
		if($args&&is_array($args)) foreach($args as $a) $cmd .= " " . $a;
		exec($cmd, $output);
		return $output;
	}
	
}
