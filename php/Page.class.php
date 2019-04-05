<?php
class Page {
	protected $argv_ = [];

	protected $view_ = "@";

	protected $result_ = null;

	protected $layout_ = null;

	protected $allow_access_ = false;

	protected function before(){}

	protected function args(){
		return $this->argv_;
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

	protected function tile($t, $args=[]){
		if(is_file(IO::root() . "webroot" . DS . "views" . DS . "templates" . DS . "tiles" . DS . $t . ".htm" )){
			$tmp = IO::read("/webroot" . DS . "views" . DS . "templates" . DS . "tiles" . DS . $t . ".htm");
			// print_r($tmp);
			if(sizeof($args)){
				foreach($args as $k => $v){
					// echo $tmp;
					$tmp = str_replace("@".$k, $v, $tmp);
				}
			}
			echo "<!--";
			var_dump($tmp);
			echo " -->";
			return $tmp;
		}else return Core::response(-1, "Template not found!");
	}

	protected function view($view = null){
		if($view!==null) $this->view_ = $view;
		$tmp = IO::root() . "webroot" . DS . "views" . DS . strtolower($this->view_=="@"?get_called_class():$this->view_) . ".php";
		return is_file($tmp) ? $tmp : Core::response(-1,"View file not found: $view");
	}

	protected function layout($layout = null){
		if($layout!==null) $this->layout_ = $layout;
		$tmp = IO::root() . "webroot" . DS . "views" . DS . "templates" . DS . "layout" . DS . strtolower($this->layout_=="@"?get_called_class():$this->layout_) . ".php";
		return is_file($tmp) ? $tmp : Core::response(-1,"Layout file not found: $layout");
	}

	protected function result($result = null){
		if($result!==null) $this->result_ = $result;
		return $this->result_;
	}

	protected function allow_access($origin = false){
		if($origin) $this->allow_access_ = $origin;
		return $this->allow_access_;
	}

	public function render($argv = []){

		$this->argv_ = array_merge($this->argv_,Convert::otoa($argv));
		
		$this->before();

		// echo $this->layout()." - ".$this->view();

		if($this->layout()) include_once $this->layout();
		else if($this->view())  include_once $this->view();
		else if($this->result()) echo $this->result();
		else Debug::show();

	}

	public function __construct(){
		$this->argv_ = Request::in();
	}
}
