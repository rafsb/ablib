<?php
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
		return 0;
	}
	
	public static function bin($shell, $args=[]){
		$cmd = IO::root("src/bin/") . $shell . " ";
		foreach($args as $a) $cmd .= $a . " ";
		return shell_exec($cmd);
	}
}