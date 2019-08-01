<?php
class Activity extends Page {
	
	/*
	 * @Overridable
	 */
	protected function before(){
		$this->argv_ = Request::in();
		$this->noVisual();
		$this->result_ = "@";
	}

	protected static function get_hash($hash=null){
		$hash = $hash ? $hash : Request::in("hash");
        if(!$hash) return Core::response(0, "no data given");
        return Convert::base($hash);
	}

}
