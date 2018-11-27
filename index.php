<?php
session_start();
function __autoload($class){
    $class = preg_replace("/\\\\/",'/',$class);
    $path = __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . $class . ".php";
    //print_r($path);
    if(is_file($path)) include_once $path;
    else{
        $path = __DIR__ . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . $class . ".php";
        if(is_file($path)) include_once $path;
        //print_r($path);
    }
    //print_r($path);
}
require "lib" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . "Constants.php";
require "lib" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . "App.php";
if(!User::logged()) if(Request::cook("USER") && Request::cook("ACTIVE")) Request::sess("USER",Request::cook("USER"));
if(Core::get('uri')){
    $uri = explode('/',Core::get('uri'));
    $uri = '(new ' . ucfirst($uri[1]) . ")->" . (isset($uri[2]) && $uri[2] ? $uri[2] : "render") . "();";
    //echo $uri;
    try{ eval($uri); } catch(Exception $e){ IO::debug($e); } 
}else{
    //header("Access-Control-Allow-Origin: *"); // OLY FOR PUBLIC API USE
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("Content-Type: text/html; charset=UTF-8",true);?>
    <!DOCTYPE html>
    <html lang="pt-BR">
        <head>
            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content=""/>
            <meta name="author" content="<?=App::devel()?>"/>
            <?php
            foreach(IO::js(SCAN) as $file){ if(!in_array($file,['.','..']))?> <script type="text/javascript" src="lib/js/<?=$file?>"></script> <?php }
            foreach(IO::css(SCAN) as $file){if(!in_array($file,['.','..']))?> <link rel="stylesheet" href="lib/css/<?=$file?>"/> <?php }?>
            <!--link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous"-->
            <title><?=App::project_name()?></title>
        </head>
        <body class="-fregular" style="font-size:12px;background:#2C2F2F;min-width:1080px;overflow-x:hidden;color:white;padding:0;margin:0;">
            <?php
            if(User::logged()) (new Home) -> render();
            else (new Login()) -> render();?>
        </body>
        <script></script>
    </html>
<?php
}