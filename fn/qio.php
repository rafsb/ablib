<?php
namespace abox;
require "../user.php";
$data = post('data');
//print_r($data);
if(aval() && $data) echo qio("SELECT ".(isset($data["field"])?$data["field"]:"*")." FROM ".$data["table"]." WHERE ".(isset($data["restrictions"])?$data["restrictions"]:"code='".user()."'"),post('n'));