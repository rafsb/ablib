<?php
namespace abox;
require '../queries.php';
function qsfill($t,$f,$r,$s,$n=false){
    if(!($t && $f)) return -1;
    //echo "select code,$f from $t ".($r?"where $r":"")." order by $f asc";
    $r = qout("select code,$f from $t ".($r?"where $r":"")." order by $f asc", AB_MYSQLI_OBJ);
    //print_r($r);
    if($r->status() && $r->data()->num_rows){
        $r=$r->data();
        $b = "[";
        while($w = $r->fetch_assoc()){
            $b .= '{
                "value":"'.$w["code"].'"
                ,"text":"'.($n?$w["code"].": ":"").($w[$f]?ucwords($w[$f]):$w["code"]).'"
                ,"selected":"'.((strtolower($w[$f])==strtolower($s) || strtolower($w["code"])==strtolower($s))?'1':'0').'"
            },';
            //print_r($w);
        }
        return substr($b,0,strlen($b)-1)."]";
    }
    else return '[{"value":"","text":"","selected":"1"}]';
}

if(in("tabl") && in("fild")){
	echo qsfill(in("tabl"),in("fild"),(in("rest")?in("rest"):null),(in("selt")?in("selt"):null),(in("fnam")?true:false));
}else{
	echo '[{"value":"","text":"","selected":"1"}]';
}
//print_r(in());