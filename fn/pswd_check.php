<?php
namespace abox;
require('../user.php');
if(in('user') && in('pswd')){ echo pswd_check(qcell("Users","code","user='".in('user')."'"),in('pswd')); } else { echo -1; }