<?php
class Core {
	/* sets or gets information under root/.conf/ folder, each parameter is stored into a separated file
	 * $f = file
	 * $v = value // used only for SET case
	 * ex.: 
	 * conf("user","rafsb") will SET 'rafsb' to a file (root)/.conf/user.cfg
	 * conf("user") will read it and return "rafsb"
	 */
	public function response($status,$data){
		return json_decode(json_encode(["status"=>$status,"data"=>$data]));
	}

	public function fget($el,$obj=null){
		if(!$el) return null;
		$el = io::root()."project/".$el;
		if(is_file($el)) {
			$el = io::fread($el);
			if($obj){
				foreach($obj as $k=>$v){
					$el = str_replace("{{$k}}",$v,$el);
				}
			}
			return $el;
		}
	}

	public function element($el){ include_once __DIR__."/../../".(Request::sess("__VP")?"desktop":"mobile")."/elements/".$el.".ctp"; }

	public function view($el){ include_once __DIR__."/../../".(Request::sess("__VP")?"desktop":"mobile")."/views/$el.ctp"; }

	public function template($el){ include_once __DIR__."/../../".(Request::sess("__VP")?"desktop":"mobile")."/templates/".$el.".ctp"; }

	public function modal($map=null,$m='default'){
		$this->innerContent = $map;
		include_once __DIR__."/../../".(Request::sess("__VP")?"desktop":"mobile")."/modals/$m.ctp";
	}

	public function script($el){ 
		echo "<script type='text/javascript'>"; 
		include_once __DIR__."/../../".(Request::sess("__VP")?"desktop":"mobile")."/js/".$el.".js"; 
		echo "</script>"; 
	}

	public function stylesheet($el){ include_once __DIR__."/../../".(Request::sess("__VP")?"desktop":"mobile")."/css/".$el.".css"; }

	public function start_section_container(){ 
		echo '
			<section class="sp-absolute sp-zero" style="width:100vw;height:100vh">
				<div class="sp-relative sp-wrapper sp-zero sp-scrolls" style="width:100vw;height:100vh">'; 
	}

	public function start_section_row(){ echo '<section class="sp-row">'; }

	public function end_section_row(){ echo "</section>"; }

	public function end_section_container(){ echo "</div></section>"; }

	public function start_reading(){  ob_start(); }

	public function end_reading($print = false){ $this->html_ = ob_end_clean(); if($print) echo $this->html(); }

	public function html(){ return $this->html_; }

	public function print($obj=null){
		if($obj){
			foreach($obj as $k=>$v){
				$this->html_ = str_replace("{{$k}}",$v,$this->html);
			}
		} 
		echo $this->html; 
	}

	public function get($f=null){
		if($f){ if(isset($_GET[$f])) return $_GET[$f]; else return null; }
		return convert::atoo($_GET);
	}
	public function post($f=null){
		if($f){ if(isset($_GET[$f])) return $_GET[$f] ; else return null; }
		return convert::atoo($_GET);
	}

	public function in($f=null){
		$tmp = json_decode(file_get_contents("php://input"));
		if($f){
			if(isset($tmp->{$f})) return $tmp->{$f};
			else return null;
		}
		return $tmp;
	}

	public function call($url=null){ include_once IO::root($url ? $url : (Core::in("url") ? Core::in('url') : null )); 	}
}