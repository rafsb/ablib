<?php
namespace abox;
require "core.php";
require "pic.php";

$name  = post("name")?post("name"):null;
$rpath = post("path");
$mini  = (bool)post("mini");
$force = (bool)post("forc");

if(substr($rpath,strlen($rpath)-1,1) != "/") $rpath.="/";
$apath = root($rpath,AB_FOLDER,AB_RECURSIVE); 

$a = new Pics($name,$apath,1024*1024*8,$mini,$force);

/*
echo "/*";
print_r($a);
print_r(post());
echo "* /";
*/


if($a->run()){
	$o = (object)[];
	$o->{"file"} = $rpath.$a->newname()."?_=".uniqid();
	$o->{"name"} = $a->newname();
	$o->{"mini"} = $mini?"mini_".$a->newname()."?_=".uniqid():null;
	$o->{"error"} = $a->log();
	echo json_encode($o);
}else echo '{"error":"'.$a->log().'"}';//.$a->log();//print_r($a);