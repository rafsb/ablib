<?php
class Page {

	public function view($view){
		$view = IO::root() . "webroot" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . strtolower($view) . ".php";
		if(is_file($view)) $this -> layout = $view;
		else if(DEBUG) echo PHP_EOL . "NO VIEW FOUND: V[$view]";
	}

	public function render($default=true){
		
		if($this -> layout) $page = implode(DIRECTORY_SEPARATOR,explode('_',strtolower($this -> layout)));
		else $page = implode(DIRECTORY_SEPARATOR,explode('_',strtolower(get_called_class())));

		//print_r($page);
		
		$view = IO::root() . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $page . ".php";

		// print_r($this -> args);

		foreach ($this -> arg as $k => $v) $$k = $v;
	
		if($default){
			//header("Access-Control-Allow-Origin: *"); // OLY FOR PUBLIC API USE
		    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		    header("Content-Type: text/html; charset=UTF-8",true);
		    ob_start()?>
			    <!DOCTYPE html>
			    <html lang="pt-BR">
			        <head>
			            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
			            <meta name="viewport" content="width=device-width, initial-scale=1">
			            <meta name="description" content=""/>
			            <meta name="author" content="<?=App::devel()?>"/>
			            <?php
			            foreach(IO::js(SCAN) as $file)                 { ?> <script type="text/javascript" src="lib/js/<?=$file?>"></script>        <?php }
			            foreach(IO::scan("webroot/js","js") as $file)  { ?> <script type="text/javascript" src="webroot/js/<?=$file?>"></script>    <?php }
			            foreach(IO::css(SCAN) as $file)                { ?> <link rel="stylesheet" href="lib/css/<?=$file?>"/>                      <?php }
			            foreach(IO::scan("webroot/css","css") as $file){ ?> <link rel="stylesheet" href="webroot/css/<?=$file?>"/>                 <?php } ?>
			            <title><?=App::project_name()?></title>
			        </head>
			        <body class="-zero">
			<?php
			echo trim(ob_get_clean());
		}
		
		if(is_file($view)){ include_once $view; }
		
		if($default){?> </body></html> <?php }
	
	}

	public function __construct(){
		$this -> argv = Request::in();
	}
}