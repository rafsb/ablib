<?php
namespace abox;
require "../user.php";
$query = "Select * from users where code='".user()."' LIMIT 0,1";
$result= qout($query,AB_ASSOC)->data();
//print_r($query);
echo (aval()?'{"status":"1","data":'.json_encode($result).'}':'{"status":"0","data":"error: No logged user"}');