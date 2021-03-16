<?php
class Activity extends Page {
	
	/*
	 * @Overridable
	 */
	protected function before(){
		$this->noVisual();
		$this->result_ = "@";
	}

	protected static function get_hash(&$hash=null, $rebase=false)
	{
		$hash = $hash ? $hash : Request::in("hash");
        if(!$hash) $hash = Core::response(0, "Activity::get_hash -> no data given");
        if($rebase) $hash = Convert::base($hash);
        return $hash;
	}

    public function render(){ return IO::debug(); }

}
