<?php
namespace lib;

use Page;
use Request;

class Activity extends Page {
	
	/*
	 * @Overridable
	 */
	protected function before(){
		$this->argv_ = Request::in();
		$this->noVisual();
		$this->result_ = "<h1>empty response</h1>";
	}

}
