<?php
namespace abox;
include_once "../user.php";
include_once "../date.php";
$u = user();
$p = in("pswd");
$d = new Date();
if($u&&$p) echo hash_it($d->date().hash_it($u.$p));
