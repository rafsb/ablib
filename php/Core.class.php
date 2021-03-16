<?php
class Core {

	public static function response($status,$data=""){
		// $data = ["status"=>$status,"data"=>$data];
		if(DEBUG)
		{
			//Request::sess("DEBUG",array_merge(is_array(Request::sess("DEBUG"))?Request::sess("DEBUG"):[],$data));
			IO::log($status . " => " . $data);
		}
		return $status;
	}
	
	public static function bin($bin,$args=null){
		$cmd = "sh " . IO::root("src/bin") . DS . $bin;
		if($args&&is_array($args)) foreach($args as $a) $cmd .= " " . $a;
		return shell_exec($cmd);
	}
	
}