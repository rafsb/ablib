<?php
class Page
{
	protected $view_ = "@";

	protected $result_ = null;

	protected $layout_ = ELayouts::DEFAULT; // also LAYOUTS::THIN

	protected $allow_access_ = false;

	/*
	 * @Overridable
	 */
	protected function before(){}

	protected function import($file = null)
	{
		$pos = ".php";
		$path = IO::root("webroot" . DS . "views" . DS);
		if(is_string($file))
			{ include_once $path . $file . $pos; }
		if(is_array($file)) foreach ($file as $k => $v)
		{
			if(is_string($v))
				{ include_once $path . $k . DS . $v . $pos; }
			else foreach ($v as $kk => $vv)
			{ 
				if(is_string($vv)) include_once $path . $k . DS . $vv . $pos;
				else if(is_array($vv)) foreach ($vv as $vvv) include_once $path . $k . DS . $kk . DS . $vvv . $pos;
			}
		}
		// print_r($file);
	}

	protected function svg($file)
	{
		$pos = strpos($file,".svg") ? "" : ".svg";
		$path = IO::root("src" . DS . "img" . DS);
		if(is_string($file)) include_once $path . $file . $pos;
		if(is_array($file)&&sizeof($file)) foreach ($file as $k => $v)
		{
			if(is_string($v)) include_once $path . $k . DS . $v . $pos;
			else foreach ($v as $vv) include_once $path . $k . DS . $vv . $pos;
		}
	}

	protected function tile($t, $args=[])
	{
		if(is_file(IO::root() . "webroot" . DS . "views" . DS . "templates" . DS . "tiles" . DS . $t . ".htm" ))
		{
			$tmp = IO::read("/webroot" . DS . "views" . DS . "templates" . DS . "tiles" . DS . $t . ".htm");
			if(sizeof($args))
			{
				foreach($args as $k => $v)
				{
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

	protected function template($t, $args=[])
	{
		if(is_file(IO::root() . "webroot" . DS . "views" . DS . "templates" .  DS . $t . ".htm" ))
		{
			$tmp = IO::read("/webroot" . DS . "views" . DS . "templates" . DS . $t . ".htm");
			// print_r($tmp);
			if(sizeof($args))
			{
				foreach($args as $k => $v)
				{
					// echo $tmp;
					$tmp = str_replace("@".$k, $v, $tmp);
				}
			}
			// echo "<!--";
			// var_dump($tmp);
			// echo " -->";
			return $tmp;
		}else return Core::response(-1, "Template not found!");
	}

	protected function component($t, $args=[])
	{
		$path = IO::root("webroot" . DS . "views" . DS . "components" . DS);
		if(is_array($t))
		{
			$tmp = "";
			foreach($t as $v) $tmp .= $this->component($v,$args);
			return $tmp;
		}
		if(is_file($path . $t . ".htm" ))
		{
			$tmp = IO::read($path . $t . ".htm");
			// print_r($args) ; die;
			if(sizeof($args))
			{
				foreach($args as $k => $v)
				{
					// echo "<-- " . $k . " = " . $v . "-->";
					$tmp = str_replace("@".$k, $v, $tmp);
				}
			}
			// echo "<!--";
			// var_dump($tmp);
			// echo " -->";
			return $tmp;
		}else return Core::response(-1, "Template not found!");
	}

	protected function view($view = null)
	{
		if($view!==null) $this->view_ = $view;
		if($this->view_) $tmp = IO::root() . "webroot" . DS . "views" . DS . strtolower($this->view_=="@"?get_called_class():$this->view_) . ".php";
		return $this->view_ ? (is_file($tmp) ? $tmp : Core::response(-1, "View file not found: $view AND $tmp")):false;
	}

	protected function layout($layout = null)
	{
		if($layout!==null) $this->layout_ = $layout;
		if($this->layout_) $tmp = IO::root() . "webroot" . DS . "views" . DS . "layouts" . DS . strtolower($this->layout_=="@"?get_called_class():$this->layout_) . ".php";
		return $this->layout_ ? (is_file($tmp) ? $tmp : Core::response(-1,"Layout file not found: $layout")) : false;
	}

	protected function noVisual()
	{ $this->layout(false); $this->view(false); }

	/*
	 * @Overridable
	 */
	protected function result()
	{ return $this->result_; }

	protected function allow_access($origin = false)
	{
		if($origin) $this->allow_access_ = $origin;
		return $this->allow_access_;
	}

	public function render()
	{

		$this->before();

		if($this->layout()) include_once $this->layout();
		else if($this->view())  include_once $this->view();
		else if($this->result()) echo $this->result();
		else Debug::show();

	}
}
