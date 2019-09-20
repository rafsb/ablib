<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "Core.class.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "Request.class.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "Debug.class.php";

spl_autoload_register(function($class)
{
    $class = preg_replace("/\\\\/",'/',$class);
    $path  = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . ucfirst($class) . ".class.php";

    if(is_file($path)) include_once $path;
    else
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . ucfirst($class) . ".class.php";
        if(is_file($path)) include_once $path;
        else Core::response(0, $class . ": not found...");
    }
    
    if(!is_file($path)){
        $class = explode("/",$class);
        $namespaces = array_slice($class, 0, sizeof($class)-1);
        foreach($namespaces as &$ns) $ns=strtolower($ns); 
        $tmp = array_merge(["src"],array_slice($class, sizeof($class)-1));
        $class = implode("/", array_merge($namespaces,$tmp));
        $path = __DIR__ . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . $class . ".php";
        // echo $path . " || " . (is_file($path) ? "exists" : "not exists"); die;
        if(is_file($path)) include_once $path;
        else Core::response(0, $class . ": not found...");
    }
});