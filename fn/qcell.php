<?php
namespace abox;
require "../user.php";
if(aval() && in()) echo qcell(in("table"),in("field"),(in("restrictions")?in("restrictions"):"code='".user()."'"));