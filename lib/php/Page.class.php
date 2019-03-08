<?php
class Page {
	private $argv_ = [];

	private $body_;

	private $layout_ = null;

	private $allow_access_ = false;
	
	private $default_layout_ = true;

	protected $html_first_portion_ = '';

	protected $html_last_portion_ = '';

	protected function onload(){}

	protected function read(){
		ob_start();
	}

	protected function record($print = false){
		$this -> body_ = ob_end_clean();
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
		if(is_string($file)) include_once IO::root("webroot" . DC . "views" . DC . $file . $pos);
		if(is_array($file)&&sizeof($file)) foreach ($file as $k => $v){
			if(is_string($v)) include_once IO::root("webroot" . DC . "views" . DC . $k . DC . $v . $pos);
			else foreach ($v as $vv) include_once IO::root("webroot" . DC . "views" . DC . $k . DC . $vv . $pos);
		}
	}

	protected function view($view = null){
		if($view!==null){
			$view = IO::root() . "webroot" . DC . "views" . DC . strtolower($view) . ".php";
			if(is_file($view)) $this -> layout_ = $view;
			else $this -> layout_ = "error";
		}
		return $this -> layout_;
	}

	protected function default_layout($tog = null){
		if($tog !== null) $this -> $default_layout_ = $tog;
		return $this -> default_layout_;
	}

	protected function allow_access($origin = false){
		if($origin) $this -> allow_access_ = $origin;
		return $this -> allow_access_;
	}

	public function render(){
		
		// $this -> default_layout_ = true;

		if(!$this -> view()) $this -> view(strtolower(get_called_class()));
	
		if($this -> default_layout()){
			include_once IO::root() . "webroot" . DC . "views" . DC ."templates" . DC . "default_layout.php";
		}
		
		echo $this -> html_first_portion_;

		// if(DEBUG) echo "<pre>" . IO::debug() . "</pre>";

		echo $this -> onload();

		if($this -> view())  include_once $this -> view();

		echo $this -> html_last_portion_;
	}

	public function __construct(){
		$this -> argv_ = Request::in();
	}
}