<?php
require("../std.php");
unlink(__DIR__."/.wgets");
exec("wget --output-document ".__DIR__."/.wgets"." ".abox\post("url0"));
$count = 5;

while($count--)
{
	sleep(1000);
	$c = @file_get_contents(__DIR__."/.wgets");
	if($c!=="") $count = 0;
}

if($c) echo $c; else echo 0;