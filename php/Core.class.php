<?php
class Core {

	public static function response($status,$data=null)
	{
		if(DEBUG) IO::log($status . " => " . $data);
		return $status;// . (DEBUG ? " | " . $data : "");
	}
	
	public static function bin($bin,$args=null)
	{
		$cmd = "sh " . IO::root("src/bin") . DS . $bin;
		if($args&&is_array($args)) foreach($args as $a) $cmd .= " " . $a;
		return shell_exec($cmd);
	}
	
}