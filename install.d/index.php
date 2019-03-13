<?php
session_start();
function __autoload($class){
    $class = preg_replace("/\\\\/",'/',$class);
    $path = __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . $class . ".class.php";
    if(is_file($path)) include_once $path;
    else{
        $path = __DIR__ . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . $class . ".class.php";
        if(is_file($path)) include_once $path;
    }
}
require "lib" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . "Constants.php";
require "lib" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . "App.php";

$args = Request::in();

if(!User::logged()) if(Request::cook("USER") && Request::cook("ACTIVE")) Request::sess("USER",Request::cook("USER"));
if(Core::get('uri')){
    $uri = explode('/',Core::get('uri'));
    $uri = '(new ' . ucfirst($uri[1]) . ")->" . (isset($uri[2]) && $uri[2] ? $uri[2] : "render") . "(" . implode(',',array_slice($uri,3)) . ($args?",".implode(',',$args)) . ");";
    try{ eval($uri); } catch(Exception $e){ IO::debug($e); } 
}else{
    include __DIR__ . DS . "webroot" . DS . "main.php";
    (new Main()) -> render($args);
}
