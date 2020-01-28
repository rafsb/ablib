<?php
require_once "constants.php";
require_once "Core.class.php";
require_once "Request.class.php";
require_once "Debug.class.php";

spl_autoload_register(function($class)
{
    // fetch plugins first, at /modules/NAMESPACE/src/CLASSNAME
    $cls = preg_replace("/\\\\/",'/',$class);
    $cls = explode("/",$cls);
    $namespaces = array_slice($cls, 0, sizeof($cls)-1);
    foreach($namespaces as &$ns) $ns=strtolower($ns); 
    $tmp = array_merge(["src"],array_slice($cls, sizeof($cls)-1));
    $cls = implode("/", array_merge($namespaces,$tmp));
    $path = __DIR__ . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . $class . ".php";
    
    if(is_file($path)) include_once $path;
    else
    {
        // seek on user's classes folder
        $path  = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . ucfirst($class) . ".class.php";
        if(is_file($path)) include_once $path;
        else
        {
            // an then use the lib's classes
            $path = __DIR__ . DIRECTORY_SEPARATOR . ucfirst($class) . ".class.php";
            if(is_file($path)) include_once $path;
            else Core::response(0, $class . ": not found...");
        }
    }
});