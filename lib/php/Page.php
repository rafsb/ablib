<?php
class Page{
	public function render(){
		$view = IO::root() . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . get_called_class() . ".php";
		if(is_file($view)) include_once $view;
	}
}