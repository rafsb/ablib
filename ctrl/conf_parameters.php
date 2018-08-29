<?php
namespace abox;
require("../std.php");
$f = post("fild");
$v = post("val0");

if($f) echo conf($f,($v?$v:null),Locations::CHROOT);
else 0;