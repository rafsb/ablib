<?php
namespace abox;
require "../user.php";
if(in()) echo qcell(in("table"),in("field"),(in("restrictions")?in("restrictions"):"code='".user()."'"));