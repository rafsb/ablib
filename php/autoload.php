<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "Core.class.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "Request.class.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "Debug.class.php";

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
    if(is_file($path))
    {
        include_once $path;
        // echo "modpath: " . $path . " <br> ";
    }
    else
    {
        // seek on user's classes folder
        $path  = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . ucfirst($class) . ".class.php";
        if(is_file($path)){
            include_once $path;
            // echo "userpath: " . $path . " <br> ";
        }
        else
        {
            // an then use the lib's classes
            $path = __DIR__ . DIRECTORY_SEPARATOR . ucfirst($class) . ".class.php";
            if(is_file($path)){
                include_once $path;
                // echo "libspath: " . $path . " <br> ";
            }
            else Core::response(0, $class . ": not found...");
        }
    }
});