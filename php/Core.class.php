<?php
namespace lib;

use IO;
use Request;

class Core {
	
	/* sets or gets information under root/.conf/ folder, each parameter is stored into a separated file
	 * $f = file
	 * $v = value // used only for SET case
	 * ex.: 
	 * conf("user","rafsb") will SET 'rafsb' to a file (root)/.conf/user.cfg
	 * conf("user") will read it and return "rafsb"
	 */
	public static function response($status,$data){
		$data = ["status"=>$status,"data"=>$data];
		Request::sess("DEBUG",array_merge(is_array(Request::sess("DEBUG"))?Request::sess("DEBUG"):[],$data));
		if(DEBUG) Debug::show();
		return json_encode($data);
	}
	
	public static function bin($bin,$args=null){
		$cmd = "sh " . IO::root("src/bin") . DS . $bin;
		if($args&&is_array($args)) foreach($args as $a) $cmd .= " " . $a;
		return shell_exec($cmd);
	}
}