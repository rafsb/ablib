<?php
namespace abox;
require_once "core.php";

/* CONTROLBOX CLASS
 * 
 * @privAttr := [buttons,userbuttons,float,color,classes]
 * @publAttr := []
 * @memberFn := [buttons,userbuttons,float,paint,set,print]
 */
class ControlBox{
	private $buttons;
	private $userbuttons;
	private $float;
	private $classes;
	private $color;

	function buttons($arr=null){
		if($arr){
			if(is_array($arr)) $this->buttons = $arr;
			else return Errors::TYPE_MISMATCH;
		}
		return $this->buttons;
	}

	function userbuttons($arr=null){
		if($arr){
			if(is_array($arr)) $this->userbuttons = $arr;
			else return Errors::TYPE_MISMATCH;
		}else return $this->userbuttons;
	}

	function append($b=null){ if($b) $this->userbuttons[] = $b; return $this->userbuttons; }

	function float($dir=null){ if($dir) $this->float = $dir; return $this->float; }

	function set($class=null){ if($class) $this->classes = $class; return $this->classes; }

	function paint($color){ if($color) $this->color = $color; return $this->color; }

	function print($arr=null){
		if($arr && is_array($arr)){
			$s = sizeof($arr);
			if($s > 0) $this->buttons($arr[0]);
			if($s > 1) $this->userbuttons($arr[1]);
		}
		$content = "";
		foreach($this->buttons as $bt){ $content .= "<div style='background:transparent;color:".$this->color.";' class='-$bt'></div>"; };
		if(sizeof($this->userbuttons)){ foreach($this->userbuttons as $bt){ $content.=$bt; } }
		sess("buttons","<nav class='-controlbox'>$content</nav>");
		return "<nav class='-controlbox'>$content</nav>";
	}

	public function __construct($arr=["close","maximize","minimize"]){
		$this->buttons = $arr;
		$this->userbuttons = [];
		$this->float = "right";
		$this->classes = "";
		$this->color = "#444";
	}
}

class Modal{
	private $classes;
	private $type;
	private $size;
	private $position;
	private $id;
	private $title;
	private $color;
	private $forecolor;
	private $border;
	private $barcolor;
	private $scrollscope;
	private $body;
	public  $controlbox;
	public  $blured;

	function set($class=null,$mode=AB_APPEND){
		if($class){
			switch ($mode) {
				case AB_REPLACE:{
					$this->classes = $class;
					break;
				}default:{
					$this->classes .= $class;
					break;
				}
			}
		}
		return $this->classes;
	}

	function type($type=null){ 
		if($type && in_array(strtolower($type),[AB_WRAPPER,AB_WINDOW,AB_PANEL,AB_DIALOG])) $this->type = $type; else $this->type = AB_WINDOW;
		return $this->type; 
	}

	function size($size=null){ 
		if($size && is_array($size)){
			if(isset($size[0]))	$this->size[0] = $size[0];
			if(isset($size[1]))	$this->size[1] = $size[1];
		}
		return $this->size;
	}

	function pos($pos=null){ 
		if($pos && is_array($pos)){
			if(isset($pos[0]))	$this->pos[0] = $pos[0];
			if(isset($pos[1]))	$this->pos[1] = $pos[1];
		}
		return $this->position;
	}

	function name($id=null){ if($id) $this->id = $id; return $this->id; }

	function blured($id=null){ if($id) $this->blured = true; else $this->blured = false; return $this->blured; }

	function title($title=null,$mode=AB_APPEND){
		if($title){
			switch($mode){
				case(AB_APPEND) :{ $this->title .= $title; 				} break;
				case(AB_PREPEND):{ $this->title = $title.$this->title; 	} break;
				case(AB_REPLACE):{ $this->title = $title; 				} break;
				default:{ $this->title .= strtoupper(conf("project_name")); } break;
			}
		}
		return $this->title;
	}

	function paint($color=null){ if($color) $this->color = $color; return $this->color; }

	function font($color=null){ if($color) $this->forecolor = $color; return $this->forecolor; }

	function border($color=null){ if($color) $this->border = $color; return $this->border; }

	function barpaint($color=null){ 
		if(!is_array($color)) $this->barcolor[0] = $color;
		else{ $this->barcolor = $color; if(sizeof($color)>1) $this->controlbox->paint($color[1]); }
		return $this->barcolor;
	}

	function sscope(){ $this->scrollscope = true; }

	function buttons($arr=null){ if($arr) $this->controlbox->buttons($arr); return $this->controlbox->buttons(); }

	function appendbutton($arr=null){ if($arr) $this->controlbox->append($arr); return $this->controlbox->userbuttons(); }

	function bstart(){ ob_start(); }

	function bend($print=false){ $this->body(ob_get_clean()); if($print) $this->print(); }

	function body($b=null){ if($b) $this->body = $b; }

	function print(){
		$this->type = [
			AB_WRAPPER => "wrapper" ,
			AB_WINDOW  => "window"  ,
			AB_PANEL   => "panel"	,
			AB_DIALOG  => "dialog"
		][$this->type];
		if($this->type == "dialog" && sizeof($this->controlbox->buttons())==3) $this->controlbox->buttons(["close","minimize"]);
		echo str_replace("{{#}}",$this->id,
			'<div id="{{#}}"
				class="-'.$this->type.' f'.$this->type.' b'.$this->type.' '.$this->classes.($this->blured?' -blur':'').'"
				style="display:block;position:fixed;'
					.($this->size 	  ? 'width:'	  .$this->size[0]	  .';height:'.$this->size[1]     :'')
					.($this->position ? ';top:' 	  .$this->position[0] .';left:'  .$this->position[1] :'')
					.($this->color    ? ";background:".$this->color										 :'')
					.($this->forecolor? ";color:"     .$this->forecolor									 :'')
					.($this->blured   ? 'background:inherit'											 :'')
					.';border:'.$this->border.';"
				onclick="ab.reorder(this.id)">
				<div class="abs zero wf" style="height:2rem;background:'.$this->barcolor[0].';"></div>
				<div class="-restore -title" style="padding:.5rem;color:'.$this->barcolor[1].';">'.$this->title.'</div>'
				.$this->controlbox->print()
				.'<session class="stretch '.($this->scrollscope?"-sscope":"").'">'
					.$this->body
				.'</session>
				<script>
					setTimeout(function(){
						var
						x = document.getElementById("{{#}}");
						if(x){ x = x.getElementsByClassName("-hook")[0]; if(x) x.click(); }
						$(document.getElementById("{{#}}")).draggable();'.
						($this->scrollscope?"ab.scrolls();":"").'
					},400);
				</script>
			</div>'
		);
	}

	public function __construct($id=null,$title=null,$type=null,$sscope=false, $blured=false){
		$this->classes 		= "";
		$this->size 		= null;
		$this->position 	= null;
		$this->color		= null;
		$this->forecolor	= null;
		$this->border		= "none";
		$this->barcolor		= ["inherit","#444"];
		$this->body			= "";
		if($id) $this->id = $id; else $this->id = uniqid("abmodal");
		$this->title($title?$title:conf("enterprise"));
		$this->type($type?$type:"window");
		if($sscope) $this->sscope();
		if($blured) $this->blured(true);
		$this->controlbox = new ControlBox();
	}
}
/* */