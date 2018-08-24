<?php
namespace abox;
include_once('../core.php');
switch(in("mode")){
	case("is"): $st =  sess(in("field"),in("data"));    break;
	case("os"): $st =  is_array(sess(in("field")))?json_encode(sess(in("field"))):sess(in("field")); 				break;
	case("ic"): $st =  cook(in("field"),in("data"));	break;
	case("oc"): $st =  cook(in("field"));				break;
	default: $st =  0; break;
}
if($st) echo $st; else echo 0;