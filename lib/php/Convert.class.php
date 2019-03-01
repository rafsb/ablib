<?php
class convert{
    // stdobject to array
    public function otoa($o){
        $r = (array)$o;
        foreach($r as $k => $v){ if(is_object($v)) $v = Convert::otoa($v); }
        return $r;
    }

    public function atoo($a){
        if(!is_array($a)){ return json_decode(json_encode(["error"=>"argument is not an array()"])); }
        foreach($a as $k=>$v){
            if(is_array($v)) $a[$k] = Convert::atoo($v);
        }
        return json_decode(json_encode($a));
    }

    public function atoh($a=null){
        if(!$a) return 0;
        if(is_object($a)) $a = otoa($a);
        if(gettype($a)=="array"){
            $t = "";
            foreach($a as $k=>$v){
                if(is_array($a[$k])) $t.=Convert::atoh($a[$k]);
                else $t.=str_replace(" ","+",$k)."=".str_replace(" ","+",$v)."&";
            }
            return substr($t,0,strlen($t)-1);
        }
        return 0;
    }
}
