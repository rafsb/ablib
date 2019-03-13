<?php
class Page {
	protected $argv_ = [];

	protected $body_;

	protected $layout_ = false;

	protected $allow_access_ = false;
	
	protected $default_layout_;

	protected $html_first_portion_ = '';

	protected $html_last_portion_ = '';

	protected function before_load(){ }

	protected function onload(){ return ''; }

	protected function read(){
		ob_start();
	}

	protected function save($print = NOPRINT){
		$this -> body_ = ob_get_clean();
		if($print) $this -> print();
		else return trim($this -> body_);
	}

	protected function print(){
		echo trim($this -> body_);
	}

	protected function args(){
		return $this -> argv_;
	}

	protected function import($file = null){
		$pos = ".php";
		if(is_string($file)) include_once IO::root("webroot" . DS . "views" . DS . $file . $pos);
		if(is_array($file)&&sizeof($file)) foreach ($file as $k => $v){
			if(is_string($v)) include_once IO::root("webroot" . DS . "views" . DS . $k . DS . $v . $pos);
			else foreach ($v as $vv) include_once IO::root("webroot" . DS . "views" . DS . $k . DS . $vv . $pos);
		}
	}

	protected function view($view = null){
		if($view!==null){
			$view = IO::root() . "webroot" . DS . "views" . DS . strtolower($view) . ".php";
			if(is_file($view)) $this -> layout_ = $view;
			else $this -> layout_ = false;
		}
		return $this -> layout_;
	}

	protected function default_layout($tog = null){
		if($tog !== null) $this -> default_layout_ = $tog;
		return $this -> default_layout_;
	}

	protected function allow_access($origin = false){
		if($origin) $this -> allow_access_ = $origin;
		return $this -> allow_access_;
	}

	public function render($argv = []){
		
		$this -> before_load();

		if($this -> view()===null) $this -> view(strtolower(get_called_class()));
	
		if($this -> default_layout()){
			include_once IO::root() . "webroot" . DS . "views" . DS ."templates" . DS . "layout" . DS . "default.php";
		}
		
		echo $this -> html_first_portion_;

		echo $this -> onload();

		if($this -> view())  include_once $this -> view();

		echo $this -> html_last_portion_;

	}

	public function __construct(){
		$this -> argv_ = Request::in();
		$this -> default_layout_ = false;
		$this -> layout_ = false;
	}
}
