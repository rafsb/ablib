<?php require_once("../std.php");
if(abox\post("text"))
{
    abox\log(abox\post("text"));
}
else echo 0;
