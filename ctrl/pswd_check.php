<?php
namespace abox;
require('../std.php');
if(post('user') && post('pswd')){ echo pswd_check(qcell("Users","code","user='".post('user')."'"),post('pswd')); } else { echo -1; }