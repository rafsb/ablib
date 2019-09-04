<?php
class Convert {
    // stdobject to array
    public static function otoa($o){
        $r = (array)$o;
        foreach($r as $k => $v){ if(is_object($v)) $v = self::otoa($v); }
        return $r;
    }

    public static function atoo($a){
        if(!is_array($a)){ return json_decode(json_encode(["error"=>"argument is not an array()"])); }
        foreach($a as $k=>$v){
            if(is_array($v)) $a[$k] = self::atoo($v);
        }
        return json_decode(json_encode($a));
    }

    public static function atoh($a=null){
        if(!$a) return 0;
        if(is_object($a)) $a = otoa($a);
        if(gettype($a)=="array"){
            $t = "";
            foreach($a as $k=>$v){
                if(is_array($a[$k])) $t.=self::atoh($a[$k]);
                else $t.=str_replace(" ","+",$k)."=".str_replace(" ","+",$v)."&";
            }
            return substr($t,0,strlen($t)-1);
        }
        return 0;
    }

    public static function json($input){
        if(!is_string($input)) return json_encode($input);
        return json_decode($input);
    }


    public static function base($input, $json=true){
        if(is_string($input)) return $json ? self::json(base64_decode($input)) : base64_decode($input);
        else return $json ? base64_encode(self::json($input)) : base64_decode($input);
    }

    public static function xml2json($xml){
        return json_decode(json_encode(simplexml_load_string($xml)));
    }

}
