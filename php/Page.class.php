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
		if(is_string($file)){ include_once $path . $file . $pos; }
		if(is_array($file)) foreach ($file as $k => $v){
			if(is_string($v)){ include_once $path . $k . DS . $v . $pos; }
			else foreach ($v as $kk => $vv){ 
				if(is_string($vv)){ include_once $path . $k . DS . $vv . $pos; }
				else if(is_array($vv)) foreach ($vv as $vvv) {
					include_once $path . $k . DS . $kk . DS . $vvv . $pos;
					// echo '<pre>';
					// print_r($vv);;
				}
			}
		}
		// print_r($file);
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

	protected function view($view = null, $print=false){
		if($view===null) $view = $this -> view_;
		else $this -> view_ = $view;
		$view = IO::root() . "webroot" . DS . "views" . DS . strtolower($this -> view_ === null?get_called_class():$this -> view_) . ".php";
		if(is_file($view)) if($print) include_once $view;
		return $view;
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
