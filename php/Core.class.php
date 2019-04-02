<?php
class Core {
	/* sets or gets information under root/.conf/ folder, each parameter is stored into a separated file
	 * $f = file
	 * $v = value // used only for SET case
	 * ex.: 
	 * conf("user","rafsb") will SET 'rafsb' to a file (root)/.conf/user.cfg
	 * conf("user") will read it and return "rafsb"
	 */
	public function response($status,$data){
		return json_decode(json_encode(["status"=>$status,"data"=>$data]));
	}
	
	public function call($url=null){ include_once IO::root($url ? $url : (Core::in("url") ? Core::in('url') : null )); 	}
}