<?php
namespace abox;
require("../std.php");
$f = post("fild");
$v = post("val0");

if($f) echo schema($f,($v?$v:null),Locations::CHROOT);
else echo 0;