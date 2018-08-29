<?php require("../std.php"); 
echo abox\qcell("Users","mchk","user='".(abox\post("user") ? abox\post("user") : abox\qcell("Users","user","code='".abox\sess("user")."'"))."'");