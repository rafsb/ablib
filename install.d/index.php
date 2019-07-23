<?php
session_start();
spl_autoload_register(function($class)
{
    $class = preg_replace("/\\\\/",'/',$class);
    $path  = __DIR__ . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . ucfirst($class) . ".class.php";

    if(is_file($path)) include_once $path;
    else
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . ucfirst($class) . ".class.php";
        if(is_file($path)) include_once $path;
    }
});

require "lib" . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . "Constants.php";
require "webroot" . DS . "App.php";

if(!User::logged()) if(Request::cook("USER") && Request::cook("ACTIVE")) Request::sess("USER",Request::cook("USER"));

if(Request::get('_'))
{   
    $args = explode('/',Request::get('_'));
    $uri = 'echo (new ' . ucfirst($args[1]) . ")->" . (isset($args[2]) && $args[2] ? $args[2] : "render") . "(" . implode(',',array_slice($args,3)) . ");";

    try{ eval($uri); } catch(Exception $e){ Core::response(-1,var_dump($e)); }
}
else
{
    App::init();
}