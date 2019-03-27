<?php
class Page {
	protected $argv_ = [];

	protected $view_;

	protected $result_;

	protected $layout_;

	protected $allow_access_ = false;

	protected function before(){}

	protected function args(){
		return $this -> argv_;
	}

	protected function import($file = null){
		$pos = ".php";
		$path = IO::root("webroot" . DS . "views" . DS);
		if(is_string($file)) include_once $path . $file . $pos;
		if(is_array($file)&&sizeof($file)) foreach ($file as $k => $v){
			if(is_string($v)) include_once $path . $k . DS . $v . $pos;
			else foreach ($v as $vv) include_once $path . $k . DS . $vv . $pos;
		}
	}

	protected function svg($file){
		$pos = strpos($file,".svg") ? "" : ".svg";
		$path = IO::root("src" . DS . "img" . DS);
		if(is_string($file)) include_once $path . $file . $pos;
		if(is_array($file)&&sizeof($file)) foreach ($file as $k => $v){
			if(is_string($v)) include_once $path . $k . DS . $v . $pos;
			else foreach ($v as $vv) include_once $path . $k . DS . $vv . $pos;
		}
	}

	protected function view($view = null){
		$view = IO::root() . "webroot" . DS . "views" . DS . strtolower($view===null?get_called_class():$view) . ".php";	
		if(is_file($view)) $this -> view_ = $view;
		return $this -> view_;
	}

	protected function layout($layout = null){
		if($layout!==false){
			$layout = IO::root() . "webroot" . DS . "views" . DS . "templates" . DS . "layout" . DS . strtolower($layout!==null?$layout:get_called_class()) . ".php";
			if(is_file($layout)) $this -> layout_ = $layout;
		}
		return $this -> layout_;
	}

	protected function default_layout(){
		return $this -> layout("default");
	}

	protected function result($result = null){
		if($result!==null){
			$this -> result_ = $result;
		}
		return $this -> result_;
	}

	protected function allow_access($origin = false){
		if($origin) $this -> allow_access_ = $origin;
		return $this -> allow_access_;
	}

	public function render($argv = []){

		$this -> argv_ = $this -> argv_ + $argv;
		
		$this -> before();

		if($this -> layout()) include_once $this -> layout();
		else if($this -> view())  include_once $this -> view();
		else if($this -> result()) echo $this -> result();
		else Debug::show();

	}

	public function __construct(){
		$this -> argv_ = Request::in();
	}
}
