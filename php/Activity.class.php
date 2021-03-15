<?php
class Activity extends Page {
	
	/*
	 * @Overridable
	 */
	protected function before(){
		$this->noVisual();
		$this->result_ = "@";
	}

	protected static function get_hash($hash=null, $rebase=true)
	{
		$hash = $hash ? $hash : Request::in("hash");
        if(!$hash) return Core::response(0, "no data given");
        return $rebase ? Convert::base($hash) : $hash;
	}

}
