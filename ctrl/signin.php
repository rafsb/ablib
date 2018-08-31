<?php 
namespace abox; 
require('../user.php');
//print_r(in());
if(in('user') && in('pswd')) echo signin(in('user'),in('pswd'),in('keep')); else echo -1;