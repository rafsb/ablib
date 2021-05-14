<?php
class Core {

	public static function response($status,$data=null)
	{
		if(DEBUG) IO::log(
			(is_object($status)||is_array($status) ? Convert::json($status) : $status) 
			. " => " . 
			(is_object($date)||is_array($data) ? Convert::json($data) : $data)
		);
		return $status;// . (DEBUG ? " | " . $data : "");
	}
	
	public static function bin($bin,$args=null)
	{
		$cmd = "sh " . IO::root("src/bin") . DS . $bin;
		if($args&&is_array($args)) foreach($args as $a) $cmd .= " " . $a;
		return shell_exec($cmd);
	}
	
}
