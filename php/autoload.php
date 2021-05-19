<?php
require_once "constants.php";
require_once "Core.class.php";
require_once "Request.class.php";
require_once "Debug.class.php";

// WEBROOT/CLASSES 
spl_autoload_register(function($class)
{
    // fetch plugins first, at /webroot/controller/NAMESPACE/CLASSNAME
    $cls = preg_replace("/\\\\/",'/',$class);
    $cls = explode("/",$cls);
    // seek on user's classes folder
    $path  = dirname(__DIR__, 2) . DS . "webroot" . DS . "controller" . DS . ucfirst($cls[0]) . ".class.php";
    if(is_file($path)) include_once $path;    
});

// VENDOR
spl_autoload_register(function($class)
{
    // fetch plugins first, at /modules/NAMESPACE/src/CLASSNAME
    $cls = preg_replace("/\\\\/",'/',$class);
    $cls = explode("/",$cls);
    $namespaces = array_slice($cls, 0, sizeof($cls)-1);
    foreach($namespaces as &$ns) $ns=strtolower($ns); 
    $tmp = array_merge(["src"],array_slice($cls, sizeof($cls)-1));
    $cls = implode("/", array_merge($namespaces,$tmp));
    $path = dirname(__DIR__, 2) . DS . "modules" . DS . $cls . ".class.php";
    if(is_file($path)) include_once $path;
});

// LIB/PHP
spl_autoload_register(function($class)
{
    // fetch plugins first, at /lib/php/NAMESPACE/CLASSNAME
    $cls = preg_replace("/\\\\/",'/',$class);
    $cls = explode("/",$cls);
    // an then use the lib's classes
    $path = __DIR__ . DS . ucfirst($cls[0]) . ".class.php";
    if(is_file($path)) include_once $path;
});
